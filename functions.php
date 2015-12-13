<?php
//* Start the engine
include_once( get_template_directory() . '/lib/init.php' );

//* Child theme (do not remove)
define( 'CHILD_THEME_NAME', 'Genesis Sample Theme' );
define( 'CHILD_THEME_URL', 'http://www.studiopress.com/' );
define( 'CHILD_THEME_VERSION', '2.1.2' );

//* Enqueue Google Fonts
add_action( 'wp_enqueue_scripts', 'genesis_sample_google_fonts' );
function genesis_sample_google_fonts() {

	wp_enqueue_style( 'google-fonts', '//fonts.googleapis.com/css?family=Lato:300,400,700', array(), CHILD_THEME_VERSION );

}

/** Support for various Image sizes */
add_image_size('post-image', 900, 400, TRUE );

/* Code to Display Featured Image on top of the post */

add_action( 'genesis_before_entry', 'featured_page_image', 8 );
function featured_page_image() {
  if ( ! is_singular( 'page'  ) )  return;
	the_post_thumbnail('post-image');
}


add_action( 'genesis_entry_content', 'featured_post_image', 8 );
function featured_post_image() {
  if ( ! is_singular( 'post'  ) )  return;
	the_post_thumbnail('post-image');
}

//* Modify the length of post excerpts
add_filter( 'excerpt_length', 'sp_excerpt_length' );
function sp_excerpt_length( $length ) {
	return 5; // pull first 5 words

}


//* Add HTML5 markup structure
add_theme_support( 'html5', array( 'search-form', 'comment-form', 'comment-list' ) );

//* Add viewport meta tag for mobile browsers
add_theme_support( 'genesis-responsive-viewport' );

//* Add support for custom background
add_theme_support( 'custom-background' );

//* Add support for 3-column footer widgets
add_theme_support( 'genesis-footer-widgets', 3 );

// =================================================================
// = Pinterest-like Masonry layout for Posts page and Archives =
// =================================================================

//* Enqueue and initialize jQuery Masonry script
function sk_masonry_script() {
	if (is_home() || is_archive()) {

		wp_enqueue_script( 'masonry-init', get_stylesheet_directory_uri().'/js/masonry-init.js' , array( 'jquery-masonry' ), '1.0', true );


    	//* Infinite Scroll
    	wp_enqueue_script( 'infinite-scroll', get_stylesheet_directory_uri().'/js/jquery.infinitescroll.min.js' , array('jquery'), '1.0', true );
    	wp_enqueue_script( 'infinite-scroll-init', get_stylesheet_directory_uri().'/js/infinitescroll-init.js' , array('jquery'), '1.0', true );
    }
}
add_action( 'wp_enqueue_scripts', 'sk_masonry_script' );

//* Add custom body class to the head
add_filter( 'body_class', 'sk_body_class' );
function sk_body_class( $classes ) {
	if (is_home()||is_archive())
		$classes[] = 'masonry-page';
		return $classes;
}

