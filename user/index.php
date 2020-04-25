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

$servicecount = $DB->query("SELECT * FROM `ytidc_service` WHERE `userid`='{$user['id']}'")->num_rows;
$wordercount = $DB->query("SELECT * FROM `ytidc_worder` WHERE `user`='{$user['id']}'")->num_rows;
$invitecount = $DB->query("SELECT * FROM `ytidc_user` WHERE `invite`='{$user['id']}'")->num_rows;
$noticecount = $DB->query("SELECT * FROM `ytidc_notice`")->num_rows;
$template = file_get_contents("../templates/".$template_name."/user_index.template");
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'data' => array(
		'invitecount' => $invitecount,
		'noticecount' => $noticecount,
		'servicecount' => $servicecount,
		'wordercount' => $wordercount,
	),
	'user' => $user,
);
echo set_template($template, $template_name, $template_code);
?>