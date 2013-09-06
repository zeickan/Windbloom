<?php

/*
 * class main 
 */

class operador extends models {
    
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
        
        $this->msg = '';
        
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
		
		
		if( $_GET['edit'] ){
            
            Request::HttpRedirect( $this->url_site."operador/edit",array('id'=>numeric($_GET['edit']) ) );	
            
        }
		
		if( $_GET['view'] ){
            
            Request::HttpRedirect( $this->url_site."operador/view",array('id'=>numeric($_GET['view']) ) );	
            
        }
		
		if( $_GET['view2'] ){
            
            Request::HttpRedirect( $this->url_site."coordinador/view",array('id'=>numeric($_GET['view2']) ) );	
            
        }
	
    }
    
    protected function header(){

    	/* Armamos el HEADER DEFAULT */

    	$item[] = parent::header();	
		$cssPath = "static/stylesheets/";
		$item[] = $this->AddStyleSheet("principal.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("colorbox.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("themes/red.css",$cssPath,'screen');        
	
	
        $jsPath = "static/javascript/";
        $item[] = $this->addJavaScript('fecha.js',$jsPath);
		$item[] = $this->addJavaScript('redireccionar.js',$jsPath);        
        $item[] = $this->addJavaScript('jquery-1.8.0.min.js',$jsPath);
        $item[] = $this->addJavaScript('colorbox.js',$jsPath);
        $item[] = $this->addJavaScript('datepicker.js',$jsPath);
        $item[] = $this->addJavaScript('zqdatagrid.js',$jsPath);
        

	return join("\n    ",$item);
	    
    }
    
    public function main(){
		
		if( !isset($_SESSION['username']) ){
	
			if( $_POST ){		
			$user = alphanumeric($_POST['user'],"_-.\@");		
			$pass = md5( $this->framework->sys['crypt'] . $_POST['pass']);		
			Request::HttpRedirect( $this->url_site."accounts/login/@",array('user'=>$user,'pass'=>$pass),'');		
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
    
	
	/* VISTAS */    
    
    /*
     * function dashboard
     */
    
    public function dashboard() {
	
		# Titulo adicional
		$this->title.= " - Prospectador";
		
		$this->nav = array('function' => 'nav');
			
		$this->header = array('function' => 'header');
		
		$this->body = array('function' => 'mainpage');
		
		//$this->body = array('function' => 'mainpage');    
		$this->readfiletemplate("system.html");
	
    }
	
	/*
	 * function grid
	 */
	
	public function grid() {
	
		# Titulo adicional
		$this->title.= " - Historial";
		
		$this->nav = array('function' => 'nav');
			
		$this->header = array('function' => 'header');
		
		$this->body = array('function' => 'grido');
		
		$this->readfiletemplate("system.html");
	
    }    
	
	
	/*
	 * function edit
	 * @access public
	 */
	
	public function edit(){
		
		# Titulo adicional
		$this->title.= " - Editar";
		
		$this->nav = array('function' => 'nav');
			
		$this->header = array('function' => 'header');
		
		$this->body = array('function' => 'edito');
		
		$this->readfiletemplate("system.html");
		
	}
	
	/*
	 * function view
	 * @access public
	 */
	
	public function view(){
		
		# Titulo adicional
		$this->title.= " - Vista completa";
		
		$this->nav = array('function' => 'nav');
			
		$this->header = array('function' => 'header');
		
		$this->body = array('function' => 'ver');
		
		$this->readfiletemplate("system.html");
		
	}
	
	public function view2(){
		
		$this->view();
		
	}
	
	protected function ver(){      
       
        $c = (object) array();
		
		$this->internal_js = '';
        
        $c->url_site = $this->url_site;
		
		$c->selected_all = '';
        
        $c->title = "Vista del prospecto ".$_S;
        
        $c->msg = ($this->msg)?$this->msg:'';
        
		$this->ver = true;
		
        $c->form = array( 'function' => 'lista', "param" => array("ver" => true) );
		
		if( $_GET['acc'] == "view" ){
			
			$file = "main";
                
		} else {
			
			$file = "ver";
		}
        return $this->render($this->framework->application."/$file.html",true,$c);

    }
	
	/*
	 * function cordinador
	 * @access public
	 */
	
	public function asignados(){
		
		# Titulo adicional
		$this->title.= " - Historial";
		
		$this->nav = array('function' => 'nav');
			
		$this->header = array('function' => 'header');
		
		$this->body = array('function' => 'listaAsignados');
		
		$this->readfiletemplate("system.html");
		
	}
	
	/*
	 * function asignar
	 */
	
	public function listaAsignados() {
		
		session_start();
        
        $id_user = numeric($_SESSION['username']['id']);
		
		$c = (object) array();
        
        $c->url_site = $this->url_site;
		
		$c->activity = alphanumeric($_GET["app"]);
        
        $c->title = "Coordinador";
		
		$pdo = new db_pdo();
		
		$pdo->add_consult("SELECT id FROM prospectos WHERE asignador_id=$id_user ");
		
		$rows = $pdo->numRows();     
		
		$c->pendiente = ( $rows[0][0] >= 1 )?'zdatagrid();':'nomore();';
	
        return $this->render("operador/asignados.html",true,$c);
		
	}
	
	
	/*
	 * function cordinador
	 * @access public
	 */
	
	public function cordinador(){
		
		# Titulo adicional
		$this->title.= " - Gerente de ventas";
		
		$this->nav = array('function' => 'nav');
			
		$this->header = array('function' => 'header');
		
		$this->body = array('function' => 'asignar');
		
		$this->readfiletemplate("system.html");
		
	}
	
	
	
	
	/* WIDGETS */
	
    
    protected function nav(){	
		$nav = new Nav();	
		return $nav->load();	
    }
    
    
    protected function mainpage(){
        
        if( $_POST ){
			
			$pdo = new db_pdo();
			
			if( $pdo->insert("prospectos",
					array("banco_id" => alphanumeric($_POST["bank"],".-_ ,"),
					      "provedor_id" => alphanumeric($_POST["provider"],".-_ ,"),
					      "titulo_id" => alphanumeric($_POST["rank"],".-_ ,"),
					      "producto_bancario" => alphanumeric(0,".-_ ,"),
					      "punto_prospectacion" => alphanumeric($_POST["point"],".-_ ,"),
					      "punto_prospectacion_value" => alphanumeric($_POST["point_value"],".-_ ,"),
					      "nombre" => alphanumeric($_POST["name"],".-_ ,"),
					      "apellido_pa" => alphanumeric($_POST["last_name"],".-_ ,"),
					      "apellido_ma" => alphanumeric($_POST["mother_lastname"],".-_ ,"),
					      "clave_id" => alphanumeric($_POST["idnumber"],".-_ ,"),
					      "clave_id_value" => alphanumeric($_POST["idnumber_value"],".-_ ,"),
					      "empleo_id" => alphanumeric($_POST["employments"],".-_ ,"),
					      "empleo_value" => alphanumeric($_POST["employments_value"],".-_ ,"),
					      "empleo_unidad" => alphanumeric(0,".-_ ,"),
					      "alta" => alphanumeric($_POST["alta"],".-_ ,"),
					      "puntos" => alphanumeric($_POST["puntos"],".-_ ,"),
					      "tel_oficina" => alphanumeric($_POST["oficina"],".-_ ,"),
					      "tel_celular" => alphanumeric($_POST["cel"],".-_ ,"),
					      "tel_casa" => alphanumeric($_POST["casa"],".-_ ,"),
					      "estado_civil" => alphanumeric($_POST["marital"],".-_ ,"),
					      "conyuge" => alphanumeric($_POST["name_relatioship"],".-_ ,"),
					      "conyuge_pa" => alphanumeric($_POST["last_name_relatioship"],".-_ ,"),
					      "conyuge_ma" => alphanumeric($_POST["mother_lastname_relatioship"],".-_ ,"),
					      "author_id" => numeric($_SESSION['username']['id']),
					      "plaza_interesado" => alphanumeric($_POST['plaza']),
					      "sexo" => alphanumeric($_POST['sexo']),
					      "com_vendedor" => $_POST['com_vendedor']
					      )
				       )
			){
			    
			    $this->sendMessage("Asigname","Prospecto para asignación",null,4,"gerente");
    
			    $this->msg = $this->genmsg("Prospecto enviado al gerente de ventas para asignación ","success");
			    
			} else {
			
				$this->msg = $this->genmsg("ERROR: ". $pdo->error[2],"error");
			
			}
            
        }
       
        $c = (object) array();
		
		$c->internal_js = '';
        
        $c->url_site = $this->url_site;
        
        $c->title = "Registro de prospectos ".$_S;
        
        $c->msg = ($this->msg)?$this->msg:'';
        
		$c->selected_all = '.attr("selected","selected")';
		
        $c->form = array( 'function' => 'capturar' );
                
        return $this->render("operador/main.html",true,$c);

    }
	
	protected function edito(){
        
        if( $_POST ){
			
			$pdo = new db_pdo();	
            
        }
       
        $c = (object) array();
		
		$this->internal_js = '';
        
        $c->url_site = $this->url_site;
		
		$c->selected_all = '';
        
        $c->title = "Edición de prospecto ".$_S;
        
        $c->msg = ($this->msg)?$this->msg:'';
        
        $c->form = array( 'function' => 'capturar' );
                
        return $this->render("operador/main.html",true,$c);

    }
	
    
	
    
    protected function capturar($ver = false){
		
		if( $_GET['acc'] == "edit" || $ver ):
		
			$id = numeric($_GET['id']);
		
			$pdo = new db_pdo();
			
			$pdo->add_consult("SELECT * FROM prospectos WHERE id='$id'");
			
			$sql = $pdo->query();
			$row = $sql[0][0];
			
			$iden_edit = 'edit';
			
			/* SELECTS DATA */
			
			$provider = $this->Dictionary('provider',$row['banco_id']);
			
			
			if( $row['punto_prospectacion'] == 5){				
				$this->internal_js.= '$("#point").parent().append(\'<input type="text" id="point_value" name="point_value" placeholder="Especificar" value="'.$row['punto_prospectacion_value'].'">\');';				
			}
			
			if( $row['empleo_id'] == 19 || $row['empleo_id'] == 22 || $row['empleo_id'] == 23 ){				
				$this->internal_js.= '$("#employments").parent().append(\'<input type="text" id="employments_value" name="employments_value" placeholder="Especificar" value="'.$row['empleo_value'].'">\')';				
			}
			
			
			$ranks =  $this->Dictionary('rank',$row['provedor_id']);
			
			$employ = $this->Dictionary('employ',$row['banco_id']);
						
		else: 
		
			$provider = array( "0" => array('id'=>'0' , 'description' => '---' ) );
			
			$ranks = array( "0" => array('id'=>'0' , 'description' => '---' ) );
			
			$employ = array( "0" => array('id'=>'0' , 'description' => '---' ) );
		
		endif;
		
		
        
        // Create form(Name & ID) <form>
        
        $form = new Form("RegistroDeContacto".$iden_edit);        
        // Legenda para el formulario
        $form->legend = 'Registro de prospectos';        
        //$form->newline = '<br><br>';        
        $form->coverField = array("<div>","</div>");

		
        // Banco, ademas es el iniciador de gran parte del formulario 
        $tmp = WbForm::buildSelectOptions( $this->Dictionary('bank') , $row['banco_id'] , array( "" => 'Selecciona el origen del cliente' ) );
        $form->addField( new FormSelect($tmp, "bank", "Financiera: ") );
        
		// Provedor de producto bancario
        $tmp = WbForm::buildSelectOptions( $provider, $row['provedor_id'], array("" => 'Selecciona un provedor') );        
        $form->addField( new FormSelect($tmp, "provider", "Producto Bancario: ") );
		
        
	// Punto de prospectacion
	$tmp = WbForm::buildSelectOptions( $this->Dictionary('point') , $row['punto_prospectacion'] , array( "" => 'Selecciona una opción' ) );        
        $form->addField( new FormSelect($tmp, "point", "Punto de prospectación: ") );
	
	// Punto de prospectacion
	$tmp = WbForm::buildSelectOptions( $this->Dictionary('plaza_prototipos') , $row['plaza_interesado'] , array( "" => 'Selecciona una opción' ) );        
        $form->addField( new FormSelect($tmp, "plaza", "Desarrollo del interesado: ") );
		
		// Separador
        $form->addField(new FormSeparator("Datos del prospecto"));
		
		// Grado / Tituto
		$tmp = WbForm::buildSelectOptions( $ranks , $row['titulo_id'] , array("" => "Selecciona un Grado/Titulo") );        
        $form->addField( new FormSelect($tmp, "rank", "Grado/Titulo: ") );
		
		
		// Nombre 
        $form->addField(new FormInput($row['nombre'], "text", "name", "Nombre"));
		// Apellido Paterno
        $form->addField(new FormInput($row['apellido_pa'], "text", "last_name", "Apellido paterno"));
		// Apellido Materno
        $form->addField(new FormInput($row['apellido_ma'], "text", "mother_lastname", "Apellido materno"));
	
	// Punto de prospectacion
	$tmp = WbForm::buildSelectOptions( $this->Dictionary('sex') , $row['sexo'] , array( "" => 'Selecciona un genero' ) );        
        $form->addField( new FormSelect($tmp, "sexo", "Sexo: ") );
		
		// Clave ID
		$tmp = WbForm::buildSelectOptions( $this->Dictionary('idnumber') , $row['clave_id'] , array( "" => 'Selecciona una opción' ) );        
        $form->addField( new FormSelect($tmp, "idnumber", "Tipo de Clave: ") );
		
        $form->addField(new FormInput($row['clave_id_value'], "text", "idnumber_value", "Clave ID") );
		
		$form->addField(new FormInput($row["puntos"], "number", "puntos", "Puntos","",null,false,false,false) );
		
		// Separador
        $form->addField(new FormSeparator("Empleo"));
		
		// Clave ID
		$tmp = WbForm::buildSelectOptions(  $employ , $row['empleo_id'] , array("" => "Selecciona un lugar de trabajo") );        
        $form->addField( new FormSelect($tmp, "employments", "Datos del empleo: ") );
		
		$form->addField(new FormInput($row['alta'], "date", "alta", "Fecha de ingreso") );
		
		$form->addField(new FormInput($row['tel_oficina'], "tel", "oficina", "Teléfono oficina") );
		$form->addField(new FormInput($row['tel_celular'], "tel", "cel", "Teléfono celular") );
		$form->addField(new FormInput($row['tel_casa'], "tel", "casa", "Teléfono casa") );
		
		
		$tmp = WbForm::buildSelectOptions( $this->Dictionary('marital_status') , $row['estado_civil'] , array( "" => 'Estado civil' ) );        
        $form->addField( new FormSelect($tmp, "marital", "Estado civil: ") );
		
		// Nombre 
        $form->addField(new FormInput($row['conyuge'], "text", "name_relatioship", "Nombre de Conyuge/Concubina","",null,false,false,false));
		// Apellido Paterno
        $form->addField(new FormInput($row['conyuge_pa'], "text", "last_name_relatioship", "Apellido paterno de Conyuge/Concubina","",null,false,false,false));
		// Apellido Materno
        $form->addField(new FormInput($row['conyuge_ma'], "text", "mother_lastname_relatioship", "Apellido materno de Conyuge/Concubina","",null,false,false,false));
		
	// Separador
        $form->addField(new FormSeparator("Extra"));
	
	// Create the text area.
        $defaultText = "Comentarios sobre el prospecto";
        $textarea = new FormTextArea($row['com_vendedor'], "com_vendedor","Comentarios sobre el prospecto"); 
        
        // You can also set properties like this.
        $textarea->setColumns(50);
        $textarea->setRows(5);
        
        // Add the text area.
        $form->addField($textarea);
		
        if( !$this->ver ){
			// Add the submit button.
			$form->addField(new FormInput("Enviar", "submit", "submit"));
		}
		
		$r = $form->buildForm();
		
		$r.= $this->addJavaScript('operacion.js',"static/javascript/acciones/"); 
		
        return $r;
        
    }
	
	/*
	 * function grido
	 */
	
	protected function grido() {
       
        $c = (object) array();
        
        $c->url_site = $this->url_site;
        
        $c->title = "Lista de registros";
		
		$c->activity = alphanumeric($_GET["app"]);
                
        return $this->render("operador/grid.html",true,$c);

		
	}
	
	
	/*
	 * function asignar
	 */
	
	public function asignar() {
		
		$c = (object) array();
        
        $c->url_site = $this->url_site;
		
		$c->activity = alphanumeric($_GET["app"]);
        
        $c->title = "Coordinador";
		
		$pdo = new db_pdo();
		
		$pdo->add_consult("SELECT id FROM prospectos WHERE status='1' ");
		
		$rows = $pdo->numRows();     
		
		$c->pendiente = ( $rows[0][0] >= 1 )?'zdatagrid();':'nomore();';
	
        return $this->render("operador/asignar.html",true,$c);
		
	}
	

    
}