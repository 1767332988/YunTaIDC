<?php
include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('admin_read', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
if(daddslashes($_GET['act']) == 'add'){
	if(in_array('*', $permission) || in_array('admin_create', $permission)){
		$username = 'admin'.rand(100,999);
		$password = md5(md5($username));
		$permission = json_encode(array('*'));
		$DB->query("INSERT INTO `ytidc_admin`(`username`, `password`, `permission`, `lastip`, `status`) VALUES ('{$username}','{$password}','{$permission}','',1)");
		@header("Location: ./admin.php");
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
if(daddslashes($_GET['act']) == "search"){
	$search = daddslashes($_POST['search']);
	$result = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$search}'");
}else{
	$result = $DB->query("SELECT * FROM `ytidc_admin` LIMIT {$start}, 10");
}
include("./head.php");
?>
        <div class="bg-light lter b-b wrapper-md">
          <h1 class="m-n font-thin h3">管理员管理</h1>
        </div>
        <div class="wrapper-md">
          <div class="panel panel-default">
            <div class="panel-heading">
            	<div class="col-sm-9">
            		管理员列表<a href="./admin.php?act=add" class="btn btn-primary btn-xs btn-small">添加</a>
		        </div>
		        <form action="./admin.php?act=search" method="POST">
		        <div class="input-group col-sm-3">
		          <input type="text" class="input-sm form-control" name="search" placeholder="管理用户名">
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
                    <th>用户名称</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                	<?php
                  	 while($row = $result->fetch_assoc()){
                  	 	echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['username'].'</td>
                    <td><a href="./editadmin.php?id='.$row['id'].'" class="btn btn-primary btn-xs btn-small">编辑</a><a href="./editadmin.php?act=del&id='.$row['id'].'" class="btn btn-default btn-xs btn-small">删除</a></td>
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
		          			echo '<li><a href="./product.php?page='.$page.'"><i class="fa fa-chevron-left"></i></a></li>';
		          		}
		          		$total = $DB->query("SELECT * FROM `ytidc_product`");
		          		$records = $total->num_rows;
		          		$total_pages = ceil($records / 10);
		            	for($i = 1;$i <= $total_pages; $i++){
		            		echo '<li><a href="./product.php?page='.$i.'">'.$i.'</a></li>';
		            	}
		            	if($page+2 <= $total_pages){
		            		$next_page = $page + 2;
		            		echo '<li><a href="./product.php?page='.$next_page.'"><i class="fa fa-chevron-right"></i></a></li>';
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