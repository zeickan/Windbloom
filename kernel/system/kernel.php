<?php

function dir_exists ($dir, $try_to_create = false, $create_mode = 0777) {
	if ( is_dir($dir) ) {
		return true;
	} elseif ( $try_to_create ) {
		if ( PHP_VERSION >= 5 ) {
			mkdir($dir, $create_mode, true);
		} else {
			$parts = explode('/', trim($dir, '/'));
			$current = ( substr($dir, 0, 1) == '/' ) ? '/' : '' ;
			foreach( $parts as $key => $value ) {
				$current .= $value.'/';
				if ( !is_dir($current) ) {
					if ( !mkdir($current, $create_mode) ) {
						return false;
					}
				}
			}
		}
		if ( is_dir($dir) ) {
			return true;
		}
	}
	return false;
}

function rm($fileglob)
{
   if (is_string($fileglob)) {
       if (is_file($fileglob)) {
           return unlink($fileglob);
       } else if (is_dir($fileglob)) {
           $ok = rm("$fileglob/*");
           if (! $ok) {
               return false;
           }
           return rmdir($fileglob);
       } else {
           $matching = glob($fileglob);
           if ($matching === false) {
               trigger_error(sprintf('No files match supplied glob %s', $fileglob), E_USER_WARNING);
               return false;
           }      
           $rcs = array_map('rm', $matching);
           if (in_array(false, $rcs)) {
               return false;
           }
       }      
   } else if (is_array($fileglob)) {
       $rcs = array_map('rm', $fileglob);
       if (in_array(false, $rcs)) {
           return false;
       }
   } else {
       trigger_error('Param #1 must be filename or glob pattern, or array of filenames or glob patterns', E_USER_ERROR);
       return false;
   }

   return true;
} 


function ls($path = ".",$list = true){

		$Dir = opendir($path); 
		
			while (false !== ($file = readdir($Dir))) { 
			
			if($path == "."){ $ruta = $file; } else { $ruta =  $path.$file; }
				
				if(file_exists($ruta)){
					
					if(is_dir($ruta)){
						if($file == ".." || $file == "."){ } else { 
							//,htmlentities("<a href=\"$ruta/\">$file</a>"),$ruta."/preview.jpg"
							$dir[] = array($file,$ruta."/"); 
							}
					} else {
						
						$ext = str_replace(".","",substr($file,-4));
								
						
						$doc[] = array(
									   $file,
									   $path,
									   $ruta,
									   htmlentities("<a href=\"$ruta/$file\">$file</a>"),
									   'mime' => $ext
									   ); 
						
						
					}
					
				} else {
					$dir[] = $file;
				}
					 
			}
			
		if($list){
			return $dir;
			} else{
			return $doc;
			}
	
}


function move_to($origen,$destino){
  copy($origen,$destino);
  unlink($origen);
}  

