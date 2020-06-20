<?php

namespace YunTaIDC\Service;

use YunTaIDC\Database\Database;
use YunTaIDC\Functions\Functions;
use YunTaIDC\System\System;
use YunTaIDC\Server\Server;
use YunTaIDC\Product\Product;

class Service{
    
    public $database;
    public $config;
    public $service;
    public $product;
    public $server;
    
    public function __construct($id = null,$product, $server, $conf){
        $this->database = new Database;
        $this->product = $product;
        $this->server = $server;
        $this->config = $conf;
        if(!is_null($id)){
            if($this->database->num_rows("SELECT * FROM `ytidc_service` WHERE `id`='{$id}'")){
                throw new Exception("Service.php服务不存在");
            }else{
                $this->service = $this->DB->get_row("SELECT * FROM `ytidc_service` WHERE `id`='{$id}'");
            }
        }
    }
    
    // public function NewService($username, $password, $period, $user, $promocode = ''){
    //     $function = new Functions();
    //     if($this->config['random_username'] == 1){
    //         $username = $function->randomkeys(8);
    //     }  
    //     if($this->config['random_password'] == 1){
    //         $passowrd = base64_encode($function->randomkeys(8));
    //     }
    //     $productperiod = json_decode($function->url_decode($this->product['period']));
    //     foreach($productperiod as $k => $v){
    //         if($v['id'] == $period){
    //             $dis = array(
    //     			'name' => $v['name'],
    //     			'price' => $v['price'],
    //     			'day' => $v['day'],
    //     			'remark' => $v['remark'],
    //     		);
    //         }
    //     }
    //     if(empty($dis)){
    //         return false;
    //     }
    //     $enddate = date("Y-m-d", strtotime("+{$dis['day']} days", time()));
    //     $this->database->exec("INSERT INTO `ytidc_service`(`userid`, `username`, `password`, `buydate`, `enddate`, `period`, `product`, `promo_code`, `configoption`, `status`) VALUES ('{$user}','{$username}','{$password}','{date('Y-m-d)}','{$enddate}','{json_encode($dis)}','{$this->product['id']}','{$promocode}','','等待审核')");
    //     $serviceid = $this->database->lastInsertId();
    //     $plugin = $this->server['plugin'];
    //     $this->product['configoption'] = json_decode($this->product['configoption']);
    //     $postdata = array(
    //         'data' => array(
    //             'username' => $username,
    //             'password' => base64_decode($passowrd),
    //             'period' => $dis,
    //         ),
    //         'product' => $this->product,
    //         'server' => $this->server,
    //     );
    //     $pluginloader = new PluginLoader();
    //     $plugin = $pluginloader->LoadPlugin($plugin);
    //     if(!method_exists("CreateService", $plugin)){
    //         return array(
    //             'status' => 'fail',
    //             'msg' => '插件并没有任何开通功能！',
    //         );
    //     }else{
    //         $return = $plugin->CreateService($postdata);
    //         if($return['status'] == 'success'){
    //             $password = base64_encode($return['password']);
    //             $this->database->exec("UPDATE `ytidc_service` SET `username`='{$return['username']}', `password`='{$password}', 'enddate'='{$return['enddate']}', 'configoption'='{$return['configoption']}', 'status'='激活' WHERE `id`='{$serviceid}'");
    //             return array(
    //                 'status' => 'success',
    //                 'msg' => '开通成功！',
    //             );
    //         }else{
    //             return array(
    //                 'status' => 'fail',
    //                 'msg' => '插件返回开通错误！',
    //             );
    //         }
    //     }
    // }
    
