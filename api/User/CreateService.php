<?php

include("../../includes/common.php");
header("Content-type: text/json");

foreach($_GET as $k => $v){
  	$params[$k] = daddslashes($v);
}

if(empty($params['ytidc_user']) && empty($params['ytidc_pass'])){
  	if($user->num_rows != 1){
      	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '账号密码为空',
    );
  	 exit(json_encode($retdata));
    }else{
      	$user = $user->fetch_assoc();
    }
}else{
  	$ytuser = daddslashes($params['ytidc_user']);
  	$ytpass = md5(md5(daddslashes($params['ytidc_pass'])));
  	$user = $DB->query("SELECT * FROM `ytidc_user` WHERE `username`='{$ytuser}' AND `password`='{$ytpass}'");
  	if($user->num_rows != 1){
      	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '账号密码错误',
    );
  	 exit(json_encode($retdata));
    }else{
      	$user = $user->fetch_assoc();
    }
}

if(empty($params['username']) || empty($params['password']) || empty($params['product']) || empty($params['time'])){
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '参数不足',
    );
  	 exit(json_encode($retdata));
}
if($DB->query("SELECT * FROM `ytidc_service` WHERE `username`='{$params['username']}'")->num_rows != 0){
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '服务用户名已被占用',
    );
  	 exit(json_encode($retdata));
}
$product = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$params['product']}'");
if($product->num_rows != 1){
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '产品不存在',
    );
  	 exit(json_encode($retdata));
}else{
	$product = $product->fetch_assoc();
}
if($product['status'] != 1){
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '该产品暂时不允许开通',
    );
  	 exit(json_encode($retdata));
}

if($product['limit'] != 0){
	$userservice = $DB->query("SELECT * FROM `ytidc_service` WHERE `userid`='{$user['id']}'")->num_rows;
	if($userservice >= $product['limit']){
		$retdata = array(
			'ret' => 'fail',
			'msg' => '开通数量已达上限',
		);
		exit(json_encode($retdata));
	}
}
$server = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$product['server']}'");
if($server->num_rows != 1){
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '服务器不存在，联系上级处理',
    );
  	 exit(json_encode($retdata));
}else{
	$server = $server->fetch_assoc();
}

if($user['grade'] != "0" && $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'")->num_rows == 1){
  	$grade = $DB->query("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'")->fetch_assoc();
	$price = json_decode($grade['price'], true);
	$discount = $price[$product['id']];
	if(empty($discount)){
		$discount = $price['*'];
		if(empty($discount)){
			$discount = 100;
		}
	}
}else{
	$discount = 100;
}
$price = json_decode($grade['price'], true);
$pdis = json_decode(url_decode($product['period']),true);
foreach($pdis as $k => $v){
	if($v['name'] == $params['time']){
		$dis = array(
			'name' => $v['name'],
			'price' => $v['price'],
			'day' => $v['day'],
			'remark' => $v['remark'],
		);
	}
}
if(empty($dis)){
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '周期设置错误，联系上级处理',
    );
  	 exit(json_encode($retdata));
}
if(empty($params['promo_code'])){
	$price = $dis['price'] * $discount / 100;
}else{
	$price = $dis['price'] * $discount / 100;
	$promo = $DB->query("SELECT * FROM `ytidc_promo` WHERE `code`='{$params['promo_code']}'");
	if($promo->num_rows != 1){
	  	$retdata = array(
	      	'ret' => 'fail',
	      	'msg' => '该优惠码不存在',
	    );
	  	 exit(json_encode($retdata));
	}else{
		$promo = $promo->fetch_assoc();
		if($promo['product'] != $product['id']){
		  	$retdata = array(
		      	'ret' => 'fail',
		      	'msg' => '该优惠码不适用于此商品',
		    );
		  	 exit(json_encode($retdata));
		}
		if($promo['status'] != 1){
		  	$retdata = array(
		      	'ret' => 'fail',
		      	'msg' => '优惠码已过期或被下架',
		    );
		  	 exit(json_encode($retdata));
		}
		if($promo['diali'] != 1 && $grade['default'] != 1){
		  	$retdata = array(
		      	'ret' => 'fail',
		      	'msg' => '该优惠码不允许代理使用',
		    );
		  	exit(json_encode($retdata));
		}
		$price = $price - $promo['price'];
	}
}
if(!check_price($price, true)){
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '价格设置错误，联系上级处理！',
    );
  	 exit(json_encode($retdata));
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
					// 2.4添加修复：检查若邀请上级为分站站长
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
$new_money = $user['money'] - $price;
if($new_money >= 0){
  	$DB->query("UPDATE `ytidc_user` SET `money`='{$new_money}' WHERE `id`='{$user['id']}'");
  	$orderid = date('YmdHis').rand(1000, 99999);
  	$DB->query("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','开通服务','{$price}','扣款','{$user['id']}','已完成')");
}else{
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '用户余额不足',
    );
  	 exit(json_encode($retdata));
}
$date = date('Y-m-d',strtotime("+{$dis[day]} days", time()));
$buydate = date('Y-m-d');
$service_password = base64_encode($params['password']);
$speriod = json_encode(url_encode($dis));
$DB->query("INSERT INTO `ytidc_service` (`userid`, `username`, `password`, `buydate`, `enddate`, `period`, `product`, `promo_code`, `configoption`, `status`) VALUES ('{$user['id']}', '{$params['username']}', '{$service_password}', '{$buydate}', '{$date}', '{$speriod}', '{$product['id']}', '{$params['promo_code']}', '' ,'等待审核')");
$serviceid = $DB->query("SELECT * FROM `ytidc_service` WHERE `username`='{$params['username']}' AND `password`='{$service_password}'")->fetch_assoc();
$serviceid = $serviceid['id'];
$plugin = ROOT."/plugins/server/".$server['plugin']."/main.php";
if(!is_file($plugin) || !file_exists($plugin)){
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => '服务器插件不存在',
    );
  	 exit(json_encode($retdata));
}
include($plugin);
$postdata = array(
  	'service' => array(
      	'username' => $params['username'],
       	'password' => $params['password'],
      	'time' => $dis,
    ),
  	'product' => $product,
  	'server' => $server,
);
$function = $server['plugin']."_CreateService";
$return = $function($postdata);

if($return['status'] == "fail"){
  	$retdata = array(
      	'ret' => 'fail',
      	'msg' => $return['msg'],
    );
  	 exit(json_encode($retdata));
}else{
	$return_pass = base64_encode($return['password']);
  	$DB->query("UPDATE `ytidc_service` SET `username`='{$return['username']}', `password`='{$return_pass}', `enddate`='{$return['enddate']}', `configoption`='{$return['configoption']}', `status`='激活' WHERE `id`='{$serviceid}'");
  	$retdata = array(
      	'ret' => 'success',
      	'msg' => '开通成功',
      	'username' => $return['username'],
      	'password' => $return['password'],
      	'enddate' => $return['enddate'],
    );
  	 exit(json_encode($retdata));
}

?>