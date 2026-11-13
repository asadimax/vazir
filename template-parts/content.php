<?php
/**
 * نمایش خلاصهٔ نوشته در فهرست‌ها (صفحهٔ نخست، آرشیو)
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mfa_full = ( 'full' === get_theme_mod( 'mfa_list_content', 'excerpt' ) );
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'teaser clear' ); ?>>
	<header class="entry-header">
		<h2 class="entry-title"><a href="<?php the_permalink(); ?>" rel="bookmark"><?php the_title(); ?></a></h2>
		<?php mfa_comment_count_badge(); ?>
	</header>

	<div class="post-std">
		<?php mfa_post_date(); ?>
		<?php mfa_post_info(); ?>
	</div>

	<div class="entry-content clear">
		<?php if ( get_theme_mod( 'mfa_show_thumbs', true ) && has_post_thumbnail() ) : ?>
			<a class="post-thumb" href="<?php the_permalink(); ?>" tabindex="-1" aria-hidden="true">
				<?php the_post_thumbnail( 'post-thumbnail', mfa_list_thumb_attrs() ); ?>
			</a>
		<?php endif; ?>

		<?php
		if ( $mfa_full ) {
			the_content( 'ادامهٔ مطلب…' );
		} else {
			the_excerpt();
		}
		?>
	</div>

	<?php if ( get_theme_mod( 'mfa_show_cats', true ) && has_tag() ) : ?>
		<footer class="entry-footer">
			<?php the_tags( '<span class="tags">برچسب‌ها: ', '، ', '</span>' ); ?>
		</footer>
	<?php endif; ?>
</article>
