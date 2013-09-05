<?php

/*
 
 # INICIO DE LA CLASE
  
$pdo = new db_pdo();
 
 # EJEMPLO DE SELECT MULTIPLE

$pdo->add_consult("SELECT * FROM tabla");
$pdo->add_consult("SELECT * FROM tabla2");

$query = $pdo->query();

 # EJEMPLO DE INSERT CON VALIDACION
 
if( $pdo->insert("demo", array( "demo" => 'xD', "varia" => 'lolaaa', "intec" => '101'  ) ) ){
    
    echo"EXITO";
    
} else {

    echo"ERROR: ". $pdo->error[2];

}

 # EJEMPLO DE UPDATE (DEVUELVE TRUE O FALSE)
 
$pdo->update("demo", // TABLE NAME
	     
	     // SET name     value
	     array("varia" => 'Zeickan',
		   "demo" => 'http://zeickan.com',		
		   "intec" => '1989'),
	     
	     // CONDITION (OPTIONAL)
	     array("id" => '1')			
	     );

 # EJEMPLO DE DELETE (DEVUELVE TRUE O FALSE)

$pdo->delete("demo", array("id" => '4')	 );

 # EJEMPLO DE CONTEO DE COLUMNAS CON IMPRESION DEL RESULTADO
 # (DEVUELVE EL VALOR NUMERICO)

$pdo->add_consult("SELECT * FROM tabla");

print_r( $pdo->numRows() );
 
*/

class db_pdo {

	function db_pdo(){
	
		$this->dsn = '';
		
		$this->connetion = array( "host" => DBHOST , "name" => DBNAME, "user" => DBUSER, "pass" => DBPASS );
		
		$this->consult = array();
		
		$this->query = array();
	
	}
	
	function getDSN($dsn){			
				
		preg_match_all('*(mysql://([a-zA-Z0-9]+)+:([a-zA-Z0-9]+)+@([a-zA-Z0-9./]+)+/([a-zA-Z0-9]+)+)*',$dsn,$db);
		
		$db[host] = $db[4][0];
		$db[name] = $db[5][0];
		$db[user] = $db[2][0];
		$db[pass] = $db[3][0];
		
		return $db;
		
	}
	
	function connect_db(){
	
		
		$db = $this->connetion;
		
		$pdo = new PDO('mysql:host='.$db[host].';dbname='.$db[name], 	// HOST Y BASE DE DATOS
								  $db[user], 	// USUARIO
								  $db[pass]	// CONTRASE?A				   
					   );
		
		return $pdo;
	}
	
	function add_consult( $consult, $exec = NULL ){
	
		$this->consult[] = array($consult,$exec);
	
	}
	
	function unset_consult(){
		
		unset( $this->consult );
		
		$this->consult = array();
		
	}
	
	function removenumber($array){
		
		if($array){
			
			for($i = 0; $i <= count($array); $i++ ){
				
				unset($array[$i]);
				
			}
			
		}
		
		return $array;
		
	}
	
	function query(){
		
		$pdo = self::connect_db();
		
		$response = array();
		
		while( list($l,$v) = each($this->consult) ){
		
			$query = $pdo->prepare($v[0]);
		
			$query->execute( $v[1] );			
			
			
			while($row = $query->fetch()) $response[$l][] = self::removenumber($row);
			
		}		 
		
		$query->closeCursor();	
		
		return $response;
	
	}
	
	function select( $table, $where = NULL, $order = NULL , $limit = NULL , $sel = '*' ){
		
		$select = "SELECT $sel FROM $table ";
		
		
		echo $select;
		
	}
	
