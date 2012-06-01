<?php

function bbcode_linebreak($str,$entities = false){		
	
	$str = str_replace("\r\n","<br />",$str);
	$str = str_replace("\n","<br />",$str);
	
	if($entities):
	
		$str = str_replace("<br />","&lt;br /&gt;",$str);
		
		
	endif;
	
	$str = preg_replace('/\s\s+/', ' ', $str);
	
	$r = 1;
	
			while($r < 2){
				
				if(stristr($str,"<br /><br /><br />")){
					$str = str_replace("<br /><br /><br />","<br />",$str);
					$r = 1;
				} else {
					$r = 2;
				}
						
			}
		
	return $str;
}

function codebox($string){
	$string = "ESTO ES UN CODEBOX";
}

function bbcode_is_image($string){
	$string = '&lt;img src="'.$string.'" border="0" /&gt;';
	return $string;
}

function no_backslash($string){
	
	$string = str_replace("\\","",$string);
	
	return $string;
	
}

function bbcode_pre($string){

	$string = str_replace("\\","",$string);
	
	$string = strtolower($string);
	
	return $string;
	
}

function bbcode($string){
	
   /*
	* Definimos los BBCODE y su resultado
	* Pasamos a minusculas todo el BBCODE
	*/
	
	$string = preg_replace("/(\[)([a-zA-Z0-9:\/. _?=&+#|\-\"']+)(\/?])/e", "'\\1'.bbcode_pre('\\2').'\\3'", $string);
	
	$string  = bbcode_linebreak($string,true);
	
	$patterns = array();
	
	$replacements = array();

   /* 
	* BBCODE para URL y URL compuesta
	* [url] % [/url] 
	* [url="$1"] % [/url]
	*/
	
	$patterns[] = "/(\[(url|URL)])([a-zA-Z0-9:\/. _?=&+#|\-]+)(\[\/?(url|URL)])/e";
	
	$replacements[] = "'&lt;a href=\"\\3\" target=\"_blank\"&gt;\\3&lt;/a&gt;'";
	
	$patterns[] = "/(\[(url|URL)=\"([a-zA-Z0-9:\/. _?=&+#|\-]+)\"])([a-zA-Z0-9:\/. _?=&+#|\-\"\]\[';]+)(\[\/?(url|URL)])/e";
	
	$replacements[] = "'&lt;a href=\"\\3\" target=\"_blank\"&gt;'.no_backslash('\\4').'&lt;/a&gt;'";	

   /* 
	* BBCODE para imagenes
	* [img] % [/img] 
	*/
	 
	$patterns[] = "/(\[(img|IMG)])([a-zA-Z0-9:\/. _?=&+#|\-]+)(\[\/?(img|IMG)])/e";
	
	$replacements[] = "''.bbcode_is_image('\\3').''";
	

   /*
   	* BBCODE para Formato
    */
	
	$format = "b|i|s|u";
	
	$patterns[] = "/(\[)([$format])(])/e";
	$replacements[] = "'&lt;'.strtolower('\\2').'&gt;'";
	 
	$patterns[] = "/(\[)(\/[$format])(])/e";
	$replacements[] = "'&lt;'.strtolower('\\2').'&gt;'";
	
	
	$string = preg_replace($patterns, $replacements, $string);
	
	return htmlspecialchars_decode($string);

}

?>