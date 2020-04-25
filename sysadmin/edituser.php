<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('user_read', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./user.php");
  	exit;
}
$act = daddslashes($_GET['act']);
if($act == "del"){
	if(!in_array('*', $permission) && !in_array('user_delete', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
	if($DB->query("SELECT * FROM `ytidc_service` WHERE `user`='{$id}'")->num_rows >= 1){
		@header("Location: ./msg.php?msg=该用户尚有在线服务！");
		exit;
	}else{
  		$DB->query("DELETE FROM `ytidc_user` WHERE `id`='{$id}'");
  		@header("Location: ./user.php");
  		exit;
	}
}
if($act == "edit"){
	if(!in_array('*', $permission) && !in_array('user_write', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
  	foreach($_POST as $k => $v){
      	$value = daddslashes($v);
      	if($k == "password"){
      		if(!empty($value)){
	      		$value = md5(md5($value));
	      		$DB->query("UPDATE `ytidc_user` SET `{$k}`='{$value}' WHERE `id`='{$id}'");
      		}
      	}else{
      		$DB->query("UPDATE `ytidc_user` SET `{$k}`='{$value}' WHERE `id`='{$id}'");
      	}
    }
  	@header("Location: ./edituser.php?id={$id}");
  	exit;
}
if($act == "login"){
	if(!in_array('*', $permission) && !in_array('user_login', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
	$ip = getRealIp();
	$DB->query("UPDATE `ytidc_user` SET `lastip`='$ip' WHERE `id`='{$id}'");
	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$id}'")->fetch_assoc();
	$_SESSION['ytidc_user'] = $user['username'];
	$_SESSION['userip'] = $ip;
	@header("Location: /user/index.php");
	exit;
}
include("./head.php");
$row = $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$id}'")->fetch_assoc();
$grade = $DB->query("SELECT * FROM `ytidc_grade` WHERE `status`='1'");
$password = base64_decode($row['password']);
?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">编辑用户</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">编辑内容</div>
        <div class="panel-body">
          <form role="form" action="./edituser.php?act=edit&id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>用户邮箱</label>
              <input name="email" type="text" class="form-control" placeholder="用户邮箱" value="<?=$row['email']?>">
            </div>
            <div class="form-group">
              <label>用户密码（留空为不修改）</label>
              <input name="password" type="password" class="form-control" placeholder="用户密码">
            </div>
            <div class="form-group">
              <label>用户价格组</label>
              <select name="grade" class="form-control">
              	<option value="0">无价格组</option>
              	<?php while($row2 = $grade->fetch_assoc()){
              			if($row2['id'] == $row['grade']){
              				$selected = "selected";
              			}else{
              				$selected = '';
              			}
  						echo '
                        <option value="'.$row2['id'].'" '.$selected.'>'.$row2['name'].'</option>';
					}
                ?>
              </select>
            </div>
            <div class="form-group">
              <label>邀请上级</label>
              <input name="invite" type="text" class="form-control" placeholder="邀请上级" value="<?=$row['invite']?>">
            </div>
            <div class="form-group">
              <label>所属站点</label>
              <input name="site" type="text" class="form-control" placeholder="所属站点" value="<?=$row['site']?>">
            </div>
            <div class="form-group">
              <label>用户状态</label>
              <select name="status" class="form-control">
              	<?php
              		if($row['status'] == 1){
              			echo '<option value="1" selected>正常</option><option value="0">冻结</option>';
              		}else{
              			echo '<option value="1">正常</option><option value="0" selected>冻结</option>';
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