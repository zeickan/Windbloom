<?php


class jsonLib {
    
    function __construct(){
    
	$this->callback = $_GET['jsoncallback']?true:false;
    
    }
    
    public function convert($array = false,$nonumb = false){
	
	$return = '';
	
	if( $this->callback ){
	    
	    $return.= alphanumeric($_GET['jsoncallback']).'(';
	    
	}
	
	if( $nonumb ){
	    
	    foreach($array as $key => $value){
		
		$t = count($value);
		
		for($x = 0;$x <= $t; $x++){
		    
		    unset($value[$x]);
		    
		}
		
		$array[$key] = $value;
		
	    }
	    
	}
	
	$return.= json_encode($array);
	
	
	if( $this->callback ){
	    
	    $return.= ')';
	    
	}
	
	return $return;
	
    }
    
    protected function encode($string,$encode = 'encode'){
	
	if( $encode == 'encode'){
	    
	    $string = utf8_encode($string);
	    
	} else {
	    
	    $string = utf8_decode($string);
	    
	}
	
	return $string;
	
    }
    
    public function resetArray( $array, $selective = false , $entities = true , $encode = false	 ){
	
	$reset = array();
	
	foreach($array as $key => $value){	    
	        
	    if($selective):
	    
		$sel = array();
	    
		foreach($selective as $v):
		
		    $encode = ($encode)?self::encode($value[$v],$encode):$value[$v];
		    
		    $sel[$v] = ($entities)?htmlentities($value[$v]):$value[$v];
		
		endforeach;
		
		$reset[$key] = $sel;
	    
	    else:
	    
		$reset[$key] = $value;
		
	    endif;
	}
	
	return $reset;
	
    }
    
    public function publish($json){
	
	print_r($json);
	
	
    }
    
}