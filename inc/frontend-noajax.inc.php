<?php
if( !defined( 'ABSPATH' ) )
	die( 'Cheatin\' uh?' );

if( !function_exists( 'baw_first_image') ):
	function baw_first_image( $post, $default ) {
		if( is_null( $post ) )
			return $default;
		$post = !is_object( $post ) && (int)$post>0 ? get_post( $post ) : $post;
		if( $post )
			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', $post->post_content, $matches );
		$first_img = isset( $matches[1][0] ) ? $matches[1][0] : '';
		return !empty( $first_img ) ? $first_img : $default;
	}	
endif;

add_shortcode( 'manual_related_posts', 'bawmrp_the_content' );
add_shortcode( 'bawmrp', 'bawmrp_the_content' );

add_filter( 'the_content', 'bawmrp_the_content' );
function bawmrp_the_content( $content='' )
{
	global $post, $bawmrp_options;
	if( $bawmrp_options['in_content']!='on' && $content!='' )
		return $content;
	if( ( is_home() && $bawmrp_options['in_homepage']=='on' ) ||
		is_singular( $bawmrp_options['post_types'] ) ):
		$ids_manual = wp_parse_id_list( bawmrp_get_related_posts( $post->ID ) );
		$transient_name = 'bawmrp_' . $post->ID . '_' . substr( md5( $ids_manual . serialize( $bawmrp_options ) ), 0, 12 );
		if( (int)$bawmrp_options['cache_time']>0 && $contents = get_transient( $transient_name ) ):
			extract( $contents );
			if( empty( $list ) || !is_array( $list ) )
				if( $bawmrp_options['display_no_posts']=='text' && $bawmrp_options['display_no_posts_text']!='' )
					$list = array( '<ul><li>' . $bawmrp_options['display_no_posts_text'] . '</li></ul>' );
				else
					$list = array();
			if( $bawmrp_options['random_posts'] == 'on' )
				@shuffle( $list );
			$final = $content . $head . @implode( "\n", $list ) . $foot;
			$content = apply_filters( 'bawmrp_posts_content', $final, $content, $head, $list, $foot );
			return $content;
		endif;
		$ids_auto = $bawmrp_options['auto_posts']!='none' ||  $bawmrp_options['sticky_posts']=='on' ||  $bawmrp_options['recent_posts']=='on' ? bawmrp_get_related_posts_auto( $post ) : array();
		$ids = wp_parse_id_list( array_merge( $ids_manual, $ids_auto ) );
		$head_title = isset( $bawmrp_options['head_title'] ) ? $bawmrp_options['head_title'] : __( 'You may also like:', 'bawmrp' ); //// delete asap
		$head_title = isset( $bawmrp_options['head_titles'][$post->post_type] ) ? $bawmrp_options['head_titles'][$post->post_type] : $head_title;
		if( !empty( $ids ) && is_array( $ids ) && isset( $ids[0] ) && $ids[0]!=0 ):
			$ids = wp_parse_id_list( $ids );
			$list = array();
			if( $bawmrp_options['random_posts'] == 'on' )
				shuffle( $ids );
			if( (int)$bawmrp_options['max_posts']>0 && count( $ids )>(int)$bawmrp_options['max_posts'] )
				$ids = array_slice( $ids, 0, (int)$bawmrp_options['max_posts'] );
			$head = '<div class="bawmrp"><h3>' . $head_title . '</h3><ul>';
			do_action( 'bawmrp_first_li' );
			$style = apply_filters( 'bawmrp_li_style', 'float:left;width:120px;height:180px;overflow:hidden;list-style:none;border-right: 1px solid #ccc;text-align:center;padding:0px 5px;' );
			foreach( $ids as $id ):
				if( in_array( $id, $ids_manual ) )
					$class = 'bawmrp_manual';
				elseif( $bawmrp_options['sticky_posts']=='on' && in_array( $id, get_option( 'sticky_posts' ) ) )
					$class = 'bawmrp_sticky';
				elseif( in_array( $id, $ids_auto ) )
					$class = 'bawmrp_auto';
				switch( $bawmrp_options['display_content'] ):
					default: case 'none': $_content = ''; break;
					case 'excerpt': $p = get_post( $id ); $_content = '<br />' . apply_filters( 'the_excerpt', $p->post_excerpt ) .'<p>&nbsp;</p>'; break;
					case 'content': $p = get_post( $id ); remove_filter( 'the_content', 'bawmrp_the_content' ); $_content = '<br />' . apply_filters( 'the_content', $p->post_content ) .'<p>&nbsp;</p>'; break;
				endswitch;
				$_content = apply_filters( 'bawmrp_more_content', $_content );
				if( $bawmrp_options['in_content_mode']=='list' ):
					$list[] = '<li class="' . $class . '">' .
								'<a href="' . esc_url( apply_filters( 'the_permalink', get_permalink( $id ) ) ) . '">' . 
									apply_filters( 'the_title', get_the_title( $id ) ) . 
								'</a>' .
								$_content .
							'</li>';
				else:
					$no_thumb = apply_filters( 'bawmrp_no_thumb', admin_url( '/images/wp-badge.png' ), $id );
					$thumb_size = apply_filters( 'bawmrp_thumb_size', array( 100, 100 ) );
					if( current_theme_supports( 'post-thumbnails' ) )
						$thumb = has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, $thumb_size ) : '<img src="' . $no_thumb . '" height="' . $thumb_size[0] . '" width="' . $thumb_size[1] . '" />';
					else
						$thumb = '<img src="' . baw_first_image( $bawmrp_options['first_image']=='on' ? $id : null, $no_thumb ) . '" height="' . $thumb_size[0] . '" width="' . $thumb_size[1] . '" />';
					$list[] = '<li style="' . esc_attr( $style ) . '" class="' . $class . '"><a href="' . esc_url( apply_filters( 'the_permalink', get_permalink( $id ) ) ) . '">' . $thumb . '<br />' . apply_filters( 'the_title', get_the_title( $id ) ) . '</a></li>';
				endif;
			endforeach;
			do_action( 'bawmrp_last_li' );
			$list = apply_filters( 'bawmrp_list_li', $list );
			if( $bawmrp_options['in_content_mode']=='list' ):									
				$foot = '</ul></div>';
			else:
				$foot = '</ul></div><div style="clear:both;"></div>';
			endif;
			$final = $content . $head . implode( "\n", $list ) . $foot;
			$content = apply_filters( 'bawmrp_posts_content', $final, $content, $head, $list, $foot );
		elseif( $bawmrp_options['display_no_posts']=='text' && $bawmrp_options['display_no_posts_text']!='' ):
			$head = '<div class="bawmrp"><h3>' . esc_html( $head_title ) . '</h3>';
			$list = '<ul><li>' . $bawmrp_options['display_no_posts_text'] . '</li></ul>';
			$foot = '</div>';
			$final = $content . $head . $list . $foot;
			$content = apply_filters( 'bawmrp_posts_content', $final, $content, $head, $list, $foot );
		endif;
	endif;
	if( (int)$bawmrp_options['cache_time']>0 )
		set_transient( $transient_name, array( 'head'=>$head, 'list'=>$list, 'foot'=>$foot ), $bawmrp_options['cache_time']*1*60*60*24 );
	return $content;
}
