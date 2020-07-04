<?php
namespace YunTaIDC\System;

//require_once(ROOT."/Common/Plugin/PluginLoader.php");
use YunTaIDC\Database\Database;
//use Plugin\Plugin\PluginLoader;
use YunTaIDC\Input\Input;
use YunTaIDC\Template\Template;
use Exception as Exception;
use Pages as Pages;

class System{
    
    public $DB;
    public $PluginLoader;
    public $conf;
    public $site;
    
    public function getVersion(){
        return '3.0.0';
    }
    
    public function LoadDatabase(){
        $this->DB = new Database();
    }
    
    public function LoadSystem(){
        //$this->PluginLoader = new PluginLoader();
        //$this->PluginLoader->LoadAllPlugin();
        //$this->status = "success";
        $this->LoadDatabase();
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
        $Input = new Input();
        $getparams = $Input->daddslashes($_GET);
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
        if(new Pages($m, $this->conf, $this->site)){
            return true;
        }else{
            return false;
        }
    }
    
    public function GetConfig(){
        return $this->conf;
    }
    
    public function GetAllType($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_type`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_type` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllUser($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_user`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_user` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllServer($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_server`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_server` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllProduct($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_product`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_product` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllPriceSet($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_priceset`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_priceset` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllWorkorder($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_workorder`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_workorder` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllSubsite($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_subsite`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_subsite` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllPromocode($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_promocode`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_promocode` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllPlugin($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_plugin`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_plugin` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllAdmin($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_admin`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_admin` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllNotice($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_notice`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_notice` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllOrder($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_order`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_order` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetAllService($params = array()){
        $rows = array();
        if(empty($params)){
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_service`") as $row){
                $rows[] = $row;
            }
        }else{
            foreach($this->DB->get_rows("SELECT * FROM `ytidc_service` WHERE `{$params['key']}`='{$params['value']}'") as $row){
                $rows[] = $row;
            }
        }
        return $rows;
    }
    
    public function GetType($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_type` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetUser($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_user` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetServer($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_server` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetProduct($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_product` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetPriceset($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_priceset` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetWorkorder($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_workorder` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetSubsite($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_subsite` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetPromocode($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_promocode` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetPlugin($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_plugin` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetAdmin($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_admin` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetNotice($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_notice` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetOrder($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_order` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
    
    public function GetService($params){
        $rows = array();
        foreach($params as $k => $v){
            $rows = $this->DB->get_row("SELECT * FROM `ytidc_service` WHERE `{$k}`='{$v}'");
        }
        return $rows;
    }
}

?>