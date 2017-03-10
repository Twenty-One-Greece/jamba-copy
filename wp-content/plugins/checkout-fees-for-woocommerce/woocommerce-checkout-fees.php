<?php
/*
Plugin Name: Payment Gateway Based Fees and Discounts for WooCommerce
Plugin URI: http://coder.fm/item/checkout-fees-for-woocommerce-plugin/
Description: WooCommerce Payment Gateways Fees and Discounts.
Version: 2.1.1
Author: Algoritmika Ltd
Author URI: http://www.algoritmika.com
Copyright: © 2016 Algoritmika Ltd.
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html
*/

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) exit;

// Check if WooCommerce is active
$plugin = 'woocommerce/woocommerce.php';
if (
	! in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) &&
	! ( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) return;

// Check if Pro is active, if so then return
$plugin = 'checkout-fees-for-woocommerce-pro/woocommerce-checkout-fees-pro.php';
if (
	in_array( $plugin, apply_filters( 'active_plugins', get_option( 'active_plugins', array() ) ) ) ||
	( is_multisite() && array_key_exists( $plugin, get_site_option( 'active_sitewide_plugins', array() ) ) )
) return;

if ( ! class_exists( 'Alg_Woocommerce_Checkout_Fees' ) ) :

/**
 * Main Alg_Woocommerce_Checkout_Fees Class
 *
 * @version 2.1.0
 * @class   Alg_Woocommerce_Checkout_Fees
 */

final class Alg_Woocommerce_Checkout_Fees {

	/**
	 * Plugin version.
	 *
	 * @var   string
	 * @since 2.1.0
	 */
	public $version = '2.1.1';

	/**
	 * @var Alg_Woocommerce_Checkout_Fees The single instance of the class
	 */
	protected static $_instance = null;

	/**
	 * Main Alg_Woocommerce_Checkout_Fees Instance
	 *
	 * Ensures only one instance of Alg_Woocommerce_Checkout_Fees is loaded or can be loaded.
	 *
	 * @static
	 * @return Alg_Woocommerce_Checkout_Fees - Main instance
	 */
	public static function instance() {
		if ( is_null( self::$_instance ) )
			self::$_instance = new self();
		return self::$_instance;
	}

	/**
	 * Alg_Woocommerce_Checkout_Fees Constructor.
	 *
	 * @version 2.0.0
	 * @access  public
	 */
	public function __construct() {

		// Include required files
		$this->includes();

		add_action( 'init', array( $this, 'init' ), 0 );

		if ( is_admin() ) {
			add_filter( 'woocommerce_get_settings_pages',                     array( $this, 'add_woocommerce_settings_tab' ) );
			add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'action_links' ) );

			require_once( 'includes/admin/admin-functions.php' );
		}
	}

	/**
	 * Show action links on the plugin screen
	 *
	 * @param mixed $links
	 * @return array
	 */
	public function action_links( $links ) {
		return array_merge( array(
			'<a href="' . admin_url( 'admin.php?page=wc-settings&tab=alg_checkout_fees' ) . '">' . __( 'Settings', 'woocommerce' ) . '</a>',
		), $links );
	}

	/**
	 * Include required core files used in admin and on the frontend.
	 *
	 * @version 2.1.0
	 */
	private function includes() {

		$settings = array();
		$settings[] = require_once( 'includes/admin/class-wc-checkout-fees-settings-general.php' );
		$settings[] = require_once( 'includes/admin/class-wc-checkout-fees-settings-gateways.php' );
		if ( is_admin() && get_option( 'alg_woocommerce_checkout_fees_version', '' ) !== $this->version ) {
			foreach ( $settings as $section ) {
				foreach ( $section->get_settings() as $value ) {
					if ( isset( $value['default'] ) && isset( $value['id'] ) ) {
						/* if ( isset ( $_GET['alg_woocommerce_checkout_fees_admin_options_reset'] ) ) {
							require_once( ABSPATH . 'wp-includes/pluggable.php' );
							if ( is_super_admin() ) {
								delete_option( $value['id'] );
							}
						} */
						$autoload = isset( $value['autoload'] ) ? ( bool ) $value['autoload'] : true;
						add_option( $value['id'], $value['default'], '', ( $autoload ? 'yes' : 'no' ) );
					}
				}
			}
			update_option( 'alg_woocommerce_checkout_fees_version', $this->version );
		}

		require_once( 'includes/admin/class-wc-checkout-fees-meta-boxes-per-product.php' );

		require_once( 'includes/class-wc-checkout-fees.php' );
	}

	/**
	 * Add Woocommerce settings tab to WooCommerce settings.
	 */
	public function add_woocommerce_settings_tab( $settings ) {
		$settings[] = include( 'includes/admin/class-wc-settings-checkout-fees.php' );
		return $settings;
	}

	/**
	 * Init Alg_Woocommerce_Checkout_Fees when WordPress initialises.
	 */
	public function init() {
		// Set up localisation
		load_plugin_textdomain( 'alg-woocommerce-fees', false, dirname( plugin_basename( __FILE__ ) ) . '/langs/' );
	}

	/**
	 * Get the plugin url.
	 *
	 * @return string
	 */
	public function plugin_url() {
		return untrailingslashit( plugin_dir_url( __FILE__ ) );
	}

	/**
	 * Get the plugin path.
	 *
	 * @return string
	 */
	public function plugin_path() {
		return untrailingslashit( plugin_dir_path( __FILE__ ) );
	}
}

endif;

/**
 * Returns the main instance of Alg_Woocommerce_Checkout_Fees to prevent the need to use globals.
 *
 * @version 2.0.0
 * @return Alg_Woocommerce_Checkout_Fees
 */
if ( ! function_exists( 'create_alg_woocommerce_checkout_fees' ) ) {
	function create_alg_woocommerce_checkout_fees() {
		return Alg_Woocommerce_Checkout_Fees::instance();
	}
}

create_alg_woocommerce_checkout_fees();
