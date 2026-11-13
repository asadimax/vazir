<?php
/**
 * آرشیو دسته، برچسب، نویسنده و تاریخ
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

get_header();
?>
<main id="content" class="content-area">
	<?php mfa_breadcrumbs(); ?>

	<?php if ( have_posts() ) : ?>
		<header class="archive-header">
			<?php the_archive_title( '<h1 class="page-title">', '</h1>' ); ?>
			<?php the_archive_description( '<div class="archive-description">', '</div>' ); ?>
		</header>

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
