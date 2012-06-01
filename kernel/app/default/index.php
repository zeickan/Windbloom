<?php

/*
 * class forum
 */

class main extends template {
    
    /*
     * __construct()
     */
    
    function __construct() {
	
	global $windbloom;
        
        session_start();
        
        //$_SESSION[usuario] = FAUTH::getUserMeta(1);      
        
        $this->msg = '';
	
	$this->username = "<strong>".ucwords(strtolower($_SESSION['usuario']['aka']." ".$_SESSION['usuario']['apellido']))."</strong>";
            
        $this->current_date = "".date("F d, Y")."";
	
	$this->site_url = "?acc=viewtopic&topic=".numeric($_GET[topic]);
	
	$this->cloudtags = array( "function" => "cloudtags" );
	
	$this->most_visited = array( "function" => "most_visited" );

        
        if($_POST){ $this->insert(); }

    }
    
    function copyright(){        
        
        return "&copy; 2012 Windbloom 2.0 REV 5 Luna 2";

    }
    
    function sidebar(){
        
        $forum = new forums;
        
        $sql = $forum->getBanners();
	
	$banners = unserialize($sql[0][valor]);
	
	#return $sql[0][valor];
	
	$temp = array();
	
	foreach( $banners as $name => $value ){
	    
	    $temp[] = '<p class="banners"><a href="'.$value[1].'" target="'.$value[2].'"><img src="../Files/forums/'.$value[0].'" border="0" /></a></p>';
	    
	}
        
        #return "<pre>".print_r( $banners ,1)."</pre>";
        
	return join("\n",$temp);
        
    }
    
    function most_commented(){
	
	$temp = '';
	
	#$temp.= '<ol>';
	
	    $template = $this->getTemplate2Loop('comment','loop');
	    
	    $rgex = array( "id", "topic_name", "topic_details" );
	    
	    $forum = new forums;
	    
	    $query = $forum->getMostCommented();
	    
	    $part = $this->get_template_part($query,$rgex,$template);
	    
	    $temp.= $part;
	
	#$temp.= '</ol>';
	    
	
	return $temp;
	
    }
    
    function most_visited(){
	
	$temp = '';
	
	#$temp.= '<ol>';
	
	    $template = $this->getTemplate2Loop('comment','loop');
	    
	    $rgex = array( "id", "topic_name", "topic_details" );
	    
	    $forum = new forums;
	    
	    $query = $forum->getMostVisited();
	    
	    $part = $this->get_template_part($query,$rgex,$template);
	    
	    $temp.= $part;
	
	#$temp.= '</ol>';
	
	
	return $temp;
	
    }
    
    function cloudtags(){
	
	$forum = new forums;
            
        $tags = $forum->getTags();
	
	$foo = array();
	
	if($tags){
	    
	    foreach($tags as $name){
		
		$foo[] = '<a href="#"><span style="font-size:'.rand(10,22).'px;">'.$name["meta_key"].'</span></a>';
		
	    }
	    
	}
	
	return '<div class="most_commented">
		<h1>Cloud Tags</h1>
		
		    '.join(" ",$foo).'
		
		</div>';
	
    }

    function newDate($date){

        $r = array();

        if( $date <=12 ):

            $r[] = $date;

            $r[] = "am";

        else:

            $r[] = ($date-12);

            $r[] = "pm";

        endif;

        return $r;

    }
    
