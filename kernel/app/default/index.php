<?php

/*
 * class main 
 */

class main  extends models {
    
    /*
     * __construct()
     * @param $arg
     */
    
    function __construct() {	
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

		$this->readfiletemplate("index.html");
		
	}
	

	protected function content(){

		$c = (object) array();
		
		$c->total = (string) "Total";
		
		$c->asignados = (string) "Hello world!";		
		        
        $c->url_site = $this->url_site;
        
        $c->title = "Title";

		return $this->render("contenido.html",true,$c);

	}

	protected function mainpage(){
		
		session_start();
        
        $id_user = numeric($_SESSION['username']['id']);
		
		$pdo = new db_pdo();
		
		$pdo->add_consult("SELECT id FROM prospectos WHERE author_id=$id_user");
		
		$pdo->add_consult("SELECT id FROM prospectos WHERE author_id=$id_user AND status='2'");
		
		
		$query = $pdo->numRows();
		
		
        $c = (object) array();
		
		$c->total = (string) $query[0][0];
		
		$c->asignados = (string) $query[1][0];	
		
		        
        $c->url_site = $this->url_site;
        
        $c->title = "Verificar datos del prospecto ".$_S;
        
        return $this->render("plantilla_A.html",true,$c);
		
	}
		
		
	protected function nav(){	
		$nav = new Nav();	
		return $nav->load();	
    }
    
}

