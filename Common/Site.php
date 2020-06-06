<?php

namespace YunTaIDC\Site;

use YunTaIDC\Database\Database;
use YunTaIDC\User\User;

class Site{
    
    var $site;
    var $DB;
    
    public function __construct(){
        $this->DB = new Database();
    }
    
    public function GetSiteById($id){
        if(empty($id)){
            return false;
        }
        $this->site = $this->DB->get_row("SELECT * FROM `ytidc_subsite` WHERE `id`='{$id}'");
    }
    
    public function GetSiteConf(){
        return $this->site;
    }
    
    public function GetSiteTitle(){
        return $this->site['title'];
    }
    
    public function GetSiteSubtitle(){
        return $this->site['subtitle'];
    }
    
    public function GetSiteDomain(){
        return $this->site['domain'];
    }
    
    public function GetSiteUser(){
        return $this->site['user'];
    }
    
    public function GetSiteStatus(){
        return $this->site['status'];
    }
    
    public function GetSiteDescrption(){
        return $this->site['description'];
    }
    
    public function GetSiteKeywords(){
        return $this->site['keywords'];
    }
    
    public function GetSiteNotice(){
        return $this->site['notice'];
    }
    
    public function GetSiteId(){
        return $this->site['id'];
    }
    
    public function GetSiteInvitePercent(){
        return $this->site['invitepercent'];
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