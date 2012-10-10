<?php

/*
 * class forum
 */

class medic extends template  {
    
    /*
     * Constructor 
     * Funciones y variables globales del script
     */
    
    function __construct() {
	    
	global $user;
        
        global $windbloom;

        $this->framework = $windbloom;
        
        $this->msg = '';
        
        $this->template_path = "tdu/";
        
        $this->template_url = $windbloom->sys['url'].'template/'.$this->template_path;
        
        $this->url_site = $windbloom->sys['url'];

        $this->user = $user;

        $this->username = 'Administrador';

        $this->title = TITLE;


        # Funciones de remplazo
        
        $this->header = array('function' => 'header');
        
        $this->copyright = array('function' => "copyright");
        
    }
    

    /*
     * function main() 
     * El metodo principal y por defecto
     */
    
    public function main(){
	
	$pdo = new db_pdo();
        
        $pdo->add_consult("SELECT DISTINCT estado FROM redMedica");
        $pdo->add_consult("SELECT DISTINCT colonia FROM redMedica");
        $pdo->add_consult("SELECT DISTINCT tipo FROM redMedica");
        
        #$pdo->add_consult("SELECT * FROM tdu_client");
        
        $query = $pdo->query();
    
        $this->states = HTML::OPTION($query[0],'estado','estado');
        
        $this->country = HTML::OPTION($query[1],'colonia','colonia');
        
        $this->types = HTML::OPTION($query[2],'tipo','tipo');

        $this->readfiletemplate("medic.html"); 
	
    }
    
    public function medicos(){
        
        if( $_POST ):
        
        $pdo = new db_pdo;
        
        $w = array();
        
        $w[] = ($_POST[estado])?"estado='".alphanumeric($_POST[estado])."'":false;
        $w[] = ($_POST[colonia])?"colonia='".alphanumeric($_POST[colonia],"çƒêîò‡Ž’—œ. _-/()")."'":false;        
        $w[] = ($_POST[giro])?"tipo='".alphanumeric($_POST[giro],"çƒêîò‡Ž’—œ. _-/()")."'":false;
        
        $where = array();
        
        
        foreach($w as $key => $val){
            
            if( $val ){
                
                $where[] = $val;
                
            } else{
                unset($w[$key]);
            }
        }
        
        if( count($where) >= 1 ){
            
            $sql = "WHERE ".join(" AND ", $where);
            
        }
  
   
        $pdo->add_consult("SELECT * FROM redMedica $sql");
       
        $query = $pdo->query();
        
        $this->query = $query[0];
        
        $this->results = array( "function" => "results" );
        
        $this->readfiletemplate("buscar.html"); 
        
        endif;
    }
    
    
    protected function results(){
        
        $query = $this->query;
        
        $result = array(); 

        if( $query ):
        
        while( list($k,$v) = each($query) ){
            
            $tmp = '';
            
            $img = ($v[image])?$this->framework->sys['url']."uploads/".$v[image]:$this->framework->sys['url']."static/images/notfound.png";
            
            $tmp.= HTML::TAG(NULL,"img", array( "src" => $img , "border" => '0', "width" => '200'), false );
            
            $tmp.= HTML::TAG( htmlentities($v[sucursal]) ,"h1");
            
            $tmp.= HTML::TAG( htmlentities($v[direccion]),"small");
            
            $tmp.= HTML::TAG( htmlentities($v[colonia]),"small");
            
            $tmp.= HTML::TAG( '',"br",NULL, false );            
            
            $tmp.= HTML::TAG( htmlentities($v[poblado]),"small"); $tmp.= HTML::TAG( htmlentities($v[estado]),"small");
            
            $tmp.= HTML::TAG( '',"br",NULL, false );      
            
            $dir = str_replace( strstr( strtoupper($v[direccion]) ,"CONSULTORIO"), "",$v[direccion]);
            
            $tmp.= HTML::TAG(
                                HTML::TAG( 'Ver mapa' ,"a", array( 'href' => 'https://maps.google.com.mx/?q='.urlencode($dir.', '.$v[colonia].', '.$v[estado]) , 'target' => "_blank" ) ),
                                "span"
                            );

        
            $result[] = HTML::TAG($tmp,"div", array("class" => "box") );
            
        }
        
        return join( HTML::TAG('','div',array("class"=>'clear'))."\n" ,$result);

        endif;
        
    }
    
    
    
    
    
}

