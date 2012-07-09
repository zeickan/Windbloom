<?php

/*
 * class forum
 */

class form  extends template  {
    
    /*
     * Constructor 
     * Funciones y variables globales del script
     */
    
    function __construct() {
	    
	session_start();
	
	global $windbloom;        
        
        $this->framework = $windbloom;
        
        # System URL
        
        # URL
        
        $this->url_site = $windbloom->sys['url'];
        
        $this->url_app = $this->url_site.$windbloom->application.'/';
        
        $this->self_file = alphanumeric($_GET['acc']).".html";    
        
        # Plantilla folder
        
        $this->template_path = "formato/";
        
        $this->template_url = $windbloom->sys['url'].'template/'.$this->template_path;
        
        $this->title = ':D';
	
	# Form vars template
	
	$this->msg_form = '';
	
        # GetHeader, GetCopyright, GetSidebar
        
        $this->header = array('function' => 'header');
        
        $this->copyright = array('function' => "copyright");
        
    }
    
    /* HEADER */
    
    protected function header(){
        
        $item[] = '<meta charset="UTF-8" />';        
        $item[] = '<title>'.$this->title.'</title>'."\n";
        
        $cssPath = "static/css/";
        $item[] = $this->AddStyleSheet("reset.css",$cssPath,'all');
        $item[] = $this->AddStyleSheet("form.css",$cssPath,'all');
	$item[] = $this->AddStyleSheet("jquery/datePicker.css",$cssPath,'screen');
	$item[] = $this->AddStyleSheet("datePicker.css",$cssPath,'screen');        
        
        $jsPath = "static/js/";  
        $item[] = $this->addJavaScript('jquery-1.5.2.js',$jsPath);        
        $item[] = $this->addJavaScript('date.js',$jsPath);        
        $item[] = $this->addJavaScript('jquery/jquery.datepicker.js',$jsPath);
        
        $specificDate = "$(document).ready(function() { $('.date-pick').datePicker({autoFocusNextInput: true} ); });";
        
        $item[] = $this->addJavaScript('','',$specificDate);

        return join("\n    ",$item);
        
    }
    
    /*
     * function main() 
     * El metodo principal y por defecto
     */
    
