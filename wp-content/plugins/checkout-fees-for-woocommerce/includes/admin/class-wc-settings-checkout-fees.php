<?php
/**
 * Checkout Fees for WooCommerce - Settings
 *
 * @version 2.1.0
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Settings_Checkout_Fees' ) ) :

class Alg_WC_Settings_Checkout_Fees extends WC_Settings_Page {

	/**
	 * Constructor.
	 *
	 * @version 2.1.0
	 */
	function __construct() {

		$this->id    = 'alg_checkout_fees';
		$this->label = __( 'Payment Gateway Based Fees and Discounts', 'alg-woocommerce-fees' );

		parent::__construct();
	}

	/**
	 * get_settings.
	 */
	public function get_settings() {
		global $current_section;
		$the_current_section = ( '' != $current_section ) ? $current_section : 'general';
		return apply_filters( 'woocommerce_get_settings_' . $this->id . '_' . $the_current_section, array() );
	}

	/**
	 * Output sections.
	 *
	 * @version 2.0.2
	 * @since   2.0.0
	 */
	public function output_sections() {
		global $current_section;

		$the_current_section = ( '' != $current_section ) ? $current_section : 'general';

		$sections = $this->get_sections();

		if ( empty( $sections ) || 1 === sizeof( $sections ) ) {
			return;
		}

		echo '<ul class="subsubsub">';

		$array_keys = array_keys( $sections );

		foreach ( $sections as $id => $label ) {
			echo '<li><a href="' . admin_url( 'admin.php?page=wc-settings&tab=' . $this->id . '&section=' . /* sanitize_title */( $id ) ) . '" class="' . ( $the_current_section == $id ? 'current' : '' ) . '">' . $label . '</a> ' . ( end( $array_keys ) == $id ? '' : '|' ) . ' </li>';
		}

		echo '</ul><br class="clear" />';
	}
}

endif;

return new Alg_WC_Settings_Checkout_Fees();
