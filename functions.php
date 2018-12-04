<?php
add_action( 'wp_enqueue_scripts', 'dndtheme_enqueue_styles' );
add_filter( 'generate_after_entry_title' , 'dnd_reviewpostitle' );
add_filter( 'generate_after_entry_content' , 'dnd_reviewmeta' );

function dndtheme_enqueue_styles() {
    $parent_style = 'GeneratePress-style';
    wp_enqueue_style( 'parent-style', get_template_directory_uri() . '/style.css' );
    wp_enqueue_style( 'child-style',
        get_stylesheet_directory_uri() . '/style.css',
        array( $parent_style ),
        wp_get_theme()->get('Version')
    );
}

class dnd_post_stars {
	public static $instance;
	private static $stars;
	function __construct() {
		if (isset(self::$instance)) return;
		self::$instance = $this;
		self::$stars = array();
	}
	public static function get_stars( $postid ) {
		if (!empty(self::$stars[$postid])) return self::$stars[$postid];
		$starv = intval( get_post_meta( $postid, '_dnd_review_stars', true ) );
		self::$stars[$postid] = '<span class="starRender star'.$starv.'"></span>';
		return self::$stars[$postid];
	}
}

/** 
 * Add manufactorer to post title section
 */
function dnd_reviewpostitle( $hookstring ) {
	if (get_post_type() != 'dnd_review') return $hookstring;
	global $post;

	echo '<div class="entry-meta">';
	generate_posted_on();
	
	$manuf = get_post_meta( $post->ID, '_dnd_review_manufacturer', true );
	if ( !empty($manuf) ) {
		echo '<div class="dnd_review_manufacturer"><span>'.$manuf.'</span></div>';
	}
	echo dnd_post_stars::get_stars( $post->ID );
	echo '</div>';
}
function dnd_reviewmeta( $hookstring ) {
	if (get_post_type() != 'dnd_review') return $hookstring;
	$id = get_the_ID();

	echo '<div class="stars_wide"><span class="header">Review Score: </span>'.dnd_post_stars::get_stars( $id ).'</div>';

	$links = 		get_post_meta ( $id, '_dnd_review_links', true);
	$ingredients = 	get_post_meta ( $id, '_dnd_review_ingredients', true);
	
	if (empty($links.$ingredients)) return $hookstring;
	echo '<div class="dnd_review_meta">';
	if (!empty($links)) {
		// formatting the meta data from raw text
		$links = str_replace( "\n" , ',' , htmlspecialchars( $links ) );
		while (substr_count( $links , ',,') > 0) {
			$links = str_replace( ",," , ',' , $links);
		}
		$links = explode( ',', str_replace( "\n" , ',' , $links ) );

		echo '<div class="review-links"><h4>Links</h4><ul>';
		foreach ($links as $link) {
			if ( substr( $link, 0, 4 ) != 'http' ) {
				$link = 'http://'.$link;
			}
			echo '<li><a href="'.$link.'" target="_blank" rel="external">'.$link.'</a></li>';
		}
		echo '</div>';
	}
	if (!empty($ingredients)) {
		$ingredients = str_replace( ',' , "<br>" , nl2br(htmlspecialchars($ingredients)) );
		echo '<div class="review-ingredients"><h4>Ingredients</h4><div>'.$ingredients.'</div>';
	}
	echo '</div><!-- end dnd_review_meta -->';
}


