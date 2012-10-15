<?php
/*
Plugin Name: BAW Manual Related Posts
Plugin URI: http://www.boiteaweb.fr
Description: Set related posts manually but easily with great ergonomics! Stop displaying auto/random related posts!
Version: 1.6.1
Author: Juliobox
Author URI: http://www.boiteaweb.fr
*/

$args = array(	'post_types' => array( 'post' ),
				'in_content' => 'on',
				'in_content_mode' => 'list',
				'in_homepage' => '',
				'max_posts' => 0,
				'random_posts' => false,
				'random_order' => false,
				'auto_posts' => 'none',
				'cache_time' => 1
			);
$bawmrp_options = wp_parse_args( get_option( 'bawmrp' ), $args );
unset( $args );

add_action( 'admin_init', 'bawmrp_init', 1 );
function bawmrp_init()
{
	load_plugin_textdomain( 'bawmrp', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}

function bawmrp_get_related_posts( $post_id )
{
	$ids = get_post_meta( $post_id, '_yyarpp', true );
	return !empty( $ids ) != '' ? implode( ',', wp_parse_id_list( $ids ) ) : array();
}

function bawmrp_get_related_posts_auto( $post )
{
	global $bawmrp_options;
	$ids_manual = bawmrp_get_related_posts( $post->ID );
	$num_posts = (int)$bawmrp_options['max_posts']==0 ? '-1' : (int)$bawmrp_options['max_posts'] - count( wp_parse_id_list( $ids_manual ) );
	if( $num_posts > 0 || (int)$bawmrp_options['max_posts']==0 ):
		$ids_sticky = get_option( 'sticky_posts' );
		$ids_recent = $bawmrp_options[ 'recent_posts' ] == 'on' ? bawmrp_get_recent_posts( $post, true ) : array();
		$ids = wp_parse_id_list( array_merge( explode( ',', $ids_manual ), $ids_sticky, $ids_recent ) );
		$args = array(
			'post_type' => $post->post_type,
			'post_status' => 'publish',
			'post__not_in' => explode( ',', $post->ID . $ids ),
			'numberposts' => $num_posts,
			'order' => $bawmrp_options['random_order'] ? 'RAND' : 'DESC'
		);
		if( $bawmrp_options['auto_posts'] == 'tags' || $bawmrp_options['auto_posts'] == 'both' ):
			$tags = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
			if( $tags )
				$args['tag__in'] = $tags;
		endif;
		if( $bawmrp_options['auto_posts'] == 'cat' || $bawmrp_options['auto_posts'] == 'both' ):
			$cat = get_the_category( $post->ID );
			$cat_ids = array();
			foreach( $cat as $c )
				if( isset( $c->term_id ) )
					$cat_ids[] = $c->term_id;
			$args['category'] = implode( ',', $cat_ids );
		endif;
		$transient_name = 'mrp_auto_' . substr( md5( serialize( $args + $bawmrp_options ) ), 0, 12 );
		unset( $ids );
		if( $ids = get_transient( $transient_name ) )
			return wp_parse_id_list( $ids );
		$relative_query = get_posts( $args );
		$rel_id = array();
		if( !empty( $relative_query ) ):
			foreach( $relative_query as $rel_post )
				$rel_id[] = $rel_post->ID;
			set_transient( 'mrp_auto_' . substr( md5( serialize( $args + $bawmrp_options ) ), 0, 12 ), $rel_id, $bawmrp_options['cache_time']*1*60*60*24 );
		endif;
		return wp_parse_id_list( $rel_id );
	endif;
	return array();
}  
  
function bawmrp_get_recent_posts( $post, $ignore_auto=false )
{
	global $bawmrp_options;
	$ids_manual = bawmrp_get_related_posts( $post->ID );
	$num_posts = (int)$bawmrp_options['max_posts']==0 ? '-1' : (int)$bawmrp_options['max_posts'] - count( wp_parse_id_list( $ids_manual ) );
	if( $num_posts > 0 || (int)$bawmrp_options['max_posts']==0 ):
		$ids_sticky = get_option( 'sticky_posts' );
		$ids_auto = !$ignore_auto && $bawmrp_options[ 'auto_posts' ] != 'none' ? bawmrp_get_related_posts_auto( $post ) : array();
		$ids = wp_parse_id_list( array_merge( explode( ',', $ids_manual ), $ids_sticky, $ids_auto ) );
		$args = array(
			'post_type' => $post->post_type,
			'post_status' => 'publish',
			'post__not_in' => explode( ',', $post->ID . $ids ),
			'numberposts' => $num_posts
		);
		$transient_name = 'mrp_recent_' . substr( md5( serialize( $args + $bawmrp_options ) ), 0, 12 );
		unset( $ids );
		if( $ids = get_transient( $transient_name ) )
			return wp_parse_id_list( $ids );
		$relative_query = get_posts( $args );
		$rel_id = array();
		if( !empty( $relative_query ) ):
			foreach( $relative_query as $rel_post )
				$rel_id[] = $rel_post->ID;
			set_transient( 'mrp_recent_' . substr( md5( serialize( $args + $bawmrp_options ) ), 0, 12 ), $rel_id, $bawmrp_options['cache_time']*1*60*60*24 );
		endif;
		return wp_parse_id_list( $rel_id );
	endif;
	return array();
}  
  
if( is_admin() ):

add_action( 'add_meta_boxes','bawmrp_add_meta_box' );
function bawmrp_add_meta_box()
{
	global $bawmrp_options;
	if( !empty( $bawmrp_options['post_types'] ) )
		foreach( $bawmrp_options['post_types'] as $cpt )
			add_meta_box( 'bawmrp', 'Manual Related Posts', 'bawmrp_box', $cpt, 'side' );
}

function bawmrp_box( $post )
{
	$related_post_ids = bawmrp_get_related_posts( $post->ID );
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
					<li data-id="<?php echo esc_html( $id ); ?>"><span style="float:none;"><a class="hide-if-no-js erase_yyarpp">X</a>&nbsp;&nbsp;<?php echo get_the_title( (int)$id ); ?></span></li>
			<?php endforeach;
			endif;?>
		</ul>
	</div>
	<?php
}

add_action( 'admin_print_scripts-post.php', 'bawmrp_add_media_script_in_header_but_in_footer_damn_hook' );
add_action( 'admin_print_scripts-post-new.php', 'bawmrp_add_media_script_in_header_but_in_footer_damn_hook' );
function bawmrp_add_media_script_in_header_but_in_footer_damn_hook()
{
	wp_enqueue_script( 'media', null, null, null, true );
}

add_action( 'admin_footer-post.php', 'bawmrp_admin_footer_scripts' );
add_action( 'admin_footer-post-new.php', 'bawmrp_admin_footer_scripts' );
function bawmrp_admin_footer_scripts()
{
	global $bawmrp_options, $typenow, $post;
	if( !empty( $bawmrp_options['post_types'] ) ):
		$available_pages = array( 'post.php', 'post-new.php' );
		if( in_array( $typenow, $bawmrp_options['post_types'] ) ):
			find_posts_div();
			?>
			<script>
			function bawmrp_open_find_posts_dialog( event )
			{
				event.preventDefault();
				findPosts.open( 'from_post','<?php echo $post->ID; ?>' ); 
				jQuery( '.find-box-inside .find-box-search input:radio' ).removeAttr( 'checked' ).filter( ':visible:first' ).attr( 'checked','checked' );
			}
                        
			jQuery( document ).ready( function() {
			
                jQuery( '#bawmrp_open_find_posts_button' ).on( 'click', bawmrp_open_find_posts_dialog );
				
				jQuery( '#bawmrp_delete_related_posts' ).click( function(){
					jQuery( '#ul_yyarpp' ).animate( {opacity: 0 }, 500, function() { 
																			jQuery( this ).html( '' ) ;
																			jQuery( '#bawmrp_post_ids' ) .val( '' );
																			jQuery( this ).css( 'opacity', '1' ) ;
																		}
													);
 				} );
                        
				jQuery( 'body:first' ).prepend( jQuery( '.find-box-search input#_ajax_nonce' ) );
				
				jQuery( "#ul_yyarpp" ).sortable({
					'update' : function(event, ui) {
						var ids = [];
						jQuery('#ul_yyarpp li').each(function(i, item){
							ids.push(jQuery(item).attr('data-id'));
						});
						jQuery('#bawmrp_post_ids').val(ids.join(','));
					},
					'revert': true,
					'placeholder': 'sortable-placeholder',
					'tolerance': 'pointer',
					'axis': 'y',
					'containment': 'parent',
					'cursor': 'move',
					'forcePlaceholderSize': true,
					'dropOnEmpty': false,
				});
				
				jQuery( '#find-posts-submit' ).click( function(e) {
					e.preventDefault();
                    if( jQuery( 'input[name="found_post_id[]"]:checked' ).length == 0)
						return false;
					jQuery( 'input[name="found_post_id[]"]:checked' ).each( function(id){
						var selectedID = jQuery(this).val();
						var posts_ids = new Array();
						posts_ids = jQuery( '#bawmrp_post_ids' ).val()!='' ? jQuery( '#bawmrp_post_ids' ).val().split( ',' ) : [];
						if( jQuery.inArray( selectedID, posts_ids )=="-1" && selectedID!=<?php echo $post->ID; ?>){
							posts_ids.push( selectedID );
							jQuery( '#bawmrp_post_ids' ).val( posts_ids );
							jQuery( this ).parent().parent().css( 'background', '#FF0000' ).fadeOut( 500, function(){ jQuery( this ).remove() } );
							var label = jQuery( this ).parent().next().text();
							label = label.replace(/</g, "&lt;");
							label = label.replace(/>/g, "&gt;");
							var elem_li = '<li data-id="' + selectedID + '"><span style="float:none;"><a class="erase_yyarpp">X</a>&nbsp;&nbsp;' + label + '</span></li>';
							jQuery( '#ul_yyarpp' ).append( elem_li );
						}
					});
					return false;			
				});

				setInterval( function()
							{
								if( jQuery( '#find-posts-response input:radio' ).length>0 ){
									var $forbidden_ids = jQuery( '#bawmrp_post_ids' ).val().split( ',' );
									jQuery( '#find-posts-response input[value="<?php echo $post->ID; ?>"]' )
										.attr('disabled', 'disabled');
									jQuery( '#find-posts-response input' ).filter( function(i)
										{ 
											return jQuery.inArray(jQuery(this).val(),$forbidden_ids)>-1;
										} )
										.attr('disabled', 'disabled').attr('checked', 'checked');
									jQuery( '#find-posts-response' ).html( jQuery( '#find-posts-response' ).html()
																			.replace(/type=\"radio\"/g,'type=\"checkbox\"')
																			.replace(/name=\"found_post_id\"/g,'name=\"found_post_id[]\"')
																		);
								}
							}, 100 
				);
				
				jQuery( '.erase_yyarpp' ).live( 'click', function() {
					var id = jQuery( this ).parent().parent().attr( 'data-id' );
					jQuery( this ).parent().parent().fadeOut( 500, function(){ jQuery( this ).remove() } );
					var posts_ids = ',' + jQuery( '#bawmrp_post_ids' ).val() + ',';
					posts_ids = posts_ids.replace( ','+id+',', ',' );
					jQuery( '#bawmrp_post_ids' ).val( posts_ids.length>1 ? posts_ids.substring( 1, posts_ids.length-1 ) : '' );
				});
				
			});
			</script>
			<?php
		endif;
	endif;
}

add_action( 'admin_print_styles-post.php', 'bawmrp_admin_print_styles' );
add_action( 'admin_print_styles-post-new.php', 'bawmrp_admin_print_styles' );
function bawmrp_admin_print_styles()
{ 
	global $bawmrp_options, $typenow;
	if( !empty( $bawmrp_options['post_types'] ) )
		if( in_array( $typenow, $bawmrp_options['post_types'] ) ):
	?>
	<style type="text/css">
		#ul_yyarpp > li > span { cursor: n-resize; }
		.bawmrp_placeholder{ border: 3px dotted cyan; display:block; height: 15px; }
		.find-box-inside .find-box-search input[type=radio],
		.find-box-inside .find-box-search input[type=radio] + label {
			display: none;
		}
		<?php foreach( $bawmrp_options['post_types'] as $post_type ): ?>
		.find-box-inside .find-box-search input[type=radio][value=<?php echo $post_type; ?>],
		.find-box-inside .find-box-search input[type=radio][value=<?php echo $post_type; ?>] + label {
			display: inline-block;
		}
		<?php endforeach; ?>
	</style>
<?php
	endif;
}

add_action( 'save_post', 'bawmmrp_save_post' );
function bawmmrp_save_post( $post_id )
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):
		return $post_id;
	elseif( isset( $_POST['bawmrp_post_ids'], $_POST['post_ID'] ) ):
		check_admin_referer( 'add-relastedpostsids_' . $_POST['post_ID'], '_wpnonce_yyarpp' );
        $ids = implode( ',', wp_parse_id_list( $_POST['bawmrp_post_ids'] ) );
		update_post_meta( $post_id, '_yyarpp', $ids );
	endif;
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'bawmrp_settings_action_links' );
function bawmrp_settings_action_links( $links )
{
	array_unshift( $links, '<a href="' . admin_url( 'admin.php?page=bawmrp_settings' ) . '">' . __( 'Settings' ) . '</a>' );
	return $links;
}

add_action( 'admin_menu', 'bawmrp_create_menu' );
function bawmrp_create_menu()
{
	add_options_page( 'Manual Related Posts', 'Manual Related Posts' , 'manage_options', 'bawmrp_settings', 'bawmrp_settings_page' );
	register_setting( 'bawmrp_settings', 'bawmrp' );
}

register_activation_hook( __FILE__, 'bawmrp_activation' );
function bawmrp_activation()
{
	global $bawmrp_options;
	add_option( 'bawmrp', $bawmrp_options );
}

register_uninstall_hook( __FILE__, 'bawmrp_uninstaller' );
function bawmrp_uninstaller()
{
	global $wpdb;
	delete_option( 'bawmrp' );
	$wpdb->query( 'DELETE FROM ' . $wpdb->postmeta . ' WHERE meta_key="_yyarpp"' );
}

function bawmrp_settings_page()
{
	add_settings_section( 'bawmrp_settings_page', __( 'General', 'bawmrp' ), '__return_null', 'bawmrp_settings' );
	add_settings_field( 'bawmrp_field_max_posts', __( 'How many posts maximum', 'bawmrp' ), 'bawmrp_field_max_posts', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_random_posts', __( 'Randomize related posts', 'bawmrp' ), 'bawmrp_field_random_posts', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_in_content', __( 'Display related posts in post content', 'bawmrp' ), 'bawmrp_field_in_content', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_in_content_mode', __( 'Display mode', 'bawmrp' ), 'bawmrp_field_in_content_mode', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_in_homepage', __( 'Display related posts in home page too', 'bawmrp' ), 'bawmrp_field_in_homepage', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_post_types', __( 'Select post types', 'bawmrp' ), 'bawmrp_field_post_types', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_cache_time', __( 'Caching data', 'bawmrp' ), 'bawmrp_field_cache_time', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_section( 'bawmrp_settings_page', __( 'Auto posts', 'bawmrp' ), 'bawmrp_so_ironic', 'bawmrp_settings2' );
	add_settings_field( 'bawmrp_field_auto_posts', __( 'Use auto related posts to fill the max posts ?', 'bawmrp' ), 'bawmrp_field_auto_posts', 'bawmrp_settings2', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_sticky_posts', __( 'Use sticky posts to fill the max posts ?', 'bawmrp' ), 'bawmrp_field_sticky_posts', 'bawmrp_settings2', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_recent_posts', __( 'Use recent posts to fill the max posts ?', 'bawmrp' ), 'bawmrp_field_recent_posts', 'bawmrp_settings2', 'bawmrp_settings_page' );
	add_settings_section( 'bawmrp_settings_page', __( 'About', 'bawmrp' ), '__return_null', 'bawmrp_settings3' );
	add_settings_field( 'bawmrp_field_about', '', create_function( '', "include( dirname( __FILE__ ) . '/about.php' );" ), 'bawmrp_settings3', 'bawmrp_settings_page' );

?>
	<div class="wrap">
	<?php screen_icon( 'options-general' ); ?>
	<h2>Manual Related Posts</h2>

	<form action="options.php" method="post">
		<?php settings_fields( 'bawmrp_settings' ); ?>
		<?php do_settings_sections( 'bawmrp_settings' ); ?>
		<?php do_settings_sections( 'bawmrp_settings2' ); ?>
		<?php submit_button(); ?>
		<?php do_settings_sections( 'bawmrp_settings3' ); ?>
	</form>
<?php
}

function bawmrp_field_in_content()
{
	global $bawmrp_options;
?>
	<label><input type="checkbox" name="bawmrp[in_content]" <?php checked( $bawmrp_options['in_content'], 'on' ); ?> /> <em><?php _e( 'Will be displayed at bottom of content.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_so_ironic()
{ ?>
	<em><?php _e( 'So ironic ... auto related posts in manual related posts ;)', 'bawmrp' ); ?></em>
<?php
}
function bawmrp_field_post_types()
{
	global $bawmrp_options;
	$bawmrp_options['post_types'] = !empty( $bawmrp_options['post_types'] ) ? $bawmrp_options['post_types'] : array();
	foreach( get_post_types( array( 'public'=>true, 'show_ui'=>true ), 'objects' ) as $cpt ):
		$bawmrp_options['head_titles'][$cpt->name] = isset( $bawmrp_options['head_titles'][$cpt->name] ) ? $bawmrp_options['head_titles'][$cpt->name] : __( 'You may also like:', 'bawmrp' );
		echo '<label><input type="checkbox" '.checked( in_array( $cpt->name, $bawmrp_options['post_types'] ) ? 'on' : '', 'on', false ).' name="bawmrp[post_types][]" value="'.esc_attr( $cpt->name ).'" /> '.esc_html( $cpt->label ).'</label><br />' .
			'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . sprintf( __( 'Front-end title for %s:', 'bawmrp' ), strtolower( esc_html( $cpt->label ) ) ) . ' <input type="text" size="40" name="bawmrp[head_titles][' . esc_attr( $cpt->name ) . ']" value="' . esc_attr( $bawmrp_options['head_titles'][$cpt->name] ) . '" /><br />';
	endforeach;
}

function bawmrp_field_auto_posts()
{
	global $bawmrp_options;
?>
	<label><input type="radio" name="bawmrp[auto_posts]" value="none" <?php checked( $bawmrp_options['auto_posts'], 'none' ); ?> /> <em><?php _e( 'No thank you, i only need my manual posts.', 'bawmrp' ); ?></em></label><br />
	<label><input type="radio" name="bawmrp[auto_posts]" value="both" <?php checked( $bawmrp_options['auto_posts'], 'both' ); ?> /> <em><?php _e( 'Use tags and categories to find related posts.', 'bawmrp' ); ?></em></label><br />
	<label><input type="radio" name="bawmrp[auto_posts]" value="tags" <?php checked( $bawmrp_options['auto_posts'], 'tags' ); ?> /> <em><?php _e( 'Use only tags.', 'bawmrp' ); ?></em></label><br />
	<label><input type="radio" name="bawmrp[auto_posts]" value="cat" <?php checked( $bawmrp_options['auto_posts'], 'cat' ); ?> /> <em><?php _e( 'Use ony categories.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_cache_time()
{
	global $bawmrp_options;
?>
	<label><input type="number" name="bawmrp[cache_time]" min="1" max="365" value="<?php echo (int)$bawmrp_options['cache_time']; ?>" /> <em><?php _e( 'How many days do we have to cache results ? (min. 1)', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_max_posts()
{
	global $bawmrp_options;
?>
	<label><input type="number" name="bawmrp[max_posts]" min="0" value="<?php echo (int)$bawmrp_options['max_posts']; ?>" /> <em><?php _e( 'Including manual and auto related posts. (0 = No limit)', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_random_posts()
{
	global $bawmrp_options;
?>
	<label><input type="checkbox" name="bawmrp[random_posts]" <?php checked( $bawmrp_options['random_posts'], 'on' ); ?> /> <em><?php _e( 'You can randomize the order of posts display.', 'bawmrp' ); ?></em></label><br />
	<label><input type="checkbox" name="bawmrp[random_order]" <?php checked( $bawmrp_options['random_order'], 'on' ); ?> /> <em><?php _e( 'You can randomize the order of posts date (posts requests.', 'bawmrp' ); ?></em></label><br />
<?php
}

function bawmrp_field_sticky_posts()
{
	global $bawmrp_options;
?>
	<label><input type="checkbox" name="bawmrp[sticky_posts]" <?php checked( $bawmrp_options['sticky_posts'], 'on' ); ?> /> <em><?php _e( 'Sticky posts will be included if needed.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_recent_posts()
{
	global $bawmrp_options;
?>
	<label><input type="checkbox" name="bawmrp[recent_posts]" <?php checked( $bawmrp_options['recent_posts'], 'on' ); ?> /> <em><?php _e( 'Recents posts will be included if needed.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_in_content_mode()
{
	global $bawmrp_options;
	$bawmrp_options['in_content_mode'] = $bawmrp_options['in_content_mode'] ? $bawmrp_options['in_content_mode'] : 'list';
?>
	<label><input type="radio" name="bawmrp[in_content_mode]" <?php checked( $bawmrp_options['in_content_mode'], 'list' ); ?> value="list" /> <em><?php _e( 'List mode.', 'bawmrp' ); ?></em></label>
	<br />
	<label><input type="radio" name="bawmrp[in_content_mode]" <?php checked( $bawmrp_options['in_content_mode'], 'thumb' ); ?> value="thumb" /> <em><?php _e( 'Thumb mode.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_in_homepage()
{
	global $bawmrp_options;
?>
	<label><input type="checkbox" name="bawmrp[in_homepage]" <?php checked( $bawmrp_options['in_homepage'], 'on' ); ?> /> <em><?php _e( 'Will be displayed at bottom of content on home page.', 'bawmrp' ); ?></em></label>
<?php
}

else:

if( $bawmrp_options['in_content']=='on' )
	add_filter( 'the_content', 'bawmrp_the_content' );

add_shortcode( 'manual_related_posts', 'bawmrp_the_content' );
add_shortcode( 'bawmrp', 'bawmrp_the_content' );
if( $bawmrp_options['in_content_mode']=='list' ): // LIST mode
	function bawmrp_the_content( $content )
	{
		global $post, $bawmrp_options;
		
		if( ( ( is_home() && $bawmrp_options['in_homepage']=='on' ) ||
			  ( is_singular() ) )
			&& in_array( $post->post_type, $bawmrp_options['post_types'] ) ):
			$ids_manual = wp_parse_id_list( bawmrp_get_related_posts( $post->ID ) );
			$ids_auto = $bawmrp_options['auto_posts'] != 'none' ? bawmrp_get_related_posts_auto( $post ) : array();
			$ids = wp_parse_id_list( array_merge( $ids_manual, $ids_auto ) );
			$ids_sticky = $bawmrp_options['sticky_posts']=='on' && ( (int)$bawmrp_options['max_posts']>count($ids) || (int)$bawmrp_options['max_posts']==0 ) ? get_option( 'sticky_posts' ) : array();
			$ids = wp_parse_id_list( array_merge( $ids, $ids_sticky ) );
			$ids_recent = $bawmrp_options['recent_posts']=='on' && ( (int)$bawmrp_options['max_posts']>count($ids) || (int)$bawmrp_options['max_posts']==0 ) ? bawmrp_get_recent_posts( $post ) : array();
			$ids = wp_parse_id_list( array_merge( $ids, $ids_recent ) );
			if( !empty( $ids ) && is_array( $ids ) && isset( $ids[0] ) && $ids[0]!=0 ):
				$ids = wp_parse_id_list( $ids );
				$list = array();
				if( $bawmrp_options['random_posts'] == 'on' )
					shuffle( $ids );
				if( (int)$bawmrp_options['max_posts'] > 0 )
					$ids = array_slice( $ids, 0, (int)$bawmrp_options['max_posts'] );
				$head_title = isset( $bawmrp_options['head_title'] ) ? $bawmrp_options['head_title'] : __( 'You may also like:', 'bawmrp' ); // retro compat
				$head_title = isset( $bawmrp_options['head_titles'][$post->post_type] ) ? $bawmrp_options['head_titles'][$post->post_type] : $head_title;
				$head = '<div class="bawmrp"><h3>' . esc_html( $head_title ) . '</h3><ul>';
				do_action( 'bawmrp_first_li' );
				foreach( $ids as $id ):
					if( in_array( $id, $ids_manual ) )
						$class = 'bawmap_manual';
					elseif( in_array( $id, $ids_auto ) )
						$class = 'bawmrp_auto';
					elseif( in_array( $id, $ids_sticky ) )
						$class = 'bawmrp_sticky';
					elseif( in_array( $id, $ids_recent ) )
						$class = 'bawmrp_recent';
					$list[] = '<li class="' . $class . '"><a href="' . esc_url( apply_filters( 'the_permalink', get_permalink( $id ) ) ) . '">' . apply_filters( 'the_title', get_the_title( $id ) ) . '</a></li>';
				endforeach;
				do_action( 'bawmrp_last_li' );
				$foot = '</ul></div>';
				$list = apply_filters( 'bawmrp_list_li', $list );
				$final = $content . $head . implode( "\n", $list ) . $foot;
				$content = apply_filters( 'related_posts_content', $final, $content, $head, $list, $foot );
			endif;
		endif;
		return $content;
	}
else: // THUMB mode
	function bawmrp_the_content( $content )
	{
		global $post, $bawmrp_options;
		if( ( is_home() && $bawmrp_options['in_homepage']=='on' ) ||
			is_singular( $bawmrp_options['post_types'] ) ):
			$ids_manual = wp_parse_id_list( bawmrp_get_related_posts( $post->ID ) );
			$ids_auto = $bawmrp_options['auto_posts'] != 'none' ? bawmrp_get_related_posts_auto( $post ) : array();
			$ids = wp_parse_id_list( array_merge( $ids_manual, $ids_auto ) );
			$ids_sticky = $bawmrp_options['sticky_posts']=='on' && ( (int)$bawmrp_options['max_posts']>count($ids) || (int)$bawmrp_options['max_posts']==0 ) ? get_option( 'sticky_posts' ) : array();
			$ids = wp_parse_id_list( array_merge( $ids, $ids_sticky ) );
			$ids_recent = $bawmrp_options['recent_posts']=='on' && ( (int)$bawmrp_options['max_posts']>count($ids) || (int)$bawmrp_options['max_posts']==0 ) ? bawmrp_get_recent_posts( $post ) : array();
			$ids = wp_parse_id_list( array_merge( $ids, $ids_recent ) );
			if( !empty( $ids ) && is_array( $ids ) && isset( $ids[0] ) && $ids[0]!=0 ):
				$ids = wp_parse_id_list( $ids );
				$list = array();
				if( $bawmrp_options['random_posts'] == 'on' )
					shuffle( $ids );
				if( (int)$bawmrp_options['max_posts'] > 0 )
					$ids = array_slice( $ids, 0, (int)$bawmrp_options['max_posts'] );
				$head_title = isset( $bawmrp_options['head_title'] ) ? $bawmrp_options['head_title'] : __( 'You may also like:', 'bawmrp' ); // retro compat
				$head_title = isset( $bawmrp_options['head_titles'][$post->post_type] ) ? $bawmrp_options['head_titles'][$post->post_type] : $head_title;
				$head = '<div class="mrp_div"><h3>' . esc_html( $head_title ) . '</h3><ul>';
				$style = apply_filters( 'bawmrp_li_style', 'float:left;width:120px;height:180px;overflow:hidden;list-style:none;border-right: 1px solid #ccc;text-align:center;padding:0px 5px;' );
				do_action( 'bawmrp_first_li' );
				foreach( $ids as $id ):
					if( in_array( $id, $ids_manual ) )
						$class = 'bawmap_manual';
					elseif( in_array( $id, $ids_auto ) )
						$class = 'bawmrp_auto';
					elseif( in_array( $id, $ids_sticky ) )
						$class = 'bawmrp_sticky';
					elseif( in_array( $id, $ids_recent ) )
						$class = 'bawmrp_recent';
					$thumb = has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, array( 100, 100 ) ) : '<img src="' . admin_url( '/images/wp-badge.png' ) . '" height="100" width="100" />';
					$list[] = '<li style="' . esc_attr( $style ) . '" class="' . $class . '"><a href="' . esc_url( apply_filters( 'the_permalink', get_permalink( $id ) ) ) . '">' . $thumb . '<br />' . apply_filters( 'the_title', get_the_title( $id ) ) . '</a></li>';
				endforeach;
				do_action( 'bawmrp_last_li' );
				$foot = '</ul></div><div style="clear:both;"></div>';
				$list = apply_filters( 'bawmrp_list_li', $list );
				$final = $content . $head . implode( "\n", $list ) . $foot;
				$content = apply_filters( 'related_posts_content', $final, $content, $head, $list, $foot );
			endif;
		endif;
		return $content;
	}
endif;

endif;