<?php

class doDB {
    
    private $conn;
    private $tableId;
    private $startSlash;
    private $endSlash;
    private $pkColumn;
    private $table;
    private $dbType;
    
    function __construct($hostname, $username, $password, $database, $tableId = "undefined", $startSlash, $endSlash, $pkColumn, $table, $dbType) {
        $this->conn = odbc_connect($hostname, $username, $password, SQL_CURSOR_FORWARD_ONLY);
        $this->tableId = $tableId;
        $this->startSlash = $startSlash;
        $this->endSlash = $endSlash;
        $this->table = $table;
        $this->pkColumn = $pkColumn;
        $this->dbType = $dbType;
    }
    
    public function close()
    {
        odbc_close($this->conn);
    }
    
    function createLogTable()
    {
        if($this->dbType == "MSSQL")
            $result = odbc_exec($this->conn, "CREATE TABLE [zqgridlogs](
                                    [id] [int] IDENTITY(1,1) NOT NULL,
                                    [table_id] [varchar](200) COLLATE Latin1_General_CI_AS NOT NULL,
                                    [sql] [text] COLLATE Latin1_General_CI_AS NOT NULL,
                                    [run_at] [timestamp] NOT NULL
                                ) ON [PRIMARY] TEXTIMAGE_ON [PRIMARY]");
        
        if($this->dbType == "ORACLE")
            $result = odbc_exec($this->conn, "CREATE TABLE zqgridlogs(
                                    \"id\" NUMBER primary key,
                                    table_id varchar(200) not null,
                                    \"sql\" varchar(4000) not null,
                                    run_at timestamp
                                )");

        if($this->dbType == "PGSQL")
            $result = odbc_exec($this->conn, "CREATE TABLE zqgridlogs(
                                    \"id\" NUMBER primary key,
                                    table_id varchar(200) not null,
                                    \"sql\" varchar(4000) not null,
                                    run_at timestamp
                                )");
    }
    
    function addToLog($stmt)
    {
        if($this->dbType == "MSSQL")
            $query = "insert into zqgridlogs(" . $this->startSlash . "table_id" . $this->endSlash . ", " . $this->startSlash ."sql" . $this->endSlash . ")
                         values('" . str_replace("'", "''", $this->tableId) . "','" . str_replace("'", "''", $stmt) . "')";
        
        odbc_exec($this->conn, $query);
        
        //Table created ?
        if(odbc_errormsg($this->conn) != "")
        {
            echo "error:" . $query;
            exit();
            $this->createLogTable();
            odbc_exec($this->conn, $query);
        };
    }
    
    function getNextPk()
    {
        $pk = $this->startSlash . $this->pkColumn . $this->endSlash;
        $table = $this->startSlash . $this->table . $this->endSlash;
        $query = "select max($pk) maxid from $table";
        
        $res = odbc_exec($this->conn, $query);
        $row = odbc_fetch_array($res);
        
        if(odbc_num_rows($res) == 0)
            return 1;
        else
            return intval($row["maxid"])+1;
    }
    
    function execute($stmt, $log = false){
        
        $clean = $stmt;
        
        //Get $type
        $type = explode(" ", $clean);
        $type = strtoupper($type[0]);
        
        if($type == "INSERT")
        {
            $result = odbc_exec($this->conn, $clean);
            
            //Get last id inserted
            $table = $startSlash . $table . $endSlash;
            $pk = $startSlash . $pkColumn . $endSlash;
            
            $resPk = odbc_exec($this->conn, "select $pk maxid from $table order by $pk desc");
            $row = odbc_fetch_array($resPk);
            $pk = $row["maxid"];
            
            if($log)
                $this->addToLog($clean);
            
            return $pk;
        };
        
        if($type == "UPDATE")
        {
            $result = odbc_exec($this->conn, $clean);
            if($log)
                $this->addToLog($clean);
            
            return null;
        };
        
        if($type == "DELETE")
        {
            $result = odbc_exec($this->conn, $clean);
            if($log)
                $this->addToLog($clean);
            
            return null;
        };
        
        if($type == "SELECT")
        {
            $result = odbc_exec($this->conn, $clean);
            
            if($log)
                $this->addToLog($clean);
            
            $arr = Array();
            $arrPK = Array();
            $ret = new stdClass();
            
            $t = 0;
            while($row = odbc_fetch_array($result))
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
