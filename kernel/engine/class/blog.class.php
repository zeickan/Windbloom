<?php

/**
 * Blog
 * 
 * Clase para crear un blog completo.
 * 
 * @author Webservice
 * @version 1.0
 * @package Data
 */

 $_wb_posts = (object) array();
	
 $_wb_comment;

 $_wb_posts->initial_comments = 0;
 
 $_wb_send_comment = $_POST["send_comment"];
 

class blog {
	
	/**
	 * Numero maximo de post por pagina.
	 *
	 * @var string
	 * @access public
	 */	
	var $Max;
	
	/**
	 * Objeto donde se almacenan los posts
	 *
	 * @var string
	 * @access protected
	 */	
	protected $_wb_posts;
	
	/**
	 * Objeto donde se almacenan los datos del post
	 * @example
	 * ->ID 
	 * ->DATE
	 * ->AUTHOR
	 * ->TITLE
	 * ->PERMALINK
	 * ->CONTENT
	 * ->STATUS
	 * ->PARENT
	 * ->PASSWORD
	 * ->MODIFIED
	 * ->TYPE
	 * ->COMMENT_STATUS
	 * ->COMMENT_COUNT
	 * 
	 * @var string
	 * @access public
	 */	
	public $post;
	
	/**
	 * Objeto donde se almacenan los terminos
	 *
	 * @var string
	 * @access protected
	 */	
	protected $term;
	
	/**
	 * Llamadas al PDO:MySQL no permitidas fuera de la clase
	 *
	 * @var string
	 * @access protected
	 */
	protected $pdo;
	
	/**
	 * Variable que contiene la seccion actual
	 *
	 * @var string
	 * @access protected
	 */
	protected $section;
	
	
	/**
	 * Titulo del blog o post
	 *
	 * @var string
	 * @access public
	 */
	public $title;
	
	/**
	 * Descripcion del blog o post
	 *
	 * @var string
	 * @access public
	 */
	public $description;
	
	/**
	 * Keywords del blog o post
	 *
	 * @var string
	 * @access public
	 */
	public $keywords;
	
	/**
	 * Autor del blog o post
	 *
	 * @var string
	 * @access public
	 */
	public $author;
	
	
	/**
	 * Constructor de la clase blog
	 * @access public
	 */
	function __construct(){
	
		global $windbloom;				
		
		$this->windbloom = $windbloom;
			
		$this->Max = $this->Max?$this->Max:1;
		
		$this->_wb_posts = (object) array();
		
		$this->_wb_posts->initial = 0;
		
		$this->_wb_posts->have_posts = false;
		
		$this->_wb_posts->query = 0;
		
		$this->post = (object) array();
		
		$this->term = (object) array();
		
		$this->term->Query = false;
		
		$this->term->Array = array();
		
		$this->pdo = new db_pdo();
		
		$this->section = self::section();		
		
		$this->title = "";
		
		$this->description = "";
		
		$this->keywords = "";
		
		$this->author = "";

	}
	
	/**
	 * Funcion para definir en que seccion de blog se encuntra
	 * Categorias,Etiquetas,Autor,buscador
	 * @access private
	 */
	private function section(){
		
		// Funcion que nos permitira definir en que seccion del blog se
		// encuntra y asi mostrar los posts adecuados...
		
		switch( $this->windbloom->get->app ){
			
			case "tags":
				
			// Se busca el tag en la lista de terminos para obtener
			// el ID correspondiente, si no se encuentra regresa
			// false
			
			$terms = $this->terms($this->windbloom->get->post);
			
			$tag = search_in_array($this->windbloom->get->post,$this->term->Query);
			
			$tag = $this->term->Query[$tag[0]];
			
			if($tag):
			
				$tag = numeric($tag[id]);
				
				$this->pdo->unset_consult();
				
				$this->pdo->add_consult("SELECT DISTINCT terms_relationship.object_id FROM terms_relationship WHERE term_type='post_tag' AND term_id='$tag'");
				
				$posts = $this->pdo->query();				
				
				$postsIds = array();
				
				foreach($posts[0] as $id) $postsIds[] = $id[0];				
				
				//$this->section = $postsIds;
				
				return $postsIds;				
								
			else:	return false;				
			endif;
				
			break;
		
			case "categories":
			
			// Se busca el tag en la lista de terminos para obtener
			// el ID correspondiente, si no se encuentra regresa
			// false
			
			$terms = $this->terms($this->windbloom->get->post);
			
			$tag = search_in_array($this->windbloom->get->post,$this->term->Query);
			
			$tag = $this->term->Query[$tag[0]];
			
			if($tag):
			
				$tag = numeric($tag[id]);
				
				$this->pdo->unset_consult();
				
				$this->pdo->add_consult("SELECT DISTINCT terms_relationship.object_id FROM terms_relationship WHERE term_type='category' AND term_id='$tag'");
				
				$posts = $this->pdo->query();
				
				$postsIds = array();
				
				foreach($posts[0] as $id) $postsIds[] = $id[0];				
				
				//$this->section = $postsIds;
				
				return $postsIds;				
								
			else:	return false;				
			endif;
				
			break;	
			
		}	
	}
	
