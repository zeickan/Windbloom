<?php

/*
 * class main 
 */

class productos extends models {

	/*
     * __construct()
     * @param $arg
     */
    
    function __construct() {	

    	global $windbloom;

    	$this->url_site = $windbloom->sys['url'];
        
        $this->url_app = $this->url_site.'';

        $this->current_link();

    }
    
    protected function header(){

    	/* Armamos el HEADER DEFAULT */

    	$item[] = parent::header();
	
		$cssPath = "static/stylesheets/";

		return join("\n    ",$item);
	    
    }
    
    
    public function main(){

    	$this->dashboard();

    }
    
    /*
     * function dashboard
     */
    
    function dashboard() {
	
		# Titulo adicional
		$this->title.= "Aceros Vimar - Productos";
				
		# Funciones de remplazo: header function    
		$this->header = array('function' => 'header');

		$this->content = array('function' => 'categorias');

		$this->readfiletemplate("base.html");
		
	}

	protected function categorias(){

		$c = (object) array();
		
		$c->static = $this->url_site."static/";
		        
        $c->url_site = $this->url_site;
        
        $c->title = "Title";

        #### CATEGORIAS #####

        $pdo = new db_pdo();
		$pdo->add_consult("SELECT * FROM aceros_categoria WHERE inherit_id IS NULL ");
		$query = $pdo->query();
        $c->categorias = array('function'=>'lala','param'=>$query[0]);

		$c->rows = print_r($query[0],1);

		return $this->render("productos.html",true,$c);

	}


	protected function lala($query){
		$tmp = array();
		while(list($foo,$bar) = each($query) ):
			$pdo = new db_pdo();
			$pdo->add_consult("SELECT * FROM aceros_categoria WHERE inherit_id='".$bar['id']."' LIMIT 5 ");
			$subs = $pdo->query();
			$ele = '';
			$ids = array($bar['id']);
			$ele.= '<section class="columns">
			 	<h2><span><a href="">'.utf8_encode($bar['nombre']).'</a></span></h2>
			 	<section class="col1 subcatsContainer"><ul class="subcats">';			 	
				foreach($subs[0] as $key => $value) {
					$ids[] = $value['id'];
					$ele.= '<li><a href="">'.utf8_encode($value["nombre"]).'</a></li>';
				}
			$pdo = new db_pdo();
			$pdo->add_consult("SELECT * FROM aceros_producto WHERE categoria_id IN(".join(',',$ids).") ORDER BY rand() LIMIT 2 ");
			$rows = $pdo->query();			 		
			$ele.= '</ul></section>';
			if( $rows[0] ):
			 	foreach($rows[0] as $k => $v){
			 		$ele.='<article class="col2 team">
						 		<div class="img-border"><img src="'.$this->url_site.'thumbnails/media/'.$v["imagen"].'" alt=""></div>
						 		<h3> <a href="item@id='.$v["id"].'">'.utf8_encode($v["nombre"]).'</a></h3>
						 		<p>'.$v["description"].'</p>
						 		<p><a href="javascript:void()" class="button">+ Cotizar</a></p>
						 	</article>';
			 	}
			 	$ele.='<p class="more masproductos"><a href="#">Ver más productos de '.utf8_encode($bar['nombre']).'</a></p>';
			endif;
			$ele.= '</section>';

			if( count($rows[0]) >= 2 ){ $tmp[] = $ele; }

		endwhile;
		return join("", $tmp);
	}

	function item(){

		$id = numeric($_GET['id']);


        $pdo = new db_pdo();
		$pdo->add_consult("SELECT aceros_producto.id, 
								aceros_producto.nombre, 
								aceros_producto.description, 
								aceros_producto.categoria_id, 
								aceros_producto.imagen, 
								aceros_producto.ventaja, 
								aceros_producto.ficha, 
								aceros_categoria.nombre as categoria
							FROM aceros_producto INNER JOIN aceros_categoria ON aceros_producto.categoria_id = aceros_categoria.id
							WHERE aceros_producto.id='$id' LIMIT 1");
		$pdo->add_consult("SELECT * FROM aceros_medidas WHERE producto_id='$id'");
		$query = $pdo->query();

		# Titulo adicional
		$this->title.= "Aceros Vimar - ".$query[0][0]['nombre'];
				
		# Funciones de remplazo: header function    
		$this->header = array('function' => 'header');

		$this->content = array('function' => 'details','param' => $query );

		$this->readfiletemplate("base.html");


	}

	public function details($query){

		$c = (object) array();
		
		$c->static = $this->url_site."static/";
		        
        $c->url_site = $this->url_site;
        
        $c->titulo = $query[0][0]['nombre'];
        $c->details = $query[0][0]['description'];
        $c->categoria = $query[0][0]['categoria'];

        $pdo = new db_pdo();
		$pdo->add_consult("SELECT *	FROM aceros_producto WHERE categoria_id='".$query[0][0]['categoria_id']."' LIMIT 6");
		$rels = $pdo->query();
		$tmp = '';
		if($rels[0]):
			foreach($rels[0] as $raw => $row){
				$tmp.='<div class="col6"><div class="img">
								<a href="#"><span class="img-border"><img src="'.$this->url_site.'mini/media/'.$row["imagen"].'" alt=""></span></a>
							</div>
							<a href="#">'.$row["nombre"].'</a>
						</div>';
			}
		endif;
		$c->relacionados = $tmp;

        

        $c->thumbnail = $this->url_site.'thumbnails/media/'.$query[0][0]["imagen"].'';

        if( $query[1] ):
        	$tmp = '';
        	foreach($query[1] as $row){
        		$tmp.= '<option>'.$row['nombre'].'</option>';
        	}
    	endif;
    	$c->table = $tmp;


		return $this->render("producto.html",true,$c);

	}

}