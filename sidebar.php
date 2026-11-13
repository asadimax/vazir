<?php
/**
 * سایدبار
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! mfa_has_sidebar() ) {
	return;
}
?>
<aside id="sidebar" class="sidebar" aria-label="ستون کناری">
	<?php dynamic_sidebar( 'sidebar-1' ); ?>
</aside>