	/**
	 * Funcion que revisa si existen posts dentro de la seccion actual.
	 * Si no existen posts disponibles regresa false.
	 * @access public
	 */	
	public function have_posts(){		
		
		// Comprobamos si ya se ha hecho la consulta para comprobar
		// si hay posts e iniciamos un loop por el numero de post
		// permitidos por pagina o el total disponible.
		
		if( $this->_wb_posts->have_posts ):
		
			$this->_wb_posts->initial++;
			
			$max = $this->windbloom->get->reg?'1':$this->Max;
			
			if( $this->_wb_posts->stop ) return false; 
			
			if( $this->_wb_posts->initial > $this->_wb_posts->total || $this->_wb_posts->initial > $max ) return false;
			
			else return true;					
		
		// Comprobamos si hay posts para mostrar...
		
		else:		
		
		$condition = $this->windbloom->get->reg?" AND slug='".$this->windbloom->get->reg."'":'';
		
		$this->pdo->unset_consult();
		
		// Comprobamos si estamos en alguna seccion
				
		if( $this->section ) $condition.=" AND id IN(".implode(",",$this->section).")";	
		
		$this->pdo->add_consult("SELECT id FROM posts WHERE status='publish' AND post_type='post' ".$condition);	
		
		$total = $this->pdo->numRows(); 
		
		$this->_wb_posts->total = $total[0][0];
		
		switch( $this->_wb_posts->total ){
		
			case 0:
				return false;
			break;
			
			default:
			
				$this->_wb_posts->have_posts = true;
				
				return true;
				
			break;
			
		}
		
		endif;
	
	}
	
	
	/**
	 * Almacenamos temporalmente en un Array la informacion de un post.
	 * @access private
	 */	
	private function global_post(){	
				
		$ID = $this->_wb_posts->query;		
		
		$this->post->ID = $this->_wb_posts->sql[$ID]["id"];
		
		$this->post->DATE = $this->_wb_posts->sql[$ID]["post_date"];
		
		$this->post->AUTHOR = $this->_wb_posts->sql[$ID]["post_author"];
		
		$this->post->TITLE = $this->_wb_posts->sql[$ID]["title"];
		
		$this->post->PERMALINK = $this->_wb_posts->sql[$ID]["slug"];
		
		$this->post->CONTENT = $this->_wb_posts->sql[$ID]["meta"];
		
		$this->post->STATUS = $this->_wb_posts->sql[$ID]["status"];
		
		$this->post->PARENT = $this->_wb_posts->sql[$ID]["post_parent"];
		
		$this->post->PASSWORD = $this->_wb_posts->sql[$ID]["post_password"];
		
		$this->post->MODIFIED = $this->_wb_posts->sql[$ID]["post_modified"];
		
		$this->post->TYPE = $this->_wb_posts->sql[$ID]["post_type"];
		
		$this->post->COMMENT_STATUS = $this->_wb_posts->sql[$ID]["comment_status"];
		
		$this->post->COMMENT_COUNT = $this->_wb_posts->sql[$ID]["comment_count"];		
	
	}
	
