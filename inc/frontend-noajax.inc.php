<?php
defined( 'ABSPATH' ) ||	die( 'Cheatin\' uh?' );

if ( ! function_exists( 'baw_first_image') ):
	function baw_first_image( $post, $default ) {
		if( is_null( $post ) ) {
			return $default;
		}
		$post = ! is_a( $post, 'WP_Post' ) && (int) $post > 0 ? get_post( $post ) : $post;
		if ( is_a( $post, 'WP_Post' ) ) {
			$output = preg_match_all( '/<img.+src=[\'"]([^\'"]+)[\'"].*>/i', do_shortcode( $post->post_content ), $matches );
			$first_img = isset( $matches[1][0] ) ? $matches[1][0] : '';
		}
		return ! empty( $first_img ) ? $first_img : $default;
	}	
endif;

add_shortcode( 'manual_related_posts', 'bawmrp_the_content' );
add_shortcode( 'bawmrp', 'bawmrp_the_content' );

add_filter( 'the_content', 'bawmrp_the_content', 9 );
function bawmrp_the_content( $content='' ) {
	global $post;
	$bawmrp_options = get_option( 'bawmrp' );
	if ( ! $post || $bawmrp_options['in_content']!='on' && $content!='' || apply_filters( 'stop_bawmrp', false ) ) {
		return $content;
	}
	if ( ( is_home() && $bawmrp_options['in_homepage']=='on' && in_the_loop() ) ||
		is_singular( $bawmrp_options['post_types'] ) ) {
		$ids_manual = wp_parse_id_list( bawmrp_get_related_posts( $post->ID ) );
		$lang = isset( $_GET['lang'] ) ? $_GET['lang'] : get_locale();
		$transient_name = time().'bawmrp_' . $post->ID . '_' . substr( md5( serialize( $ids_manual ) . serialize( $bawmrp_options ) . get_permalink( $post->ID ) . $lang ), 0, 12 );
		if ( $contents = get_transient( $transient_name ) ) {
			extract( $contents );
			if ( ! empty( $list ) && is_array( $list ) && isset( $bawmrp_options['random_posts'] ) ) {
				shuffle( $list );
			}
			$final = $content . $head . @implode( "\n", $list ) . $foot;
			$content = apply_filters( 'bawmrp_posts_content', $final, $content, $head, $list, $foot );
			return $content;
		}
		$ids_auto = isset( $bawmrp_options['auto_posts'] ) && 'none' != $bawmrp_options['auto_posts'] ? bawmrp_get_related_posts_auto( $post ) : array();
		$ids = wp_parse_id_list( array_merge( $ids_manual, $ids_auto ) );
		if ( defined( 'ICL_LANGUAGE_CODE' ) ) {
			$head_title = isset( $bawmrp_options['head_titles'][ $post->post_type ][ bawmrp_wpml_lang_by_code( ICL_LANGUAGE_CODE ) ] ) && is_string( $bawmrp_options['head_titles'][ $post->post_type ][ bawmrp_wpml_lang_by_code( ICL_LANGUAGE_CODE ) ] )? $bawmrp_options['head_titles'][$post->post_type][ bawmrp_wpml_lang_by_code( ICL_LANGUAGE_CODE ) ] : $head_title;
		} elseif ( isset( $bawmrp_options['head_titles'][ $post->post_type ][ get_locale() ] ) && is_string( $bawmrp_options['head_titles'][ $post->post_type ][ get_locale() ] ) ){
			$head_title = $bawmrp_options['head_titles'][ $post->post_type ][ get_locale() ];
		} else {
			$head_title = isset( $bawmrp_options['head_titles'][ $post->post_type ] ) && is_string( $bawmrp_options['head_titles'][ $post->post_type ] ) ? $bawmrp_options['head_titles'][ $post->post_type ] : $head_title;
		}
		if ( ! empty( $ids ) && is_array( $ids ) && isset( $ids[0] ) && $ids[0] != 0 ) {
			$ids = wp_parse_id_list( $ids );
			$list = array();
			if ( isset( $bawmrp_options['random_posts'] ) ) {
				shuffle( $ids );
			}
			if ( (int) $bawmrp_options['max_posts'] > 0 && count( $ids ) > (int) $bawmrp_options['max_posts'] ) {
				$ids = array_slice( $ids, 0, (int)$bawmrp_options['max_posts'] );
			}
			$head = '<div class="bawmrp"><h3>' . $head_title . '</h3><ul>';
			do_action( 'bawmrp_first_li' );
			$style = apply_filters( 'bawmrp_li_style', 'float:left;width:120px;height:auto;overflow:hidden;list-style:none;border-right: 1px solid #ccc;text-align:center;padding:0px 5px;' );
			$n = 0;
			foreach( $ids as $id ) {
				global $in_bawmrp_loop;
				$in_bawmrp_loop = true;
				if( in_array( $id, $ids_manual ) ) {
					$class = 'bawmrp_manual';
				} elseif( in_array( $id, get_option( 'sticky_posts' ) ) ) {
					$class = 'bawmrp_sticky';
				} elseif( in_array( $id, $ids_auto ) ) {
					$class = 'bawmrp_auto';
				}

				$_content = ''; 
				if( isset( $bawmrp_options['display_content'] ) ) {
					$p = get_post( $id );
					$_content = '<br />' . apply_filters( 'the_excerpt', $p->post_excerpt ) .'<p>&nbsp;</p>';
				}
				$_content = apply_filters( 'bawmrp_the_content', $_content, $id );
				$_content = apply_filters( 'bawmrp_more_content', $_content );
				if( $bawmrp_options['in_content_mode']=='list' ) {
					$list[] = '<li class="' . $class . '">' .
								'<a href="' . esc_url( apply_filters( 'the_permalink', get_permalink( $id ) ) ) . '">' . 
									apply_filters( 'the_title', get_the_title( $id ) ) . 
								'</a>' .
								$_content .
							'</li>';
				} else {
					$no_thumb = apply_filters( 'bawmrp_no_thumb', admin_url( '/images/w-logo-blue.png' ), $id );
					$thumb_size = apply_filters( 'bawmrp_thumb_size', array( 100, 100 ) );
					if( current_theme_supports( 'post-thumbnails' ) ) {
						$thumb = has_post_thumbnail( $id ) ? get_the_post_thumbnail( $id, $thumb_size ) : '<img src="' . baw_first_image( isset( $bawmrp_options['first_image'] ) && $bawmrp_options['first_image']=='on' ? $id : null, $no_thumb ) . '" height="' . $thumb_size[0] . '" width="' . $thumb_size[1] . '" />';
					} else {
						$thumb = '<img src="' . baw_first_image( isset( $bawmrp_options['first_image'] ) && $bawmrp_options['first_image']=='on' ? $id : null, $no_thumb ) . '" height="' . $thumb_size[0] . '" width="' . $thumb_size[1] . '" />';
					}
					$list[] = '<li style="' . esc_attr( $style ) . '" class="' . $class . '"><a href="' . esc_url( apply_filters( 'the_permalink', get_permalink( $id ) ) ) . '">' . $thumb . '<br />' . apply_filters( 'the_title', get_the_title( $id ) ) . '</a></li>';
				}
				$list = apply_filters( 'bawmrp_li', $list, ++$n );
			}
			do_action( 'bawmrp_last_li' );
			$list = apply_filters( 'bawmrp_list_li', $list );
			if( $bawmrp_options['in_content_mode']=='list' ) {									
				$foot = '</ul></div>';
			} else {
				$foot = '</ul></div><div style="clear:both;"></div>';
			}
			$final = $content . $head . implode( "\n", $list ) . $foot;
			$content = apply_filters( 'bawmrp_posts_content', $final, $content, $head, $list, $foot );
		} else {
			$head = '';//<div class="bawmrp"><h3>' . esc_html( $head_title ) . '</h3>';
			$list = '';//<ul><li>' . __( 'No posts found.' ) . '</li></ul>';
			$foot = '';//</div>';
			$final = $content . $head . $list . $foot;
			$content = apply_filters( 'bawmrp_posts_content', $final, $content, $head, $list, $foot );
		}
	}
	if ( ! empty( $list ) ) {
		set_transient( $transient_name, array( 'head' => $head, 'list' => $list, 'foot' => $foot ) );
	}
	return $content;
}

add_filter( 'the_title', 'bawmrp_cut_words' );
function bawmrp_cut_words( $title ) {
	global $in_bawmrp_loop;
	if ( $in_bawmrp_loop ) {
		return wp_trim_words( $title, 10 );
	}
	return $title;
}