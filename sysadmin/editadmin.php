<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('admin_write', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$id = daddslashes($_GET['id']);
if(empty($id)){
	@header("Location: ./admin.php");
	exit;
}
$promo = $DB->query("SELECT * FROM `ytidc_admin` WHERE `id`='{$id}'");
if($promo->num_rows != 1){
	@header("Location: ./admin.php");
	exit();
}else{
	$row = $promo->fetch_assoc();
	$permission = json_decode($row['permission']);
}
$act = daddslashes($_GET['act']);
if($act == "edit"){
	if(empty($_POST['permission'])){
		@header("Location: ./msg.php?msg=请至少保留一项权限");
		exit;
	}else{
		$permission = json_encode(daddslashes($_POST['permission']));
	}
	$username = daddslashes($_POST['username']);
	$DB->query("UPDATE `ytidc_admin` SET `username`='{$username}', `permission`='{$permission}' WHERE `id`='{$id}'");
	if(!empty($_POST['password'])){
		$password = md5(md5(daddslashes($_POST['password'])));
		$DB->query("UPDATE `ytidc_admin` SET `password`='{$password}' WHERE `id`='{$id}'");
	}
	@header("Location: ./editadmin.php?id={$id}");
	exit;
}
if($act == "del"){
	if(!in_array('*', $permission) && !in_array('admin_delete', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
	if($id == $admin['id']){
		@header("Location: ./msg.php?msg=不能删除自己的账户！");
		exit;
	}
	$DB->query("DELETE FROM `ytidc_admin` WHERE `id`='{$id}'");
	@header("Location: ./msg.php?msg=删除成功！");
	exit;
}

include("./head.php");

?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">编辑管理员</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">编辑管理员</div>
        <div class="panel-body">
          <form role="form" action="./editadmin.php?act=edit&id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>管理员账户</label>
              <input type="text" name="username" class="form-control" placeholder="管理员账户" value="<?=$row['username']?>">
            </div>
            <div class="form-group">
              <label>管理员密码（不修改不用填）</label>
              <input type="password" name="password" class="form-control" placeholder="管理员密码">
            </div>
            <div class="form-group">
	          <label>管理员权限</label>
	          <div class="col-sm-12" style="margin-bottom: 5px;line-height: ;">
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('*', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="*" <?=$checked?>><i></i> 所有权限（选了其他就不用选）
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('worder_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="worder_read" <?=$checked?>><i></i> 查看工单
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('worder_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="worder_write" <?=$checked?>><i></i> 修改工单
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('worder_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="worder_delete" <?=$checked?>><i></i> 删除工单
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('service_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="service_read" <?=$checked?>><i></i> 查看在线服务
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('service_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="service_write" <?=$checked?>><i></i> 修改在线服务
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('service_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="service_delete" <?=$checked?>><i></i> 删除在线服务
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('product_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="product_read" <?=$checked?>><i></i> 查看产品
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('product_create', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="product_create" <?=$checked?>><i></i> 新增产品
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('product_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="product_write" <?=$checked?>><i></i> 编辑产品
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('product_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="product_delete" <?=$checked?>><i></i> 删除产品
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('server_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="server_read" <?=$checked?>><i></i> 查看服务器
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('server_create', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="server_create" <?=$checked?>><i></i> 新增服务器
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('server_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="server_write" <?=$checked?>><i></i> 编辑服务器
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('server_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="server_delete" <?=$checked?>><i></i> 删除服务器
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('notice_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="notice_read" <?=$checked?>><i></i> 查看公告
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('notice_create', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="notice_create" <?=$checked?>><i></i> 新增公告
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('notice_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="notice_write" <?=$checked?>><i></i> 编辑公告
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('notice_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="notice_delete" <?=$checked?>><i></i> 删除公告
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('pay_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="pay_read" <?=$checked?>><i></i> 查看支付接口
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('pay_create', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="pay_create" <?=$checked?>><i></i> 新增支付接口
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('pay_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="pay_write" <?=$checked?>><i></i> 编辑支付接口
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('pay_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="pay_delete" <?=$checked?>><i></i> 删除支付接口
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('price_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="price_read" <?=$checked?>><i></i> 查看价格组
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('price_create', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="price_create" <?=$checked?>><i></i> 新增价格组
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('price_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="price_write" <?=$checked?>><i></i> 编辑价格组
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('price_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="price_delete" <?=$checked?>><i></i> 删除价格组
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('site_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="site_read" <?=$checked?>><i></i> 查看分站
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('site_create', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="site_create" <?=$checked?>><i></i> 新增分站
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('site_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="site_write" <?=$checked?>><i></i> 编辑分站
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('site_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="site_delete" <?=$checked?>><i></i> 删除分站
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('type_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="type_read" <?=$checked?>><i></i> 查看分类
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('type_create', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="type_create" <?=$checked?>><i></i> 新增分类
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('type_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="type_write" <?=$checked?>><i></i> 编辑分类
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('type_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="type_delete" <?=$checked?>><i></i> 删除分类
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('code_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="code_read" <?=$checked?>><i></i> 查看优惠码
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('code_create', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="code_create" <?=$checked?>><i></i> 新增优惠码
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('code_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="code_write" <?=$checked?>><i></i> 编辑优惠码
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('code_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="code_delete" <?=$checked?>><i></i> 删除优惠码
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('admin_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="admin_read" <?=$checked?>><i></i> 查看管理员
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('admin_create', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="admin_create" <?=$checked?>><i></i> 新增管理员
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('admin_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="admin_write" <?=$checked?>><i></i> 编辑管理员
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('admin_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="admin_delete" <?=$checked?>><i></i> 删除管理员
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('user_read', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="user_read" <?=$checked?>><i></i> 查看用户
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('user_write', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="user_write" <?=$checked?>><i></i> 编辑用户
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('user_fund', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="user_fund" <?=$checked?>><i></i> 用户加减款
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('user_login', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="user_login" <?=$checked?>><i></i> 后台登陆用户
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('user_delete', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="user_delete" <?=$checked?>><i></i> 删除用户
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('main_order', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="main_order" <?=$checked?>><i></i> 查看订单
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('main_invite', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="main_invite" <?=$checked?>><i></i> 查看邀请
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('main_config', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="main_config" <?=$checked?>><i></i> 编辑系统配置
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('main_cloud', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="main_cloud" <?=$checked?>><i></i> 编辑云中心配置
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('main_smtp', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="main_smtp" <?=$checked?>><i></i> 编辑邮件配置
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('main_template', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="main_template" <?=$checked?>><i></i> 编辑模板配置
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('main_cron', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="main_cron" <?=$checked?>><i></i> CRON设置配置
	            </label>
	            <label class="checkbox-inline i-checks" style="margin-bottom: 5px;">
	            	<?php
	            		if(in_array('main_update', $permission)){
	            			$checked = 'checked';
	            		}else{
	            			$checked = '';
	            		}
	            	?>
	              <input type="checkbox" name="permission[]" value="main_update" <?=$checked?>><i></i> 主系统更新
	            </label>
	          </div>
	        </div>
            <button type="submit" class="btn btn-sm btn-primary">提交</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
<?php

include("./foot.php");

?>