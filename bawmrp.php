<?php
/*
Plugin Name: BAW Manual Related Posts
Plugin URI: http://www.boiteaweb.fr
Description: Set related posts manually but easily with great ergonomics! Stop displaying auto/random related posts!
Version: 1.3.2
Author: Juliobox
Author URI: http://www.boiteaweb.fr
*/

$bawmrp_options = get_option( 'bawmrp' );

function bawmrp_init()
{
	load_plugin_textdomain( 'bawmrp', '', dirname( plugin_basename( __FILE__ ) ) . '/lang' );
}
add_action( 'init', 'bawmrp_init', 1 );

function bawmrp_get_related_posts( $post_id )
{
	return get_post_meta( $post_id, '_yyarpp', true );
}

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
	$related_post_ids = bawmrp_get_related_posts( $post->ID );
	?>
	<div>
		<input class="hide-if-js" type="text" name="bawmrp_post_ids" id="bawmrp_post_ids" value="<?php echo esc_attr( $related_post_ids ); ?>" />&nbsp;&nbsp;
		<?php wp_nonce_field( 'add-relastedpostsids_' . $post->ID, '_wpnonce_yyarpp' ); ?>
		<div>
			<a href="javascript:void(0);" id="bawmrp_open_find_posts_button" class="button-secondary hide-if-no-js"><?php _e( 'Add a related post', 'bawmrp' ); ?></a>
			<a href="javascript:void(0);" id="bawmrp_delete_related_posts" class="button-secondary hide-if-no-js"><?php _e( 'Clear all', 'bawmrp' ); ?></a>
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

function bawmrp_add_media_script_in_header_but_in_footer_damn_hook()
{
	wp_enqueue_script( 'media', null, null, null, true );
}
add_action( 'admin_print_scripts-post.php', 'bawmrp_add_media_script_in_header_but_in_footer_damn_hook' );
add_action( 'admin_print_scripts-post-new.php', 'bawmrp_add_media_script_in_header_but_in_footer_damn_hook' );

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
				jQuery( '.find-box-inside .find-box-search input:radio' ).removeAttr('checked').filter(':visible:first').attr( 'checked','checked' );
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
						// alert(selectedID);
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
add_action( 'admin_footer-post.php', 'bawmrp_admin_footer_scripts' );
add_action( 'admin_footer-post-new.php', 'bawmrp_admin_footer_scripts' );


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
add_action( 'admin_print_styles-post.php', 'bawmrp_admin_print_styles' );
add_action( 'admin_print_styles-post-new.php', 'bawmrp_admin_print_styles' );

function bawmmrp_save_post( $post_id )
{
	if( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ):
		return $post_id;
	elseif( isset( $_POST['bawmrp_post_ids'], $_POST['post_ID'] ) ):
		check_admin_referer( 'add-relastedpostsids_' . $_POST['post_ID'], '_wpnonce_yyarpp' );
        $ids = explode( ',', $_POST['bawmrp_post_ids'] );
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
									'in_content' => 'on',
									'in_content_mode' => 'list',
									'in_homepage' => ''
								) );
}
register_activation_hook( __FILE__, 'bawmrp_activation' );

function bawmrp_uninstaller()
{
	delete_option( 'bawmrp' );
}
register_uninstall_hook( __FILE__, 'bawmrp_uninstaller' );

