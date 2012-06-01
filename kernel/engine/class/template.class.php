<?php

/*
 * class template
 */

class template {
    
    var $title;
    
    
    /*
     * __construct()
     * @param $arg
     */
    
    function __construct() {
        
        global $windbloom;        
        
        $this->framework = $windbloom;
        
        $this->title = $title;

    }
    
    protected function header(){
        
        $title = '<title>'.$this->title.'</title>'."\n    ";
        $title.= '<meta charset="UTF-8" />';
        
        return "$title";
        
    }
    
    protected function copyright(){
        
        echo"&copy; 2012 Windbloom 2.0 REV 1 Luna";
        
    }
    
    protected function getTemplate2Loop($file,$type){
        
        $template = $this->framework->sys[path].'template/'.$file.'.'.$type.'.html';
        
        if( file_exists( $template ) ):        
        
            $f = file_get_contents($template);
            
            return $f;

        endif;
        
    }
    
    protected function get_template_part( $main , $replc, $template ){
        
        if( $main ):
            
            $tmp = array();
            
            foreach( $main as $k => $v ){
                
                $temp = $template;
                
                foreach($replc as $key => $value){
                    
                    $temp = preg_replace("@(\{$value\})@i",$v[$value],$temp);
                    
                }
                
                $tmp[] = $temp;
                
            }
            
            return join($tmp);
        
        endif;
        
    }
    
    protected function readfiletemplate($file){       
        
        $template = $this->framework->sys[path].'template/'.$file;
        
        if( file_exists( $template ) ):        
        
            $f = file_get_contents($template);
            
            while( list($k,$v) = each($this) ){
                
                if( is_string($v) ){                    
                    
                    $f = preg_replace("@(\{$k\})@i","$v",$f);
                    
                } elseif( is_array($v) ){
                    
                    if( $v['function'] ):
                    
                        $name = $v['function'];
                    
                        $function = $this->$name();
                        
                        $f = str_replace('{'.$k.'}',$function,$f);
                    
                    endif;
                }
                
                
                
            }           
            
            $this->returnTemplate = $f;            
            
            return $f;
        
        endif;
        
    }
}