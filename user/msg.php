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
$msg = daddslashes($_GET['msg']);
$template_code = array(
	'user' => $user,
	'site' => $site,
	'config' => $conf,
	'msg' => $msg,
	'template_file_path' => "../templates/".$template_name,
);
$template = file_get_contents("../templates/".$template_name."/user_msg.template");
echo set_template($template, $template_name, $template_code);
?>