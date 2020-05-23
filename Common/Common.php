<?php
//error_reporting(0);
define("CACHE_FILE", 0);
define("IN_CRONLITE", true);
define("SYSTEM_ROOT", dirname(__FILE__) . "/");
define("ROOT", dirname(SYSTEM_ROOT) . "/");
session_start();
require_once(ROOT."/Common/System.php");
//require_once(ROOT."/Common/Template/Template.php");
require_once(ROOT."/Common/Security.php");
require_once(ROOT."/Common/Format.php");
require_once(ROOT."/Common/User.php");
use YunTaIDC\System\System;

$system = new System();
try{
    $system->LoadSystem();
} catch(Exception $e){
    exit("云塔提示错误信息：".$e);
}
?>