<?php
// Add custom Theme Functions here




//Remove WooCommerce Tabs - this code removes all 3 tabs - to be more specific just remove actual unset lines
add_filter( 'woocommerce_product_tabs', 'woo_remove_product_tabs', 98 );
function woo_remove_product_tabs( $tabs ) {
 unset( $tabs['description'] );      	// Remove the description tab
 return $tabs;
}

/**
 * Apply a different tax rate based on the user role.
 */
function wc_diff_rate_for_user( $tax_class, $product ) {
	if ( is_user_logged_in() && current_user_can( 'new_role' ) ) {
		$tax_class = 'Reduced Rate';
	}
	return $tax_class;
}
add_filter( 'woocommerce_product_tax_class', 'wc_diff_rate_for_user', 1, 2 );



