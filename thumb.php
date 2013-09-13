<?php

function resize($image,$n3wX,$position = "1:1",$crop = false,$mask = false){

header("Content-type: image/jpeg");

	$Datos = getimagesize($image); 	$Ancho=$Datos[0]; $Alto=$Datos[1];		 
		 
	if(!empty($n3wX)){ $newX = (int) $n3wX; } else { $newX = "600"; }
	
	if( $position == "4:6"){
		
	$AX = round(($newX*100)/$Alto);
		 
	if($newX >= $Alto){ $a1 = substr($AX,0,1); $a2 = substr($AX,-2); $ax = "$a1.$a2"; } else { if($AX <= 9){ $ax = ".0$AX"; } else { $ax = ".$AX";  } }
	
	$newW = round($Ancho*$ax);
	
	$new_w = $newW;
	
	$new_h = $newX;	
	
	
		
	} else {
	
	$AX = round(($newX*100)/$Ancho);
		 
	if($newX >= $Ancho){ $a1 = substr($AX,0,1); $a2 = substr($AX,-2); $ax = "$a1.$a2"; } else { if($AX <= 9){ $ax = ".0$AX"; } else { $ax = ".$AX";  } }
	
	$newW = round($Alto*$ax);
	
	$new_w = $newX;
	
	$new_h = $newW;	
	
	}

	 
		 
	$image_info = getimagesize($image);

	if ($image_info['mime'] == 'image/gif') {
		$alphablending = true;
		$img_old = @imagecreatefromgif($image);
		imagealphablending($img_old, true);
		imagesavealpha($img_old, true);
	}
	elseif ($image_info['mime'] == 'image/png') {
		$alphablending = false;
		$img_old = @imagecreatefrompng($image);
		imagealphablending($img_old, true);
		imagesavealpha($img_old, true);
	} elseif($image_info['mime'] == 'image/jpeg'){
		$alphablending = false;
		$img_old = @imagecreatefromjpeg($image);
		imagealphablending($img_old, true);
		imagesavealpha($img_old, true);
	}

	if( $crop ):
		
		$mask = explode(":",$mask);		
		
		$width = $mask[0]?$mask[0]:$new_w;
	
		$height = $mask[1]?$mask[1]:$new_h;	
		
	else:
	
		$width = $new_w;
		
		$height = $new_h;
	
	endif;	
	
	$img_temp = imagecreatetruecolor($width, $height);
	
	$fondo = imagecreate($width,$height);
	$bg = imagecolorallocate($fondo, 255, 255, 255);
	imagecopy($img_temp, $fondo, 0, 0, 0, 0, $width, $height);
	
	$background = imagecolorallocate($img_temp, 255, 255, 255);
	
	ImageColorTransparent($img_temp, $background);
	
	imagealphablending($img_temp, $alphablending);
	
	imagesavealpha($img_temp, true);

	ImageCopyResampled(
		$img_temp,
		$img_old,
		($width/2)-($new_w/2), ($height/2)-($new_h/2), 0, 0,
		$new_w,
		$new_h,
		imagesx($img_old),
		imagesy($img_old)
	);
	
	

	if ($image_info['mime'] == 'image/gif') {
		 imagegif($img_temp); 
	}
	else if ($image_info['mime'] == 'image/png') {
		 imagepng($img_temp);
	} else if ($image_info['mime'] == 'image/jpeg') {
		 imagejpeg($img_temp,'',90);
	}
}

#echo"<pre>".print_r( $_GET ,1)."</pre>";

$path = "";

$imagen = $path.$_GET["image"];

if( file_exists( $imagen ) ):
	
	$size = preg_replace("@([^0-9]+)@i","",$_GET[size]);
	
	$rdio = preg_replace("@([^0-9:]+)@i","",$_GET[ratio]);
	
	$crop = preg_replace("@([^0-9]+)@i","",$_GET[crop]);	
	
	$mask = preg_replace("@([^0-9:]+)@i","",$_GET[mask]);
	
	resize($imagen,$size,$rdio,$crop,$mask);
	
else:
	
endif;


?>