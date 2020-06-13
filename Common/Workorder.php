<?php

namespace YunTaIDC\Workorder;

use YunTaIDC\Database\Database;
use YunTaIDC\User\User;
use Exception as Exception;

class Workorder{
    
    var $user;
    var $DB;
    var $workorder;
    
    public function __construct($user, $id = null){
        $this->DB = new Database();
        $this->user = $user;
        if(!is_null($id)){
            if($this->DB->num_rows("SELECT * FROM `ytidc_workorder` WHERE `id`='{$id}'") != 1){
                throw new Exception("workorder.php工单不存在");
            }else{
                $this->workorder = $this->DB->get_row("SELECT * FROM `ytidc_workorder` WHERE `id`='{$id}'");
            }
        }
    }
    
    public function AddWorkOrder($params){
        if(empty($params['title']) || empty($params['content'])){
            return false;
        }else{
            if(!$this->DB->exec("INSERT INTO `ytidc_workorder`(`title`, `user`, `status`) VALUES ('{$params['title']}','{$this->user->GetUserId()}','待回复')")){
                return false;
            }else{
                $newid = $this->DB->lastInsertId();
                $time = date('Y-m-d H:i:s');
                if(!$this->DB->exec("INSERT INTO `ytidc_workorder_chat` (`person`, `content`, `workorder`, `time`) VALUES ('{$this->user->GetUserUsername()}','{params['content]}','{$newid}','{$time}')")){
                    return false;
                }else{
                    return true;
                }
            }
        }
    }
    
    public function ReplyWorkOrder($params){
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
    
    public function GetReply(){
        $replylist = array();
        foreach ($this->DB->get_rows("SELECT * FROM `ytidc_workorder_chat` WHERE `workorder`='{$this->workorder['id']}'") as $row){
            $replylist[] = $row;
        }
        return $replylist;
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
    
    public function SetStatus($status){
        return $this->DB->exec("UPDATE `ytidc_workorder` SET `status`='{$status}' WHERE `id`='{$this->workorder['id']}'");
    }
    
}

?>