<?php

use Template\Template\Template;
use Security\Security\Security;
use Database\Database\Database;
use Functions\Functions\Functions;
use User\User\User;

class Pages{
    
    public $m;
    public $template;
    public $templateLoader;
    public $conf;
    public $site;
    public $security;
    public $DB;
    public $formator;
    
    private $user;
    
    public function __construct($m, $conf, $site, $DB){
        $this->m = $m;
        $this->security = new Security();
        $this->DB = $DB;
        $this->conf = $conf;
        $this->site = $site;
        $this->user = new User("", $DB);
        $this->formator = new Functions();
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
            @header("Location: /index2.php/user/Login/");
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
            @header("Location: /index.php/user/Index/");
            exit;
        }
        if(!empty($_POST['username']) && !empty($_POST['password']) && !empty($_POST['authcode'])){
            $params = $this->security->daddslashes($_POST);
            if($params['authcode'] != $_SESSION['authcode']){
                @header("Location: /index.php/user/Msg/msg=验证码错误&to=/index/user/Login/");
                exit;
            }
            if($this->user->Login($params['username'], $params['password'])){
                @header("Location: /index.php/user/Index/");
                exit;
            }else{
                @header("Location: /index.php/user/Msg/msg=账号密码错误&to=/index/user/Login/");
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
            @header("Location: /index2.php/user/Login/");
            exit;
        }else{
            $user = $this->user->GetUserInfo();
        }
        
    }
    
    public function Buy(){
        $params = $this->security->daddslashes($_GET);
        if(empty($params['type'])){
            $type = 0;
            $typecontent = $this->DB->get_row("SELECT * FROM `ytidc_type` ORDER BY `weight` DESC");
        }else{
            $type = $params['type'];
          	$typecontent = $this->DB->get_row("SELECT * FROM `ytidc_type` WHERE `id`='{$type}'");
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