<?php

function cleanHtml($cadena,$exception = NULL){
	
	if($exception):
		
		$e = str_replace(array("<",">"),array("%[","]%"),$exception);
		
		$cadena = str_replace($exception,$e,$cadena);
		
		$cadena = preg_replace('<+[a-zA-Z0-9_ -.="\':; /]+>',"",$cadena);
		
		$cadena = str_replace($e,$exception,$cadena);
		
		#$cadena = "exception";
	
	else:
	
		$cadena = preg_replace('<+[a-zA-Z0-9_ -.="\':; /]+>',"",$cadena);
		
	endif;
	
	return $cadena;
}


function numeric($cadena , $exeptions = ''){
	$cadena = preg_replace('[^0-9 '.$exeptions.']',"",$cadena);
	
	return $cadena;
}


function alphabetic($cadena,$exeptions = ''){
	$cadena = preg_replace('[^a-zA-Z '.$exeptions.']',"",$cadena);
	
	return $cadena;
}


function alphanumeric($cadena , $exeptions = ''){
	$cadena = preg_replace('[^a-zA-Z0-9 '.$exeptions.']',"",$cadena);
	
	return $cadena;
}

function noAcute($cadena){

	$cadena = trim($cadena);
	$cadena = preg_replace("(À|Á|Â|Ã|Ä|Å|à|á|â|ã|ä|å)","a",$cadena);
	$cadena = preg_replace("(È|É|Ê|Ë|è|é|ê|ë)","e",$cadena);
	$cadena = preg_replace("(Ì|Í|Î|Ï|ì|í|î|ï)","i",$cadena);
	$cadena = preg_replace("(Ò|Ó|Ô|Õ|Ö|Ø|ò|ó|ô|õ|ö|ø)","o",$cadena);
	$cadena = preg_replace("(Ù|Ú|Û|Ü|ù|ú|û|ü)","u",$cadena);
	$cadena = preg_replace("(Ñ|ñ)","n",$cadena);
	$cadena = preg_replace("(Ç|ç)","c",$cadena);
	$cadena = preg_replace("(ÿ)","y",$cadena);
	$cadena = preg_replace("[ \t\n\r]", " ", $cadena);
	#$cadena = str_replace(" ", "_", $cadena);
	$cadena = preg_replace("[^ A-Za-z0-9_-]", " ", $cadena);

	return $cadena;
}

function antiI($variable){
	$variable = str_replace("'","&acute;",$variable);
	$variable = str_replace('"',"quot",$variable);
	$variable = str_replace(";","",$variable);
	$variable = str_replace("\\","",$variable);
	$variable = mysql_real_escape_string($variable);
	
	return $variable;
}

function rWrite($cadena,$delimitador = "_",$toLower = false,$toUpper = false){
	
	$cadena = trim(noAcute(utf8_encode($cadena)));	
	
	$rewrite = preg_replace("@[^A-Za-z0-9".$delimitador."]@i", $delimitador, $cadena);
	
	if($toLower){
		$rewrite = strtolower($rewrite);
	} elseif($toUpper){
		$rewrite = strtoupper($rewrite);
	}
	
	$r = 1;
	
	while($r < 2){
		
		if(stristr($rewrite,"".$delimitador."".$delimitador."")){
			$rewrite = str_replace("".$delimitador."".$delimitador."",$delimitador,$rewrite);
			$r = 1;
		} else {
			$r = 2;
		}
				
	}
	
		
	return $rewrite;

}

function randStr($long = 28, $letras_min = true, $letras_max = true, $num = true,$scl = true) {
	
	$salt = $letras_min?'abcdefghijklmnopqrstuvwxyz':'';
	  					   
	$salt .= $letras_max?'ABCDEFGHIJKLMNOPQRSTUVWXYZ':'';
	
	$salt .= $scl?'!#$%&/()=?[]-_*':'';
	
	$salt .= $num?(strlen($salt)?'0123456789':'0123456789'):'';
	
     
	if (strlen($salt) == 0){  return '';  }
       
	$i = 0;
	
	$str = '';
       
	srand((double)microtime()*1000000);
       
	while ($i < $long) {
	  $num = rand(0, strlen($salt)-1);
	  $str .= substr($salt, $num, 1);
	  $i++;
	}
     		
	return $str;
		
}

function url_encrypt($cadena, $sql = false){
	// Validamos la cadena y elimiamos lo que no valga	
	$cadena =  preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '$1', $cadena);	
	// Encriptamos la URL para navegadores
	$cadena = urlencode($cadena);
	// Validamos la encriptacion
	$cadena = preg_replace('([^a-zA-Z0-9 _%-. ])',"",$cadena);
	// Datos listos para SQL
	if($sql){ $cadena = mysql_real_escape_string($cadena); } 
	
	return $cadena;
}


function url_decrypt($cadena,$sql = false){	
	// Validamos la encriptacion
	$cadena = preg_replace('([^a-zA-Z0-9 _%-. ])',"",$cadena);
	// Desencriptamos la URL para usuarios
	$cadena = urldecode($cadena);	
	// Validamos la cadena y elimiamos lo que no valga	
	$cadena =  preg_replace('@(https?://([-\w\.]+)+(:\d+)?(/([\w/_\.]*(\?\S+)?)?)?)@', '$1', $cadena);	
	// Datos listos para SQL
	if($sql){ $cadena = mysql_real_escape_string($cadena); } 
	
	return $cadena;
}

function stri($haystack,$needle){
	
	$stristr = stristr($haystack,$needle);	
	
	$return  = str_replace($stristr,"",$haystack);
	
	return $return;
}

function valid_email($string,$bool = false){
			
	if($bool):		
		

   		return (bool)preg_match('/^\w+([\.-]?\w+)*@\w+([\.-]?\w+)*(\.\w{2,3})+$/',$string);
 
		
	else:
	
	$string = preg_replace('[^a-zA-Z0-9.-ñÑ:_@-]',"",$string);
			
	return $string;
	
	endif;
}

function search_in_array($needle,$haystack,$bool = false){

	foreach($haystack as $name => $value){
		
		if($value == $needle):
		
			$result = $name;
			
			$boo = true;
		
		endif;
	
	}
	
	if($bool):
	
		if($boo){ return true; } else  { return false; }
	
	else:
	
		return $result;
	
	endif;

}

function zerofill($mStretch, $iLength = 2){
    $sPrintfString = '%0' . (int)$iLength . 's';
    return sprintf($sPrintfString, $mStretch);
}

function exists_char($str,$needle,$separator){
			
	$i = explode($separator,$str);
		
	$exists = false;
		
	foreach($i as $e){
		
		if($e == $needle): $exists = true; endif;
		
	}
		
	return $exists;
	
}

function total($subtotal){

	$total = 0;
	
	foreach($subtotal as $price){
		
		$total = $total + $price;
		
	}
	
	return $total;

}

$windbloom->sec = rWrite($_GET["sec"],"-",true);

$windbloom->reg = rWrite($_GET["reg"],"-",true);

$windbloom->pag = numeric($_GET["pag"]);

?>