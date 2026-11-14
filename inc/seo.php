<?php
/**
 * سئو: داده‌های ساختاریافته (Schema.org JSON-LD)، مسیر راهنما (breadcrumb) و توضیح متا
 *
 * اگر افزونهٔ سئو (Yoast، Rank Math، All in One SEO، SEOPress، The SEO Framework) فعال باشد،
 * JSON-LD و توضیح متا از پوسته ارسال نمی‌شود تا تکراری نشود. مسیر راهنمای قابل‌مشاهده مستقل است.
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * آیا افزونهٔ سئو فعال است؟
 *
 * @return bool
 */
function mfa_seo_plugin_active() {
	return defined( 'WPSEO_VERSION' ) || defined( 'RANK_MATH_VERSION' ) || class_exists( 'RankMath' )
		|| defined( 'AIOSEO_VERSION' ) || defined( 'SEOPRESS_VERSION' ) || defined( 'THE_SEO_FRAMEWORK_VERSION' );
}

/**
 * اجرای یک تابع بدون تبدیل ارقام به فارسی (برای خروجی‌های ماشینی).
 *
 * @param callable $callback
 * @return mixed
 */
function mfa_raw_digits( $callback ) {
	$GLOBALS['mfa_no_digits'] = true;
	$result                   = call_user_func( $callback );
	unset( $GLOBALS['mfa_no_digits'] );
	return $result;
}

/**
 * متن ساده و تمیز از یک رشتهٔ HTML.
 *
 * @param string $html
 * @return string
 */
function mfa_plain( $html ) {
	return trim( preg_replace( '/\s+/u', ' ', html_entity_decode( wp_strip_all_tags( strip_shortcodes( (string) $html ) ), ENT_QUOTES, 'UTF-8' ) ) );
}

/**
 * نشانی صفحهٔ جاری.
 *
 * @return string
 */
function mfa_current_url() {
	if ( is_singular() ) {
		return get_permalink();
	}
	if ( is_category() || is_tag() || is_tax() ) {
		$link = get_term_link( get_queried_object() );
		if ( ! is_wp_error( $link ) ) {
			return $link;
		}
	}
	if ( is_author() ) {
		return get_author_posts_url( (int) get_queried_object_id() );
	}
	return esc_url_raw( home_url( add_query_arg( null, null ) ) );
}

/**
 * زنجیرهٔ مسیر راهنما: آرایه‌ای از [عنوان، نشانی یا null].
 *
 * @return array[]
 */
function mfa_breadcrumb_trail() {
	if ( is_front_page() ) {
		return array();
	}

	$trail   = array( array( 'خانه', home_url( '/' ) ) );
	$page_fp = (int) get_option( 'page_for_posts' );

	if ( is_home() ) {
		$trail[] = array( single_post_title( '', false ), null );
		return $trail;
	}

	$is_posts_context = is_singular( 'post' ) || is_category() || is_tag() || is_date() || is_author() || get_query_var( 'jm' );
	if ( $is_posts_context && $page_fp ) {
		$trail[] = array( get_the_title( $page_fp ), get_permalink( $page_fp ) );
	}

	if ( is_singular( 'post' ) ) {
		$cats = get_the_category();
		if ( $cats ) {
			$cat = $cats[0];
			foreach ( array_reverse( get_ancestors( $cat->term_id, 'category' ) ) as $anc_id ) {
				$anc = get_term( $anc_id, 'category' );
				if ( $anc && ! is_wp_error( $anc ) ) {
					$trail[] = array( $anc->name, get_term_link( $anc ) );
				}
			}
			$trail[] = array( $cat->name, get_term_link( $cat ) );
		}
		$trail[] = array( get_the_title(), null );
	} elseif ( is_page() ) {
		foreach ( array_reverse( get_post_ancestors( get_the_ID() ) ) as $anc_id ) {
			$trail[] = array( get_the_title( $anc_id ), get_permalink( $anc_id ) );
		}
		$trail[] = array( get_the_title(), null );
	} elseif ( is_category() ) {
		$term = get_queried_object();
		foreach ( array_reverse( get_ancestors( $term->term_id, 'category' ) ) as $anc_id ) {
			$anc = get_term( $anc_id, 'category' );
			if ( $anc && ! is_wp_error( $anc ) ) {
				$trail[] = array( $anc->name, get_term_link( $anc ) );
			}
		}
		$trail[] = array( $term->name, null );
	} elseif ( is_tag() ) {
		$trail[] = array( 'برچسب: ' . single_tag_title( '', false ), null );
	} elseif ( is_author() ) {
		$trail[] = array( 'نویسنده: ' . get_queried_object()->display_name, null );
	} elseif ( get_query_var( 'jm' ) || is_date() || is_archive() ) {
		$trail[] = array( wp_strip_all_tags( get_the_archive_title() ), null );
	} elseif ( is_search() ) {
		$trail[] = array( 'نتایج جست‌وجو برای «' . get_search_query() . '»', null );
	} elseif ( is_404() ) {
		$trail[] = array( 'صفحه پیدا نشد', null );
	} elseif ( is_singular() ) {
		$trail[] = array( get_the_title(), null );
	}

	return $trail;
}

