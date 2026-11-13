<?php
/**
 * توابع کمکی قالب‌ها
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * لوگو یا عنوان سایت.
 */
function mfa_logo() {
	if ( has_custom_logo() ) {
		the_custom_logo();
		return;
	}

	$tag = ( is_front_page() && is_home() ) ? 'h1' : 'p';
	printf(
		'<%1$s id="logo"><a href="%2$s" rel="home">%3$s</a></%1$s>',
		esc_html( $tag ),
		esc_url( home_url( '/' ) ),
		esc_html( get_bloginfo( 'name' ) )
	);
}

/**
 * منوی جایگزین وقتی منویی انتخاب نشده است.
 */
function mfa_menu_fallback() {
	wp_page_menu(
		array(
			'menu_class' => 'menu-wrap',
			'show_home'  => 'صفحهٔ اصلی',
			'before'     => '<ul id="primary-menu" class="menu">',
			'after'      => '</ul>',
		)
	);
}

/**
 * نشان تاریخ (قرص سیاه کنار عنوان، مثل Mystique).
 */
function mfa_post_date() {
	printf(
		'<a class="post-date" href="%1$s"><time class="entry-date published" datetime="%2$s">%3$s</time></a>',
		esc_url( get_permalink() ),
		esc_attr( get_the_date( 'c' ) ),
		esc_html( get_the_date() )
	);
}

/**
 * خط اطلاعات نوشته: نویسنده و دسته‌ها.
 */
function mfa_post_info() {
	$parts = array();

	if ( get_theme_mod( 'mfa_show_author', true ) ) {
		$parts[] = sprintf(
			'نویسنده: <a href="%1$s" class="author">%2$s</a>',
			esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),
			esc_html( get_the_author() )
		);
	}

	if ( get_theme_mod( 'mfa_show_cats', true ) && 'post' === get_post_type() ) {
		$cats = get_the_category_list( '، ' );
		if ( $cats ) {
			$parts[] = 'در ' . $cats;
		}
	}

	if ( $parts ) {
		echo '<span class="post-info">' . implode( ' · ', $parts ) . '</span>'; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}

/**
 * نشانگر تعداد دیدگاه‌ها.
 */
function mfa_comment_count_badge() {
	if ( ! comments_open() && 0 === (int) get_comments_number() ) {
		return;
	}
	printf(
		'<a class="comments-badge" href="%1$s" title="دیدگاه‌ها">%2$s</a>',
		esc_url( get_comments_link() ),
		esc_html( number_format_i18n( get_comments_number() ) )
	);
}

/**
 * صفحه‌بندی فهرست‌ها.
 */
function mfa_pagination() {
	the_posts_pagination(
		array(
			'mid_size'  => 2,
			'prev_text' => 'قبلی',
			'next_text' => 'بعدی',
		)
	);
}

/**
 * نمایش یک دیدگاه (callback برای wp_list_comments).
 *
 * @param WP_Comment $comment
 * @param array      $args
 * @param int        $depth
 */
function mfa_comment( $comment, $args, $depth ) {
	$tag = ( 'div' === $args['style'] ) ? 'div' : 'li';
	?>
	<<?php echo $tag; // phpcs:ignore WordPress.Security.EscapeOutput ?> id="comment-<?php comment_ID(); ?>" <?php comment_class( empty( $args['has_children'] ) ? '' : 'parent' ); ?>>
		<article id="div-comment-<?php comment_ID(); ?>" class="comment-body">
			<header class="comment-head">
				<?php if ( 0 !== (int) $args['avatar_size'] ) : ?>
					<div class="avatar"><?php echo get_avatar( $comment, $args['avatar_size'] ); ?></div>
				<?php endif; ?>
				<span class="comment-author vcard"><?php echo get_comment_author_link( $comment ); ?></span>
				<a class="comment-date" href="<?php echo esc_url( get_comment_link( $comment, $args ) ); ?>">
					<time datetime="<?php comment_time( 'c' ); ?>"><?php echo esc_html( get_comment_date( '', $comment ) . ' – ' . get_comment_time() ); ?></time>
				</a>
			</header>

			<div class="comment-content">
				<?php if ( '0' === $comment->comment_approved ) : ?>
					<p class="comment-awaiting-moderation">دیدگاه شما پس از تأیید مدیر نمایش داده می‌شود.</p>
				<?php endif; ?>
				<?php comment_text(); ?>
			</div>

			<footer class="comment-actions">
				<?php
				comment_reply_link(
					array_merge(
						$args,
						array(
							'add_below'  => 'div-comment',
							'depth'      => $depth,
							'max_depth'  => $args['max_depth'],
							'reply_text' => 'پاسخ',
							'before'     => '',
							'after'      => '',
						)
					)
				);
				edit_comment_link( 'ویرایش', ' · ' );
				?>
			</footer>
		</article>
	<?php
	// تگ بسته را وردپرس خودش اضافه می‌کند.
}

/**
 * اسکریپت کوچک و درون‌خطی در head: حالت ذخیره‌شده را پیش از رسم صفحه اعمال می‌کند (بدون پرش رنگ).
 */
function mfa_theme_init_script() {
	$default = get_theme_mod( 'mfa_dark_default', 'light' );
	if ( ! in_array( $default, array( 'light', 'dark', 'auto' ), true ) ) {
		$default = 'light';
	}
	?>
	<script>
	( function () {
		var t = null;
		try { t = localStorage.getItem( 'mfa-theme' ); } catch ( e ) {}
		if ( 'dark' !== t && 'light' !== t ) {
			t = '<?php echo esc_js( $default ); ?>';
			if ( 'auto' === t ) {
				t = ( window.matchMedia && window.matchMedia( '(prefers-color-scheme: dark)' ).matches ) ? 'dark' : 'light';
			}
		}
		document.documentElement.setAttribute( 'data-theme', t );
	}() );
	</script>
	<?php
}
add_action( 'wp_head', 'mfa_theme_init_script', 0 );

/**
 * کلید حالت روشن/تاریک.
 */
function mfa_theme_toggle() {
	if ( ! get_theme_mod( 'mfa_dark_toggle', true ) ) {
		return;
	}
	?>
	<button type="button" class="theme-toggle" aria-pressed="false" aria-label="تغییر به حالت تاریک" title="حالت روشن/تاریک">
		<svg class="icon-moon" viewBox="0 0 24 24" width="17" height="17" fill="currentColor" aria-hidden="true" focusable="false"><path d="M20.5 14.6A8.500 8.500 0 0 1 9.400 3.500a8.500 8.500 0 1 0 11.100 11.100z"/></svg>
		<svg class="icon-sun" viewBox="0 0 24 24" width="17" height="17" fill="currentColor" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4.500"/><path d="M12 1.500v3M12 19.500v3M1.500 12h3M19.500 12h3M4.600 4.600l2.100 2.100M17.300 17.300l2.100 2.100M4.600 19.400l2.100-2.100M17.300 6.700l2.100-2.100" stroke="currentColor" stroke-width="2" stroke-linecap="round" fill="none"/></svg>
	</button>
	<?php
}