	function insert( $table, $attr ){
		
		$attrib = array();
		$values = array();
		
		while( list($k,$v) = each($attr) ){
			
			$values[val][] = $k;
			$values[sub][] = ":".$k;			
			$attrib[":".$k] = $v;
			
		}
		
		$val = join(",",$values[val]);
		
		$sub = join(",",$values[sub]);		
		
		$pdo = self::connect_db();						
		
		$sql = "INSERT INTO $table ($val) VALUES ($sub)";
		
		$query = $pdo->prepare($sql);
		
		$query->execute( $attrib );
		
		$error = $query->errorInfo();
		
		if( $error[0] === '00000' ){ return true; }
		else { $this->error = $error; return false; }
	}
	
	
	function update( $table, $sql ,$condition = NULL, $conf = array( "where_sep" => 'AND' ) ){
			
		$set = array();
		
		while( list($k,$v) = each($sql)) $set[] = $k."='".$v."'";
		
		if( !is_null($condition) ):
			
		$add = ' WHERE '; $adds = array();
			
		$conds = array();
	
		while( list($k,$v) = each($condition)){		
			$conds[] = $v;
			$adds[]= "$k=?";		
		}
		
		$add = $add.join(" ".$conf[where_sep]." ",$adds);
		
		endif;	
		
		$sql = "UPDATE $table SET ".join(",",$set).$add;		
			
		$pdo = self::connect_db();	
			
		$query = $pdo->prepare($sql);
		
		$query->execute( $conds );
		
		$error = $query->errorInfo();
	
		if( $error[0] === '00000' ){ return true; }
		else { $this->error = $error; return false; }

		
	
	}
	
	function delete($table , $condition = NULL, $conf = array( "where_sep" => 'AND' )){
		
		if( !is_null($condition) ):
			
		$add = ' WHERE '; $adds = array();
			
		$conds = array();
	
		while( list($k,$v) = each($condition)){		
			$conds[] = $v;
			$adds[]= "$k=?";		
		}
		
		$add = $add.join(" ".$conf[where_sep]." ",$adds);
		
		endif;	
		
		$sql = "DELETE FROM $table".$add;		
				
		$pdo = self::connect_db();	
			
		$query = $pdo->prepare($sql);
		
		$query->execute( $conds );
		
		$error = $query->errorInfo();
		
				
		if( $error[0] === '00000' ){ return true; }
		else { $this->error = $error; return false; }
		
	}
	
	function numRows($array = NULL){
		
	if( is_null($array) ){
			
		$pdo = self::connect_db();
			
		$response = array();
		
		while( list($l,$v) = each($this->consult) ){
			
			$query = $pdo->prepare($v[0]);
			
			$query->execute( $v[1] );			
			
			$response[$l][] = $query->rowCount();
				
		}
		
		$this->response = $response;
			
			
	} else {
			
		$response = count($array);
		
	}	
	
		return $response;
	
	}

}


class query {

	function get($sql,$array_values = false){
		
		if(is_array($sql)){
		
			$table = $sql[0];
			
			$select = $sql[1]; if(empty($select)): $select = "*"; endif;
			
			$where = $sql[2];
			
			$order = $sql[3];
			
			$limit = $sql[4];
			
			
			if($where):
			
				$w = "WHERE ";
				
				if($where[0]): $w.= implode(" AND ",$where[0]); endif;
				
				if($where[1] AND $where[0]): $w.= " AND (".implode(" OR ",$where[1]).")"; elseif($where[1]): $w.= implode(" OR ",$where[1]);  endif;
				
			endif;
			
				
			if($order):
				$o = "ORDER BY ".implode(",",$order[0])." ".$order[1]; 
			endif;
			
			if($limit):
				
				$l = "LIMIT ";
				
				if(is_array($limit)): $l.= $limit[0].",".$limit[1]; else : $l.= $limit; endif;
			
			endif;
			
			$query = "SELECT ".$select." FROM $table $w $o $l";
		
		} else {
		
			$query = $sql;
		
		}
		
		
		$query = mysql_query($query);
		
		$final = array();
		
		if ( $array_values ):
		
			while($v = mysql_fetch_array($query)){
				
				$final[$v[$array_values]] = $v;
				
			}	
		
		else:	
		
			while($v = mysql_fetch_array($query)){
				
				$final[] = $v;
				
			}		
		
		endif;
		
		mysql_free_result($query);
		
		return $final;
		
	}
	
