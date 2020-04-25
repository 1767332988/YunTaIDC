<?php

//服务器插件提供所需功能
global $DB;

function Plugin_GetServiceById($id){
	global $DB;
	$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `id`='{$id}'");
	if($result->num_rows != 1){
		return false;
	}else{
		return $result->fetch_assoc();
	}
}

function Plugin_GetServiceByUsername($username){
	global $DB;
	$result = $DB->query("SELECT * FROM `ytidc_service` WHERE `username`='{$username}'");
	if($result->num_rows != 1){
		return false;
	}else{
		return $result->fetch_assoc();
	}
}

function Plugin_GetProductById($id){
	global $DB;
	$result = $DB->query("SELECT * FROM `ytidc_product` WHERE `id`='{$id}'");
	if($result->num_rows != 1){
		return false;
	}else{
		return $result->fetch_assoc();
	}
}

function Plugin_GetServerById($id){
	global $DB;
	$result = $DB->query("SELECT * FROM `ytidc_server` WHERE `id`='{$id}'");
	if($result->num_rows != 1){
		return false;
	}else{
		return $result->fetch_assoc();
	}
}

function Plugin_ExplodeProductPeriod($period){
	$pdis = json_decode(url_decode($period),true);
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
	return $dis;
}

function Plugin_ChangeServicePassword($id, $password){
	global $DB;
	$DB->query("UPDATE `ytidc_service` SET `password`='{$password}' WHERE `id`='{$id}'");
	if(!$DB->error){
		return true;
	}else{
		return false;
	}
}

function Plugin_ChangeServiceEnddate($id, $enddate){
	global $DB;;
	$DB->query("UPDATE `ytidc_service` SET `enddate`='{$enddate}' WHERE `id`='{$id}'");
	if(!$DB->error){
		return true;
	}else{
		return false;
	}
}

function Plugin_ChangeServiceConfigOption($id, $configoption){
	global $DB;;
	$DB->query("UPDATE `ytidc_service` SET `configopiton`='{$configoption}' WHERE `id`='{$id}'");
	if(!$DB->error){
		return true;
	}else{
		return false;
	}
}

function Plugin_TemplateRequirer($file, $plugin){
	if(preg_match('/\.\./U',$file) || preg_match('/\.\./U',$plugin)){
		return false;
	}
	if(file_exists(ROOT.'/plugins/server/'.$plugin.'/template/'.$file.'.template')){
		return file_get_contents(ROOT.'/plugins/server/'.$plugin.'/template/'.$file.'.template');
	}else{
		return false;
	}
}

?>