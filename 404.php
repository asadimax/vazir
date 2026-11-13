<?php
/**
 * صفحهٔ ۴۰۴
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

	<section class="error-404">
		<h1 class="page-title">صفحهٔ مورد نظر پیدا نشد</h1>
		<p>ممکن است نشانی را اشتباه وارد کرده باشید یا صفحه حذف شده باشد. می‌توانید جست‌وجو کنید:</p>
		<?php get_search_form(); ?>
	</section>
</main>
<?php
get_footer();
