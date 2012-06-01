<?php

	function resize($image,$n3wX,$position = "vertical") {
	
		 $Datos = getimagesize($image);
			 $Ancho=$Datos[0];
			 $Alto=$Datos[1];		 
			 
			 if(!empty($n3wX)){
				$newX = (int) $n3wX;
			 } else {
				$newX = "600";
			 }
			 
			 
			 if($vertical == "vertical"):
			 
				$AX = round(($newX*100)/$Alto);
			 
				 if($newX >= $Alto){
					$a1 = substr($AX,0,1);
					$a2 = substr($AX,-2);
					$ax = "$a1.$a2";
				 } else {		 
				 
					if($AX <= 9){
					  $ax = ".0$AX";
					  } else {
					   $ax = ".$AX";
					  }
				}
				 $newW = round($Ancho*$ax);
		
				 $new_w = $newX; 
				 $new_h = $newW;
			 
			 else:
			 
			 $AX = round(($newX*100)/$Ancho);
			 
			 if($newX >= $Ancho){
				$a1 = substr($AX,0,1);
				$a2 = substr($AX,-2);
				$ax = "$a1.$a2";
			 } else {		 
			 
				if($AX <= 9){
				  $ax = ".0$AX";
				  } else {
				   $ax = ".$AX";
				  }
			}
			 $newW = round($Alto*$ax);
	
			 $new_w = $newX; 
			 $new_h = $newW;
			 
			 endif;
			 
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
	
		$img_temp = imagecreatetruecolor($new_w, $new_h);
	
		$background = imagecolorallocate($img_temp, 0, 0, 0);
		ImageColorTransparent($img_temp, $background);
		imagealphablending($img_temp, $alphablending);
		imagesavealpha($img_temp, true);
	
		ImageCopyResampled(
			$img_temp,
			$img_old,
			0, 0, 0, 0,
			$new_w,
			$new_h,
			imagesx($img_old),
			imagesy($img_old)
		);
	
		if ($image_info['mime'] == 'image/gif') {
			 imagegif($img_temp,$image); 
		}
		else if ($image_info['mime'] == 'image/png') {
			 imagepng($img_temp,$image);
		} else if ($image_info['mime'] == 'image/jpeg') {
			 imagejpeg($img_temp,$image,100);
		}
	}


function create_image($img,$dir,$base_w = 400,$base_h = 400 , $savepath = "tmp/"){

## PATH IMAGES

$path = $dir;   

## FILENAME

$filename=urldecode($img);

## FILE EXISTS

if(file_exists($path.$filename)):
	
## GET IMAGE SIZE	
	
$image_info = getimagesize($path.$filename);

$Ancho=$image_info[0]; $Alto=$image_info[1];  $Tipo=$image_info[2];  

$Peso = filesize($path.$filename)/1024;  $Peso = round($Peso);
 

	if ($image_info['mime'] == 'image/gif') {
		$alphablending = true;
		$src_img = @imagecreatefromgif($path.$filename);
		imagealphablending($src_img, true);
		imagesavealpha($src_img, true);
	}
	elseif ($image_info['mime'] == 'image/png') {
		$alphablending = true;
		$src_img = @imagecreatefrompng($path.$filename);
		imagealphablending($src_img, true);
		imagesavealpha($src_img, true);
	} elseif($image_info['mime'] == 'image/jpeg'){
		$alphablending = true;
		$src_img = @imagecreatefromjpeg($path.$filename);
		imagealphablending($src_img, true);
		imagesavealpha($src_img, true);
	}
	
## VARIABLES

	$bw = $base_w;
	$bh = $base_h;

## Equation
   	$a = $Alto;
	$w = $Ancho;
	$n = $bh;
	$p = (($n*100)/$a)/100; 
	
	$m = ceil($w*$p);
		
	$new_w = $m; 
		
	$new_h = $n;
	
	$c = ceil( ($bw / 2) - ($m / 2) );

## CREATE BASE

 $dst_img = imagecreatetruecolor($bw, $bh);

 $background = imagecolorallocate($dst_img, 100, 100, 100);
  
 imagecolortransparent($dst_img, $background);
 imagealphablending($dst_img, $alphablending);
 imagesavealpha($dst_img, true);

 $barra = imagecreate($bw, $bh);
   
 $bg = imagecolorallocate($barra, 255, 255, 255);
 
 imagecopy($dst_img, $barra, 0, 0, 0, 0, $bw, $bh);

 $color = imagecolorallocate($dst_img, 50, 60, 70);
 
 // Switch antialiasing on for one image
 
 if(function_exists('imageantialias')){  imageantialias($src_img, true); }  
  
   
 # header('Content-Disposition:  attachment;  filename="vista_previa.jpg"');
 #header("Content-type: image/jpeg");
 
	
 imagecopyresampled(
	$dst_img,
	$src_img,
	$c, 0, 0, 0,
	$new_w,
	$new_h,
	imagesx($src_img),
	imagesy($src_img)
   );
	
	$ext = ".".str_replace(".","",substr($filename,-4));
	
	$name = str_replace($ext,"",$filename);
	
	$save =  $savepath."thumb-".$name.".jpg";
	
	#imagestring($dst_img, 2, 2, 2, "$Ancho $Alto / $m $n - $c | $save", $color);
	
	imagejpeg($dst_img,$save,100);
		
  
  imagedestroy($barra);
  #imagedestroy($bg);
  #imagedestroy($color);
  imagedestroy($src_img);
  imagedestroy($dst_img);
  
  return "thumb-".$name.".jpg";
    
  else:
  
  return "NO EXISTS";
  
  endif;
	
}



?>