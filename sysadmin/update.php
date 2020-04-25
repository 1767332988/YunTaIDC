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
        <div class="panel-heading font-bold">注意事项</div>
        <div class="panel-body">
          <ol>
          	<li>更新将会连接到云中心的服务器，更新包下载失败可能是云中心服务器遭到攻击，请耐性等候恢复!</li>
          	<li>每个更新包名称后面都会附有最低版本信息，请确保达到该版本后在进行更新！</li>
          	<li>在线更新功能不保证每次都成功，更新前请先备份数据以防意外！若发现更新失败导致系统崩溃，可以到Gitee或者Github进行下载！</li>
          	<li>若不想有任何与云中心的连接可以直接到Gitee或者Github下载。</li>
          </ol>
          </form>
        </div>
      </div>
    </div>
  </div>
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
              <label>当前版本</label>
              <input type="text" name="now" class="form-control" placeholder="当前版本" value="2.4.10" disabled>
            </div>
            <div class="form-group">
              <label>选择版本</label>
              <select name="version" class="form-control">
              	<?php
              	  foreach($versions['list'] as $k =>$v){
              	  	echo '<option value="'.$v.'">'.$k.'</option>';
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