	function numRows($tabla,$cond = false,$sel = "id"){
		if($cond){
			$q = mysql_query("SELECT $sel FROM $tabla WHERE $cond");
		} else {
		$q = mysql_query("SELECT $sel FROM $tabla");
		}
		$t = mysql_num_rows($q);
		mysql_free_result($q);
		return $t;
	}
	
	function getData($table,$sel,$where = NULL,$order = NULL,$limit = NULL){
		$query = mysql_query("SELECT $sel FROM $table $where $order $limit");
		$v = mysql_fetch_row($query);
		mysql_free_result($query);
		return $v;
	}

}

class db {

	function get($sql,$array_values = false){
		
		if(is_array($sql)){
		
		$table = $sql[0];
		
		$select = $sql[1]; if(empty($select)): $select = "*"; endif;
		
		$where = $sql[2];
		
		$order = $sql[3];
		
		$limit = $sql[4];
		
		
		if($where):
		
			$w = "WHERE ";
			
			if($where[0]): $w.= implode(" AND ",$where[0]); endif;
			
			if($where[1] AND $where[0]): $w.= " AND (".implode(" OR ",$where[1]).")"; elseif($where[1]): $w.= implode(" OR ",$where[1]);  endif;
			
		endif;
		
			
		if($order):
			$o = "ORDER BY ".implode(",",$order[0])." ".$order[1]; 
		endif;
		
		if($limit):
			
			$l = "LIMIT ";
			
			if(is_array($limit)): $l.= $limit[0].",".$limit[1]; else : $l.= $limit; endif;
		
		endif;
		
		$query = "SELECT ".$select." FROM $table $w $o $l";
		
		} else {
		
		$query = $sql;
		
		}
		
		
		$query = mysql_query($query);
		
		$final = array();
		
		if ( $array_values ):
		
			while($v = mysql_fetch_array($query)){
				
				$final[$v[$array_values]] = $v;
				
			}	
		
		else:	
		
			while($v = mysql_fetch_array($query)){
				
				$final[] = $v;
				
			}		
		
		endif;
		
		mysql_free_result($query);
		
		return $final;
		
	}
	
	function show($tabla,$cond = NULL,$field = "*"){
		$q = mysql_query("SELECT $field FROM $tabla $cond");
		$t = mysql_fetch_row($q);
		mysql_free_result($q);
		return $t;
	}
	
	function numRows($tabla,$cond = false,$sel = "id"){
		if($cond){
			$q = mysql_query("SELECT $sel FROM $tabla WHERE $cond");
		} else {
		$q = mysql_query("SELECT $sel FROM $tabla");
		}
		$t = mysql_num_rows($q);
		mysql_free_result($q);
		return $t;
	}
	
	function mysqlInsert($tabla,$campos,$valores){
		$I = "INSERT INTO $tabla ($campos) VALUES ($valores)";
		mysql_query($I) or die(mysql_error());
		} 
	
	function mysqlUpdate($tabla,$SET,$condiciones){
		$U = "UPDATE $tabla SET $SET $condiciones";
		mysql_query($U) or die(mysql_error());
	}
	
	function mysqlDelete($tabla,$condiciones = false){
		if($condiciones){
			$D = "DELETE FROM $tabla WHERE $condiciones";
		} else {
			$D = "DELETE FROM $tabla";
			}
		mysql_query($D) or die(mysql_error());
	}
	
	function getData($table,$sel,$where = NULL,$order = NULL,$limit = NULL){
		$query = mysql_query("SELECT $sel FROM $table $where $order $limit");
		$v = mysql_fetch_row($query);
		mysql_free_result($query);
		return $v;
	}


}

?>