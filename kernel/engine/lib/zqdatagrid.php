<?php
    class ZqDatagrid{

        function __construct($mode, $id, $useLog) {
            
            switch(strtoupper($mode))
            {
                case "MYSQL":
                    include_once("extensions/mysql.php");
                    break;
                    
                case "ODBC":
                    include_once("extensions/odbc.php");
                    break;
            };
            
            //Construct array in session.
            if(empty($_SESSION))
                @session_start();
                
            if(!array_key_exists("ZqDatagrid", $_SESSION))
                $_SESSION["ZqDatagrid"] = Array();
            
            if(!array_key_exists($id, $_SESSION["ZqDatagrid"])){
                //Create session structure
                $_SESSION["ZqDatagrid"][$id] = Array();
                $_SESSION["ZqDatagrid"][$id]["pk"] = Array();
                $_SESSION["ZqDatagrid"][$id]["files"] = Array();
            };
            
            //Pivot id
            $this->id = $id;
            
            //Log
            $this->useLog = $useLog;
            
            //Start and end column and table slashes
            if(ZQ_TYPE == "MYSQL")
            {
                $this->startSlash = "`";
                $this->endSlash = "`";
            }; 
            
            if(ZQ_TYPE == "MSSQL")
            {
                $this->startSlash = "[";
                $this->endSlash = "]";
            };
            
            if(ZQ_TYPE == "PGSQL" || ZQ_TYPE == "ORACLE")
            {
                $this->startSlash = "\"";
                $this->endSlash = "\"";
            };
            
            //Create dir
            if(defined("ZQ_FILE_REPOSITORY_PATH") && defined("ZQ_FILE_URL_PREFIX"))
            {
                $this->imageRepositoryPath = ZQ_FILE_REPOSITORY_PATH;
                $this->imageURLPrefix = ZQ_FILE_URL_PREFIX;
                
                if(!is_dir(ZQ_FILE_REPOSITORY_PATH))
                    mkdir(ZQ_FILE_REPOSITORY_PATH, 0777, true);
            };
        }
        
        public $username = null;
        public $hostname = null;
        public $password = null;
        public $database = null;
        public $useLog = false;
        
        public $action = null;
        public $colNames = null;
        private $id = null;
        public $orderBy = null;
        public $pk = null;
        public $fk = null;
        public $fkop = null;
        public $table = null;
        public $where = null;
        public $overrideWhere = null;
        public $colType = null;
        
        public $imageRepositoryPath = null;
        public $imageURLPrefix = null;
        
        private $errorNumber = 0;
        private $errorMessage = "";
        
        private $startSlash = "";
        private $endSlash = "";
        
        private $db;
        
        /* Public Methods */
        
        /* Add a new file to session pointer */
        public function addFile($file, $pk = "", $table){
            return $this->upload("file", ZQ_FILE_REPOSITORY_PATH, "", 100, $table);
        }
        
        //Uploads a temporary file
        private function upload($file_id, $folder = "", $types = "", $maxWidth = 100, $table = "") {
            if(!$_FILES[$file_id]['name']) return array('','No file specified');

            $file_title = $_FILES[$file_id]['name'];
            
            //Get file extension
            $ext_arr = explode(".",basename($file_title));
            $ext = strtolower($ext_arr[count($ext_arr)-1]); //Get the last extension
            
            //Not really uniqe - but for all practical reasons, it is
            $uniqer = substr(md5(uniqid(rand(),1)),0,5);
            $file_name = $table . "_" . $uniqer . '_' . $file_title;//Get Unique Name

            $all_types = explode(",",strtolower($types));
            if($types) {
                if(in_array($ext,$all_types));
                else {
                    $result = "'".$_FILES[$file_id]['name']."' is not a valid file."; //Show error if any.
                    return array('',$result);
                }
            }

            //Where the file must be uploaded to
            if($folder) $folder .= '/';//Add a '/' at the end of the folder
            $uploadfile = $folder . $file_name;

            $result = '';
            //Move the file from the stored location to the new location
            if (!move_uploaded_file($_FILES[$file_id]['tmp_name'], $uploadfile)) {
                $result = "Cannot upload the file '".$_FILES[$file_id]['name']."'"; //Show error if any.
                if(!file_exists($folder)) {
                    $result .= " : Folder don't exist.";
                } elseif(!is_writable($folder)) {
                    $result .= " : Folder not writable.";
                } elseif(!is_writable($uploadfile)) {
                    $result .= " : File not writable.";
                }
                $file_name = '';
                
            } else {
                if(!$_FILES[$file_id]['size']) { //Check if the file is made
                    @unlink($uploadfile);//Delete the Empty file
                    $file_name = '';
                    $result = "Empty file found - please use a valid file."; //Show the error message
                } else {
                    chmod($uploadfile,0777);//Make it universally writable.
                }
            }
            
            //Check if it's an image or file. If it's an image, create thumbnail to be shown.
            $imgExts = Array("png", "jpg", "gif");
            if(in_array($ext, $imgExts))
            {
                //It's an image!
                $image = new SimpleImage();
                $image->load($folder . $file_name);
                $image->resizeToWidth($maxWidth);
                $newFilename = substr($file_name, 0,strrpos($file_name,'.'));
                $newFilename = $newFilename . "_thumb." . $ext;
                $image->save($folder . $newFilename);                
                $file_name = $newFilename;
            };
            
            return array($file_name,$result);
        }
        
        function getFilePath(){
            $ret = Array();
            $ret["error"] = Array();
            $ret["error"]["number"] = 0;
            $ret["error"]["message"] = "";
            $ret["result"] = ZQ_FILE_URL_PREFIX;
            
            $php = $this->phpVersion();
            if($php[0] == 5 && $php[1] >= 3)
                echo json_encode($ret, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
            else
                echo json_encode($ret);
       }
        
        private function phpVersion(){
            $php = phpversion();
            $php = explode(".", $php);
            return $php;
        }
        
        /* Checks  */
        public function getTableValues($table, $language = ""){
             
            global $ZQ_FK_TABLE;
            
            $sql = "select id ";
            $sql .= ", " . $this->startSlash . $language . $this->endSlash . " description ";
            $sql .= "from " . $this->startSlash . $table . $this->endSlash . " ";
            $sql .= "order by id asc";

            //Check ZQ_FK_TABLE
            if(isset($ZQ_FK_TABLE)){
                $fkTable = $ZQ_FK_TABLE;
                foreach($fkTable as $key => $pivotTable){
                    if($key == $table){
                        $sql = "select " . $this->startSlash . $pivotTable[0] . $this->endSlash . " id ";
                        $sql .= ", " . $this->startSlash . $pivotTable[1] . $this->endSlash . " description ";
                        $sql .= "from " . $this->startSlash . $table . $this->endSlash . " ";
                        $sql .= "order by id asc";
                    };
                };
            };
            
            //Create DB connection
            $this->db = new doDB($this->hostname, $this->username, $this->password, $this->database, $this->id, $this->startSlash, $this->endSlash, $this->pk, $this->table, ZQ_TYPE);
            
            $result = Array();
            
            //Execute query and fetch result (object, 2 parameters)
            $result = $this->db->execute($sql, $this->useLog);
            
            $ret = Array();
            $ret["error"] = Array();
            $ret["error"]["number"] = 0;
            $ret["error"]["message"] = "";
            $ret["result"] = $result->result;
            
            $php = $this->phpVersion();
            
            if($php[0] == 5 && $php[1] >= 3)
                echo json_encode($ret, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
            else
                echo json_encode($ret);
            $this->db->close();
        }
        
        /* Refresh data (normally called on "refresh" button or at init event) */
        public function refresh(){
            global $language;
            global $ZQ_FK_TABLE;
            
            //Reset session array
            if(!array_key_exists("ZqDatagrid", $_SESSION))
                $_SESSION["ZqDatagrid"] = Array();
            
            //Create session structure
            $_SESSION["ZqDatagrid"][$this->id] = Array();
            $_SESSION["ZqDatagrid"][$this->id]["pk"] = Array();
            $_SESSION["ZqDatagrid"][$this->id]["files"] = Array();
            
            //Join columns
            $cols = "";
            
            $i = 0;
            foreach($this->colNames as $column)
            {
                if(count($this->colType)-1 > $i && $i > 0)
                {
                    if($this->colType[$i] == "image")
                        $cols .= "concat('" . ZQ_FILE_URL_PREFIX . "', " . $this->startSlash . $column . $this->endSlash . ", ";
                    else
                        $cols .= $this->startSlash . $column . $this->endSlash . ", ";
                }
                else
                    $cols .= $this->startSlash . $column . $this->endSlash . ", ";
                
                $i++;
            };
            
            $cols = substr($cols,0,-2);
            
            $sql = "select $cols from " . $this->startSlash . $this->table . $this->endSlash . " WHERE 1='1' ";
            
            if($this->getWhere() != null)
                $sql .= $this->getWhere();
                
            //Check FK columns and values, if they exist
            if(is_array($this->fk))
                if(count($this->fk) > 0)
                {
                    if(empty($this->fkop))
                        $op = "and";
                    else
                        $op = $this->fkop;
                        
                    $fkcol = array_keys($this->fk);
                    
                    for($i = 0 ; $i < count($fkcol); $i++)
                        $sql .= $op . " " . $this->startSlash . $fkcol[$i] . $this->endSlash . " = '" . $this->fk[$fkcol[$i]] . "'";
                };
            
            if(!empty($this->orderBy)){
                //Check if the word "desc" or "asc" comes at the end.
                $ob = explode(' ', $this->orderBy);
                if(count($ob) == 1)
                    $sql .= " order by " . $this->startSlash . $this->orderBy . $this->endSlash; 
                else
                    $sql .= " order by " . $this->startSlash . $ob[0] . $this->endSlash . " " . $ob[1]; 
            };
                
                
            //Create DB connection
            $this->db = new doDB($this->hostname, $this->username, $this->password, $this->database, $this->id, $this->startSlash, $this->endSlash, $this->pk, $this->table, ZQ_TYPE);
            
            $result = Array();
            
            if(function_exists("ZqBeforeRefresh"))
                ZqBeforeRefresh($this->table, $this->db);
            
            //Execute query and fetch result (object, 2 parameters)
            $result = $this->db->execute($sql, $this->useLog);
            
            if(function_exists("ZqAfterRefresh"))
                ZqAfterRefresh($this->table, $this->db);
            
            //Check image type and if it's not empty add the url prefix
            //Create index with column type = image
            $indexImage = Array();
            $indexDisplay = Array();
            
            for($i = 0 ; $i < count($this->colType); $i++)
                if($this->colType[$i] == "image")
                    $indexImage[] = ($i+1); //+1 due to the first $result->result column is always the PK
                    
            //Check the display|fk|<table> formatter
            for($i = 0 ; $i < count($this->colType); $i++)
                if(strtolower(substr($this->colType[$i],0,11)) == "display|fk|")
                    $indexDisplay[] = ($i+1); //+1 due to the first $result->result column is always the PK
                    
            //Find image and display|fk type columns, and place the prefix if an image filename is detected
            for($i = 0 ; $i < count($result->result) ; $i++)
            {
                $key = array_keys($result->result[$i]);
                
                for($j = 0 ; $j < count($indexImage) ; $j++)
                    if($result->result[$i][$key[$indexImage[$j]]] != "")
                        $result->result[$i][$key[$indexImage[$j]]] = ZQ_FILE_URL_PREFIX . $result->result[$i][$key[$indexImage[$j]]];
                    else
                        $result->result[$i][$key[$indexImage[$j]]] = "";
                        
                for($j = 0 ; $j < count($indexDisplay) ; $j++)
                    if($result->result[$i][$key[$indexDisplay[$j]]] != ""){
                        
                        $formatter = explode("|",$this->colType[$indexDisplay[$j]-1]);
                        $destFKTable = $formatter[2];
                        
                        //Select the respective description from the FK table
                        //Standard query
                        $sql = "select id ";
                        if($language != "")
                            $sql .= ", " . $this->startSlash . $language . $this->endSlash . " description ";
                        else
                            $sql .= ", description ";
                        $sql .= "from " . $this->startSlash . $destFKTable . $this->endSlash . " ";
                        $sql .= "where " . $this->startSlash . "id" . $this->endSlash . " = " . $result->result[$i][$key[$indexDisplay[$j]]];
                        
                        //Or not...
                        if(isset($ZQ_FK_TABLE)){
                            $fkTable = $ZQ_FK_TABLE;
                            foreach($fkTable as $key2 => $pivotTable){
                                if($key2 == $destFKTable){
                                    $sql = "select " . $this->startSlash . $pivotTable[1] . $this->endSlash . " description ";
                                    $sql .= "from " . $this->startSlash . $destFKTable . $this->endSlash . " ";
                                    $sql .= "where " . $this->startSlash . $pivotTable[0] . $this->endSlash . " = " . $result->result[$i][$key[$indexDisplay[$j]]];
                                };
                            };
                        };
                        
                        //Create DB connection
                        $this->dbFK = new doDB($this->hostname, $this->username, $this->password, $this->database, $this->id, $this->startSlash, $this->endSlash, $this->pk, $this->table, ZQ_TYPE);
                        $resultFK = Array();
                        $resultFK = $this->db->execute($sql, $this->useLog);
                        $resultValue = $resultFK->result[0]['description'];;
                        $result->result[$i][$key[$indexDisplay[$j]]] = $resultValue;
                    }
                    else
                        $result->result[$i][$key[$indexDisplay[$j]]] = "";
            };
            
            /*
            if(isset($ZQ_FK_TABLE)){
                $fkTable = $ZQ_FK_TABLE;
                foreach($fkTable as $key => $pivotTable){
                    if($key == $table){
                        $sql = "select " . $this->startSlash . $pivotTable[0] . $this->endSlash . " id ";
                        $sql .= ", " . $this->startSlash . $pivotTable[1] . $this->endSlash . " description ";
                        $sql .= "from " . $this->startSlash . $table . $this->endSlash . " ";
                        $sql .= "order by id asc";
                    };
                };
            */
            
            $ret = Array();
            $ret["error"] = Array();
            $ret["error"]["number"] = 0;
            $ret["error"]["message"] = "";
            $ret["result"] = $result->result;
            
            //Load primary keys to session
            foreach($result->result as $line)
                $_SESSION["ZqDatagrid"][$this->id]["pk"][] = $line[$this->pk];
                
            $php = $this->phpVersion();
            if($php[0] == 5 && $php[1] >= 3)
                echo json_encode($ret, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
            else
                echo json_encode($ret);
            $this->db->close();
        }
        
        /* Update data on table (called when user click the UPDATE button/image) */
        public function update($lines){
            
            /*
            For each line we check:
            - If [0] is empty, then it's a new line. We need to INSERT data.
            - Then, we run the pk's that are on session.
              - If [0] exists also, we UPDATE. If not, we DELETE (session pk);
            */
           
            //Grab session pk's
            //Purge repeated pk's
            $_SESSION["ZqDatagrid"][$this->id]["pk"] = array_unique($_SESSION["ZqDatagrid"][$this->id]["pk"]);
            $pks = $_SESSION["ZqDatagrid"][$this->id]["pk"];
            
            //Create DB connection
            $this->db = new doDB($this->hostname, $this->username, $this->password, $this->database, $this->id, $this->startSlash, $this->endSlash, $this->pk, $this->table, ZQ_TYPE);
            
            //Insert new lines
            $pkclient = Array();
            $pkgenerated = Array();
            
            //INSERT actions
            for($i = 0 ; $i < count($lines) ; $i++)
            {
                if(!$this->emptyLine($lines[$i]))
                {
                    $line = $lines[$i];
                    if(empty($line[0]))
                    {
                        if(function_exists("ZqBeforeInsert"))
                            ZqBeforeInsert($this->table, $this->colNames, $line, $this->db);
                            
                        $pk = $this->db->execute($this->getInsertSQL($line, $this->fk), $this->useLog);
                        $pkgenerated[] = $pk;
                        
                        if(function_exists("ZqAfterInsert"))
                            ZqAfterInsert($this->table, $pk, $this->db);
                    };
                        
                    $pkclient[] = $line[0];
                };
            };
            
            //DELETE or UPDATE actions
            for($i = 0 ; $i < count($pks) ; $i++)
            {
                if(!in_array($pks[$i], $pkclient))
                {
                    if(function_exists("ZqBeforeDelete"))
                        ZqBeforeDelete($this->table, $pks[$i], $this->db);

                    $this->db->execute($this->getDeleteSQL($pks[$i]), $this->useLog, $this->pk, $this->startSlash, $this->endSlash, $this->table);
                    
                    if(function_exists("ZqAfterDelete"))
                        ZqAfterDelete($this->table, $pks[$i], $this->db);

                    unset($_SESSION["ZqDatagrid"][$this->id]["pk"][$i]);
                }
                else
                {
                    if(function_exists("ZqBeforeUpdate"))
                        ZqBeforeUpdate($this->table, $pks[$i], $this-db);
                    
                    $this->db->execute($this->getUpdateSQL($this->getLineWithPK($pks[$i], $lines)), $this->useLog, $this->pk, $this->startSlash, $this->endSlash, $this->table);
                    
                    if(function_exists("ZqAfterUpdate"))
                        ZqAfterUpdate($this->table, $pks[$i], $this->db);
                };
            };
            
            //Reindex array
            $_SESSION["ZqDatagrid"][$this->id]["pk"] = array_values(array_merge($_SESSION["ZqDatagrid"][$this->id]["pk"], $pkgenerated));
            
            //Transfer new primary keys, to be added to datagrid
            $ret = Array();
            $ret["error"] = Array();
            $ret["error"]["number"] = 0;
            $ret["error"]["message"] = "";
            $ret["newpks"] = $pkgenerated;
            
            $php = $this->phpVersion();
            if($php[0] == 5 && $php[1] >= 3)
                echo json_encode($ret, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_QUOT|JSON_HEX_AMP);
            else
                echo json_encode($ret);
            
            unset($this->db);
        }
        
        private function emptyLine($line){
            $isEmpty = true;
            foreach($line as $cell)
                if(!empty($cell))
                    $isEmpty = false;
                    
            return $isEmpty;
        }
        
        //Get line give a PK
        private function getLineWithPK($pk, $lines){
            foreach($lines as $line)
                if($line[0] == $pk)
                    return $line;
                    
            return null;
        }
        
        //Get SQL Update
        private function getUpdateSQL($line){
            $sql = "update " . $this->startSlash . $this->table . $this->endSlash . " set ";
            
            //Id is auto-increment
            for($i = 1 ; $i < count($line) ; $i++){
                $sql .= $this->startSlash . $this->colNames[$i] . $this->endSlash . " = '" . utf8_decode(mysql_real_escape_string(stripslashes($line[$i]))) . "',";
            };
                
            $sql = substr($sql,0,-1);
            $sql .= " where " . $this->startSlash . $this->pk . $this->endSlash . " = '" . $line[0] . "'";
            
            return $sql;
        }
        
        //Get SQL Insert
        private function getInsertSQL($line, $fk){
            $sql = "insert into " . $this->startSlash . $this->table . $this->endSlash . " (";

            $i = 0;
            //Create column names with PK at the beginning
            foreach($this->colNames as $colname)
            {
                if($i == 0)
                    $sql .= $this->startSlash . $this->pk . $this->endSlash . ", ";
                else
                    $sql .= $this->startSlash . "$colname" . $this->endSlash . ", ";
                $i++;
            };
            
            //Check foreign keys
            if(is_array($this->fk))
                if(count($this->fk) > 0)
                {
                    $fkcol = array_keys($this->fk);
                    
                    for($i = 0 ; $i < count($fkcol); $i++)
                        $sql .= $this->startSlash . $fkcol[$i] . $this->endSlash . ", ";
                };
            
            $sql = substr($sql, 0, -2) . ") VALUES (";

            $i = 0;
            foreach($line as $data){
                //First column has the id
                if($i == 0)
                    $sql .= $this->db->getNextPk() . ", ";
                if($i > 0)
                    $sql .= "'" . utf8_decode(mysql_real_escape_string(stripslashes($data))) . "', ";
                $i++;
            };
            
            //Check foreign keys
            if(is_array($this->fk))
                if(count($this->fk) > 0)
                {
                    $fkcol = array_keys($this->fk);
                    
                    for($i = 0 ; $i < count($fkcol); $i++)
                        $sql .= "'" . $this->fk[$fkcol[$i]] . "', ";
                };
            
            $sql = substr($sql, 0, -2) . ")";

            return $sql;
        }
        
        //Get SQL Delete
        private function getDeleteSQL($pk)
        {
            $sql = "delete from " . $this->startSlash . $this->table . $this->endSlash . " where " . $this->startSlash . $this->pk . $this->endSlash . " = '" . mysql_real_escape_string(stripslashes($pk)) . "'";
            return $sql;
        }
        
        //Get where clause
        public function getWhere(){
            if(empty($this->overrideWhere))
                return $this->where;
            else
                return $this->overrideWhere;
        }
    }

    /*
    * Author: Simon Jarvis
    * Copyright: 2006 Simon Jarvis
    * Date: 08/11/06
    * Link: http://www.white-hat-web-design.co.uk/articles/php-image-resizing.php
    *
    * This program is free software; you can redistribute it and/or
    * modify it under the terms of the GNU General Public License
    * as published by the Free Software Foundation; either version 2
    * of the License, or (at your option) any later version.
    *
    * This program is distributed in the hope that it will be useful,
    * but WITHOUT ANY WARRANTY; without even the implied warranty of
    * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
    * GNU General Public License for more details:
    * http://www.gnu.org/licenses/gpl.html
    *
    */
     
    class SimpleImage {
     
       var $image;
       var $image_type;
     
       function load($filename) {
     
          $image_info = getimagesize($filename);
          $this->image_type = $image_info[2];
          if( $this->image_type == IMAGETYPE_JPEG ) {
     
             $this->image = imagecreatefromjpeg($filename);
          } elseif( $this->image_type == IMAGETYPE_GIF ) {
     
             $this->image = imagecreatefromgif($filename);
          } elseif( $this->image_type == IMAGETYPE_PNG ) {
     
             $this->image = imagecreatefrompng($filename);
          }
       }
       
       function save($filename, $image_type=IMAGETYPE_JPEG, $compression=90, $permissions=null) {
                                         
          if( $image_type == IMAGETYPE_JPEG ) {
             imagejpeg($this->image,$filename,$compression);
          } elseif( $image_type == IMAGETYPE_GIF ) {
     
             imagegif($this->image,$filename);
          } elseif( $image_type == IMAGETYPE_PNG ) {
     
             imagepng($this->image,$filename);
             
          }
          if( $permissions != null) {
     
             chmod($filename,$permissions);
          }
       }
       
       function output($image_type = IMAGETYPE_JPEG) {
     
          if( $image_type == IMAGETYPE_JPEG ) {
             imagejpeg($this->image);
          } elseif( $image_type == IMAGETYPE_GIF ) {
     
             imagegif($this->image);
          } elseif( $image_type == IMAGETYPE_PNG ) {
     
             imagepng($this->image);
          }
       }
       
       function getWidth() {
     
          return imagesx($this->image);
       }
       
       function getHeight() {
     
          return imagesy($this->image);
       }
       
       function resizeToHeight($height) {
     
          $ratio = $height / $this->getHeight();
          $width = $this->getWidth() * $ratio;
          $this->resize($width,$height);
       }
     
       function resizeToWidth($width) {
          $ratio = $width / $this->getWidth();
          $height = $this->getheight() * $ratio;
          $this->resize($width,$height);
       }
     
       function scale($scale) {
          $width = $this->getWidth() * $scale/100;
          $height = $this->getheight() * $scale/100;
          $this->resize($width,$height);
       }
     
       function resize($width,$height) {
          $new_image = imagecreatetruecolor($width, $height);
          imagecopyresampled($new_image, $this->image, 0, 0, 0, 0, $width, $height, $this->getWidth(), $this->getHeight());
          $this->image = $new_image;
       }      
     
    }
