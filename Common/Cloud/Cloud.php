<?php

namespace Cloud\Cloud;

class Cloud{
    
    public $cloud = "https://cloud.yunta.cc/";
    public $username;
    public $password;
    
    public function __construct($cloud, $username, $password){
        $this->username = $username;
        $this->password = $password;
        if(!empty($cloud)){
            $this->cloud = $cloud;
        }
    }
    
    public function GetNewVersion(){
        $path = $this->cloud."api/YunTaIDC/version.php";
        $return = @file_get_contents($path);
        if(!$return){
            throw new Exception('连接云塔中心失败[Connection failure to YunTower Center]');
        }else{
            $result = json_decode($return, true);
            return $result;
        }
    }
    
    public function GetPluginList(){
        $path = $this->cloud."api/YunTaIDC/PluginList.php";
        $return = @file_get_contents($path);
        if(!$return){
            throw new Exception('连接云塔中心失败[Connection failure to YunTower Center]');
        }else{
            $result = json_decode($return, true);
            return $result;
        }
    }
    
    public function GetTemplateList(){
        $path = $this->cloud."api/YunTaIDC/TemplateList.php";
        $return = @file_get_contents($path);
        if(!$return){
            throw new Exception('连接云塔中心失败[Connection failure to YunTower Center]');
        }else{
            $result = json_decode($return, true);
            return $result;
        }
    }
    
    public function GetNewsList(){
        $path = $this->cloud."api/YunTaIDC/news.php";
        $return = @file_get_contents($path);
        if(!$return){
            throw new Exception('连接云塔中心失败[Connection failure to YunTower Center]');
        }else{
            $result = json_decode($return, true);
            return $result;
        }
    }
    
    public function UpdateSystem($version){
        $path = $this->cloud."api/YunTaIDC/news.php";
        $return = @file_get_contents($path);
        if(!$return){
            throw new Exception('连接云塔中心失败[Connection failure to YunTower Center]');
        }else{
            $result = json_decode($return, true);
            return $result;
        }
    }
    
}

?>