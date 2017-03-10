<?php
/*
  Plugin Name: Product Category Tree
  Description: See your product categories in a tree structure and change their order via drag 'n' drop.
  Version: 1.0.1
  Author: TOGI Data
  Author URI: http://togidata.dk
  Text Domain: wc-disable-categories
  Domain Path: /languages/
 */

add_action('plugins_loaded', 'wcdc_plugin_free_install', 11);

if (!function_exists('wcdc_plugin_constructor')) {

    function wcdc_plugin_constructor() {
        include 'wc-disable-categories.php';

        if (class_exists('WC_Disable_Categories')) {
            // Let's start the game
            new WC_Disable_Categories();
        }
    }

}

add_action('wcdc_plugin_init', 'wcdc_plugin_constructor');

if (!function_exists('wcdc_plugin_free_install')) {

    function wcdc_plugin_free_install() {

        if (!function_exists('is_plugin_active')) {
            require_once( ABSPATH . 'wp-admin/includes/plugin.php' );
        }

        if (!function_exists('WC')) {
            add_action('admin_notices', 'wcdc_plugin_install_woocommerce_admin_notice');
        } elseif (defined('WCDC_PLUGIN_PREMIUM')) {
            add_action('admin_notices', 'wcdc_plugin_install_free_admin_notice');
            deactivate_plugins(plugin_basename(__FILE__));
        } else {
            do_action('wcdc_plugin_init');
        }
    }

}

if (!function_exists('wcdc_plugin_install_woocommerce_admin_notice')) {

    function wcdc_plugin_install_woocommerce_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e('Product Category Tree is enabled but not effective. It requires WooCommerce in order to work.', 'wc-disable-categories'); ?></p>
        </div>
        <?php
    }

}

if (!function_exists('wcdc_plugin_install_free_admin_notice')) {

    function wcdc_plugin_install_free_admin_notice() {
        ?>
        <div class="error">
            <p><?php _e('You can\'t activate the free version of Product Category Tree while you are using the premium one.', 'wc-disable-categories'); ?></p>
        </div>
        <?php
    }

}
