<?php
include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('code_read', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
if(daddslashes($_GET['act']) == 'add'){
	if(in_array('*', $permission) || in_array('code_create', $permission)){
		$code = "CODE".rand(100, 999);
		$DB->query("INSERT INTO `ytidc_promo`(`code`, `price`, `product`, `renew`, `daili`, `status`) VALUES ('{$code}','0','0','0','0','1')");
		@header("Location: ./code.php");
		exit;
	}else{
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
}
if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] >= 1){
	$page = daddslashes($_GET['page']) - 1;
}else{
	$page = 0;
}
$start = $page * 10;
$title = "优惠码管理";
if(daddslashes($_GET['act']) == "search"){
	$search = daddslashes($_POST['search']);
	$result = $DB->query("SELECT * FROM `ytidc_promo` WHERE `code`='{$search}'");
}else{
	$result = $DB->query("SELECT * FROM `ytidc_promo` LIMIT {$start}, 10");
}
$product1 = $DB->query("SELECT * FROM `ytidc_product`");
while($row = $product1->fetch_assoc()){
	$product[$row['id']] = $row['name'];
}
include("./head.php");
?>
        <div class="bg-light lter b-b wrapper-md">
          <h1 class="m-n font-thin h3">优惠码管理</h1>
        </div>
        <div class="wrapper-md">
          <div class="panel panel-default">
            <div class="panel-heading">
            	<div class="col-sm-9">
            		优惠码列表<a href="./code.php?act=add" class="btn btn-primary btn-xs btn-small">添加</a>
		        </div>
		        <form action="./code.php?act=search" method="POST">
		        <div class="input-group col-sm-3">
		          <input type="text" class="input-sm form-control" name="search" placeholder="优惠码">
		          <span class="input-group-btn">
		            <button class="btn btn-sm btn-default" type="submit">查找</button>
		          </span>
		        </div>
		        </form>
            </div>
            <div class="table-responsive">
              <table class="table table-striped b-t b-light">
                <thead>
                  <tr>
                    <th>编号</th>
                    <th>优惠码</th>
                    <th>产品</th>
                    <th>金额</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                	<?php
                  	 while($row = $result->fetch_assoc()){
                  	 	echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['code'].'</td>
                    <td>'.$product[$row['product']].'</td>
                    <td>'.$row['price'].'</td>
                    <td><a href="./editcode.php?id='.$row['id'].'" class="btn btn-primary btn-xs btn-small">编辑</a><a href="./editcode.php?act=del&id='.$row['id'].'" class="btn btn-default btn-xs btn-small">删除</a></td>
                  </tr>';
                  	 }
                  	?>
                </tbody>
              </table>
            </div>
		    <footer class="panel-footer">
		      <div class="row">
		        <div class="col-sm-12 text-right text-center-xs">                
		          <ul class="pagination pagination-sm m-t-none m-b-none">
		          	<?php
		          		if($page != 0){
		          			echo '<li><a href="./code.php?page='.$page.'"><i class="fa fa-chevron-left"></i></a></li>';
		          		}
		          		$total = $DB->query("SELECT * FROM `ytidc_promo`");
		          		$records = $total->num_rows;
		          		$total_pages = ceil($records / 10);
		            	for($i = 1;$i <= $total_pages; $i++){
		            		echo '<li><a href="./code.php?page='.$i.'">'.$i.'</a></li>';
		            	}
		            	if($page+2 <= $total_pages){
		            		$next_page = $page + 2;
		            		echo '<li><a href="./code.php?page='.$next_page.'"><i class="fa fa-chevron-right"></i></a></li>';
		            	}
		            ?>
		            
		          </ul>
		        </div>
		      </div>
		    </footer>
          </div>
        </div>
<?php

include("./foot.php");
?>