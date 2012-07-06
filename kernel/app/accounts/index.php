<?php

/*
 * class forum
 */

class accounts extends template  {
    
    /*
     * Constructor 
     * Funciones y variables globales del script
     */
    
    function __construct() {
	    
	session_start();
	
	global $user;
        
        global $windbloom;
        
        # Configuraci—n del Framework
        
        $this->framework = $windbloom;
        
        $this->user = $user;
        
        # Textos para la plantilla
        
        $this->username = 'Username';
        
        $this->title = "Login";
        
    }
    

    /*
     * function main() 
     * El metodo principal y por defecto
     */
    
    public function main(){
	
	header("CONTENT-TYPE: text/plain;");

        echo"Accounts...";
	
    }
    
    /*
     * function login()
     * Login
     */
    
    public function login(){
	
	echo 'asd';
	
    }
    
    
    
}

