<?php

include("../includes/common.php");
if(empty($_SESSION['yuntauser']) || empty($_SESSION['userip'])){
  	@header("Location: ./login.php");
     exit;
}else{
	$user = daddslashes($_SESSION['yuntauser']);
	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$user}'")->fetch_assoc();
	if($user['lastip'] != getRealIp() || $_SESSION['userip'] != getRealIp()){
		@header("Location: ./login.php");
		exit;
	}
}
foreach($_POST as $k => $v){
  	$params[$k] = daddslashes($v);
}
if(empty($params['id'])|| empty($params['time'])){
  	@header("Location: ./msg.php?msg=参数不足够，请勿为空！");
  	exit;
}
$service = $DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$params['id']}' AND `userid`='{$user['id']}'");
if($service->num_rows != 1){
	@header("Location: ./msg.php?msg=该服务不存在");
	exit;
}else{
	$service = $service->fetch_assoc();
}
$service['password'] = base64_decode($service['password']);
$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$service['product']}'");
if($product->num_rows != 1){
	@header("Location: ./msg.php?msg=该服务所属产品不存在");
	exit;
}else{
	$product = $product->fetch_assoc();
}
$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$product['server']}'");
if($server->num_rows != 1){
	@header("Location: ./msg.php?msg=该产品所属服务器不存在");
	exit;
}else{
	$server = $server->fetch_assoc();
}

if($user['grade'] != "0" && $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'")->num_rows == 1){
  	$grade = $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'")->fetch_assoc();
	$price = json_decode($grade['price'], true);
	$discount = $price[$product['id']];
}else{
	$discount = 100;
}
$pdis = json_decode(url_decode($product['period']),true);
foreach($pdis as $k => $v){
	if($v['name'] == $params['time']){
		$dis = array(
			'name' => $v['name'],
			'price' => $v['price'],
			'day' => $v['day'],
			'remark' => $v['remark'],
			'renew' => $v['renew'],
		);
	}
}
if(empty($dis)){
	@header("Location: ./msg.php?msg=周期设置错误！");
  	exit;
}
if($dis['renew'] == 0){
	@header("Location: ./msg.php?msg=该周期不允许续费！");
	exit;
}
if(empty($service['promo_code'])){
	$price = $dis['price'] * $discount / 100;
}else{
	$price = $dis['price'] * $discount / 100;
	$promo = $DB->query("SELECT * FROM `ytidc_promo` WHERE `code`='{$service['promo_code']}'");
	if($promo->num_rows == 1){
		$promo = $promo->fetch_assoc();
		if($promo['renew'] == 1){
			$price = $price - $promo['price'];
		}
	}
}
if(!check_price($price, true)){
  	@header("Location: ./msg.php?msg=价格设置错误，请联系上级进行管理！");
  	exit;
}

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
			if($usersiteownergrade['weight'] >= $user['grade']){
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
				  	$DB->query("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','下级用户续费服务返现','{$usersitemoneyprice}','加款','{$usersiteowner['id']}','已完成')");
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
		$highgradeprice =  $highgradeprice2 * $dis['discount'] / 100;
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
$new_money = $user['money'] - $price;
if($new_money >= 0){
  	$DB->query("UPDATE `ytidc_user` SET `money`='{$new_money}' WHERE `id`='{$user['id']}'");
  	$orderid = date('YmdHis').rand(1000, 99999);
  	$DB->query("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','续费服务','{$price}','扣款','{$user['id']}','已完成')");
}else{
  	@header("Location: ./msg.php?msg=用户余额不足");
  	exit;
}
$plugin = "../plugins/server/".$server['plugin']."/main.php";
if(!is_file($plugin) || !file_exists($plugin)){
  	@header("Location: ./msg.php?msg=服务器插件不存在");
  	exit;
}
include($plugin);
$postdata = array(
  	'data' => array(
      	'time' => $dis,
      	'id' => $params['id'],
    ),
  	'service' => $service,
  	'product' => $product,
  	'server' => $server,
);
$function = $server['plugin']."_RenewService";
$return = $function($postdata);
if($return['status'] != "success"){
	WriteLog(ROOT."/logs/service_error.log", "服务{$params['username']}续费失败，返回信息：{$return['msg']}");
	@header("Location: ./msg.php?msg=服务器返回错误，请联系管理员处理！");
  	exit;
}else{
  	$DB->query("UPDATE `ytidc_service` SET `enddate`='{$return['enddate']}' WHERE `id`='{$service['id']}'");
  	@header("Location: ./msg.php?msg=续费成功");
  	exit;
}
?>