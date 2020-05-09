<?php
include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('service_read', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] >= 1){
	$page = daddslashes($_GET['page']) - 1;
}else{
	$page = 0;
}
$start = $page * 10;
$title = "服务管理";
if(daddslashes($_GET['act']) == "search"){
	$search = daddslashes($_POST['search']);
	$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `username`='{$search}'");
}else{
	$result = $DB->query("SELECT * FROM `ytidc_service` LIMIT {$start}, 10");
}
$product1 = $DB->query("SELECT * FROM `ytidc_product`");
while($row = $product1->fetch_assoc()){
	$product[$row['id']] = $row['name'];
}
include("./head.php");
?>
        <div class="bg-light lter b-b wrapper-md">
          <h1 class="m-n font-thin h3">服务管理</h1>
        </div>
        <div class="wrapper-md">
          <div class="panel panel-default">
            <div class="panel-heading">
            	<div class="col-sm-9">
            		服务列表
		        </div>
		        <form action="./service.php?act=search" method="POST">
		        <div class="input-group col-sm-3">
		          <input type="text" class="input-sm form-control" name="search" placeholder="服务账号">
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
                    <th>用户UID</th>
                    <th>服务账号</th>
                    <th>服务产品</th>
                    <th>到期时间</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                	<?php
                  	 while($row = $result->fetch_assoc()){
                  	 	echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['userid'].'</td>
                    <td>'.$row['username'].'</td>
                    <td>'.$product[$row['product']].'</td>
                    <td>'.$row['enddate'].'</td>
                    <td>'.$row['status'].'</td>';
                    if($row['status'] == '等待审核'){
                    	echo '<td><a href="./editservice.php?id='.$row['id'].'&act=reopen" class="btn btn-success btn-xs btn-small">开通</a><a href="./editservice.php?id='.$row['id'].'" class="btn btn-primary btn-xs btn-small">编辑</a><a href="./editservice.php?act=del&id='.$row['id'].'" class="btn btn-default btn-xs btn-small">删除</a></td>
                  </tr>';
                    }else{
                    	echo '<td><a href="./editservice.php?id='.$row['id'].'" class="btn btn-primary btn-xs btn-small">编辑</a><a href="./editservice.php?act=del&id='.$row['id'].'" class="btn btn-default btn-xs btn-small">删除</a></td>
                  </tr>';
                    }
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
		          			echo '<li><a href="./service.php?page='.$page.'"><i class="fa fa-chevron-left"></i></a></li>';
		          		}
		          		$total = $DB->query("SELECT * FROM `ytidc_service`");
		          		$records = $total->num_rows;
		          		$total_pages = ceil($records / 10);
		            	for($i = 1;$i <= $total_pages; $i++){
		            		echo '<li><a href="./service.php?page='.$i.'">'.$i.'</a></li>';
		            	}
		            	if($page+2 <= $total_pages){
		            		$next_page = $page + 2;
		            		echo '<li><a href="./service.php?page='.$next_page.'"><i class="fa fa-chevron-right"></i></a></li>';
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