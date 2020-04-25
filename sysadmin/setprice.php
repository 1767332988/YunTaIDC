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
$product = daddslashes($_GET['product']);
if(empty($id)){
  	@header("Location: ./price.php");
  	exit;
}
$row = $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$id}'")->fetch_assoc();
$act = daddslashes($_GET['act']);
if($act == "edit"){
	$price = json_encode(daddslashes($_POST['price']));
	$DB->query("UPDATE `ytidc_grade` SET `price`='{$price}' WHERE `id`='{$id}'");
	@header("Location: ./setprice.php?id={$id}");
	exit;
}
include("./head.php");
$product = $DB->query("SELECT * FROM `ytidc_product`");
$price = json_decode($row['price'], true);
?>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">价格设置</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">代理折扣设置（%数，100%填100）</div>
        <div class="panel-body">
          <form role="form" action="./setprice.php?act=edit&id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>默认折扣（下面不填就会使用这个）</label>
              <input type="number" name="price[*]" class="form-control" placeholder="默认折扣" value="<?=$price['*']?>">
            </div>
            <?php
            while($row2 = $product->fetch_assoc()){
            	echo '
            <div class="form-group">
              <label>'.$row2['name'].'</label>
              <input type="number" name="price['.$row2['id'].']" class="form-control" placeholder="'.$row2['name'].'折扣" value="'.$price[$row2['id']].'">
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