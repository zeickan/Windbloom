<?php

/*
 * class TDU
 */

class tdu extends template {
    
    /*
     * __construct()
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
    
    
    public function main(){
        
        $pdo = new db_pdo();
        
        $pdo->add_consult("SELECT * FROM tdu_state");
        $pdo->add_consult("SELECT * FROM tdu_type");
        
        #$pdo->add_consult("SELECT * FROM tdu_client");
        
        $query = $pdo->query();
    
        $this->states = HTML::OPTION($query[0],'id','meta_key');
        
        $this->types = HTML::OPTION($query[1],'id','meta_key');
        
        $this->readfiletemplate("index.html"); 
        
    }
    
    public function buscar(){
        
        if( $_POST ):
        
        $pdo = new db_pdo;
        
        $w = array();
        
        $w[] = ($_POST[estado])?"tdu_state='".numeric($_POST[estado])."'":false;
        $w[] = ($_POST[ciudad])?"tdu_city='".numeric($_POST[ciudad])."'":false;
        $w[] = ($_POST[municipio])?"tdu_address='".numeric($_POST[municipio])."'":false;
        $w[] = ($_POST[giro])?"tdu_type='".numeric($_POST[giro])."'":false;
        $w[] = ($_POST[local])?"title LIKE '%".alphanumeric($_POST[local])."%'":false;

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
  
   
        $pdo->add_consult("SELECT tdu_client.id, 
                                    tdu_client.title, 
                                    tdu_client.slug, 
                                    tdu_client.image, 
                                    tdu_client.tdu_state, 
                                    tdu_state.meta_key, 
                                    tdu_client.tdu_city, 
                                    tdu_city.meta_key, 
                                    tdu_client.tdu_address, 
                                    tdu_address.meta_key
                            FROM tdu_client INNER JOIN tdu_state ON tdu_client.tdu_state = tdu_state.id
                                     INNER JOIN tdu_city ON tdu_client.tdu_city = tdu_city.id
                                     INNER JOIN tdu_address ON tdu_client.tdu_address = tdu_address.id
                            $sql
                            ORDER BY tdu_client.id ASC");
       
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
            
            $tmp.= HTML::TAG( HTML::TAG( htmlentities($v[title]),"a",array( 'href' => 'perfil.html@ficha='.$v[slug].'&id='.$v[id] ) ) ,"h1");
            
            $tmp.= HTML::TAG( htmlentities($v[state]),"small");
            
            $tmp.= HTML::TAG( htmlentities($v[city]),"small");
            
            $tmp.= HTML::TAG( htmlentities($v[address]) ,"small");
            
            if( $_GET['id'] ):
            
                $tmp.= HTML::TAG( html_entity_decode($v['beneficio']) ,"p");
                
                $tmp.= HTML::TAG( html_entity_decode($v['beneficiodesc']) ,"p");
                
                $tmp.= HTML::TAG( html_entity_decode($v['field31']) ,"p");
                
                $tmp.= HTML::TAG( model::decode_suc($v['tdu_suc']) ,"p");
            
            endif;
            
            $result[] = HTML::TAG($tmp,"div", array("class" => "box") );
            
        }
        
        return join( HTML::TAG('','div',array("class"=>'clear'))."\n" ,$result);

        endif;
        
    }
    
    public function perfil(){
        
        if( $_GET['id'] ):
        
        $pdo = new db_pdo;
        
        $id = numeric($_GET[id]);
        
        $sql = "WHERE tdu_client.id='$id'";
        
        $pdo->add_consult("SELECT tdu_client.id, 
                                            tdu_client.title, 
                                            tdu_client.slug, 
                                            tdu_client.image, 
                                            tdu_state.meta_key AS state,  
                                            tdu_city.meta_key AS city,
                                            tdu_address.meta_key AS address ,
                                            tdu_client.beneficio, 
                                            tdu_client.beneficiodesc, 
                                            tdu_client.field31, 
                                            tdu_client.tdu_suc
                            
                            FROM tdu_client INNER JOIN tdu_state ON tdu_client.tdu_state = tdu_state.id
                                             INNER JOIN tdu_city ON tdu_client.tdu_city = tdu_city.id
                                             INNER JOIN tdu_address ON tdu_client.tdu_address = tdu_address.id
                            $sql
                            ORDER BY tdu_client.id ASC");
       
        $query = $pdo->query();
        
        $this->query = $query[0];
        
        $this->results = array( "function" => "results" );
        
        $this->readfiletemplate("perfil.html");
        
        endif;
        
    }
    
    protected function result(){
        
        echo"alskjdh";
        
        $query = $this->query;
        
        echo"<pre>".print_r($query,1)."</pre>";
    }
    
    

}