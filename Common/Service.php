<?php

namespace YunTaIDC\Service;

use YunTaIDC\Database\Database;
use YunTaIDC\Functions\Functions;
use YunTaIDC\System\System;
use YunTaIDC\Server\Server;
use YunTaIDC\Product\Product;
use YunTaIDC\Plugin\PluginLoader;
use YunTaIDC\Plugin\PluginBase;

class Service{
    
    public $database;
    public $functioner;
    public $config;
    public $service;
    public $product;
    public $server;
    
    public function __construct($id,$product, $server){
        $this->database = new Database;
        $this->functioner = new Functions;
        $this->product = $product;
        $this->server = $server;
        if(empty($id) || $this->database->num_rows("SELECT * FROM `ytidc_service` WHERE `id`='{$id}'")){
            throw new Exception("Service.php服务不存在");
        }else{
            $this->service = $this->database->get_row("SELECT * FROM `ytidc_service` WHERE `id`='{$id}'");
        }
    }
    
    public function NewService($username, $password, $period){
        if($this->config['random_username'] == 1){
            $username = $functioner->randomkeys(8);
        }  
        if($this->config['random_password'] == 1){
            $passowrd = $functioner->randomkeys(8);
        }
        $productperiod = $this->product->GetProductPeriod();
        foreach($productperiod as $k => $v){
            if($v['id'] == $period){
                $dis = array(
        			'name' => $v['name'],
        			'price' => $v['price'],
        			'day' => $v['day'],
        			'remark' => $v['remark'],
        		);
            }
        }
        if(empty($dis)){
            return false;
        }
        $plugin = $this->server->GetServerPlugin();
        $configoption = $this->product->GetProductConfigOption();
        $postdata = array(
            'data' => array(
                'username' => $username,
                'password' => $passowrd,
                'period' => $dis,
            ),
            'product' => $this->product,
            'server' => $this->server,
        );
        $pluginloader = new PluginLoader();
        $plugin = $pluginloader->LoadPlugin($plugin);
        if(!method_exists("CreateService", $plugin)){
            return array(
                'status' => 'fail',
                'msg' => '插件并没有任何开通功能！',
            );
        }else{
            return $plugin->CreateService($postdata);
        }
    }
    
    public function RenewService($period){
        $this->GetServiceProduct();
        $this->GetServiceServer();
        $productperiod = $this->product->GetProductPeriod();
        foreach($productperiod as $k => $v){
            if($v['id'] == $period){
                $dis = array(
        			'name' => $v['name'],
        			'price' => $v['price'],
        			'day' => $v['day'],
        			'remark' => $v['remark'],
        			'renew' => $v['renew'],
        		);
            }
        }
        if(empty($dis)){
            return false;
        }
        if($dis['renew'] != 1){
            return array(
                'status' => 'fail',
                'msg' => '周期不允许续费!',
            );
        }
        $plugin = $this->server->GetServerPlugin();
        $configoption = $this->product->GetProductConfigOption();
        $postdata = array(
            'data' => array(
                'id' => $id,
                'period' => $dis,
            ),
            'service' => $this->service,
            'product' => $this->product,
            'server' => $this->server,
        );
        $pluginloader = new PluginLoader();
        $plugin = $pluginloader->LoadPlugin($plugin);
        if(!method_exists("RenewService", $plugin)){
            return array(
                'status' => 'fail',
                'msg' => '插件并没有任何开通功能！',
            );
        }else{
            return $plugin->RenewService($postdata);
        }
    }
    
}


?>