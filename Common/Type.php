<?php

namespace YunTaIDC\Type;

use YunTaIDC\Database\Database;

class Type{
    
    var $type;
    var $DB;
    
    public function __construct($id = null){
        $this->DB = new Database();
        if(!is_null($id)){
            if($this->DB->num_rows("SELECT * FROM `ytidc_type` WHERE `id`='{$id}'") != 1){
                throw new Exception("Type.php产品组不存在");
            }else{
                $this->type = $this->DB->get_row("SELECT * FROM `ytidc_type` WHERE `id`='{$id}'");
            }
        }
    }
    
    public function AddType($params){
        return $this->DB->exec("INSERT INTO `ytidc_type`(`name`, `description`, `father`, `weight`, `status`) VALUES ('新建分类','新建分类',0,0,0)");
    }
    
    public function SetTypeName($name){
        if(empty($this->type)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_type` SET `name`='{$name}' WHERE `id`='{$this->type['id']}'");
        }
    }
    
    public function SetTypeDescription($description){
        if(empty($this->type)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_type` SET `description`='{$description}' WHERE `id`='{$this->type['id']}'");
        }
    }
    
    public function SetTypeFather($father){
        if(empty($this->type)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_type` SET `father`='{$father}' WHERE `id`='{$this->type['id']}'");
        }
    }
    
    public function SetTypeWeight($weight){
        if(empty($this->type)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_type` SET `weight`='{$weight}' WHERE `id`='{$this->type['id']}'");
        }
    }
    
    public function SetTypeStatus($status){
        if(empty($this->type)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_type` SET `status`='{$status}' WHERE `id`='{$this->type['id']}'");
        }
    }
    
    public function SetTypeInfo($params){
        foreach($params as $k => $v){
            $this->DB->exec("UPDATE `ytidc_type` SET `{$k}`='{$v}' WHERE `id`='{$this->type['id']}'");
        }
    }
    
}

?>