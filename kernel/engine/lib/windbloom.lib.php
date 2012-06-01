<?php

/*
 * class windbloom
 */

class windbloom_lib {
    
    /*
     * __construct()
     * @param $arg
     */
    
    function __construct() {
        
    }
    
    /*
     * function -v
     * @param $arg
     */
    
    function _v() {
        
        return 'Windbloom Class 0.0 rev 0';
        
    }
    
}

/*
 * class wb
 */

class wb extends windbloom_lib {
    
    /*
     * __construct()
     * @param $arg
     */
    
    function __construct() {
        
    }
    
    
    function _V(){
        
        return parent::_v;
        
    }
    
}
