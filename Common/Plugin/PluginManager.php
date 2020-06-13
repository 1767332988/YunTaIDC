<?php

namespace YunTaIDC\Plugin;

use YunTaIDC\Database\Database;
use Exception as Exception;
use InstallConfig;

class PluginManager{
    
    public function LoadPlugin($id){
        $database = new Database();
        if($database->num_rows("SELECT * FROM `ytidc_plugin` WHERE `id`='{$id}' AND `status`='1'") != 1){
            throw new Exception("加载插件失败，可能插件为安装或者未启用！插件ID：{$id}");
        }else{
            $plugin = $database->get_row("SELECT * FROM `ytidc_plugin` WHERE `id`='{$id}' AND `status`='1'");
            require_once($plugin['filepath']);
            if(!class_exists($plugin['main'])){
                throw new Exception("加载插件失败，插件没有设置主运行程序，插件ID：{$id}");
            }else{
                $pluginclass = new $plugin['main']();
                return $pluginclass;
            }
        }
    }
    
    public function InstallPlugin($pluginpath){
        $installer = $pluginpath.'installer.php';
        if(!file_exists($installer)){
            throw new Exception('安装插件失败！无法找到安装器！');
        }
        require_once($installer);
        if(!function_exists('InstallConfig')){
            throw new Exception("安装插件失败，installer.php没有注册任何安装程序！");
        }else{
            $config = InstallConfig();
            $database = new Database();
            $filepath = $pluginpath.$config['mainfile'];
            if(!file_exists($filepath)){
                throw new Exception('安装失败，安装程序返回运行主文件不存在！');
            }else{
                if($database->num_rows("SELECT * FROM `ytidc_plugin` WHERE `filepath`='{$filepath}'")){
                    throw new Exception("安装失败，检查到插件数据库已经安装过改插件!");
                }else{
                    return $this->database->exec("INSERT INTO `ytidc_plugin`(`name`, `main`, `filepath`, `config`, `status`) VALUES ('{$config['name']}','{$config['main']}', '{$filepath}', '', '1')");
                }
            }
        }
    }
    
    public function DeletePlugin($Id){
        $database = new Database();
        if($database->num_rows("SELECT * FROM `ytidc_plugin` WHERE `id`='{$id}'") != 1){
            throw new Exception("卸载插件失败，插件记录不存在！");
        }else{
            $plugin = $database->get_row("SELECT * FROM `ytidc_plugin` WHERE `id`='{$id}'");
            if(!file_exists($plugin['filepath'])){
                return $database->exec("DELETE FROM `ytidc_plugin` WHERE `id`='{$id}'");
            }else{
                if(!unlink($plugin['filepath'])){
                    throw new Exception("卸载插件失败！无法删除插件文件，请先自行删除文件再点击卸载！");
                }else{
                    return $database->exec("DELETE FROM `ytidc_plugin` WHERE `id`='{$id}'");
                }
            }
        }
    }
    
}

?>