/**
 * نمایش مسیر راهنما در قالب‌ها.
 */
function mfa_breadcrumbs() {
	if ( ! get_theme_mod( 'mfa_breadcrumbs', true ) ) {
		return;
	}

	$trail = mfa_breadcrumb_trail();
	if ( count( $trail ) < 2 ) {
		return;
	}

	echo '<nav class="breadcrumbs" aria-label="مسیر راهنما"><ol>';
	$last = count( $trail ) - 1;
	foreach ( $trail as $i => $crumb ) {
		if ( $i === $last || empty( $crumb[1] ) ) {
			echo '<li aria-current="page"><span>' . esc_html( $crumb[0] ) . '</span></li>';
		} else {
			echo '<li><a href="' . esc_url( $crumb[1] ) . '">' . esc_html( $crumb[0] ) . '</a></li>';
		}
	}
	echo '</ol></nav>';
}

/**
 * ساخت گراف JSON-LD برای صفحهٔ جاری.
 *
 * @return array
 */
function mfa_schema_graph() {
	$site  = home_url( '/' );
	$name  = get_bloginfo( 'name' );
	$desc  = get_bloginfo( 'description' );
	$lang  = 'fa-IR';
	$org   = array(
		'@type' => 'Organization',
		'@id'   => $site . '#organization',
		'name'  => $name,
		'url'   => $site,
	);

	$logo_id = (int) get_theme_mod( 'custom_logo' );
	if ( $logo_id ) {
		$logo = wp_get_attachment_image_src( $logo_id, 'full' );
		if ( $logo ) {
			$org['logo'] = array(
				'@type'  => 'ImageObject',
				'url'    => $logo[0],
				'width'  => (int) $logo[1],
				'height' => (int) $logo[2],
			);
		}
	}

	$graph = array(
		$org,
		array(
			'@type'           => 'WebSite',
			'@id'             => $site . '#website',
			'url'             => $site,
			'name'            => $name,
			'description'     => $desc,
			'inLanguage'      => $lang,
			'publisher'       => array( '@id' => $site . '#organization' ),
			'potentialAction' => array(
				'@type'       => 'SearchAction',
				'target'      => array(
					'@type'       => 'EntryPoint',
					'urlTemplate' => $site . '?s={search_term_string}',
				),
				'query-input' => 'required name=search_term_string',
			),
		),
	);

	$url   = mfa_current_url();
	$trail = mfa_breadcrumb_trail();

	$page = array(
		'@type'      => 'WebPage',
		'@id'        => $url . '#webpage',
		'url'        => $url,
		'name'       => wp_get_document_title(),
		'inLanguage' => $lang,
		'isPartOf'   => array( '@id' => $site . '#website' ),
	);

	if ( is_singular( 'post' ) && ! post_password_required() ) {
		$post_id = get_the_ID();
		$content = mfa_plain( get_post_field( 'post_content', $post_id ) );
		$words   = $content ? count( preg_split( '/\s+/u', $content, -1, PREG_SPLIT_NO_EMPTY ) ) : 0;

		$article = array(
			'@type'            => 'BlogPosting',
			'@id'              => $url . '#article',
			'headline'         => mb_substr( mfa_plain( get_the_title() ), 0, 110 ),
			'description'      => mfa_meta_description(),
			'datePublished'    => get_post_time( 'c', false, $post_id ),
			'dateModified'     => get_post_modified_time( 'c', false, $post_id ),
			'inLanguage'       => $lang,
			'wordCount'        => $words,
			'commentCount'     => (int) get_comments_number( $post_id ),
			'mainEntityOfPage' => array( '@id' => $url . '#webpage' ),
			'isPartOf'         => array( '@id' => $url . '#webpage' ),
			'publisher'        => array( '@id' => $site . '#organization' ),
			'author'           => array(
				'@type' => 'Person',
				'name'  => get_the_author_meta( 'display_name', (int) get_post_field( 'post_author', $post_id ) ),
				'url'   => get_author_posts_url( (int) get_post_field( 'post_author', $post_id ) ),
			),
		);

		if ( has_post_thumbnail( $post_id ) ) {
			$img = wp_get_attachment_image_src( get_post_thumbnail_id( $post_id ), 'full' );
			if ( $img ) {
				$article['image'] = array(
					'@type'  => 'ImageObject',
					'url'    => $img[0],
					'width'  => (int) $img[1],
					'height' => (int) $img[2],
				);
			}
		}

		$cats = get_the_category( $post_id );
		if ( $cats ) {
			$article['articleSection'] = $cats[0]->name;
		}
		$tags = get_the_tags( $post_id );
		if ( $tags ) {
			$article['keywords'] = implode( '، ', wp_list_pluck( $tags, 'name' ) );
		}

		$page['@type'] = 'WebPage';
		$graph[]       = $page;
		$graph[]       = $article;
	} else {
		if ( is_search() ) {
			$page['@type'] = 'SearchResultsPage';
		} elseif ( is_archive() || is_home() || get_query_var( 'jm' ) ) {
			$page['@type'] = 'CollectionPage';
		}
		$graph[] = $page;
	}

	if ( count( $trail ) >= 2 ) {
		$items = array();
		foreach ( $trail as $i => $crumb ) {
			$item = array(
				'@type'    => 'ListItem',
				'position' => $i + 1,
				'name'     => mfa_plain( $crumb[0] ),
			);
			$item['item'] = ! empty( $crumb[1] ) ? $crumb[1] : $url;
			$items[]      = $item;
		}
		$graph[] = array(
			'@type'           => 'BreadcrumbList',
			'@id'             => $url . '#breadcrumb',
			'itemListElement' => $items,
		);
		// صفحه را به مسیر راهنما وصل می‌کنیم.
		foreach ( $graph as $k => $node ) {
			if ( isset( $node['@id'] ) && $node['@id'] === $url . '#webpage' ) {
				$graph[ $k ]['breadcrumb'] = array( '@id' => $url . '#breadcrumb' );
			}
		}
	}

	return $graph;
}

