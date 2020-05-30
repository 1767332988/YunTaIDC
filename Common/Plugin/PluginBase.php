<?php

namespace YunTaIDC\Plugin;

use YunTaIDC\Database\Database;

class PluginBase{
    
    public $config;
    
    public function onLoad($config){
        $this->config = $config;
    }
    
    public function onInstall(){
        return true;
    }
    
    public function onAdminConfig($config){
        return true;
    }
    
    public function GetConfig(){}
    
}

?>