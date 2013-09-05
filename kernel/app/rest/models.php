<?php

/*
 * class models
 */

class models extends template {
    
    /*
     * __construct()
     * @param 
     */
    
    function __construct(){
        
        $this->debug = true;
        
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json; CHARSET: UTF-8;');
        
        $this->callback = $_GET['callback']?alphanumeric($_GET['callback'],".-_"):'Windbloom';
        
        if( isset($_GET['callback']) ){
            unset($_GET['callback']);
        }
        
    }
    
    /*
     * function GenerateJson
     * @param String $stat  Response is Ok, error, false, null, etc
     * @param Array  $array Array to serialize in a Json String
     * @access protected
     */
    
    protected function GenerateJson($stat = 'ok' , $array){
        
        $json = array();
        
        $json['stat'] = $stat;
        
        if( $array ):
            
            while( list($k,$v) = each($array) ){
                
                $json[$k] = $v;    
                
            }
            
        endif;
        
        $json_str = $this->callback."(".json_encode($json).")";
        
        return $json_str;
        
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
    
    
    /*
     * function error
     * @param $error, $code
     * @access private
     */
    
    protected function error($error,$code = 1){
        
        #header('HTTP/1.1 500 Internal Server Error');        
        
        $json['code'] = $code;
        $json['message'] = $error;
        if($this->debug === true)  $json['debug'] = $_GET;
        
        $error = $this->GenerateJson('fail',$json);
        
        echo $error;
        
    }
    
}