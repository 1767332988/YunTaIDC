<?php

use YunTaIDC\Template\Template;
use YunTaIDC\Security\Security;
use YunTaIDC\Database\Database;
use YunTaIDC\Functions\Functions;
use YunTaIDC\User\User;

class Pages{
    
    public $m;
    public $template;
    public $templateLoader;
    public $conf;
    public $site;
    public $security;
    public $DB;
    public $formator;
    public $getparams;
    private $user;
    
    public function __construct($m, $conf, $site, $DB, $getparams){
        $this->m = $m;
        $this->security = new Security();
        $this->DB = $DB;
        $this->conf = $conf;
        $this->site = $site;
        $this->user = new User("", $DB);
        $this->formator = new Functions();
        $this->getparams = $getparams;
        if($this->isMobile()){
            $this->template = $conf['template_mobile'];
        }else{
            $this->template = $conf['template'];
        }
        $this->templateLoader = new Template($this->template);
        try{
            $this->$m();
        }catch(Error $e){
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
        $wordercount = $this->DB->num_rows("SELECT * FROM `ytidc_worder` WHERE `user`='{$user['id']}'");
        $invitecount = $this->DB->num_rows("SELECT * FROM `ytidc_user` WHERE `invite`='{$user['id']}'");
        $noticecount = $this->DB->num_rows("SELECT * FROM `ytidc_notice`");
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
        if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['authcode'])){
            $params = $this->security->daddslashes($_POST);
            if($params['authcode'] != $_SESSION['authcode']){
                @header("Location: /index.php?p=user&m=msg&msg=验证码错误&to=/index/user/Login/");
                exit;
            }
            if($this->user->Login($params['username'], $params['password'])){
                @header("Location: /index.php?p=user&m=Index");
                exit;
            }else{
                @header("Location: /index.php?p=user&m=msg&msg=账号密码错误&to=/index/user/Login/");
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
        
    }
    
    public function Buy(){
        $getparams = $this->getparams;
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
            $period = json_decode($this->formator->url_decode($row['period']), true);
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
        $getparams = $this->getparams;
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
        if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['email']) && !empty($_POST['authcode'])){
            $username = $this->security->daddslashes($_POST['username']);
            if($DB->num_rows("SELECT * FROM `ytidc_user` WHERE `username`='{$username}'") != 0){
          	    @header("Location: /index.php?p=user&m=msg&msg=用户名已被注册");
            	exit();
            }
            $password = $this->security->daddslashes($_POST['password']);
            $password = md5(md5($password));
          	$email = $this->security->daddslashes($_POST['email']);
            if($DB->num_rows("SELECT * FROM `ytidc_user` WHERE `email`='{$email}'") != 0){
          	    @header("Location: /index.php?p=user&m=msg&msg=邮箱已被使用");
            	exit();
            }
            $invite = $_SESSION['invite'];
            $authcode = $this->security->daddslashes($_POST['authcode']);
            if($authcode != $_SESSION['authcode']){
          	    @header("Location: /index.php?p=user&m=msg&msg=验证码错误");
            	exit();
            }
          	$domain = $_SERVER['HTTP_HOST'];
          	$site = $this->site['id'];
          	if($DB->num_rows("SELECT * FROM `ytidc_grade` WHERE `default`='1'") == 1){
          		$grade = $DB->get_row("SELECT * FROM `ytidc_grade` WHERE `default`='1'");
          		$grade = $grade['id'];
          	}else{
          		$grade = 0;
          	}
          	if($DB->exec("INSERT INTO `ytidc_user` (`username`, `password`, `email`, `money`, `grade`, `invite`, `site`, `status`) VALUE ('{$username}', '{$password}', '{$email}', '0.00', '{$grade}', '{$invite}', '{$site}', '1')")){
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
        $getparams = $this->getparams;
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
        $template = $this->templateLoader->GetTemplateContent("user_worder");
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
        $template = $this->templateLoader->GetTemplateContent("user_worder_detail");
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
        echo $this->templateLoader->SetTemplate('user_workorder', $template_code, $template);
    }
    
    public function ServiceDetali(){
        
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