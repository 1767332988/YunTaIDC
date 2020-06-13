<?php

namespace YunTaIDC\Site;

use YunTaIDC\Database\Database;
use YunTaIDC\User\User;

class Site{
    
    var $site;
    var $DB;
    
    public function __construct($id = null){
        $this->DB = new Database();
        if(!is_null($id)){
            if($this->DB->num_rows("SELECT * FROM `ytidc_subsite` WHERE `id`='{$id}'") != 1){
                throw new Exception("Site.php子站点不存在");
            }else{
                $this->site = $this->DB->get_row("SELECT * FROM `ytidc_subsite` WHERE `id`='{$id}'");
            }
        }
    }
    
    public function GetSiteInfo(){
        return $this->site;
    }
    
    public function SetSiteTitle($title){
        if(empty($this->site)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_subsite` SEt `title`='{$title}' WHERE `id`='{$this->site['id']}'");
        }
    }
    
    public function SetSiteSubtitle($subtitle){
        if(empty($this->site)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_subsite` SEt `subtitle`='{$subtitle}' WHERE `id`='{$this->site['id']}'");
        }
    }
    
    public function SetSiteDomain($domain){
        if(empty($this->site)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_subsite` SEt `domain`='{$domain}' WHERE `id`='{$this->site['id']}'");
        }
    }
    
    public function SetSiteUser($user){
        if(empty($this->site)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_subsite` SEt `user`='{$user}' WHERE `id`='{$this->site['id']}'");
        }
    }
    
    public function SetSiteDescription($description){
        if(empty($this->site)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_subsite` SEt `description`='{$description}' WHERE `id`='{$this->site['id']}'");
        }
    }
    
    public function SetSiteKeywords($keywords){
        if(empty($this->site)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_subsite` SEt `keywords`='{$keywords}' WHERE `id`='{$this->site['id']}'");
        }
    }
    
    public function SetSiteStatus($status){
        if(empty($this->site)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_subsite` SEt `status`='{$status}' WHERE `id`='{$this->site['id']}'");
        }
    }
    
    public function SetSiteNotice($notice){
        if(empty($this->site)){
            return false;
        }else{
            return $this->DB->exec("UPDATE `ytidc_subsite` SEt `notice`='{$notice}' WHERE `id`='{$this->site['id']}'");
        }
    }
    
    public function AddSite($params){
        return $this->DB->exec("INSERT INTO `ytidc_subsite`(`domain`, `title`, `subtitle`, `description`, `keywords`, `notice`, `user`, `status`) VALUES ('{$params['domain']}','新建分站','新建IDC分站','','','{$params['notice']}','{$params['user']}','1')");
    }
    
}

?>