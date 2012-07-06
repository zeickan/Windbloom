<?php

class WB_LOGIN {
	
var $is_login;

var $valid;
	
	function login(){
	
		$this->is_login = false;
		
		$this->valid = false;
		
		$this->user = rWrite(utf8_decode($_POST["user"]),"-");
		
		$this->pass = $_POST["pass"];
		
		$this->group = false;
		
		$this->msg = '';
		
		$this->cookies = array( "user" => "_wb_user",
								"pass" => "_wb_pass",
								"sess" => "_wb_sess",
								"expire" => time()+31536000
								);
								
		$this->valid_user = '';
		
		$this->valid_pass = '';
		
		$this->valid_id = '';
								
	}
	
	function verify(){
		
		if( stristr( $this->user , '@' ) ):
				$auth_field = "email";
		else:
				$auth_field = "user";
		endif;
					
		$exists = db::numRows("users","$auth_field='".antiI($this->user)."'");
		
		if( $exists ){
			
			$data = db::show("users","WHERE $auth_field LIKE '".antiI($this->user)."'");
			
			if( $this->is_login ): 
						 $password = self::ncrypt( $this->pass , $data[4] ); 
			else: 		 
						 $password = alphanumeric( $this->pass ); 
			endif;
			
			if( trim($password) == trim($data[3]) ){
				
				# Verificamos grupo
				
				if( $this->group ): #Si esta definido un grupo o varios (array)
					
						if( is_array($this->group) ){
						
							foreach( $this->group as $group ): if($group == $data[5]){ $g = true; } endforeach;
					
						} else {	
							
							if($this->group == $data[5]){ $g = true; }
							
						}
					
				else:	# Pasamos si no hay grupo
					 	$g = true; 
				endif;
				
				
				if($g):					
						$this->msg = "Usuario y Contrase&ntilde;a correctos.";
						$this->valid = true;
						$this->valid_user = $data[1];
						$this->valid_pass = $data[3];
				else:							
						$this->msg = "No tienes permisos para entrar.";	
				endif;	
			
			
			} 
			 else 
			{			
				$this->msg = "La contrase&ntilde;a no es correcta.";
			}				
			
		} 
		 else 
		{ 			
			$this->msg = "El usuario ingresado no esta registrado."; 
		}
		
		return $this->valid;
		
	}
	
	function ncrypt($pass,$crypt){		
		$c = md5($crypt);
		$p = md5($pass);
		
		$password = md5($c.$p);
		
		return $password;		
	}
	
	function logout(){
		
		setcookie( $this->cookies["user"] , "x" , time()-3600 );
		setcookie( $this->cookies["pass"] , "x" , time()-3600 );
		
		return true;
		
	}
	
	function auth(){
		
		if( $this->verify() ):
		
			if( $this->is_login ){
			
				setcookie( $this->cookies["user"] , $this->valid_user , $this->cookies["expire"] );
				setcookie( $this->cookies["pass"] , $this->valid_pass ,$this->cookies["expire"] );
								
				return true;
				
			} else {
			
			return true;
				
			}
		
		else:		
				return false;
		endif;
		
	}
	
	
}

/*

$wb->login = new login();

if( $_GET["action"] == "login" ){
	
	if( stristr( $_POST["user"] , '@' ) ):
			$wb->login->user = valid_email( utf8_decode($_POST["user"]) );
	else:
			$wb->login->user = rWrite(utf8_decode($_POST["user"]),"-");

	endif;
	
	
	$wb->login->pass = $_POST["pass"];
		
	$wb->login->is_login = true;		
	
	if( $wb->login->auth() ):
	
			$_wb_admin_cp = $_GET["admin"]?"admin/":"";
	
			header("location: ". $windbloom->site["url"].$_wb_admin_cp);
	
	endif;
	
} elseif( $_GET["action"] == "logout" ){

	if( $wb->login->logout() ){
		
		$_wb_admin_cp = $_GET["admin"]?"admin/":"";
		
		header("location: ". $windbloom->site["url"].$_wb_admin_cp);
		
	}
	
}

if( !empty($_COOKIE["_wb_user"]) && !empty($_COOKIE["_wb_pass"]) ){
	
$wb->login->is_login = false;
	
if( stristr( $_COOKIE["_wb_user"] , '@' ) ):
	$wb->login->user = valid_email( utf8_decode($_COOKIE["_wb_user"]) );
else:
	$wb->login->user = rWrite(utf8_decode($_COOKIE["_wb_user"]),"-");
endif;
	
$wb->login->pass = $_COOKIE["_wb_pass"];

$is_login = $wb->login->auth();



}

*/