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
    
    public function __construct($id){
        $this->database = new Database();
        $this->security = new Security();
        if(!empty($id)){
            $this->product = $this->database->get_row("SELECT * FROM `ytidc_product` WHERE `id`='{$id}'");
            if(!$this->product){
                return false;
            }
        }
    }
    
    public function GetProductById($id){
        $this->product = $this->database->get_row("SELECT * FROM `ytidc_product` WHERE `id`='{$id}'");
        if(!$this->product){
            return false;
        }
    }
    
    public function GetProductName(){
        return $this->product['name'];
    }
    
    public function GetProductDescription(){
        return $this->product['description'];
    }
    
    public function GetProductType(){
        return $this->product['type'];
    }
    
    public function GetProductServer(){
        return new Server($this->product['server']);
    }
    
    public function GetProductPeriod(){
        $function = new Functions();
        return json_decode($function->url_decode($this->product['period']));
    }
    
    public function GetProductLimit(){
        return $this->product['limit'];
    }
    
    public function GetProductConfigOption(){
        return json_decode($this->product['configoption']);
    }
    
    public function GetProductWeight(){
        return $this->product['weight'];
    }
    
    public function GetProductHidden(){
        return $this->product['hidden'];
    }
    
    public function GetProductStatus(){
        return $this->product['status'];
    }
    
    public function GetProductInfo(){
        return $this->product;
    }
    
    public function SetProductName($name){
        $name = $this->security->daddslashes($name);
        return $this->database->exec("UPDATE `ytidc_product` SET `name`='{$name}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductDescription($description){
        $description = $this->security->daddslashes($description);
        return $this->database->exec("UPDATE `ytidc_product` SET `description`='{$description}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductType($type){
        $type = $this->security->daddslashes($type);
        return $this->database->exec("UPDATE `ytidc_product` SET `type`='{$type}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductServer($server){
        $server = $this->security->daddslashes($server);
        return $this->database->exec("UPDATE `ytidc_product` SET `server`='{$server}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductPeriod($period){
        $function = new Functions();
        $period = $this->security->daddslashes($period);
        $period = json_encode($function->url_encode($period));
        return $this->database->exec("UPDATE `ytidc_product` SET `period`='{$period}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductLimit($limit){
        $limit = $this->security->daddslashes($limit);
        return $this->database->exec("UPDATE `ytidc_product` SET `limit`='{$limit}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductConfigoption($configoption){
        $configoption = json_encode($this->security->daddslashes($configoption));
        return $this->database->exec("UPDATE `ytidc_product` SET `configoption`='{$configoption}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductWeight($weight){
        $weight = $this->security->daddslashes($weight);
        return $this->database->exec("UPDATE `ytidc_product` SET `weight`='{$weight}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductHidden($hidden){
        $hidden = $this->security->daddslashes($hidden);
        return $this->database->exec("UPDATE `ytidc_product` SET `hidden`='{$hidden}' WHERE `id`='{$this->product['id']}'");
    }
    
    public function SetProductStatus($status){
        $status = $this->security->daddslashes($status);
        return $this->database->exec("UPDATE `ytidc_product` SET `status`='{$status}' WHERE `id`='{$this->product['id']}'");
    }
    
}

?>