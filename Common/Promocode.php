<?php

namespace YunTaIDC\Promocode;

use YunTaIDC\Database\Database;

class Promocode{
    
    var $DB;
    var $promocode;
    
    public function __construct($id = null){
        $this->DB = new Database();
        if(!is_null($id)){
            if($this->DB->num_rows("SELECT * FROM `ytidc_promocode` WHERE `id`='{$id}'") != 1){
                throw new Exception("Promocode.php优惠码不存在");
            }else{
                $this->promocode = $this->DB->get_row("SELECT * FROM `ytidc_promocode` WHERE `id`='{$id}'");
            }
        }
    }
    
    public function GetPromoCodeByCode($code){
        if(empty($code)){
            return false;
        }else{
            return $this->DB->get_row("SELECT * FROM `ytidc_promocode` WHERE `code`='{$code}'");
        }
    }
    
    public function AddPromoCode(){
        $rand = date('YmdHis').rand(100,999);
        return $this->DB->exec("INSERT INTO `ytidc_promo`(`code`, `price`, `product`, `renew`, `daili`, `status`) VALUES ('promocode{$rand}',0.00,'',0,0,1)");
    }
    
    public function SetPromoCodeCode($code){
        if(empty($code)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_promocode` SET `code`='{$code}' WHERE `id`='{$this->promocode['id']}'");
        }
    }
    
    public function SetPromoCodePrice($price){
        if(empty($price)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_promocode` SET `price`='{$price}' WHERE `id`='{$this->promocode['id']}'");
        }
    }
    
    public function SetPromoCodeProduct($product){
        if(empty($product)){
            return false;
        }else{
            $product = json_encode($product);
            return $this->DB->exec("UPDATE `ytidc_promocode` SET `product`='{$product}' WHERE `id`='{$this->promocode['id']}'");
        }
    }
    
    public function SetPromoCodeRenew($renew){
        if(empty($renew)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_promocode` SET `renew`='{$renew}' WHERE `id`='{$this->promocode['id']}'");
        }
    }
    
    public function SetPromoCodeDaili($daili){
        if(empty($daili)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_promocode` SET `daili`='{$daili}' WHERE `id`='{$this->promocode['id']}'");
        }
    }
    
    public function SetPromoCodeStatus($status){
        if(empty($status)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_promocode` SET `status`='{$status}' WHERE `id`='{$this->promocode['id']}'");
        }
    }
    
    public function SetPromoCodeInfo($params){
        foreach($params as $k => $v){
            $this->DB->exec("UPDATE `ytidc_promocode` SET `{$k}`='{$v}' WHERE `id`='{$this->promocode['id']}'");
        }
    }
    
}

?>