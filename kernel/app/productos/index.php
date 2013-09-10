<?php

/*
 * class main 
 */

class productos extends models {

	/*
     * __construct()
     * @param $arg
     */
    
    function __construct() {	

    	global $windbloom;

    	$this->url_site = $windbloom->sys['url'];
        
        $this->url_app = $this->url_site.'';

        $this->current_link();

    }
    
    protected function header(){

    	/* Armamos el HEADER DEFAULT */

    	$item[] = parent::header();
	
		$cssPath = "static/stylesheets/";

		return join("\n    ",$item);
	    
    }
    
    
    public function main(){

    	$this->dashboard();

    }
    
    /*
     * function dashboard
     */
    
    function dashboard() {
	
		# Titulo adicional
		$this->title.= "Aceros Vimar - Productos";
				
		# Funciones de remplazo: header function    
		$this->header = array('function' => 'header');

		$this->content = array('function' => 'categorias');

		$this->readfiletemplate("base.html");
		
	}

	protected function categorias(){

		$c = (object) array();
		
		$c->static = $this->url_site."static/";
		        
        $c->url_site = $this->url_site;
        
        $c->title = "Title";


    	$pdo = new db_pdo();
 
 		# EJEMPLO DE SELECT MULTIPLE

		$pdo->add_consult("SELECT * FROM aceros_categoria");

		$query = $pdo->query();

		$c->rows = print_r($query[0],1);

		return $this->render("productos.html",true,$c);

	}



}