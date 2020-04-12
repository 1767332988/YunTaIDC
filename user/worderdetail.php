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
$result = $DB->query("SELECT * FROM `ytidc_worder` WHERE `id`='{$id}' AND `user`='{$user['id']}'");
if($result->num_rows != 1){
	@header("Location: ./worder.php");
	exit;
}else{
	$row = $result->fetch_assoc();
}
if($_POST['reply']){
	$reply = daddslashes($_POST['reply']);
  	$time = date('Y-m-d H:i:s');
	$DB->query("INSERT INTO `ytidc_wreply`(`person`, `content`, `worder`, `time`) VALUES ('{$user['username']}','{$reply}','{$id}','{$time}')");
	$DB->query("UPDATE `ytidc_worder` SET `status`='待处理' WHERE `id`='{$id}'");
	@header('Location: ./msg.php?msg=回复成功');
	exit;
}
$template = file_get_contents("../templates/".$template_name."/user_worder_detail.template");
$reply_template = find_list_html('工单回复', $template);
$reply = $DB->query("SELECT * FROM `ytidc_wreply` WHERE `worder`='{$id}'");
while($row2 = $reply->fetch_assoc()){
	$reply_template_code = array(
		'person' => $row2['person'],
		'content' => $row2['content'],
		'time' => $row2['time'],
	);
	$reply_template_new = $reply_template_new . template_code_replace($reply_template[1][0], $reply_template_code);
}
$template = str_replace($reply_template[0][0], $reply_template_new, $template);
$template_code = array(
	'site' => $site,
	'config' => $conf,
	'template_file_path' => '../templates/'.$template_name,
	'user' => $user,
	'worder' => $row,
);
echo set_template($template, $template_name, $template_code);