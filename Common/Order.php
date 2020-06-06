<?php

namespace YunTaIDC\Order;

use YunTaIDC\Database\Database;
use YunTaIDC\User\User;

class Order{
    
    var $order;
    var $DB;
    var $user;
    
    public function __construct($user){
        $this->DB = new Database();
        $this->user = new User();
        $this->user->GetUserById($user);
    }
    
    public function CreateOrder($params){
        $orderid = date('YmdHis').rand(10000,999999);
        if($this->DB->exec("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','{$params['description']}','{$params['money']}','{$params['action']}','{$this->user->GetUserId()}','未完成')")){
            return $orderid;
        }else{
            return false;
        }
    }
    
    public function FinishOrder($orderid){
        if($this->DB->exec("UPDATE `ytidc_order` SET `status`='已完成' WHERE `orderid`='{$orderid}'")){
            return true;
        }else{
            return false;
        }
    }
    
    public function GetOrderList(){
        $orderarray = array();
        foreach ($this->DB->get_rows("SELECT * FROM `ytidc_order` WHERE `user`='{$this->user->GetUserId()}'") as $row){
            $orderarray[] = $row;
        }
        return $orderarray;
    }
    
    public function GetOrderInfo($orderid){
        return $this->DB->get_row("SELECT * FROM `ytidc_order` WHERE `orderid`='{$orderid}'");
    }
    
}

?>