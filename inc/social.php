<?php
/**
 * آیکن‌های شبکه‌های اجتماعی
 *
 * آدرس‌ها از «سفارشی‌سازی > شبکه‌های اجتماعی» وارد می‌شوند و آیکن‌ها در سربرگ، پایین سایت
 * و (با ویجت «آیکن‌های شبکه‌های اجتماعی») هر جای دیگر نمایش داده می‌شوند.
 * آیکن‌ها SVG داخلی‌اند؛ بدون درخواست به سرویس خارجی.
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * فهرست شبکه‌ها: کلید => [برچسب، رنگ برند، راهنمای ورودی، مسیر SVG].
 * مسیرها در بوم ۲۴×۲۴ و با fill=currentColor کشیده شده‌اند.
 *
 * @return array<string,array{label:string,color:string,hint:string,svg:string}>
 */
function mfa_social_networks() {
	return array(
		'telegram'  => array(
			'label' => 'تلگرام',
			'color' => '#27a7e7',
			'hint'  => 'نشانی کانال یا @نام‌کاربری',
			'svg'   => '<path d="M21.6 3.3 2.9 10.5c-.9.4-.9 1.1-.2 1.3l4.8 1.5 1.8 5.6c.2.6.4.8.9.8.4 0 .6-.2.9-.5l2.3-2.2 4.8 3.5c.9.5 1.5.2 1.7-.8L22.9 4.5c.3-1.2-.4-1.8-1.3-1.2zM9 13.2l9.6-6c.5-.3.9-.1.5.2l-7.9 7.1-.3 3.4L9 13.2z"/>',
		),
		'instagram' => array(
			'label' => 'اینستاگرام',
			'color' => '#e1306c',
			'hint'  => 'نشانی صفحه یا @نام‌کاربری',
			'svg'   => '<path d="M7.5 2h9A5.5 5.5 0 0 1 22 7.5v9a5.5 5.5 0 0 1-5.5 5.5h-9A5.5 5.5 0 0 1 2 16.5v-9A5.5 5.5 0 0 1 7.5 2zm0 2A3.5 3.5 0 0 0 4 7.5v9A3.5 3.5 0 0 0 7.5 20h9a3.5 3.5 0 0 0 3.5-3.5v-9A3.5 3.5 0 0 0 16.5 4h-9zM12 7a5 5 0 1 1 0 10 5 5 0 0 1 0-10zm0 2a3 3 0 1 0 0 6 3 3 0 0 0 0-6zm5.3-3.3a1.2 1.2 0 1 1 0 2.4 1.2 1.2 0 0 1 0-2.4z"/>',
		),
		'whatsapp'  => array(
			'label' => 'واتس‌اپ',
			'color' => '#25d366',
			'hint'  => 'شمارهٔ کامل با کد کشور، مثل 989121234567',
			'svg'   => '<path d="M12 2a10 10 0 0 0-8.6 15.1L2 22l5-1.3A10 10 0 1 0 12 2zm0 2a8 8 0 1 1-4.2 14.8l-.4-.2-2.9.8.8-2.8-.3-.4A8 8 0 0 1 12 4zM8.6 7.5c-.2 0-.5.1-.7.4-.3.3-.9.9-.9 2.1s.9 2.5 1 2.6c.1.2 1.8 2.9 4.5 3.9 2.2.9 2.7.7 3.1.6.5-.1 1.5-.6 1.7-1.2.2-.6.2-1.1.2-1.2-.1-.1-.2-.2-.5-.3l-1.6-.8c-.2-.1-.4-.1-.6.1l-.8 1c-.1.2-.3.2-.5.1-.8-.4-1.7-.9-2.5-2-.2-.3-.4-.6-.5-.8-.1-.2 0-.3.1-.5l.4-.5c.1-.1.2-.3.2-.5 0-.2 0-.3-.1-.4l-.7-1.7c-.2-.4-.3-.4-.5-.4z"/>',
		),
		'eitaa'     => array(
			'label' => 'ایتا',
			'color' => '#f26522',
			'hint'  => 'نشانی کانال ایتا',
			'svg'   => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16z"/><text x="12" y="15.2" font-size="8.5" font-weight="700" text-anchor="middle" fill="currentColor" style="font-family:inherit">ایتا</text>',
		),
		'rubika'    => array(
			'label' => 'روبیکا',
			'color' => '#a43bd4',
			'hint'  => 'نشانی کانال روبیکا',
			'svg'   => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-3 4v8h2v-3h1.2l1.8 3h2.4l-2.1-3.3A2.9 2.9 0 0 0 15 10.8C15 9.200 13.800 8 12.200 8H9zm2 2h1.200c.5 0 .8.3.8.8s-.3.8-.8.8H11V10z"/>',
		),
		'aparat'    => array(
			'label' => 'آپارات',
			'color' => '#ed145b',
			'hint'  => 'نشانی کانال آپارات',
			'svg'   => '<path d="M12 2a10 10 0 1 0 0 20 10 10 0 0 0 0-20zm0 2a8 8 0 1 1 0 16 8 8 0 0 1 0-16zm-1.500 4.500v7l6-3.500-6-3.500z"/>',
		),
		'youtube'   => array(
			'label' => 'یوتیوب',
			'color' => '#ff0000',
			'hint'  => 'نشانی کانال',
			'svg'   => '<path d="M21.600 7.200a2.500 2.500 0 0 0-1.800-1.800C18.200 5 12 5 12 5s-6.200 0-7.800.4A2.500 2.500 0 0 0 2.400 7.200C2 8.800 2 12 2 12s0 3.200.4 4.800a2.500 2.500 0 0 0 1.800 1.800C5.800 19 12 19 12 19s6.200 0 7.800-.4a2.500 2.500 0 0 0 1.800-1.800c.4-1.600.4-4.800.4-4.800s0-3.200-.4-4.800zM10 15.200V8.800l5.500 3.200-5.500 3.200z"/>',
		),
		'x'         => array(
			'label' => 'ایکس (توییتر)',
			'color' => '#000000',
			'hint'  => 'نشانی صفحه یا @نام‌کاربری',
			'svg'   => '<path d="M17.800 3h3.100l-6.800 7.700L22 21h-6.200l-4.900-6.300L5.300 21H2.200l7.300-8.300L2 3h6.400l4.400 5.800L17.800 3zm-1.100 16.200h1.700L7.400 4.700H5.600l11.100 14.500z"/>',
		),
		'linkedin'  => array(
			'label' => 'لینکدین',
			'color' => '#0a66c2',
			'hint'  => 'نشانی پروفایل',
			'svg'   => '<path d="M4.500 2a2.500 2.500 0 1 1 0 5 2.500 2.500 0 0 1 0-5zM2.500 9h4v13h-4V9zm7 0h3.800v1.800c.6-1 1.900-2.100 3.900-2.100 4 0 4.800 2.600 4.800 6V22h-4v-6.300c0-1.500 0-3.400-2.100-3.400s-2.400 1.600-2.400 3.300V22h-4V9z"/>',
		),
		'github'    => array(
			'label' => 'گیت‌هاب',
			'color' => '#333333',
			'hint'  => 'نشانی پروفایل',
			'svg'   => '<path d="M12 2a10 10 0 0 0-3.200 19.500c.5.100.7-.2.7-.5v-1.800c-2.800.6-3.400-1.200-3.400-1.200-.5-1.200-1.100-1.500-1.100-1.500-.9-.6.100-.6.100-.6 1 .1 1.500 1 1.500 1 .9 1.500 2.300 1.100 2.900.8.1-.7.4-1.100.6-1.300-2.200-.3-4.600-1.100-4.600-5 0-1.100.4-2 1-2.700-.1-.3-.4-1.300.1-2.700 0 0 .8-.3 2.700 1a9.300 9.300 0 0 1 5 0c1.900-1.300 2.700-1 2.700-1 .5 1.400.2 2.400.1 2.700.6.700 1 1.600 1 2.700 0 3.900-2.400 4.700-4.600 5 .4.300.7.900.7 1.800v2.700c0 .3.200.6.700.5A10 10 0 0 0 12 2z"/>',
		),
		'email'     => array(
			'label' => 'ایمیل',
			'color' => '#6b6b6b',
			'hint'  => 'نشانی ایمیل',
			'svg'   => '<path d="M3 5h18a1 1 0 0 1 1 1v12a1 1 0 0 1-1 1H3a1 1 0 0 1-1-1V6a1 1 0 0 1 1-1zm1.500 2 7.500 5.500L19.500 7h-15zM4 9.300V17h16V9.300l-8 5.800-8-5.800z"/>',
		),
	);
}

