<?php
/**
 * قالب اصلی: صفحهٔ نخست و فهرست نوشته‌ها
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" class="content-area">
	<?php if ( is_home() && ! is_front_page() && single_post_title( '', false ) ) : ?>
		<h1 class="page-title"><?php single_post_title(); ?></h1>
	<?php endif; ?>

	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', get_post_type() );
		endwhile;
		mfa_pagination();
		?>
	<?php else : ?>
		<?php get_template_part( 'template-parts/content', 'none' ); ?>
	<?php endif; ?>
</main>
<?php
get_sidebar();
get_footer();
