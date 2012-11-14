<?php
if( !defined( 'ABSPATH' ) )
	die( 'Cheatin\' uh?' );

function bawmrp_find_posts_div()
{
	global $bawmrp_options;
?>
	<div id="find-posts" class="find-box" style="display:none;">
		<div id="find-posts-head" class="find-box-head"><?php _e( 'Find related posts', 'bawmrp' ); ?></div>
		<div class="find-box-inside">
			<div class="find-box-search">

				<input type="hidden" name="affected" id="affected" value="" />
				<?php wp_nonce_field( 'find-posts', '_ajax_nonce', false ); ?>
				<label class="screen-reader-text" for="find-posts-input"><?php _e( 'Search' ); ?></label>
				<input type="text" id="find-posts-input" name="ps" value="" />
				<input type="button" id="find-posts-search" value="<?php esc_attr_e( 'Search' ); ?>" class="button" /> <span class="spinner"></span><br />
				<?php
				$post_types = get_post_types( array( 'public' => true, 'show_ui'=>true ), 'objects' );
				foreach ( $post_types as $post ):
					if ( 'attachment' == $post->name || !in_array( $post->name, $bawmrp_options['post_types'] ) )
						continue;
					?>
					<input type="checkbox" name="find-posts-what[]" id="find-posts-<?php echo esc_attr( $post->name ); ?>" value="<?php echo esc_attr( $post->name ); ?>" checked="checked" />
					<label for="find-posts-<?php echo esc_attr( $post->name ); ?>"><?php echo $post->label; ?></label>
					<?php
				endforeach;
				?>
			</div>
			<div id="find-posts-response"></div>
		</div>
		<div class="find-box-buttons">
			<input id="find-posts-close" type="button" class="button alignleft" value="<?php esc_attr_e( 'Close' ); ?>" />
			<?php submit_button( __( 'Select' ), 'button-primary alignright', 'find-posts-submit', false ); ?>
		</div>
	</div>
<?php
}

add_action( 'add_meta_boxes','bawmrp_add_meta_box' );
function bawmrp_add_meta_box()
{
	global $bawmrp_options;
	if( !empty( $bawmrp_options['post_types'] ) )
		foreach( $bawmrp_options['post_types'] as $cpt )
			add_meta_box( 'bawmrp', BAWMRP_FULLNAME, 'bawmrp_box', $cpt, 'side' );
}

function bawmrp_box( $post )
{
	$related_post_ids = bawmrp_get_related_posts( $post->ID, false );
	$related_post_ids = is_array( $related_post_ids ) ? '' : $related_post_ids;
	?>
	<div>
		<input class="hide-if-js" type="text" name="bawmrp_post_ids" id="bawmrp_post_ids" value="<?php echo esc_attr( $related_post_ids ); ?>" />&nbsp;&nbsp;
		<?php wp_nonce_field( 'add-relastedpostsids_' . $post->ID, '_wpnonce_yyarpp' ); ?>
		<div>
			<a href="javascript:void(0);" id="bawmrp_open_find_posts_button" class="button-secondary hide-if-no-js"><?php _e( 'Add a related post', 'bawmrp' ); ?></a>
			<a href="javascript:void(0);" id="bawmrp_delete_related_posts" class="button-secondary hide-if-no-js"><?php _e( 'Clear all', 'bawmrp' ); ?></a>
			<span class="hide-if-js"><?php _e( 'Add posts IDs from posts you want to relate, comma separated.', 'bawmrp' ); ?></span>
		</div>
		<ul id="ul_yyarpp" class="tagchecklist">
			<?php
			if( !empty( $related_post_ids ) ):
				$related_post_ids = wp_parse_id_list( $related_post_ids );
				foreach( $related_post_ids as $id ) : ?>
					<li data-id="<?php echo (int)$id; ?>"><span style="float:none;"><a class="hide-if-no-js erase_yyarpp">X</a>&nbsp;&nbsp;<?php echo get_the_title( (int)$id ); ?></span></li>
			<?php endforeach;
			endif;?>
		</ul>
	</div>
	<?php
}

add_action( 'admin_head-post.php', 'bawmrp_admin_head_scripts' );
add_action( 'admin_head-post-new.php', 'bawmrp_admin_head_scripts' );
function bawmrp_admin_head_scripts()
{
	$suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
	$file = "bawmrp$suffix.js";
	wp_enqueue_script( 'bawmrp_js', plugins_url( 'js/' . $file, BAWMRP__FILE__ ), 'jquery', BAWMRP_VERSION, true );
	wp_localize_script( 'bawmrp_js', 'bawmrp_js', array( 'ID'=>$GLOBALS['post']->ID ) );
}

add_action( 'admin_footer-post.php', 'bawmrp_admin_footer_scripts' );
add_action( 'admin_footer-post-new.php', 'bawmrp_admin_footer_scripts' );
function bawmrp_admin_footer_scripts()
{
	global $bawmrp_options, $typenow;
	if( !empty( $bawmrp_options['post_types'] ) && in_array( $typenow, $bawmrp_options['post_types'] ))
		bawmrp_find_posts_div();
}

add_action( 'save_post', 'bawmmrp_save_post' );
function bawmmrp_save_post( $post_id )
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE )
		return $post_id;
	if( isset( $_POST['bawmrp_post_ids'], $_POST['post_ID'] ) ):
		check_admin_referer( 'add-relastedpostsids_' . $_POST['post_ID'], '_wpnonce_yyarpp' );
        $ids = implode( ',', array_filter( wp_parse_id_list( $_POST['bawmrp_post_ids'] ) ) );
		update_post_meta( $_POST['post_ID'], '_yyarpp', $ids );
	endif;
}

