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
$id = daddslashes($_GET['update']);
if(!empty($id)){
	$usergrade = $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'")->fetch_assoc();
	$grade = $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$id}'")->fetch_assoc();
	if(empty($grade)){
		@header("Location: ./msg.php?msg=该等级不存在！");
		exit;
	}
	if($grade['default'] == 1){
		@header("Location: ./msg.php?msg=默认价格组不支持被升降级！");
		exit;
	}
	if($usergrade['weight'] >= $grade['weight']){
		@header("Location: ./msg.php?msg=不能开通比您现在低等级的价格组哦！！");
		exit;
	}
	$payment = $DB->query("SELECT * FROM `ytidc_order` WHERE `user`='{$user['id']}' AND `action`='扣款'");
	while($pay = $payment->fetch_assoc()){
		$money = $money + $pay['money'];
	}
	if($money >= $grade['need_paid'] && $grade['need_paid'] != 0){
		$DB->query("UPDATE `ytidc_user` SET `grade`='{$grade['id']}' WHERE `id`='{$user['id']}'");
		@header("Location: ./msg.php?msg=升级成功");
		exit;
	}
	if($user['money'] >= $grade['need_save']&& $grade['need_save'] != 0){
		$DB->query("UPDATE `ytidc_user` SET `grade`='{$grade['id']}' WHERE `id`='{$user['id']}'");
		@header("Location: ./msg.php?msg=升级成功");
		exit;	
	}
	if($user['money'] >= $grade['need_money'] && $grade['need_money'] != 0){
		$new_money = $user['money'] - $grade['need_money'];
		$DB->query("UPDATE `ytidc_user` SET `grade`='{$grade['id']}', `money`='{$new_money}' WHERE `id`='{$user['id']}'");
		@header("Location: ./msg.php?msg=升级成功");
		exit;
	}
	@header("Location: ./msg.php?msg=开通失败，请联系管理员进行开通！");
	exit;
}
$template = file_get_contents("../templates/".$template_name."/user_price.template");
$price_template = find_list_html("价格组列表", $template);
$result = $DB->query("SELECT * FROM `ytidc_grade` WHERE `status`='1'");
while($row2 = $result->fetch_assoc()){
	$price_template_code = array(
		'name' => $row2['name'],
		'id' => $row2['id'],
		'description' => $row2['description'],
		'need_save' => $row2['need_save'],
		'need_money' => $row2['need_money'],
		'need_paid' => $row2['need_paid'],
	);
	$price_template_new = $price_template_new . template_code_replace($price_template[1][0], $price_template_code);
}
$template = str_replace($price_template[0][0], $price_template_new, $template);
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