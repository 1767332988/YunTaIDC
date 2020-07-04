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
    
    public function CheckPrice($price, $stp = false){
        if($price == 0){
            if(!$stp){
                return true;
            }else{
                return false;
            }
        }else{
            if($price < 0){
                return false;
            }else{
                return ture;
            }
        }
    }
    
    public function CaculateSubsiteOrderMoney($money, $user, $site, $conf){
        if($user['site'] != 0){
        	$usersite = $DB->query("SELECT * FROM `ytidc_subsite` WHERE `id`='{$user['site']}'");
        	if($usersite->num_rows != 1){
        		$usersite = $site;
        	}else{
        		$usersite = $usersite->fetch_assoc();
        		$usersiteowner = $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$usersite['user']}'");
        		if($usersiteowner->num_rows == 1){
        			//分站奖励
        			$usersiteowner = $usersiteowner->fetch_assoc();
        			$usersiteownergrade = $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$usersiteowner['grade']}'");
        			if($usersiteownergrade->num_rows != 1){
        				$usersiteownergrade = $DB->query("SELECT * FROM `ytidc_grade` WHERE `default`='1'");
        				if($usersiteownergrade->num_rows != 1){
        					$usersiteownergrade = $DB->query("SELECT * FROM `ytidc_grade`")->fetch_assoc();
        				}else{
        					$usersiteownergrade = $usersiteownergrade->fetch_assoc();
        				}
        			}else{
        				$usersiteownergrade = $usersiteownergrade->fetch_assoc();
        			}
        			if($usersiteownergrade['weight'] >= $grade['weight']){
        				$usersiteownerprice = json_decode($usersiteownergrade['price'] ,true);
        				$usersiteownerprice2 = $usersiteownerprice[$product['id']];
        				if(empty($usersiteownerprice2)){
        					$usersiteownerprice2 = $usersiteownerprice['*'];
        					if(empty($usersiteownerprice2)){
        						$usersiteownerprice2 = 100;
        					}
        				}
        				//总共奖励金额计算：用户价格组价格 - 分站价格组价格
        				$usersiteownerprice = $usersiteownerprice2 * $dis['price'] / 100;
        				$usersitemoneyprice = $price -  $usersiteownerprice;
        				//必须要大于1块钱才会进行邀请返现，防止出现过小金额
        				if($usersitemoneyprice >= 1){
        					//若有邀请者，检查邀请者是否存在，分站后台设置邀请返现是否合理，防止刷余额情况
        					if(!empty($user['invite']) && $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$user['invite']}'")->num_rows == 1 && $usersite['invitepercent'] < 100 && $user['invite'] != $site['user']){
        						//邀请奖励
        						$invite = $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$user['invite']}'")->fetch_assoc();
        						//邀请者奖励金额计算：总共奖励金额 * 分站设置邀请返现比例
        						$giftmoney = $usersitemoneyprice * $usersite['invitepercent'] / 100;
        						//再次检查返现是否超出总计奖励金额
        						if($usersitemoneyprice >= $giftmoney){
        						//返现后所剩金额
        							$usersitemoneyprice = $usersitemoneyprice - $giftmoney;
        							$invitemoney = $invite['money'] + $giftmoney;
        							$DB->query("UPDATE `ytidc_user` SET `money`='{$invitemoney}' WHERE `id`='{$invite['id']}'");
        						  	$orderid = date('YmdHis').rand(1000, 99999);
        						  	$DB->query("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','邀请奖励返现','{$giftmoney}','加款','{$invite['id']}','已完成')");
        						}
        					}
        				}
        				//防止出现负数
        				if($usersitemoneyprice >= 0){
        					$usersiteownermoney = $usersiteowner['money'] + $usersitemoneyprice;
        					$DB->query("UPDATE `ytidc_user` SET `money`='{$usersiteownermoney}' WHERE `id`='{$usersiteowner['id']}'");	
        				  	$orderid = date('YmdHis').rand(1000, 99999);
        				  	$DB->query("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','下级用户开通服务返现','{$usersitemoneyprice}','加款','{$usersiteowner['id']}','已完成')");
        				}
        			}
        		}
        	}
        }else{
        	if(!empty($user['invite']) && $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$user['invite']}'")->num_rows == 1 && $conf['invitepercent'] < 100){
        		//邀请奖励
        		$invite = $DB->query("SELECT * FROM `ytidc_user` WHERE `id`='{$user['invite']}'")->fetch_assoc();
        		//邀请者奖励金额计算：(订单金额 - 最高等级价格) * 分站设置邀请返现比例
        		$highgrade = $DB->query("SELECT * FROM `ytidc_grade` ORDER BY `weight` DESC")->fetch_assoc();
        		$highgradeprice = json_decode($highgrade['price'], true);
        		$highgradeprice2 = $highgradeprice[$product['id']];
        		if(empty($highgradeprice2)){
        			$highgradeprice2 = $highgradeprice['*'];
        			if(empty($highgradeprice2)){
        				$highgradeprice2 = 100;
        			}
        		}
        		$highgradeprice =  $highgradeprice2 * $dis['price'] / 100;
        		$inviteprice = $price - $highgradeprice;
        		$giftmoney = $inviteprice * $conf['invitepercent'] / 100;
        		//再次检查返现是否超出总计奖励金额
        		if($inviteprice >= $giftmoney && $inviteprice >= 1){
        			$invitemoney = $invite['money'] + $giftmoney;
        			$DB->query("UPDATE `ytidc_user` SET `money`='{$invitemoney}' WHERE `id`='{$invite['id']}'");
        			$orderid = date('YmdHis').rand(1000, 99999);
        			$DB->query("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','邀请奖励返现','{$giftmoney}','加款','{$invite['id']}','已完成')");
        		}
        	}
        }
    }
    
}

?>