<?php

include_once getcwd().'/kernel/app/default/models.php';

/*
 * class preprocess
 */

class view extends models {
	
	var $id;
	
	/*
	 * __construct()
	 * @param $arg
	 */
	
	function __construct($id){
		
		$this->id = numeric($id);
        
        $this->debug = true;
        
        header('Cache-Control: no-cache, must-revalidate');
        header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
        header('Content-type: application/json; CHARSET: UTF-8;');
        
        $this->callback = $_GET['jsoncallback']?alphanumeric($_GET['jsoncallback'],".-_"):'Windbloom';
        
        if( isset($_GET['jsoncallback']) ){
            unset($_GET['jsoncallback']);
        }
        
    }
	
	/*
	 * function encodeArray
	 * @param $array
	 */
	
	function encodeArray($array) {
		
		if( $array ){
			
			$tmp = array();
			
			while( list($k,$v) = each($array) ){
				
				if( is_array( $v ) ){
					
					foreach($v as $key => $value ){
						
						$array[$k][$key] = utf8_encode($value);
						
					}
					
				} else {
					
					$array[$k] = utf8_encode($v);
					
				}
				
			}
			
		}
		
		return $array;
		
	}
	
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