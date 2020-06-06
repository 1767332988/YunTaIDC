<?php

namespace YunTaIDC\Site;

use YunTaIDC\Database\Database;
use YunTaIDC\User\User;

class Site{
    
    var $site;
    var $DB;
    
    public function __construct($id){
        $this->DB = new Database();
        if(empty($id) || $this->DB->num_rows("SELECT * FROM `ytidc_subsite` WHERE `id`='{$id}'")){
            throw new Exception("Site.php分站不存在");
        }else{
            $this->site = $this->DB->get_row("SELECT * FROM `ytidc_subsite` WHERE `id`='{$id}'");
        }
    }
    
    public function SetSiteTitle($title){
        return $this->DB->exec("UPDATE `ytidc_subsite` SEt `title`='{$title}' WHERE `id`='{$this->site['id']}'");
    }
    
    public function SetSiteSubtitle($subtitle){
        return $this->DB->exec("UPDATE `ytidc_subsite` SEt `subtitle`='{$subtitle}' WHERE `id`='{$this->site['id']}'");
    }
    
    public function SetSiteDomain($domain){
        return $this->DB->exec("UPDATE `ytidc_subsite` SEt `domain`='{$domain}' WHERE `id`='{$this->site['id']}'");
    }
    
    public function SetSiteUser($user){
        return $this->DB->exec("UPDATE `ytidc_subsite` SEt `user`='{$user}' WHERE `id`='{$this->site['id']}'");
    }
    
    public function SetSiteDescription($description){
        return $this->DB->exec("UPDATE `ytidc_subsite` SEt `description`='{$description}' WHERE `id`='{$this->site['id']}'");
    }
    
    public function SetSiteKeywords($keywords){
        return $this->DB->exec("UPDATE `ytidc_subsite` SEt `keywords`='{$keywords}' WHERE `id`='{$this->site['id']}'");
    }
    
    public function SetSiteStatus($status){
        return $this->DB->exec("UPDATE `ytidc_subsite` SEt `status`='{$status}' WHERE `id`='{$this->site['id']}'");
    }
    
    public function SetSiteNotice($notice){
        return $this->DB->exec("UPDATE `ytidc_subsite` SEt `notice`='{$notice}' WHERE `id`='{$this->site['id']}'");
    }
    
    public function GetSiteDomainList($conf){
        return json_decode($conf['subsitedomain']);
    }
    
    public function AddSite($params){
        return $this->DB->exec("INSERT INTO `ytidc_subsite`(`domain`, `title`, `subtitle`, `description`, `keywords`, `notice`, `user`, `status`) VALUES ('{$params['domain']}','新建分站','新建IDC分站','','','{$params['notice']}','{$params['user']}','1')");
    }
    
}

?>