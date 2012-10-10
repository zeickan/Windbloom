<?php

/*
 * class template
 */

class template {
    
    var $title;
    
    var $framework;
    
    
    /*
     * __construct()
     * @param $arg
     */
    
    function __construct() {
        
        global $windbloom;       
        
        $this->framework = $windbloom;
        
        $this->title = $title;

    }
    
    protected function addMsg($msg = '',$type = 'error'){

        return '<div class="'.$type.'">'.$msg.'</div>';
    
    }
    
    protected function addJavaScript($file,$path = "template/js/",$specific = NULL){
        
        if( is_null($specific) ){
        
            if(  strstr($file,"http://") || strstr($file,"https://") ):
            
                $js = '<script type="text/javascript" src="'.$file.'"></script>';
            
            else:
            
                $js = '<script type="text/javascript" src="'.$this->url_site.$path.$file.'"></script>';
            
            endif;
        
        } else {
            
            $js = '<script type="text/javascript">'.$specific.'</script>';
            
        }
        
        return $js;
        
    }
    
    protected function addStyleSheet($file,$path = "template/css/",$media = 'all'){
        
        if(  strstr($file,"http://") || strstr($file,"https://") ):
        
            $css = '<link rel="stylesheet" href="'.$file.'" type="text/css" media="'.$media.'" />';
        
        else:
        
            $css = '<link rel="stylesheet" href="'.$this->url_site.$path.$file.'" type="text/css" media="'.$media.'" />';
        
        endif;
        
        return $css;
        
    }
    
    protected function header(){
        
        $title = '<title>'.$this->title.'</title>'."\n    ";
        $title.= '<meta charset="UTF-8" />';
        
        return "$title";
        
    }
    
    protected function copyright(){
        
        return "&copy; 2012 Windbloom 2.0 REV 1 Luna";
        
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
                    
                    $temp = preg_replace("@(\{\{$value\}\})@i",$v[$value],$temp);
                    
                }
                
                $tmp[] = $temp;
                
            }
            
            return join($tmp);
        
        endif;
        
    }
    
    protected function readfiletemplate($file,$bool = false){       
        
        $template = $this->framework->sys[path].'template/'.$this->template_path.$file;
        
        if( file_exists( $template ) ):        
        
            $f = file_get_contents($template);
            
            while( list($k,$v) = each($this) ){
                
                if( is_string($v) ){                    
                    
                    $f = preg_replace("@(\{\{$k\}\})@i","$v",$f);
                    
                } elseif( is_array($v) ){
                    
                    if( $v['function'] ):
                    
                        $name = $v['function'];
                    
                        $function = $this->$name();
                        
                        $f = str_replace('{%'.$k.'%}',$function,$f);
                    
                    endif;
                }
                
                
                
            }           
            
            if( !$bool ):
            $this->returnTemplate = $f;            
            endif;            
            
            return $f;
        
        endif;
        
    }
}

class GET {
    
    public function ACTIONS(){
        
        $get = $_GET;
        
        $temp = (object) array();
        
        unset($get['app']);
        unset($get['acc']);
        
        if( get ):
            
            foreach($get as $key => $value){
                
                $key = alphanumeric($key,'-_.,|');
                
                $value = alphanumeric($value,'-_.,|');
                
                $temp->$key = $value;
                
            }           
            
            return $temp;
        
        endif;
    }
    
}



class HTML {
    

    public function TAG( $content, $tag, $attr = NULL, $doble = true ){

        if( $attr ):
            
            $atr = array();

            foreach ($attr as $key => $value) {
                
                $atr[] = $key.'="'.$value.'"';

            }

            $atributes = " ".join(" ",$atr);

        endif;

        $end = $doble?'</'.$tag.'>':'';

        return '<'.$tag.''.$atributes.'>'.$content.$end;

    }
    
    public function OPTION( $array, $id = 0, $val = 1 ){
            
        $return = array();
            
        if( $array ):
        
            foreach($array as $key => $value){
                
                $return[] = '<option value="'.utf8_encode($value[$id]).'">'.utf8_encode($value[$val]).'</option>';
                
            }
        
        endif;
        
        return join("\n",$return);
        
    }

}




class Request {
    
    public function HttpRedirect($url){

        header("location: ".$url);

    }
    
}









