<?php
/**
 * سرعت و بهینه‌سازی
 *
 * - پیش‌بارگذاری تصویر سربرگ (WebP)
 * - پاک‌سازی چند خروجی غیرضروری وردپرس در head (ایموجی، ژنراتور و ...)
 *
 * بارگذاری کند (lazy) تصاویر را خود وردپرس مدیریت می‌کند: تصویر اول صفحه فوری و بقیه با تأخیر
 * بارگذاری می‌شوند؛ پوسته عمداً ویژگی loading را دستی تنظیم نمی‌کند تا این تشخیص خراب نشود.
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * نام فایل تصویر سربرگ انتخاب‌شده (بدون پسوند) یا رشتهٔ خالی برای حالت ساده.
 *
 * @return string
 */
function mfa_header_image_slug() {
	$bg = get_theme_mod( 'mfa_header_bg', 'aurora' );
	return in_array( $bg, array( 'aurora', 'scarecrow' ), true ) ? 'header-' . $bg : '';
}

/**
 * پیش‌بارگذاری تصویر سربرگ برای نمایش سریع‌تر بالای صفحه.
 */
function mfa_preload_header_image() {
	$slug = mfa_header_image_slug();
	if ( ! $slug || ! file_exists( MFA_DIR . '/assets/img/' . $slug . '.webp' ) ) {
		return;
	}
	printf(
		'<link rel="preload" as="image" href="%s" type="image/webp" fetchpriority="high">' . "\n",
		esc_url( MFA_URI . '/assets/img/' . $slug . '.webp' )
	);
}
add_action( 'wp_head', 'mfa_preload_header_image', 1 );

/**
 * حذف خروجی‌های اضافی head (قابل خاموش‌کردن از سفارشی‌سازی).
 */
function mfa_head_cleanup() {
	if ( ! get_theme_mod( 'mfa_head_cleanup', true ) ) {
		return;
	}

	remove_action( 'wp_head', 'print_emoji_detection_script', 7 );
	remove_action( 'wp_print_styles', 'print_emoji_styles' );
	remove_action( 'wp_head', 'wp_generator' );
	remove_action( 'wp_head', 'wlwmanifest_link' );
	remove_action( 'wp_head', 'rsd_link' );
	remove_action( 'wp_head', 'wp_shortlink_wp_head', 10 );
}
add_action( 'init', 'mfa_head_cleanup' );

/**
 * فقط نسخه‌های لازم تصویر ساخته شود (اصل + بندانگشتی ۱۵۰)؛ جلوگیری از پر شدن فضای هاست.
 * فقط بر آپلودهای تازه اثر دارد. از سفارشی‌سازی «سئو و سرعت» قابل خاموش‌کردن است.
 *
 * @param array $sizes اندازه‌های پیش‌فرض برای ساخت.
 * @return array
 */
function mfa_limit_image_sizes( $sizes ) {
	if ( ! get_theme_mod( 'mfa_limit_images', true ) ) {
		return $sizes;
	}
	return array_intersect_key( $sizes, array_flip( array( 'thumbnail', 'post-thumbnail' ) ) );
}
add_filter( 'intermediate_image_sizes_advanced', 'mfa_limit_image_sizes', 20 );

/**
 * آیا تصویر کوچک نوشتهٔ اول فهرست باید اولویت بالا بگیرد؟ (معمولاً همان عنصر LCP صفحهٔ اصلی است)
 *
 * @return bool
 */
function mfa_is_first_list_thumb() {
	global $wp_query;
	return ! is_singular() && isset( $wp_query->current_post ) && 0 === (int) $wp_query->current_post && ! is_paged();
}

/**
 * ویژگی‌های img برای تصویر کوچک فهرست‌ها: نوشتهٔ اول با fetchpriority=high.
 *
 * @return array
 */
function mfa_list_thumb_attrs() {
	return mfa_is_first_list_thumb() ? array( 'fetchpriority' => 'high' ) : array();
}

/**
 * پیش‌بارگذاری تصویر کوچک نوشتهٔ اول در صفحهٔ اصلی و آرشیوها (حذف تأخیر شروع دانلود).
 */
function mfa_preload_first_thumb() {
	global $wp_query;
	if ( is_singular() || is_paged() || ! get_theme_mod( 'mfa_show_thumbs', true ) || empty( $wp_query->posts[0] ) ) {
		return;
	}
	$thumb_id = get_post_thumbnail_id( $wp_query->posts[0] );
	if ( ! $thumb_id ) {
		return;
	}
	$url = wp_get_attachment_image_url( $thumb_id, 'post-thumbnail' );
	if ( ! $url ) {
		return;
	}
	$srcset = wp_get_attachment_image_srcset( $thumb_id, 'post-thumbnail' );
	$sizes  = wp_get_attachment_image_sizes( $thumb_id, 'post-thumbnail' );
	printf(
		'<link rel="preload" as="image" href="%1$s"%2$s%3$s fetchpriority="high">' . "\n",
		esc_url( $url ),
		$srcset ? ' imagesrcset="' . esc_attr( $srcset ) . '"' : '',
		( $srcset && $sizes ) ? ' imagesizes="' . esc_attr( $sizes ) . '"' : ''
	);
}
add_action( 'wp_head', 'mfa_preload_first_thumb', 2 );
