<?php
/**
 * پایین سایت
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
	</div><!-- #main -->

	<footer id="footer" class="site-footer">
		<div class="wrap">
			<?php if ( is_active_sidebar( 'footer-1' ) ) : ?>
				<div class="footer-widgets">
					<?php dynamic_sidebar( 'footer-1' ); ?>
				</div>
			<?php endif; ?>

			<?php if ( has_nav_menu( 'footer' ) ) : ?>
				<nav class="nav-footer" aria-label="منوی پایین سایت">
					<?php
					wp_nav_menu(
						array(
							'theme_location' => 'footer',
							'menu_class'     => 'menu',
							'container'      => false,
							'depth'          => 1,
						)
					);
					?>
				</nav>
			<?php endif; ?>

			<?php mfa_social_icons( 'footer' ); ?>

			<div id="copyright">
				<?php
				$mfa_footer = get_theme_mod( 'mfa_footer_text', '' );
				if ( $mfa_footer ) {
					echo wp_kses_post( wpautop( $mfa_footer ) );
				} else {
					printf(
						'<p>© %1$s <a href="%2$s">%3$s</a>. تمامی حقوق محفوظ است.</p>',
						esc_html( wp_date( 'Y' ) ),
						esc_url( home_url( '/' ) ),
						esc_html( get_bloginfo( 'name' ) )
					);
				}
				?>
			</div>
		</div>
	</footer>

	<a class="go-top" href="#page" aria-label="بازگشت به بالا">↑</a>
</div><!-- #page -->

<?php wp_footer(); ?>
</body>
</html>
