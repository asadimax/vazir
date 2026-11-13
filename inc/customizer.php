<?php
/**
 * تنظیمات سفارشی‌سازی (Customizer)
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * طرح‌های رنگی (همان طرح‌های اصلی Mystique).
 *
 * @return array<string,array{label:string,accent:string,hover:string}>
 */
function mfa_schemes() {
	return array(
		'blue'   => array(
			'label'  => 'آبی',
			'accent' => '#2563eb',
			'hover'  => '#1d4ed8',
		),
		'teal'   => array(
			'label'  => 'فیروزه‌ای',
			'accent' => '#0f766e',
			'hover'  => '#115e59',
		),
		'purple' => array(
			'label'  => 'بنفش',
			'accent' => '#7c3aed',
			'hover'  => '#6d28d9',
		),
		'amber'  => array(
			'label'  => 'کهربایی',
			'accent' => '#b45309',
			'hover'  => '#92400e',
		),
		'green'  => array(
			'label'  => 'سبز',
			'accent' => '#5c7503',
			'hover'  => '#4a5f02',
		),
		'red'    => array(
			'label'  => 'قرمز',
			'accent' => '#bf1616',
			'hover'  => '#df2121',
		),
		'pink'   => array(
			'label'  => 'صورتی',
			'accent' => '#d00a40',
			'hover'  => '#b3083a',
		),
		'silver' => array(
			'label'  => 'نقره‌ای',
			'accent' => '#414141',
			'hover'  => '#206ba4',
		),
	);
}

/**
 * جایگاه سایدبار: end = سمت چپ (پیش‌فرض)، start = سمت راست، none = بدون سایدبار.
 *
 * @return string
 */
function mfa_sidebar_position() {
	$pos = get_theme_mod( 'mfa_sidebar', 'end' );
	return in_array( $pos, array( 'end', 'start', 'none' ), true ) ? $pos : 'end';
}

/**
 * آیا در این صفحه سایدبار نمایش داده شود؟
 *
 * @return bool
 */
function mfa_has_sidebar() {
	if ( 'none' === mfa_sidebar_position() ) {
		return false;
	}
	if ( is_page_template( 'page-full.php' ) || is_404() ) {
		return false;
	}
	return is_active_sidebar( 'sidebar-1' );
}

/**
 * روشنایی نسبی یک رنگ (طبق WCAG).
 *
 * @param string $hex رنگ هگزادسیمال (#rrggbb یا #rgb).
 * @return float
 */
function mfa_luminance( $hex ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$lum = 0;
	foreach ( array( array( 0.2126, 0 ), array( 0.7152, 2 ), array( 0.0722, 4 ) ) as $pair ) {
		list( $w, $i ) = $pair;
		$c    = hexdec( substr( $hex, $i, 2 ) ) / 255;
		$c    = ( $c <= 0.03928 ) ? $c / 12.92 : pow( ( $c + 0.055 ) / 1.055, 2.4 );
		$lum += $w * $c;
	}
	return $lum;
}

/**
 * رنگ را آن‌قدر تیره می‌کند که متن سفید روی آن حداقل کنتراست ۴٫۵ داشته باشد (استاندارد WCAG AA).
 *
 * @param string $hex رنگ ورودی.
 * @return string رنگ خروجی (#rrggbb).
 */
function mfa_ensure_white_contrast( $hex ) {
	$hex = ltrim( $hex, '#' );
	if ( 3 === strlen( $hex ) ) {
		$hex = $hex[0] . $hex[0] . $hex[1] . $hex[1] . $hex[2] . $hex[2];
	}
	$rgb = array( hexdec( substr( $hex, 0, 2 ) ), hexdec( substr( $hex, 2, 2 ) ), hexdec( substr( $hex, 4, 2 ) ) );
	for ( $i = 0; $i < 40; $i++ ) {
		$out = sprintf( '#%02x%02x%02x', $rgb[0], $rgb[1], $rgb[2] );
		if ( 1.05 / ( mfa_luminance( $out ) + 0.05 ) >= 4.5 ) {
			return $out;
		}
		$rgb = array( (int) round( $rgb[0] * 0.95 ), (int) round( $rgb[1] * 0.95 ), (int) round( $rgb[2] * 0.95 ) );
	}
	return $out;
}

/**
 * CSS پویای وابسته به تنظیمات.
 *
 * @return string
 */
function mfa_customizer_css() {
	$schemes = mfa_schemes();
	$scheme  = get_theme_mod( 'mfa_scheme', 'blue' );
	if ( ! isset( $schemes[ $scheme ] ) ) {
		$scheme = 'blue';
	}

	$accent = $schemes[ $scheme ]['accent'];
	$hover  = $schemes[ $scheme ]['hover'];

	$custom = sanitize_hex_color( get_theme_mod( 'mfa_accent', '' ) );
	if ( $custom ) {
		$accent = $custom;
		$hover  = $custom;
	}

	// متن سفید روی رنگ اصلی (منو، دکمه‌ها) باید خوانا باشد.
	$accent = mfa_ensure_white_contrast( $accent );
	$hover  = mfa_ensure_white_contrast( $hover );

	$css  = ':root{';
	$css .= '--accent:' . $accent . ';';
	$css .= '--accent-hover:' . $hover . ';';
	$css .= '}';

	return $css;
}

