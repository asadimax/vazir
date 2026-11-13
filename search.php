<?php
/**
 * نتایج جست‌وجو
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

	<header class="archive-header">
		<h1 class="page-title">
			<?php
			/* translators: %s: عبارت جست‌وجو */
			printf( 'نتایج جست‌وجو برای «%s»', '<span class="search-term">' . esc_html( get_search_query() ) . '</span>' ); // phpcs:ignore WordPress.Security.EscapeOutput
			?>
		</h1>
	</header>

	<?php if ( have_posts() ) : ?>
		<?php
		while ( have_posts() ) :
			the_post();
			get_template_part( 'template-parts/content', 'search' );
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
