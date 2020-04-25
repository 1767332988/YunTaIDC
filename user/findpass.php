<?php

include("../includes/common.php");
if(!empty($_SESSION['ytidc_user']) && !empty($_SESSION['ytidc_token'])){
	$username = daddslashes($_SESSION['ytidc_user']);
  	$userkey = daddslashes($_SESSION['ytidc_token']);
  	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$username}'");
  	if($user->num_rows == 1){
    	$user = $user->fetch_assoc();
      	$userkey1 = md5($_SERVER['HTTP_HOST'].$user['password']);
      	if($userkey == $userkey1){
      		@header("Location: ./index.php");
      		exit;
      	}
    }
}
$act = daddslashes($_GET['act']);
require_once(ROOT. 'includes/mail.class.php');
if($act == "getcode"){
	$user = daddslashes($_POST['user']);
	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$user}'")->fetch_assoc();
	$_SESSION['reset_code'] = rand(1000000000, 9999999999);
	$mail = new SendMail;
	$mail->FindPassMail($user, $_SESSION['reset_code'], $conf, $site);
	@header("Location: ./findpass.php");
	exit;
}
if($act == "reset"){
	$code = daddslashes($_POST['code']);
	$password = daddslashes($_POST['password']);
	$user = daddslashes($_POST['user']);
	$password = md5(md5($password));
	if($code == $_SESSION['reset_code']){
		$DB->query("UPDATE `ytidc_user` SET `password`-'{$password}' WHERE `username`='{$user}'");
		exit('重置成功！<a href="./login.php">点击登陆</a>');
	}else{
		exit('验证码不正确！');
	}
}
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
);
$template = file_get_contents("../templates/".$template_name."/user_findpass.template");
$include_file = find_include_file($template);
foreach($include_file[1] as $k => $v){
		if(file_exists("../templates/".$template_name."/".$v)){
			$replace = file_get_contents("../templates/".$template_name."/".$v);
			$template = str_replace("[include[{$v}]]", $replace, $template);
		}
		
}
$template = template_code_replace($template, $template_code);
echo $template;

?>