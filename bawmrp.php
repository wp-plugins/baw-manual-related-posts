<?php
/*
Plugin Name: BAW Manual Related Posts
Plugin URI: http://www.boiteaweb.fr
Description: Set related posts manually but easily with great ergonomics! Stop displaying auto/random related posts!
Version: 1.0.2
Author: Juliobox
Author URI: http://www.boiteaweb.fr
*/

$bawmrp_options = get_option( 'bawmrp' );

function bawmrp_init()
{
	load_plugin_textdomain( 'bawmrp', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}
add_action( 'init', 'bawmrp_init', 1 );

if( is_admin() ):

function bawmrp_add_meta_box()
{
	global $bawmrp_options;
	if( !empty( $bawmrp_options['post_types'] ) )
		foreach( $bawmrp_options['post_types'] as $cpt )
			add_meta_box( 'yyarpp', 'Manual Related Posts', 'bawmrp_box', $cpt, 'side' );
}
add_action( 'add_meta_boxes','bawmrp_add_meta_box' );

function bawmrp_box( $post )
{
	$related_post_ids = get_post_meta( $post->ID, '_yyarpp', true );
	?>
	<div>
		<input class="hide-if-js" type="text" name="post_ids" id="post_ids" value="<?php echo esc_attr( $related_post_ids ); ?>" />&nbsp;&nbsp;
		<?php wp_nonce_field( 'add-relastedpostsids_' . $post->ID, '_wpnonce_yyarpp' ); ?>
		<div>
			<a href="javascript:void(0);" onclick="findPosts.open( 'from_post','<?php echo $post->ID; ?>' ); return false;" class="button-primary hide-if-no-js"><?php _e( 'Add a related post', 'bawmrp' ); ?></a>
			<span class="hide-if-js"><?php _e( 'Add posts IDs from posts you want to relate, comma separated.'); ?></span>
		</div>
		<ul id="ul_yyarpp" class="tagchecklist">
			<?php
			if( !empty( $related_post_ids ) ):
				$related_post_ids = explode( ',', $related_post_ids );
				foreach( $related_post_ids as $id ) : ?>
					<li data-id="<?php echo esc_html( $id ); ?>"><span style="float:none;"><a class="hide-if-no-js erase_yyarpp">X</a>&nbsp;&nbsp;<?php echo get_the_title( (int)$id ); ?></span></li>
			<?php endforeach;
			endif;?>
		</ul>
	</div>
	<?php
}

function bawmrp_admin_footer()
{
	global $bawmrp_options, $pagenow, $typenow, $post;
	if( !empty( $bawmrp_options['post_types'] ) ):
		$available_pages = array( 'post.php', 'post-new.php' );
		if( in_array( $pagenow, $available_pages ) && in_array( $typenow, $bawmrp_options['post_types'] ) ):
			find_posts_div();
			?>
			<script>
			jQuery( document ).ready( function() {
			
				jQuery( 'body:first' ).prepend( jQuery( '.find-box-search input#_ajax_nonce' ) );
				
				jQuery( '#find-posts-submit' ).click( function(e) {
					e.preventDefault();
					var selectedID = jQuery( 'input[name="found_post_id"]:checked' ).val();
					var posts_ids = new Array();
					posts_ids = jQuery( '#post_ids' ).val()!='' ? jQuery( '#post_ids' ).val().split( ',' ) : [];
					if( jQuery.inArray( selectedID, posts_ids )=="-1" && selectedID!=<?php echo $post->ID; ?>){
						posts_ids.push( selectedID );
						jQuery( '#post_ids' ).val( posts_ids );
						jQuery( 'input[name="found_post_id"]:checked' ).parent().parent().css( 'background', '#FF0000' ).fadeOut( 500, function(){ jQuery( this ).remove() } );
						var label = jQuery( 'input[name="found_post_id"]:checked' ).parent().next().text();
						label = label.replace(/</g, "&lt;");
						label = label.replace(/>/g, "&gt;");
						var elem_li = '<li data-id="' + selectedID + '"><span style="float:none;"><a class="erase_yyarpp">X</a>&nbsp;&nbsp;' + label + '</span></li>';
						jQuery( '#ul_yyarpp' ).append( elem_li );
					}
					return false;			
				});
				
				jQuery( '.erase_yyarpp' ).live( 'click', function() {
					var id = jQuery( this ).parent().parent().attr( 'data-id' );
					jQuery( this ).parent().parent().fadeOut( 500, function(){ jQuery( this ).remove() } );
					var posts_ids = ',' + jQuery( '#post_ids' ).val() + ',';
					posts_ids = posts_ids.replace( ','+id+',', ',' );
					jQuery( '#post_ids' ).val( posts_ids.length>1 ? posts_ids.substring( 1, posts_ids.length-1 ) : '' );
					
				});
				
			});
			</script>
			<?php
		endif;
		wp_enqueue_script( 'media' );
	endif;
}
add_action( 'admin_footer', 'bawmrp_admin_footer' );

function bawmmrp_save_post( $post_id )
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):
		return $post_id;
	elseif( isset( $_POST['post_ids'], $_POST['post_ID'] ) ):
		check_admin_referer( 'add-relastedpostsids_' . $_POST['post_ID'], '_wpnonce_yyarpp' );
        $ids = explode( ',', $_POST['post_ids'] );
        $ids = array_map( 'absint', $ids );
        $ids = array_filter( $ids );
        $ids = array_unique( $ids );
        $ids = implode( ',', $ids );
		update_post_meta( $post_id, '_yyarpp', $ids );
	endif;
}
add_action( 'save_post', 'bawmmrp_save_post' );

