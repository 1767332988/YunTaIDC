<?php

include("../../includes/common.php");
foreach($_GET as $k => $v){
	$params[$k] = daddslashes($v);
}
if(empty($_SESSION['service'])){
	if(empty($params['username']) || empty($params['password'])){
		exit('请先登陆服务器！');
	}else{
		$params['password'] = base64_encode($params['password']);
		$service = $DB->query("SELECT * FROM `ytidc_service` WHERE `username`='{$params['username']}' AND `password`='{$params['password']}'");
		if($service->num_rows != 1){
			exit('服务不存在！');
		}else{
			$service = $service->fetch_assoc();
			$_SESSION['service']  = $service['id'];
		}
	}
}else{
	$service = $DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$_SESSION['service']}'");
	if($service->num_rows != 1){
		exit('登陆cookie超时！请重新登陆');
	}else{
		$service = $service->fetch_assoc();
	}
}
if(empty($params['act'])){
	$params['act'] = "LoginService";
}
$service['password'] = base64_decode($service['password']);
$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$service['product']}'");
if($product->num_rows != 1){
	exit("产品不存在");
}else{
	$product = $product->fetch_assoc();
}
$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$product['server']}'");
if($server->num_rows != 1){
	@header("服务器不存在");
	exit;
}else{
	$server = $server->fetch_assoc();
}
$plugin = ROOT."/plugins/server/".$server['plugin']."/clientarea.php";
if(!file_exists($plugin)){
	exit('该服务器插件没有提供管理操作中心！');
}
include_once($plugin);
$function = 'ClientArea_'.$params['act'];
if(!function_exists($function)){
	$function = 'ClientArea_LoginService';
	if(!function_exists($function)){
		@header("该服务器插件无法对服务进行登陆！");
		exit;
	}
}
$postdata = array(
	'service' => $service,
	'product' => $product,
	'server' => $server,
);
if(file_exists(ROOT.'plugins/server/'.$server['plugin'].'/clientarea.template')){
	$postdata['clientarea'] = file_get_contents(ROOT.'plugins/server/'.$server['plugin'].'/clientarea.template');
}
$function($postdata);

?>