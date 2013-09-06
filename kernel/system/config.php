<?php

$windbloom = (object) array();

$wb = (object) array();

/* CONFIGURACION DEL SISTEMA */

$windbloom->sys = array(
					  
	"url" => 'http://127.0.0.1/Windbloom/',
        
        #"path" => '/Applications/XAMPP/htdocs/luna/',
        "path" => getcwd().'/',
        
	"theme" => 'theme/beta/',
        
	"upload" => 'uploads/',
        
	"kernel" => 'kernel/',
            
	"lang" => 'lang/',
        
	"lang" => 'es'
	
);

/* CONFIGURACION DEL MODULO LOGIN */

$windbloom->login = array(
	
	"user_cookie" => 'user',

	"pass_cookie" => 'pass',

	"domain" => 'localhost',

	"path" => '/',
	
	"expire" => time()+7776000
	
);

/* CONFIGURACION DEl MODULO BLOG */

$windbloom->posts = array(
					   
	"max" => '10'
	
);

/* CONFIGURACION DEL MODULO POST */

$windbloom->upload = array(
	
	"valid_extensions" => 'jpg,jpeg,gif,png,flv,mp4'
	
);

define("DBHOST","localhost");

define("DBUSER","dev");

define("DBPASS","qwe8521z");

define("DBNAME","dev_aceros");


# Iniciamos...

include_once("init.php");