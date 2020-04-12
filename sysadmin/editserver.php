<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('server_write', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./server.php");
  	exit;
}
$act = daddslashes($_GET['act']);
if($act == "del"){
	if(!in_array('*', $permission) && !in_array('server_delete', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
	if($DB->query("SELECT * FROM `ytidc_product` WHERE `server`='{$id}'")->num_rows >= 1){
		@header("Location: ./msg.php?msg=该服务器尚有产品使用，暂时无法删除。");
		exit;
	}else{
	  	$DB->query("DELETE FROM `ytidc_server` WHERE `id`='{$id}'");
	  	@header("Location: ./server.php");
	  	exit;
	}
}
if($act == "edit"){
  	foreach($_POST as $k => $v){
      	$value = daddslashes($v);
      	$DB->query("UPDATE `ytidc_server` SET `{$k}`='{$value}' WHERE `id`='{$id}'");
    }
  	@header("Location: ./editserver.php?id={$id}");
  	exit;
}
if($act == "connect"){
	$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$id}'")->fetch_assoc();
	if(empty($server['plugin'])){
		@header("Location: ./msg.php?msg=服务器尚未配置插件");
		exit;
	}else{
		include(ROOT.'/plugins/server/'.$server['plugin'].'/main.php');
		$function = $server['plugin'].'_ConnectServer';
		if(function_exists($function)){
			$result = $function($server);
			if($result['status'] == 'fail'){
				@header("Location: ./msg.php?msg=连接失败，返回信息：{$result['msg']}");
				exit;
			}else{
				@header("Location: ./msg.php?msg=连接成功：返回信息：{$result['msg']}");
				exit;
			}
		}else{
			@header("Location: ./msg.php?msg=该服务器插件没有提供连接测试的功能!");
			exit;
		}
	}
}
include("./head.php");
$row = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$id}'")->fetch_assoc();
$plugins = get_dir(ROOT."/plugins/server");
?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">编辑服务器</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">编辑服务器</div>
        <div class="panel-body">
          <form role="form" action="./editserver.php?act=edit&id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>服务器名称：</label>
              <input type="text" name="name" class="form-control" placeholder="服务器名称" value="<?=$row['name']?>">
            </div>
            <div class="form-group">
              <label>服务器IP</label>
              <input type="text" name="serverip" class="form-control" placeholder="服务器IP" value="<?=$row['serverip']?>">
            </div>
            <div class="form-group">
              <label>服务器域名</label>
              <input type="text" name="serverdomain" class="form-control" placeholder="服务器域名" value="<?=$row['serverdomain']?>">
            </div>
            <div class="form-group">
              <label>服务器DNS1</label>
              <input type="text" name="serverdns1" class="form-control" placeholder="服务器DNS1" value="<?=$row['serverdns1']?>">
            </div>
            <div class="form-group">
              <label>服务器DNS2</label>
              <input type="text" name="serverdns2" class="form-control" placeholder="服务器DNS2" value="<?=$row['serverdns2']?>">
            </div>
            <div class="form-group">
              <label>服务器账号</label>
              <input type="text" name="serverusername" class="form-control" placeholder="服务器账号" value="<?=$row['serverusername']?>">
            </div>
            <div class="form-group">
              <label>服务器密码</label>
              <input type="text" name="serverpassword" class="form-control" placeholder="服务器密码" value="<?=$row['serverpassword']?>">
            </div>
            <div class="form-group">
              <label>服务器哈希</label>
              <input type="text" name="serveraccesshash" class="form-control" placeholder="服务器哈希" value="<?=$row['serveraccesshash']?>">
            </div>
            <div class="form-group">
              <label>服务器控制面板</label>
              <input type="text" name="servercpanel" class="form-control" placeholder="服务器控制面板" value="<?=$row['servercpanel']?>">
            </div>
            <div class="form-group">
              <label>服务器端口</label>
              <input type="number" name="serverport" class="form-control" placeholder="服务器端口" value="<?=$row['serverport']?>">
            </div>
            <div class="form-group">
              <label>服务器接通插件</label>
              <select name="plugin" class="form-control m-b">
              	<?php
              	foreach($plugins as $k => $v){
              		if($row['plugin'] == $v){
              			echo '<option value="'.$v.'" selected>'.$k.'</option>';
              		}else{
              			echo '<option value="'.$v.'">'.$k.'</option>';
              		}
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