/**
 * چاپ JSON-LD در head.
 */
function mfa_print_schema() {
	if ( is_admin() || is_feed() || is_robots() || is_embed() || is_404() || ! get_theme_mod( 'mfa_schema', true ) || mfa_seo_plugin_active() ) {
		return;
	}

	$data = mfa_raw_digits(
		static function () {
			return array(
				'@context' => 'https://schema.org',
				'@graph'   => mfa_schema_graph(),
			);
		}
	);

	$json = wp_json_encode( $data, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_HEX_TAG );
	if ( $json ) {
		echo '<script type="application/ld+json">' . $json . "</script>\n"; // phpcs:ignore WordPress.Security.EscapeOutput
	}
}
add_action( 'wp_head', 'mfa_print_schema', 5 );

/**
 * توضیح متا برای صفحهٔ جاری (حداکثر ۱۶۰ نویسه).
 *
 * @return string
 */
function mfa_meta_description() {
	$text = '';

	if ( is_singular() && ! post_password_required() ) {
		$post = get_post();
		$text = $post && has_excerpt( $post ) ? mfa_plain( $post->post_excerpt ) : mfa_plain( $post ? $post->post_content : '' );
	} elseif ( is_front_page() || is_home() ) {
		$text = get_bloginfo( 'description' );
	} elseif ( is_category() || is_tag() || is_tax() ) {
		$text = mfa_plain( term_description() );
	}

	if ( '' === $text ) {
		return '';
	}

	return mb_strlen( $text ) > 160 ? mb_substr( $text, 0, 157 ) . '…' : $text;
}