//* Display Post thumbnail, Post title, Post content/excerpt, Post info and Post meta in masonry brick
add_action('genesis_meta','sk_masonry_layout');
function sk_masonry_layout() {
	if (is_home()||is_archive()) {

		add_filter( 'genesis_pre_get_option_site_layout', '__genesis_return_content_sidebar' );

		remove_action( 'genesis_entry_header', 'genesis_do_post_title' );
		remove_action( 'genesis_entry_header', 'genesis_post_info', 12 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_open', 5 );
		remove_action( 'genesis_entry_header', 'genesis_entry_header_markup_close', 15 );

		remove_action( 'genesis_entry_content', 'genesis_do_post_image', 8 );
		remove_action( 'genesis_entry_content', 'genesis_do_post_content' );

		remove_action( 'genesis_entry_footer', 'genesis_post_meta' );

		add_action( 'genesis_entry_content', 'sk_masonry_block_post_image', 8 ) ;
		add_action( 'genesis_entry_content', 'sk_masonry_title_content', 9 );

		add_action( 'genesis_entry_footer', 'sk_masonry_entry_footer' );

		remove_action( 'genesis_before_loop', 'genesis_do_breadcrumbs' );
		add_action( 'genesis_before_content', 'genesis_do_breadcrumbs' );

		remove_action( 'genesis_before_loop', 'genesis_do_taxonomy_title_description', 15 );
		add_action( 'genesis_before_content', 'genesis_do_taxonomy_title_description', 15 );

		remove_action( 'genesis_before_loop', 'genesis_do_author_title_description', 15 );
		add_action( 'genesis_before_content', 'genesis_do_author_title_description', 15 );

		remove_action( 'genesis_before_loop', 'genesis_do_author_box_archive', 15 );
		add_action( 'genesis_before_content', 'genesis_do_author_box_archive', 15 );

		remove_action( 'genesis_before_loop', 'genesis_do_cpt_archive_title_description' );
		add_action( 'genesis_before_content', 'genesis_do_cpt_archive_title_description' );

		remove_action( 'genesis_after_endwhile', 'genesis_posts_nav' );
		add_action( 'genesis_after_content', 'genesis_posts_nav' );

	}
}


//* Helper function to display Post title and Post content/excerpt wrapped in a custom div
function sk_masonry_title_content() {
	echo '<div class="title-content">';
		genesis_do_post_title();
		genesis_do_post_content();
	echo '</div>';
}

//* Helper function to display Post info and Post meta
function sk_masonry_entry_footer() {
		genesis_post_info();
		genesis_post_meta();
}

//* Set the second parameter to width of your masonry brick (.home .entry, .archive .entry)
add_image_size( 'masonry-brick-image', 255, 0, TRUE );

//* Helper function to display featured image
//* Source: http://surefirewebservices.com/development/genesis-framework/using-the-genesis-featured-image
function sk_masonry_block_post_image() {
		$img = genesis_get_image( array( 'format' => 'html', 'size' => 'masonry-brick-image', 'attr' => array( 'class' => 'post-image' ) ) );
		printf( '<a href="%s" title="%s">%s</a>', get_permalink(), the_title_attribute( 'echo=0' ), $img );
}

//* Add more link when using excerpts
function sk_excerpt_more($more) {
    return '<a class="more-link" href="'. get_permalink() . '">&nbsp;&nbsp;[Continue Reading]</a>';
}
add_filter('excerpt_more', 'sk_excerpt_more');

//* Modify the length of post excerpts
add_filter( 'excerpt_length', 'sk_excerpt_length' );
function sk_excerpt_length( $length ) {
	return 10; // pull first 10 words
}

//* Make Font Awesome available
add_action( 'wp_enqueue_scripts', 'enqueue_font_awesome' );
function enqueue_font_awesome() {

	wp_enqueue_style( 'font-awesome', '//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css' );

}



//* Customize search form input button text
add_filter( 'genesis_search_button_text', 'sk_search_button_text' );
function sk_search_button_text( $text ) {

	return esc_attr( '&#xf002;' );

}

add_action( 'genesis_entry_footer', 'sk_single_post_nav', 9 );
function sk_single_post_nav() {

	if ( is_singular('post' ) ) {

		$prev_post = get_adjacent_post(false, '', true);
		$next_post = get_adjacent_post(false, '', false);
		echo '<div class="prev-next-post-links">';
			previous_post_link( '<div class="previous-post-link" title="Previous Post: ' . $prev_post->post_title . '">%link </div>', 'Previous Post' );
			next_post_link( '<div class="next-post-link" title="Next Post: ' . $next_post->post_title . '">%link</div>', 'Next Post' );
		echo '</div>';

	}

}

// Enqueue scripts and styles
add_action( 'wp_enqueue_scripts', 'custom_scripts_styles_mobile_responsive' );
function custom_scripts_styles_mobile_responsive() {

	wp_enqueue_script( 'beautiful-responsive-menu', get_stylesheet_directory_uri() . '/js/responsive-menu.js', array( 'jquery' ), '1.0.0' );
	wp_enqueue_style( 'dashicons' );

}

// Enqueue To Top script
add_action( 'wp_enqueue_scripts', 'to_top_script' );
function to_top_script() {
    wp_enqueue_script( 'to-top', get_stylesheet_directory_uri() . '/js/to-top.js', array( 'jquery' ), '1.0', true );
}

// Add To Top button
add_action( 'genesis_before', 'genesis_to_top');
	function genesis_to_top() {
	 echo '<a href="#0" class="to-top" title="Back To Top">Top</a>';
}

// Do not copy opening php tag

// Force IE to NOT use compatibility mode
add_filter( 'wp_headers', 'wsm_keep_ie_modern' ); 
function wsm_keep_ie_modern( $headers ) {
        if ( isset( $_SERVER['HTTP_USER_AGENT'] ) && ( strpos( $_SERVER['HTTP_USER_AGENT'], 'MSIE' ) !== false ) ) {
                $headers['X-UA-Compatible'] = 'IE=edge,chrome=1';
        }
        return $headers;
}