	/**
	 * Carga la informacion de los posts, los guarda en un Array
	 * Cada post se muestran segun su posicion en el Array.
	 * @access public
	 */	
	public function the_post(){
		
		//global $_wb_posts;
		
		if( !$this->_wb_posts->sql ){
			
			// Vaciamos las consultas almacenadas...
			
			$this->pdo->unset_consult();
			
			// Preparamos la consulta...
			
			$conditions = "status='publish' AND post_type='post'";
			
			$reg = $this->windbloom->get->reg;
			
			if( !empty( $reg ) ) $conditions.= " AND slug='".$reg."'";
			
			if( $this->section ) $conditions.=" AND id IN(".implode(",",$this->section).")";
			
			$max = $this->Max;
						
			$pag = $this->windbloom->get->pag;
			
			if( empty($pag) ): $def = 0; $pag = 1; else : $def = ($pag - 1) * $max; endif;
			
			$pages = ceil($this->_wb_posts->total / $max);			
						
			$this->pdo->add_consult( "SELECT * FROM posts WHERE $conditions  ORDER BY id DESC LIMIT $def,$max" );			
			
			$sql = $this->pdo->query();
			
			$this->_wb_posts->sql = $sql[0];
		
		}
	
		
		$i = $this->_wb_posts->initial-1;		
		
		$this->_wb_posts->query = $i;
		
		//echo $this->_wb_posts->query;
		
		self::global_post();
				
		if( !$this->_wb_posts->sql[$this->_wb_posts->initial] ){ $this->_wb_posts->stop = true; }
		
	}
	
	/**
	 * Funcion encargada de traer los terminos a un Array
	 * Si el ID no se encuentra regresa false
	 * @access private
	 */	
	private function terms($term_id){
		
		if( !$this->term->Query ){
			
			$this->pdo->unset_consult();
			
			$this->pdo->add_consult("SELECT id,name,slug FROM terms");
			
			$terms = $this->pdo->query();
			
			$this->term->Query = $terms[0];
			
			while( list($k,$v) = each($this->term->Query) )	$this->term->Array[$v[id]] = $v;			
			
		}
		
		$return = $this->term->Array[$term_id];
		
		return array( $return[name], $return[slug], $return[id] );
		
	}

	/**
	 * Muestra el titulo del post
	 * Esta funcion regresa un echo con el valor
	 * @access public
	 */	
	public function the_title(){
		
		$ID = $this->_wb_posts->query;
		
		echo $this->_wb_posts->sql[$ID]["title"];
		
	}
	
	/**
	 * Muestra el url del post completo (ver contenido completo en the_content)
	 * Esta funcion regresa un echo con el valor
	 * @access public
	 */	
	public function the_permalink($in_var = false){		
		
		$ID = $this->_wb_posts->query;
		
		$category = $this->terms($this->_wb_posts->sql[$ID]["post_parent"]);
		
		switch ($in_var):
			case true:
				return $this->windbloom->site["url"].$category[1]."/".$this->_wb_posts->sql[$ID]["slug"].".htm";
			break;
			default:
				echo $this->windbloom->site["url"].$category[1]."/".$this->_wb_posts->sql[$ID]["slug"].".htm";
			break;
		endswitch;
		
	}
	
	/**
	 * Muestra contenido limitado o no, dependiendo de la seccion en la que se encuentre
	 * Esta funcion regresa un echo con el valor
	 * @access public
	 */	
	public function the_content($in  = false , $utf8 = NULL ){
				
		$ID = $this->_wb_posts->query;
		
		$content =  $utf8?utf8_encode($this->_wb_posts->sql[$ID]["meta"]):$this->_wb_posts->sql[$ID]["meta"];
		
		$more = strstr($content,"<!-- more -->");
		
		if($more AND empty($this->windbloom->get->sec)):
			
			$str = str_replace($more,"",$content);
			
			echo bbcode_linebreak($str);
			
			$slug = self::the_permalink(true);
			
			if(is_array($in)): 
			
			echo ''.$in[1].'<a href="'.$slug.'" class="more" id="more-'.$this->_wb_posts->sql[$ID]["id"].'">'.$in[0].'</a>'.$in[2].'';
			
			else:
			
			echo '<a href="'.$slug.'" class="more" id="more-'.$this->_wb_posts->sql[$ID]["id"].'">'.$in.'</a>';
			
			endif;
			
		else :
		
			echo bbcode_linebreak($content);
			
		endif;
		
	}
	
	/**
	 * Muestra la fecha de publicacion segun el formato $format en dateFormat()
	 * Esta funcion regresa un echo con el valor
	 * @access public
	 */	
	public function the_time($format = "F jS, y"){
		
		$ID = $this->_wb_posts->query;
		
		$date = dateFormat($this->_wb_posts->sql[$ID]["post_date"],$format,true);
		
		echo $date;
		
	}
	
