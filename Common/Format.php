<?php

namespace YunTaIDC\Functions;

class Functions{
    
    public function GetRealIp(){
        $ip = false;
        if (!empty($_SERVER["HTTP_CLIENT_IP"])) {
        	$ip = $_SERVER["HTTP_CLIENT_IP"];
        }
        if (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        	$ips = explode(", ", $_SERVER['HTTP_X_FORWARDED_FOR']);
        	if ($ip) {
        		array_unshift($ips, $ip);
        		$ip = FALSE;}
        	for ($i = 0; $i < count($ips); $i++) {
        		if (!preg_match("^(10│172.16│192.168).^", $ips[$i])) {
        			$ip = $ips[$i];
        			break;
        		}
        	}
        }
        return ($ip ? $ip : $_SERVER['REMOTE_ADDR']);
    }
    
    public function randomkeys($length)   {   
        $pattern = '1234567890abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLOMNOPQRSTUVWXYZ-_';  
        $key = "";
        for($i=0;$i<$length;$i++)   
        {   
            $key = $key . $pattern[mt_rand(0,61)];    //生成php随机数   
        }   
        return $key;   
    }   
    
    public function url_decode($str) {  
        if(is_array($str)) {  
            foreach($str as $key=>$value) {  
                $str[urldecode($key)] = urldecode($value);  
            }  
        } else {  
            $str = urldecode($str);  
        }  
    
        return $str;  
    }
    
    public function url_encode($str) {  
        if(is_array($str)) {  
            foreach($str as $key=>$value) {  
                $str[urlencode($key)] = url_encode($value);  
            }  
        } else {  
            $str = urlencode($str);  
        }  
    
        return $str;  
    }
    
}

?>