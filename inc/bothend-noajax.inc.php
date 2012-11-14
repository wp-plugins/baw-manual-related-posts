<?php
if( !defined( 'ABSPATH' ) )
	die( 'Cheatin\' uh?' );

add_action( 'init', 'bawmrp_init', 1 );
function bawmrp_init()
{
	global $bawmrp_options;
	load_plugin_textdomain( 'bawmrp', '', dirname( plugin_basename( BAWMRP__FILE__ ) ) . '/lang' );
	$bawmrp_args = array(	'post_types' => array( 'post' ),
				'in_content' => false,
				'in_content_mode' => 'list',
				'display_content' => 'none',
				'display_no_posts' => 'none',
				'display_no_posts_text' => __( 'No related posts found!', 'bawmrp' ),
				'in_homepage' => '',
				'max_posts' => 4,
				'random_posts' => false,
				'recent_posts' => false,
				'random_order' => false,
				'auto_posts' => 'none',
				'cache_time' => 1
			);
	$bawmrp_options = wp_parse_args( get_option( 'bawmrp' ), $bawmrp_args );
}

function bawmrp_get_all_related_posts( $post )
{
	global $bawmrp_options;
	if( !$related_posts = get_transient( 'bawmrp_allrelatedposts_' . $post->ID ) ):
		$ids_manual = wp_parse_id_list( bawmrp_get_related_posts( $post->ID ) );
		$ids_auto = $bawmrp_options['auto_posts']!='none' ||  $bawmrp_options['sticky_posts']=='on' ||  $bawmrp_options['recent_posts']=='on' ? bawmrp_get_related_posts_auto( $post ) : array();
		$ids = wp_parse_id_list( array_merge( $ids_manual, $ids_auto ) );
		$related_posts = get_posts( array( 'include' => $ids, 'post_type' => $bawmrp_options['post_types'] ) );
		set_transient( 'bawmrp_allrelatedposts_' . $post->ID, $related_posts, $bawmrp_options['cache_time']*60*60 );
	endif;
	return $related_posts;
}

function bawmrp_get_related_posts( $post_id, $only_published=true )
{
	global $bawmrp_options;
	$ids = get_post_meta( $post_id, '_yyarpp', true );
	if( $only_published )
		$ids = !empty( $ids ) != '' ? implode( ',', wp_parse_id_list( wp_list_pluck( get_posts( array( 'include' => $ids, 'post_type' => $bawmrp_options['post_types'] ) ), 'ID' ) ) ) : array();
	else
		$ids = !empty( $ids ) != '' ? implode( ',', wp_parse_id_list( $ids ) ) : array();
	return $ids;
}

function bawmrp_get_related_posts_auto( $post )
{
	global $bawmrp_options;
	// Get all manuals ids
	$rel_id = bawmrp_get_related_posts( $post->ID, false );
	// How many auto posts we need
	$num_posts = (int)$bawmrp_options['max_posts']==0 ? -1 : (int)$bawmrp_options['max_posts'] - count( wp_parse_id_list( $rel_id ) );
	// If we still need auto posts or post number is illimited
	if( $num_posts>0 || $num_posts==-1 ):
		$rel_id = array_filter( wp_parse_id_list( $rel_id ) );
		if( $bawmrp_options['auto_posts']!='none' ):
			$args = array(
				'post_type' => $post->post_type,
				'post_status' => 'publish',
				'post__not_in' => array_merge( array( $post->ID ), $rel_id ),
				'numberposts' => $num_posts,
				'post_count' => $num_posts,
				'order' => $bawmrp_options['random_order'] ? 'RAND' : 'DESC'
			);
			// auto posts by tags
			if( $bawmrp_options['auto_posts'] == 'tags' || $bawmrp_options['auto_posts'] == 'both' ):
				$tags = wp_get_post_tags( $post->ID, array( 'fields' => 'ids' ) );
				if( $tags )
					$args['tag__in'] = $tags;
			endif;
			// auto posts by cat
			if( $bawmrp_options['auto_posts'] == 'cat' || $bawmrp_options['auto_posts'] == 'both' ):
				$cat = get_the_category( $post->ID );
				$cat_ids = wp_list_pluck( $cat, 'term_id' );
				$args['category'] = implode( ',', $cat_ids );
			endif;
			// auto posts query
			$relative_query = get_posts( $args );
			$rel_id = array(); // set empty
			// we found posts ?
			if( !empty( $relative_query ) )
				$rel_id = wp_list_pluck( $relative_query, 'ID' );
		endif;
		// do we still need posts ? ok go sticky if checked
		if( $num_posts>count( $rel_id ) && $bawmrp_options['sticky_posts']=='on' )
			$rel_id = array_merge( $rel_id, get_option( 'sticky_posts' ) );
		$rel_id = array_flip($rel_id);
		if( isset( $rel_id[$post->ID] ) )
			unset( $rel_id[$post->ID] );
		$rel_id = array_flip($rel_id);
		// do we still need posts ? ok go recent if checked
		if( $num_posts>count( $rel_id ) && $bawmrp_options['recent_posts']=='on' ):
			$args = array(
				'post_type' => $post->post_type,
				'post_status' => 'publish',
				'post__not_in' => array_merge( array( $post->ID ), $rel_id ),
				'numberposts' => $num_posts - count( $rel_id ),
				'post_count' => $num_posts - count( $rel_id )
			);
			$recent_query = get_posts( $args );
			// we found posts ?
			if( !empty( $recent_query ) )
				$rel_id = array_merge( $rel_id, wp_list_pluck( $recent_query, 'ID' ) );
		endif;
		// return not empty
		return wp_parse_id_list( $rel_id );
	endif;
	// return empty
	return array();
}