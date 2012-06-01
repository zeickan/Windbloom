<?php

/* FUNCIONES DEL KERNEL */

require_once("kernel.php");

/* RUN ENGINE */

include("engine_run.php");

/* DEFAULT LOAD MODULE */

$windbloom->application = $_GET[app]?alphanumeric($_GET[app]):'main';

$windbloom->action = $_GET[acc]?alphanumeric($_GET[acc]):'main';

/* LIBRERIAS EXTRAS */

define('FPDF_FONTPATH', $windbloom->host["path"].'module/pdf/font/');

/* INICIAMOS */

include_once("load.php");
