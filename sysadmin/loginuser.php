<?php

include("../includes/common.php");
$session = md5($conf['admin'].$conf['password'].$conf['domain']);
if(empty($_SESSION['adminlogin']) || $_SESSION['adminlogin'] != $session){
  	@header("Location: ./login.php");
  	exit;
}
$id = daddslashes($_GET['id']);
if(empty($id)){
  	@header("Location: ./user.php");
  	exit;
}else{
	$row = $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$id}'")->fetch_assoc();
}
$_SESSION['ytidc_user'] = $row['username'];
$_SESSION['ytidc_token'] = md5($_SERVER['HTTP_HOST'].$row['password']);
@header("Location: /user/index.php");
exit;
?>