function bawmrp_settings_page()
{
	add_settings_section( 'bawmrp_settings_page', __( 'General', 'bawmrp' ), '__return_null', 'bawmrp_settings' );
	add_settings_field( 'bawmrp_field_in_content', __( 'Display related posts in post content', 'bawmrp' ), 'bawmrp_field_in_content', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_in_content_mode', __( 'Display mode', 'bawmrp' ), 'bawmrp_field_in_content_mode', 'bawmrp_settings', 'bawmrp_settings_page' );
	add_settings_field( 'bawmrp_field_in_homepage', __( 'Display related posts in home page too', 'bawmrp' ), 'bawmrp_field_in_homepage', 'bawmrp_settings', 'bawmrp_settings_page' );
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
	foreach( get_post_types( array( 'public'=>true, 'show_ui'=>true ), 'objects' ) as $cpt ):
		$bawmrp_options['head_titles'][$cpt->name] = isset( $bawmrp_options['head_titles'][$cpt->name] ) ? $bawmrp_options['head_titles'][$cpt->name] : __( 'You may also like:', 'bawmrp' );
		echo '<label><input type="checkbox" '.checked( in_array( $cpt->name, $bawmrp_options['post_types'] ) ? 'on' : '', 'on', false ).' name="bawmrp[post_types][]" value="'.esc_attr( $cpt->name ).'" /> '.esc_html( $cpt->label ).'</label><br />' .
			'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . sprintf( __( 'Front-end title for %s:', 'bawmrp' ), strtolower( esc_html( $cpt->label ) ) ) . ' <input type="text" size="40" name="bawmrp[head_titles][' . esc_attr( $cpt->name ) . ']" value="' . esc_attr( $bawmrp_options['head_titles'][$cpt->name] ) . '" /><br />';
	endforeach;
}

function bawmrp_field_in_content()
{
	global $bawmrp_options;
?>
	<label><input type="checkbox" name="bawmrp[in_content]" <?php checked( $bawmrp_options['in_content'], 'on' ); ?> /> <em><?php _e( 'Will be displayed at bottom of content.', 'bawmrp' ); ?></em></label>
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

if( isset( $bawmrp_options['in_content'] ) && $bawmrp_options['in_content']=='on' )
	add_filter( 'the_content', 'bawmrp_the_content' );

if( !isset( $bawmrp_options['in_content_mode'] ) || $bawmrp_options['in_content_mode']=='list' ): // LIST mode
	function bawmrp_the_content( $content )
	{
		global $post, $bawmrp_options;
		if( ( ( is_home() && isset( $bawmrp_options['in_homepage'] ) && $bawmrp_options['in_homepage']=='on' ) ||
			  ( is_singular() ) )
			&& in_array( $post->post_type, $bawmrp_options['post_types'] ) ):
			$ids = bawmrp_get_related_posts( $post->ID );
			if( !empty( $ids ) ):
				$list = '';
				$ids = explode( ',', $ids );
				$head_title = isset( $bawmrp_options['head_title'] ) ? $bawmrp_options['head_title'] : __( 'You may also like:', 'bawmrp' ); // retro compat
				$head_title = isset( $bawmrp_options['head_titles'][$post->post_type] ) ? $bawmrp_options['head_titles'][$post->post_type] : $head_title;
				$head = '<div class="bawmrp"><h3>'.$head_title.'</h3><ul>';
				foreach( $ids as $id )
					$list .= '<li><a href="' . apply_filters( 'the_permalink', get_permalink( $id ) ) . '">' . apply_filters( 'the_title', get_the_title( $id ) ) . '</a></li>';
				$foot = '</ul></div>';
				$final = $content . $head . $list . $foot;
				$content = apply_filters( 'related_posts_content', $final, $content, $head, $list, $foot );
			endif;
		endif;
		return $content;
	}
else: // THUMB mode
	function bawmrp_the_content( $content )
	{
		global $post, $bawmrp_options;
		if( ( ( is_home() && isset( $bawmrp_options['in_homepage'] ) && $bawmrp_options['in_homepage']=='on' ) ||
			  ( is_singular() ) )
			&& in_array( $post->post_type, $bawmrp_options['post_types'] ) ):
			$ids = bawmrp_get_related_posts( $post->ID );
			if( !empty( $ids ) ):
				$list = '';
				$ids = explode( ',', $ids );
				$head_title = isset( $bawmrp_options['head_title'] ) ? $bawmrp_options['head_title'] : __( 'You may also like:', 'bawmrp' ); // retro compat
				$head_title = isset( $bawmrp_options['head_titles'][$post->post_type] ) ? $bawmrp_options['head_titles'][$post->post_type] : $head_title;
				$head = '<div class="mrp_div"><h3>'.$head_title.'</h3><ul>';
				$style = apply_filters( 'bawmrp_li_style', 'float:left;width:120px;height:180px;overflow:hidden;list-style:none;border-right: 1px solid #ccc;text-align:center;padding:0px 5px;' );
				foreach( $ids as $id ):
					$thumb = has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, array( 100, 100 ) ) : '<img src="' . admin_url( '/images/wp-badge.png' ) . '" height="100" width="100" />';
					$list .= '<li style="' . $style . '"><a href="' . apply_filters( 'the_permalink', get_permalink( $id ) ) . '">' . $thumb . '<br />' . apply_filters( 'the_title', get_the_title( $id ) ) . '</a></li>';
				endforeach;
				$foot = '</ul></div><div style="clear:both;"></div>';
				$final = $content . $head . $list . $foot;
				$content = apply_filters( 'related_posts_content', $final, $content, $head, $list, $foot );
			endif;
		endif;
		return $content;
	}
endif;
add_shortcode( 'manual_related_posts', 'bawmrp_the_content' );
add_shortcode( 'bawmrp', 'bawmrp_the_content' );

endif;