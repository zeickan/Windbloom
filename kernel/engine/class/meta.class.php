<?php

$meta;

class meta {
	
	var $keys = array();
	
	var $type;
	
	function meta(){
	
		$this->type = '';
		
		$this->rel = '';
		
		$this->rel_value = '';
		
		$this->prefix = '_meta';
		
		$this->lkey = 'meta_key';
		
		$this->lvalue = 'meta_value';
	
	}
	
	function add_field($field, $value) {
            
      $this->keys["$field"] = $value;
	  
   }
	
	function new_meta(){
	
		$table = $this->type . $this->prefix;
		
		foreach($this->keys as $name => $value){
						
			db::mysqlInsert($table,"".$this->rel.",".$this->lkey.",".$this->lvalue."","'".$this->rel_value."','$name','$value'");
			
		}		
	
	}
	
	function debug(){
	
		$table = $this->type . $this->prefix;
		
		foreach($this->keys as $name => $value){
						
			#db::mysqlInsert($table,"".$this->rel.",".$this->lkey.",".$this->lvalue."","'".$this->rel_value."','$name','$value'");
			
			echo"Table: $table <br /> ";
			echo"Atribu: ".$this->rel.",".$this->lkey.",".$this->lvalue." <br />";
			echo"Values: '".$this->rel_value."','$name','$value' <br /><br />";
			
		}		
	
	}
	
	function update_meta(){
		
		$table = $this->type . $this->prefix;
		
		foreach($this->keys as $name => $value){
						
			db::mysqlUPDATE($table,"".$this->lvalue."='$value'","WHERE ".$this->rel."='".$this->rel_value."' AND ".$this->lkey."='$name'");
			
		}		
		
	}
	
	function get_meta(){
	
		$table = $this->type . $this->prefix;
		
		$sql = db::get(array($table,
							 "*",
							 array(
								   array($this->rel."='".$this->rel_value."'")
								   ), 
							 array(
							 		array("id"),
									"ASC"),
							false
						 )
					   ,2);
		
		return $sql;
	
	
	}
	
	function sql($table,$where,$o = NULL){
		
		$result = array();
		
		$sql = mysql_query("SELECT * FROM $table $where");
		
			while($row = mysql_fetch_array($sql)){
			
				$result[$row["meta_key"]] = $row;
			
			}
		
		mysql_free_result($sql);
		
		return $result;
		
	}
	
}

?>