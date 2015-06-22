<?php
if( !defined( 'ABSPATH' ) )
	die( 'Cheatin\' uh?' );

function bawmrp_field_in_content()
{
	$bawmrp_options = get_option( 'bawmrp' );
	$bawmrp_options['in_content'] = isset( $bawmrp_options['in_content'] ) ? $bawmrp_options['in_content'] : false;
?>
	<label><input type="checkbox" name="bawmrp[in_content]" <?php checked( $bawmrp_options['in_content'], 'on' ); ?> /> <em><?php _e( 'Will be displayed at bottom of content.', 'bawmrp' ); ?></em></label>
	<?php if( current_user_can( 'edit_themes' ) ) { ?>
		<br />
		<em><?php _e( 'You can also use <code>&lt;?php echo do_shortcode( \'[manual_related_posts]\' ); ?&gt</code> to pull the list out.', 'bawmrp' ); ?></em>
	<?php } ?>
<?php
}

function bawmrp_so_ironic()
{ ?>
	<em><?php _e( 'So ironic ... auto related posts in manual related posts ;)', 'bawmrp' ); ?></em>
<?php
}

function bawmrp_field_post_types()
{
	$bawmrp_options = get_option( 'bawmrp' );
	global $sitepress;
	if ( isset( $sitepress ) && defined( 'ICL_LANGUAGE_CODE' ) ) {
		$lang = bawmrp_wpml_lang_by_code( ICL_LANGUAGE_CODE );
		$code = ICL_LANGUAGE_CODE;
		$flag = sprintf( ' <img src="%s" alt="%s" />', $sitepress->get_flag_url( $code ), $code );
	} else {
		$lang = $code = get_locale();
		$flag = '';
	}
	$bawmrp_options['post_types'] = !empty( $bawmrp_options['post_types'] ) ? $bawmrp_options['post_types'] : array();
	foreach ( get_post_types( array( 'public'=>true, 'show_ui'=>true ), 'objects' ) as $cpt ) {
		echo '<label><input type="checkbox" '.checked( in_array( $cpt->name, $bawmrp_options['post_types'] ) ? 'on' : '', 'on', false ).' name="bawmrp[post_types][]" value="'.esc_attr( $cpt->name ).'" /> '.esc_html( $cpt->label ).'</label><br />' .
			'&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;' . sprintf( __( 'Front-end title for %s%s', 'bawmrp' ), strtolower( esc_html( $cpt->label ) ), $flag ) . '<br />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
		if ( isset( $sitepress ) ) {
			$langs = $sitepress->get_languages( "en' AND active='1" );
		} else {
			$langs = array( array( 'default_locale'=>get_locale() ) );
		}
		foreach ( $langs as $_lang ) {
			if ( isset( $bawmrp_options['head_titles'][ $cpt->name ][ $_lang['default_locale'] ] ) ) {
				$value = esc_attr( $bawmrp_options['head_titles'][ $cpt->name ][ $_lang['default_locale'] ] );
			}elseif ( isset( $bawmrp_options['head_titles'][ $cpt->name ] ) && is_string( $bawmrp_options['head_titles'][ $cpt->name ] ) ) {
				$value = esc_attr( $bawmrp_options['head_titles'][ $cpt->name ] );
			} else {
				$value = __( 'You may also like:', 'bawmrp' );
			}
			$type = $_lang['default_locale'] != $lang ? 'hidden' : 'text';
			echo '<input type="' . $type . '" class="regular-text" name="bawmrp[head_titles][' . esc_attr( $cpt->name ) . '][' . $_lang['default_locale'] . ']" value="' . $value . '" />';
		}
		echo '<br />';
	}
}

function bawmrp_field_auto_posts()
{
	$bawmrp_options = get_option( 'bawmrp' );
?>
	<label><input type="checkbox" name="bawmrp[auto_posts]" value="both" <?php checked( isset( $bawmrp_options['auto_posts'] ) && $bawmrp_options['auto_posts'] == 'both', true ); ?> /> <em><?php _e( 'Use categories to find related posts.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_max_posts()
{
	$bawmrp_options = get_option( 'bawmrp' );
?>
	<label><input type="number" name="bawmrp[max_posts]" maxlength="3" size="2" min="0" class="small-text" value="<?php echo (int) $bawmrp_options['max_posts']; ?>" /> <em><?php _e( 'Including manual and auto related posts. (0 = No limit)', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_random_posts()
{
	$bawmrp_options = get_option( 'bawmrp' );
?>
	<label><input type="checkbox" name="bawmrp[random_order]" <?php checked( isset( $bawmrp_options['random_order'] ) && $bawmrp_options['random_order'] == 'on', true ); ?> /> <em><?php _e( 'You can randomize the posts request.', 'bawmrp' ); ?></em></label><br />
	<label><input type="checkbox" name="bawmrp[random_posts]" <?php checked( isset( $bawmrp_options['random_posts'] ) && $bawmrp_options['random_posts'] == 'on', true ); ?> /> <em><?php _e( 'You can randomize the display for posts order.', 'bawmrp' ); ?></em></label><br />
<?php
}

function bawmrp_field_display_content()
{
	$bawmrp_options = get_option( 'bawmrp' );
?>
	<label><input type="checkbox" name="bawmrp[display_content]" <?php checked( isset( $bawmrp_options['display_content'] ) && $bawmrp_options['display_content'] != 'none', true ); ?> value="excerpt" /> <em><?php _e( 'Print the post excerpt too.', 'bawmrp' ); ?></em></label>
<?php
}

function bawmrp_field_in_content_mode()
{
	$bawmrp_options = get_option( 'bawmrp' );
	$bawmrp_options['in_content_mode'] = $bawmrp_options['in_content_mode'] ? $bawmrp_options['in_content_mode'] : 'list';
?>
	<label><input type="radio" name="bawmrp[in_content_mode]" <?php checked( $bawmrp_options['in_content_mode'], 'list' ); ?> value="list" /> <em><?php _e( 'List mode.', 'bawmrp' ); ?></em></label>
	<br />
	<label><input type="radio" name="bawmrp[in_content_mode]" <?php checked( $bawmrp_options['in_content_mode'], 'thumb' ); ?> value="thumb" /> <em><?php _e( 'Thumb mode.', 'bawmrp' ); ?></em></label>
<?php
}