<?php

/*
 * class main 
 */

class main extends models {
    
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
		$item[] = $this->AddStyleSheet("principal.css",$cssPath,'screen');

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
		$this->title.= "Aceros";
				
		# Funciones de remplazo: header function    
		$this->header = array('function' => 'header');

		$this->content = array('function' => 'content');

		$this->readfiletemplate("base.html");
		
	}


	function productos(){
		echo"xD";
	}
	

	protected function content(){

		$c = (object) array();
		
		$c->static = $this->url_site."static/";

		$c->url_site = $this->url_site;

		$c->title = "Title";

		return $this->render("contenido.html",true,$c);

	}

}

