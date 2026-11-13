<?php
/**
 * فرم جست‌وجو
 *
 * @package mystique-fa
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$mfa_uid = wp_unique_id( 'search-form-' );
?>
<form role="search" method="get" class="search-form" action="<?php echo esc_url( home_url( '/' ) ); ?>">
	<label for="<?php echo esc_attr( $mfa_uid ); ?>" class="screen-reader-text">جست‌وجو برای:</label>
	<input type="search" id="<?php echo esc_attr( $mfa_uid ); ?>" class="search-field" placeholder="جست‌وجو…" value="<?php echo esc_attr( get_search_query() ); ?>" name="s">
	<button type="submit" class="search-submit">بگرد</button>
</form>
