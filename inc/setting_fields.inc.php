<?php
if( !defined( 'ABSPATH' ) )
	die( 'Cheatin\' uh?' );

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

function bawmrp_field_display_no_posts()
{
	global $bawmrp_options;
?>
	<label><input type="radio" name="bawmrp[display_no_posts]" value="none" <?php checked( $bawmrp_options['display_no_posts'], 'none' ); ?> /> <em><?php _e( 'Do not show anything.', 'bawmrp' ); ?></em></label>
	<br />
	<label><input type="radio" name="bawmrp[display_no_posts]" value="text" <?php checked( $bawmrp_options['display_no_posts'], 'text' ); ?> /> <input type="text" size="40" name="bawmrp[display_no_posts_text]" value="<?php echo esc_attr( $bawmrp_options['display_no_posts_text'] ); ?>" /> <em><?php _e( 'HTML allowed.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_auto_posts()
{
	global $bawmrp_options;
?>
	<label><input type="radio" name="bawmrp[auto_posts]" value="none" <?php checked( $bawmrp_options['auto_posts'], 'none' ); ?> /> <em><?php _e( 'No thank you, i only need my manual posts.', 'bawmrp' ); ?></em></label>
	<br />
	<label><input type="radio" name="bawmrp[auto_posts]" value="both" <?php checked( $bawmrp_options['auto_posts'], 'both' ); ?> /> <em><?php _e( 'Use tags and categories to find related posts.', 'bawmrp' ); ?></em></label>
	<br />
	<label><input type="radio" name="bawmrp[auto_posts]" value="tags" <?php checked( $bawmrp_options['auto_posts'], 'tags' ); ?> /> <em><?php _e( 'Use only tags.', 'bawmrp' ); ?></em></label>
	<br />
	<label><input type="radio" name="bawmrp[auto_posts]" value="cat" <?php checked( $bawmrp_options['auto_posts'], 'cat' ); ?> /> <em><?php _e( 'Use ony categories.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_cache_time()
{
	global $bawmrp_options;
?>
	<label><input type="number" name="bawmrp[cache_time]" size="2" maxlength="3" min="0" max="365" value="<?php echo (int)$bawmrp_options['cache_time']; ?>" /> <em><?php _e( 'How many days do we have to cache results ? (suggested min. 1)', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_max_posts()
{
	global $bawmrp_options;
?>
	<label><input type="number" name="bawmrp[max_posts]" maxlength="3" size="2" min="0" value="<?php echo (int)$bawmrp_options['max_posts']; ?>" /> <em><?php _e( 'Including manual and auto related posts. (0 = No limit)', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_random_posts()
{
	global $bawmrp_options;
?>
	<label><input type="checkbox" name="bawmrp[random_posts]" <?php checked( $bawmrp_options['random_posts'], 'on' ); ?> /> <em><?php _e( 'You can randomize the display for posts order.', 'bawmrp' ); ?></em></label><br />
	<label><input type="checkbox" name="bawmrp[random_order]" <?php checked( $bawmrp_options['random_order'], 'on' ); ?> /> <em><?php _e( 'You can randomize the posts request.', 'bawmrp' ); ?></em></label><br />
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

function bawmrp_field_display_content()
{
	global $bawmrp_options;
	$bawmrp_options['display_content'] = $bawmrp_options['display_content'] ? $bawmrp_options['display_content'] : 'none';
?>
	<label><input type="radio" name="bawmrp[display_content]" <?php checked( $bawmrp_options['display_content'], 'none' ); ?> value="none" /> <em><?php _e( 'No content. You can add you own using the <code>bawmrp_more_content</code> filter hook.', 'bawmrp' ); ?></em></label>
	<br />
	<label><input type="radio" name="bawmrp[display_content]" <?php checked( $bawmrp_options['display_content'], 'excerpt' ); ?> value="excerpt" /> <em><?php _e( 'Print the post excerpt. <span style="font-size: smaller">(This adds 1 more query per related posts. I suggest you to active HTML cache, see below.)</span>', 'bawmrp' ); ?></em></label>
	<br />
	<label><input type="radio" name="bawmrp[display_content]" <?php checked( $bawmrp_options['display_content'], 'content' ); ?> value="content" /> <em><?php _e( 'Print the post content <span style="font-size: smaller">(This adds 1 more query per related posts. I suggest you to active HTML cache, see below.)</span>', 'bawmrp' ); ?></em></label>
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
	<?php if( !current_theme_supports( 'post-thumbnails' ) ): ?>
	<br />
	&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
	<label><input type="checkbox" name="bawmrp[first_image]" <?php checked( $bawmrp_options['first_image'], 'on' ); ?> /> <em><?php _e( 'Your theme does not support posts thumbnails, check this to grab the first post image instead.', 'bawmrp' ); ?></em></label>
	<?php endif; ?>
	<?php if( current_user_can( 'edit_themes' ) ): ?>
	<br />
	<em><?php _e( 'You can also use a Shortcode. Use <code>&lt;?php echo do_shortcode( \'[manual_related_posts]\' ); ?&gt</code> to pull the list out.', 'bawmrp' ); ?></em>
	<?php endif; ?>
<?php
}

function bawmrp_field_in_homepage()
{
	global $bawmrp_options;
?>
	<label><input type="checkbox" name="bawmrp[in_homepage]" <?php checked( $bawmrp_options['in_homepage'], 'on' ); ?> /> <em><?php _e( 'Will be displayed at bottom of content on home page.', 'bawmrp' ); ?></em></label>
<?php
}