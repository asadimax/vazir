<?php
/**
 * تاریخ شمسی (جلالی)
 *
 * تاریخ‌های نمایش‌داده‌شده در قالب را با فیلتر wp_date به شمسی تبدیل می‌کند.
 * اگر افزونهٔ WP-Parsidate (یا مشابه) فعال باشد، یا گزینه در سفارشی‌سازی خاموش باشد،
 * هیچ کاری نمی‌کند. خروجی‌های ماشینی (فرمت‌های c و r و U، فید و REST) تغییر نمی‌کنند.
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * تبدیل تاریخ میلادی به شمسی.
 *
 * @param int $gy سال میلادی.
 * @param int $gm ماه میلادی (۱ تا ۱۲).
 * @param int $gd روز میلادی.
 * @return int[] [سال، ماه، روز] شمسی.
 */
function mfa_gregorian_to_jalali( $gy, $gm, $gd ) {
	$g_d_m = array( 0, 31, 59, 90, 120, 151, 181, 212, 243, 273, 304, 334 );

	if ( $gy > 1600 ) {
		$jy  = 979;
		$gy -= 1600;
	} else {
		$jy  = 0;
		$gy -= 621;
	}

	$gy2  = ( $gm > 2 ) ? ( $gy + 1 ) : $gy;
	$days = ( 365 * $gy ) + intdiv( $gy2 + 3, 4 ) - intdiv( $gy2 + 99, 100 ) + intdiv( $gy2 + 399, 400 ) - 80 + $gd + $g_d_m[ $gm - 1 ];

	$jy  += 33 * intdiv( $days, 12053 );
	$days = $days % 12053;
	$jy  += 4 * intdiv( $days, 1461 );
	$days = $days % 1461;

	if ( $days > 365 ) {
		$jy  += intdiv( $days - 1, 365 );
		$days = ( $days - 1 ) % 365;
	}

	if ( $days < 186 ) {
		$jm = 1 + intdiv( $days, 31 );
		$jd = 1 + ( $days % 31 );
	} else {
		$jm = 7 + intdiv( $days - 186, 30 );
		$jd = 1 + ( ( $days - 186 ) % 30 );
	}

	return array( $jy, $jm, $jd );
}

/**
 * نام ماه‌های شمسی.
 *
 * @return string[]
 */
function mfa_jalali_months() {
	return array( 'فروردین', 'اردیبهشت', 'خرداد', 'تیر', 'مرداد', 'شهریور', 'مهر', 'آبان', 'آذر', 'دی', 'بهمن', 'اسفند' );
}

/**
 * قالب‌دهی تاریخ شمسی با همان نشانه‌های تابع date().
 * نشانه‌های پشتیبانی‌شده: d j m n F M Y y D l a A ؛ بقیه از DateTime گرفته می‌شوند.
 *
 * @param string    $format   قالب تاریخ.
 * @param DateTime  $dt       شیء تاریخ (با منطقهٔ زمانی سایت).
 * @return string
 */
function mfa_jalali_format( $format, DateTime $dt ) {
	list( $jy, $jm, $jd ) = mfa_gregorian_to_jalali( (int) $dt->format( 'Y' ), (int) $dt->format( 'n' ), (int) $dt->format( 'j' ) );

	$months   = mfa_jalali_months();
	$weekdays = array( 'یکشنبه', 'دوشنبه', 'سه‌شنبه', 'چهارشنبه', 'پنجشنبه', 'جمعه', 'شنبه' );
	$short    = array( 'ی', 'د', 'س', 'چ', 'پ', 'ج', 'ش' );
	$w        = (int) $dt->format( 'w' );

	$out = '';
	$len = strlen( $format );
	for ( $i = 0; $i < $len; $i++ ) {
		$c = $format[ $i ];

		if ( '\\' === $c ) { // نویسهٔ بعدی بدون تغییر.
			$i++;
			if ( $i < $len ) {
				$out .= $format[ $i ];
			}
			continue;
		}

		switch ( $c ) {
			case 'd':
				$out .= sprintf( '%02d', $jd );
				break;
			case 'j':
				$out .= $jd;
				break;
			case 'm':
				$out .= sprintf( '%02d', $jm );
				break;
			case 'n':
				$out .= $jm;
				break;
			case 'F':
			case 'M':
				$out .= $months[ $jm - 1 ];
				break;
			case 'Y':
				$out .= $jy;
				break;
			case 'y':
				$out .= sprintf( '%02d', $jy % 100 );
				break;
			case 'l':
				$out .= $weekdays[ $w ];
				break;
			case 'D':
				$out .= $short[ $w ];
				break;
			case 'a':
				$out .= ( 'am' === $dt->format( 'a' ) ) ? 'ق.ظ' : 'ب.ظ';
				break;
			case 'A':
				$out .= ( 'AM' === $dt->format( 'A' ) ) ? 'قبل از ظهر' : 'بعد از ظهر';
				break;
			default:
				$out .= $dt->format( $c );
		}
	}

	return $out;
}

/**
 * آیا باید تاریخ‌ها شمسی شوند؟
 *
 * @return bool
 */
function mfa_jalali_enabled() {
	// قبل از اجرای کوئری، تابع‌های شرطی وردپرس قابل‌اعتماد نیستند.
	if ( ! did_action( 'wp' ) || is_admin() || is_feed() || ( defined( 'REST_REQUEST' ) && REST_REQUEST ) || wp_doing_ajax() ) {
		return false;
	}
	if ( function_exists( 'parsidate' ) || class_exists( 'wp_parsidate' ) ) {
		return false;
	}
	return (bool) get_theme_mod( 'mfa_jalali', true );
}

/**
 * فیلتر wp_date.
 *
 * @param string       $date      تاریخ قالب‌دهی‌شده.
 * @param string       $format    قالب.
 * @param int          $timestamp زمان یونیکس.
 * @param DateTimeZone $timezone  منطقهٔ زمانی.
 * @return string
 */
function mfa_filter_wp_date( $date, $format, $timestamp, $timezone ) {
	// خروجی‌های ماشینی (مثل datetime در تگ time) را دست نمی‌زنیم.
	if ( in_array( $format, array( 'c', 'r', 'U', 'Y-m-d', 'Y-m-d H:i:s', DATE_W3C, DATE_RFC2822 ), true ) ) {
		return $date;
	}

	if ( mfa_jalali_enabled() ) {
		// قالب‌های پیش‌فرض انگلیسی وردپرس را به ترتیب فارسی (روز، ماه، سال) برمی‌گردانیم.
		$format = strtr(
			$format,
			array(
				'F j, Y' => 'j F Y',
				'M j, Y' => 'j M Y',
				'F jS, Y' => 'j F Y',
				'l, F j, Y' => 'l، j F Y',
			)
		);
		try {
			$dt = new DateTime( '@' . (int) $timestamp );
			$dt->setTimezone( $timezone instanceof DateTimeZone ? $timezone : wp_timezone() );
			$date = mfa_jalali_format( $format, $dt );
		} catch ( Exception $e ) {
			return $date;
		}
	}

	return mfa_digits_enabled() ? mfa_fa_digits_plain( $date ) : $date;
}
add_filter( 'wp_date', 'mfa_filter_wp_date', 10, 4 );
