<?php

class archivos {

var $secc;

	function __construct($secc){
		$this->secc = $secc;
	}
	
	function contar($tabla,$cond = NULL){
		$q = mysql_query("SELECT id FROM $tabla $cond");
		$t = mysql_num_rows($q);
		mysql_free_result($q);
		return $t;
	}
	
	function distinc($tabla,$campo,$cond = NULL){
		$q = mysql_query("SELECT DISTINCT $campo FROM $tabla $cond");
		while($row = mysql_fetch_row($q)){
			$t[] = $row[0];
		}
		mysql_free_result($q);
		return $t;
	}
	
	function mostrar($tabla,$cond = NULL,$field = "*"){
		$q = mysql_query("SELECT $field FROM $tabla $cond");
		$t = mysql_fetch_row($q);
		mysql_free_result($q);
		return $t;
	}
	
	function categorias($list = true,$b = NULL,$tabla = "categorias",$k = ""){
		
		if($list){
			$q = mysql_query("SELECT * FROM $tabla $k ORDER BY id ASC");
			
			
			$o = 0;
			
			while($v = mysql_fetch_row($q)){
				for($i = 0;$i < sizeof($v); $i++){
					$w[$o][$i] = $v[$i];
				}
				$o++;
			}
			
			mysql_free_result($q);
			return $w;
		} else {
			$q = mysql_query("SELECT * FROM $tabla WHERE id LIKE '$b'");
			$v = mysql_fetch_row($q);		
			mysql_free_result($q);
			return $v;
		}
		
	}
	
	function subcategorias($id = false){
		$q = mysql_query("SELECT * FROM categorias WHERE main='1'");
			while($v = mysql_fetch_row($q)){					
						echo'<option value="'.$v[0].'" disabled="disabled">: '.$v[1].'</option>';
					$r = mysql_query("SELECT * FROM categorias WHERE main='0' AND rel='$v[0]'");
					while($w = mysql_fetch_row($r)){
						if($id == $w[0]){
							echo'<option value="'.$w[0].'" selected="selected">&nbsp;&nbsp;&nbsp; - '.$w[1].'</option>';
						} else {
							echo'<option value="'.$w[0].'">&nbsp;&nbsp;&nbsp; - '.$w[1].'</option>';
						}
					}
				}
		mysql_free_result($q);
	}
	
	function upload($file,$url,$nombre,$valid,$path,$overwrite = false){
		
		if(stristr($url,"http://")){
		
				$e = str_replace(".","",substr($url,-4));
				$exs = explode(",",$valid);
				
				$image = $path.$nombre.".".$e;
				
				$aS = array_search($e, $exs);
				
				if($aS || stristr(strtolower($url),".torrent")){				
					if(file_exists($image) AND !$overwrite){
						$datos = getimagesize($image);
							if($datos[0] > 3500){
								//resize($image,150);
								$name = $nombre.".".$e;
								return $name;
							} else {
								$name = $nombre.".".$e;
								return $name;
							}	
					} else {
						$mi_curl = curl_init ($url);  
						$fs_archivo = fopen ($image, "w");  
						curl_setopt ($mi_curl, CURLOPT_FILE, $fs_archivo);  
						curl_setopt ($mi_curl, CURLOPT_HEADER, 0);  
						curl_exec ($mi_curl);  
						curl_close ($mi_curl);  
						fclose ($fs_archivo);
						
						$datos = getimagesize($image);
							if($datos[0] > 3500){
								//resize($image,150);
								$name = $nombre.".".$e;
								return $name;
							} else {
								$name = $nombre.".".$e;
								return $name;
							}
						
					}
				} else {
					return false;
				}
		
		} else {
		
				$e = str_replace(".","",substr($file["name"],-4));
				$exs = explode(",",$valid);
				
				$aS = array_search($e, $exs);
				
				if($aS || stristr(strtolower($file["name"]),".torrent")){	
					if(is_uploaded_file($file["tmp_name"])){
						
						$image = $path.$nombre.".".$e;
						
						if(file_exists($image) AND !$overwrite){
							$datos = getimagesize($image);	
								if($datos[0] > 3500){
									//resize($image,150);
									$name = $nombre.".".$e;
									return $name;
								} else {
									$name = $nombre.".".$e;
									return $name;
								}						
						} else {
							if(move_uploaded_file($file["tmp_name"],$image)){						
								$datos = getimagesize($image);
								if($datos[0] > 3500){
									//resize($image,150);
									$name = $nombre.".".$e;
									return $name;
								} else {
									$name = $nombre.".".$e;
									return $name;
								}						
							} else {
								return false;
							}					
						}						
					} else {
						return false;
					}			
				} else {
					return false;
				}
		}
			
	}
	
	function mysql_insert($tabla,$campos,$valores){
		$I = "INSERT INTO $tabla ($campos) VALUES ($valores)";
		mysql_query($I) or die(mysql_error());
		} 
	
	function mysql_update($tabla,$SET,$condiciones){
		$U = "UPDATE $tabla SET $SET $condiciones";
		mysql_query($U) or die(mysql_error());
	}
	
	function mysql_delete($tabla,$condiciones = false){
		if($condiciones){
			$D = "DELETE FROM $tabla WHERE $condiciones";
		} else {
			$D = "DELETE FROM $tabla";
			}
		mysql_query($D) or die(mysql_error());
	}
	
	function tags($del,$var){
		$var = explode($del,$var);
		
		for($i = 0; $i < sizeof($var);$i++){
			
			$tag = strtolower(trim($var[$i]));
			if(!empty($tag)){
				$slug = rWrite($tag,"_",true);
				$c = self::mostrar("tags","WHERE title='$tag' AND slug='$slug'");
					if($c){
						// UPDATE ”USED” 
						$s = $c[4]+1;
						self::mysql_update('tags',"used='$s'","WHERE title='$tag' AND slug='$slug'");
					} else {
						// INSERT NEW TAG
						self::mysql_insert('tags',"title,slug,meta,used","'$tag','$slug','$tag','1'");
				}				
			}
			
		}
		$f = array();
		for($i = 0; $i < sizeof($var);$i++){
			$tag = strtolower(trim($var[$i]));
			$slug = rWrite($tag,"_",true);
			if(!empty($tag)){
				$c = self::mostrar("tags","WHERE title='$tag' AND slug='$slug'");
			}
			$f[] = $c[0];
		}
		
		$final = implode(",",$f);
		return $final;
	}

}

?>