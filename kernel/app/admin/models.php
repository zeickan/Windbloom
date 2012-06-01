<?php

/* MODELOS */

class model {
    
    public function box_sucursal($IN){
        
        $str = explode("{*}", strip_tags(str_replace( array("</span>","<hr>"),array("\n","{*}"), $IN )) );
            
        $return = array();
        
        if( $str ){                
            foreach($str as $name => $val){
                
                $int = explode("\n",$val);
                
                $r = array();
                
                foreach($int as $key => $value){
                    
                    $value = trim($value);
                    
                    if( $value ){
                        
                        $r[] = $value;
                        
                    }
                    
                }
                
                $t = $r[0];
                
                $return[$r[0]] = $r;
              
            }
        }           
            
        return serialize($return);
        
    }
    
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
        
        return join("<hr>",$r);
        
    }
    
    public function requiredFileds( $require , $string){      
        
        $r = 0;
        
        foreach( $require as $key => $value ) {
            # code...
            if( !empty($string[$value]) ){                    
                $r++;
            }
        }
        
        if( $r == count($require)){
            return true;
        } else {
            return false;
        }     
        
    }
    
    
    public function valid_encode($array,$exception = false){
        
        $valid = array();

        foreach ($array as $key => $value) {
            # valid code & utf8 encoding...
            if( $exception[$key] ){
                
                $valid[$key] = utf8_encode($value);
                
            } else {
                
                $valid[$key] = alphanumeric( htmlentities( utf8_decode($value) ) , "-_.$&;/:,*+@#()=?{}[]\" " );
                
            }
            
        }
        
        return $valid;
        
    }
    
    
    
    public function return_decode($string){
        
        return htmlentities($string);
        
    }
    
    
    
    
    
    
    
    
    
    
    
    
    
}