<?php

class user {
	
	function user(){
		
		$this->data = '';
		
		$this->info = '';
		
		$this->meta = '';
			
	}
		
	function info($is_login = false , $return = "array", $reload = false ){
		
		global $user;
				
		if($is_login){			
			
			if( $reload ){
				
				$this->data = db::getData("users","*","WHERE user='".$is_login."'");
							
			} else {
				
				if($this->data):	
					$this->data = $this->data;
				else:
					$this->data = db::getData("users","*","WHERE user='".$is_login."'");
				endif;	
				
			}
			
			switch ($return) {
				case "id":
					$this->info = $this->data[0];
				break;
				case "user":
					$this->info = $this->data[1];
				break;
				case "email":
					$this->info = $this->data[2];
				break;
				case "group":
					$this->info = $this->data[5];
				break;
				case "rel":
					$this->info = $this->data[6];
				break;
				default:
					$this->info = $this->data;
				break;
			}			
			
		} 
	
	}
	
	function meta($id = false){		
		
		$id = $id?''.$id.'':''.$this->info[0].'';
		
		$meta = query::get("SELECT * FROM users_meta WHERE user_id='$id'",2);
		
		$this->meta = $meta;		
		
	}
	
	function avatar($size = false,$url = false){
		
		include "conf.global.php";
		
		$user = self::is_login();
		
		if($user){
			$data = db::getData("users_meta","avatar","WHERE user='$user'");
			
			if(!empty($data[0])){
			
			switch ($data[0]) {
				case "gravatar.com":
					$mail = db::getData("users","email","WHERE user='$user'");
					$path = "http://www.gravatar.com/avatar/".md5($mail[0]);
				break;
				default:
					$path = $data[0];
			}
			
			if($size){
				switch ($data[0]) {
					case "gravatar.com":
						$s = "?s=".$size;
					break;
					default:
						$s = "";
					
				}
				
				$sz = 'width="'.$size.'"';
			}
			
			if($url){
				echo $path;
			} else {
				echo'<img src="'.$path.$s.'" '.$sz.' border="0" />';	
			}
			
			}
			
		}
		
	}

	
	function login(){
		
		global $global;
		
				
		echo'
		
		<div style="float:right;">
		<form action="'.$global->site["url"].'?app=registro" method="post">
		<input name="registro" type="submit" value="registro" class="btn_reg"/>
		</form>
		</div>
		
		
		<form action="'.$global->site["url"].'" enctype="multipart/form-data" method="post" name="loginForm" id="loginForm">
		
		<input name="user" type="text" value="usuario" class="para_reg"/>
		<input name="pass" type="password" value="" class="para_reg"/>
		<input name="login" type="submit" value="Entrar" class="btn_reg"/>		
		
        </form>
		
		';
		
	}
	
	
	function new_messages(){
		
		include "conf.global.php";
		
		$user = self::info();
		
		$cont = db::numRows("message","xto='$user[0]' AND xread='1'");
		
		echo"<label>Mensajes sin leer:</label> $cont";
		
	}
	
	
	function register($group = 1){
		
		global $register_success;
		
		$register_success = false;
		
		$user = array();
			
			$user[] = rWrite($_POST["user_nick"],"-",true);
			
			$user[] = $_POST["user_pass"];
			
			$user[] = $_POST["user_cpass"];
			
			$user[] = valid_email($_POST["user_mail"]);
			
			$user[] = rWrite($_POST["user_nick"],"-",false);
			
			$meta = $_POST["meta"];
			
		
		if(valid_email($user[3],true)):
		#	
		
			if($user[1] == $user[2]):
				
				$exists = db::numRows("users","user='".$user[0]."'");
					
				if($exists <= 0):
				
						
					$u = $user[0];
					
					$m = $user[3];
					
					$p = $user[1];
					
					$c = randStr(5);
					
					$pass = login::ncrypt($p,$c);
					
					$exists_e = db::numRows("users","email='".$m."'");
					
					if($exists_e <= 0):
					
					
						$values = "'$u','$m','$pass','$c','$group','".$user[4]."'";
						
						db::mysqlInsert("users","user,email,pass,ncrypt,user_group,display",$values);
						
						$user_info = db::getData("users","*","WHERE user='$u' AND pass='$pass'");
				
						$profile = new meta;
							
						$profile->type= "users_";
							
						$profile->rel = "user_id";
							
						$profile->rel_value = $user_info[0];
						
						if( $meta ){
							
						foreach($meta as $name => $value){ 
							
							$shipping = $name."_SHIPPING";
							
							$profile->add_field(strtoupper(antiI($name)),htmlentities(antiI($value)));
							
							$profile->add_field(strtoupper(antiI($shipping)),htmlentities(antiI($value)));
							
						}
						
						
						$profile->new_meta();
						
						}
						
						$msj[] = "Registro exitoso.";
						
						#$msj = $meta;
						
						$register_success = true;
					
					else:
					
						$msj[] = "Ese email ya fue registrado.";
					
					endif;
					
				else:
						
					$msj[] = "El usuario esta ocupado, selecciona otro";
						
				endif;
				
			else:
				
				$msj[] = "Las contraseñas no coinciden.";
				
			endif;			
		
		else:
				
			$msj[] = "El E-Mail no es valido.";
				
		endif;
				
				
		return $msj;
		
	
	}
	
	

}

?>