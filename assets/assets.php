<?php

/**
 * Class for managing plugin/theme assets

 */
class Bcat_Assets {

	/**
	 * Set of queried assets
	 *
	 * @var array
	 */
	static $assets = array( 'css' => array(), 'js' => array() );

	/**
	 * Constructor
	 */
	function __construct() {
		// Register
		add_action( 'wp_head',                     array( __CLASS__, 'register' ) );
	
		// Enqueue
		add_action( 'wp_footer',                   array( __CLASS__, 'enqueue' ) );

	}

	/**
	 * Register assets
	 */
	public static function register() {
/* js */	
	 wp_register_script ('load-more-script',  get_stylesheet_directory_uri() . '/assets/js/load-more.js', array('jquery'), CH_KADENCE_VERSION, true );
	
	   wp_register_script ('ajax-filter',  get_stylesheet_directory_uri() . '/assets/js/ajax-filter.js', array('jquery'), CH_KADENCE_VERSION, true ); 
	   wp_register_script ('custom',  get_stylesheet_directory_uri() . '/assets/js/custom.js', array('jquery'), CH_KADENCE_VERSION, true );
        wp_register_script('popperjs',  "https://unpkg.com/@popperjs/core@2", array(), false, true);
        wp_register_script('tippy', "https://unpkg.com/tippy.js@6", array('popperjs'), false, true);
	           wp_add_inline_script( 'tippy', "tippy('[data-tippy-content]', { placement: 'top-start' });");


 /* css */

	    wp_register_style('theme-pciwgas-publlic-style', get_stylesheet_directory_uri(). '/assets/css/categoryimage-public.css', array(), CH_KADENCE_VERSION);
	    wp_register_style( 'filter', get_stylesheet_directory_uri() . '/assets/css/filter.css', array(), CH_KADENCE_VERSION );
        wp_register_style( 'shortcode-filter', get_stylesheet_directory_uri() . '/assets/css/shortcode-filter.css', array(), CH_KADENCE_VERSION );
	}

	/**
	 * Enqueue assets
	 */
	public static function enqueue() {
		// Get assets query and plugin/theme object
		$assets = self::$assets;
		// Enqueue stylesheets
		foreach ( $assets['css'] as $style ) wp_enqueue_style( $style );
		// Enqueue scripts
		foreach ( $assets['js'] as $script ) wp_enqueue_script( $script );

	}


	/**
	 * Add asset to the query
	 */
	public static function add( $type, $handle ) {
		// Array with handles
		if ( is_array( $handle ) ) { foreach ( $handle as $h ) self::$assets[$type][$h] = $h; }
		// Single handle
		else self::$assets[$type][$handle] = $handle;
	}


}

new Bcat_Assets;

/**
 * Helper function to add asset to the query
 *
 * @param string  $type   Asset type (css|js)
 * @param mixed   $handle Asset handle or array with handles
 */
function su_query_asset( $type, $handle ) {
	Bcat_Assets::add( $type, $handle );
}
