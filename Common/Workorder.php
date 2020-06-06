<?php

namespace YunTaIDC\Workorder;

use YunTaIDC\Database\Database;
use YunTaIDC\User\User;
use YunTaIDC\Security\Security;

class Workorder{
    
    var $user;
    var $DB;
    var $security;
    
    public function __construct($user){
        $this->DB = new Database();
        $this->security = new Security();
        $this->user = $user;
    }
    
    public function Add($params){
        if(empty($params['title']) || empty($params['content'])){
            return false;
        }else{
            if(!$this->DB->exec("INSERT INTO `ytidc_workorder`(`title`, `user`, `status`) VALUES ('{$params['title']}','{$this->user->GetUserId()}','待回复')")){
                return false;
            }else{
                $newid = $this->DB->LastInsertId();
                $time = date('Y-m-d H:i:s');
                if(!$this->DB->exec("INSERT INTO `ytidc_workorder_chat` (`person`, `content`, `workorder`, `time`) VALUES ('{$this->user->GetUserUsername()}','{params['content]}','{$newid}','{$time}')")){
                    return false;
                }else{
                    return true;
                }
            }
        }
    }
    
    public function Reply($params){
        if(empty($params['content']) || empty($params['id'])){
            return false;
        }else{
            $time = date('Y-m-d H:i:s');
            if(!$this->DB->exec("INSERT INTO `ytidc_workorder_chat`(`person`, `content`, `workorder`, `time`) VALUES ('{$this->user->GetUserUsername()}','{$params['content']}','{$params['id']}','{$time}')")){
                return false;
            }else{
                if($this->DB->exec("UPDATE `ytidc_workorder` SET `status`='待处理' WHERE `id`='{$params['id']}'")){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }
    
    public function GetReply($id){
        $replylist = array();
        foreach ($this->DB->get_rows("SELECT * FROM `ytidc_workorder_chat` WHERE `workorder`='{$id}'") as $row){
            $replylist[] = $row;
        }
        return $replylist;
    }
    
    public function GetStatus($id){
        $worder = $this->DB->get_row("SELECT * FROM `ytidc_workorder` WHERE `id`='{$id}'");
        if(empty($worder)){
            return false;
        }else{
            return $worder['status'];
        }
    }
    
    public function AdminReply($params){
        if(empty($params['content']) || empty($params['id']) || empty($params['admin'])){
            return false;
        }else{
            $time = date("Y-m-d H:i:s");
            if(!$this->DB->exec("INSERT INTO `ytidc_workorder_chat`(`person`, `content`, `workorder`, `time`) VALUES ('{$params['admin']}','{$params['content']}','{$params['id']}','{$time}')")){
                return false;
            }else{
                if($this->DB->exec("UPDATE `ytidc_workorder` SET `status`='已回复' WHERE `id`='{$params['id']}'")){
                    return true;
                }else{
                    return false;
                }
            }
        }
    }
    
    public function SetStatus($status, $id){
        return $this->DB->exec("UPDATE `ytidc_workorder` SET `status`='{$status}' WHERE `id`='{$id}'");
    }
    
}

?>