/**
 * چاپ meta description (فقط وقتی افزونهٔ سئو فعال نیست).
 */
function mfa_print_meta_description() {
	if ( is_admin() || is_feed() || ! get_theme_mod( 'mfa_meta_desc', true ) || mfa_seo_plugin_active() ) {
		return;
	}

	$desc = mfa_raw_digits( 'mfa_meta_description' );
	if ( $desc ) {
		echo '<meta name="description" content="' . esc_attr( $desc ) . '">' . "\n";
	}
}
add_action( 'wp_head', 'mfa_print_meta_description', 5 );

/**
 * تنظیمات سفارشی‌سازی: سئو و سرعت.
 *
 * @param WP_Customize_Manager $wp_customize
 */
function mfa_seo_customize( $wp_customize ) {
	$wp_customize->add_section(
		'mfa_seo',
		array(
			'title'       => 'سئو و سرعت',
			'description' => 'اگر افزونهٔ سئو (مثل Yoast یا Rank Math) فعال باشد، داده‌های ساختاریافته و توضیح متا از پوسته ارسال نمی‌شود.',
			'priority'    => 32,
		)
	);

	$flags = array(
		'mfa_breadcrumbs'   => 'نمایش مسیر راهنما (breadcrumb) بالای محتوا',
		'mfa_schema'        => 'داده‌های ساختاریافته Schema.org (JSON-LD)',
		'mfa_meta_desc'     => 'افزودن meta description خودکار',
		'mfa_head_cleanup'  => 'حذف خروجی‌های غیرضروری head (ایموجی، ژنراتور و ...)',
	);
	foreach ( $flags as $id => $label ) {
		$wp_customize->add_setting(
			$id,
			array(
				'default'           => true,
				'sanitize_callback' => static function ( $v ) {
					return (bool) $v;
				},
			)
		);
		$wp_customize->add_control(
			$id,
			array(
				'label'   => $label,
				'section' => 'mfa_seo',
				'type'    => 'checkbox',
			)
		);
	}

	// جلوگیری از پرش متن هنگام بارگذاری فونت.
	$wp_customize->add_setting(
		'mfa_font_optional',
		array(
			'default'           => true,
			'sanitize_callback' => static function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp_customize->add_control(
		'mfa_font_optional',
		array(
			'label'       => 'جلوگیری از پرش متن هنگام بارگذاری فونت (بهبود CLS)',
			'description' => 'اگر فونت وزیرمتن در لحظهٔ اول نرسد (اینترنت کند یا بازدید اول)، صفحهٔ همان بازدید با فونت سیستم نمایش داده می‌شود و از بازدید بعد با وزیرمتن. خاموش‌کردنش فونت را همیشه جایگزین می‌کند ولی ممکن است متن لحظه‌ای جابه‌جا شود.',
			'section'     => 'mfa_seo',
			'type'        => 'checkbox',
		)
	);

	// محدودکردن نسخه‌های تصویر.
	$wp_customize->add_setting(
		'mfa_limit_images',
		array(
			'default'           => true,
			'sanitize_callback' => static function ( $v ) {
				return (bool) $v;
			},
		)
	);
	$wp_customize->add_control(
		'mfa_limit_images',
		array(
			'label'       => 'ساخت فقط نسخه‌های لازم برای تصاویر تازه‌آپلودشده',
			'description' => 'فقط تصویر اصلی و بندانگشتی ۱۵۰×۱۵۰ ساخته می‌شود و از پر شدن فضای هاست جلوگیری می‌کند. اگر از بلوک گالری یا اندازه‌های متوسط/بزرگ استفاده می‌کنید، خاموشش کنید. روی تصاویر قبلی اثر ندارد (برای آن‌ها از افزونهٔ Regenerate Thumbnails استفاده کنید).',
			'section'     => 'mfa_seo',
			'type'        => 'checkbox',
		)
	);
}
add_action( 'customize_register', 'mfa_seo_customize' );
