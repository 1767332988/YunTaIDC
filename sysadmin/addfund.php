<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('user_fund', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./user.php");
  	exit;
}else{
	$row = $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$id}'")->fetch_assoc();
}
if(!empty($_POST['money']) && !empty($_POST['action'])){
	$money = daddslashes($_POST['money']);
	$action = daddslashes($_POST['action']);
	$orderid = date('YmdHis').rand(1000,9999);
	if($action == "add"){
		$new_money = $row['money'] + $money;
		$DB->query("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','管理员加款','{$money}','加款','{$id}','已完成')");
		$DB->query("UPDATE `ytidc_user` SET `money`='{$new_money}' WHERE `id`='{$id}'");
		@header("Location: ./user.php");
		exit;
	}else{
		$new_money = $row['money'] - $money;
		if($new_money < 0){
			@header("Location: ./msg.php?msg=用户余额不能为负");
			exit;
		}else{
			$DB->query("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','管理员扣款','{$money}','扣款','{$id}','已完成')");
			$DB->query("UPDATE `ytidc_user` SET `money`='{$new_money}' WHERE `id`='{$id}'");
			@header("Location: ./user.php");
			exit;
		}
	}
}
include("./head.php");
?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">余额管理</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">手动加/减款</div>
        <div class="panel-body">
          <form role="form" action="./addfund.php?id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>用户余额</label>
              <input type="text" class="form-control" value="<?=$row['money']?>" oninput="value=value.replace(/[^\d.]/g,'')" disabled>
            </div>
            <div class="form-group">
              <label>增加余额</label>
              <input name="money" type="text" class="form-control" placeholder="新增余额" oninput="value=value.replace(/[^\d.]/g,'')">
            </div>
            <div class="form-group">
              <label>详细操作</label>
              <select name="action" class="form-control">
              	<option value="add">加款</option>
              	<option value="minus">减款</option>
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