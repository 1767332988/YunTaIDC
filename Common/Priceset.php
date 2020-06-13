<?php

namespace YunTaIDC\Priceset;

use YunTaIDC\Database\Database;
use Exception as Exception;

class Priceset{
    
    public $database;
    public $priceset;
    
    public function __construct($id = null){
        $this->database = new Database();
        if(!is_null($id)){
            if($this->DB->num_rows("SELECT * FROM `ytidc_priceset` WHERE `id`='{$id}'") != 1){
                throw new Exception("Priceset.php价格组不存在");
            }else{
                $this->priceset = $this->DB->get_row("SELECT * FROM `ytidc_priceset` WHERE `id`='{$id}'");
            }
        }
    }
    
    public function GetPricesetInfo(){
        return $this->priceset;
    }
    
    public function AddPriceset(){
        return $this->database->exec("INSERT INTO `ytidc_priceset`(`name`, `description`, `price`, `factor`, `weight`, `status`) VALUES ('新建价格组','','','','1','1')");
    }
    
    public function SetPricesetName($name){
        if(empty($this->priceset)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_priceset` SET `name`='{$name}' WHERE `id`='{$this->priceset['id']}'");
        }
    }
    
    public function SetPricesetDescription($description){
        if(empty($this->priceset)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_priceset` SET `description`='{$description}' WHERE `id`='{$this->priceset['id']}'");
        }
    }
    
    public function SetPricesetPrice($price){
        if(empty($this->priceset)){
            return false;
        }else{
            $price = json_encode($price);
            return $this->DB->exec("UPDATE `ytidc_priceset` SET `price`='{$price}' WHERE `id`='{$this->priceset['id']}'");
        }
    }
    
    public function SetPricesetFactor($factor){
        if(empty($this->priceset)){
            return false;
        }else{
            $factor = json_encode($factor);
            return $this->DB->exec("UPDATE `ytidc_priceset` SET `factor`='{$factor}' WHERE `id`='{$this->priceset['id']}'");
        }
    }
    
    public function SetPricesetWeight($weight){
        if(empty($this->priceset)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_priceset` SET `weight`='{$weight}' WHERE `id`='{$this->priceset['id']}'");
        }
    }
    
    public function SetPricesetStatus($status){
        if(empty($this->priceset)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_priceset` SET `status`='{$status}' WHERE `id`='{$this->priceset['id']}'");
        }
    }
    
}

?>