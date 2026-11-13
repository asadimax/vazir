<?php
/**
 * برگه
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<article id="post-<?php the_ID(); ?>" <?php post_class( 'single-page' ); ?>>
	<header class="entry-header">
		<h1 class="entry-title"><?php the_title(); ?></h1>
	</header>

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

	<?php edit_post_link( 'ویرایش', '<p class="edit-link">', '</p>' ); ?>
</article>
