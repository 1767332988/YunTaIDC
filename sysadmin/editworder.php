<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('worder_write', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./worder.php");
  	exit;
}
$act = daddslashes($_GET['act']);
if($act == "del"){
	if(!in_array('*', $permission) && !in_array('worder_delete', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
  	$DB->query("DELETE FROM `ytidc_worder` WHERE `id`='{$id}'");
  	@header("Location: ./worder.php");
  	exit;
}
if($act == "edit"){
  	$reply = daddslashes($_POST['reply']);
  	$time = date('Y-m-d H:i:s');
  	$DB->query("INSERT INTO `ytidc_wreply`(`person`, `content`, `worder`, `time`) VALUES ('{$_SESSION['admin']}','{$reply}','{$id}','{$time}')");
  	$DB->query("UPDATE `ytidc_worder` SET `status`='已回复' WHERE `id`='{$id}'");
  	@header("Location: ./worder.php");
  	exit;
}
include("./head.php");
$row = $DB->query("SELECT * FROM `ytidc_worder` WHERE `id`='{$id}'")->fetch_assoc();
$reply = $DB->query("SELECT * FROM `ytidc_wreply` WHERE `worder`='{$row['id']}'");
?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">回复工单</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">工单内容</div>
        <div class="panel-body">
          <form role="form" action="./editworder.php?act=edit&id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>工单题目</label>
              <input type="text" class="form-control" placeholder="工单题目" value="<?=$row['title']?>" disabled="">
            </div>
            <div class="form-group">
	           <label>回复工单</label>
	           <textarea class="form-control" name="reply"></textarea>
	        </div>
            <?php
            	while($row2 = $reply->fetch_assoc()){
	            	echo '
	            <div class="form-group">
	              <label>'.$row2['person'].'（'.$row2['time'].'）</label>
	              <textarea class="form-control" disabled>'.$row2['content'].'</textarea>
	            </div>';
            	}
            ?>
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