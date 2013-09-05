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
        
        session_start();
    	
    	global $user;
        
        global $windbloom;
        
        # Configuraci—n del Framework
        
        $this->framework = $windbloom;
        
        $this->user = $user;
        
        # Variables para la plantilla
        
	$this->template_path = "viviendafacil/";
        
        $this->msg = '';
        
        $this->url_site = $windbloom->sys['url'];
        
        $this->url_app = $this->url_site.'';
        
        $this->self_file = alphanumeric($_GET['acc']).".html";
        
        # Textos para la plantilla
        
        $this->title = "Vivienda Facil";

$this->messages = array('function' => 'loadmessages');
	
    
        
        # GetHeader, GetCopyright, GetSidebar        
	
		// Validamos el Login en cada pagina de esta APP
			session_start();
		
		if( !isset($_SESSION['username']) && !empty($_GET['acc'])  ){
			
			Request::HttpRedirect( $this->url_site );
			
		} else {
			
			$this->username = $_SESSION['username']['profile']['name'].' '.$_SESSION['username']['profile']['last_name'];
			
			$this->url_logout = $this->url_site.'accounts/logout';
			
		}
	
    }
    
    protected function header(){

    	/* Armamos el HEADER DEFAULT */

    	$item[] = parent::header();
	
		$cssPath = "static/stylesheets/";
		$item[] = $this->AddStyleSheet("principal.css",$cssPath,'screen');
	
	
        $jsPath = "static/javascript/";
        $item[] = $this->addJavaScript('fecha.js',$jsPath);
		$item[] = $this->addJavaScript('redireccionar.js',$jsPath);   

		return join("\n    ",$item);
	    
    }
    
    
    public function main(){
	
		if( !isset($_SESSION['username']) ){
	
			if( $_POST ){		
			$user = alphanumeric($_POST['user'],"_\-.\@");		
			$pass = md5( $this->framework->sys['crypt'] . $_POST['pass']);		
			Request::HttpRedirect( $this->url_site."accounts/login@",array('user'=>$user,'pass'=>$pass),'');		
			}
			
			
			
			# Titulo adicional
			$this->title.= " - Login.";    
			# Funciones de remplazo: header function    
			$this->header = array('function' => 'header');    
			//$this->body = array('function' => 'mainpage');    
			$this->readfiletemplate("login.html");
			
		} else {
			
			$this->dashboard();
			
		}
        
    }
    
    /*
     * function dashboard
     */
    
    function dashboard() {
	
		# Titulo adicional
		$this->title.= " - DashBoard";
		
		$this->nav = array('function' => 'nav');
		
		# Funciones de remplazo: header function    
		$this->header = array('function' => 'header');
		
		
		
		
		$this->body = array('function' => 'mainpage');    
		
		$this->readfiletemplate("system.html");
		
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

