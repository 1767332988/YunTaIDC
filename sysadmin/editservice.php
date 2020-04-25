<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('service_write', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./service.php");
  	exit;
}
$act = daddslashes($_GET['act']);
if($act == "reopen"){
	if(!in_array('*', $permission) && !in_array('service_reopen', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
	$service = $DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$id}'");
	if($service->num_rows != 1){
		@header("Location: ./msg.php?msg=服务不存在");
		exit;
	}else{
		$service = $service->fetch_assoc();
	}
	if($service['status'] != '等待审核'){
		@header("Location: ./msg.php?msg=该服务非等待审核状态");
		exit;
	}
	$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$service['product']}'")->fetch_assoc();
	$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$product['server']}'")->fetch_assoc();
	if(!file_exists(ROOT.'plugins/server/'.$server['plugin'].'/main.php')){
		@header("Location: ./msg.php?msg=服务器插件不存在");
		exit;
	}else{
		include_once(ROOT.'plugins/server/'.$server['plugin'].'/main.php');
		$postdata = array(
			'server' => $server,
			'product' => $product,
			'service' => array(
				'username' => $service['username'],
				'password' => base64_decode($service['password']),
				'time' => json_decode(url_decode($service['period']), true),
			),
		);
		$function = $server['plugin']."_CreateService";
		$return = $function($postdata);
		if($return['status'] != "success"){
			WriteLog(ROOT."/logs/service_error.log", "服务{$params['username']}开通失败，返回信息：{$return['msg']}");
		  	@header("Location: ./msg.php?msg=服务器返回错误：{$return['msg']}");
		  	exit;
		}else{
			$new_password = base64_encode($return['password']);
			$DB->query("UPDATE `ytidc_service` SET `username`='{$return['username']}',`password`='{$new_password}',`enddate`='{$return['enddate']}',`configoption`='{$return['configoption']}',`status`='激活' WHERE `id`='{$id}'");
		  	@header("Location: ./msg.php?msg=开通成功");
		  	exit;
		}
	}
}
if($act == "del"){
	if(!in_array('*', $permission) && !in_array('service_delete', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
  	$DB->query("DELETE FROM `ytidc_service` WHERE `id`='{$id}'");
  	@header("Location: ./service.php");
  	exit;
}
if($act == "edit"){
  	foreach($_POST as $k => $v){
      	$value = daddslashes($v);
      	if($k == 'password'){
      		$value = base64_encode($value);
      	}
      	$DB->query("UPDATE `ytidc_service` SET `{$k}`='{$value}' WHERE `id`='{$id}'");
    }
  	@header("Location: ./editservice.php?id={$id}");
  	exit;
}
include("./head.php");
$row = $DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$id}'")->fetch_assoc();
$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$row['product']}'")->fetch_assoc();
?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">编辑在线服务</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">编辑服务</div>
        <div class="panel-body">
          <form role="form" action="./editservice.php?act=edit&id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>购买服务产品名称</label>
              <input type="text" class="form-control" value="<?=$product['name']?>" disabled>
            </div>
            <div class="form-group">
              <label>服务账号</label>
              <input type="text" name="username" class="form-control" placeholder="服务账号" value="<?=$row['username']?>">
            </div>
            <div class="form-group">
              <label>服务密码</label>
              <input type="text" name="password" class="form-control" placeholder="服务密码" value="<?php echo base64_decode($row['password']); ?>">
            </div>
            <div class="form-group">
              <label>服务到期时间</label>
              <input type="date" name="enddate" class="form-control" placeholder="服务到期时间" value="<?=$row['enddate']?>">
            </div>
            <div class="form-group">
              <label>服务状态</label>
              <select class="form-control" name="status">
              	<?php
              	if($row['status'] == '等待审核'){
              		echo '<option value="激活">激活</option><option value="等待审核" selected>等待审核</option>';
              	}else{
              		echo '<option value="激活" selected>激活</option><option value="等待审核">等待审核</option>';
              	}
              	?>
              </select>
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