    public function main(){

    	# Insertamos un Hola mundo en {hello_world}
    	# Ejem: $this->STRING = {STRING}

    	# $this->hello_world = models::dex('Hola mundo');
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
     * Ejemplo de remplazo de funcion existente.
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
    
    /* FORMATO */
    
    protected function getFieldByType($type,$title = '',$special = false,$default = false){
        
        $name = 'name="'.$title.'" id="'.$title.'"';
        
        if( $type[0] == "tinytext" || $type[0] == "text" || $type[0] == "longtext" ){
            
            $field = '<textarea '.$name.'>'.$_POST[$title].'</textarea>';
            
        } else {
            
            $max = (! empty($type[1])  )?'maxlength="'.$type[1].'"':'';
            
            if( $type[0] == "int" ):
            
                $field = '<input '.$name.' type="number" size="5" class="mini-input onlyNumbers" '. $max .' />';
            
            elseif( $type[0] == "date" ):
            
                $field = '<input '.$name.' type="text" size="8" class="date-pick" value="'.$_POST[$title].'" readonly="readonly" />';
                
            elseif( $type[0] == "tinyint" ):
            
                $field = '<input '.$name.' type="checkbox" size="8" class="" value="1" />';
                
            elseif( $type[0] == "enum" ):
                
                for($i = 1; $i < count($type); $i++ ){
		    
		    if( $default == $type[$i] ):
			$checked = 'checked="checked"';
		    else:
			$checked = '';
		    endif;
		    
                    
                    $field.= '<input '.$name.' type="radio" size="8" value="'.$type[$i].'" '.$checked.' /> '.$type[$i];
                    
                }
               
            else:
                
                $field = '<input '.$name.' type="text" '. $max .' class="normal-input" value="'.$_POST[$title].'"  />';
                
            endif;
        
        }
        
        return $field;
        
    }
    
    protected function genFormByTable(){
	
	    $pdo = new db_pdo();
        
        # Tabla a armar

        $pdo->add_consult("DESC demo_contrato");
       
        $query = $pdo->query();

        $form = array();
        
        #echo"<pre>".print_r($query[0],1)."</pre>";
        
        $temp = array();
	
	$names = array( 'id' => 'ID',
		        'fecha' => 'Fecha',
			'responsableConvenio' => 'Responsable del convenio',
			'nombreComercio' => 'Nombre del comercio',
			'firmaComercio' => 'Quien firma en el contrato',
			'puestoComercio' => 'Puesto',
			'emailComercio' => 'Correo de contacto',
			'telefonoComercio' => 'Teléfono de contacto',
			'nombreRepresentante' => 'Nombre del representante',
			'puestoRepresentante' => 'Puesto del representante',
			'emailRepresentante' => 'Correo del representante',
			'telefonoRepresentante' => 'Teléfono del responsable',
			'fechaFirma' => 'Fecha en la que se firma',
			'fechaEfectivo' => 'Fecha cuando hacer efectivo el movimiento',
			'fechaVencimiento' => 'Fecha de vencimiento',
			'tipoBeneficio' => 'Tipo de beneficio',
			'inicioBeneficio' => 'Fecha en la que inicia',
			'venceBeneficio' => 'Fecha en la que vence',
			'consumoMinimo' => 'Consumo minimo',
			'tdu' => 'TDU+',
			'descripcion' => 'Descripción',
			'restricciones' => 'Restricciones',
			'programaAplica' => 'Programa en el que aplica',
			'nombreRecibe' => 'Nombre de quien recibe',
			'nombreAuth' => 'Nombre de autorización',
			'nombreValid' => 'Nombre de validacion de datos'
		    );
	
	$hidden = array( 'id' => true , 'user_id' => true , 'tdu' => true );
         
        foreach ($query[0] as $key => $value) {
            
            preg_match_all("@[A-Za-z0-9+]([A-Za-z0-9 ]+)@i",$value['Type'],$type);
            
            $title = $value['Field'];
	    
	    
            $diferent = array();
            
            if( $title == 'tdu'){
                $array = array( "tdu" => "TDU Basica" );
            } else {
                $array = NULL;
            }
            
	    $name = $names[$title]?$names[$title]:$title;
	    
	    if( $hidden[$title] ):
		#Hiiden Field
	    else:
	    
		$temp[] = array( 'name' => $name  , "box" => $this->getFieldByType($type[0],$title) );
	    
	    endif;            
            
        }
	
	$template = $this->getTemplate2Loop('form','loop');
	
	$rgex = array( "name", "box" );
	
	$part = $this->get_template_part($temp,$rgex,$template);
        
        return $part;
	
	
    }
    
    protected function save($table = 'demo_contrato'){	
	$pdo = new db_pdo();
	
	if( $_POST ):
	
	    $valid = true;
	    
	    session_start();
	    
	    $_POST['user_id'] = $_SESSION['user']['id'];
	
	    foreach($_POST as $key => $value){		
		if( empty($value) ){
		    $valid = false;		    
		    break;		    
		}		
	    }
	    
	    if( $valid ){	    
		if( $pdo->insert($table, $_POST ) ){    		    
		    return true;	    
		} else {
		    $this->error_msg = $pdo->error[2];		    
		    return false; 		
		}	    
	    } else {		
		$this->error_msg = 'Incomplete form';		
		return false;		
	    }	
	endif;
    }
    
    public function formato(){
	
	if( GET::ACTIONS()->action == "save" ):
	
	    if( $this->save('demo_contrato') ){
		
		$this->msg_form = 'Exito';
		
	    } else {
		
		$this->msg_form = $this->error_msg;
		
	    }
	
	endif;
	
	session_start();
	
	$userid = $_SESSION['user']['id'];
	
	$pdo = new db_pdo();
       
	$pdo->add_consult("SELECT id FROM demo_contrato WHERE user_id='$userid' LIMIT 1");

	$query = $pdo->query();
	
	if( $query[0][0]['id'] ):
	
	    $this->forma = 'Ya enviaste tu negocio.';
	    
	    # Cargar plantilla
	    
	    $this->readfiletemplate("error.form.html");
	    
	
	else:
	
	    $this->forma = $this->genFormByTable();
	    
	    # Cargar plantilla
	    
	    $this->readfiletemplate("index.form.html");
	    
	endif;
        
        
        
        
    }
    
    public function ahorro(){
	
	if( GET::ACTIONS()->action == "save" ):
	
	    if( $this->save('redAhorro') ){
		
		$this->msg_form = 'Exito';
		
	    } else {
		
		$this->msg_form = $this->error_msg;
		
	    }
	
	endif;
	
        $this->forma = $this->genFormByTable2();
        
        
        # Cargar plantilla
        
        $this->readfiletemplate("index.form.html");
        
    }
    
    protected function genFormByTable2(){
	
	$pdo = new db_pdo();
        
        # Tabla a armar

        $pdo->add_consult("DESC redAhorro");        
       
        $query = $pdo->query();
	
        $form = array();
        
        #echo"<pre>".print_r($query[0],1)."</pre>";
        
        $temp = array();
	
	$names = array( 'id' => 'ID',
		        'fecha' => 'Fecha',
			'responsableConvenio' => 'Responsable del convenio',
			'nombreComercio' => 'Nombre del comercio',
			'firmaComercio' => 'Quien firma en el contrato',
			'puestoComercio' => 'Puesto',
			'emailComercio' => 'Correo de contacto',
			'telefonoComercio' => 'Teléfono de contacto',
			'nombreRepresentante' => 'Nombre del representante',
			'puestoRepresentante' => 'Puesto del representante',
			'emailRepresentante' => 'Correo del representante',
			'telefonoRepresentante' => 'Teléfono del responsable',
			'fechaFirma' => 'Fecha en la que se firma',
			'fechaEfectivo' => 'Fecha cuando hacer efectivo el movimiento',
			'fechaVencimiento' => 'Fecha de vencimiento',
			'tipoBeneficio' => 'Tipo de beneficio',
			'inicioBeneficio' => 'Fecha en la que inicia',
			'venceBeneficio' => 'Fecha en la que vence',
			'consumoMinimo' => 'Consumo minimo',
			'tdu' => 'TDU+',
			'descripcion' => 'Descripción',
			'restricciones' => 'Restricciones',
			'programaAplica' => 'Programa en el que aplica',
			'nombreRecibe' => 'Nombre de quien recibe',
			'nombreAuth' => 'Nombre de autorización',
			'nombreValid' => 'Nombre de validacion de datos',
			
			'razonSocial' => 'Razón social',
			'rfc' => 'R.F.C.',
			'movimiento' => 'Movimiento',
			'giro' => 'Giro',
			'tipo' => 'Tipo',
			'ubicacionLogo' => 'Ubicación del logo',
			'web' => 'Página web',
			'calificacion' => 'Calificación',
			'clasificacionEdad' => 'Clasificación por edad',
			'sexo' => 'Sexo',
			'edad' => 'Edad',
			'cuentaPromedio' => 'Cuenta promedio',
			'fechaEntrega' => 'Fecha de entrega',
			'medios' => 'Medios',
			'descuentoEfectivo' => 'Descuentos en efectivo',
			'descuentoTC' => 'Descuentos en T.C',
			'descripcionBeneficio' => 'Descripción del beneficio',			
			'restriccionBeneficio' => 'Restricciones',
			'responsableNombre' => 'Responsable del convenio (TDU)',
			'quienFirma' => 'Quien firma en el contrato',
			'puestoFirma' => 'Puesto',
			'emailFirma' => 'Correo electrónico',
			'telefonoFirma' => 'Teléfono de contacto',
			'nombreLegal' => 'Nombre del representante legal y/o quien autoriza el convenio',
			'puestoLegal' => 'Puesto',
			'emailLegal' => 'Email',
			'telefonoLegal' => 'Teléfono de quien autoriza',
			'fechaConvenio' => 'Fecha que se firmo el contrato',
			'fechaInicia' => 'A partir de cuando hacer efectivo el movimiento',
			'fechaVence' => 'Fecha de vencimiento',
		
			'' => ''
			
		    );
	
	$hidden = array( 'id' => true );
         
        foreach ($query[0] as $key => $value) {
            
            preg_match_all("@[A-Za-z0-9+]([A-Za-z0-9 ]+)@i",$value['Type'],$type);
            
            $title = $value['Field'];
	    
	    $default = $value['Default'];
            
            
            $diferent = array();
            
            if( $title == 'tdu'){
                $array = array( "tdu" => "TDU Basica" );
            } else {
                $array = NULL;
            }
            
	    $name = $names[$title]?$names[$title]:$title;
	    
	    if( $hidden[$title] ):
		#Hiiden Field
	    else:
	    
		$temp[] = array( 'name' => $name  , "box" => $this->getFieldByType($type[0],$title,false, $default) );
	    
	    endif;            
            
        }
	
	$template = $this->getTemplate2Loop('form','loop');
	
	$rgex = array( "name", "box" );
	
	$part = $this->get_template_part($temp,$rgex,$template);
        
        return $part;
	
	
    }
    
}

