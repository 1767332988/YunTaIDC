<?php
include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('price_read', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
if(daddslashes($_GET['act']) == 'add'){
	if(in_array('*', $permission) || in_array('price_create', $permission)){
		$name = "新建价格组".rand(100, 999);
		$DB->query("INSERT INTO `ytidc_grade`(`name`, `description`, `weight`, `need_paid`, `need_money`, `need_save`, `default`, `price`, `status`) VALUES ('{$name}','','0','0','0','0','0','','1')");
		@header("Location: ./price.php");
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
$title = "价格管理";
$result = $DB->query("SELECT * FROM `ytidc_grade` LIMIT {$start}, 10");
include("./head.php");
?>
        <div class="bg-light lter b-b wrapper-md">
          <h1 class="m-n font-thin h3">价格组管理</h1>
        </div>
        <div class="wrapper-md">
          <div class="panel panel-default">
            <div class="panel-heading">
              价格组列表<a href="./price.php?act=add" class="btn btn-primary btn-xs btn-small">添加</a>
            </div>
            <div class="table-responsive">
              <table class="table table-striped b-t b-light">
                <thead>
                  <tr>
                    <th>编号</th>
                    <th>名称</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                	<?php
                  	 while($row = $result->fetch_assoc()){
                  	 	echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['name'].'</td>
                    <td><a href="./editprice.php?id='.$row['id'].'" class="btn btn-primary btn-xs btn-small">编辑</a><a href="./setprice.php?id='.$row['id'].'" class="btn btn-success btn-xs btn-small">设置价格</a><a href="./editprice.php?act=del&id='.$row['id'].'" class="btn btn-default btn-xs btn-small">删除</a></td>
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
		          			echo '<li><a href="./price.php?page='.$page.'"><i class="fa fa-chevron-left"></i></a></li>';
		          		}
		          		$total = $DB->query("SELECT * FROM `ytidc_grade`");
		          		$records = $total->num_rows;
		          		$total_pages = ceil($records / 10);
		            	for($i = 1;$i <= $total_pages; $i++){
		            		echo '<li><a href="./price.php?page='.$i.'">'.$i.'</a></li>';
		            	}
		            	if($page+2 <= $total_pages){
		            		$next_page = $page + 2;
		            		echo '<li><a href="./price.php?page='.$next_page.'"><i class="fa fa-chevron-right"></i></a></li>';
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