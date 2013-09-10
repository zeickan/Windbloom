<?php


class models extends template {

	function __construct(){


	}

	protected function header(){

    	/* Armamos el HEADER DEFAULT */

    	$item[] = parent::header();	
		$cssPath = "static/";
		$item[] = $this->AddStyleSheet("style.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("style-headers.css",$cssPath,'screen');
        $item[] = $this->AddStyleSheet("style-colors.css",$cssPath,'screen');
        
	
        $jsPath = "static/js/";
        //$item[] = $this->addJavaScript('fecha.js',$jsPath);
        

        return join("\n    ",$item);
	    
    }

    protected function current_link(){

    	$this->current_home = '';

        $this->current_productos = 'class="current-menu-item"';

        $this->current_catalogos = '';

        $this->current_directorio = '';

        $this->current_nosotros = '';

        $this->current_cotizar = '';

    }

}