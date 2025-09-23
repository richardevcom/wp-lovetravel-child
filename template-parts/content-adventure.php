<?php
/**
 * Template part for displaying adventure post content
 *
 * @package LoveTravel_Child
 * @subpackage Template_Parts
 * @since LoveTravel Child 1.0
 */

// Prevent direct access
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<article id="post-<?php the_ID(); ?>" <?php post_class( 'adventure-content' ); ?>>
	
	<header class="entry-header">
		<?php
		if ( is_singular() ) :
			the_title( '<h1 class="entry-title">', '</h1>' );
		else :
			the_title( '<h2 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h2>' );
		endif;
		?>
	</header><!-- .entry-header -->

	<?php if ( has_post_thumbnail() ) : ?>
		<div class="post-thumbnail">
			<?php the_post_thumbnail( 'large' ); ?>
		</div><!-- .post-thumbnail -->
	<?php endif; ?>

	<div class="entry-content">
		<?php
		if ( is_singular() ) :
			the_content();
		else :
			the_excerpt();
		endif;
		?>
	</div><!-- .entry-content -->

	<footer class="entry-footer">
		<?php
		// Display adventure meta information
		$duration = get_post_meta( get_the_ID(), 'nd_travel_meta_box_duration', true );
		$difficulty = get_post_meta( get_the_ID(), 'nd_travel_meta_box_difficulty', true );
		
		if ( $duration || $difficulty ) :
		?>
			<div class="adventure-meta">
				<?php if ( $duration ) : ?>
					<span class="adventure-duration">
						<?php echo esc_html__( 'Duration:', 'lovetravel-child' ) . ' ' . esc_html( $duration ); ?>
					</span>
				<?php endif; ?>
				
				<?php if ( $difficulty ) : ?>
					<span class="adventure-difficulty">
						<?php echo esc_html__( 'Difficulty:', 'lovetravel-child' ) . ' ' . esc_html( $difficulty ); ?>
					</span>
				<?php endif; ?>
			</div><!-- .adventure-meta -->
		<?php endif; ?>
	</footer><!-- .entry-footer -->

</article><!-- #post-<?php the_ID(); ?> -->