    // public function RenewService($period){
    //     $productperiod = $this->product['period'];
    //     foreach($productperiod as $k => $v){
    //         if($v['id'] == $period){
    //             $dis = array(
    //     			'name' => $v['name'],
    //     			'price' => $v['price'],
    //     			'day' => $v['day'],
    //     			'remark' => $v['remark'],
    //     			'renew' => $v['renew'],
    //     		);
    //         }
    //     }
    //     if(empty($dis)){
    //         return false;
    //     }
    //     if($dis['renew'] != 1){
    //         return array(
    //             'status' => 'fail',
    //             'msg' => '周期不允许续费!',
    //         );
    //     }
    //     $plugin = $this->server['plugin'];
    //     $this->product['configoption'] = json_decode($this->product['configoption']);
    //     $postdata = array(
    //         'data' => array(
    //             'id' => $id,
    //             'period' => $dis,
    //         ),
    //         'service' => $this->service,
    //         'product' => $this->product,
    //         'server' => $this->server,
    //     );
    //     $pluginloader = new PluginLoader();
    //     $plugin = $pluginloader->LoadPlugin($plugin);
    //     if(!method_exists("RenewService", $plugin)){
    //         return array(
    //             'status' => 'fail',
    //             'msg' => '插件并没有任何开通功能！',
    //         );
    //     }else{
    //         $return = $plugin->RenewService($postdata);
    //         if($return['status'] == "success"){
    //             $this->database->exec("UPDATE `ytidc_service` SET `enddate`='{$return['enddate']}' WHERE `id`='{$this->service['id']}'");
    //             return array(
    //                 'status' => 'success',
    //                 'msg' => '续费成功！',
    //             );
    //         }else{
    //             return array(
    //                 'status' => 'fail',
    //                 'msg' => '插件返回续费失败！',
    //             );
    //         }
    //     }
    // }
    
    public function AddService($params){
        $params['password'] = base64_encode($params['password']);
        $params['period'] = json_encode($params['period']);
        if($this->DB->exec("INSERT INTO `ytidc_service`(`userid`, `username`, `password`, `buydate`, `enddate`, `period`, `product`, `promo_code`, `configoption`, `status`) VALUES ('{$params['user']}', '{$params['username']}', '{$params['password']}', '{$params['buydate']}', '{$params['enddate']}', '{$params['period']}', '{$params['productid']}', '{$params['promo_code']}', '{$params['configoption']}', '{$params['status']}')")){
            $this->service = $this->DB->get_row("SELECT * FROM `ytidc_service` WHERE `id`='{$this->DB->lastInsertId()}'");
            return true;
        }else{
            return false;
        }
    }
    
    public function GetServiceInfo(){
        return $this->service;
    }
    
    public function SetServiceUsername($username){
        if(empty($this->service)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_service` SET `username`='{$username}' WHERE `id`='{$this->service['id']}'");
        }
    }
    
    public function SetServicePassword($password){
        if(empty($this->service)){
            return false;
        }else{
            $password = base64_encode($password);
            return $this->database->exec("UPDATE `ytidc_service` SET `password`='{$password}' WHERE `id`='{$this->service['id']}'");
        }
    }
    
    public function SetServiceEnddate($enddate){
        if(empty($this->service)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_service` SET `enddate`='{$enddate}' WHERE `id`='{$this->service['id']}'");
        }
    }
    
    public function SetServicePeriod($period){
        if(empty($this->service)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_service` SET `period`='{$period}' WHERE `id`='{$this->service['id']}'");
        }
    }
    
    public function SetServicePromocode($promocode){
        if(empty($this->service)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_service` SET `promo_code`='{$promocode}' WHERE `id`='{$this->service['id']}'");
        }
    }
    
    public function SetServiceProduct($product){
        if(empty($this->service)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_service` SET `product`='{$product}' WHERE `id`='{$this->service['id']}'");
        }
    }
    
    public function SetServiceConfigoption($configoption){
        if(empty($this->service)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_service` SET `configoption`='{$configoption}' WHERE `id`='{$this->service['id']}'");
        }
    }
    
    public function SetServiceStatus($status){
        if(empty($this->service)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_service` SET `status`='{$status}' WHERE `id`='{$this->service['id']}'");
        }
    }
    
}


?>