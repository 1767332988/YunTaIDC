<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('price_write', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./type.php");
  	exit;
}
$row = $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$id}'")->fetch_assoc();
$act = daddslashes($_GET['act']);
if($act == "del"){
	if(!in_array('*', $permission) && !in_array('price_delete', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
	if($DB->query("SELECT * FROM `ytidc_user` WHERE `grade`='{$id}'")->num_rows >= 1){
		@header("location: ./msg.php?msg=该价格组尚有用户使用，暂时无法删除。");
		exit;
	}else{
	  	$DB->query("DELETE FROM `ytidc_grade` WHERE `id`='{$id}'");
	  	@header("Location: ./price.php");
	  	exit;
	}
}
if($act == "edit"){
  	$params = daddslashes($_POST);
  	$DB->query("UPDATE `ytidc_grade` SET `name`='{$params['name']}',`description`='{$params['description']}',`weight`='{$params['weight']}',`need_paid`='{$params['need_paid']}',`need_money`='{$params['need_money']}',`need_save`='{$params['need_save']}',`default`='{$params['default']}' WHERE `id`='{$id}'");
  	if($DB->error){
      	$error_log = file_get_contents(ROOT."logs/system_error.log");
      	$error_log = $error_log .  $return['status'] .":" . $return['msg'] . "\r\n";
      	file_put_contents(ROOT."/logs/system_error.log", $error_log);
  	}
  	@header("Location: ./editprice.php?id={$id}");
  	exit;
}
include("./head.php");
$product = $DB->query("SELECT * FROM `ytidc_product`");
$price = json_decode($row['price'], true);
?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">编辑价格组</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">编辑价格组</div>
        <div class="panel-body">
          <form role="form" action="./editprice.php?act=edit&id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>价格组名称：</label>
              <input type="text" name="name" class="form-control" placeholder="价格组名称" value="<?=$row['name']?>">
            </div>
            <div class="form-group">
              <label>价格组介绍</label>
              <textarea name="description" class="form-control" row="6"><?=$row['description']?></textarea>
            </div>
            <div class="form-group">
              <label>是否设置为默认</label>
              <select name="default" class="form-control">
              	<?php if($row['default'] == '1'){
              		echo '<option value="1" selected>是</option><option value="0">否</option>';
              	}else{
              		echo '<option value="1">是</option><option value="0" selected>否</option>';
              	}
              	?>
              </select>
            </div>
            <div class="form-group">
              <label>价格组等级（越大越高）</label>
              <input type="number" name="weight" class="form-control" placeholder="价格组等级" value="<?=$row['weight']?>">
            </div>
            <div class="form-group">
              <label>开通消费要求（优先使用，0为不启用）</label>
              <input type="number" name="need_paid" class="form-control" placeholder="开通消费要求" value="<?=$row['need_paid']?>">
            </div>
            <div class="form-group">
              <label>开通预存要求（第二使用，0为不启用）</label>
              <input type="number" name="need_save" class="form-control" placeholder="开通预存要求" value="<?=$row['need_save']?>">
            </div>
            <div class="form-group">
              <label>开通价格（最后使用，0为不启用）</label>
              <input type="number" name="need_money" class="form-control" placeholder="开通价格" value="<?=$row['need_money']?>">
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