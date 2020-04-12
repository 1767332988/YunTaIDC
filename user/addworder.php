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
	}
}
$title = daddslashes($_POST['title']);
$content = daddslashes($_POST['content']);
if(!empty($title) && !empty($content)){
	$DB->query("INSERT INTO `ytidc_worder`(`title`, `user`, `status`) VALUES ('{$title}','{$user['id']}','待回复')");
	$newid = $DB->query("select MAX(id) from `ytidc_worder`")->fetch_assoc();
  	$newid = $newid['MAX(id)'];
  	$time = date('Y-m-d H:i:s');
	$DB->query("INSERT INTO `ytidc_wreply`(`person`, `content`, `worder`, `time`) VALUES ('{$user['username']}','{$content}','{$newid}','{$time}')");
	@header("Location: ./msg.php?msg=提交成功，请等待处理！");
	exit;
}

$template = file_get_contents("../templates/".$template_name."/user_addworder.template");
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'user' => $user,
);
echo set_template($template, $template_name, $template_code);
?>