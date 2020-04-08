<?php

include("./includes/common.php");
require_once("./includes/mail.class.php");
$date = date('Y-m-d');
//清除到期服务
$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `enddate`='{$date}'");
$DB->query("UPDATE `ytidc_config` SET `v`='{$date}' WHERE `k`='crondate'");
while($row = $result->fetch_assoc()){
  	$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$row['product']}'")->fetch_assoc();
  	$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$product['server']}'")->fetch_assoc();
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
      	$return = $function($postdata);
      	WriteLog(ROOT."logs/cron_error.log", "Cron删除服务{$return['status']}：{$return['msg']}");
      	$DB->query("DELETE FROM `ytidc_service` WHERE `id`='{$row['id']}'");
    }
}

$date = date("Y-m-d", strtotime("+7 days", time()));
$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `enddate`='{$date}'");
while($row = $result->fetch_assoc()){
	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$row['userid']}'")->fetch_assoc();
	$mail = new SendMail();
	$mail->ServiceRenewMail($user, $row, $conf, $site);
}

exit('OK');
?>