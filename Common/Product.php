<?php

namespace YunTaIDC\Product;

use YunTaIDC\Database\Database;
use YunTaIDC\Security\Security;
use YunTaIDC\Server\Server;
use YunTaIDC\Functions\Functions;

class Product{
    
    public $product;
    public $database;
    public $security;
    
    public function __construct($id = null){
        $this->database = new Database();
        $this->security = new Security();
        if(!is_null($id)){
            if($this->DB->num_rows("SELECT * FROM `ytidc_product` WHERE `id`='{$id}'") != 1){
                throw new Exception("Product.php产品不存在");
            }else{
                $this->product = $this->DB->get_row("SELECT * FROM `ytidc_product` WHERE `id`='{$id}'");
            }
        }
    }
    
    public function AddProduct(){
        return $this->database->exec("INSERT INTO `ytidc_product`(`name`, `description`, `type`, `server`, `period`, `limit`, `configoption`, `weight`, `hidden`, `status`) VALUES ('新建产品','',0,0,'',0,'',0,0,1)");
    }
    
    public function SetProductName($name){
        return $this->database->exec("UPDATE `ytidc_product` SET `name`='{$name}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductDescription($description){
        return $this->database->exec("UPDATE `ytidc_product` SET `description`='{$description}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductType($type){
        return $this->database->exec("UPDATE `ytidc_product` SET `type`='{$type}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductServer($server){
        return $this->database->exec("UPDATE `ytidc_product` SET `server`='{$server}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductPeriod($period){
        $function = new Functions();
        $period = json_encode($function->url_encode($period));
        return $this->database->exec("UPDATE `ytidc_product` SET `period`='{$period}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductLimit($limit){
        return $this->database->exec("UPDATE `ytidc_product` SET `limit`='{$limit}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductConfigoption($configoption){
        $configoption = json_encode($configoption);
        return $this->database->exec("UPDATE `ytidc_product` SET `configoption`='{$configoption}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductWeight($weight){
        return $this->database->exec("UPDATE `ytidc_product` SET `weight`='{$weight}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductHidden($hidden){
        return $this->database->exec("UPDATE `ytidc_product` SET `hidden`='{$hidden}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductStatus($status){
        return $this->database->exec("UPDATE `ytidc_product` SET `status`='{$status}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductInfo($params){
        foreach ($params as $k => $v){
            $this->database->exec("UPDATE ytidc_product` SET `{$K}`='{$v}' WHERE `id`='{$this->product['id']}'");
        }
    }
    
}

?>