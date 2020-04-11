<?php

include("../includes/common.php");
$money = daddslashes($_POST['money']);
$gateway = daddslashes($_POST['gateway']);
$user = daddslashes($_POST['user']);
$gateway_plugin = $DB->query("SELECT * FROM `ytidc_gateway` WHERE `id`='{$gateway}'")->fetch_assoc();
$gateway = $gateway_plugin['gateway'];
$orderid = date('YmdHis').rand(1000,9999);
$pluginfile = ROOT."/plugins/payment/".$gateway."/main.php";
$configoption = json_decode($gateway_plugin['configoption'], true);
$DB->query("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','余额充值',$money,'加款',$user,'待支付')");
$order = array(
	'orderid' => $orderid,
	'money' => $money,
	'gatewayid' => $gateway_plugin['id'],
);
if(!file_exists($pluginfile)){
	@header("Location: /user/msg.php?msg=服务器插件不存在");
	exit;
}
include($pluginfile);
$function = $gateway."_ProcessOrder";
if(!function_exists($function)){
	@header("Location: /user/msg.php?msg=支付插件无法进行支付请求！");
	exit;
}
echo $function($configoption, $order);
?>