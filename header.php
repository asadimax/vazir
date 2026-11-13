<?php
/**
 * سربرگ: بخش head، لوگو و منوی اصلی
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<!DOCTYPE html>
<html lang="fa-IR" dir="rtl">
<head>
	<meta charset="<?php bloginfo( 'charset' ); ?>">
	<meta name="viewport" content="width=device-width, initial-scale=1">
	<link rel="profile" href="https://gmpg.org/xfn/11">
	<noscript><style>.search-btn{display:none!important}@media (max-width:720px){.nav-main{position:static;transform:none;visibility:visible;width:auto;box-shadow:none}.menu-btn,.nav-close{display:none!important}.site-header{position:static}}</style></noscript>
	<?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<?php wp_body_open(); ?>
<a class="skip-link screen-reader-text" href="#content">پرش به محتوا</a>

<div id="page" class="site">
	<header id="header" class="site-header">
		<div class="wrap">
			<div class="site-title clear">
				<button type="button" class="menu-btn mobile-only" aria-expanded="false" aria-controls="primary-nav" aria-label="باز کردن منو">
					<span class="nav-toggle-bars" aria-hidden="true"></span>
				</button>

				<?php mfa_logo(); ?>

				<?php if ( get_bloginfo( 'description' ) ) : ?>
					<p class="headline"><?php bloginfo( 'description' ); ?></p>
				<?php endif; ?>

				<div class="header-tools">
					<?php mfa_social_icons( 'header' ); ?>
					<?php mfa_theme_toggle(); ?>
					<button type="button" class="search-btn" aria-expanded="false" aria-controls="mobile-search" aria-label="جست‌وجو">
						<svg viewBox="0 0 24 24" width="18" height="18" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round" aria-hidden="true" focusable="false"><circle cx="10.5" cy="10.5" r="6.5"/><path d="M15.500 15.500 21 21"/></svg>
					</button>
				</div>
			</div>

			<div id="mobile-search" class="mobile-search" hidden>
				<button type="button" class="search-close" aria-label="بستن جست‌وجو"><svg viewBox="0 0 24 24" width="22" height="22" fill="none" stroke="currentColor" stroke-width="2.4" stroke-linecap="round" aria-hidden="true" focusable="false"><path d="M5 5l14 14M19 5L5 19"/></svg></button>
				<?php get_search_form(); ?>
			</div>
		</div>

		<div class="nav-overlay" aria-hidden="true"></div>

		<div class="wrap">
			<nav id="primary-nav" class="nav-main" aria-label="منوی اصلی">
				<div class="drawer-head mobile-only">
					<span class="drawer-title">منو</span>
					<button type="button" class="nav-close" aria-label="بستن منو"><span aria-hidden="true">&times;</span></button>
				</div>
				<?php
				wp_nav_menu(
					array(
						'theme_location' => 'primary',
						'menu_id'        => 'primary-menu',
						'menu_class'     => 'menu',
						'container'      => false,
						'fallback_cb'    => 'mfa_menu_fallback',
					)
				);
				?>
				<?php if ( mfa_has_social() ) : ?>
					<div class="drawer-foot mobile-only">
						<?php echo mfa_social_icons_html( 'social-drawer' ); // phpcs:ignore WordPress.Security.EscapeOutput ?>
					</div>
				<?php endif; ?>
			</nav>
		</div>
	</header>

	<div id="main" class="wrap">
