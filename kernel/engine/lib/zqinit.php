<?php
    
    include_once("zqdatagrid.php");
    
    // Database information
    define("ZQ_CONN", "MYSQL"); //DB Mode: MYSQL or ODBC
    define("ZQ_TYPE", "MYSQL"); //DB Mode type: MYSQL or MSSQL
    define("ZQ_DB_USERNAME", DBUSER); //DB Username
    define("ZQ_DB_PASSWORD", DBPASS); //DB Password
    define("ZQ_DB_HOSTNAME", DBHOST); //Hostname or Hostame\SQL Server instance
    define("ZQ_DB_DATABASE", DBNAME); //DB Database
    define("USE_LOG", false); //Record queries ? (use log?)
    
    /*
    //SQL SERVER binding example
    define("ZQ_CONN", "ODBC"); //DB Mode: MYSQL or ODBC
    define("ZQ_TYPE", "MSSQL"); //ORACLE, PGSQL or MSSQL
    define("ZQ_DB_USERNAME", "sa"); //DB Username
    define("ZQ_DB_PASSWORD", "Kawasaki1#!"); //DB Password
    define("ZQ_DB_HOSTNAME", "Driver={SQL Server};Server=GFIPTBFALCAO\\SQLEXPRESS;Database=teste"); //DSN String
    define("ZQ_DB_DATABASE", "teste"); //DB Database
    define("USE_LOG", true); //Record queries ? (use log?)
    */
    
    // Override "where" clause from client
    //General WHERE clause for refresh action. Will override and WHERE clause given by web browser
    define("ZQ_DB_OVERRIDE_WHERE", ""); 
    
    // *** Image REQUIRED information (if you use "file" formatter) ***
    //Full path prefix to save files - Change it to your path
    
    define("ZQ_FILE_REPOSITORY_PATH", $windbloom->sys['path']."uploads/");
    
    //URL path prefix to show images on browser - Change it to your path
    define("ZQ_FILE_URL_PREFIX", $windbloom->sys['url']."uploads/");
    
    /* *** */
    
    // *** Define custom FK table columns (id, and description) - If needed *** 
    //$ZQ_FK_TABLE = Array("categoria" => Array("id", "description"));
    
    /* Advanced action binding events */
    function ZqBeforeInsert($table, $columns, $values, $conn){};
    
    function ZqAfterInsert($table, $pk, $conn){};
    
    function ZqBeforeUpdate($table, $pk, $conn){};
    
    function ZqAfterUpdate($table, $pk, $conn){};
    
    function ZqBeforeDelete($table, $pk, $conn){};
    
    function ZqAfterDelete($table, $pk, $conn){};
    
    function ZqBeforeRefresh($table, $conn){};
    
    function ZqAfterRefresh($table, $conn){};
    /* *** */
    
    //From here, you don't need to do anything :)
    //Check if image path exists, and create it 
    if(defined("ZQ_FILE_REPOSITORY_PATH") || defined("ZQ_FILE_URL_PREFIX"))
        if(!is_dir(ZQ_FILE_REPOSITORY_PATH))
            mkdir(ZQ_FILE_REPOSITORY_PATH, 0777, true);
    
    

    