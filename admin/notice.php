<?php
include("../includes/common.php");
if(empty($_SESSION['yuntauser']) || empty($_SESSION['userip'])){
  	@header("Location: ./login.php");
     exit;
}else{
	$user = daddslashes($_SESSION['yuntauser']);
	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$user}'")->fetch_assoc();
	if($user['lastip'] != getRealIp() || $_SESSION['userip'] != getRealIp()){
		@header("Location: ./login.php");
		exit;
	}else{
		$site = $DB->query("SELECT * FROM `ytidc_subsite` WHERE `user`='{$user['id']}'");
		if($site->num_rows != 1){
			exit('该用户未开通分站！<a href="/user">点击返回用户中心</a>');
		}else{
			$site = $site->fetch_assoc();
		}
	}
}
if(daddslashes($_GET['act']) == 'add'){
	$date = date('Y-m-d');
	$rand = rand(100, 999);
	$DB->query("INSERT INTO `ytidc_notice` (`title`, `content`, `date`, `site`, `status`) VALUES ('新建公告{$rand}', '', '{$date}', '{$site['id']}', '1')");
	@header("Location: ./notice.php");
	exit;
}
if(isset($_GET['page']) && is_numeric($_GET['page']) && $_GET['page'] >= 1){
	$page = daddslashes($_GET['page']) - 1;
}else{
	$page = 0;
}
$start = $page * 10;
$title = "公告管理";
$result = $DB->query("SELECT * FROM `ytidc_notice` WHERE `site`='{$site['id']}' LIMIT {$start}, 10");
include("./head.php");
?>
        <div class="bg-light lter b-b wrapper-md">
          <h1 class="m-n font-thin h3">公告管理</h1>
        </div>
        <div class="wrapper-md">
          <div class="panel panel-default">
            <div class="panel-heading">
              公告列表<a href="?act=add" class="btn btn-primary btn-xs btn-small">添加</a>
            </div>
            <div class="table-responsive">
              <table class="table table-striped b-t b-light">
                <thead>
                  <tr>
                    <th>编号</th>
                    <th>题目</th>
                    <th>操作</th>
                  </tr>
                </thead>
                <tbody>
                	<?php
                  	 while($row = $result->fetch_assoc()){
                  	 	echo '<tr>
                    <td>'.$row['id'].'</td>
                    <td>'.$row['title'].'</td>
                    <td><a href="./editnotice.php?id='.$row['id'].'" class="btn btn-primary btn-xs btn-small">编辑</a><a href="./editnotice.php?act=del&id='.$row['id'].'" class="btn btn-default btn-xs btn-small">删除</a></td>
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
		          			echo '<li><a href="./notice.php?page='.$page.'"><i class="fa fa-chevron-left"></i></a></li>';
		          		}
		          		$total = $DB->query("SELECT * FROM `ytidc_notice`");
		          		$records = $total->num_rows;
		          		$total_pages = ceil($records / 10);
		            	for($i = 1;$i <= $total_pages; $i++){
		            		echo '<li><a href="./notice.php?page='.$i.'">'.$i.'</a></li>';
		            	}
		            	if($page+2 <= $total_pages){
		            		$next_page = $page + 2;
		            		echo '<li><a href="./notice.php?page='.$next_page.'"><i class="fa fa-chevron-right"></i></a></li>';
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