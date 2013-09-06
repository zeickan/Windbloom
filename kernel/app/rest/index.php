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
    
    
    public function login(){
        
        if( parent::requieredFields( $_GET, array('user','pass') ) ):
            
            $pdo = new db_pdo();
            
            $user = alphanumeric($_GET['user'],"_-.\@");
            
            $pass = alphanumeric($_GET['pass'],"_-.\@");
            
            $pdo->add_consult("SELECT * FROM users WHERE username='$user' && passwd='$pass'");

            $query = $pdo->query();
            
            $user_query = $query[0];
            
            if( $query[0][0] >= 1 ){
                
                $pdo->unset_consult();
                
                $pdo->add_consult("SELECT * FROM users_profile WHERE aka='$user'");
                
                $query = $pdo->query();
                
                $json['message'] = "Welcome";
                
                $json['profile'] = $query[0][0];
                
                $json['user'] = $user_query;
            
                $str = $this->GenerateJson('ok',$json);
            
                echo $str;
                
                
            } else {
                
                $this->error('Usuario no encontrado.');
                
            }
        
        else:
        
            $this->error('Error en la petición.');
        
        endif;       
        
    }
    
    public function signup(){
        
        if( parent::requieredFields( $_GET, array('user','name','locate') ) ){
            
            
            $pdo = new db_pdo();
            
            $json['OwnCode'] = randStr(21,0,1,1,0);
            
            $name = explode(" ", alphanumeric(rawurldecode($_GET['name'])) );
            
            $pais = alphanumeric($_GET['locate']);
            
            $user = alphanumeric($_GET['user'],"_-.\@");
            
            
            
            $pdo->add_consult("SELECT * FROM trconcursantes WHERE email='$user'");

            $query = $pdo->numRows();
            
            if( $query[0][0] < 1 ){
            
                
                if( $pdo->insert("trconcursantes",
                                 array("nombre" => $name[0],
                                       "apellidos" => $name[1],
                                       "pais" => $pais,
                                       "email" => $user,
                                       "facebook" => '',
                                       "authcode" => $json['OwnCode'],
                                       "creado" => date("Y-m-d H:i:s"),
                                       "activo" => 1
                                       )
                                 )
                   ){
                    
                    $json['message'] = "Welcome";
            
                    $str = $this->GenerateJson('ok',$json);
                
                    echo $str;
                    
                } else {
                
                    $this->error('Error Interno. '.$pdo->error[2]);
                
                }
                
            } else {
            
                $this->error('Error. Usuario ya registrado.');    
                
            }
            
        } else {
            
            $this->error('Error. Los datos enviados no estan completos.');
            
        }
        
        
    }
    
    
}