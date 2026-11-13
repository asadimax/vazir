<?php
/**
 * وقتی چیزی پیدا نشد
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<section class="no-results">
	<h1 class="page-title">چیزی پیدا نشد</h1>
	<?php if ( is_search() ) : ?>
		<p>نتیجه‌ای برای عبارت شما نبود. با کلمه‌های دیگر دوباره امتحان کنید.</p>
	<?php else : ?>
		<p>هنوز مطلبی در این بخش منتشر نشده است.</p>
	<?php endif; ?>
	<?php get_search_form(); ?>
</section>
