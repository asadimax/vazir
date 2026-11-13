<?php
/**
 * بایگانی شمسی
 *
 * ویجت و بلوک «بایگانی» وردپرس را با فهرست ماه‌های شمسی (مثلاً «مهر ۱۴۰۵») جایگزین می‌کند.
 * هر ماه به نشانی ‎/?jm=140507‎ پیوند می‌خورد که نوشته‌های همان ماه شمسی را نشان می‌دهد
 * (روی هر تنظیم پیوند یکتا کار می‌کند و نیازی به بازنویسی قوانین نیست).
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * تبدیل تاریخ شمسی به میلادی.
 *
 * @param int $jy سال شمسی.
 * @param int $jm ماه شمسی (۱ تا ۱۲).
 * @param int $jd روز شمسی.
 * @return int[] [سال، ماه، روز] میلادی.
 */
function mfa_jalali_to_gregorian( $jy, $jm, $jd ) {
	if ( $jy > 979 ) {
		$gy  = 1600;
		$jy -= 979;
	} else {
		$gy  = 621;
	}

	$days = ( 365 * $jy ) + intdiv( $jy, 33 ) * 8 + intdiv( ( $jy % 33 ) + 3, 4 ) + 78 + $jd + ( ( $jm < 7 ) ? ( $jm - 1 ) * 31 : ( ( $jm - 7 ) * 30 ) + 186 );

	$gy  += 400 * intdiv( $days, 146097 );
	$days = $days % 146097;

	if ( $days > 36524 ) {
		$days--;
		$gy  += 100 * intdiv( $days, 36524 );
		$days = $days % 36524;
		if ( $days >= 365 ) {
			$days++;
		}
	}

	$gy  += 4 * intdiv( $days, 1461 );
	$days = $days % 1461;

	if ( $days > 365 ) {
		$gy  += intdiv( $days - 1, 365 );
		$days = ( $days - 1 ) % 365;
	}

	$gd    = $days + 1;
	$leap  = ( ( 0 === $gy % 4 && 0 !== $gy % 100 ) || 0 === $gy % 400 );
	$sal_a = array( 0, 31, $leap ? 29 : 28, 31, 30, 31, 30, 31, 31, 30, 31, 30, 31 );

	for ( $gm = 0; $gm < 13 && $gd > $sal_a[ $gm ]; $gm++ ) {
		$gd -= $sal_a[ $gm ];
	}

	return array( $gy, $gm, $gd );
}

/**
 * آیا بایگانی شمسی فعال است؟ (همان گزینهٔ تاریخ شمسی؛ با افزونهٔ تاریخ شمسی کاری نمی‌کنیم.)
 *
 * @return bool
 */
function mfa_jalali_archive_enabled() {
	return ! function_exists( 'parsidate' ) && ! class_exists( 'wp_parsidate' ) && (bool) get_theme_mod( 'mfa_jalali', true );
}

/**
 * ماه‌های شمسی که نوشته دارند: [ 'YYYYMM' => ['year'=>, 'month'=>, 'count'=>] ] از جدید به قدیم.
 *
 * @return array
 */
function mfa_jalali_months_with_posts() {
	$cached = get_transient( 'mfa_jalali_months' );
	if ( is_array( $cached ) ) {
		return $cached;
	}

	global $wpdb;
	// فقط ردیف‌های «روز + تعداد»؛ حداکثر ۳۶۶ ردیف در سال، حتی برای سایت‌های بزرگ.
	$rows = $wpdb->get_results( "SELECT SUBSTR(post_date, 1, 10) AS d, COUNT(*) AS c FROM {$wpdb->posts} WHERE post_type = 'post' AND post_status = 'publish' AND post_password = '' GROUP BY SUBSTR(post_date, 1, 10) ORDER BY d DESC" ); // phpcs:ignore WordPress.DB

	$months = array();
	foreach ( (array) $rows as $row ) {
		$parts = explode( '-', $row->d );
		if ( 3 !== count( $parts ) || (int) $parts[0] < 1 ) {
			continue;
		}
		list( $jy, $jm ) = mfa_gregorian_to_jalali( (int) $parts[0], (int) $parts[1], (int) $parts[2] );
		$key             = sprintf( '%04d%02d', $jy, $jm );
		if ( ! isset( $months[ $key ] ) ) {
			$months[ $key ] = array(
				'year'  => $jy,
				'month' => $jm,
				'count' => 0,
			);
		}
		$months[ $key ]['count'] += (int) $row->c;
	}

	krsort( $months );
	set_transient( 'mfa_jalali_months', $months, DAY_IN_SECONDS );
	return $months;
}

