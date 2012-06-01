<?php

$valid_exts = explode(",",$global->upload["valid_extensions"]);

function move_upload($archivo,$destino){
 				
	move_uploaded_file($archivo,$destino);
				
}

function valid_extension($file){

	global $valid_exts;
	
	$ext = str_replace(".","",substr($file,-4));
	
	if( search_in_array($ext,$valid_exts,true) ):
	
		return $ext;
	
		else:
	
		return false;
	
	endif;
		

}

function submit_uploaded($resize = false, $thumb = false, $name = "xfile"){
	
	global $uploaded_response;
	
	global $upload_path;	
	
	global $upload_file;
	
	global $upload_thumb;
	
	if( !empty($_FILES["$name"]["tmp_name"]) ){
	
		$ext = valid_extension($_FILES["$name"]["name"]);		
		
		if( $ext ):
		
			if( is_uploaded_file($_FILES["$name"]['tmp_name']) ) {
				
				$new_name = md5(randStr(25));
				
				$upload_file = $upload_path.$new_name.".".$ext;
				
				move_upload($_FILES["$name"]['tmp_name'],"../".$upload_file);
				
				if( $resize ):
				
					$or = getimagesize("../".$upload_file);
				
					if( $or[0] == $or[1]){ resize("../".$upload_file,$resize); } 
					elseif( $or[0] < $or[1] ){ resize("../".$upload_file,$resize,"vertical"); } 
					else { resize("../".$upload_file,$resize); }
				
				endif;
				
				if( $thumb ):
				
					$thumb_name = $upload_path.$new_name."-thumb".".".$ext;
					
					copy("../".$upload_file,"../".$thumb_name);
					
					$or = getimagesize("../".$thumb_name);
				
					if( $or[0] == $or[1]){ resize("../".$thumb_name,$thumb); } 
					elseif( $or[0] < $or[1] ){ resize("../".$thumb_name,$thumb,"vertical"); } 
					else { resize("../".$thumb_name,$thumb); }
					
					$upload_thumb = $thumb_name;
				
				endif;
				
				$uploaded_response = "El archivo se subio con exito.";
				
				return true;
							
			} else {
			
			$uploaded_response = "Error con el fichero.";
			
			return false;
			
			}		
		
		else:
		
		$uploaded_response = "La extension del archivo no es valida.";	
		
		return false;
		
		endif;		
		
	} else {
	
		$uploaded_response = "";
		
		return false;
	
	}
	
}


?>