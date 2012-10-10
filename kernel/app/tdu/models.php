<?php

/* MODELOS */

class model {
    
    
    public function decode_suc($array){
        
        $array = unserialize($array);
        
        $r = array();
        
        if( $array ){           
           
            foreach($array as $key => $value):                
                
                $tmp = '<div><span>';
                $tmp.= join("</span></div><div><span>",$value);
                $tmp.= '</span></div>';
                
                $r[] = $tmp;
                
            endforeach;
            
        }
        
        #$array = print_r($r,1);
        
        #echo"<pre>".print_r($r,1)."</pre>";
        
        return join('<div class="separator"></div>',$r);
        
    }
 
    
}