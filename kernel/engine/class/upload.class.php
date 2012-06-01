<?php

class upload {

var $fields = array();

	function upload(){
		
		$this->file = $_FILES["file"];
		
		$this->valid = array("jpg","jpeg","png","gif","bmp","zip","rar");
		
		$this->mime = array("image/jpeg",
							"image/png",
							"image/bmp",
							"image/gif",
							"application/force-download",
							"application/zip"
							);
		
		$this->path = "uploads/";
		
		$this->max_size = 1048576;
				
	}
	
	function add_field($name,$value){
	
		$this->fields["$name"] = $value;
	
	}
	
	function filename($str){
		
		preg_match("@(.*)([.]+?[a-zA-Z0-9]+)@",$str,$result);
		
		$result[1] = rWrite($result[1],"-",true);
		
		return $result;
	
	}
	
	function valid($file,$type){
		
		$filename = self::filename($file);
		
		$ext = str_replace(".","",strtolower($filename[2]));
		
		if( $this->valid === false ){
			
			#Valid file [ Any file ]
			$this->add_field("valid_msg","Success: Valid file");
			return true; 
			
		} else {
			
			if( search_in_array($ext,$this->valid,true) ){
								
				if( $this->mime === false ){
				
				# Valid file [ Any Mime ]
				$this->add_field("valid_msg","Success: Valid file");
				return true;
				
				} else {
					
					if( search_in_array($type,$this->mime,true) ){
						
					# Valid File 	
					$this->add_field("valid_msg","Success: Valid file");
					return true;
						
					} else {
						
					# Invalid File [ Invalid MIME ]
					$this->add_field("valid_msg","Error: Invalid type file");
					return false;
					
					}				
				}
			
			} else { 
			
			# Invalid File [ Invalid Filetype ]
			$this->add_field("valid_msg","Error: Invalid file extention");
			return false;
			
			}
			
		}
		
	}
	
	function process($file,$size,$tmp,$type){
		
		$this->add_field("uploaded_file",false);
		
		$this->add_field("upload_status","false");
	
		if( self::valid($file,$type) ){
		
			if( is_uploaded_file($tmp) ){
				
				$this->add_field("is_uploaded_file","Success: It's an uploaded file");
				
				if( $this->max_size >= $size ){
					
					$this->add_field("size_msg","Success: Ok");
					
					$filename = self::filename($file);
					
					if( move_uploaded_file( $tmp, $this->path.$filename[1].$filename[2] ) ){
					
					$this->add_field("upload_msg","Success: Upload successful");
					
					$this->add_field("upload_status","true");
					
					$this->add_field("uploaded_file",$filename[1].$filename[2]);
					
					return $this->fields["upload_msg"];
					
					} else {
					
					$this->add_field("upload_msg","Error: Error saving file");
					
					return $this->fields["upload_msg"];
					
					}
					
				} else {
					
					$this->add_field("size_msg","Error: File size limit exceeded");
					
					return $this->fields["size_msg"];
					
				}
				
			} else {
				
				$this->add_field("is_uploaded_file","Error: Not's an uploaded file");
				
				return $this->fields["is_uploaded_file"];
				
			}
		
		} else {		
		
		return $this->fields["valid_msg"];
		
		}
		
	
	}
	
	function submit(){
				
		if( is_array( $this->file["tmp_name"] ) ){
			
			$c = count($this->file["tmp_name"]);
			
			$uploaded_files = array();
			
			for($i = 0; $i < $c; $i++){
				
				if( !empty($this->file["tmp_name"][$i]) ){
					
				$upload = self::process($this->file["name"][$i],$this->file["size"][$i],$this->file["tmp_name"][$i],$this->file["type"][$i]) . "<br />";
				
				$file_name = $this->fields["uploaded_file"]?''.$this->fields["uploaded_file"].'':''.$this->file["name"][$i].'';
				
				$uploaded_files[] = array( $file_name , $upload );
				
				}
			
			}
			
			$this->add_field("uploaded_files", $uploaded_files);
		
		} else {			
		
			$upload = self::process($this->file["name"],$this->file["size"],$this->file["tmp_name"],$this->file["type"]);
			
			$file_name = $this->fields["uploaded_file"]?''.$this->fields["uploaded_file"].'':''.$this->file["name"].'';
			
			$this->add_field("uploaded_files", array( $file_name, $upload ));
			
		}
		
		return $this->fields["upload_status"];
			
	}
	
}

?>