/**
 * پاک‌کردن کش با تغییر نوشته‌ها.
 */
function mfa_flush_jalali_months() {
	delete_transient( 'mfa_jalali_months' );
}
add_action( 'save_post', 'mfa_flush_jalali_months' );
add_action( 'deleted_post', 'mfa_flush_jalali_months' );
add_action( 'transition_post_status', 'mfa_flush_jalali_months' );

/**
 * نشانی بایگانی یک ماه شمسی.
 *
 * @param int $jy
 * @param int $jm
 * @return string
 */
function mfa_jalali_month_url( $jy, $jm ) {
	return add_query_arg( 'jm', sprintf( '%04d%02d', $jy, $jm ), home_url( '/' ) );
}

/**
 * برچسب یک ماه شمسی، مثل «مهر ۱۴۰۵».
 *
 * @param int $jy
 * @param int $jm
 * @return string
 */
function mfa_jalali_month_label( $jy, $jm ) {
	$months = mfa_jalali_months();
	$label  = $months[ $jm - 1 ] . ' ' . $jy;
	return function_exists( 'mfa_fa_digits_plain' ) && mfa_digits_enabled() ? mfa_fa_digits_plain( $label ) : $label;
}

/**
 * متغیر کوئری jm.
 *
 * @param string[] $vars
 * @return string[]
 */
function mfa_archive_query_vars( $vars ) {
	$vars[] = 'jm';
	return $vars;
}
add_filter( 'query_vars', 'mfa_archive_query_vars' );

/**
 * تبدیل jm=YYYYMM به بازهٔ تاریخ در کوئری اصلی.
 *
 * @param WP_Query $query
 */
function mfa_archive_pre_get_posts( $query ) {
	if ( is_admin() || ! $query->is_main_query() ) {
		return;
	}

	$jm = $query->get( 'jm' );
	if ( ! $jm || ! preg_match( '/^(\d{4})(\d{2})$/', (string) $jm, $m ) ) {
		return;
	}

	$jy = (int) $m[1];
	$mo = (int) $m[2];
	if ( $mo < 1 || $mo > 12 || $jy < 1 ) {
		return;
	}

	list( $y1, $m1, $d1 ) = mfa_jalali_to_gregorian( $jy, $mo, 1 );
	list( $y2, $m2, $d2 ) = ( 12 === $mo ) ? mfa_jalali_to_gregorian( $jy + 1, 1, 1 ) : mfa_jalali_to_gregorian( $jy, $mo + 1, 1 );

	$start = sprintf( '%04d-%02d-%02d 00:00:00', $y1, $m1, $d1 );
	$end   = gmdate( 'Y-m-d 23:59:59', strtotime( sprintf( '%04d-%02d-%02d', $y2, $m2, $d2 ) . ' -1 day UTC' ) );

	$query->set(
		'date_query',
		array(
			array(
				'after'     => $start,
				'before'    => $end,
				'inclusive' => true,
			),
		)
	);
	$query->set( 'post_type', 'post' );
	$query->set( 'ignore_sticky_posts', true );
	$query->is_home    = false;
	$query->is_archive = true;
	$query->is_date    = true;
}
add_action( 'pre_get_posts', 'mfa_archive_pre_get_posts' );

/**
 * عنوان صفحهٔ بایگانی ماه شمسی.
 *
 * @param string $title
 * @return string
 */
function mfa_archive_title( $title ) {
	$jm = get_query_var( 'jm' );
	if ( $jm && preg_match( '/^(\d{4})(\d{2})$/', (string) $jm, $m ) && (int) $m[2] >= 1 && (int) $m[2] <= 12 ) {
		return 'بایگانی ' . mfa_jalali_month_label( (int) $m[1], (int) $m[2] );
	}
	return $title;
}
add_filter( 'get_the_archive_title', 'mfa_archive_title', 5 );

