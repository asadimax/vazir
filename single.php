<?php
/**
 * نوشتهٔ تکی
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

	<?php
	while ( have_posts() ) :
		the_post();
		get_template_part( 'template-parts/content', 'single' );

		the_post_navigation(
			array(
				'prev_text' => '<span class="nav-label">نوشتهٔ قبلی</span> <span class="nav-title">%title</span>',
				'next_text' => '<span class="nav-label">نوشتهٔ بعدی</span> <span class="nav-title">%title</span>',
			)
		);

		if ( comments_open() || get_comments_number() ) {
			comments_template();
		}
	endwhile;
	?>
</main>
<?php
get_sidebar();
get_footer();