/**
 * تبدیل ورودی کاربر به نشانی کامل.
 *
 * @param string $key   کلید شبکه.
 * @param string $value مقدار واردشده.
 * @return string نشانی امن یا رشتهٔ خالی.
 */
function mfa_social_url( $key, $value ) {
	$value = trim( (string) $value );
	if ( '' === $value ) {
		return '';
	}

	if ( 'email' === $key ) {
		$mail = sanitize_email( preg_replace( '/^mailto:/i', '', $value ) );
		return $mail ? 'mailto:' . $mail : '';
	}

	$handle = ltrim( $value, '@' );
	if ( ! preg_match( '#^https?://#i', $value ) ) {
		switch ( $key ) {
			case 'telegram':
				return esc_url_raw( 'https://t.me/' . $handle );
			case 'instagram':
				return esc_url_raw( 'https://instagram.com/' . $handle );
			case 'x':
				return esc_url_raw( 'https://x.com/' . $handle );
			case 'whatsapp':
				$digits = preg_replace( '/\D+/', '', $value );
				return $digits ? esc_url_raw( 'https://wa.me/' . $digits ) : '';
			default:
				return esc_url_raw( 'https://' . $value );
		}
	}

	return esc_url_raw( $value, array( 'http', 'https' ) );
}

/**
 * آیا حداقل یک شبکه وارد شده است؟
 *
 * @return bool
 */
