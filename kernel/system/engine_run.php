<?php

$kernel = array(0 => array('functions','engine/functions/'),
                1 => array('class','engine/class/'),
		2 => array('lib','engine/lib/')
                );



foreach($kernel as $explore){
	
	$folder = ls($windbloom->sys["path"]."kernel/".$explore[1],false);
	
	if( $folder ):
	
	foreach($folder as $file){
		
		if( $file["mime"] == "php"){ include_once($file[2]); }
	
	}
	
	endif;	
	
}