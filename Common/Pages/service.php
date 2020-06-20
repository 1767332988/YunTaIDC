<?php

use YunTaIDC\Input\Input;
use YunTaIDC\Database\Database;
use YunTaIDC\Functions\Functions;
use YunTaIDC\User\User;
use YunTaIDC\Plugin\PluginBase;
use YunTaIDC\Plugin\PluginManager;
use YunTaIDC\Service\Service;
use YunTaIDC\Product\Product;
use YunTaIDC\Server\Server;

class Pages{
    
    public $m;
    public $conf;
    public $site;
    public $Input;
    public $DB;
    public $service;
    private $user;
    
    public function __construct($m, $conf, $site, $DB){
        $this->m = $m;
        $this->security = new Security();
        $this->DB = $DB;
        $this->conf = $conf;
        $this->site = $site;
        $this->user = new User("", $DB);
        if($this->user->GetUserBySessionLogin){
            @header("Location: ./index.php?p=user&m=Login");
            exit;
        }
        $this->service = new Service();
        $this->Input = $Input->getInputs("GET");
        try{
            $this->$m();
        }catch(Exception $e){
            exit("云塔提醒您：".$e);
        }
    }
    
    public function Create(){
        $params = $this->Input->getInputs("POST");
        try {
            $product = new Product($params['productid']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $product = $product->GetProductInfo();
        $function = new Functions();
        $periods = $function->url_decode($product['period']);
        foreach($periods as $k => $v){
            if($v['id'] == $params['period']){
                $dis = array(
        			'name' => $v['name'],
        			'price' => $v['price'],
        			'day' => $v['day'],
        			'remark' => $v['remark'],
        		);
            }
        }
        if(empty($dis)){
            throw new Exception('service.php无法获取正确周期');
        }
        $enddate = date("Y-m-d", strtotime("+{$dis['day']} days", time()));
        try {
            $server = new Server($product['server']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $server = $server->GetServerInfo();
        $user = $this->user->GetUserInfo();
        
        $servicedata = array(
            'username' => $params['username'],
            'password' => $params['password'],
            'user' => $this->user['id'],
            'buydate' => date('Y-m-d'),
            'enddate' => $enddate,
            'period' => $dis,
            'productid' => $product['id'],
            'promo_code' => $params['promo_code'],
            'status' => '等待审核',
        );
        if(!$this->service->AddService($servicedata)){
            throw new Exception('service.php服务录入数据库失败');
        }
        try {
            $manager = new PluginManager();
            $plugin = $manager->LoadPlugin($server['plugin']);
        } catch (Exception $e) {
            throw new Exception($e->getMessage());
        }
        $return = $plugin->CreateService();
        if($return['status'] == 'ok'){
            
        }
    }
    
}

?>