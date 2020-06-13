<?php

namespace YunTaIDC\Plugin;

use YunTaIDC\Database\Database;
use Exception as Exception;

class PluginBase{
    
    public $database;
    public $PluginId;
    
    public function __construct($pluginid){
        $this->PluginId = $pluginid;
        $database = new DB;
    }
    
    public function setPluginConfig($array){
        if(empty($this->PluginId)){
            throw new Exception('【插件配置】插件传入ID失败');
        }else{
            $config = json_encode($array);
            return $this->database->exec("UPDATE `ytidc_plugin` SET `config`='{$config}' WHERE `id`='{$this->PluginId}}");
        }
    }
    
    public function getPluginConfig(){
        if(empty($this->PluginId)){
            throw new Exception('【插件配置】插件传入ID失败');
        }else{
            $plugin = $this->database->get_row("SELECT * FROM `ytidc_plugin` WHERE `id`='{$this->PluginId}'");
            return $plugin['config'];
        }
    }
    
    public function addPluginData($array){
        if(empty($this->PluginId)){
            throw new Exception('【插件配置】插件传入ID失败');
        }else{
            return $this->database->exec("INSERT INTO `ytidc_plugindata`(`plugin`, `key`, `value`) VALUES ('{$this->PluginId}', '{$array['key']}', '{$array['value']}')");
        }
    }
    
    public function getPluginData($key){
        if(empty($this->PluginId)){
            throw new Exception('【插件配置】插件传入ID失败');
        }else{
            return $this->database->get_row("SELECT * FROM `ytidc_plugindata` WHERE `key`='{$key}' AND `plugin`='{$this->PluginId}'");
        }
    }
    
    public function editPluginData($array){
        if(empty($this->PluginId)){
            throw new Exception('【插件配置】插件传入ID失败');
        }else{
            return $this->database->exec("UPDATE `ytidc_plugindata` SET `value`='{$array['value']}' WHERE `key`='{$array['key']}' AND `plugin`='{$this->PluginId}'");
        }
    }
    
    public function deletePluginData($key){
        if(empty($this->PluginId)){
            throw new Exception('【插件配置】插件传入ID失败');
        }else{
            return $this->database->get_row("DELETE FROM `ytidc_plugindata` WHERE `key`='{$key}' AND `plugin`='{$this->PluginId}'");
        }
    }
}

?>