	/**
	 * Muestra el nombre del autor de post
	 * Esta funcion regresa un echo con el valor
	 * @access public
	 */	
	public function the_author($sel_array = "user"){		
		
		$ID = $this->_wb_posts->query;
		
		$author = db::getData("users","user,display","WHERE id='".$_wb_posts->sql[$ID]["post_author"]."'");
		
		switch ($sel_array):
			case "display":
				echo $author[1];
			break;
			case "user":
				echo $author[0];
			break;
			default:
				echo $author[0];
			break;	
		endswitch;
		
	}
	
	/**
	 * Muestra los tags de post con su respectivo enlace a la seccion tags
	 * Esta funcion regresa un echo con el valor
	 * @access public
	 */	
	public function the_tags($ind = "Tags: ", $pre = " - ", $pos = ", ", $end = "",$force = false){
		
		$ID = ( is_numeric($force))?$force:$this->_wb_posts->query;
		
		$id = $this->_wb_posts->sql[$ID]["id"];
		
		$this->pdo->unset_consult();
		
		$this->pdo->add_consult("SELECT term_id FROM terms_relationship WHERE term_type='post_tag' AND object_id='$id' ");
		
		$sql = $this->pdo->query();
		
		$sql = $sql[0];
		
		$return = '';
		
		foreach( $sql as $line ):
			
			$tag_id = $line[0];
			
			$term = $this->terms($tag_id);
		
			$return.= $pre.'<a href="'.$this->windbloom->site["url"].'tags/'.$term[1].'/">'.$term[0].'</a>'.$pos;
		
		endforeach;
		
		$return.= $end;
		
		if( is_numeric($force) ) return $return;
		else echo $return;
		
	}
	
	/**
	 * Muestra las categorias del post con su respectivo enlace a la seccion categories
	 * Esta funcion regresa un echo con el valor
	 * @access public
	 */	
	public function the_category($separate = false,$theme = "%categories%"){
				
		$ID = $this->_wb_posts->query;
		
		$id = $this->_wb_posts->sql[$ID]["id"];		
		
		$this->pdo->unset_consult();
		
		$this->pdo->add_consult("SELECT term_id FROM terms_relationship WHERE term_type='category' AND object_id='$id' ");
		
		$sql = $this->pdo->query();
		
		$sql = $sql[0];
		
		$template = array();		
		
		 
		foreach( $sql as $line ):
			
			$tag_id = $line[0];			
					
			$term = $this->terms($tag_id);
			
			$cat = '<a href="'.$this->windbloom->site["url"].'categories/'.$term[1].'/">'.$term[0].'</a>';
			
			if(is_array($theme)):
				$template[] = $theme[0].$cat.$theme[1];
			else: 
				$template[] = str_replace("%categories%",$cat,$theme);
			endif;
		
		endforeach;
		
		if($separate AND !is_array($theme)):	
			$categories = implode(str_replace("%categories%",$separate,$theme),$template);
		else:
			$categories = implode("",$template);
		endif;
		
		echo $categories;
		
	}
	
