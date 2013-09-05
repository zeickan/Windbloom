<?php

/*
 * class models
 */

class models extends template {
    
    /*
     * __construct()
     * @param 
     */
    
    function __construct() {
        
	
        
        
    }
    
    public function sendMessage($title,$msg,$to,$togroup = null,$activity = false,$rel = null){
	
	$pdo = new db_pdo();
	
	$array = array( "title" => $title,
		        "message" => $msg,
			"message_to" => $to,
			"rel_id" => $togroup,
			"activity" => $activity,
			"client_id" => $rel
			);
	
	if( $pdo->insert("messages",$array) ){
	    
	    return true;
	    
	} else {
	    
	    return false;
	    
	}
	
    }
    
    public function loadmessages(){
	
	global $windbloom;
        
        $this->url_site = $windbloom->sys['url'];
    
	$cssPath = "static/stylesheets/";
	$item[] = $this->AddStyleSheet("jquery.ambiance.css",$cssPath,'screen');
	
	
        $jsPath = "static/javascript/";
        $item[] = $this->addJavaScript('jquery.ambiance.js',$jsPath);
	
	$pdo = new db_pdo();
	
	$app = alphanumeric($_GET['app']);
	
	$pdo->add_consult("SELECT * FROM messages WHERE activity='$app' GROUP BY client_id");
	
	$query = $pdo->query();
	
	//print_r($query);
	if( $query[0] ){
	    
	$item[] = '<script type="text/javascript">';
	$item[] = '$(document).ready(function() {';
	    
	    foreach($query[0] as $row){
		
		if( $row["client_id"] ){
		
		    $msg = '<a href="'.$this->url_site.$app.'/editar@d='.$row["client_id"].'">'.$row['title'].'</a><small><br>'.$row['message'].'</small>';    
		    
		} else {
		    
		    $msg = '<span>'.$row['title'].'</span><small><br>'.$row['message'].'</small>';
		    
		}
		
	      
	      $item[] = '$.ambiance({message: \''.$msg.'\', width: 350, timeout: 5});';
	      
	    }
	$item[] = '});	</script>';
	
	}

	return join("\n    ",$item);
	
    }
    
    public function Masks(){
        
        $pdo = new db_pdo();       
    
        $pdo->add_consult("SELECT * FROM masks");
        
        $query = $pdo->query();
        
        return $query[0];
        
    }
    
    public function Groups(){
        
        $pdo = new db_pdo();       
    
        $pdo->add_consult("SELECT * FROM users_groups");
        
        $query = $pdo->query();
        
        return $query[0];
        
    }
    
    
    public function Dictionary($dictionary, $inherit = null ){
		
		if( !is_null($inherit) ):
			
			$condition = " WHERE inherit='$inherit' ";
			
		endif;
        
        $pdo = new db_pdo();       
    
        $pdo->add_consult("SELECT * FROM  dictionary_".$dictionary.$condition);
        
        $query = $pdo->query();
        
        return $query[0];
        
    }
    
    /*
     * function requieredFields
     * @param $array,$needle
     */
    
    function requieredFields($array,$needle){
        
        $return = true;        
        if($needle){                        
            foreach($needle as $key ){                
                if( $array[$key] ){
                    # Run run
                } else {
                    $return = false;
                }                
            }            
        }        
        return $return;        
    }
    
    
    public function genmsg($msg , $type = 'success'){
        
        switch($type){
            
            case"success":
                $msg = '<div class="msgHecho">'.$msg.'</div>';
                
            break;
                
            case"warning":
                $msg = '<div class="msgAdvertencia">'.$msg.'</div>';
                
            break;
        
            case"error":
                $msg = '<div class="msgError">'.$msg.'</div>';
                
            break;
            
        }
    
    
        return $msg;
    
    
    }
    
