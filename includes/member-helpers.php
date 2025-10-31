<?php
/**
 * Member CPT Helper Functions
 *
 * Helper functions for working with Member custom post type data.
 *
 * @package    LoveTravelChild
 * @subpackage LoveTravelChild/includes
 * @since      2.2.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

/**
 * Get member occupation.
 *
 * @since  2.2.0
 * @param  int    $post_id  Member post ID.
 * @return string           Member occupation or empty string.
 */
function lovetravelChild_get_member_occupation( $post_id ) {
	return get_post_meta( $post_id, '_member_occupation', true );
}

/**
 * Get member social networks.
 *
 * @since  2.2.0
 * @param  int    $post_id  Member post ID.
 * @return array            Array of social networks with type and URL.
 */
function lovetravelChild_get_member_social_networks( $post_id ) {
	$social_networks = get_post_meta( $post_id, '_member_social_networks', true );
	return is_array( $social_networks ) ? $social_networks : array();
}

/**
 * Get social network icon class.
 *
 * Returns Font Awesome or other icon class for social network type.
 *
 * @since  2.2.0
 * @param  string $type     Social network type.
 * @return string           Icon class name.
 */
function lovetravelChild_get_social_network_icon( $type ) {
	$icons = array(
		'facebook'   => 'fab fa-facebook-f',
		'twitter'    => 'fab fa-twitter',
		'instagram'  => 'fab fa-instagram',
		'linkedin'   => 'fab fa-linkedin-in',
		'youtube'    => 'fab fa-youtube',
		'tiktok'     => 'fab fa-tiktok',
		'pinterest'  => 'fab fa-pinterest-p',
		'snapchat'   => 'fab fa-snapchat-ghost',
		'whatsapp'   => 'fab fa-whatsapp',
		'telegram'   => 'fab fa-telegram-plane',
		'website'    => 'fas fa-globe',
		'email'      => 'fas fa-envelope',
	);

	return isset( $icons[ $type ] ) ? $icons[ $type ] : 'fas fa-link';
}

/**
 * Get member data as array.
 *
 * Returns all member data in a structured array for easy use in widgets.
 *
 * @since  2.2.0
 * @param  int|WP_Post $post  Member post ID or post object.
 * @return array|false        Member data array or false if not found.
 */
function lovetravelChild_get_member_data( $post ) {
	$post = get_post( $post );
	
	if ( ! $post || $post->post_type !== 'lovetravel_member' ) {
		return false;
	}

	return array(
		'id'              => $post->ID,
		'name'            => $post->post_title,
		'about'           => $post->post_content,
		'occupation'      => lovetravelChild_get_member_occupation( $post->ID ),
		'avatar_id'       => get_post_thumbnail_id( $post->ID ),
		'avatar_url'      => get_the_post_thumbnail_url( $post->ID, 'medium' ),
		'social_networks' => lovetravelChild_get_member_social_networks( $post->ID ),
		'permalink'       => get_permalink( $post->ID ), // In case we enable single views later
	);
}

/**
 * Get members query.
 *
 * Utility function to query members with common parameters.
 *
 * @since  2.2.0
 * @param  array $args  Query arguments (optional).
 * @return WP_Query     Members query object.
 */
function lovetravelChild_get_members_query( $args = array() ) {
	$defaults = array(
		'post_type'      => 'lovetravel_member',
		'post_status'    => 'publish',
		'posts_per_page' => -1,
		'orderby'        => 'title',
		'order'          => 'ASC',
	);

	$args = wp_parse_args( $args, $defaults );
	
	return new WP_Query( $args );
}

/**
 * Render member social networks HTML.
 *
 * @since  2.2.0
 * @param  int    $post_id     Member post ID.
 * @param  string $wrapper     HTML wrapper element (default: 'div').
 * @param  string $classes     CSS classes for wrapper.
 * @return string              HTML output.
 */
function lovetravelChild_render_member_social_networks( $post_id, $wrapper = 'div', $classes = 'member-social-networks' ) {
	$social_networks = lovetravelChild_get_member_social_networks( $post_id );
	
	if ( empty( $social_networks ) ) {
		return '';
	}

	$html = sprintf( '<%s class="%s">', esc_attr( $wrapper ), esc_attr( $classes ) );
	
	foreach ( $social_networks as $network ) {
		if ( empty( $network['type'] ) || empty( $network['url'] ) ) {
			continue;
		}

		$icon_class = lovetravelChild_get_social_network_icon( $network['type'] );
		$label = ucfirst( $network['type'] );
		
		$html .= sprintf(
			'<a href="%s" target="_blank" rel="noopener" class="social-link social-link-%s" aria-label="%s">
				<i class="%s"></i>
			</a>',
			esc_url( $network['url'] ),
			esc_attr( $network['type'] ),
			esc_attr( sprintf( __( '%s profile', 'lovetravel-child' ), $label ) ),
			esc_attr( $icon_class )
		);
	}
	
	$html .= sprintf( '</%s>', esc_attr( $wrapper ) );
	
	return $html;
}