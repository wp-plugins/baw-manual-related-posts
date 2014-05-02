<?php
/*
Plugin Name: BAW Manual Related Posts
Plugin URI: http://www.boiteaweb.fr/mrp
Description: Set related posts manually but easily with great ergonomics! Stop displaying auto/random related posts!
Version: 1.8.1
Author: Julio Potier
Author URI: http://www.boiteaweb.fr
*/

define( 'BAWMRP__FILE__', __FILE__ );
define( 'BAWMRP_PLUGIN_URL', plugin_dir_url( __FILE__ ) );
define( 'BAWMRP_FULLNAME', 'Manual Related Posts' );
define( 'BAWMRP_VERSION', '1.8.1' );

add_action( 'plugins_loaded', 'bawmrp_bootstrap' );
function bawmrp_bootstrap() {
	$folder = 'inc/';
	$where = is_admin() ? 'backend-' : 'frontend-';
	$pre_ajax = defined( 'DOING_AJAX' ) && DOING_AJAX ? '' : 'no';
	$ajax = 'ajax.inc.php';
	$filename = $folder . $where . $pre_ajax . $ajax;
	if( file_exists( plugin_dir_path( __FILE__ ) . $filename ) ) {
		include( plugin_dir_path( __FILE__ ) . $filename );
	}
	$where = 'bothend-';
	$filename = $folder . $where . $pre_ajax . $ajax;
	if( file_exists( plugin_dir_path( __FILE__ ) . $filename ) ) {
		include( plugin_dir_path( __FILE__ ) . $filename );
	}
}