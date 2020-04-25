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
if(!empty($_POST['email'])){
	if(!empty($_POST['password'])){
		$password = md5(md5(daddslashes($_POST['password'])));
	}else{
		$password = $user['password'];
	}
	$email = daddslashes($_POST['email']);
    $DB->query("UPDATE `ytidc_user` SET `password`='{$password}', `email`='{$email}' WHERE `username`='{$user['username']}'");
    @header("Location: ./msg.php?msg=修改成功");
    exit;
}
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'user' => $user,
	'template_file_path' => '../templates/'.$template_name,
);
$template = file_get_contents("../templates/".$template_name."/user_info.template");
echo set_template($template, $template_name, $template_code);

?>