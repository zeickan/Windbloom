<?php

class doDB {
    
    private $conn;
    private $tableId;
    private $startSlash;
    private $endSlash;
    private $table;
    private $pkColumn;
    
    function __construct($hostname, $username, $password, $database, $tableId = "undefined", $startSlash, $endSlash, $pkColumn, $table, $dbType = null) {
        $this->conn = mysql_connect($hostname, $username, $password, true);
        $this->tableId = $tableId;
        $this->startSlash = $startSlash;
        $this->endSlash = $endSlash;
        $this->table = $table;
        $this->pkColumn = $pkColumn;
        
        mysql_select_db($database);
    }
    
    public function close()
    {
        mysql_close($this->conn);
    }
    
    function createLogTable()
    {
        $result = mysql_query("CREATE TABLE `zqgridlogs` (
                                  `id` int(11) NOT NULL AUTO_INCREMENT,
                                  `table_id` varchar(200) NOT NULL,
                                  `sql` text NOT NULL,
                                  `run_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
                                  PRIMARY KEY (`id`)
                                  ) ENGINE=MyISAM DEFAULT CHARSET=latin1");
    }
    
    function addToLog($stmt)
    {
        $query = "insert into zqgridlogs(" . $this->startSlash . "table_id" . $this->startSlash . ", " . $this->startSlash ."sql" . $this->endSlash . ")
                     values('" . addslashes($this->tableId) . "','" . addslashes($stmt) . "')";
        
        mysql_query($query, $this->conn);
        
        if(mysql_error() != "")
            die(mysql_error());
        
        //Table created ?
        if(mysql_error() != "")
        {
            $this->createLogTable();
            mysql_query($query, $this->conn);
        };
    }
    
    function getNextPk()
    {
        $pk = $this->startSlash . $this->pkColumn . $this->endSlash;
        $table = $this->startSlash . $this->table . $this->endSlash;
        $query = "select max($pk) from $table";
        $res = mysql_query($query);
        
        if(mysql_error() != "")
            die(mysql_error());
        
        if(mysql_num_rows($res) == 0)
            return 1;
        else
        {
            $row = mysql_fetch_row($res);
            return intval($row[0])+1;
        };
    }
    
    function execute($stmt, $log = false){
        
        $clean = $stmt;
        
        //Get $type
        $type = explode(" ", $clean);
        $type = strtoupper($type[0]);
        
        if($type == "INSERT")
        {
            $result = mysql_query($clean, $this->conn);
        
            if(mysql_error() != "")
                die(mysql_error());
            
            //Get last id inserted
            $table = $this->startSlash . $this->table . $this->endSlash;
            $pk = $this->startSlash . $this->pkColumn . $this->endSlash;
            
            $query = "select max($pk) from $table";
            $res = mysql_query($query);
            
            if(mysql_error() != "")
                die(mysql_error());
            
            $row = mysql_fetch_row($res);
            
            if($log)
                $this->addToLog($clean);
            
            return $row[0];
        };
        
        if($type == "UPDATE")
        {
            $result = mysql_query($clean, $this->conn);
        
            if(mysql_error() != "")
                die(mysql_error());
            
            if($log)
                $this->addToLog($clean);
            
            return null;
        };
        
        if($type == "DELETE")
        {
            $result = mysql_query($clean, $this->conn);
            
            if(mysql_error() != "")
                die(mysql_error());
            
            if($log)
                $this->addToLog($clean);
            
            return null;
        };
        
        if($type == "SELECT")
        {
            $result = mysql_query($clean, $this->conn);
        
            if(mysql_error() != "")
                die(mysql_error());
            
            if($log)
                $this->addToLog($clean);
            
            $arr = Array();
            $arrPK = Array();
            $ret = new stdClass();
            
            $t = 0;
            while($row = mysql_fetch_assoc($result))
            {
                $arr[$t] = Array();
                $key = array_keys($row);
                $i = 0;
                foreach($row as $cell)
                {
                    if($i == 0)
                        $arrPK[] = $row[$key[$i]];
                        
                    $arr[$t][$key[$i]] = utf8_encode($row[$key[$i]]);
                    $i++;
                };
                
                $t++;
            };
               
            $ret->result = $arr;
            $ret->pk = $arrPK;
            
            return $ret;
        };
        
    }
};

?>