add_filter( 'plugin_action_links_' . plugin_basename( BAWMRP__FILE__ ), 'bawmrp_settings_action_links' );
function bawmrp_settings_action_links( $links )
{
	array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=bawmrp_settings' ) . '">' . __( 'Settings' ) . '</a>' );
	return $links;
}

add_action( 'admin_menu', 'bawmrp_create_menu' );
function bawmrp_create_menu()
{
	add_options_page( BAWMRP_FULLNAME, BAWMRP_FULLNAME, 'manage_options', 'bawmrp_settings', 'bawmrp_settings_page' );
	register_setting( 'bawmrp_settings', 'bawmrp' );
}

register_activation_hook( BAWMRP__FILE__, 'bawmrp_activation' );
function bawmrp_activation()
{
	global $bawmrp_options;
	add_option( 'bawmrp', $bawmrp_options );
}

register_uninstall_hook( BAWMRP__FILE__, 'bawmrp_uninstaller' );
function bawmrp_uninstaller()
{
	global $wpdb;
	delete_option( 'bawmrp' );
	$wpdb->query( 'DELETE FROM ' . $wpdb->postmeta . ' WHERE meta_key="_yyarpp"' );
}

function bawmrp_settings_page()
{
	add_settings_section( 'bawmrp_settings_page', __( 'General', 'bawmrp' ), '__return_false', 'bawmrp_settings' );
		add_settings_field( 'bawmrp_field_max_posts', __( 'How many posts maximum', 'bawmrp' ), 'bawmrp_field_max_posts', 'bawmrp_settings', 'bawmrp_settings_page' );
		add_settings_field( 'bawmrp_field_random_posts', __( 'Randomize related posts', 'bawmrp' ), 'bawmrp_field_random_posts', 'bawmrp_settings', 'bawmrp_settings_page' );
		add_settings_field( 'bawmrp_field_post_types', __( 'Select post types', 'bawmrp' ), 'bawmrp_field_post_types', 'bawmrp_settings', 'bawmrp_settings_page' );
		add_settings_field( 'bawmrp_field_cache_time', __( 'Caching data', 'bawmrp' ), 'bawmrp_field_cache_time', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_section( 'bawmrp_settings_page', __( 'Display', 'bawmrp' ), '__return_false', 'bawmrp_settings4' );
		add_settings_field( 'bawmrp_field_in_content', __( 'Display related posts in post content', 'bawmrp' ), 'bawmrp_field_in_content', 'bawmrp_settings4', 'bawmrp_settings_page' );
		add_settings_field( 'bawmrp_field_in_homepage', __( 'Display related posts in home page too', 'bawmrp' ), 'bawmrp_field_in_homepage', 'bawmrp_settings4', 'bawmrp_settings_page' );
		add_settings_field( 'bawmrp_field_in_content_mode', __( 'Display mode', 'bawmrp' ), 'bawmrp_field_in_content_mode', 'bawmrp_settings4', 'bawmrp_settings_page' );
		add_settings_field( 'bawmrp_field_display_content', __( 'Display content?', 'bawmrp' ), 'bawmrp_field_display_content', 'bawmrp_settings4', 'bawmrp_settings_page' );
		add_settings_field( 'bawmrp_field_display_no_posts', __( 'Display something is no related posts are found?', 'bawmrp' ), 'bawmrp_field_display_no_posts', 'bawmrp_settings4', 'bawmrp_settings_page' );
	add_settings_section( 'bawmrp_settings_page', __( 'Auto posts', 'bawmrp' ), 'bawmrp_so_ironic', 'bawmrp_settings2' );
		add_settings_field( 'bawmrp_field_auto_posts', __( 'Use auto related posts to fill the max posts ?', 'bawmrp' ), 'bawmrp_field_auto_posts', 'bawmrp_settings2', 'bawmrp_settings_page' );
		add_settings_field( 'bawmrp_field_sticky_posts', __( 'Use sticky posts to fill the max posts ?', 'bawmrp' ), 'bawmrp_field_sticky_posts', 'bawmrp_settings2', 'bawmrp_settings_page' );
		add_settings_field( 'bawmrp_field_recent_posts', __( 'Use recent posts to fill the max posts ?', 'bawmrp' ), 'bawmrp_field_recent_posts', 'bawmrp_settings2', 'bawmrp_settings_page' );
	if( !apply_filters( 'hide_baw_about', false ) ):
		add_settings_section( 'bawmrp_settings_page', __( 'About', 'bawmrp' ), '__return_false', 'bawmrp_settings3' );
			add_settings_field( 'bawmrp_field_about', '', create_function( '', "include( dirname( __FILE__ ) . '/about.inc.php' );" ), 'bawmrp_settings3', 'bawmrp_settings_page' );
	endif;

include( dirname( __FILE__ ) . '/setting_fields.inc.php' );
?>
	<div class="wrap">
	<div id="icon-bawmrp" class="icon32" style="background: url(<?php echo BAWMRP_PLUGIN_URL; ?>img/icon32.png) 0 0 no-repeat;"><br/></div> 
	<h2><?php echo BAWMRP_FULLNAME; ?> <small>v<?php echo BAWMRP_VERSION; ?></small></h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'bawmrp_settings' ); ?>
		<?php submit_button(); ?>
		<div class="tabs"><?php do_settings_sections( 'bawmrp_settings' ); ?></div>
		<div class="tabs"><?php do_settings_sections( 'bawmrp_settings4' ); ?></div>
		<div class="tabs"><?php do_settings_sections( 'bawmrp_settings2' ); ?></div>
		<?php submit_button(); ?>
		<?php if( !apply_filters( 'hide_baw_about', false ) ) do_settings_sections( 'bawmrp_settings3' ); ?>
	</form>
<?php
}
