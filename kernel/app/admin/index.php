<?php

/*
 * class class
 */

class admin extends template {
    
    /*
     * __construct()
     */
    
    function __construct() {
        
        # globales
        
        global $user;
        
        global $windbloom;
        
        # Configuración del Framework
        
        $this->framework = $windbloom;
        
        $this->user = $user;
        
        # Variables para la plantilla
        
        $this->msg = '';
        
        $this->template_path = "admin/";
        
        $this->template_url = $windbloom->sys['url'].'template/'.$this->template_path;
        
        $this->url_site = $windbloom->sys['url'];
        
        $this->url_app = $this->url_site.'admin/';
        
        $this->self_file = alphanumeric($_GET['acc']).".html";        
        
        # Textos para la plantilla
        
        $this->username = 'Administrador';
        
        $this->title = "Panel de Administración";
        
        # GetHeader, GetCopyright, GetSidebar
        
        $this->header = array('function' => 'header');
        
        $this->copyright = array('function' => "copyright");

        $this->getSideBar = array('function' => 'getSideBar');
        
    }
    
    /* HEADER FUNCTION WITH STYLESHEET AND JAVASCRIPTS */
    
    protected function header(){
        
        $item[] = '<meta charset="UTF-8" />';
        $item[] = '<title>'.$this->title.'</title>'."\n    ";
        
        $cssPath = "template/".$this->template_path.'resources/css/';        
        $item[] = $this->AddStyleSheet("reset.css",$cssPath,'screen');
	$item[] = $this->AddStyleSheet("style.css",$cssPath,'screen');
	$item[] = $this->AddStyleSheet("invalid.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("blue.css",$cssPath,'screen');
        
        
        $jsPath = "template/".$this->template_path.'resources/scripts/';  
        $item[] = $this->addJavaScript('jquery-1.3.2.min.js',$jsPath);        
        $item[] = $this->addJavaScript('simpla.jquery.configuration.js',$jsPath);        
        $item[] = $this->addJavaScript('facebox.js',$jsPath);        
        $item[] = $this->addJavaScript('jquery.wysiwyg.js',$jsPath);

        return join("\n    ",$item);
        
    }
    
    /* SIDEBAR FUNCTION */

    protected function getSideBar(){
       
        $new_template = new template;

        $new_template->url_app = $this->url_site.'admin/';
        
        $new_template->username = 'Administrador';
        
        /* CURRENT CLASS */
        
        $this->current_new = ($_GET[acc] == 'create')?'class="current"':'';
        
        $this->current_manager = ($_GET[acc] == 'manager')?'class="current"':'';

        $new_template->template_path = "admin/";

        return $new_template->readfiletemplate('sidebar.html',true);

    }
    
    /* DASHBOARD */
    
    public function main(){
        
        @session_start();
        
        if( $this->valid_login($_SESSION['user']['name'],$_SESSION['user']['pass']) ){
        
            $this->readfiletemplate("index.html");

        } else {
            
     
            $this->readfiletemplate("login.html");

        }
    }
    
    # Manager 
    
    public function manager(){
        
        @session_start();

        if( $this->valid_login($_SESSION['user']['name'],$_SESSION['user']['pass']) ){
            
                if( $_GET['action'] ){
                    
                    $act = alphanumeric($_GET['action']);
                    
                    if( method_exists($this,$act) ){
                        
                        $this->$act();
                        
                    } 
                    
                }
                
            $this->grid = array('function' => 'grid');
            
            $this->pagination = array( 'function' => 'pagination' );
        
            $this->readfiletemplate("manager.html");

        } else {       
    
            Request::HttpRedirect( $this->url_app );

        }
    }

    protected function grid(){

        $pdo = new db_pdo();
        
        /*  PAGINADO */
        
        $pdo->add_consult("SELECT id FROM tdu_client");
        
        $total = $pdo->numRows();

        $total = $total[0][0];
        
        $this->total = $total;
        
        # NUMERO DE REGISTROS POR PAGINA
        
        $max = 10;
        
        $this->max = $max;
                        
        $pag = numeric($_GET[pag]);
            
        if( empty($pag) ): $def = 0; $pag = 1; else : $def = ($pag - 1) * $max; endif;
            
        $pages = ceil($total / $max);
        
        /* GRID */
        
        $pdo->unset_consult();

        $pdo->add_consult("SELECT tdu_client.id, 
                                tdu_client.title, 
                                tdu_client.slug, 
                                tdu_client.image, 
                                tdu_state.meta_key AS state, 
                                tdu_city.meta_key AS city, 
                                tdu_address.meta_key AS address
                          FROM tdu_client INNER JOIN tdu_state ON tdu_client.tdu_state = tdu_state.id
                                INNER JOIN tdu_city ON tdu_client.tdu_city = tdu_city.id
                                INNER JOIN tdu_address ON tdu_client.tdu_address = tdu_address.id                                
                          ORDER BY id ASC
                          LIMIT $def,$max");
       
        $query = $pdo->query();        
        
        $grid = array();
        
        if( $query[0] ):

        foreach ($query[0] as $key => $value) {
            
            $web = $value['web']?'<a href="'.$value[web].'">'.$value[web].'</a>':'';

            $grid[] = '<tr>
                <td><input name="action[]" value="'.$value['id'].'" type="checkbox" /></td>
                <td>'.model::return_decode($value['title']).'</td>
                <td>'.model::return_decode($value['state']).'</td>
                <td>'.model::return_decode($value['city']).'</td>
                <td>'.model::return_decode($value['address']).'</td>
                <td>
                    <!-- Icons -->
                     <a href="edit.html@&id='.$value[id].'" title="Editar"><img src="'.$this->template_url.'resources/images/icons/pencil.png" alt="Editar" /></a>
                     <a href="manager.html@action=delete&id='.$value[id].'" title="Eliminar"><img src="'.$this->template_url.'resources/images/icons/cross.png" alt="Eliminar" /></a> 
                </td>
            </tr>';

        }
        
        endif;

        return join("\n",$grid);

    }
    
    protected function pagination(){
        
        $total = $this->total;

        $max = $this->max;
        
        $pag = numeric($_GET[pag]);  
        
        if( empty($pag) ): $def = 0; $pag = 1;  else : $def = ($pag - 1) * $max; endif;
            
        $pages = ceil($total / $max);        

        $html =  array();
        
        if( $pages > 1){
            
            if( $pag > 1 ){
                $html[] = HTML::TAG( "&laquo; Primera", 'a', array( "href" => $this->url_app.'manager.html' , "title" => "Primera página") );
                if( $pag != 2):
                $html[] = HTML::TAG( "&laquo; Anterior", 'a', array( "href" => $this->url_app.'manager.html@pag='.(($pag)-1) , "title" => "Página Anterior") );
                endif;
            }
            
            
            if( $pag > 5 ):
            
                $start = ($pag-4);
            
                $PAGES = ($pag+4);
                
                if( $PAGES > $pages){
                    
                    $PAGES = $pages;
                    
                } else {}
            
            else:
                
                $start = 1;
                
                $PAGES = 9;
            
            endif;
            

            for( $i = $start; $i <= $PAGES ;$i++ ):            
            
                if( $pag == $i ){
                    
                    $html[] = HTML::TAG( $i, 'a', array( "href" => $this->url_app.'manager.html@pag='.$i, "title" => $i, "class" => "number current") );

                } else {
                    
                    $html[] = HTML::TAG( $i, 'a', array( "href" => $this->url_app.'manager.html@pag='.$i, "title" => $i, "class" => "number") );

                }
            
            
            endfor;
            
            if( $pag < $pages ){
                
                $html[] = HTML::TAG( 'Siguiente &raquo;', 'a', array( "href" => $this->url_app.'manager.html@pag='.($pag+1), "title" => "Página Siguiente" ) );
                $html[] = HTML::TAG( 'Ultima &raquo;', 'a', array( "href" => $this->url_app.'manager.html@pag='.$pages, "title" => "Ultima página" ) );
                
            }

        }

        return join("\n",$html);

    }
    
    /* NEW */
    
    public function create(){
        
        if( $_GET[enviar] == "insertar"){
            
            $this->insert();
            
        }
        
        $this->get_action = 'insertar';
        
        $this->title.= ' - Nuevo cliente';
        
        $this->createForm = array('function' => 'createForm');
        
        $this->readfiletemplate("create.html");
        
    }
    
    protected function createForma(){
        
        $form = new BuildForm();
        
        $form_attributes = array( "name" => "two1",
                                  "method" => "post",
                                  "id" => "two1",
                                  "enctype" => 'multipart/form-data',
                                  "action" => "do.php");
							
        $form -> set_form($form_attributes); // attributes of form tag  <form name = "" ... >  -> required					
        $form -> add_fieldset(array("Register", "Other Info", "Zone", "Avatar")); // fieldsets name -> optional
        
        $fieldset       = "Other Info";
        
        $field_type     = "radio";							
        $field_name     = "gender1";							
        $field_style    = "";							
        $field_label	= "Gender";
        $field_values	= array("m" => "Male", "f" => "Female" );
        $selectedvalue	= ""; 
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        
    
        
        $field_type     = "checkbox";							
        $field_name     = "check11";							
        $field_style	= "";							
        $field_label	= "I like football";
        $field_values	= "1";
        $selectedvalue	= "";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        
        
        
        $fieldset		= "Avatar";							
        
        $field_type		= "file";							
        $field_name		= "fisier1";							
        $field_style	= "";							
        $field_label	= "Choose image";
        $field_values	= "";
        $selectedvalue	= "";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
                                                                
        $fieldset		= "Register";							
        $field_type		= "text";							
        $field_name		= "username1";							
        $field_style	= "";							
        $field_label	= "username";
        $field_values	= "";
        $selectedvalue	= "";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        
        $field_type		= "text";							
        $field_name		= "name1";							
        $field_style	= "";						
        $field_label	= "your name";
        $field_values	= "";
        $selectedvalue	= "";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        
        $field_type		= "text";							
        $field_name		= "email1";							
        $field_style	= "";						
        $field_label	= "e-mail";
        $field_values	= "";
        $selectedvalue	= "";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        
        $field_type		= "password";							
        $field_name		= "pass1";							
        $field_style	= "";						
        $field_label	= "password";
        $field_values	= "";
        $selectedvalue	= "";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        
        $field_type		= "password";							
        $field_name		= "pass12";							
        $field_style	= "";						
        $field_label	= "retype pass";
        $field_values	= "";
        $selectedvalue	= "";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        
        $fieldset		= "Zone";
        $field_type		= "select";							
        $field_name		= "language1";							
        $field_style	= "";						
        $field_label	= "language";
        $field_values	= array("en" => "English",
                                                        "fr" => "French",
                                                        "ro" => "Romanian");
        $selectedvalue	= "ro";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        
        $field_type		= "select";							
        $field_name		= "location1";							
        $field_style	= "";						
        $field_label	= "location";
        $field_values	= array("United States", "France", "Romania", "Italia", "Germany");
        $selectedvalue	= "";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        
        $fieldset		= "";
        $field_type		= "submit";							
        $field_name		= "submit1";							
        $field_style	= "";							
        $field_label	= "";
        $field_values	= "Send";
        $selectedvalue	= "";
        $form -> add_field($fieldset, $field_type, $field_name, $field_style,  $field_label, $field_values, $selectedvalue);
        $example1 = $form -> output(array("style" => "width: 620px;border: 4px solid #5F5F5F; padding: 5px; "));
        
        return $example1;

        
    }

    protected function createForm(){
        
        $pdo = new db_pdo();
        
        # Tabla a armar

        $pdo->add_consult("DESC tdu_client");
        
        $pdo->add_consult("SELECT * FROM tdu_state");
        
        $pdo->add_consult("SELECT * FROM tdu_type");
       
        $query = $pdo->query();

        $form = array();

        foreach ($query[0] as $key => $value) {
            # code...

            $type = str_replace(strstr($value['Type'],'('),'',$value['Type']);

            $title = $value['Field'];
            
            # Limpiamos los campos (label)

            $name = ucwords(str_replace( array("tdu_","meta_") ," ", $title ));

            $diferent = array();
            
            # Atributos para los Input (Class y Value)

            $atrib = array( "class" => "text-input large-input",
                            "value" => $this->values[$title] );

            $pos = "left";
            
            # Campos de la tabla que no se van a mostrar

            switch ($title) {
                
                case 'id':
                    $display = false;
                break;
            
                case 'image':
                    $display = false;
                break;
            
                case 'slug':
                    $display = false;
                break;
            
                #Todos los demas se muestran...
                
                default:
                    $display = true;
                break;
            }
            
            # Tipo de campo en el formulario segun el tipo de campo
            
            switch ($type) {
                
                case ($type == 'text' || $type == 'longtext'):

                    $FORM = "TEXTAREA";

                    $atrib = array( "class" => "text-input textarea wysiwyg",
                                   "cols" => "79", "rows" => "7" );

                    $pos = "right";
                        
                    $secn = $this->values[$title];
                
                break;
            
                case($type == 'int' || $type == 'bigint'):
                
                    $FORM = "SELECT";
                    
                    $atrib = array( "class" => "medium-input" );
                    
                    if( $title == 'tdu_state' ):
                        $secn = '<option disabled="disabled" selected="selected">-- Selecciona el estado</option>';
                        $secn.= HTML::OPTION($query[1],'id','meta_key');
                    elseif($title == 'tdu_type'):
                        $secn = '<option disabled="disabled" selected="selected">-- Selecciona giro del negocio</option>';
                        $secn.= HTML::OPTION($query[2],'id','meta_key');
                    else:
                        $secn = '<option disabled="disabled" selected="selected">------------------------</option>';
                        
                        $diferent = array( "multiple" => "multiple" );
                    endif;
                
                break;
                
                default:
                    $FORM = "INPUT";
                    $secn = 'text';
                break;
            }
            
            
            #  Traducimos los campos (Label)
            
            $name = str_replace(
                                array( "State","City","Address","Title","Type","Details","Promo","Restrict","Suc","Field31" ),
                                array( "Estado","Ciudad","Municipio","Titulo","Giro del negocio","Detalles","Promoción","Restricciones","Sucursales","Restricciones" ),
                                $name
                                );
            
            if( strstr($name,"Field") ){
                
                #Hidden Field
                
               
            } else {            
            
                $form[$pos][] = $display?layout::HTML_FORM( FORMS::$FORM( $title, $secn, array_merge($atrib,$diferent) ) , $name ):'';
            
            }
        }

        # Agregamos un CAMPO manualmente
        
        $atrib    = array( "class" => "text-input medium-input" );
        $diferent = array( "multiple" => "multiple" );
        
        $form['left'][] = $display?layout::HTML_FORM(FORMS::INPUT( "file[]", "file", array_merge($atrib,$diferent) ) ,
                                                     "Logotipo" ,
                                                     "Subida multiple soportada por Chrome 10+, Safari 5+, FireFox 4+",
                                                     "JPG, JPEG, GIF, PNG","information" ):'';

        

        return  HTML::TAG( join("\n",$form['left']), 'fieldset', array("class"=>"column-left")).
                HTML::TAG( join("\n",$form['right']), 'fieldset', array("class"=>"column-right"));

    }

    
    # Insertamos el registro en la DBSQL

    protected function insert(){
        
        # Verificamos 
        
        if( $_POST ){
            
            # CAMPOS OBLIGATORIOS

            if( model::requiredFileds( array( 'tdu_state', 'tdu_city', 'tdu_address', 'tdu_type', 'title', 'tdu_suc' ) , $_POST ) ):
                
                $pdo = new db_pdo();
                
                $pdo->add_consult("SELECT MAX(id) AS id FROM tdu_client");

                $query = $pdo->query();
                
                # OBTENEMOS EL ID
                
                $id = $query[0][0]['id']?$query[0][0]['id']+1:'1';
                
                $_POST['tdu_suc'] = model::box_sucursal($_POST['tdu_suc']);                
                
                $valid = model::valid_encode($_POST, array( "tdu_suc" => true )  );
                
                $slug = array("slug" => rWrite($valid[title],"-",true) );
                
                $valid = array_merge($valid,$slug);
                
                if( $pdo->insert("tdu_client", $valid ) ){
                
                    $this->msg = layout::HTML_MSG( 'Felicidades se ha registrado tu entrada correctamente.', 'success');
                    
                    $upload = new upload();

                    $upload->submit();

                    if( count($upload->fields[uploaded_files]) >= 1 ){
                        
                        foreach ($upload->fields[uploaded_files] as $key => $value) {
                            # code...

                            if( is_array( $value ) ){

                            $pdo->update("tdu_client", // TABLE NAME
                                        
                                        // SET name     value
                                        array("image" => $value[0]
                                              ),
                                        
                                        // CONDITION (OPTIONAL)
                                        array("id" => $id)			
                                        );
                            }
                        }
                    

                    } else {
                        
                       $this->msg = layout::HTML_MSG('Felicidades se ha registrado tu entrada correctamente, pero sin fotos, podras agregarlas desde al administrador cuando quieras.','information');
 
                    }
                
                    $this->success = true;
                
                } else {
                    
                    $this->msg = layout::HTML_MSG('MySQL ERROR: '.$pdo->error[2]);                    
                    
                }
                
            else:
            
                $this->msg = layout::HTML_MSG('Debes enviar al menos la informacion requerida.');
            
            endif;          

        } else {            
            $this->msg = layout::HTML_MSG('Debes llenar los campos.');
        }
        
    }
    
    
    /* EDITAR */
    
    
    public function edit(){
        
        if( $_GET[enviar] == "actualizar"){
            
            $this->update();
            
        }
        
        $id = numeric($_GET[id]);
        
        $this->get_action = 'actualizar&id='.$id;
        
        $pdo = new db_pdo;
        
        $pdo->add_consult("SELECT * FROM tdu_client WHERE id='$id'");
        
        $query = $pdo->query();
        
        if( $query[0][0] ):
        
            $this->title.= ' - Editar registro';
            
            $sql = $query[0][0];
            
            $sql['tdu_suc'] = model::decode_suc($sql['tdu_suc']);
            
            $this->values = $sql;
            
            $this->createForm = array('function' => 'createForm');
            
            $this->readfiletemplate("create.html");
        
        endif;
        
    }
    
    protected function update(){
        
        # Verificamos 
        
        if( $_POST ){
            
            # CAMPOS OBLIGATORIOS

            if( model::requiredFileds( array( 'tdu_state', 'tdu_city', 'tdu_address', 'tdu_type', 'title', 'tdu_details', 'tdu_suc' ) , $_POST ) ):
                
                $pdo = new db_pdo();
                
                # OBTENEMOS EL ID
                
                $id = numeric($_GET['id']);
                
                $_POST['tdu_suc'] = model::box_sucursal($_POST['tdu_suc']);                
                
                $valid = model::valid_encode($_POST, array( "tdu_suc" => true )  );
                
                $slug = array("slug" => rWrite($valid[title],"-",true) );
                
                $valid = array_merge($valid,$slug);
                
                $pdo->update("tdu_client", $valid, array("id" => $id) );
                
                    $this->msg = layout::HTML_MSG( 'Felicidades se ha registrado tu entrada correctamente.', 'success');
                    
                    $upload = new upload();

                    $upload->submit();

                    if( count($upload->fields[uploaded_files]) >= 1 ){
                        
                        foreach ($upload->fields[uploaded_files] as $key => $value) {
                            # code...

                            if( is_array( $value ) ){

                            $pdo->update("tdu_client", // TABLE NAME
                                        
                                        // SET name     value
                                        array("image" => $value[0]
                                              ),
                                        
                                        // CONDITION (OPTIONAL)
                                        array("id" => $id)			
                                        );
                            }
                        }
                    

                    } else {
                        
                       $this->msg = layout::HTML_MSG('Felicidades se ha registrado tu entrada correctamente.','information');
 
                    }
                
                    $this->success = true;
                
            else:
            
                $this->msg = layout::HTML_MSG('Debes enviar al menos la informacion requerida.');
            
            endif;          

        } else {            
            $this->msg = layout::HTML_MSG('Debes llenar los campos.');
        }
        
        
    }

    /* ELIMINAR */
    
    protected function delete(){
        
        $id = numeric($_GET['id']);
        
        if( $id ):
        
            $pdo = new db_pdo;
            
            $pdo->delete("tdu_client", array("id" => $id) );
            
            $this->msg = layout::HTML_MSG('Registro eliminado con exito.','information');
        
        endif;
    }

    /* LOGIN */

    
    private function valid_login($user,$pass){
        
        if(  $this->user['name'] == $user && $this->user['pass'] == $pass ){
            
            return true;
        
        } else {
            
            return false;
        }

    }

    public function login(){
        
        if( $this->valid_login($_POST['user'],$_POST['pass']) ){
            
            session_start();

            $_SESSION['user'] = array( 'name' => $_POST['user'],
                                       'pass' => $_POST['pass']
                                        );

            header("location: ".$this->framework->sys['url'].'admin/');                                        

        } else {
            
            $this->msg = $this->addMsg('Usuario o contraseña incorrecta.','none');

            $this->readfiletemplate("login.html");
        }


    }

    public function logout(){
        
        session_start();

        unset($_SESSION['user']);

        header("location: main.html");

    }
    
    /* USERS */
    
    public function adduser(){
        
        if( $_GET[enviar] == "insertar"){
            
            $this->insertuser();
            
        }
        
        
        
        $this->get_action = 'insertar';
        
        $this->title.= ' - Nuevo usuario';
        
        $this->userform = array('function' => 'userform');
        
        $this->readfiletemplate("adduser.html");
        
    }
    
    protected function userform(){
        
        # Agregamos un CAMPO manualmente
        
        $atrib    = array( "class" => "text-input medium-input" );
        $diferent = array( "multiple" => "multiple" );
        
        $form = array();
        
        $form['left'][] = layout::HTML_FORM(FORMS::INPUT( "user", "text", array_merge($atrib,$diferent) ) ,
                                                          "Usuario" ,
                                                          "Nombre de usuario",
                                                          "Solo caracteres alfanumericos","information" );
        
        
        $form['left'][] = layout::HTML_FORM(FORMS::INPUT( "pass", "password", array_merge($atrib,$diferent) ) ,
                                                          "Contraseña" ,
                                                          "Contraseña",
                                                          "Se recomiendan 8 caracteres","information" );
        
        $form['left'][] = layout::HTML_FORM(FORMS::INPUT( "mail", "text", array_merge($atrib,$diferent) ) ,
                                                          "Correo electrónico" ,
                                                          "Direccion de email",
                                                          "Debe ser valido","information" );
        
        #Agregamos a la columna izquierda
        
        return  HTML::TAG( join("\n",$form['left']), 'fieldset', array("class"=>"column-left"))
                #.HTML::TAG( join("\n",$form['right']), 'fieldset', array("class"=>"column-right"))
                ;
        
    }
    
    protected function insertuser(){
        
        $this->crypt = 'W1nD';
        
        if( $_POST['user'] && $_POST['pass'] && $_POST['mail']){
            
            $pdo = new db_pdo();
            
            # EJEMPLO DE INSERT CON VALIDACION
            
            $user = alphanumeric($_POST['user'],"_-.");
	    
	    $pass = md5($this->crypt."".$_POST['pass']);
	    
            $mail = alphanumeric($_POST['usuario'],"_@-.");
	    
            
           if( $pdo->insert("users", array( "user" => $user, "pass" => $pass, "email" => $mail  ) ) ){
               
               $this->msg = layout::HTML_MSG( 'Felicidades se ha registrado tu entrada correctamente.', 'success');
               
           } else {
           
                $this->msg = layout::HTML_MSG($pdo->error[2]);;
           
           }
            
        } else {
            
            $this->msg = layout::HTML_MSG('Debes llenar los campos.');
            
        }
        
    }
    
    /* LIST TDU */
    
    protected function grida(){
        
        $pdo = new db_pdo();
        
        /*  PAGINADO */
        
        $pdo->add_consult("SELECT id FROM demo_contrato");
        
        $total = $pdo->numRows();

        $total = $total[0][0];
        
        $this->total = $total;
        
        # NUMERO DE REGISTROS POR PAGINA
        
        $max = 10;
        
        $this->max = $max;
                        
        $pag = numeric($_GET[pag]);
            
        if( empty($pag) ): $def = 0; $pag = 1; else : $def = ($pag - 1) * $max; endif;
            
        $pages = ceil($total / $max);
        
        /* GRID */
        
        $pdo->unset_consult();

        $pdo->add_consult("SELECT * FROM demo_contrato LIMIT $def,$max");
       
        $query = $pdo->query();        
        
        $grid = array();
        
        if( $query[0] ):

        foreach ($query[0] as $key => $value) {
            
            $web = $value['web']?'<a href="'.$value[web].'">'.$value[web].'</a>':'';

            $grid[] = '<tr>
                <td><input name="action[]" value="'.$value['id'].'" type="checkbox" /></td>
                <td>'. $value['nombreComercio'] .'</td>
                <td>'. $value['firmaComercio'] .'</td>
                <td>'. $value['puestoComercio'] .'</td>
                
                <td>'. $value['fecha'] .'</td>
                
                
                <td>
                    <!-- Icons -->
                     <a href="#" title="Editar" rel="modal"><img src="'.$this->template_url.'resources/images/icons/pencil.png" alt="Editar" /></a>
                     <a href="manager.html@action=delete&id='.$value[id].'" title="Eliminar"><img src="'.$this->template_url.'resources/images/icons/cross.png" alt="Eliminar" /></a> 
                </td>
            </tr>';

        }
        
        endif;

        return join("\n",$grid);
        
    }
    
    public function viewcliente(){
        
        
        @session_start();

        if( $this->valid_login($_SESSION['user']['name'],$_SESSION['user']['pass']) ){
            
                if( $_GET['action'] ){
                    
                    $act = alphanumeric($_GET['action']);
                    
                    if( method_exists($this,$act) ){
                        
                        $this->$act();
                        
                    }
                    
                }
                
            $this->grid = array('function' => 'grida');
            
            #$this->pagination = array( 'function' => 'pagination' );
        
            $this->readfiletemplate("tdulist.html");

        } else {       
    
            Request::HttpRedirect( $this->url_app );

        }        
        
    }
    
}

class layout {
    
    public function HTML_FORM( $content , $title = '', $msg = NULL , $alert = NULL , $type = 'success'){
        
        $elem = array();

        $elem[] = '<p>';
        $elem[] = "<label>$title</label>";
        $elem[] = $content;
        $elem[] = $alert?'<span class="input-notification '.$type.' png_bg">'.$alert.'</span>':'';
        $elem[] = $msg?'<br /><small>'.$msg.'</small>':'';
        $elem[] = '</p>';

        return join(" ",$elem);
    }


    public function HTML_MSG($msg,$type = "error"){
        
        return '<div class="notification '.$type.' png_bg">
                <a href="#" class="close"><img src="{template_url}resources/images/icons/cross_grey_small.png" title="Close this notification" alt="close" /></a>
                <div>
                    '.$msg.'
                </div>
            </div>';

    }
}