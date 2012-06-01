<?php

/*
 * class login
 */

class FAUTH {
    
    /*
     * __construct()
     */
    
    function __construct() {
        
    }
    
    static function is_login(){
        
        session_start();
        
        $user = $_SESSION['usuario'];
        
        if( is_array($user) ){
            
            return true;
            
        } else {
            
            return false;
            
        }
        
        
    }
    
    static function getUserMeta($id){
        
        $pdo = new db_pdo();
        
        // $pdo->add_consult("SELECT * FROM usuario WHERE numeroempleado='$id' LIMIT 1");
        
        $pdo->add_consult("SELECT usuario.id, 
                            usuario.`level`, 
                            usuario.numeroempleado, 
                            usuario.nombre, 
                            usuario.apellido, 
                            usuario.aka, 
                            usuario.DIV_1_CODE, 
                            usuario.LOCSTATE, 
                            usuario.email, 
                            usuario.passwd, 
                            usuario.active, 
                            code1.meta_value AS divcode1, 
                            locstate.meta_value AS state,
                            usuario.LOCCITY AS city
                        FROM usuario LEFT JOIN code1 ON usuario.DIV_1_CODE = code1.meta_key
                             LEFT JOIN locstate ON locstate.meta_key = usuario.LOCSTATE
                             LEFT JOIN loccity ON loccity.meta_key = usuario.LOCCITY
                        WHERE usuario.numeroempleado = '$id'");

        $query = $pdo->query();        
        
        return $query[0][0];
        
        
    }
    
}



/*
 * class forums
 */

class forums {
    
    /*
     * __construct()
     */
    
    function __construct() {
        
        $this->PDO = new db_pdo();
        
    }
    
    
    function getTopics(){
        
        $this->PDO->unset_consult();
       
        $this->PDO->add_consult("SELECT topics.id, 
                                         topics.topic_name, 
                                         topics.topic_details, 
                                         topics.topic_number, 
                                         topics.topic_rel
                                 FROM topics
                                 ORDER BY id ASC");
       
        $query = $this->PDO->query();
       
        return $query;
        
    }
    
    function getLastPostByTopic($id){        
        
        $this->PDO->unset_consult();
       
        $this->PDO->add_consult("SELECT posts.id, 
                                        posts.author_id, 
                                        posts.post_date, 
                                        posts.post_title, 
                                        posts.post_meta, 
                                        posts.post_rel, 
                                        posts.post_reply, 
                                        posts.post_author, 
                                        posts.post_forum, 
                                        usuario.nombre, 
                                        usuario.apellido, 
                                        usuario.aka, 
                                        usuario.email
                                FROM posts INNER JOIN usuario ON posts.author_id = usuario.numeroempleado
                                WHERE posts.post_forum='$id'
                                ORDER BY posts.post_date DESC
                                LIMIT 1");
       
        $query = $this->PDO->query();
       
        return $query;
        
    }
    
    function getPosts($reply = '0'){        
       
       $this->PDO->unset_consult();
       
       $this->PDO->add_consult("SELECT  posts.id, 
                                        posts.author_id,
                                        posts.post_date,
                                        unix_timestamp(posts.post_date) as estampa, 
                                        posts.post_title, 
                                        posts.post_meta, 
                                        posts.post_rel, 
                                        posts.post_reply
                                FROM posts
                                WHERE posts.post_reply = '0' AND posts.post_forum='$reply'
                                ORDER BY id DESC");
       
       $query = $this->PDO->query();
       
       return $query;

        
    }
    
    function getReplys($id){
        
        $PDO = new db_pdo();
        
        $PDO->add_consult("SELECT posts.id, 
                                        posts.author_id,
                                        posts.post_date,
                                        unix_timestamp(posts.post_date) as estampa, 
                                        posts.post_title, 
                                        posts.post_meta, 
                                        posts.post_rel, 
                                        posts.post_reply
                                FROM posts
                                WHERE posts.post_reply LIKE '$id' ");
       
       $query = $PDO->query();
       
       return $query[0];
        
    }
    
    function getBanners(){
        
        $PDO = new db_pdo();
        
        $PDO->add_consult("SELECT configuracion.valor, 
                                    configuracion.parametro
                            FROM configuracion
                            WHERE configuracion.parametro='forum'
                            LIMIT 1");
       
       $query = $PDO->query();
       
       return $query[0];
        
    }
    
    function viewtopic($id){
    
        $PDO = new db_pdo();
        
        $PDO->add_consult("SELECT topic_number FROM topics WHERE id='$id' LIMIT 1");
       
        $query = $PDO->query();
       
        $views = $query[0][0]['topic_number']+1;
        
        
        $PDO->update("topics", // TABLE NAME
	     
	     // SET name     value
	     array( "topic_number" => $views ),
	     
	     // CONDITION (OPTIONAL)
	     array("id" => $id)
	     );
        
        
    }
    
    function getMostCommented(){
        
        $PDO = new db_pdo();
        
        $PDO->add_consult("SELECT * FROM topics ORDER BY topic_number DESC LIMIT 5");
       
        $query = $PDO->query();
        
        return $query[0];
        
    }
    
    function getMostVisited(){
        
        $PDO = new db_pdo();
        
        $PDO->add_consult("SELECT * FROM topics ORDER BY topic_rel DESC LIMIT 5");
       
        $query = $PDO->query();
        
        return $query[0];
        
    }
    
    function getTags(){
        
        $PDO = new db_pdo();
        
        $PDO->add_consult("SELECT * FROM keywords");
       
        $query = $PDO->query();
        
        return $query[0];
        
    }
    
    
    function closeClass(){
        
        $this->PDO = NULL;
        
    }
    
}