function mfa_has_social() {
	foreach ( array_keys( mfa_social_networks() ) as $key ) {
		if ( mfa_social_url( $key, get_theme_mod( 'mfa_social_' . $key, '' ) ) ) {
			return true;
		}
	}
	return (bool) get_theme_mod( 'mfa_social_rss', false );
}

/**
 * خروجی HTML آیکن‌ها.
 *
 * @param string $extra_class کلاس اضافی.
 * @return string
 */
function mfa_social_icons_html( $extra_class = '' ) {
	if ( ! mfa_has_social() ) {
		return '';
	}

	$items = '';
	foreach ( mfa_social_networks() as $key => $net ) {
		$url = mfa_social_url( $key, get_theme_mod( 'mfa_social_' . $key, '' ) );
		if ( ! $url ) {
			continue;
		}
		$items .= sprintf(
			'<li><a class="social-%1$s" href="%2$s" style="--brand:%3$s" rel="noopener me" target="_blank" aria-label="%4$s" title="%4$s"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true" focusable="false">%5$s</svg></a></li>',
			esc_attr( $key ),
			esc_url( $url ),
			esc_attr( $net['color'] ),
			esc_attr( $net['label'] ),
			$net['svg'] // مسیرهای SVG ثابت و داخلی پوسته‌اند.
		);
	}

	if ( get_theme_mod( 'mfa_social_rss', false ) ) {
		$items .= sprintf(
			'<li><a class="social-rss" href="%1$s" style="--brand:#f58220" aria-label="خوراک RSS" title="خوراک RSS"><svg viewBox="0 0 24 24" width="20" height="20" fill="currentColor" aria-hidden="true" focusable="false"><circle cx="5.500" cy="18.500" r="2.500"/><path d="M3 10v3a8 8 0 0 1 8 8h3A11 11 0 0 0 3 10zm0-6v3a14 14 0 0 1 14 14h3A17 17 0 0 0 3 4z"/></svg></a></li>',
			esc_url( get_bloginfo( 'rss2_url' ) )
		);
	}

	return '<ul class="social-icons ' . esc_attr( $extra_class ) . '">' . $items . '</ul>'; // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * نمایش آیکن‌ها در یک جایگاه (header یا footer).
 *
 * @param string $place header|footer
 */
function mfa_social_icons( $place ) {
	if ( ! get_theme_mod( 'mfa_social_in_' . $place, true ) ) {
		return;
	}
	echo mfa_social_icons_html( 'social-' . $place ); // phpcs:ignore WordPress.Security.EscapeOutput
}

/**
 * ویجت «آیکن‌های شبکه‌های اجتماعی».
 */
class MFA_Social_Widget extends WP_Widget {

	public function __construct() {
		parent::__construct(
			'mfa_social',
			'آیکن‌های شبکه‌های اجتماعی',
			array( 'description' => 'آیکن‌هایی که در «سفارشی‌سازی > شبکه‌های اجتماعی» وارد کرده‌اید.' )
		);
	}

	public function widget( $args, $instance ) {
		$html = mfa_social_icons_html( 'social-widget' );
		if ( '' === $html ) {
			return;
		}
		echo $args['before_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
		if ( ! empty( $instance['title'] ) ) {
			echo $args['before_title'] . esc_html( apply_filters( 'widget_title', $instance['title'], $instance, $this->id_base ) ) . $args['after_title']; // phpcs:ignore WordPress.Security.EscapeOutput
		}
		echo $html; // phpcs:ignore WordPress.Security.EscapeOutput
		echo $args['after_widget']; // phpcs:ignore WordPress.Security.EscapeOutput
	}

	public function form( $instance ) {
		$title = isset( $instance['title'] ) ? $instance['title'] : 'ما را دنبال کنید';
		?>
		<p>
			<label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>">عنوان:</label>
			<input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>">
		</p>
		<p class="description">آدرس شبکه‌ها را از «نمایش > سفارشی‌سازی > شبکه‌های اجتماعی» وارد کنید.</p>
		<?php
	}

	public function update( $new_instance, $old_instance ) {
		return array( 'title' => sanitize_text_field( $new_instance['title'] ) );
	}
}

add_action(
	'widgets_init',
	static function () {
		register_widget( 'MFA_Social_Widget' );
	}
);

/**
 * تنظیمات سفارشی‌سازی.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function mfa_social_customize( $wp_customize ) {
	$wp_customize->add_section(
		'mfa_social',
		array(
			'title'       => 'شبکه‌های اجتماعی',
			'description' => 'نشانی کامل صفحه را وارد کنید (برای تلگرام، اینستاگرام و ایکس نوشتن @نام‌کاربری هم کافی است). فیلد خالی = آیکن نمایش داده نمی‌شود.',
			'priority'    => 31,
		)
	);

	foreach ( mfa_social_networks() as $key => $net ) {
		$wp_customize->add_setting(
			'mfa_social_' . $key,
			array(
				'default'           => '',
				'sanitize_callback' => static function ( $v ) use ( $key ) {
					$v = trim( (string) $v );
					// مقدار خام را نگه می‌داریم (برای ویرایش بعدی) ولی فقط اگر معتبر باشد.
					return mfa_social_url( $key, $v ) ? sanitize_text_field( $v ) : '';
				},
			)
		);
		$wp_customize->add_control(
			'mfa_social_' . $key,
			array(
				'label'       => $net['label'],
				'description' => $net['hint'],
				'section'     => 'mfa_social',
				'type'        => 'text',
				'input_attrs' => array( 'dir' => 'ltr' ),
			)
		);
	}

	$flags = array(
		'mfa_social_rss'       => array( 'نمایش آیکن خوراک RSS', false ),
		'mfa_social_in_header' => array( 'نمایش آیکن‌ها در سربرگ', true ),
		'mfa_social_in_footer' => array( 'نمایش آیکن‌ها در پایین سایت', true ),
	);
	foreach ( $flags as $id => $data ) {
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
				'section' => 'mfa_social',
				'type'    => 'checkbox',
			)
		);
	}
}
add_action( 'customize_register', 'mfa_social_customize' );
