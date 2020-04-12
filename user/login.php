<?php

include("../includes/common.php");
if(!empty($_SESSION['yuntauser']) && !empty($_SESSION['userip'])){
	$user = daddslashes($_SESSION['yuntauser']);
	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$user}'")->fetch_assoc();
	if($user['lastip'] == getRealIp() && $_SESSION['userip'] == getRealIp()){
		@header("Location: ./index.php");
		exit;
	}
}
if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['authcode'])){
    $params = daddslashes($_POST);
    if($params['authcode'] != $_SESSION['authcode']){
    	exit('验证码不正确！<a href="./login.php">点击重新登陆</a>');
    }
    $params['password'] = md5(md5($params['password']));
    $ip = getRealIp();
    if($DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$params['username']}' AND `password`='{$params['password']}'")->num_rows == 1){
    	$DB->query("UPDATE `ytidc_user` SET `lastip`='{$ip}' WHERE `username`='{$params['username']}'");
    	$_SESSION['yuntauser'] = $params['username'];
    	$_SESSION['userip'] = $ip;
    	@header("Location: ./index.php");
    	exit;
    }else{
    	exit('账户密码错误！<a href="./login.php">点击重新登陆</a>');
    }
}
$_SESSION['authcode'] = rand(100000, 999999);
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'authcode' => $_SESSION['authcode'],
);
$template = file_get_contents("../templates/".$template_name."/user_login.template");
echo set_template($template, $template_name, $template_code);

?>