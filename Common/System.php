<?php
namespace YunTaIDC\System;

require_once(ROOT."/Common/Database.php");
//require_once(ROOT."/Common/Plugin/PluginLoader.php");
require_once(ROOT.'/Common/Security.php');
require_once(ROOT."/Common/Template.php");
use YunTaIDC\Database\Database;
//use Plugin\Plugin\PluginLoader;
use YunTaIDC\Security\Security;
use YunTaIDC\Template\Template;
use Exception as Exception;
use Pages as Pages;

class System{
    
    public $DB;
    public $PluginLoader;
    public $conf;
    public $site;
    
    public function LoadSystem(){
        //$this->PluginLoader = new PluginLoader();
        //$this->PluginLoader->LoadAllPlugin();
        //$this->status = "success";
        require_once(ROOT."/config.php");
        $this->DB = new DataBase($dbconfig);
        if(!$this->DB){
            throw new Exception("数据库连接失败[Database Connection Failure]");
            return;
        }
        if(!$this->LoadConfig()){
            throw new Exception("系统配置加载失败[System Configuration Load Failure]");
            return;
        }
        if(!$this->LoadSite()){
            throw new Exception("站点配置加载失败[Site Configuration Load Failure]");
            return;
        }
        $security = new Security();
        $getparams = $security->daddslashes($_GET);
        if(!$this->LoadPages($getparams)){
            throw new Exception("页面加载失败[LoadPages Failure]");
            return;
        }
    }
    
    public function LoadConfig(){
        $conf = array();
        $DB = $this->DB;
        foreach($DB->get_rows("SELECT * FROM `ytidc_config`") as $row){
            $conf[$row['k']] = $row['v'];
        }
        $this->conf = $conf;
        return true;
    }
    
    public function LoadSite(){
        $domain = $_SERVER['HTTP_HOST'];
        if($this->DB->num_rows("SELECT * FROM `ytidc_subsite` WHERE `domain`='{$domain}'") != 1){
            $this->site = array(
                'title' => $this->conf['mainsite_title'],
                'subtitle' => $this->conf['mainsite_subtitle'],
                'domain' => $_SERVER['HTTP_HOST'],
                'description' => $this->conf['mainsite_description'],
                'keywords' => $this->conf['mainsite_keywords'],
                'id' => 0,
                'status' => 1,
                'user' => 0,
            );
        }else{
            $this->site = $this->DB->get_row("SELECT * FROM `ytidc_subsite` WHERE `domain`='{$domain}'");
        }
        if(!empty($this->site)){
            return true;
        }else{
            return false;
        }
    }
    
    public function LoadPages($params){
        if(empty($params['p']) || empty($params['m'])){
            $p = "index";
            $m = "index";
        }else{
            $p = $params['p'];
            $m = $params['m'];
        }
        if(!file_exists(ROOT."/Common/Pages/".$p.'.php')){
            return false;
        }
        require_once(ROOT."/Common/Pages/".$p.'.php');
        if(new Pages($m, $this->conf, $this->site, $this->DB, $params)){
            return true;
        }else{
            return false;
        }
    }
    
}

?>