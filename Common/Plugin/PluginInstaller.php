<?php

namespace YunTaIDC\Plugin;

use YunTaIDC\Database\Database;

class PluginInstaller{
    
    public $DB;
    public $PluginFilePath;
    
    public function __construct(){
        $this->PluginFilePath = ROOT.'/plugins/';
        $this->DB = new $DB;
    }
    
    public function AddPluginConfigRecord($array){
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
    
    public function DeleteInstaller($plugin){
        $InstallerFile = $this->PluginFilePath . '/' . $plugin . '/installer.php';
        if(file_exists($InstallerFile)){
            unlink($InstallerFile);
            return true;
        }else{
            return true;
        }
    }
    
}

?>