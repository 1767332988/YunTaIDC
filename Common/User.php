<?php

namespace YunTaIDC\User;

use YunTaIDC\Database\Database;
use YunTaIDC\Functions\Functions;
use YunTaIDC\Security\Security;

class User{
    
    private $user;
    private $DB;
    private $Logined = false;
    
    public function __construct($userid = "", $DB){
        $this->DB = $DB;
        $user = $DB->get_row("SELECT * FROM `ytidc_user` WHERE `id`='{$userid}'");
        $this->user = $user;
    }
    
    public function GetUserByUsernameLogin($username, $password){
        $functioner = new Functions();
        $password = md5(md5($password));
        $ip = $functioner->getRealIp();
        if($this->DB->num_rows("SELECT * FROM `ytidc_user` WHERE `username`='{$username}' AND `password`='{$password}'") == 1){
            $user = $this->DB->get_row("SELECT * FROM `ytidc_user` WHERE `username`='{$username}'");
            $this->user = $user;
        	$this->DB->exec("UPDATE `ytidc_user` SET `lastip`='{$ip}' WHERE `username`='{$username}'");
        	$_SESSION['yuntauser'] = $username;
        	$_SESSION['userip'] = $ip;
        	$this->Logined = true;
        	return true;
        }else{
        	return false;
        }
    }
    
    public function GetUserById($id){
        $this->user = $this->DB->get_row("SELECT * FROM `ytidc_user` WHERE `id`='{$id}'");
    }
    
    public function GetUserBySessionLogin(){
        $security = new Security();
        $functioner = new Functions();
        $user = $security->daddslashes($_SESSION['yuntauser']);
    	$user = $this->DB->get_row("SELECT * FROM `ytidc_user` WHERE `username`='{$user}'");
    	if($user['lastip'] == $functioner->getRealIp() && $_SESSION['userip'] == $functioner->getRealIp()){
    	    $this->user = $user;
        	$this->Logined = true;
    		return true;
    	}else{
    	    return false;
    	}
    }
    
    public function isLogin(){
        return $this->Logined;
    }
    
    public function GetUserInfo(){
        return $this->user;
    }
    
    public function GetUserId(){
        return $this->user['id'];
    }
    
    public function GetEmail(){
        $user = $this->user;
        return $user['email'];
    }
    
    public function GetMoney(){
        $user = $this->user;
        return $user['money'];
    }
    
    public function GetUsername(){
        $user = $this->user;
        return $user['username'];
    }
    
    public function ChangePassword($password){
        $user = $this->user;
        $password = md5(md5($password));
        if($DB->exec("UPDATE `ytidc_user` SET `password`='{$password}' WHERE `id`='{$user['id']}'")){
            return true;
        }else{
            return false;
        }
    }
    
    public function GetGradeId(){
        $user = $this->user;
        return $user['grade'];
    }
    
    public function GetInviteUser(){
        $user = $this->user;
        //return User($user['invite'], $this->DB);
        return $user['invite'];
    }
    
    public function SetUserInfo($params){
        foreach($params as $k => $v){
            $this->DB->exec("UPDATE `ytidc_user` SET `{$k}`='{$v}' WHERE `id`='{$this->user['id']}'");
        }
        return true;
    }
    
}

?>