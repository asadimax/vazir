<?php
/**
 * ارقام فارسی
 *
 * اعداد لاتین را در متن‌های نمایشی (عنوان، محتوا، دیدگاه، تاریخ و ...) به ارقام فارسی تبدیل می‌کند.
 * فقط متن بین تگ‌ها تغییر می‌کند؛ تگ‌ها، ویژگی‌ها، موجودیت‌های HTML (مثل &#8217;) و محتوای
 * pre/code/script/style دست‌نخورده می‌مانند. در مدیریت، فید و REST هیچ تغییری داده نمی‌شود.
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * آیا تبدیل ارقام فعال است؟
 *
 * @return bool
 */
function mfa_digits_enabled() {
	if ( ! empty( $GLOBALS['mfa_no_digits'] ) ) {
		return false; // خروجی ماشینی (Schema و ...).
	}
	// قبل از اجرای کوئری، تابع‌های شرطی وردپرس قابل‌اعتماد نیستند.
	if ( ! did_action( 'wp' ) || is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_ajax() ) {
		return false;
	}
	return (bool) get_theme_mod( 'mfa_persian_digits', true );
}

/**
 * تبدیل ارقام یک رشتهٔ متنی ساده (بدون HTML).
 *
 * @param string $text
 * @return string
 */
function mfa_fa_digits_plain( $text ) {
	return strtr(
		(string) $text,
		array(
			'0' => '۰',
			'1' => '۱',
			'2' => '۲',
			'3' => '۳',
			'4' => '۴',
			'5' => '۵',
			'6' => '۶',
			'7' => '۷',
			'8' => '۸',
			'9' => '۹',
		)
	);
}

/**
 * تبدیل ارقام در یک تکه HTML، با حفظ تگ‌ها و موجودیت‌ها.
 *
 * @param string $html
 * @return string
 */
function mfa_fa_digits_html( $html ) {
	if ( '' === $html || ! is_string( $html ) || ! mfa_digits_enabled() ) {
		return $html;
	}

	$parts = preg_split( '/(<[^>]*>)/', $html, -1, PREG_SPLIT_DELIM_CAPTURE );
	if ( false === $parts ) {
		return $html;
	}

	$skip = 0;
	$tags = 'pre|code|script|style|textarea|kbd|samp';
	foreach ( $parts as $i => $part ) {
		if ( '' === $part ) {
			continue;
		}
		if ( '<' === $part[0] ) {
			if ( preg_match( '#^<(?:' . $tags . ')\b#i', $part ) ) {
				$skip++;
			} elseif ( preg_match( '#^</(?:' . $tags . ')\b#i', $part ) ) {
				$skip = max( 0, $skip - 1 );
			}
			continue;
		}
		if ( 0 === $skip ) {
			$parts[ $i ] = preg_replace_callback(
				'/&#?\w+;|[0-9]+/',
				static function ( $m ) {
					return ( '&' === $m[0][0] ) ? $m[0] : mfa_fa_digits_plain( $m[0] );
				},
				$part
			);
		}
	}

	return implode( '', $parts );
}

foreach ( array( 'the_title', 'the_content', 'the_excerpt', 'comment_text', 'widget_title', 'get_the_archive_title', 'wp_list_categories', 'get_archives_link' ) as $mfa_hook ) {
	add_filter( $mfa_hook, 'mfa_fa_digits_html', 20 );
}
unset( $mfa_hook );

/**
 * number_format_i18n: اعداد خروجی توابع هسته (تعداد دیدگاه و ...).
 */
add_filter(
	'number_format_i18n',
	static function ( $formatted ) {
		return mfa_digits_enabled() ? mfa_fa_digits_plain( $formatted ) : $formatted;
	},
	20
);