    function replys($id){
        
        $forum = new forums;
            
        $replys = $forum->getReplys($id);
        
        $reply = array();
        
        if( is_array($replys) ){
            
            $reply[] = '<div class="replys">';
            
            foreach($replys as $r){
                
                $user = FAUTH::getUserMeta($r["author_id"]);
                
                $date = getdate($r["estampa"]);

                $hour = self::newDate($date[hours]);
                
                $respu = '';
                
                $respu.= ($user[level] == "administrador")?'<div class="rep">':'<div class="rep" style="background:#f1f1f1 !important;">';
                
                $respu.= '<h3>'.$r["post_title"].'</h3>';
                
                /*$respu.= '<h6> '.zerofill($date[mon],2).'/'.zerofill($date[mday],2).'/'.$date[year].' '.zerofill( $hour[0] ,2).':'.zerofill($date[minutes],2).' '.$hour[1].'<br>
                        
                           -- #'.$r[id].'</h6>';*/
				$respu.= '<h6> '.zerofill($date[mon],2).'/'.zerofill($date[mday],2).'/'.$date[year].' '.zerofill( $hour[0] ,2).':'.zerofill($date[minutes],2).' '.$hour[1].'</h6>';		   
                
                $divcode = (!empty($user['divcode1']))?'<br/>'.ucwords(strtolower($user['divcode1'])).' <br> ':'';

                $respu.= '<h5>'.ucwords(strtolower($user[aka])).' '.ucwords(strtolower($user[apellido])).' '.$divcode.'';

                $respu.= (!empty($user[city]))?ucwords(strtolower($user[city].', '.$user[state])).'</h5>':'</h5>';
                
                $respu.= "<p>".$r["post_meta"]."</p>";
                
                $respu.= '</div>';
                
                $reply[] = $respu;
                
            }
            
            $reply[] = '</div>';
            
            return join("    ",$reply);
            
        }
        
            
        $forum->closeClass();
        
        
    }
    
    function posts(){
        
        session_start();
        
        $u = $_SESSION[usuario];
        
        $posts = array();
	
	$posts[] = '<div id="formWrapper">
			<form id="formmsg" action="?acc=viewtopic&topic='.numeric($_GET[topic]).'" method="post" enctype="multipart/form-data">
			<div id="startCommentForm" class="left">
			    <textarea id="textmsg" name="msg" cols="50" rows="5" placeholder="Type your comment"></textarea><br>
			</div>
			<div id="sendCommentwrapper" class="left">
			    <input id="sendmsg" type="submit" value="Send" class="submit" />
			</div>
			</form>
		    </div>';
        
        if( count($this->query[0]) ):
		
		$num = count($this->query[0]);
        foreach( $this->query[0] as $p ){
            
            $user = FAUTH::getUserMeta($p["author_id"]);
            
            $lvl = ($user[level] == "administrador")?'Moderator':'Employee';
            
            $date = getdate($p["estampa"]);

            $hour = self::newDate($date[hours]);
            
            $post = '<article>';
            
            $post.= '<h2>' .ucwords(strtolower($user[aka])).' '.ucwords(strtolower($user[apellido])). '</h2>';            
            
            $post.= '<h6> '.zerofill($date[mon],2).'/'.zerofill($date[mday],2).'/'.$date[year].' '.zerofill( $hour[0] ,2).':'.zerofill($date[minutes],2).' '.$hour[1].'<br>
                        
            #'.$num.'
            </h6>';
            
            $divcode = (!empty($user['divcode1']))?'<h5>'.ucwords(strtolower($user['divcode1'])).' <br> ':'';

            $post.= $divcode;

            $post.=  (!empty($user[city]))?ucwords(strtolower($user[city].', '.$user[state])).'</h5>':'';
            
            $post.= '<p>'.$p["post_meta"].'</p>';
            
            $post.= $this->replys($p[id]);
            
            $post.= '<a href="javascript:void(0)" class="reply" rel="'.$p[id].'">Comment</a>';
            
            $post.= '<div id="reply-'.$p[id].'" class="replyForm"></div>';
            
            $post.= "</article> \n\n    ";
            
            $posts[] = $post;
			
			$num--;
            
        }
        endif;
        
        return join("",$posts);
        
    }
    
    function forma(){
        
        $tpl = '';
        
    }
    
    function viewtopic(){
        
        if( FAUTH::is_login() ){

            $forum = new forums;
	    
	    $forum->viewtopic( numeric($_GET[topic]) );
            
            $this->query = $forum->getPosts( numeric($_GET[topic]) );
            
            session_start();
            
            $this->posts = array('function' => 'posts');
	    
	    $this->most_commented = array('function' => 'most_commented');
            
            $forum->closeClass();
            
            $this->title = "Foros BBVA";
            
            $template = "index";
        
        } else {
            
            $this->title = "Login";
            
            $template = "login";
            
            header("location: ../Autenticacion");
            
        }
        
        # Funciones de remplazo
        
        $this->header = array('function' => 'header');
        
        $this->copyright = array('function' => "copyright");
        
        $this->sidebar = array('function' => 'sidebar');
        
        # Cargar plantilla
        $this->readfiletemplate($template.".forum.html");
        
    }
    
