<?php
/**
 * وزیر — راه‌اندازی پوسته
 *
 * سازگار با PHP 7.4 تا 8.3 و وردپرس 5.8 به بالا.
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

define( 'MFA_VERSION', '1.0.0' );
define( 'MFA_DIR', get_template_directory() );
define( 'MFA_URI', get_template_directory_uri() );

require_once MFA_DIR . '/inc/jalali.php';
require_once MFA_DIR . '/inc/digits.php';
require_once MFA_DIR . '/inc/archive.php';
require_once MFA_DIR . '/inc/social.php';
require_once MFA_DIR . '/inc/seo.php';
require_once MFA_DIR . '/inc/performance.php';
require_once MFA_DIR . '/inc/customizer.php';
require_once MFA_DIR . '/inc/template-tags.php';

/**
 * این پوسته فقط راست‌چین است؛ حتی اگر زبان سایت انگلیسی باشد،
 * جهت سند و استایل‌های هسته (نوار مدیریت و ...) راست‌چین می‌شوند.
 */
function mfa_force_rtl() {
	global $wp_locale;
	if ( isset( $wp_locale ) && 'rtl' !== $wp_locale->text_direction ) {
		$wp_locale->text_direction = 'rtl';
	}
}
add_action( 'after_setup_theme', 'mfa_force_rtl', 1 );

/**
 * قابلیت‌های پوسته
 */
function mfa_setup() {
	add_theme_support( 'title-tag' );
	add_theme_support( 'automatic-feed-links' );
	add_theme_support( 'post-thumbnails' );
	add_theme_support( 'responsive-embeds' );
	add_theme_support( 'wp-block-styles' );
	add_theme_support( 'align-wide' );
	add_theme_support(
		'html5',
		array( 'search-form', 'comment-form', 'comment-list', 'gallery', 'caption', 'style', 'script' )
	);
	add_theme_support(
		'custom-logo',
		array(
			'height'      => 80,
			'width'       => 300,
			'flex-height' => true,
			'flex-width'  => true,
		)
	);

	set_post_thumbnail_size( 150, 150, true );

	register_nav_menus(
		array(
			'primary' => 'منوی اصلی (زیر سربرگ)',
			'footer'  => 'منوی پایین سایت',
		)
	);

	add_editor_style( array( 'assets/css/editor.css' ) );
}
add_action( 'after_setup_theme', 'mfa_setup' );

/**
 * عرض محتوا برای جاسازی‌ها (embed)
 */
function mfa_content_width() {
	$GLOBALS['content_width'] = 700;
}
add_action( 'after_setup_theme', 'mfa_content_width', 0 );

/**
 * ناحیه‌های ویجت
 */
function mfa_widgets_init() {
	$common = array(
		'before_widget' => '<section id="%1$s" class="block widget %2$s">',
		'after_widget'  => '</section>',
		'before_title'  => '<h3 class="block-title"><span>',
		'after_title'   => '</span></h3>',
	);

	register_sidebar(
		array_merge(
			$common,
			array(
				'name'        => 'سایدبار اصلی',
				'id'          => 'sidebar-1',
				'description' => 'ویجت‌های ستون کناری صفحات.',
			)
		)
	);

	register_sidebar(
		array_merge(
			$common,
			array(
				'name'        => 'پایین سایت',
				'id'          => 'footer-1',
				'description' => 'تا سه ستون در بالای بخش کپی‌رایت نمایش داده می‌شود.',
			)
		)
	);
}
add_action( 'widgets_init', 'mfa_widgets_init' );

/**
 * فونت وزیرمتن (فایل متغیر woff2 داخل خود پوسته؛ بدون وابستگی به سرویس خارجی).
 *
 * @return string کد @font-face
 */
function mfa_font_face_css() {
	$url = MFA_URI . '/assets/fonts/Vazirmatn-variable.woff2';
	return sprintf(
		'@font-face{font-family:"Vazirmatn";font-style:normal;font-weight:100 900;font-display:swap;src:url("%s") format("woff2");}',
		esc_url( $url )
	);
}

/**
 * استایل‌ها و اسکریپت‌ها
 */
