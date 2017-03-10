<?php
/**
 * Checkout Fees for WooCommerce - Gateways Section(s) Settings
 *
 * @version 2.1.1
 * @since   1.0.0
 * @author  Algoritmika Ltd.
 */

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

if ( ! class_exists( 'Alg_WC_Checkout_Fees_Settings_Gateways' ) ) :

class Alg_WC_Checkout_Fees_Settings_Gateways {

	/**
	 * Constructor.
	 *
	 * @version 1.2.0
	 */
	public function __construct() {

//		$this->id   = 'gateways';
//		$this->desc = __( 'Gateways', 'alg-woocommerce-fees' );

		add_filter( 'wp_loaded',                                  array( $this, 'add_gateway_fees_settings_hook' ) );
		add_filter( 'woocommerce_get_sections_alg_checkout_fees', array( $this, 'settings_section' ) );
	}

	/**
	 * get_settings.
	 */
	function get_settings() {
		return array();
	}

	/**
	 * add_gateway_fees_settings_hook.
	 *
	 * @version 2.0.2
	 */
	function add_gateway_fees_settings_hook() {
		global $woocommerce;
		if ( isset( $woocommerce ) ) {
			$available_gateways = $woocommerce->payment_gateways->payment_gateways();
			foreach ( $available_gateways as $key => $gateway ) {
				add_filter( 'woocommerce_get_settings_alg_checkout_fees_' . sanitize_title( $key ), array( $this, 'add_gateway_fees_settings' ), PHP_INT_MAX );
			}
		}
	}

	/**
	 * settings_section.
	 */
	function settings_section( $sections ) {
		global $woocommerce;
		$available_gateways = $woocommerce->payment_gateways->payment_gateways();
		foreach ( $available_gateways as $key => $gateway ) {
			$sections[ $key ] = $gateway->title;
		}
		return $sections;
	}

