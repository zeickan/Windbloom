<?php

class zeickan {
	
	function login($user,$pass,$url,$cookie_name = "listin",$extra = NULL,$ssl = false){
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_COOKIEJAR, $cookie_name);
		curl_setopt($ch, CURLOPT_URL,$url);
		if($ssl){
			curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
			//curl_setopt($mi_curl, CURLOPT_SSL_VERIFYHOST, 2);
		}
		curl_setopt($ch, CURLOPT_POST, 1);
		curl_setopt($ch, CURLOPT_POSTFIELDS, $user[0]."=".$user[1]."&".$pass[0]."=".$pass[1]."".$extra);
			ob_start();
			curl_exec ($ch);
			ob_end_clean();
			curl_close ($ch);
		unset($ch);
	}
	
	
	function ver($url,$cookie_name = "listin"){
		
		$agent = "Mozilla/5.0 (Windows; U; Windows NT 6.0; es-ES; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5 ";
		$ch = curl_init($url);
		if($cookie_name){
			curl_setopt($ch,  CURLOPT_COOKIEJAR, $cookie_name);
			curl_setopt($ch,  CURLOPT_COOKIEFILE, $cookie_name);
		}
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_VERBOSE, 1);
		curl_setopt($ch, CURLOPT_USERAGENT, $agent);
		curl_setopt ($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch,  CURLOPT_FOLLOWLOCATION, 1);	
	  	$tmp = curl_exec ($ch);
		curl_close ($ch);
		return $tmp;
	
	}
	
	function xcopy($url,$path,$ssl = false,$auth = false,$cookie_name = NULL){
		
		$agent = "Mozilla/5.0 (Windows; U; Windows NT 6.0; es-ES; rv:1.9.1.2) Gecko/20090729 Firefox/3.5.2 GTB5 ";
		$mi_curl = curl_init ($url);
		$fs_archivo = fopen ($path, "w");
		if($auth){
			curl_setopt($mi_curl,  CURLOPT_COOKIEJAR, $cookie_name);
			curl_setopt($mi_curl,  CURLOPT_COOKIEFILE, $cookie_name);
		}
		curl_setopt($mi_curl, CURLOPT_RETURNTRANSFER, 1);
		if($ssl){
			curl_setopt($mi_curl, CURLOPT_SSL_VERIFYPEER, 0);
			//curl_setopt($mi_curl, CURLOPT_SSL_VERIFYHOST, 2);
		}
		curl_setopt($mi_curl, CURLOPT_VERBOSE, 1);
		curl_setopt($mi_curl, CURLOPT_USERAGENT, $agent);
		curl_setopt ($mi_curl, CURLOPT_HEADER, 0);
		curl_setopt($mi_curl,  CURLOPT_FOLLOWLOCATION, 1);	
		curl_setopt ($mi_curl, CURLOPT_FILE, $fs_archivo);  
		curl_exec ($mi_curl);  
		curl_close ($mi_curl);  
		fclose ($fs_archivo);  	
		
	}
	
	function clean_html($cadena){
		$cadena = ereg_replace('<+[a-zA-Z0-9_ -.="\':; /]+>',"",$cadena);
	
		return $cadena;
	}
	
	function getUrl($cadena,$href = true){
		if($href){
			preg_match_all("/href=\"(.*?)\"/is", $cadena,$tags);
		} else {
			preg_match_all("/action=\"(.*?)\"/is", $cadena,$tags);	
		}
	
		return $tags;
	}
	
	function peso($peso){
		
		$peso = strtoupper($peso);
		
		if(stristr($peso,"KB")){
			$size = (int) trim(str_replace("KB",'',$peso));
			$size = $size*1024;
		} elseif(stristr($peso,"MB")){
			$size = (int) trim(str_replace("MB",'',$peso));
			$size = ($size*1024)*1024;
		} elseif(stristr($peso,"GB")){
			$size = (int) trim(str_replace("GB",'',$peso));
			$size = (($size*1024)*1024)*1024;
		}		
		
		return $size;
	}
	
	function verify($filename,$size,$max = 4194304000){
		
		$ext = str_replace(".",'',substr($filename,-4));
		
		$invalidas = array("php","asp","html","php4","php3","htm","css","xml");
		
		$valid = true;
		
		foreach($invalidas as $line){
			if($line == $ext){
				$valid = false;	
			}			
		}
		
		if($valid){
			
			$peso = self::peso($size);
			
			if($peso <= $max){
			
				$msj = true;
				
			} else {
			
			$msj = "El archivo es muy pesado.";
				
			}
			
		} else {
			
			$msj = "La extencion del archivo, no es soportada por el sistema.";	
			
		}
		
		return $msj;
		
	}

	function nuevoNombre($long = 28, $letras_min = true, $letras_max = true, $num = true) {
      $salt = $letras_min?'abchefghknpqrstuvwxyz':'';
      $salt .= $letras_max?'ACDEFHKNPRSTUVWXYZ':'';
      $salt .= $num?(strlen($salt)?'2345679':'0123456789'):'';
     
      if (strlen($salt) == 0) {
        return '';
      }
     
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
	
	function lessPoint($user,$puntos,$grupo){
		$puntos = $puntos-1;
		$UPDATE = "UPDATE ibf_members SET points='$puntos' WHERE member_id='$user'";
		if($grupo == 4 || $grupo == 6 || $grupo == 7 || $grupo == 8){
			
		} else {
			@mysql_query($UPDATE) or die(mysql_error());	
		}
	}

	
}


