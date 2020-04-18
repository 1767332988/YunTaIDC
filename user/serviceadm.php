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
$act = daddslashes($_GET['act']);
if(empty($act)){
	$act = "LoginService";
}
include_once('../includes/splg.func.php');
if(empty($_GET['id']) && empty($_SESSION['serviceid'])){
	@header("Location: ./service.php");
	exit;
}else{
	if(!empty($_GET['id'])){
		$id = daddslashes($_GET['id']);
		if($DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$id}' AND `userid`='{$user['id']}'")->num_rows != 1){
			@header("Location: ./service.php");
			exit;
		}else{
			$_SESSION['serviceid'] = $id;
			$service = $DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$id}' AND `userid`='{$user['id']}'")->fetch_assoc();
		}
	}else{
		if($DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$_SESSION['serviceid']}' AND `userid`='{$user['id']}'")->num_rows != 1){
			@header("Location: ./service.php");
			exit;
		}else{
			$service = $DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$_SESSION['serviceid']}' AND `userid`='{$user['id']}'")->fetch_assoc();
		}
	}
}
if($service['status'] != '激活'){
	@header("Location: ./msg.php?msg=服务状态：{$service['status']}！暂时不能进行管理！");
}
$xervice['pasword'] = base64_decode($service['password'])
$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$service['product']}'");
if($product->num_rows != 1){
	@header("Location: ./msg.php?msg=产品不存在");
	exit;
}else{
	$product = $product->fetch_assoc();
}
$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$product['server']}'");
if($server->num_rows != 1){
	@header("Location: ./msg.php?msg=服务器不存在");
	exit;
}else{
	$server = $server->fetch_assoc();
}
$plugin = $server['plugin'];
$pluginfile = ROOT.'plugins/server/'.$plugin.'/clientarea.php';
if(!file_exists($pluginfile)){
	@header("Location: ./msg.php?msg=该服务器插件没有提供管理操作中心！");
	exit;
}
include_once($pluginfile);
$function = 'ClientArea_'.$act;
if(!function_exists($function)){
	$function = 'ClientArea_LoginService';
	if(!function_exists($function)){
		@header("Location: ./msg.php?msg=该服务器插件无法对服务进行登陆！");
		exit;
	}
}
$postdata = array(
	'service' => $service,
	'server' => $server,
	'product' => $product,
	
);
if(file_exists(ROOT.'plugins/server/'.$plugin.'/clientarea.template')){
	$postdata['clientarea'] = file_get_contents(ROOT.'plugins/server/'.$plugin.'/clientarea.template');
}
$function($postdata);

?>