	/**
	 * add_gateway_fees_settings.
	 *
	 * @version 2.1.1
	 */
	function add_gateway_fees_settings() {

		// Getting current gateway (section)
		if ( ! isset( $_GET['section'] ) ) return array();
		global $woocommerce;
		$available_gateways = $woocommerce->payment_gateways->payment_gateways();
		$key = $_GET['section'];
		if ( ! isset( $available_gateways[ $key ] ) ) return array();
		$gateway = $available_gateways[ $key ];

		// Countries
		$countries = alg_checkout_fees_get_countries();

		// Cats
		$product_cats = array();
		$product_categories = get_terms( 'product_cat', 'orderby=name&hide_empty=0' );
		if ( ! empty( $product_categories ) && ! is_wp_error( $product_categories ) ){
			foreach ( $product_categories as $product_category ) {
				$product_cats[ $product_category->term_id ] = $product_category->name;
			}
		}

		// Adding settings
		$settings = array(

			array(
				'title'     => $gateway->title,
				'type'      => 'title',
				'id'        => 'alg_gateways_fees_options'
			),

			array(
				'title'     => $gateway->title . ' ' . __( 'Fees and Discounts', 'alg-woocommerce-fees' ),
				'desc'      => '<strong>' . __( 'Enable', 'alg-woocommerce-fees' ) . '</strong>',
				'desc_tip'  => sprintf( __( 'Add fee/discount to %s gateway', 'alg-woocommerce-fees' ), $gateway->title ),
				'id'        => 'alg_gateways_fees_enabled_' . $key,
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'title'     => __( 'Title', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Fee (or discount) title to show to customer.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_text_' . $key,
				'default'   => '',
				'type'      => 'text',
			),

			array(
				'title'     => __( 'Fee Type', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Fee (or discount) type.', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'Percent or fixed value.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_type_' . $key,
				'default'   => 'fixed',
				'type'      => 'select',
				'options'   => array(
					'fixed'   => __( 'Fixed', 'alg-woocommerce-fees' ),
					'percent' => __( 'Percent', 'alg-woocommerce-fees' ),
				),
			),

			array(
				'title'     => __( 'Fee Value', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Fee (or discount) value.', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'The value. For discount enter a negative number.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_value_' . $key,
				'default'   => 0,
				'type'      => 'number',
				'custom_attributes' => array( 'step' => '0.0001', ),
			),

			array(
				'title'     => __( 'Minimum Fee Value', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Minimum fee (or discount).', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'Set 0 to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_min_fee_' . $key,
				'default'   => 0,
				'type'      => 'number',
				'custom_attributes' => array( 'step' => '0.0001', ),
			),

			array(
				'title'     => __( 'Maximum Fee Value', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Maximum fee (or discount).', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'Set 0 to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_max_fee_' . $key,
				'default'   => 0,
				'type'      => 'number',
				'custom_attributes' => array( 'step' => '0.0001', ),
			),

			array(
				'title'     => __( 'Additional Fee Title (Optional)', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Fee (or discount) title to show to customer.', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'To display each (i.e. main and additional) fees on different lines in cart (and checkout), you must set different titles. If titles are equal they will be merged into single line.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_text_2_' . $key,
				'default'   => '',
				'type'      => 'text',
			),

			array(
				'title'     => __( 'Additional Fee Type (Optional)', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Fee (or discount) type.', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'Percent or fixed value.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_type_2_' . $key,
				'default'   => 'fixed',
				'type'      => 'select',
				'options'   => array(
					'fixed'   => __( 'Fixed', 'alg-woocommerce-fees' ),
					'percent' => __( 'Percent', 'alg-woocommerce-fees' ),
				),
			),

			array(
				'title'     => __( 'Additional Fee Value (Optional)', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Fee (or discount) value.', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'The value. For discount enter a negative number.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_value_2_' . $key,
				'default'   => 0,
				'type'      => 'number',
				'custom_attributes' => array( 'step' => '0.0001', ),
			),

			array(
				'title'     => __( 'Minimum Additional Fee Value', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Minimum fee (or discount).', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'Set 0 to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_min_fee_2_' . $key,
				'default'   => 0,
				'type'      => 'number',
				'custom_attributes' => array( 'step' => '0.0001', ),
			),

			array(
				'title'     => __( 'Maximum Additional Fee Value', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Maximum fee (or discount).', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'Set 0 to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_max_fee_2_' . $key,
				'default'   => 0,
				'type'      => 'number',
				'custom_attributes' => array( 'step' => '0.0001', ),
			),

			array(
				'title'     => __( 'Minimum Cart Amount', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Minimum cart amount for adding the fee (or discount).', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'Set 0 to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_min_cart_amount_' . $key,
				'default'   => 0,
				'type'      => 'number',
				'custom_attributes' => array( 'step' => '0.0001', 'min' => '0', ),
			),

			array(
				'title'     => __( 'Maximum Cart Amount', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Maximum cart amount for adding the fee (or discount).', 'alg-woocommerce-fees' ),
				'desc_tip'  => __( 'Set 0 to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_max_cart_amount_' . $key,
				'default'   => 0,
				'type'      => 'number',
				'custom_attributes' => array( 'step' => '0.0001', 'min' => '0', ),
			),

			array(
				'title'     => __( 'Rounding', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Round the fee (or discount) value before adding to the cart.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_round_' . $key,
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'title'     => __( '', 'alg-woocommerce-fees' ),
				'desc'      => __( 'If rounding is enabled, set precision here.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_round_precision_' . $key,
				'default'   => 0,
				'type'      => 'number',
				'custom_attributes' => array( 'step' => '1', 'min' => '0', ),
			),

			array(
				'title'     => __( 'Taxes', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Is taxable?', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_is_taxable_' . $key,
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'title'     => __( '', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Tax Class (only if Taxable selected).', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_tax_class_id_' . $key,
				'default'   => '',
				'type'      => 'select',
				'options'   => array_merge( array( __( 'Standard Rate', 'alg-woocommerce-fees' ) ), WC_Tax::get_tax_classes() ),
			),

			array(
				'title'     => __( 'Exclude Shipping', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Exclude shipping from total cart sum, when calculating fees.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_exclude_shipping_' . $key,
				'default'   => 'no',
				'type'      => 'checkbox',
			),

			array(
				'title'     => __( 'Customer Countries', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Countries to include.', 'alg-woocommerce-fees' ) .
					apply_filters(
						'alg_wc_checkout_fees_option',
						' '
						. __( 'Get <a target="_blank" href="http://coder.fm/item/checkout-fees-for-woocommerce-plugin/">Payment Gateway Based Fees and Discounts for WooCommerce plugin page</a> to change value.', 'alg-woocommerce-fees' )
					),
				'desc_tip'  => __( 'Fee (or discount) will only be added if customer\'s country is in the list. Leave blank to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_countries_include_' . $key,
				'default'   => '',
				'type'      => 'multiselect',
				'class'     => 'chosen_select',
				'css'       => 'width: 450px;',
				'options'   => $countries,
				'custom_attributes' => apply_filters( 'alg_wc_checkout_fees_option', array( 'disabled' => 'disabled' ) ),
			),

			array(
				'title'     => __( '', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Countries to exclude.', 'alg-woocommerce-fees' ) .
					apply_filters(
						'alg_wc_checkout_fees_option',
						' '
						. __( 'Get <a target="_blank" href="http://coder.fm/item/checkout-fees-for-woocommerce-plugin/">Payment Gateway Based Fees and Discounts for WooCommerce plugin page</a> to change value.', 'alg-woocommerce-fees' )
					),
				'desc_tip'  => __( 'Fee (or discount) will only be added if customer\'s country is NOT in the list. Leave blank to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_countries_exclude_' . $key,
				'default'   => '',
				'type'      => 'multiselect',
				'class'     => 'chosen_select',
				'css'       => 'width: 450px;',
				'options'   => $countries,
				'custom_attributes' => apply_filters( 'alg_wc_checkout_fees_option', array( 'disabled' => 'disabled' ) ),
			),

			array(
				'title'     => __( 'Product Categories', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Categories to include.', 'alg-woocommerce-fees' ) .
					apply_filters(
						'alg_wc_checkout_fees_option',
						' '
						. __( 'Get <a target="_blank" href="http://coder.fm/item/checkout-fees-for-woocommerce-plugin/">Payment Gateway Based Fees and Discounts for WooCommerce plugin page</a> to change value.', 'alg-woocommerce-fees' )
					),
				'desc_tip'  => __( 'Fee (or discount) will only be added if product of selected category(-ies) is in the cart. Leave blank to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_cats_include_' . $key,
				'default'   => '',
				'type'      => 'multiselect',
				'class'     => 'chosen_select',
				'css'       => 'width: 450px;',
				'options'   => $product_cats,
				'custom_attributes' => apply_filters( 'alg_wc_checkout_fees_option', array( 'disabled' => 'disabled' ) ),
			),

			array(
				'desc'      => __( 'Categories to include (additional fee).', 'alg-woocommerce-fees' ) .
					apply_filters(
						'alg_wc_checkout_fees_option',
						' '
						. __( 'Get <a target="_blank" href="http://coder.fm/item/checkout-fees-for-woocommerce-plugin/">Payment Gateway Based Fees and Discounts for WooCommerce plugin page</a> to change value.', 'alg-woocommerce-fees' )
					),
				'desc_tip'  => __( 'Fee (or discount) will only be added if product of selected category(-ies) is in the cart. Leave blank to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_cats_include_fee_2_' . $key,
				'default'   => get_option( 'alg_gateways_fees_cats_include_' . $key, '' ),
				'type'      => 'multiselect',
				'class'     => 'chosen_select',
				'css'       => 'width: 450px;',
				'options'   => $product_cats,
				'custom_attributes' => apply_filters( 'alg_wc_checkout_fees_option', array( 'disabled' => 'disabled' ) ),
			),

			array(
				'desc'      => __( 'Categories to include - Calculation type.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_cats_include_calc_type_' . $key,
				'default'   => 'for_all_cart',
				'type'      => 'select',
				'options'   => array(
					'for_all_cart'               => __( 'For all cart', 'alg-woocommerce-fees' ),
					'only_for_selected_products' => __( 'Only for selected products', 'alg-woocommerce-fees' ),
				),
			),

			array(
				'title'     => __( '', 'alg-woocommerce-fees' ),
				'desc'      => __( 'Categories to exclude.', 'alg-woocommerce-fees' ) .
					apply_filters(
						'alg_wc_checkout_fees_option',
						' '
						. __( 'Get <a target="_blank" href="http://coder.fm/item/checkout-fees-for-woocommerce-plugin/">Payment Gateway Based Fees and Discounts for WooCommerce plugin page</a> to change value.', 'alg-woocommerce-fees' )
					),
				'desc_tip'  => __( 'Fee (or discount) will only be added if NO product of selected category(-ies) is in the cart. Leave blank to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_cats_exclude_' . $key,
				'default'   => '',
				'type'      => 'multiselect',
				'class'     => 'chosen_select',
				'css'       => 'width: 450px;',
				'options'   => $product_cats,
				'custom_attributes' => apply_filters( 'alg_wc_checkout_fees_option', array( 'disabled' => 'disabled' ) ),
			),

			array(
				'desc'      => __( 'Categories to exclude (additional fee).', 'alg-woocommerce-fees' ) .
					apply_filters(
						'alg_wc_checkout_fees_option',
						' '
						. __( 'Get <a target="_blank" href="http://coder.fm/item/checkout-fees-for-woocommerce-plugin/">Payment Gateway Based Fees and Discounts for WooCommerce plugin page</a> to change value.', 'alg-woocommerce-fees' )
					),
				'desc_tip'  => __( 'Fee (or discount) will only be added if NO product of selected category(-ies) is in the cart. Leave blank to disable.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_cats_exclude_fee_2_' . $key,
				'default'   => get_option( 'alg_gateways_fees_cats_exclude_' . $key, '' ),
				'type'      => 'multiselect',
				'class'     => 'chosen_select',
				'css'       => 'width: 450px;',
				'options'   => $product_cats,
				'custom_attributes' => apply_filters( 'alg_wc_checkout_fees_option', array( 'disabled' => 'disabled' ) ),
			),

			array(
				'desc'      => __( 'Categories to exclude - Calculation type.', 'alg-woocommerce-fees' ),
				'id'        => 'alg_gateways_fees_cats_exclude_calc_type_' . $key,
				'default'   => 'for_all_cart',
				'type'      => 'select',
				'options'   => array(
					'for_all_cart'               => __( 'For all cart', 'alg-woocommerce-fees' ),
					'only_for_selected_products' => __( 'Only for selected products', 'alg-woocommerce-fees' ),
				),
			),

			array(
				'type'      => 'sectionend',
				'id'        => 'alg_gateways_fees_options'
			),

		);

		return $settings;
	}

}

endif;

return new Alg_WC_Checkout_Fees_Settings_Gateways();
