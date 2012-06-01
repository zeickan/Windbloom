<?php


class json {
    
    
    function __construct(){
        
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json');
        
        $this->json = new jsonLib;
        
        $this->id = alphanumeric($_GET['id']);
        
    }
    
    public function main(){
        
        $jstr = $this->json->convert($array);
        
        $this->json->publish($jstr);  
        
    }
    
    public function GetCityByState(){
        
       $pdo = new db_pdo();
       
       $pdo->add_consult("SELECT * FROM tdu_city WHERE rel_id='".$this->id."'");
       
       $query = $pdo->query();
       
       $json = $this->json->resetArray($query[0],array("id","rel_id","meta_key","meta_value"));
       
       $jstr = $this->json->convert($json);
       
       $this->json->publish($jstr);
        
    }
    
    public function GetAddressByState(){
        
        $pdo = new db_pdo();
       
       $pdo->add_consult("SELECT * FROM tdu_address WHERE rel_id='".$this->id."'");
       
       $query = $pdo->query();
       
       $json = $this->json->resetArray($query[0],array("id","rel_id","meta_key","meta_value"));
       
       $jstr = $this->json->convert($json);
       
       $this->json->publish($jstr);
        
    }
    
    
}