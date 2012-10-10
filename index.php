<?php

date_default_timezone_set('America/Los_Angeles');

header('Content-Type: text/html; charset=utf-8'); 

define("TITLE","TDU");

/* ADMINISTRADOR */

    $user = array();

    $user['name'] = "admin";
    
    $user['pass'] = "123456";

require_once('kernel/system/config.php');