	/**
	 * Muestra el paginado simple, atras y siguiente.
	 * Esta funcion regresa un echo con el valor
	 * @access public
	 */	
	public function nav_pag($link = "pag/%pag%/"){
		
		//global $windbloom; @obsolete
		
		//global $_wb_posts; @obsolete
				
		$have_posts = $this->_wb_posts->total;		
		
		$max = $this->Max;
					
		$pag = numeric( $this->windbloom->get->pag );
					
		if (empty($pag)): $def = 0; $pag = 1; else : $def = ($pag - 1) * $max; endif; 
			
		$pages = ceil($have_posts / $max);
		
		if( $have_posts && $pages > 1 ):
			
			$url = $this->windbloom->site["url"];
			$url.= $this->windbloom->get->app?$this->windbloom->get->app.'/'.$this->windbloom->get->post.'/':'';
			
			echo $url;
			
			echo'<div class="nav-pag">';
			
			if( $pag > 1 && $pag == 2 ){				
				echo'<a href="'.$url.'" target="_self" title="Pagina anterior" class="prev">&lsaquo; Anterior</a>';				
			} elseif( $pag > 1 ){
				echo'<a href="'.$url.str_replace("%pag%",($pag-1),$link).'" target="_self" title="Pagina anterior" class="prev">&lsaquo; Anterior</a>';
			}
			
			if( $pag < $pages ){
				echo'<a href="'.$url.str_replace("%pag%",($pag+1),$link).'" target="_self" title="Pagina siguiente" class="next">Siguiente &rsaquo;</a>';
			}
			
			echo'</div>';
		
		endif;
		
	}
	
	
	/**
	 * SEO
	 * Esta funcion regresa un echo con los meta del blog
	 * @access public
	 */	
	public function seo(){
		
		switch($this->windbloom->get->app){
			
			case "simple":
				
			$this->the_post();
			
			//print_r($this->_wb_posts->sql);
			
			$post = $this->_wb_posts->sql[0];
			
			$this->title = $post[title];
			$this->description = trim(substr(cleanHtml($post[meta]),0,160));
			$this->keywords = cleanHtml($this->the_tags("",""," ","",0));
			
			break;
		
			case "tags":
			echo"tags";
			break;
			
			case "categories":
			echo"ctagorias";
			break;
		
			default:
			echo"home";
			break;
			
		}
		
		$pagina = $this->windbloom->get->pag?" - Pagina ".$this->windbloom->get->pag:"";
		
		$en = $this->windbloom->get->app?' En ':'';
		
		echo "<title>".$this->title.$pagina."</title>\n";
		echo "<meta name=\"description\" content=\"$this->description\" /> \n";
		echo "<meta name=\"keywords\" content=\"$this->keywords\" /> \n";
		echo "<meta name=\"title\" content=\"$this->title\" /> \n";
	}
	
	function have_comments($num_comments = false){
	
		global $_wb_posts;
		
		$ID = $_wb_posts->query;
				
		if($_wb_posts->have_comments):
		
			$_wb_posts->initial_comments++;
			
			if( $_wb_posts->initial_comments == $_wb_posts->sql[$ID]["comment_count"]+1 ):
			
				return false;
				
			else :
			
				return true;
				
			endif;
				
					
		else :
			
			if(!$num_comments):
		
				switch ($_wb_posts->sql[$ID]["comment_count"]):
					
					case 0:
						return false;
					break;
					
					default:
						$_wb_posts->have_comments = true;	
						return true;									
					break;
					
				endswitch;
			
			else: /* Regresa el numero de comentarios */ 
			
				return $_wb_posts->sql[$ID]["comment_count"]; 
				
			endif;		
		
		endif;
			
	}
	
	function global_comment(){
	
		global $_wb_posts;
		
		global $_wb_comment;
		
		$ID = $_wb_posts->comments_array_id;
		
		# $_wb_posts->comments[$ID][""];
		
		$_wb_comment->ID = $_wb_posts->comments[$ID]["id"];
		
		$_wb_comment->post_id = $_wb_posts->comments[$ID]["post_id"];
		
		$_wb_comment->author = $_wb_posts->comments[$ID]["comment_author"];
		
		$_wb_comment->email = $_wb_posts->comments[$ID]["comment_author_email"];
		
		$_wb_comment->url = $_wb_posts->comments[$ID]["comment_author_url"];
		
		$_wb_comment->ip = $_wb_posts->comments[$ID]["comment_author_ip"];
		
		$_wb_comment->date = $_wb_posts->comments[$ID]["comment_date"];
		
		$_wb_comment->meta = $_wb_posts->comments[$ID]["comment_meta"];
		
		$_wb_comment->approve = $_wb_posts->comments[$ID]["comment_approve"];
		
		$_wb_comment->type = $_wb_posts->comments[$ID]["comment_type"];
		
		$_wb_comment->parent = $_wb_posts->comments[$ID]["comment_parent"];
		
		$_wb_comment->user_id = $_wb_posts->comments[$ID]["user_id"];
		
	}
	
