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
	
	$this->crypt = 'W1nD';
        
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
	
	if( $_POST ){
	    
	    $user = alphanumeric($_POST['usuario'],"_-.");
	    
	    $pass = md5($this->crypt."".$_POST['contrasena']);
	    
	    $pdo = new db_pdo();
	   
	    $pdo->add_consult("SELECT * FROM users WHERE user='$user' AND pass='$pass' LIMIT 1");
	   
	    $query = $pdo->query();
	    
	    if( $query[0][0][id]):
	    
		session_start();
		
		$_SESSION['user'] = $query[0][0];
		
		HTTP::responseToRedirect($this->framework->sys[url]);
	    
	    else:
	    
		$error = array( "error" => 'invalid' );
	    
		HTTP::responseToRedirect($this->framework->sys[url],$error);
	    
	    endif;
	    
	}
	
    }
    
    
    
}

