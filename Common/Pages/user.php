<?php

use YunTaIDC\Template\Template;
use YunTaIDC\Input\Input;
use YunTaIDC\Database\Database;
use YunTaIDC\Functions\Functions;
use YunTaIDC\User\User;
use YunTaIDC\System\System;

class Pages{
    
    public $template;
    public $templateLoader;
    public $conf;
    public $site;
    public $DB;
    public $Input;
    public $functions;
    private $user;
    
    public function __construct($m, $conf, $site){
        $this->conf = $conf;
        $this->site = $site;
        $this->DB = new Database();
        $this->user = new User();
        $this->functions = new Functions();
        $this->Input = new Input();
        $this->user->GetUserBySessionLogin();
        if($this->isMobile()){
            $this->template = $conf['template_mobile'];
        }else{
            $this->template = $conf['template'];
        }
        $this->templateLoader = new Template($this->template);
        try{
            $this->$m();
        }catch(Exception $e){
            exit("云塔提醒您：".$e);
        }
    }
    
    public function Index(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        $servicecount = $this->DB->num_rows("SELECT * FROM `ytidc_service` WHERE `userid`='{$user['id']}'");
        $wordercount = $this->DB->num_rows("SELECT * FROM `ytidc_workorder` WHERE `user`='{$user['id']}'");
        $wordercount = $this->DB->num_rows("SELECT * FROM `ytidc_user` WHERE `invite`='{$user['id']}'");
        $noticecount = $this->DB->num_rows("SELECT * FROM `ytidc_notice` WHERE `site`='0'") + $this->DB->num_rows("SELECT * FROM `ytidc_notice` WHERE `site`='{$site['id']}'");
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'user' => $user,
        	'template_file_path' => '/templates/'.$this->template,
        	'data' => array(
        		'invitecount' => $invitecount,
        		'noticecount' => $noticecount,
        		'servicecount' => $servicecount,
        		'wordercount' => $wordercount,
        	),
        );
        echo $this->templateLoader->SetTemplate('user_index', $template_code);
    }
    
    public function Login(){
        if($this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Index");
            exit;
        }
        $inputs = $this->Input->getInputs("POST");
        if(!empty($inputs['username']) && !empty($inputs['password']) && !empty($inputs['authcode'])){
            if($inputs['authcode'] != $_SESSION['authcode']){
                @header("Location: /index.php?p=user&m=msg&msg=验证码错误&to=login");
                exit;
            }
            if($this->user->GetUserByUsernameLogin($inputs['username'], $inputs['password'])){
                @header("Location: /index.php?p=user&m=Index");
                exit;
            }else{
                @header("Location: /index.php?p=user&m=msg&msg=账号密码错误&to=login");
                exit;
            }
        }
        $_SESSION['authcode'] = rand(100000, 999999);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'authcode' => $_SESSION['authcode'],
        );
        echo $this->templateLoader->SetTemplate('user_login', $template_code);
    }
    
    public function Notice(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        $template = $this->templateLoader->GetTemplateContent("user_notice");
        if(!$template){
            throw new Exception('user.php模板文件为空或不存在');
        }
        $notice_template = $this->templateLoader->find_list_html("公告列表", $template);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_notice` WHERE `site`='0'") as $row){
    		$notice_template_code = array(
    			'id' => $row['id'],
    			'title' => $row['title'],
    		);
        	$notice_template_new = $notice_template_new . $this->templateLoader->template_code_replace($notice_template[1][0], $notice_template_code);
        }
        if($this->site['id'] != 0){
        	foreach($this->DB->get_rows("SELECT * FROM `ytidc_notice` WHERE `site`='{$this->site['id']}'") as $row){
        		$notice_template_code = array(
        			'id' => $row['id'],
        			'title' => $row['title'],
        		);
        		$notice_template_new = $notice_template_new . $this->templateLoader->template_code_replace($notice_template[1][0], $notice_template_code);
        	}
        }
        $template = str_replace($notice_template[0][0], $notice_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
	        'user' => $user,
        );
        echo $this->templateLoader->SetTemplate('user_notice', $template_code, $template);
    }
    
    public function Buy(){
        $getparams = $this->Input->getInputs('GET');
        if(empty($getparams['type'])){
            $typecontent = $this->DB->get_row("SELECT * FROM `ytidc_type` ORDER BY `weight` DESC");
        }else{
          	$typecontent = $this->DB->get_row("SELECT * FROM `ytidc_type` WHERE `id`='{$getparams['type']}'");
        }
        if(empty($typecontent)){
            return false;
        }
        $template = $this->templateLoader->GetTemplateContent("user_buy");
        if(!$template){
            return false;
        }
        $type_template = $this->templateLoader->find_list_html("分类列表", $template);
        $subtype_template = $this->templateLoader->find_list_html("二级分类列表", $type_template[1][0]);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_type` WHERE `status`='1' AND `father`='0' ORDER BY `weight` DESC") as $row){
            $subtype_template_new = "";
        	foreach($this->DB->get_rows("SELECT * FROM `ytidc_type` WHERE `status`='1' AND `father`='{$row['id']}' ORDER BY `weight` DESC") as $row2){
        		$subtype_template_code = array(
        			'name' => $row2['name'],
        			'id' => $row2['id'],
        		);
        		$subtype_template_new = $subtype_template_new . $this->templateLoader->template_code_replace($subtype_template[1][0], $subtype_template_code);
        	}
        	$type_template_cache = str_replace($subtype_template[0][0], $subtype_template_new, $type_template[1][0]);
        	$type_template_code = array(
        		'name' => $row['name'],
        		'id' => $row['id'],
        	);
        	$type_template_new = $type_template_new . $this->templateLoader->template_code_replace($type_template_cache, $type_template_code);
        }
        $template = str_replace($type_template[0][0], $type_template_new, $template);
        $product_template = $this->templateLoader->find_list_html("产品列表", $template);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_product` WHERE `type`='{$typecontent['id']}' AND `hidden`='0' ORDER BY `weight` DESC") as $row){
            $period = json_decode($this->functions->url_decode($row['period']), true);
        	$product_template_code = array(
        		'name' => $row['name'],
        		'id' => $row['id'],
        		'price' => $period[1]['price'],
        		'period' => $period[1]['name'],
        		'description' => $row['description'],
        	);
        	$product_template_new = $product_template_new . $this->templateLoader->template_code_replace($product_template[1][0], $product_template_code);
        }
        $template = str_replace($product_template[0][0], $product_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'type' => $typecontent,
        	'template_file_path' => '/templates/'.$this->template,
        );
        echo $this->templateLoader->SetTemplate('user_buy', $template_code, $template);
    }
    
    public function Register(){
        if($this->user->isLogin()){
            @header("Location: ./index.php?p=user&m=index");
            exit;
        }
        $getparams = $this->Input->getInputs('GET');
        $DB = $this->DB;
        if(!empty($getparams['code']) && empty($_SESSION['invite'])){
            if($DB->num_rows("SELECT * FROM `ytidc_user` WHERE `id`='{$getparams['code']}'") == 1){
                $_SESSION['invite'] = $getparams['code'];
            }else{
                $_SESSION['invite'] = 0;
            }
        }else{
            $_SESSION['invite'] = 0;
        }
        $posts = $this->Input->getInputs('POST');
        if(!empty($posts['username']) && !empty($posts['password']) && !empty($posts['email']) && !empty($posts['authcode'])){
            if($post['authcode'] != $_SESSION['authcode']){
          	    @header("Location: /index.php?p=user&m=msg&msg=验证码错误");
            	exit();
            }
          	if($this->user->RegisterUser($posts['username'], $posts['password'], $posts['email'], 0, $this->site['id'])){
          	    @header("Location: /index.php?p=user&m=msg&msg=注册成功");
          	}else{
          	    @header("Location: /index.php?p=user&m=msg&msg=注册失败，录入数据库失败");
          	}
          	exit();
        }
        $_SESSION['authcode'] = rand(100000, 999999);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'authcode' => $_SESSION['authcode'],
        );
        echo $this->templateLoader->SetTemplate('user_register', $template_code);
    }
    
    public function Msg(){
        $getparams = $this->Input->getInputs("GET");
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'msg' => $getparams['msg'],
        	'to' => $getparams['to']
        );
        echo $this->templateLoader->SetTemplate('user_msg', $template_code);
    }
    
    public function Order(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        $template = $this->templateLoader->GetTemplateContent("user_order");
        if(!$template){
            return false;
        }
        $order_template = $this->templateLoader->find_list_html("订单列表", $template);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_order` WHERE `user`='{$user['id']}' ORDER BY `orderid` DESC") as $row){
        	$order_template_code = array(
        		'orderid' => $row['orderid'],
        		'description' => $row['description'],
        		'money' => $row['money'],
        		'action' => $row['action'],
        		'status' => $row['status'],
        	);
        	$order_template_new = $order_template_new . $this->templateLoader->template_code_replace($order_template[1][0], $order_template_code);
        }
        $template = str_replace($order_template[0][0], $order_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        );
        echo $this->templateLoader->SetTemplate('user_order', $template_code, $template);
    }
    
    public function Service(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        $template = $this->templateLoader->GetTemplateContent("user_service");
        if(!$template){
            return false;
        }
        $service_template = $this->templateLoader->find_list_html("在线服务列表", $template);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_service` WHERE `userid`='{$user['id']}'") as $row){
        	$service_template_code = array(
        		'id' => $row['id'],
        		'username' => $row['username'],
        		'password' => $row['password'],
        		'enddate' => $row['enddate'],
        		'product' => $product[$row['product']],
        		'status' => $row['status'],
        	);
        	$service_template_new = $service_template_new . $this->templateLoader->template_code_replace($service_template[1][0], $service_template_code);
        }
        $template = str_replace($service_template[0][0], $service_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        );
        echo $this->templateLoader->SetTemplate('user_service', $template_code, $template);
    }
    
    public function Logout(){
        unset($_SESSION);
        @header("Location: /index.php?p=user&m=Login");
    }
    
    public function Price(){
        $DB = $this->DB;
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        if(!empty($this->getparams['update'])){
            $usergrade = $DB->get_row("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'");
            $updategrade = $DB->get_row("SELECT * FROM `ytidc_grade` WHERE `id`='{$this->getparams['update']}'");
            if(empty($updategrade)){
                @header("Location: ./index.php?p=user&m=msg&msg=该价格组不存在");
                exit();
            }
            if($updategrade['default']){
                @header("Location: ./index.php?p=user&m=msg&msg=默认价格组不支持升级");
                exit();
            }
        	if($usergrade['weight'] >= $updategrade['weight']){
                @header("Location: ./index.php?p=user&m=msg&msg=不能开通更低级的价格组哦！");
                exit();
        	}
        	foreach($this->DB->get_rows("SELECT * FROM `ytidc_order` WHERE `user`='{$user['id']}' AND `action`='扣款'") as $row){
        	    $money = $money + $row['money'];
        	}
        	if($money >= $updategrade['need_paid'] && $updategrade['need_paid'] != 0){
        		$DB->exec("UPDATE `ytidc_user` SET `grade`='{$grade['id']}' WHERE `id`='{$user['id']}'");
        		@header("Location: ./index.php?p=user&m=msg&msg=开通成功");
        		exit;
        	}
        	if($user['money'] >= $updategrade['need_save']&& $updategrade['need_save'] != 0){
        		$DB->exec("UPDATE `ytidc_user` SET `grade`='{$grade['id']}' WHERE `id`='{$user['id']}'");
        		@header("Location: ./index.php?p=user&m=msg&msg=开通成功");
        		exit;	
        	}
        	if($user['money'] >= $updategrade['need_money'] && $updategrade['need_money'] != 0){
        		$new_money = $user['money'] - $grade['need_money'];
        		$DB->exec("UPDATE `ytidc_user` SET `grade`='{$grade['id']}', `money`='{$new_money}' WHERE `id`='{$user['id']}'");
        		@header("Location: ./index.php?p=user&m=msg&msg=开通成功");
        		exit;
        	}
        	@header("Location: ./index.php?p=user&m=msg&msg=开通失败，条件尚未达到");
        	exit;
        }
        $template = $this->templateLoader->GetTemplateContent("user_price");
        if(!$template){
            return false;
        }
        $price_template = $this->templateLoader->find_list_html("价格组列表", $template);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_grade` WHERE `status`='1'") as $row){
        	$price_template_code = array(
        		'name' => $row['name'],
        		'id' => $row['id'],
        		'description' => $row['description'],
        		'need_save' => $row['need_save'],
        		'need_money' => $row['need_money'],
        		'need_paid' => $row['need_paid'],
        	);
        	$price_template_new = $price_template_new . $this->templateLoader->template_code_replace($price_template[1][0], $price_template_code);
        }
        $template = str_replace($price_template[0][0], $price_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        );
        echo $this->templateLoader->SetTemplate('user_price', $template_code, $template);
    }
    
    public function WorkOrder(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        $template = $this->templateLoader->GetTemplateContent("user_workorder");
        if(!$template){
            return false;
        }
        $worder_template = $this->templateLoader->find_list_html("服务单列表", $template);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_worder` WHERE `user`='{$user['id']}'") as $row){
        	$worder_template_code = array(
        		'id' => $row['id'],
        		'title' => $row['title'],
        		'status' => $row['status'],
        	);
        	$worder_template_new = $worder_template_new . $this->templateLoader->template_code_replace($worder_template[1][0], $worder_template_code);
        }
        $template = str_replace($worder_template[0][0], $worder_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        );
        echo $this->templateLoader->SetTemplate('user_workorder', $template_code, $template);
    }
    
    public function Workorderdetail(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        if(empty($this->getparams['workorderid'])){
            @header("Location: /index.php?p=user&m=Workorder");
            exit;
        }
        if($this->DB->num_rows("SELECT * FROM `ytidc_worder` WHERE `id`='{$this->getparams['workorderid']}' AND `user`='{$user['id']}'") != 1){
            @header("Location: /index.php?p=user&m=Workorder");
            exit;
        }else{
            $worder = $this->DB->get_row("SELECT * FROM `ytidc_worder` WHERE `id`='{$this->getparams['workorderid']}' AND `user`='{$user['id']}'");
        }
        $postparams = $this->security->daddslashes($_POST);
        if(!empty($postparams['reply'])){
          	$time = date('Y-m-d H:i:s');
        	$this->DB->exec("INSERT INTO `ytidc_wreply`(`person`, `content`, `worder`, `time`) VALUES ('{$user['username']}','{$reply}','{$this->getparams['workorderid']}','{$time}')");
        	$this->DB->exec("UPDATE `ytidc_worder` SET `status`='待处理' WHERE `id`='{$this->getparams['workorderid']}'");
        	@header('Location: /index.php?p=user&m=Msg&msg=回复成功');
        	exit;
        }
        $template = $this->templateLoader->GetTemplateContent("user_workorder_detail");
        $reply_template = $this->templateLoader->find_list_html('回复列表', $template);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_wreply` WHERE `worder`='{$this->getparams['workorderid']}'") as $row){
        	$reply_template_code = array(
        		'person' => $row['person'],
        		'content' => $row['content'],
        		'time' => $row['time'],
        	);
        	$reply_template_new = $reply_template_new . $this->templateLoader->template_code_replace($reply_template[1][0], $reply_template_code);
        }
        $template = str_replace($reply_template[0][0], $reply_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        	'worder' => $worder,
        );
        echo $this->templateLoader->SetTemplate('user_workorder_detail', $template_code, $template);
    }
    
    public function ServiceDetail(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        if(empty($this->getparams['serviceid'])){
            @header("Location: /index.php?p=user&m=Service");
            exit;
        }
        if($this->DB->num_rows("SELECT * FROM `ytidc_service` WHERE `id`='{$this->getparams['serviceid']}' AND `userid`='{$user['id']}'") != 1){
            @header("Location: /index.php?p=user&m=Service");
            exit;
        }else{
            $service = $this->DB->get_row("SELECT * FROM `ytidc_service` WHERE `id`='{$this->getparams['serviceid']}' AND `userid`='{$user['id']}'");
        }
        if($service['status'] != '激活' && $service['status'] != '暂停'){
        	@header("Location: /index.php?p=user&m=Msg&msg=服务状态：".$service['status'].'，联系上级处理！');
        	exit();
        }
        $service['password'] = base64_decode($service['password']);
        $product = $this->DB->get_row("SELECT * FROM `ytidc_product` WHERE `id`='{$service['product']}'");
        $template = $this->templateLoader->GetTemplateContent("user_service_detail");
        $time_template = $this->templateLoader->find_list_html('周期列表', $template);
        $pdis = json_decode($this->functions->url_decode($product['period']), true);
        if($user['grade'] != "0" && $this->DB->num_rows("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'") == 1){
          	$grade = $this->DB->get_row("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'");
          	$price = json_decode($grade['price'], true);
        	if(empty($price[$product['id']])){
        		$discount = $price['*'];
        	}else{
        		$discount = $price[$product['id']];
        	}
        	if(empty($discount)){
        		$discount = 100;
        	}
        }else{
        	$discount = 100;
        }
        foreach($pdis as $k => $v){
        	$time_template_code = array(
        		'name' => $v['name'],
        		'price' => $discount * $v['price'] / 100,
        	);
        	$time_template_new = $time_template_new . $this->templateLoader->template_code_replace($time_template[1][0], $time_template_code);
        }
        $template = str_replace($time_template[0][0], $time_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        	'service' => $service,
        	'product' => $product,
        );
        echo $this->templateLoader->SetTemplate('user_service_detail', $template_code, $template);
    }
    
    public function NoticeDetail(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        if(empty($this->getparams['noticeid'])){
            @header("Location: /index.php?p=user&m=Notice");
            exit;
        }
        if($this->DB->num_rows("SELECT * FROM `ytidc_notice` WHERE `id`='{$this->getparams['noticeid']}'") != 1){
            @header("Location: /index.php?p=user&m=Notice");
            exit;
        }else{
            $notice = $this->DB->get_row("SELECT * FROM `ytidc_notice` WHERE `id`='{$this->getparams['noticeid']}'");
        }
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        	'notice' => $notice,
        );
        echo $this->templateLoader->SetTemplate('user_notice_detail', $template_code);
    }
    
    public function Cart(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        if(empty($this->getparams['productid'])){
            @header("Location: /index.php?p=user&m=Buy");
            exit;
        }
        if($this->DB->num_rows("SELECT * FROM `ytidc_product` WHERE `id`='{$this->getparams['productid']}'") != 1){
            @header("Location: /index.php?p=user&m=Buy");
            exit;
        }else{
            $product = $this->DB->get_row("SELECT * FROM `ytidc_product` WHERE `id`='{$this->getparams['productid']}'");
        }
        $pdis = json_decode($this->functions->url_decode($product['period']), true);
        if($user['grade'] != "0" && $this->DB->num_rows("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'") == 1){
          	$grade = $this->DB->get_row("SELECT * FROM `ytidc_grade` WHERE `id`='{$user['grade']}'");
        	$price = json_decode($grade['price'], true);
        	if(empty($price[$row['id']])){
        		$discount = $price['*'];
        	}else{
        		$discount = $price[$product['id']];
        	}
        	if(empty($discount)){
        		$discount = 100;
        	}
        }else{
        	$discount = 100;
        }
        $template = $this->templateLoader->GetTemplateContent("user_cart");
        $time_template = $this->templateLoader->find_list_html('周期列表', $template);
        foreach($pdis as $k => $v){
        	$time_template_code = array(
        		'name' => $v['name'],
        		'price' => $discount * $v['price'] / 100,
        	);
        	$time_template_new = $time_template_new . $this->templateLoader->template_code_replace($time_template[1][0], $time_template_code);
        }
        $template = str_replace($time_template[0][0], $time_template_new, $template);
        if($this->conf['random_username'] == 1){
            $service_username = $this->functions->randomkeys(8);
        }else{
            $service_username = "";
        }
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        	'product' => array(
        		'id' => $product['id'],
        		'name' => $product['name'],
        		'description' => $product['description'],
        	),
        	'random_username' => $service_username,
        );
        echo $this->templateLoader->SetTemplate('user_cart', $template_code, $template);
    }
    
    public function AddWorkOrder(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        if(!empty($_POST['title']) && !empty($_POST['content'])){
            $postparams = $this->security->daddslashes($_POST);
        	$this->DB->exec("INSERT INTO `ytidc_worder`(`title`, `user`, `status`) VALUES ('{$postparams['title']}','{$user['id']}','待回复')");
        	$newid = $this->DB->lastInsertId();
          	$time = date('Y-m-d H:i:s');
        	$this->DB->exec("INSERT INTO `ytidc_wreply`(`person`, `content`, `worder`, `time`) VALUES ('{$user['username']}','{$postparams['content']}','{$newid}','{$time}')");
        	@header("Location: /index.php?p=user&m=Msg&msg=提交成功！请等待处理！");
        	exit;
        }
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        );
        echo $this->templateLoader->SetTemplate('user_add_workorder', $template_code);
    }
    
    public function Profile(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        if(!empty($_POST['email'])){
        	if(!empty($_POST['password'])){
        		$password = md5(md5($this->security->daddslashes($_POST['password'])));
        	}else{
        		$password = $user['password'];
        	}
        	$email = $this->security->daddslashes($_POST['email']);
        	$this->DB->exec("UPDATE `ytidc_user` SET `password`='{$password}', `email`='{$email}' WHERE `username`='{$user['username']}'");
        	@header("Location: /index.php?p=user&m=Msg&msg=更新资料成功！");
        	exit;
        }
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        );
        echo $this->templateLoader->SetTemplate('user_profile', $template_code);
    }
    
    public function Invite(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        $template = $this->templateLoader->GetTemplateContent("user_invite");
        if(!$template){
            return false;
        }
        $invite_template = $this->templateLoader->find_list_html("邀请列表", $template);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_user` WHERE `invite`='{$user['id']}'") as $row){
        	$invite_template_code = array(
        		'id' => $row['id'],
        		'username' => $row['username'],
        	);
        	$invite_template_new = $invite_template_new . $this->templateLoader->template_code_replace($invite_template[1][0], $invite_template_code);
        }
        $template = str_replace($invite_template[0][0], $invite_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'invite_link' => "http://".$_SERVER['HTTP_HOST']."/index.php?p=user&m=Register&code=".$user['id'],
        	'user' => $user,
        );
        echo $this->templateLoader->SetTemplate('user_invite', $template_code, $template);
    }
    
    public function Pay(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        $template = $this->templateLoader->GetTemplateContent("user_pay");
        if(!$template){
            return false;
        }
        $gateway_template = $this->templateLoader->find_list_html("支付通道列表", $template);
        foreach($this->DB->get_rows("SELECT * FROM `ytidc_gateway` WHERE `status`='1'") as $row){
        	$gateway_template_code = array(
        		'gateway' => $row['id'],
        		'name' => $row['name'],
        		'fee' => $row['fee'],
        	);
        	$gateway_template_new = $gateway_template_new . $this->templateLoader->template_code_replace($gateway_template[1][0], $gateway_template_code);
        }
        $template = str_replace($gateway_template[0][0], $gateway_template_new, $template);
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        );
        echo $this->templateLoader->SetTemplate('user_pay', $template_code, $template);
    }
    
    public function SubSite(){
        if(!$this->user->isLogin()){
            @header("Location: /index.php?p=user&m=Login");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        if($this->DB->num_rows("SELECT * FROM `ytidc_fenzhan` WHERE `user`='{$user['id']}'") >= 1){
            @header("Location: /index.php?p=user&m=Msg&msg=您已开通分站，请不要再次开通！");
        	exit;
        }
        if(!empty($_POST['domain']) && !empty($_POST['title'])){
          	$domain = $this->security->daddslashes($_POST['domain']) . '.' . $conf['sitedomain'];
          	$title = $this->security->daddslashes($_POST['title']);
          	$admin = $this->security->daddslashes($_POST['admin']);
          	$password = $this->security->daddslashes($_POST['password']);
          	$description = $this->security->daddslashes($_POST['description']);
          	$new_money = $user['money'] - $this->conf['siteprice'];
          	if($new_money < 0){
              	@header("Location: /index.php?p=user&m=Msg&msg=余额不足");
              	exit;
            }else{
            	$orderid = date('YmdHis').rand(1000, 99999);
            	$this->DB->exec("INSERT INTO `ytidc_order`(`orderid`, `description`, `money`, `action`, `user`, `status`) VALUES ('{$orderid}','开通分站','{$conf['siteprice']}','扣款','{$user['id']}','已完成')");
            }
          	$DB->exec("UPDATE `ytidc_user` SET `money`='{$new_money}' WHERE `username`='{$user['username']}'");
          	$DB->exec("INSERT INTO `ytidc_subsite`(`domain`, `title`, `subtitle`, `description`, `keywords`, `notice`, `user`, `status`) VALUES ('{$domain}','{$title}','企业级云服务器','{$description}','','{$conf['notice']}','{$user['id']}','1')");
        	@header("Location: ./msg.php?msg=开通成功！");
          	exit;
        }
        $template_code = array(
        	'site' => $this->site,
        	'config' => $this->conf,
        	'template_file_path' => '/templates/'.$this->template,
        	'user' => $user,
        );
        echo $this->templateLoader->SetTemplate('user_subsite', $template_code, $template);
    }
    
    public function isMobile(){
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE']))
            return true;
        
        //此条摘自TPM智能切换模板引擎，适合TPM开发
        if(isset ($_SERVER['HTTP_CLIENT']) &&'PhoneClient'==$_SERVER['HTTP_CLIENT'])
            return true;
        //如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA']))
            //找不到为flase,否则为true
            return stristr($_SERVER['HTTP_VIA'], 'wap') ? true : false;
        //判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array(
                'nokia','sony','ericsson','mot','samsung','htc','sgh','lg','sharp','sie-','philips','panasonic','alcatel','lenovo','iphone','ipod','blackberry','meizu','android','netfront','symbian','ucweb','windowsce','palm','operamini','operamobi','openwave','nexusone','cldc','midp','wap','mobile'
            );
            //从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))) {
                return true;
            }
        }
        //协议法，因为有可能不准确，放到最后判断
        if (isset ($_SERVER['HTTP_ACCEPT'])) {
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== false) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === false || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))) {
                return true;
            }
        }
        return false;
    }
    
}

?>