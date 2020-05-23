<?php

namespace YunTaIDC\Product;

use YunTaIDC\Database\Database;
use YunTaIDC\Functions\Functions;
use YunTaIDC\Security\Security;

class Product{
    
    public $product;
    public $DB;
    
    public function __construct($id){
        $this->DB = new Database();
        $this->product = $DB->get_row("SELECT * FROM `ytidc_product` WHERE `id`='{$id}'");
        if(!$this->product){
            return false;
        }
    }
    
    public function GetInfo(){
        return $this->product;
    }
    
    public function SetProduct($update, $factor){
        $sql = "UPDATE `ytidc_product` SET ";
        foreach($update as $k => $v){
            $sql = $sql . "`".$k."`='".$v."' WHERE";
        }
        foreach($factor as $k => $v){
            $sql = $sql . "`".$k."`='".$v."'";
        }
        if($this->DB->exec($sql)){
            return true;
        }else{
            return false;
        }
    }
    
}

?>