class megaupload {
	
	var $user;
	var $pass;
	var $path;
	var $site;
	
	function __construct($user,$pass,$path,$site){
		$this->user = $user;
		$this->pass = $pass;
		$this->path = $path;
		$this->site = $site;
	}
	
	
	function generar($link,$download = false,$uname = false,$points = false,$grupo = false){
		$u = array("username",$this->user);
		$p = array("password",$this->pass);
		$url = "http://megaupload.com/?c=login";
		$cookie_name = md5("megauploadCookie");
		$extra = "&login=1&redir=1";
		zeickan::login($u,$p,$url,$cookie_name,$extra);
		
		$tmp = zeickan::ver($link,$cookie_name);
		$html = explode("\n",$tmp); 
		
		foreach($html as $line){
			if(stristr($line,"megaupload.com/files/")){ $mulink = $line; } 
			elseif(stristr($line,"Filename:")){ $filename = $line; } 
			elseif(stristr($line,"File description:")){ $description = $line; } 
			elseif(stristr($line,"File size:")){ $size = $line; } 
		}
		
		//$filename = trim(str_replace("Filename:","",zeickan::clean_html($filename)));
		//				  http://www639.megaupload.com/files/5070f072543733cff601275fd9d1a0b3/Ultimatum.a.la.tierra.wWw.DarkVille.Com.Mx.by.Menash.part2.rar
		
		$description = trim(str_replace("File description:","",zeickan::clean_html($description)));
		$size = trim(str_replace("File size:","",zeickan::clean_html($size)));
		
		$mulink = zeickan::getUrl($mulink);

		$descarga = $mulink[1][0];
		
		$filename = ereg_replace('http://+[[:alpha:][:digit:]]+.megaupload.com/files/+[a-zA-Z0-9]+/',"",$descarga);
		
		
		
		$ver = zeickan::verify($filename,$size);
		
			if( $ver === true ){
				
				if($download){
					$filename = ereg_replace("[^A-Za-z0-9_ .]", "", $filename);
					
					if(!file_exists($this->path."/".$filename)){						
						zeickan::xcopy($descarga,$this->path."/".$filename);
					}
					
					#zeickan::lessPoint($uname,$points,$grupo);
					@unlink($cookie_name);
					header("location: ".$this->site.$this->path."/".$filename);
				} else {
					@unlink($cookie_name);
				}
				
				return array($filename,$size,$descarga);
				
			} else {
				
				@unlink($cookie_name);
				return array(false,$ver);
				
			}
		
		
		
		
	}
	
}

class gigasize {
	
	var $user;
	var $pass;
	var $path;
	var $site;
	
	function __construct($user,$pass,$path,$site){
		$this->user = $user;
		$this->pass = $pass;
		$this->path = $path;
		$this->site = $site;
	}
	
	
	function generar($link,$download = false,$uname = false,$points = false,$grupo = false){
		
		$u = array("uname",$this->user);
		$p = array("passwd",$this->pass);
		$url = "http://www.gigasize.com/login.php";
		$cookie_name = md5("gigasizeCookie");
		$extra = "&login=1&d=Login";
		
		zeickan::login($u,$p,$url,$cookie_name,$extra);		
		
		$tmp = zeickan::ver($link,$cookie_name);
		
		$tmp2 = zeickan::ver("http://www.gigasize.com/form.php",$cookie_name);
		
		$html = explode("\n",$tmp2);		
		
		foreach($html as $line){
			if(stristr($line,"getcgi.php")){ $gslink = $line; } 			
		}
		
		$html = explode("\n",$tmp);
		
		foreach($html as $line){
			if(stristr($line,'<strong>Name</strong>:')){ $filename = $line; } 
			elseif(stristr($line,'<p>Size:')){ $size = $line; } 
			
		}
		
		$filename = trim(str_replace("Name:","",zeickan::clean_html($filename)));
		$size = trim(str_replace("Size:","",zeickan::clean_html($size)));
		$size = trim(str_replace('|','',str_replace('&nbsp;','',$size)));
				
		$gslink = zeickan::getUrl($gslink);

		$descarga = "http://www.gigasize.com".$gslink[1][0];
		
			
		$ver = zeickan::verify($filename,$size);
		
			if( $ver === true ){
				
				if($download){
					if(!file_exists($this->path."/".$filename)){
						//$filename = ereg_replace("[^A-Za-z0-9_ .]", "", $filename);
						zeickan::xcopy($descarga,$this->path."/".$filename,true);
					}
					#zeickan::lessPoint($uname,$points,$grupo);
					@unlink($cookie_name);
					header("location: ".$this->site.$this->path."/".$filename);
				} else {
					@unlink($cookie_name);	
				}
		
				
				return array($filename,$size,$descarga);
				
			} else {
				
				@unlink($cookie_name);
				return array(false,$ver);
				
			}
		
		
		@unlink($cookie_name);
				

	}
	
}