    protected function header(){

    	/* Armamos el HEADER DEFAULT */

    	$item[] = parent::header();	
		$cssPath = "static/stylesheets/";
		$item[] = $this->AddStyleSheet("principal.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("colorbox.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("themes/red.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("themes/red.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("main.css",$cssPath,'screen');        
	
	
        $jsPath = "static/javascript/";
        $item[] = $this->addJavaScript('fecha.js',$jsPath);
		$item[] = $this->addJavaScript('redireccionar.js',$jsPath);        
        $item[] = $this->addJavaScript('jquery-1.8.0.min.js',$jsPath);
        $item[] = $this->addJavaScript('colorbox.js',$jsPath);
        $item[] = $this->addJavaScript('datepicker.js',$jsPath);
        $item[] = $this->addJavaScript('zqdatagrid.js',$jsPath);
        

        return join("\n    ",$item);
	    
    }
    
    
    protected function nav(){	
		$nav = new Nav();	
		return $nav->load();	
    }
    
    
    
    
    protected function lista($ver = false){
		
		if( $_GET['acc'] == "edit" || $ver ):
		
			$id = numeric($_GET['id']);
		
			$pdo = new db_pdo();
			
			$pdo->add_consult("SELECT
								dictionary_bank.description AS banco,
								dictionary_provider.description AS proveedor,
								dictionary_rank.description AS rango,
								DATE_FORMAT(prospectos.fecha_atencion,'%d/%m/%Y') AS fecha,
								dictionary_point.description AS punto,
								prospectos.punto_prospectacion_value AS punto_valor,
								CONCAT( prospectos.nombre,' ',prospectos.apellido_pa,' ',prospectos.apellido_ma) AS nombre_completo,
								dictionary_idnumber.description AS clave,
								prospectos.clave_id_value AS clave_valor,
                                prospectos.puntos,
								dictionary_employ.description AS empleo,
								prospectos.empleo_value AS empleo_valor,
								prospectos.empleo_unidad AS empleo_unidad,
								prospectos.alta,
								prospectos.tel_oficina,
								prospectos.tel_celular,
								prospectos.tel_casa,
								dictionary_marital_status.description AS estado_civil,
								CONCAT( prospectos.conyuge,' ', prospectos.conyuge_pa,' ',prospectos.conyuge_ma) AS conyuge_completo
								FROM
								prospectos
								Inner Join dictionary_bank ON prospectos.banco_id = dictionary_bank.id
								Inner Join dictionary_provider ON prospectos.provedor_id = dictionary_provider.id
								Inner Join dictionary_rank ON prospectos.titulo_id = dictionary_rank.id
								Inner Join dictionary_point ON prospectos.punto_prospectacion = dictionary_point.id
								Inner Join dictionary_idnumber ON prospectos.clave_id = dictionary_idnumber.id
								Inner Join dictionary_employ ON prospectos.empleo_id = dictionary_employ.id
								Inner Join dictionary_marital_status ON prospectos.estado_civil = dictionary_marital_status.id
								WHERE prospectos.id=$id
								");
			
			$sql = $pdo->query();
			$row = $sql[0][0];
		
			// Create form(Name & ID) <form>
			
			$form = new Form("VistaContacto".$iden_edit);        
			// Legenda para el formulario
			$form->legend = 'Datos del prospecto';        
			//$form->newline = '<br><br>';        
			$form->coverField = array("<div>","</div>");
			
			if( $row ){
				
				$dic = array( "banco" => "Financiera",
							  "proveedor" => "Punto de prospección",
							  "rango" => "Grado / Titulo",
							  "fecha" => "Fecha",
							  "punto" => "Punto de prospección",
							  "punto_valor" => "Punto",
							  "nombre_completo" => "Nombre completo",
							  "clave" => "Clave ID",
							  "clave_valor" => "Clave",
							  "empleo" => "Datos del Empleo",
							  "empleo_valor" => "Datos del Empleo",
							  "empleo_unidad" => "",
							  "alta" => "Alta en el empleo",
							  "puntos" => "Puntos",
							  "tel_oficina" => "Teléfono oficina",
							  "tel_celular" => "Teléfono celular",
							  "tel_casa" => "Teléfono casa",
							  "estado_civil" => "Estado civil",
							  "conyuge_completo" => "Conyuge / Concubina"
							  );
			
				foreach($row as $key => $value){
					
					
					if( trim($value) ):
						$label = $dic[$key];
						$value = utf8_encode($value);
						$form->addField(new FormSeparator("<label>$label</label> $value","small"));
					endif;
					
				}
			}
		
			$r = $form->buildForm();
			
			return $r;
	
		endif;
        
    }
    
}

class WbForm {
    
    public function buildSelectOptions($tmp, $id = null,$add = null){
        
        if( $tmp ){
            
            if( !is_null($add) ){
                
                foreach($add as $k => $v){
                    
                    $masks[] = array("value" => $k, 
                                     "name" =>  $v, 
                                     "selected" => false );
                    
                }  
                
            }
            
            foreach($tmp as $k => $v){
                
                if($v['id']==$id):  $selected = true;
                else:               $selected = false;
                endif;
                
                $masks[] = array("value" => $v['id'],
                           "name" => utf8_encode($v['description']),
                           "selected" => $selected);
                
            }            
        }
        
        return $masks;
        
    }
    
}

class Nav extends models {
	
    public function __construct(){
            
        global $windbloom;
        
        $this->url_site = $windbloom->sys['url'];

        $this->framework = $windbloom;
        
        $this->template_path = "loop/";
        
        $this->build();
    }
	
	protected function filter(){
		
		session_start();
		
		if( $_SESSION['username']['group_id'] != 1 ){
			
			$id = numeric($_SESSION['username']['group_id']);
			
			return "WHERE mask='$id'";
			
		}
		
		
	}
    
    protected function build(){
		
        $pdo = new db_pdo();
		    
        $pdo->add_consult( "SELECT * FROM activities ".$this->filter() );
        
        $query = $pdo->query();
        
        if($query[0]):
        
            $menu = array();
        
            foreach($query[0] as $row){
                    
                if( $row['rel'] == '0' ):
                
                        $menu[$row['id']] = $row;
                
                else:
                
                        $menu[$row['rel']]['sub'][] = $row;
                
                endif;
                    
            }
            
        endif;
        
        $this->menu = $menu;
        
        return $menu;	
        
    }
    
    
    protected function BuildList(){
            
        $template = $this->getTemplate2Loop('nav','loop');
        
        $rgex = array( "meta_key", "meta_value", "id" );
        
        if($this->menu):		
            while( list($k,$v) = each($this->menu) ){
                    
                $link = $this->url_site.''.$v['meta_value'].'/';
                
                $this->menu[$k]['meta_value'] = $link;
                
            }
        endif;

        return $this->get_template_part($this->menu,$rgex,$template);
        
    }
    
    public function load(){	
        
        return $this->BuildList();
        
    }
	
}
