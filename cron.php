<?php

include("./includes/common.php");
require_once("./includes/mail.class.php");
if($conf['cron_order_delete'] == 1){
	$DB->query("DELETE FROM `ytidc_order` WHERE `status`='待支付'");
}
$date = date('Y-m-d', strtotime("-{$conf['cron_service_delete']} days", time()));
//清除到期服务
$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `enddate`='{$date}'");
$crondate = date('Y-m-d');
$DB->query("UPDATE `ytidc_config` SET `v`='{$crondate}' WHERE `k`='crondate'");
while($row = $result->fetch_assoc()){
  	$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$row['product']}'");
  	if($product->num_rows != 1){
      	WriteLog(ROOT."logs/cron_error.log", "Cron删除服务{$row['username']}：产品不存在");
      	exit;
  	}else{
  		$product = $product->fetch_assoc();
  	}
  	$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$product['server']}'");
  	if($server->num_rows != 1){
      	WriteLog(ROOT."logs/cron_error.log", "Cron删除服务{$row['username']}：服务器不存在");
      	exit;
  	}else{
  		$server = $server->fetch_assoc();
  	}
  	$plugin = "./plugins/server/".$server['plugin']."/main.php";
  	if(!is_file($plugin) || !file_exists($plugin)){
      	$DB->query("DELETE FROM `ytidc_service` WHERE `id`='{$row['id']}'");
    }else{
      	include($plugin);
      	$function = $server['plugin']."_DeleteService";
      	$postdata = array(
          	'service' => $row,
          	'server' => $server,
          	'product' => $product,
        );
        if(function_exists($function)){
        	$return = $function($postdata);
    		WriteLog(ROOT."logs/cron_error.log", "Cron删除服务{$row['username']}：状态：{$return['status']}，信息：{$return['msg']}");
        }
      	$DB->query("DELETE FROM `ytidc_service` WHERE `id`='{$row['id']}'");
    }
}
$date = date('Y-m-d');
$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `enddate`='{$date}'");
while($row = $result->fetch_assoc()){
	$DB->query("UPDATE `ytidc_service` SET `status`='暂停' WHERE `id`='{$row['id']}'");
  	$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$row['product']}'");
  	if($product->num_rows != 1){
      	WriteLog(ROOT."logs/cron_error.log", "Cron暂停服务{$row['username']}：产品不存在");
      	exit;
  	}else{
  		$product = $product->fetch_assoc();
  	}
  	$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$product['server']}'");
  	if($server->num_rows != 1){
      	WriteLog(ROOT."logs/cron_error.log", "Cron暂停服务{$row['username']}：服务器不存在");
      	exit;
  	}else{
  		$server = $server->fetch_assoc();
  	}
  	$plugin = "./plugins/server/".$server['plugin']."/main.php";
  	if(!is_file($plugin) || !file_exists($plugin)){
      	WriteLog(ROOT."logs/cron_error.log", "Cron暂停服务{$row['username']}：插件不存在");
      	exit;
    }else{
      	include($plugin);
      	$function = $server['plugin']."_SuspendService";
      	$postdata = array(
          	'service' => $row,
          	'server' => $server,
          	'product' => $product,
        );
        if(function_exists($function)){
        	$return = $function($postdata);
      		WriteLog(ROOT."logs/cron_error.log", "Cron暂停服务{$row['username']}：状态：{$return['status']}，信息：{$return['msg']}");
        }
    }
}
$date = date("Y-m-d", strtotime("+{$conf['cron_mail_alert']} days", time()));
$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `enddate`='{$date}'");
while($row = $result->fetch_assoc()){
	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$row['userid']}'")->fetch_assoc();
	$mail = new SendMail();
	$mail->ServiceRenewMail($user, $row, $conf, $site);
}

exit('OK');
?>