/**
 * ثبت تنظیمات و کنترل‌ها.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function mfa_customize_register( $wp_customize ) {
	$wp_customize->add_section(
		'mfa_options',
		array(
			'title'    => 'تنظیمات وزیر',
			'priority' => 30,
		)
	);

	// طرح رنگی.
	$choices = array();
	foreach ( mfa_schemes() as $key => $scheme ) {
		$choices[ $key ] = $scheme['label'];
	}
	$wp_customize->add_setting(
		'mfa_scheme',
		array(
			'default'           => 'blue',
			'sanitize_callback' => static function ( $v ) {
				return array_key_exists( $v, mfa_schemes() ) ? $v : 'blue';
			},
		)
	);
	$wp_customize->add_control(
		'mfa_scheme',
		array(
			'label'   => 'طرح رنگی',
			'section' => 'mfa_options',
			'type'    => 'select',
			'choices' => $choices,
		)
	);

	// رنگ دلخواه.
	$wp_customize->add_setting(
		'mfa_accent',
		array(
			'default'           => '',
			'sanitize_callback' => 'sanitize_hex_color',
		)
	);
	$wp_customize->add_control(
		new WP_Customize_Color_Control(
			$wp_customize,
			'mfa_accent',
			array(
				'label'       => 'رنگ دلخواه (جایگزین طرح رنگی)',
				'description' => 'برای برگشت به طرح رنگی، رنگ را پاک کنید.',
				'section'     => 'mfa_options',
			)
		)
	);

	// تصویر سربرگ.
	$wp_customize->add_setting(
		'mfa_header_bg',
		array(
			'default'           => 'aurora',
			'sanitize_callback' => static function ( $v ) {
				return in_array( $v, array( 'aurora', 'scarecrow', 'plain' ), true ) ? $v : 'aurora';
			},
		)
	);
	$wp_customize->add_control(
		'mfa_header_bg',
		array(
			'label'   => 'پس‌زمینهٔ سربرگ',
			'section' => 'mfa_options',
			'type'    => 'select',
			'choices' => array(
				'aurora'    => 'طیف رنگی',
				'scarecrow' => 'مترسک (پیش‌فرض Mystique)',
				'plain'     => 'ساده (سیاه)',
			),
		)
	);

	// جایگاه سایدبار.
	$wp_customize->add_setting(
		'mfa_sidebar',
		array(
			'default'           => 'end',
			'sanitize_callback' => static function ( $v ) {
				return in_array( $v, array( 'end', 'start', 'none' ), true ) ? $v : 'end';
			},
		)
	);
	$wp_customize->add_control(
		'mfa_sidebar',
		array(
			'label'   => 'جایگاه سایدبار',
			'section' => 'mfa_options',
			'type'    => 'select',
			'choices' => array(
				'end'   => 'سمت چپ',
				'start' => 'سمت راست',
				'none'  => 'بدون سایدبار',
			),
		)
	);

	// محتوای فهرست‌ها.
	$wp_customize->add_setting(
		'mfa_list_content',
		array(
			'default'           => 'excerpt',
			'sanitize_callback' => static function ( $v ) {
				return in_array( $v, array( 'excerpt', 'full' ), true ) ? $v : 'excerpt';
			},
		)
	);
	$wp_customize->add_control(
		'mfa_list_content',
		array(
			'label'   => 'نمایش نوشته‌ها در صفحهٔ اصلی و آرشیو',
			'section' => 'mfa_options',
			'type'    => 'select',
			'choices' => array(
				'excerpt' => 'خلاصه',
				'full'    => 'متن کامل',
			),
		)
	);

	// حالت تاریک.
	$wp_customize->add_setting(
		'mfa_dark_default',
		array(
			'default'           => 'light',
			'sanitize_callback' => static function ( $v ) {
				return in_array( $v, array( 'light', 'dark', 'auto' ), true ) ? $v : 'light';
			},
		)
	);
	$wp_customize->add_control(
		'mfa_dark_default',
		array(
			'label'       => 'حالت پیش‌فرض برای بازدیدکننده',
			'description' => 'اگر بازدیدکننده خودش با کلید سربرگ حالت را انتخاب کند، انتخاب او ذخیره و اعمال می‌شود.',
			'section'     => 'mfa_options',
			'type'        => 'select',
			'choices'     => array(
				'light' => 'روشن',
				'dark'  => 'تاریک',
				'auto'  => 'مطابق تنظیم دستگاه',
			),
		)
	);

	// چک‌باکس‌ها.
	$checks = array(
		'mfa_persian_digits' => array( 'نمایش ارقام فارسی (۰۱۲۳…) در متن‌ها و تاریخ‌ها', true ),
		'mfa_jalali'         => array( 'نمایش تاریخ شمسی (اگر افزونهٔ تاریخ شمسی فعال نیست)', true ),
		'mfa_dark_toggle'    => array( 'نمایش کلید حالت روشن/تاریک در سربرگ', true ),
		'mfa_show_thumbs'    => array( 'نمایش تصویر شاخص در فهرست نوشته‌ها', true ),
		'mfa_show_author'    => array( 'نمایش نام نویسنده', true ),
		'mfa_show_cats'      => array( 'نمایش دسته‌ها و برچسب‌ها', true ),
	);
	foreach ( $checks as $id => $data ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => $data[1],
				'sanitize_callback' => static function ( $v ) {
					return (bool) $v;
				},
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $data[0],
				'section' => 'mfa_options',
				'type'    => 'checkbox',
			)
		);
	}

	// متن پایین سایت.
	$wp_customize->add_setting(
		'mfa_footer_text',
		array(
			'default'           => '',
			'sanitize_callback' => 'wp_kses_post',
		)
	);
	$wp_customize->add_control(
		'mfa_footer_text',
		array(
			'label'       => 'متن کپی‌رایت',
			'description' => 'خالی بگذارید تا متن پیش‌فرض نمایش داده شود.',
			'section'     => 'mfa_options',
			'type'        => 'textarea',
		)
	);
}
add_action( 'customize_register', 'mfa_customize_register' );
