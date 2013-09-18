<?php


class models extends template {

	function __construct(){


	}

	protected function header(){

    	/* Armamos el HEADER DEFAULT */

    	$item[] = parent::header();	
		$cssPath = "static/";
		$item[] = $this->AddStyleSheet("style.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("style-headers.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("style-colors.css",$cssPath,'screen');
        
	
        $jsPath = "static/js/";
        //$item[] = $this->addJavaScript('fecha.js',$jsPath);
        

        return join("\n    ",$item);
	    
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
    

    protected function current_link(){

    	$this->current_home = '';

        $this->current_productos = ($_GET['app'] == "productos" && $_GET['acc'] != 'cotizar' )?'class="current-menu-item"':'';

        $this->current_catalogos = '';

        $this->current_directorio = '';

        $this->current_nosotros = '';

        $this->current_cotizar = ($_GET['acc'] == "cotizar" )?'class="current-menu-item"':'';

    }

    protected function sendMail(){

        session_start();

        if( self::requieredFields( $_POST, array('nombre', 'telefono', 'email', 'mensaje') ) ):

            $mail = new PHPMailer;

            $mail->isSMTP();                     // Set mailer to use SMTP
            $mail->Host = 'webservice.com.mx';     // Specify main and backup server
            $mail->Port       = 587;     // 25 465 587 
            //$mail->SMTPAuth = false;            // Enable SMTP authentication
            $mail->Username = 'sitemailer@acerosvimar.com';        // SMTP username
            $mail->Password = 'Tempvimar55&..';      // SMTP password
            //$mail->SMTPSecure = 'tls';      // Enable encryption, 'ssl' also accepted

            $mail->From = 'andros@pixblob.com';
            $mail->FromName = 'Andros Romo';
            $mail->addAddress('zeickan@gmail.com', 'Andros Peña');  // Add a recipient
            $mail->addReplyTo('andros@webservice.com.mx', 'Webservice');
            $mail->addCC('me@androsromo.com');
            $mail->addBCC('vane@pixblob.com');

            $mail->WordWrap = 50;                                 // Set word wrap to 50 characters
            #$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments
            #$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = 'Cotizacion';
            $mail->Body    = 'Quiero una cotización <br><br>

                Nombre:   '.alphanumeric($_POST["nombre"],"_ .-").'          <br>
                Empresa:  '.alphanumeric($_POST["empresa"],"_ .-").' Giro: '.alphanumeric($_POST["giro"],"_ .-").'   <br>
                Teléfono: '.alphanumeric($_POST["telefono"],"_ .-").'          <br>
                Email:    '.alphanumeric($_POST["email"],"_ .-").'          <br>
                Estado:   '.alphanumeric($_POST["estado"],"_ .-").'          <br><br>
                Mensaje:  '.alphanumeric($_POST["mensaje"],"_ .-").'          <br>
            ';

            if( $_POST['producto'] ){
                $mail->Body.='<strong>Productos a cotizar:</strong><br><ul>';

                foreach( $_POST['producto'] as $row ){
                    $mail->Body.= '<li>'.$row.'</li>';
                }

                $mail->Body.='</ul>';

            }

            #$mail->AltBody = '';

            if(!$mail->send()) {

                return 'Error al enviar: '.$mail->ErrorInfo;
                //exit;
            } else {

                unset($_SESSION['basket']);

                return "Mail enviado";

            }


        else:

            echo "Campos marcados con * son obligatorios.";

        endif;

    }

}