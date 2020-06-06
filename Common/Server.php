<?php

namespace YunTaIDC\Server;

use YunTaIDC\Database\Database;
use YunTaIDC\System\System;

class Server{
    
    public $server;
    public $database;
    
    public function __construct($id = null){
        $this->database = new Database();
        if(!is_null($id)){
            if($this->DB->num_rows("SELECT * FROM `ytidc_server` WHERE `id`='{$id}'") != 1){
                throw new Exception("Server.php服务器不存在");
            }else{
                $this->server = $this->DB->get_row("SELECT * FROM `ytidc_server` WHERE `id`='{$id}'");
            }
        }
    }
    
    public function AddServer(){
        return $this->database->exec("INSERT INTO `ytidc_server`(`name`, `serverip`, `serverdomain`, `serverdns1`, `serverdns2`, `serverusername`, `serverpassword`, `serveraccesshash`, `servercpanel`, `serverport`, `plugin`, `status`) VALUES ('新建服务器','','','','','','','','',80,'',1)");
    }
    
    public function SetServerIp($ip){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `serverip`='{$ip}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerDomain($domain){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `serverdomain`='{$domain}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerUsername($username){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `serverusername`='{$username}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerPassword($password){
        if(empty($this->server)){
            return false;
        }else{
            $password = base64_encode($password);
            return $this->database->exec("UPDATE `ytidc_server` SET `serverpassword`='{$password}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerAccessHash($hash){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `serveraccesshash`='{$hash}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerStatus($status){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `status`='{$status}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerCpanel($cpanel){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `servercpanel`='{$cpanel}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerName($name){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `name`='{$name}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerPort($port){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `serverport`='{$port}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerDns1($dns){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `serverdns1`='{$dns}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerDns2($dns){
        if(empty($this->server)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_server` SET `serverdns2`='{$dns}' WHERE `id`='{$this->server['id']}'");
        }
    }
    
    public function SetServerInfo($params){
        if(empty($this->server)){
            return false;
        }else{
            foreach($params as $k => $v){
                $this->database->exec("UPDATE `ytidc_server` SET `{$k}`='{$v}' WHERE `id`='{$this->server['id']}'");
            }
        }
    }
    
}

?>