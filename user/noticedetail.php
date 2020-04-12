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
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./notice.php");
  	exit;
}
$row = $DB->query("SELECT * FROM `ytidc_notice` WHERE `id`='{$id}'")->fetch_assoc();
$template = file_get_contents("../templates/".$template_name."/user_notice_detail.template");
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'notice' => $row,
	'user' => $user,
);
echo set_template($template, $template_name, $template_code);
?>