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
$template = file_get_contents("../templates/".$template_name."/user_pay.template");
$gateway = $DB->query("SELECT * FROM `ytidc_gateway` WHERE `status`='1'");
$gateway_template = find_list_html("支付通道列表", $template);
while($row = $gateway->fetch_assoc()){
	$gateway_template_code = array(
		'gateway' => $row['id'],
		'name' => $row['name'],
		'fee' => $row['fee'],
	);
	$gateway_template_new = $gateway_template_new . template_code_replace($gateway_template[1][0], $gateway_template_code);
}
$template = str_replace($gateway_template[0][0], $gateway_template_new, $template);
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'user' => $user,
);
echo set_template($template, $template_name, $template_code);

?>