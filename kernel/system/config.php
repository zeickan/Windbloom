<?php

$windbloom = (object) array();

$wb = (object) array();

/* CONFIGURACION DEL SISTEMA */

$windbloom->sys = array(
					  
	"url" => 'http://4u.zeickan.com/forums/',
        
        #"path" => '/Applications/XAMPP/htdocs/luna/',
        "path" => getcwd().'/',
        
	"theme" => 'theme/beta/',
        
	"upload" => 'uploads/',
        
	"kernel" => 'kernel/',
        
	"sys_images" => 'sys_images/',
        
	"avatar" => 'avatar/',
        
	"lang" => 'lang/',
        
	"deflang" => 'es'
	
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

define("DBPASS","qwe8521z_dev");

define("DBNAME","dev_aventurandome");

include_once("init.php");