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
$title = "产品订购确认";
$id = daddslashes($_GET['id']);
$row = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$id}'")->fetch_assoc();
$pdis = json_decode(url_decode($row['period']), true);
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
$template = file_get_contents("../templates/".$template_name."/user_cart.template");
$time_template = find_list_html("周期列表", $template);
foreach($pdis as $k => $v){
	$time_template_code = array(
		'name' => $v['name'],
		'price' => $v['price'] * $discount / 100,
	);
	$time_template_new = $time_template_new . template_code_replace($time_template[1][0], $time_template_code);
}
$template = str_replace($time_template[1][0], $time_template_new, $template);
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'user' => $user,
	'product' => array(
		'id' => $row['id'],
		'name' => $row['name'],
		'description' => $row['description'],
	),
);
echo set_template($template, $template_name, $template_code);
?>