function bawmrp_settings_action_links( $links )
{
	array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=bawmrp_settings' ) . '">' . __( 'Settings' ) . '</a>' );
	return $links;
}
add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bawmrp_settings_action_links' );

function bawmrp_create_menu()
{
	add_options_page( 'Manual Related Posts', 'Manual Related Posts' , 'manage_options', 'bawmrp_settings', 'bawmrp_settings_page' );
	register_setting( 'bawmrp_settings', 'bawmrp' );
}
add_action( 'admin_menu', 'bawmrp_create_menu' );

function bawmrp_activation()
{
	add_option( 'bawmrp', array(	'post_types' => array( 'post' ),
									'head_title' => __( 'You may also like:', 'bawmrp' ),
									'in_content' => "on"
								) );
}
register_activation_hook( __FILE__, 'bawmrp_activation' );

function bawpvc_uninstaller()
{
	delete_option( 'bawmrp' );
}
register_uninstall_hook( __FILE__, 'bawmrp_uninstaller' );

function bawmrp_settings_page()
{
	settings_errors();
	add_settings_section( 'bawmrp_settings_page', __( 'General', 'bawmrp' ), '__return_null', 'bawmrp_settings' );
	add_settings_field( 'bawmrp_field_in_content', __( 'Display related posts in post content', 'bawmrp' ), 'bawmrp_field_in_content', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_headtitle', __( 'Head title for front-end (if previous checkbox is checked)', 'bawmrp' ), 'bawmrp_field_headtitle', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_post_types', __( 'Select post types', 'bawmrp' ), 'bawmrp_field_post_types', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_section( 'bawmrp_settings_page', __( 'About', 'bawmrp' ), '__return_null', 'bawmrp_settings2' );
	add_settings_field( 'bawmrp_field_about', '', create_function( '', "include( dirname( __FILE__ ) . '/about.php' );" ), 'bawmrp_settings2', 'bawmrp_settings_page' );

?>
	<div class="wrap">
	<?php screen_icon( 'options-general' ); ?>
	<h2>Manual Related Posts</h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'bawmrp_settings' ); ?>
		<?php do_settings_sections( 'bawmrp_settings' ); ?>
		<?php submit_button(); ?>
		<?php do_settings_sections( 'bawmrp_settings2' ); ?>
	</form>
<?php
}

function bawmrp_field_post_types()
{
	global $bawmrp_options;
	$bawmrp_options['post_types'] = !empty( $bawmrp_options['post_types'] ) ? $bawmrp_options['post_types'] : array();
	foreach( get_post_types( array( 'public'=>true ), 'objects' ) as $cpt )
		echo '<label><input type="checkbox" '.checked( in_array( $cpt->name, $bawmrp_options['post_types'] ) ? 'on' : '', 'on', false ).' name="bawmrp[post_types][]" value="'.esc_attr( $cpt->name ).'" /> '.esc_html( $cpt->label ).'</label><br />';
}

function bawmrp_field_in_content()
{
	global $bawmrp_options;
?>
	<label><input type="checkbox" name="bawmrp[in_content]" <?php checked( $bawmrp_options['in_content'], 'on' ); ?> /> <em><?php _e( 'Will be displayed at bottom of content.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_headtitle()
{
	global $bawmrp_options;
?>
	<input type="text" name="bawmrp[head_title]" size="40" value="<?php echo esc_attr( $bawmrp_options['head_title'] ); ?>" /></em>
<?php
}

else:

if( isset( $bawmrp_options['in_content'] ) && $bawmrp_options['in_content']=='on' ):
function bawmrp_the_content( $content )
{
	global $post, $bawmrp_options;
	if( in_array( $post->post_type, $bawmrp_options['post_types'] ) ):
		$ids = get_post_meta( $post->ID, '_yyarpp', true );
		if( !empty( $ids ) ):
			$list = '';
			$ids = explode( ',', $ids );
			$head = '<div><h3>'.$bawmrp_options['head_title'].'</h3><ul>';
			foreach( $ids as $id )
				$list .= '<li><a href="'.get_permalink( $id ).'">'.get_the_title( $id ).'</a></li>';
			$foot = '</ul></div>';
			$final = $content . $head . $list . $foot;
			$content = apply_filters( 'related_posts_content', $final, $content, $head, $list, $foot );
		endif;
	endif;
	return $content;
}
add_filter( 'the_content', 'bawmrp_the_content' );
endif;

endif;