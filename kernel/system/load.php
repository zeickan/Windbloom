<?php

/*
 * class load
 */

class load {
    
    /*
     * __construct()
     * @param
     */
    
    function __construct() {
        
        
    }
    
    
    /*
     * function url
     * @param $str
     * @access private
     * @example URL validas para el rewrite
     */
    
    function url($str) {
        
        $str = preg_replace("@([^a-zA-Z0-9_-]+)@i","",$str);
        
        return $str;
        
    }
    
    
}

/*
 * class App
 */

class App extends load {
    
    /*
     * __construct()
     */
    
    function __construct() { 
        
        global $windbloom;
        
        $this->framework = $windbloom;
        
        $this->app = $_GET[app];
        
        $this->sec = $_GET[sec];
        
    }
    
    /*
    function loadFile(){
        
        $app = parent::url($this->app);
        
        if( file_exists( $this->framework->sys[path] . "kernel/app/$app/index.php" ) ):
        
        $this->file = $this->framework->sys[path] . "kernel/app/$app/index.php";
        
        return true;
        
        else:
        
        $this->file = $this->framework->sys[path] . "kernel/app/default/index.php";
        
        return false;
        
        endif;
        
    }
    */

    function loadFile(){
        
        $app = parent::url($this->app);
        
        if( file_exists( $this->framework->sys[path] . "kernel/app/$app/index.php" ) ):
        
            $this->models = ( file_exists($this->framework->sys[path]."kernel/app/$app/models.php") )?$this->framework->sys[path]."kernel/app/$app/models.php":false;
        
            $this->file = $this->framework->sys[path] . "kernel/app/$app/index.php";
            
            $this->className = $app;
        
        return true;
        
        else:
        
            $this->models = ( file_exists($this->framework->sys[path]."kernel/app/default/models.php") )?$this->framework->sys[path]."kernel/app/default/models.php":false;
            
            $this->file = $this->framework->sys[path] . "kernel/app/default/index.php";
            
            $this->className = "main";
        
        return false;
        
        endif;
        
    }
    
}
/*
$app = new App;

if( $app->loadFile() ): include_once( $app->file );
else: include_once( $app->file );
endif;
*/


$app = new App;

if( $app->loadFile() ):

    if( $app->models ) include_once($app->models);
    
    include_once( $app->file );
else:
    
    if( $app->models ) include_once($app->models);   

    include_once( $app->file );
endif;

# Nombre de la clase

$theme = new $app->className;

# Nombre del metodo

$__acc = $windbloom->action;

if( method_exists($theme,$__acc) ){
    
    $theme->$__acc();

    if( $theme->returnTemplate):
        
        echo $theme->returnTemplate;
    
    endif;
    
} else {

error_600( 'Framework error 1000.templateClass' );
    
}