<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('main_update', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$cloud = new Cloud;
$act = daddslashes($_GET['act']);
if($act == "update"){
  	$params = daddslashes($_POST);
  	$cloud->getSystemUpdate($params['version']);
  	exit('<br>更新结束！<a href="/sysadmin/index.php">点击返回主系统后台</a>');
}
include("./head.php");
$versions = $cloud->getSystemVersion();
?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">系统更新</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">更新系统</div>
        <div class="panel-body">
          <form role="form" action="./update.php?&act=update" method="POST">
            <div class="form-group">
              <label>最新版本</label>
              <input type="text" name="newest" class="form-control" placeholder="最新版本" value="<?=$versions['newest']?>" disabled>
            </div>
            <div class="form-group">
              <label>选择版本</label>
              <select name="version" class="form-control">
              	<?php
              	  foreach($versions['list'] as $k =>$v){
              	  	echo '<option value="'.$v.'">'.$k.'</select>';
              	  }
              	?>
              </select>
            </div>
            <button type="submit" class="btn btn-sm btn-primary">更新（更新将会连接到云中心）</button>
          </form>
        </div>
      </div>
    </div>
  </div>
</div>
	<?php
include("./foot.php");

?>