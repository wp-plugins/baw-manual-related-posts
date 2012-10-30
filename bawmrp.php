<?php
/*
Plugin Name: BAW Manual Related Posts
Plugin URI: http://www.boiteaweb.fr
Description: Set related posts manually but easily with great ergonomics! Stop displaying auto/random related posts!
Version: 1.7.4
Author: Juliobox
Author URI: http://www.boiteaweb.fr
*/

define( 'BAWMRP__FILE__', __FILE__ );
define( 'BAWMRP_PLUGIN_DIR', plugin_dir_path( __FILE__ ) );
define( 'BAWMRP_FULLNAME', 'Manual Related Posts' );
define( 'BAWMRP_VERSION', '1.7.4' );

add_action( 'plugins_loaded', 'bawmrp_plugin_loaded' );
function bawmrp_plugin_loaded()
{
	$filename  = 'inc/';
	$filename .= is_admin() ? 'backend-' : 'frontend-';
	$filename .= defined('DOING_AJAX') && DOING_AJAX ? 'ajax' : 'noajax';
	$filename .= '.inc.php';
	if( file_exists( BAWMRP_PLUGIN_DIR . $filename ) )
		include( BAWMRP_PLUGIN_DIR . $filename );
	$filename  = 'inc/';
	$filename .= 'bothend-';
	$filename .= defined('DOING_AJAX') && DOING_AJAX ? 'ajax' : 'noajax';
	$filename .= '.inc.php';
	if( file_exists( BAWMRP_PLUGIN_DIR . $filename ) )
		include( BAWMRP_PLUGIN_DIR . $filename );
}