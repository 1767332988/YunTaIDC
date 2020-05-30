<?php

namespace YunTaIDC\Plugin;

use YunTaIDC\Database\Database;

class PluginInstaller{
    
    public $DB;
    
    public function __construct(){
        $this->DB = new $DB;
    }
    
    public function AddPluginBaseRecord($array){
        if($this->DB->exec("INSERT INTO `ytidc_plugin`")){
            return true;
        }else{
            return false;
        }
    }
    
    public function AddPluginDataRecord($array){
        if($this->DB->exec("INSERT INTO `ytidc_plugin`")){
            return true;
        }else{
            return false;
        }
    }
    
    public function Install(){
        
    }
    
}

?>