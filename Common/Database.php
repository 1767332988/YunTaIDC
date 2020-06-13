<?php

namespace YunTaIDC\Database;
use PDO as PDO;

class Database{
    
    private $DB;
    
    public function __construct(){
        $dsn = DBCONFIG['type'].':dbname='.DBCONFIG['name'].';host='.DBCONFIG['host'];
        $user = DBCONFIG['user'];
        $password = DBCONFIG['pass'];
        try {
            $dbh = new PDO($dsn, $user, $password);
            $this->DB = $dbh;
        } catch (PDOException $e) {
            return false;
        }
    }
    
    public function exec($sql){
        $DB = $this->DB;
        if($DB->exec($sql)){
            return true;
        }else{
            return false;
        }
    }
    
    public function get_rows($sql){
        $DB = $this->DB;
        return $DB->query($sql);
    }
    
    public function num_rows($sql){
        $DB = $this->DB;
        $row2 = array();
        foreach($DB->query($sql) as $row){
            $row2[] = $row;
        }
        return count($row2);
    }
    
    public function get_row($sql){
        $DB = $this->DB;
        $row2 = array();
        foreach($DB->query($sql) as $row){
            $row2[] = $row;
        }
        return $row2[0];
    }
    
    public function lastInsertId(){
        $DB = $this->DB;
        return $DB->lastInsertId();
    }
    
    public function error(){
        $DB = $this->DB;
        return $DB->errorCode();
    }
    
}

?>