function mfa_scripts() {
	$min = MFA_DIR . '/assets/css/style.min.css';
	if ( is_readable( $min ) && ! is_customize_preview() ) {
		// CSS فشرده مستقیم داخل صفحه قرار می‌گیرد: بدون درخواست جداگانه‌ی مسدودکنندهٔ رسم صفحه.
		$css = file_get_contents( $min ); // phpcs:ignore WordPress.WP.AlternativeFunctions
		$css = str_replace( '{{URI}}', MFA_URI, $css );
		wp_register_style( 'mfa-style', false, array(), MFA_VERSION );
		wp_enqueue_style( 'mfa-style' );
		wp_add_inline_style( 'mfa-style', mfa_font_face_css() . $css . mfa_customizer_css() );
	} else {
		wp_enqueue_style( 'mfa-style', get_stylesheet_uri(), array(), MFA_VERSION );
		wp_add_inline_style( 'mfa-style', mfa_font_face_css() . mfa_customizer_css() );
	}

	wp_enqueue_script( 'mfa-nav', MFA_URI . '/js/nav.js', array(), MFA_VERSION, true );
	wp_script_add_data( 'mfa-nav', 'strategy', 'defer' );

	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) ) {
		wp_enqueue_script( 'comment-reply' );
	}
}
add_action( 'wp_enqueue_scripts', 'mfa_scripts' );

/**
 * پیش‌بارگذاری فونت برای سرعت بیشتر
 */
function mfa_preload_font() {
	printf(
		'<link rel="preload" href="%s" as="font" type="font/woff2" crossorigin>' . "\n",
		esc_url( MFA_URI . '/assets/fonts/Vazirmatn-variable.woff2' )
	);
}
add_action( 'wp_head', 'mfa_preload_font', 1 );

/**
 * کلاس‌های body (محل سایدبار و ...)
 *
 * @param string[] $classes
 * @return string[]
 */
function mfa_body_classes( $classes ) {
	$classes[] = 'sidebar-' . mfa_sidebar_position();
	if ( ! mfa_has_sidebar() ) {
		$classes[] = 'no-sidebar';
	}
	$classes[] = 'header-' . get_theme_mod( 'mfa_header_bg', 'aurora' );
	return $classes;
}
add_filter( 'body_class', 'mfa_body_classes' );

/**
 * طول خلاصهٔ نوشته‌ها
 */
add_filter(
	'excerpt_length',
	static function () {
		return 40;
	}
);
add_filter(
	'excerpt_more',
	static function () {
		return '';
	}
);

/**
 * پیوند «ادامه …» در انتهای خلاصهٔ نوشته‌ها (صفحهٔ اصلی، دسته‌ها، جست‌وجو).
 * هم برای خلاصهٔ خودکار و هم برای خلاصهٔ دستی کار می‌کند.
 *
 * @param string $excerpt
 * @return string
 */
function mfa_excerpt_read_more( $excerpt ) {
	if ( is_admin() || is_feed() || is_singular() || '' === trim( $excerpt ) ) {
		return $excerpt;
	}
	$link = sprintf(
		' <a class="more-link" href="%s">ادامه …<span class="screen-reader-text"> %s</span></a>',
		esc_url( get_permalink() ),
		esc_html( get_the_title() )
	);
	if ( preg_match( '~</p>\s*$~', $excerpt ) ) {
		return preg_replace( '~</p>\s*$~', $link . '</p>', $excerpt );
	}
	return $excerpt . $link;
}
add_filter( 'the_excerpt', 'mfa_excerpt_read_more', 20 );

/**
 * اندازهٔ تصویر شاخص ابتدای نوشته: تصویر اصلی بدون برش (برای فایل‌های خیلی بزرگ، اندازهٔ large).
 *
 * @return string
 */
function mfa_hero_image_size() {
	$meta = wp_get_attachment_metadata( get_post_thumbnail_id() );
	return ( is_array( $meta ) && ! empty( $meta['width'] ) && (int) $meta['width'] > 1000 ) ? 'large' : 'full';
}

/**
 * حذف تعداد کلمهٔ اضافی در عنوان آرشیو (پیشوند «دسته:» و ...)
 */
add_filter(
	'get_the_archive_title_prefix',
	static function () {
		return '';
	}
);
