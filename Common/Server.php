<?php

namespace YunTaIDC\Server;

use YunTaIDC\Database\Database;
use YunTaIDC\Security\Security;

class Server{
    
    public $server;
    public $database;
    public $security;
    
    public function __construct($id){
        $this->database = new Database();
        $this->security = new security();
        $this->server = $this->database->get_row("SELECT * FROM `ytidc_server` WHERE `id`='{$id}'");
        if(!$this->server){
            return false;
        }
    }
    
    public function GetServerIp(){
        return $this->server['serverip'];
    }
    
    public function GetServerDomain(){
        return $this->server['serverdoamin'];
    }
    
    public function GetServerDns1(){
        return $this->server['serverdns1'];
    }
    
    public function GetServerDns2(){
        return $this->server['serverdns2'];
    }
    
    public function GetServerUsername(){
        return $this->server['serverusername'];
    }
    
    public function GetServerPassword(){
        return $this->server['serverpassword'];
    }
    
    public function GetServerAccessHash(){
        return $this->server['serveraccesshash'];
    }
    
    public function GetServerPort(){
        return $this->server['serverport'];
    }
    
    public function GetServerPlugin(){
        return $this->server['plugin'];
    }
    
    public function GetServerName(){
        return $this->server['name'];
    }
    
    public function GetServerCpanel(){
        return $this->server['servercpanel'];
    }
    
    public function GetServerStatus(){
        return $this->server['status'];
    }
    
    public function SetServerIp($ip){
        $ip = $this->security->daddslashes($ip);
        return $this->database->exec("UPDATE `ytidc_server` SET `serverip`='{$ip}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerDomain($domain){
        $domain = $this->security->daddslashes($domain);
        return $this->database->exec("UPDATE `ytidc_server` SET `serverdomain`='{$domain}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerUsername($username){
        $username = $this->security->daddslashes($username);
        return $this->database->exec("UPDATE `ytidc_server` SET `serverusername`='{$username}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerPassword($password){
        $password = $this->security->daddslashes($password);
        return $this->database->exec("UPDATE `ytidc_server` SET `serverpassword`='{$password}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerAccessHash($hash){
        $hash = $this->security->daddslashes($hash);
        return $this->database->exec("UPDATE `ytidc_server` SET `serveraccesshash`='{$hash}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerStatus($status){
        $status = $this->security->daddslashes($status);
        return $this->database->exec("UPDATE `ytidc_server` SET `status`='{$status}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerCpanel($cpanel){
        $cpanel = $this->security->daddslashes($cpanel);
        return $this->database->exec("UPDATE `ytidc_server` SET `servercpanel`='{$cpanel}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerName($name){
        $name = $this->security->daddslashes($name);
        return $this->database->exec("UPDATE `ytidc_server` SET `name`='{$name}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerPort($port){
        $port = $this->security->daddslashes($port);
        return $this->database->exec("UPDATE `ytidc_server` SET `serverport`='{$port}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerDns1($dns){
        $dns = $this->security->daddslashes($dns);
        return $this->database->exec("UPDATE `ytidc_server` SET `serverdns1`='{$dns}' WHERE `id`='{$this->server['id']}'");
    }
    
    public function SetServerDns2($dns){
        $dns = $this->security->daddslashes($dns);
        return $this->database->exec("UPDATE `ytidc_server` SET `serverdns2`='{$dns}' WHERE `id`='{$this->server['id']}'");
    }
    
}

?>