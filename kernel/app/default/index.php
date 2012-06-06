<?php

/*
 * class forum
 */

class main extends template {
    
    /*
     * Constructor 
     * Funciones y variables globales del script
     */
    
    function __construct() {
	
		global $windbloom;
        
        session_start();
        
    }
    
    /*
     * function main() 
     * El metodo principal y por defecto
     */
    
    public function main(){

    	# Insertamos un Hola mundo en {hello_world}
    	# Ejem: $this->STRING = {STRING}

    	$this->hello_world = 'Hola mundo';

    	# Insertamos una variable en la plantilla con el resultado de una función

    	$this->bloque = array('function' => 'block');

    	# Titulo de la página

    	$this->title = "index method:main default";
	
		# Funciones de remplazo: header function
        
        $this->header = array('function' => 'header');
        
        # Cargar plantilla

        $this->readfiletemplate("index.default.html");
	
    }
    
    /*
     * Ejemplo de remplazo de función existente.
     */

    function copyright(){        
        
        return "&copy; 2012 Windbloom 2.0 REV 5 Luna 2";

    }
    
    
    function block(){
	
		$temp = '';
		
		    $template = $this->getTemplate2Loop('comment','loop');
		    
		    $rgex = array( "name", "email" );

		    $array = array( 
		    				array( "name" => "Andros" , "email" => "andros@pixblob.com") 
		    );
		    
		    $part = $this->get_template_part($array,$rgex,$template);
		    
		    $temp.= $part;
		
		return $temp;
	
    }
    
    
    
}

