<?php
/**
 * نوشتهٔ تکی
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-post' ); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</header>

	<div class="post-std">
		<?php mfa_post_date(); ?>
		<?php mfa_post_info(); ?>
	</div>

	<?php if ( has_post_thumbnail() ) : ?>
		<figure class="post-hero"><?php the_post_thumbnail( mfa_hero_image_size() ); ?></figure>
	<?php endif; ?>

	<div class="entry-content clear">
		<?php
		the_content();
		wp_link_pages(
			array(
				'before' => '<nav class="page-links">صفحه‌ها: ',
				'after'  => '</nav>',
			)
		);
		?>
	</div>

	<?php if ( get_theme_mod( 'mfa_show_cats', true ) && has_tag() ) : ?>
		<footer class="entry-footer">
			<?php the_tags( '<span class="tags">برچسب‌ها: ', '، ', '</span>' ); ?>
		</footer>
	<?php endif; ?>

	<?php edit_post_link( 'ویرایش', '<p class="edit-link">', '</p>' ); ?>
</article>