function mover($archivo,$destino){
 				
	move_uploaded_file($archivo,$destino);
				
}


	
function dateFormat($timestamp,$format = "D M J",$es = true){
		
	$date = $format;
	$f = getdate($timestamp);
		
	if(strlen($f["mday"]) == 1){ $two["day"] = str_replace(".","",$f["mday"]*0.1);	} else { $two["day"] = $f["mday"];	}
	if(strlen($f["mon"]) == 1){ $two["mon"] = str_replace(".","",$f["mon"]*0.1);	} else { $two["mon"] = $f["mon"];	}
		
	if(strlen($f["minutes"]) == 1){ $i = str_replace(".","",$f["minutes"]*0.1);	} else { $i = $f["minutes"]; }
	if(strlen($f["seconds"]) == 1){ $s = str_replace(".","",$f["seconds"]*0.1);	} else { $s = $f["seconds"]; }
		
	if($f["hours"]){
			if(strlen($f["hours"]) == 1){ $H = str_replace(".","",$f["hours"]*0.1);	} else { $H = $f["hours"];	}
			if($f["hours"] > 12){ $g = $f["hours"]-12; } else { $g = $f["hours"]; }
			if(strlen($g) == 1){ $h = str_replace(".","",$g*0.1);	} else { $h = $g;	}
	} else {
			$H = "00";
			$g = "12";
			$h = "12";
	}	
		

		$date = str_replace("%d",$two["day"],$date);
		$date = str_replace("%j",$f["mday"],$date);
		

		$date = str_replace("%Y",$f["year"],$date);
		$date = str_replace("%y",substr($f["year"],-2),$date);

		$date = str_replace("%H",$H,$date);
		$date = str_replace("%h",$h,$date);
		$date = str_replace("%g",$g,$date);
		$date = str_replace("%G",$f["hours"],$date);
		$date = str_replace("%i",$i,$date);
		$date = str_replace("%s",$s,$date);
		
		$date = str_replace("%m",$two["mon"],$date);
		$date = str_replace("%n",$f["mon"],$date);
		$date = str_replace("%F",$f["month"],$date);
		$date = str_replace("%M",substr($f["month"],0,3),$date);
		
		$date = str_replace("%D",$f["weekday"],$date);
		$date = str_replace("%l",substr($f["weekday"],0,3),$date);
		
		if($es){
			if(strstr($format,"%D")){
			$date = str_replace("Monday","Lunes",$date);
			$date = str_replace("Tuesday","Martes",$date);
			$date = str_replace("Wednesday","Miércoles",$date);
			$date = str_replace("Thursday","Jueves",$date);
			$date = str_replace("Friday","Viernes",$date);
			$date = str_replace("Saturday","Sábado",$date);
			$date = str_replace("Sunday","Domingo",$date);
			}
			if(strstr($format,"%l")){
			$date = str_replace("Mon","Lun",$date);
			$date = str_replace("Tue","Mar",$date);
			$date = str_replace("Wed","Mié",$date);
			$date = str_replace("Thu","Jue",$date);
			$date = str_replace("Fri","Vie",$date);
			$date = str_replace("Sat","Sáb",$date);
			$date = str_replace("Sun","Dom",$date);
			}
			if(strstr($format,"%F")){
			$date = str_replace("January","Enero",$date);
			$date = str_replace("February","Febrero",$date);
			$date = str_replace("March","Marzo",$date);
			$date = str_replace("April","Abril",$date);
			$date = str_replace("May","Mayo",$date);
			$date = str_replace("June","Junio",$date);
			$date = str_replace("July","Julio",$date);
			$date = str_replace("August","Agosto",$date);
			$date = str_replace("September","Septiembre",$date);
			$date = str_replace("October","October",$date);
			$date = str_replace("November","Noviembre",$date);
			$date = str_replace("December","Diciembre",$date); 
			}
			if(strstr($format,"%M")){
			$date = str_replace("Jan","Ene",$date);
			$date = str_replace("Feb","Feb",$date);
			$date = str_replace("Mar","Mar",$date);
			$date = str_replace("Apr","Abr",$date);
			$date = str_replace("May","Mayo",$date);
			$date = str_replace("Jun","Jun",$date);
			$date = str_replace("Jul","Jul",$date);
			$date = str_replace("Aug","Ago",$date);
			$date = str_replace("Sep","Sep",$date);
			$date = str_replace("Oct","Oct",$date);
			$date = str_replace("Nov","Nov",$date);
			$date = str_replace("Dec","Dic",$date); 
			}
		}
		
	return $date;
	
}
	
function error_600($def = ''){
	
	header('"HTTP/1.0 500 Internal Server Error');
	
	$msg = $def?'<h1>'.$def.'</h1>':'';
	
	echo $msg;
	
}



function debug($str){
	
	if(is_array($str)):
		
		echo"<pre>";
		print_r($str);
		echo"</pre>";
	
	else:
	
		echo $str;
		
	endif;
	
}