class rapidshare {
	
	var $user;
	var $pass;
	var $path;
	var $site;
	
	function __construct($user,$pass,$path,$site){
		$this->user = $user;
		$this->pass = $pass;
		$this->path = $path;
		$this->site = $site;
	}
	
	function generar($link,$download = false,$uname = false,$points = false,$grupo = false){
		$u = array("login",$this->user);
		$p = array("password",$this->pass);
		$url = "https://ssl.rapidshare.com/cgi-bin/premiumzone.cgi";
		$cookie_name = md5("rapidshareCookie");
		$extra = "&uselandingpage=1";
		
		$tmp = zeickan::ver($link,false);
		
		$html = explode("\n",$tmp);	
		
		foreach($html as $line){
			if(stristr($line,'id="ff"')){ $rslink = $line; }
			elseif(stristr($line,"downloadlink")){ $size = $line; }
		}
		
		$rslink = zeickan::getUrl($rslink,false);

		$descarga = $rslink[1][0];
		
		$filename = ereg_replace('http://+[[:alpha:][:digit:]]+.rapidshare.com/files/+[[:digit:]]+/',"",$descarga);
		$size = ereg_replace('http://rapidshare.com/files/+[[:digit:]]+/+[a-zA-Z0-9_ -.="\':; /]+',"",$size);
		$size = trim(str_replace("|",'',zeickan::clean_html($size)));
		
				
		$ver = zeickan::verify($filename,$size);
		
			if( $ver === true ){
				
				if($download){
					zeickan::login($u,$p,$url,$cookie_name,$extra,true);
					if(!file_exists($this->path."/".$filename)){
						//$filename = ereg_replace("[^A-Za-z0-9_ .]", "", $filename);
						zeickan::xcopy($descarga,$this->path."/".$filename,true,true,$cookie_name);
					}
					@unlink($cookie_name);
					#zeickan::lessPoint($uname,$points,$grupo);
					header("location: ".$this->site.$this->path."/".$filename);
				 } else {
					@unlink($cookie_name); 
					}
				
				return array($filename,$size);
				
			} else {
				
				@unlink($cookie_name);
				return array(false,$ver);
				
			}
		
		@unlink($cookie_name);

	}
	
}

function account($in = '',$cache = false){
		
		$remaining = user::search_meta('premium_expires');
		
		$time = time();
		
		$expires = round(((($remaining-$time)/60)/60)/24);
		
		switch ($in):
		
			case "expieres":
				$return = $expires;
			break;
			
			case "remaining":
				$return = $remaining;
			break;
			
			default:
				
				if( $remaining > $time):
				
					$return = "Premium";
				
				else:
				
					$return = "Normal";
				
				endif;
				
			break;
		
		endswitch;
		
		if( $cache ){
			return $return;
		} else {
			echo $return;	
		}
		
	}
	
	function not_service($url){
		
		preg_match('@^(?:http://)?([^/]+)@i',$url, $matches);
		
		$host = $matches[1];
		
		preg_match('/[^.]+\.[^.]+$/', $host, $matches);
		
		$host = strtolower($matches[0]);
		
		switch ($host){
			
			case strstr($host,"megaupload"):
				
				preg_match('@^(?:http://)?([^=]+)@i',$url, $code);
				
				$string = $url;
				
				$patterns = "@(?:http://)([^=]+)=@";
					
				$replacements = '';
					
				$cod = preg_replace($patterns, $replacements, $string);				
				
				return array($url,"megaupload",$cod);
				
			break;
			
			case strstr($host,"gigasize"):
				
				preg_match('@^(?:http://)?([^=]+)@i',$url, $code);
				
				$string = $url;
				
				$patterns = "@(?:http://)([^=]+)=@";
					
				$replacements = '';
					
				$cod = preg_replace($patterns, $replacements, $string);				
				
				return array($url,"gigasize",$cod);
				
			break;
			
			default:
				return false;
			break;
			
		}		
		
	}
	

?>