	function the_comment(){
			
		global $_wb_posts;
		
		global $_wb_post;
		
		if( !$_wb_posts->comments ):
		
			$sql = query::get(array("comments",
									"*",
									array(
										  array("post_id='".$_wb_post->ID."'","comment_approve='1'","comment_parent='0'")
										  ), 
									array(
										  array("comment_date"),
										  "ASC"),  // ORDER
									false // LIMIT
									)
							  );
			
			$_wb_posts->comments = $sql;
		
		endif;
		
		$array_id = $_wb_posts->initial_comments-1;
		
		$_wb_posts->comments_array_id = $array_id;
		
		self::global_comment();
		
		#echo"<pre>";
		#print_r($_wb_posts->comments_array_id);
		#echo"</pre>";
		
	}
	
	function comments_number($none = "Sin comentarios",$one = "1 comentario",$some = "% comentarios"){		
		
		$ID = $this->_wb_posts->query;
		
		switch ( $this->_wb_posts->sql[$ID]["comment_count"] ) :
		
			case 0:
				echo $none;
			break;
			case 1:
				echo $one;
			break;
			default:
				echo str_replace("%",$this->_wb_posts->sql[$ID]["comment_count"],$some);
			break;
		
		endswitch;
		
	}
	
	function comment_template(){
				
		global $_wb_comment;
		
		include($this->site["theme"]."comments.php");	
	}
	
	function comment_status($status = "open"){
		
		global $_wb_posts;
		
		$ID = $_wb_posts->query;
		
		switch ($_wb_posts->sql[$ID]["comment_status"]):
			
			case ($status == $_wb_posts->sql[$ID]["comment_status"]):
				return true;
			break;
			
			default:
				return false;
			break;
			
		endswitch;
		
	}
	
	function comment_date($format = "F jS, y"){
			
		global $_wb_comment;
		
		$date = dateFormat($_wb_comment->date,$format,true);
		
		echo $date;
		
	}
	
	function comment_avatar($gravatar = true, $html = true){
		
		global $windbloom;
		
		global $_wb_comment;
		
		if( $_wb_comment->user_id ):
		
			$avatar = db::getData("users_meta","meta_value","WHERE user_id='".$_wb_comment->user_id."' AND meta_key='avatar' LIMIT 1");
			
			switch ($avatar[0]){
				
				case "gravatar.com":
					$url = "http://www.gravatar.com/avatar/".md5($_wb_comment->email);
				break;
				
				case "":
					$url = $windbloom->site["url"].$windbloom->site["sys_images"].$windbloom->site["avatar"]."default.jpg";
				break;
				
				default:
					$url =  $windbloom->site["url"].$avatar[0];
				break;
			}
			
		else:
			
			switch($gravatar){
				
				case true:
					$url = "http://www.gravatar.com/avatar/".md5($_wb_comment->email);
				break;
				
				default:
					$url = $windbloom->site["url"].$windbloom->site["sys_images"].$windbloom->site["avatar"]."default.jpg";
				break;
			}
	
			
		endif;
		
		# <img src="http://www.gravatar.com/avatar/<?=md5("zeickan@gmail.com"); >" border="0" />
		if($html):
			echo '<img src="'.$url.'" border="0" alt="author_avatar" />';
		else:
			echo $url;
		endif;
		
	}
	
	function send_comment(){
		
		global $_wb_posts;
		
		global $_wb_send_comment;
		
		$ID = $_wb_posts->query;
		
		$id = $_wb_posts->sql[$ID]["id"];
				
		if($_wb_send_comment):
			
			$name = cleanHtml(antiI($_POST["comment_author"]));
		
			$mail = cleanHtml(antiI($_POST["comment_mail"]));
			
			$site = url_encrypt(antiI($_POST["comment_url"]));
			
			$comm = bbcode_linebreak(htmlentities(cleanHtml($_POST["comment_meta"])));
		
			$atr = "post_id,comment_author,comment_author_email,comment_author_url,comment_author_ip,comment_date,comment_meta,comment_approve,comment_type,comment_parent,user_id";
			
			$values = "'$id','$name','$mail','$site','".$_SERVER['REMOTE_ADDR']."','".time()."','$comm','1','post','0','1'";
			
			db::mysqlInsert("comments",$atr,$values);
			
			db::mysqlUpdate("posts","comment_count='".($_wb_posts->sql[$ID]["comment_count"]+1)."'","WHERE id='$id'");
			
			$_wb_posts->sql[$ID]["comment_count"] = $_wb_posts->sql[$ID]["comment_count"]+1;
			
			return true;
		
		else:
		
			return false;
		
		endif;
	
	}
	
	

	
}


