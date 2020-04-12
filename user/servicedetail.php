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
$id = daddslashes($_GET['id']);
if(empty($id) || $DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$id}' AND `userid`='{$user['id']}'")->num_rows != 1){
  	@header("Location: ./service.php");
  	exit;
}
$row = $DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$id}' AND `userid`='{$user['id']}'")->fetch_assoc();
if($row['status'] != '激活'){
	@header("Location: ./msg.php?msg=服务器状态：".$row['status']."，请联系上级处理！");
	exit();
}
$row['password'] = base64_decode($row['password']);
$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$row['product']}'")->fetch_assoc();
$template = file_get_contents("../templates/".$template_name."/user_service_detail.template");
$time_template = find_list_html("周期列表", $template);
$pdis = json_decode(url_decode($product['period']), true);
if($user['grade'] != "0" && $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'")->num_rows == 1){
  	$grade = $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'")->fetch_assoc();
  	$price = json_decode($grade['price'], true);
	if(empty($price[$row['id']])){
		$discount = $price['*'];
	}else{
		$discount = $price[$row['id']];
	}
	if(empty($discount)){
		$discount = 100;
	}
}else{
	$discount = 100;
}
foreach($pdis as $k => $v){
	$time_template_code = array(
		'name' => $v['name'],
		'price' => $discount * $v['price'] / 100,
	);
	$time_template_new = $time_template_new . template_code_replace($time_template[1][0], $time_template_code);
}
$template = str_replace($time_template[1][0], $time_template_new, $template);
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'service' => $row,
	'product' => $product,
	'user' => $user,
	'time' => $time_template_new,
);
echo set_template($template, $template_name, $template_code);
?>