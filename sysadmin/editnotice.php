<?php

include("../includes/common.php");
$admin = daddslashes($_SESSION['admin']);
$admin = $DB->query("SELECT * FROM `ytidc_admin` WHERE `username`='{$admin}'")->fetch_assoc();
if($admin['lastip'] != getRealIp() || $_SESSION['adminip'] != getRealIp()){
	@header("Location: ./login.php");
	exit;
}else{
	$permission = json_decode($admin['permission'], true);
	if(!in_array('*', $permission) && !in_array('notice_write', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
	}
}
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./notice.php");
  	exit;
}
$act = daddslashes($_GET['act']);
if($act == "del"){
  	$DB->query("DELETE FROM `ytidc_notice` WHERE `id`='{$id}'");
  	@header("Location: ./notice.php");
  	exit;
}
if($act == "edit"){
	if(!in_array('*', $permission) && !in_array('notice_delete', $permission)){
		@header("Location: ./msg.php?msg=你无权限进行此操作！");
		exit;
	}
  	foreach($_POST as $k => $v){
      	$$k = daddslashes($v);
    }
  	$date = date('Y-m-d');
  	$DB->query("UPDATE `ytidc_notice` SET `title`='{$title}', `content`='{$content}', `date`='{$date}' WHERE `id`='{$id}'");
  	@header("Location: ./editnotice.php?id={$id}");
  	exit;
}
include("./head.php");
$row = $DB->query("SELECT * FROM `ytidc_notice` WHERE `id`='{$id}'")->fetch_assoc();
?>
<link rel="stylesheet" href="/assets/umeditor/themes/default/css/umeditor.css">
<!-- 引用jquery -->
<script src="/assets/umeditor/third-party/jquery.min.js"></script>
<!-- 引入 etpl -->
<script type="text/javascript" src="/assets/umeditor/third-party/template.min.js"></script>
<!-- 配置文件 -->
<script type="text/javascript" src="/assets/umeditor/umeditor.config.js"></script>
<!-- 编辑器源码文件 -->
<script type="text/javascript" src="/assets/umeditor/umeditor.js"></script>
<!-- 语言包文件 -->
<script type="text/javascript" src="/assets/umeditor/lang/zh-cn/zh-cn.js"></script>
<!-- 实例化编辑器代码 -->
<script type="text/javascript">
    $(function(){
        window.um = UM.getEditor('container', {
        	/* 传入配置参数,可配参数列表看umeditor.config.js */
            toolbar: ['source | undo redo | bold italic underline strikethrough | fontsize fontfamily paragraph | subscript superscript | link image']
        });
    });
</script>
<div class="bg-light lter b-b wrapper-md">
  <h1 class="m-n font-thin h3">编辑公告</h1>
</div>
<div class="wrapper-md" ng-controller="FormDemoCtrl">
  <div class="row">
    <div class="col-sm-12">
      <div class="panel panel-default">
        <div class="panel-heading font-bold">编辑公告</div>
        <div class="panel-body">
          <form role="form" action="./editnotice.php?act=edit&id=<?=$id?>" method="POST">
            <div class="form-group">
              <label>公告标题：</label>
              <input type="text" name="title" class="form-control" placeholder="公告标题" value="<?=$row['title']?>">
            </div>
            <div class="form-group">
              <label>公告内容</label>
              <div>
	              <script id="container" name="content" type="text/plain" style="width:100%;height:200px;">
					    <?=$row['content']?>
					</script>
    		  </dib>
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