<?php

/*
 * class REST API
 * @author Andros Romo <me@androsromo.com>
 * @copyright Copyright (c) 2013, Andros Romo
 * @version 1.0
 * @filesource https://github.com/zeickan/Windbloom-Framework
 */

class rest extends models  {
    
    /*
     * function __construct 
     * @example JSON HEADERS
     */
    
    public function __construct() {
        
        parent::__construct();
        
    }
    
    
    public function main(){                
            
        $str = $this->GenerateJson('ok',$json);
    
        echo $str;
           
    }
    
    
    public function addItem(){
        
        $id = numeric($_GET['i']);

        if( $id ):

            session_start();

            $_SESSION['basket'][$id] = numeric($id);

            $str = $this->GenerateJson('ok',$json);
            
            echo $str;
        
        else:

            $this->error('ID incorrecto');

        endif;        
        
    }

    public function sendMail(){

        $mail = new PHPMailer;

        $mail->isSMTP();                     // Set mailer to use SMTP
        $mail->Host = 'smtp.gmail.com';     // Specify main and backup server
        $mail->Port       = 587;     
        $mail->SMTPAuth = true;            // Enable SMTP authentication
        $mail->Username = 'andros@xstilo.net';        // SMTP username
        $mail->Password = 'qwe8521z_&cx';      // SMTP password
        $mail->SMTPSecure = 'tls';      // Enable encryption, 'ssl' also accepted

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

        $mail->Subject = 'Here is the subject';
        $mail->Body    = 'This is the HTML message body <b>in bold!</b>';
        $mail->AltBody = 'This is the body in plain text for non-HTML mail clients';

        if(!$mail->send()) {
            $this->error('Error al enviar: '.$mail->ErrorInfo );
            exit;
        }


        $str = $this->GenerateJson('ok', $json);

        echo $str;

    }

}