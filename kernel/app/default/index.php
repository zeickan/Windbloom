<?php

/*
 * class forum
 */

class main extends template  {
    
    /*
     * Constructor 
     * Funciones y variables globales del script
     */
    
    function __construct() {
	    
	session_start();
	
	global $user;
        
        global $windbloom;
        
        # Configuración del Framework
        
        $this->framework = $windbloom;
        
        $this->user = $user;
        
        # Variables para la plantilla
        
        $this->msg = '';
        
        $this->template_path = "formato/";
        
        $this->template_url = $windbloom->sys['url'].'template/'.$this->template_path;
        
        $this->url_site = $windbloom->sys['url'];
        
        $this->url_app = $this->url_site.'formato/';
        
        $this->self_file = alphanumeric($_GET['acc']).".html";        
        
        # Textos para la plantilla
        
        $this->username = 'Username';
        
        $this->title = "Login";
        
        # GetHeader, GetCopyright, GetSidebar
        
        $this->header = array('function' => 'header');
        
    }
    
    protected function header(){
	    
	    $cssPath = "static/css/";
	    $item[] = $this->AddStyleSheet("reset.css",$cssPath,'all');
	    $item[] = $this->AddStyleSheet("form.css",$cssPath,'all');
	    $item[] = $this->AddStyleSheet("jquery/datePicker.css",$cssPath,'screen');
	    $item[] = $this->AddStyleSheet("datePicker.css",$cssPath,'screen');        
    
	    return join("\n    ",$item);
	    
    }
    
    /*
     * function main() 
     * El metodo principal y por defecto
     */
    
    public function main(){

    	# Insertamos un Hola mundo en {hello_world}
    	# Ejem: $this->STRING = {STRING}

    	#$this->hello_world = models::dex('Hola mundo');

    	# Insertamos una variable en la plantilla con el resultado de una función

    	#$this->bloque = array('function' => 'block');

	session_start();
	
	if( $_SESSION['user'] ):
	
	    HTTP::responseToRedirect($this->framework->sys['url'].'form/formato.html');
	
	else:
	
	    $this->action_form = 'accounts/login';
	
	    # Titulo de la página
	    
	    $this->title.= " Identificate";
	    
	    # Funciones de remplazo: header function
	    
	    $this->header = array('function' => 'header');
	    
	    # Cargar plantilla
	    
	    $this->readfiletemplate("login.form.html");
	    
	endif;
    }
    
    /*
     * Ejemplo de remplazo de función existente.
     */

    function copyright(){        
        
        return "&copy; 2012 Windbloom 2.0 REV 5 Luna 2";

    }
    
    
    function block(){
	
	    $temp = '';
	    
		$template = $this->getTemplate2Loop('default','loop');
		
		$rgex = array( "name", "email" );

		$array = array( 
			array( "name" => "Andros" , "email" => "andros@pixblob.com") 
		);
		
		$part = $this->get_template_part($array,$rgex,$template);
		
		$temp.= $part;
	    
	    return $temp;
	
    }
    
    
    
}

