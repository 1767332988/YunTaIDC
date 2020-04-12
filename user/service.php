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
$product1 = $DB->query("SELECT * FROM `ytidc_product`");
while($row = $product1->fetch_assoc()){
	$product[$row['id']] = $row['name'];
}
include("./head.php");
$template = file_get_contents("../templates/".$template_name."/user_service.template");
$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `userid`='{$user['id']}'");
$service_template = find_list_html("在线服务列表", $template);
while($row = $result->fetch_assoc()){
	$service_template_code = array(
		'id' => $row['id'],
		'username' => $row['username'],
		'password' => $row['password'],
		'enddate' => $row['enddate'],
		'product' => $product[$row['product']],
		'status' => $row['status'],
	);
	$service_template_new = $service_template_new . template_code_replace($service_template[1][0], $service_template_code);
}
$template = str_replace($service_template[0][0], $service_template_new, $template);
$include_file = find_include_file($template);
foreach($include_file[1] as $k => $v){
		if(file_exists("../templates/".$template_name."/".$v)){
			$replace = file_get_contents("../templates/".$template_name."/".$v);
			$template = str_replace("[include[{$v}]]", $replace, $template);
		}
		
}
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'user' => $user,
);
$template = template_code_replace($template, $template_code);
echo $template;

?>