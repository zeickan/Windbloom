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
    
    
}