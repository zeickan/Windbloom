<?php

/*
 * class class
 */

class admin extends template {
    
    /*
     * __construct()
     */
    
    function __construct() {

    }
    
    function copyright(){
        
        return "&copy; 2012 Windbloom 2.0 REV 1 Luna 2";
        
    }
    
    function Make(){
        
        $this->algo = 'XD';
        
        $this->xD = 'Works';
        
        $this->title = "Administrador";
        
        $this->header = array('function' => 'header');
        
        $this->copyright = array('function' => "copyright");
        
        #echo"<pre>".print_r( $_GET ,1)."</pre>";
        
        $this->readfiletemplate("admin.main.html");
        
    }
    
}


$theme = new admin;

$theme->Make();

echo $theme->returnTemplate;