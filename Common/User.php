<?php

namespace YunTaIDC\User;

use YunTaIDC\Database\Database;
use YunTaIDC\Functions\Functions;
use YunTaIDC\Security\Security;

class User{
    
    private $user;
    private $DB;
    private $Logined = false;
    
    public function __construct($id = null){
        $this->DB = new Database();
        if(!is_null($id)){
            if($this->DB->num_rows("SELECT * FROM `ytidc_user` WHERE `id`='{$id}'")){
                throw new Exception("User.php用户不存在");
            }else{
                $this->user = $this->DB->get_row("SELECT * FROM `ytidc_user` WHERE `id`='{$id}'");
            }
        }
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
    
    public function SetUserUsername($username){
        if(empty($this->user)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_user` SET `username`='{$username}' WHERE `id`='{$this->user['id']}'");
        }
    }
    
    public function SetUserPassword($password){
        $password = md5(md5($password));
        if(empty($this->user)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_user` SET `password`='{$password}' WHERE `id`='{$this->user['id']}'");
        }
    }
    
    public function SetUserEmail($email){
        if(empty($this->user)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_user` SET `email`='{$email}' WHERE `id`='{$this->user['id']}'");
        }
    }
    
    public function SetUserMoney($money){
        if(empty($this->user)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_user` SET `money`='{$money}' WHERE `id`='{$this->user['id']}'");
        }
    }
    
    public function SetUserPriceset($priceset){
        if(empty($this->user)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_user` SET `priceset`='{$priceset}' WHERE `id`='{$this->user['id']}'");
        }
    }
    
    public function SetUserInvite($invite){
        if(empty($this->user)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_user` SET `invite`='{$invite}' WHERE `id`='{$this->user['id']}'");
        }
    }
    
    public function SetUserSite($site){
        if(empty($this->user)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_user` SET `site`='{$site}' WHERE `id`='{$this->user['id']}'");
        }
    }
    
    public function SetUserStatus($status){
        if(empty($this->user)){
            return false;
        }else{
            return $this->database->exec("UPDATE `ytidc_user` SET `status`='{$status}' WHERE `id`='{$this->user['id']}'");
        }
    }
    
    public function GetUserInfo(){
        return $this->user;
    }
    
}

?>