<?php

/*
 * class API
 * @author Andros Romo <me@androsromo.com>
 * @copyright Copyright (c) 2013, Andros Romo
 * @version 1.0
 */

class api extends view {
	
    /*
     * __construct()
     * @param $arg
     */
    
    function __construct() {		
		parent::__construct($_GET['parent']);		
	}
	
	public function main(){        
        $str = $this->GenerateJson('ok',$json);
        echo $str;        
    }
	
	public function GetProviderByBank(){
		
		$sql = $this->Dictionary('provider',$this->id);
		
		if( $sql ){
			
			$json['message'] = "Success";
			$json['response'] = $this->encodeArray($sql);
            
			$str = $this->GenerateJson('ok',$json);
		
			echo $str;
			
		} else {
			
			$this->error('Sin resultados');
			
		}
		
	}
	
	public function GetEmploymentsByBank(){
		
		$sql = $this->Dictionary('employ',$this->id);
		
		if( $sql ){
			
			$json['message'] = "Success";
			$json['response'] = $this->encodeArray($sql);
            
			$str = $this->GenerateJson('ok',$json);
		
			echo $str;
			
		} else {
			
			$this->error('Sin resultados');
			
		}
		
	}
	
	public function GetRankByProvider(){
		
		$sql = $this->Dictionary('rank',$this->id);
		
		if( $sql ){
			
			$json['message'] = "Success";
			$json['response'] = $this->encodeArray($sql);
            
			$str = $this->GenerateJson('ok',$json);
		
			echo $str;
			
		} else {
			
			$this->error('Sin resultados');
			
		}
		
	}
	
	
	public function asignar(){
		
		if( $_GET['id'] ):
		
		if( $_GET['estatus'] != 1 ){
			
			if( $_GET['asesor'] != 2 ){
			
				$pdo = new db_pdo();
				
				 session_start();
        
			    $id_user = numeric($_SESSION['username']['id']);
				
				if( $pdo->update("prospectos", // TABLE NAME						
								// SET name     value
								array("validator_id" => numeric($_GET['asesor']),
									  "status" => numeric($_GET['estatus']) ,
									  "asignador_id" => $id_user ),
								
								// CONDITION (OPTIONAL)
								array("id" => numeric($_GET['id']))			
								) ){
    
					$json['message'] = "Success";
					$json['response'] = "Asignado con éxito";
					
					$str = $this->GenerateJson('ok',$json);
					
					$this->sendMessage("Citame","Prospecto para cita",numeric($_GET['asesor']),null,"asesor");
				
					echo $str;
					
				} else {				
					$this->error( "ERROR: ". $pdo->error[2]);
				}
				
			} else {
				$this->error('Incorrect Asessor');	
			}
			
		} else {			
			$this->error('Incorrect status');				
		}
		
		else:
			$this->error('Unknown ID');				
		endif;
		
	}
	
	
	public function citar(){
		
		if( $_GET['id'] ):
		
		$pdo = new db_pdo();
		
			if( $_GET['estatus'] == 3 ){
				
				
				if( $pdo->update("prospectos", // TABLE NAME						
								// SET name     value
								array("cita" => numeric($_GET['estatus'])
									  ),
								
								// CONDITION (OPTIONAL)
								array("id" => numeric($_GET['id']))			
								) ){
	
					$json['message'] = "redirect";
					$json['response'] = "Citado con éxito";
					
					$str = $this->GenerateJson('ok',$json);
					
					$this->sendMessage("Segunda llamada","Marcale para confirmar",null,8,"telemarketing",numeric($_GET['id']));
				
					echo $str;
					
				} else {				
					$this->error( "ERROR: ". $pdo->error[2]);
				}
			
			} elseif(  $_GET['estatus'] == 2 ) {
						
						
				if( $pdo->update("prospectos", // TABLE NAME						
								// SET name     value
								array("cita" => numeric($_GET['estatus']),
								      "cita_negativas" => 1
									  ),
								
								// CONDITION (OPTIONAL)
								array("id" => numeric($_GET['id']))			
								) ){
	
					$json['message'] = "Success";
					$json['response'] = "Reprograma con éxito";
					
					
					
					$str = $this->GenerateJson('ok',$json);
				
					echo $str;
					
				} else {				
					$this->error( "ERROR: ". $pdo->error[2]);
				}	
			
			} else {			
				$this->error('Incorrect status');				
			}
			
		else:
			$this->error('Unknown ID');				
		endif;
		
	}
	
	
	public function GetSubproovedor(){
		
		$sql = $this->Dictionary('cita_subproveedor',$this->id);
		
		if( $sql ){
			
			$json['message'] = "Success";
			$json['response'] = $this->encodeArray($sql);
            
			$str = $this->GenerateJson('ok',$json);
		
			echo $str;
			
		} else {
			
			$this->error('Sin resultados');
			
		}
		
	}
	
	
	public function GetDesarrollo(){
		
		$sql = $this->Dictionary('plaza_desarrollo',$this->id);
		
		if( $sql ){
			
			$json['message'] = "Success";
			$json['response'] = $this->encodeArray($sql);
            
			$str = $this->GenerateJson('ok',$json);
		
			echo $str;
			
		} else {
			
			$this->error('Sin resultados');
			
		}
		
	}
	
	
	public function GetPrototipo(){
		
		$sql = $this->Dictionary('plaza_prototipos',$this->id);
		
		if( $sql ){
			
			$json['message'] = "Success";
			$json['response'] = $this->encodeArray($sql);
            
			$str = $this->GenerateJson('ok',$json);
		
			echo $str;
			
		} else {
			
			$this->error('Sin resultados');
			
		}
		
	}
	
	public function GetMetros(){
		
		$sql = $this->Dictionary('plaza_metros',$this->id);
		
		if( $sql ){
			
			$json['message'] = "Success";
			$json['response'] = $this->encodeArray($sql);
            
			$str = $this->GenerateJson('ok',$json);
		
			echo $str;
			
		} else {
			
			$this->error('Sin resultados');
			
		}
		
	}
	
	public function GetPrecios(){
		
		$pdo = new db_pdo();
		
		$metros = numeric($_GET["metros"]);
		$bancos = numeric($_GET["banco"]);
		$plaza = numeric($_GET["plaza"]);
		
		$pdo->add_consult("SELECT * FROM dictionary_plaza_precios WHERE meta_key='$metros' AND meta_value LIKE '%$bancos%' AND  inherit='$plaza' ");
		
		$query = $pdo->query();
		
		$sql = $query[0][0];
				
		if( $sql ){
			
			$json['message'] = "Success";
			$json['response'] =  numeric($query[0][0]['description']);
            
			$str = $this->GenerateJson('ok',$json);
		
			echo $str;
			
		} else {
			
			$this->error('Sin resultados');
			
		}
		
	}
	
	
	public function lsdir(){
			
        session_start();
		
        $path = "uploads/".$_SESSION['uploads_id']."";
		
		$ls = ls($path);
		
		if( $ls ){
			
			$l = array();
			foreach( $ls  as $r ){
				
				if( $r != "." ){
					if( $r != ".." ){
					$l[] = $r;
					}
				}
				
			}
			
			$json['message'] = "Success";
			$json['response'] =  array("files" => $l,"path"=>$path.'/');
            
			$str = $this->GenerateJson('ok',$json);
		
			echo $str;
			
		} else {
			
			$this->error('Sin resultados');
			
		}
	}
	
}