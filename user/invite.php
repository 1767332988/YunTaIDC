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
include("./head.php");
$template = file_get_contents("../templates/".$template_name."/user_invite.template");
$result = $DB->query("SELECT * FROM `ytidc_user` WHERE `invite`='{$user['id']}'");
$invite_template = find_list_html("邀请列表", $template);
while($row = $result->fetch_assoc()){
	$invite_template_code = array(
		'id' => $row['id'],
		'username' => $row['username'],
	);
	$invite_template_new = $invite_template_new . template_code_replace($invite_template[1][0], $invite_template_code);
}
$template = str_replace($invite_template[0][0], $invite_template_new, $template);
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
	'invite_link' => $conf['http']."://".$_SERVER['HTTP_HOST']."/user/register.php?code=".$user['id'],
);
$template = template_code_replace($template, $template_code);
echo $template;

?>