    function insert(){
        
        $msg = alphanumeric( htmlentities( utf8_decode($_POST[msg]) ) ,"&#;-_.!$%/()");
        
        $_GET['send'] = numeric($_GET['send']);
        
        if( is_numeric($_SESSION[usuario][id]) && $_SESSION[usuario][id] >= 1  && !empty($msg) && $_GET['topic'] ):
        
            $pdo = new db_pdo();
	    
	    $id_topic = numeric( $_GET[topic] );
	    
	    $pdo->add_consult("SELECT id FROM posts WHERE post_forum='$id_topic'");
	    
	    $num = $pdo->numRows();
	    
	    $num = $num[0][0];
	    
	    $sum = $num+1;

            
            $tlt = alphanumeric( htmlentities( utf8_decode($_POST[title]) ) ,"&#;-_.!$%/()");
            
            $msg = alphanumeric( htmlentities( utf8_decode($_POST[msg]) ) ,"&#;-_.!$%/()'?:");
            
            $id = numeric($_SESSION[usuario][numeroempleado]);
            
            $rel = $_POST[reply]?numeric($_POST[reply]):'0';
            
            if( $pdo->insert("posts",
                             array(
                                   "author_id" => $id,
                                   "post_title" => $tlt,
                                   "post_meta" => str_replace("\\","",$msg),
                                   "post_reply" => $rel,
				   "post_forum" => numeric( $_GET[topic] )
                                   )
                            )
               ){
                
		 
		$pdo->update("topics", // TABLE NAME
	     
			// SET name     value
			array("topic_rel" => $sum),
			
			// CONDITION (OPTIONAL)
			array("id" => $id_topic )			
			);
		
                $this->msg = "EXITO";
                
                header("location: index.php?acc=viewtopic&topic=".numeric($_GET[topic]));
                
            } else {
            
                $this->msg = "ERROR: ". $pdo->error[2];
            
            }
	    
	    
            
        endif;
    }
    
    /* FORUMS */
    
    public function topics(){
	
	$template = $this->getTemplate2Loop('topic','loop');
	
	$rgex = array( "id", "topic_name", "topic_details", "post_date", "author_id", "nombre", "author_name", "email" );
	
	$forum = new forums;
            
        $query = $forum->getTopics();
	
	$final = array();
	
	if( $query[0] ):
	
	    foreach( $query[0] as $key => $value ){
		
		$rel = $forum->getLastPostByTopic($value[id]);		
		
		$u = $rel[0][0]?$rel[0][0]:array();
		
		$u[post_id] = $u[id];
		
		unset($u[id],$u[0],$u[1],$u[2],$u[3],$u[4],$u[5],$u[6],$u[7],$u[8],$u[9],$u[10],$u[11],$u[12]);
		
		$name = ucwords( strtolower( $u[aka].' '.$u[apellido] ) );
		
		$name = (trim($name))?$name:'Ningun mensaje';
		
		$final[$key] = array_merge($value,$u,array( "author_name" => $name  ) );
		
	    }
	
	endif;
	
	#echo"<pre>".print_r($final,1)."</pre>";
	
	
	$part = $this->get_template_part($final,$rgex,$template);
	
	return $part;
	
    }
    
    public function viewforums(){
	
	if( FAUTH::is_login() ){

            $forum = new forums;
            
            $this->query = $forum->getPosts();
            
            session_start();
            
            $this->posts = array( 'function' => 'topics' );
            
   	    $this->most_commented = array('function' => 'most_commented');
            
            $forum->closeClass();
            
            $this->title = "Foros BBVA";
            
            $template = "index";
        
        } else {
            
            $this->loginError();
            
        }
        
        # Funciones de remplazo
        
        $this->header = array('function' => 'header');
        
        $this->copyright = array('function' => "copyright");
        
        $this->sidebar = array('function' => 'sidebar');
        
        # Cargar plantilla
        $this->readfiletemplate($template.".forum.html");
	
    }
    
    
    
    
    
    function loginError(){
	
	$this->title = "Login";
            
	$template = "login";
	
	# Cargar plantilla
        $this->readfiletemplate($template.".forum.html");
	
	//header("location: ../Autenticacion");
	
    }
    
    
    public function main(){
	
	$this->viewforums();
	
    }
    
    
    
    
}

