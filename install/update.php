<?php
//2.4.02更新版本
require_once("../config.php");
$DB = new Mysqli($dbconfig['host'], $dbconfig['user'], $dbconfig['pass'], $dbconfig['name'], $dbconfig['port']);
$sqlfile = file_get_contents("./update.sql");
$sqlquery = explode(';', $sqlfile);
$DB->query("set sql_mode = ''");
$DB->query("set names utf8");
$t = 0;
$e = 0;
$error = '';
for($i=0;$i<count($sqlquery);$i++) {
	if ($sqlquery[$i]=='')continue;
	if($DB->query($sqlquery[$i])) {
		++$t;
	} else {
		++$e;
		$error.=$DB->error.'<br/>';
	}
}
unlink('./update.sql');
unlink('./update.php');
exit('更新数据库成功！若没有失败可以直接返回！<a href="/">点击返回首页</a><br>成功数量：'.$t.'<br>失败数量：'.$e.'<br>失败信息：'.$error);

?>