/**
 * ساخت خروجی HTML فهرست یا منوی کشویی.
 *
 * @param bool $count    نمایش تعداد.
 * @param bool $dropdown منوی کشویی به‌جای فهرست.
 * @return string
 */
function mfa_jalali_archive_html( $count = false, $dropdown = false ) {
	$months = mfa_jalali_months_with_posts();
	if ( ! $months ) {
		return '';
	}

	$current = (string) get_query_var( 'jm' );
	$out     = '';

	if ( $dropdown ) {
		$uid  = wp_unique_id( 'mfa-archive-' );
		$out .= '<label class="screen-reader-text" for="' . esc_attr( $uid ) . '">بایگانی</label>';
		$out .= '<select id="' . esc_attr( $uid ) . '" onchange="if(this.value){document.location.href=this.value;}">';
		$out .= '<option value="">انتخاب ماه</option>';
		foreach ( $months as $key => $mo ) {
			$text = mfa_jalali_month_label( $mo['year'], $mo['month'] );
			if ( $count ) {
				$text .= ' (' . ( get_theme_mod( 'mfa_persian_digits', true ) ? mfa_fa_digits_plain( $mo['count'] ) : $mo['count'] ) . ')';
			}
			$out .= '<option value="' . esc_url( mfa_jalali_month_url( $mo['year'], $mo['month'] ) ) . '"' . selected( (string) $key, $current, false ) . '>' . esc_html( $text ) . '</option>';
		}
		return $out . '</select>';
	}

	$out .= '<ul>';
	foreach ( $months as $key => $mo ) {
		$out .= '<li><a href="' . esc_url( mfa_jalali_month_url( $mo['year'], $mo['month'] ) ) . '"' . ( (string) $key === $current ? ' aria-current="page"' : '' ) . '>' . esc_html( mfa_jalali_month_label( $mo['year'], $mo['month'] ) ) . '</a>';
		if ( $count ) {
			$n    = get_theme_mod( 'mfa_persian_digits', true ) ? mfa_fa_digits_plain( $mo['count'] ) : $mo['count'];
			$out .= ' (' . esc_html( $n ) . ')';
		}
		$out .= '</li>';
	}
	return $out . '</ul>';
}

/**
 * ویجت کلاسیک «بایگانی»: خروجی شمسی جایگزین می‌شود.
 *
 * @param array|false $instance
 * @param WP_Widget   $widget
 * @param array       $args
 * @return array|false
 */
function mfa_archive_widget_display( $instance, $widget, $args ) {
	if ( ! $widget instanceof WP_Widget_Archives || ! mfa_jalali_archive_enabled() || ! is_array( $instance ) ) {
		return $instance;
	}

	$html = mfa_jalali_archive_html( ! empty( $instance['count'] ), ! empty( $instance['dropdown'] ) );
	if ( '' === $html ) {
		return $instance;
	}

	$title = ! empty( $instance['title'] ) ? $instance['title'] : 'بایگانی';
	$title = apply_filters( 'widget_title', $title, $instance, $widget->id_base );

	echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	echo $args['before_title'] . esc_html( $title ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput
	echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
	echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput

	return false;
}
add_filter( 'widget_display_callback', 'mfa_archive_widget_display', 10, 3 );

/**
 * بلوک «بایگانی» (ویجت‌های مبتنی بر بلوک).
 *
 * @param string $content
 * @param array  $block
 * @return string
 */
function mfa_archive_block( $content, $block ) {
	if ( ! mfa_jalali_archive_enabled() ) {
		return $content;
	}

	$attrs = isset( $block['attrs'] ) ? $block['attrs'] : array();
	$type  = isset( $attrs['type'] ) ? $attrs['type'] : 'monthly';
	if ( 'monthly' !== $type ) {
		return $content;
	}

	$html = mfa_jalali_archive_html( ! empty( $attrs['showPostCounts'] ), ! empty( $attrs['displayAsDropdown'] ) );
	if ( '' === $html ) {
		return $content;
	}

	return '<div class="wp-block-archives">' . $html . '</div>';
}
add_filter( 'render_block_core/archives', 'mfa_archive_block', 10, 2 );
