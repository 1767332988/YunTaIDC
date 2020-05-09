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
if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] >= 1){
	$page = daddslashes($_GET['page']) - 1;
}else{
	$page = 0;
}
$start = $page * 10;
$title = "用户管理";
if(daddslashes($_GET['act']) == "search"){
	$search = daddslashes($_POST['search']);
	$result = $DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$search}'");
}else{
	$result = $DB->query("SELECT * FROM `ytidc_user` LIMIT {$start}, 10");
}
include("./head.php");
?>
        <div class="bg-light lter b-b wrapper-md">
          <h1 class="m-n font-thin h3">用户管理</h1>
        </div>
        <div class="wrapper-md">
          <div class="panel panel-default">
            <div class="panel-heading">
            	<div class="col-sm-9">
            		用户列表
		        </div>
		        <form action="./user.php?act=search" method="POST">
		        <div class="input-group col-sm-3">
		          <input type="text" class="input-sm form-control" name="search" placeholder="用户名">
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
                    <th>账号</th>
                    <th>邮箱</th>
                    <th>余额</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                	<?php
                  	 while($row = $result->fetch_assoc()){
                  	 	echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['username'].'</td>
                    <td>'.$row['email'].'</td>
                    <td>'.$row['money'].'</td>
                    <td><a href="./edituser.php?id='.$row['id'].'" class="btn btn-primary btn-xs btn-small">编辑</a><a href="./addfund.php?id='.$row['id'].'" class="btn btn-info btn-xs btn-small">加款/减款</a><a href="./edituser.php?act=login&id='.$row['id'].'" class="btn btn-success btn-xs btn-small">登陆用户</a><a href="./edituser.php?act=del&id='.$row['id'].'" class="btn btn-default btn-xs btn-small">删除</a></td>
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
		          			echo '<li><a href="./user.php?page='.$page.'"><i class="fa fa-chevron-left"></i></a></li>';
		          		}
		          		$total = $DB->query("SELECT * FROM `ytidc_user`");
		          		$records = $total->num_rows;
		          		$total_pages = ceil($records / 10);
		            	for($i = 1;$i <= $total_pages; $i++){
		            		echo '<li><a href="./user.php?page='.$i.'">'.$i.'</a></li>';
		            	}
		            	if($page+2 <= $total_pages){
		            		$next_page = $page + 2;
		            		echo '<li><a href="./user.php?page='.$next_page.'"><i class="fa fa-chevron-right"></i></a></li>';
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