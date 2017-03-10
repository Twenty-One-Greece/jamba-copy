<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

// Check if WooCommerce is active
if (!in_array('woocommerce/woocommerce.php', apply_filters('active_plugins', get_option('active_plugins'))))
    return;

if (!class_exists('WC_Disable_Categories')) {

    include 'includes/disable-functions.php';
    include 'includes/admin-functions.php';
    include 'includes/main-functions.php';
    include 'includes/updateDB.php';
    include 'includes/class-ads.php';

    define('WCDC_PLUGIN_URL', plugins_url('product-category-tree'));
    define('WCDC_PLUGIN_DIR', plugin_dir_path(__FILE__));
    define('WCDC_PLUGIN_PRO_UPGRADE_URL', 'http://togidata.dk/en/product-category-tree/');

    class WC_Disable_Categories {

        public function __construct() {

            load_plugin_textdomain('wc-disable-categories', false, dirname(plugin_basename(__FILE__)) . '/languages/');

            add_action('admin_enqueue_scripts', array($this, 'action_enqueue_assets_admin'));
            add_action('wp_head', array($this, 'action_enqueue_assets'));
            add_action('plugin_row_meta', array($this, 'add_plugin_meta'), 10, 2);

            add_filter('manage_edit-product_cat_columns', 'wcdc_productCatDisableColumns');
            add_filter('manage_product_cat_custom_column', 'wcdc_productCatDisableColumn', 100, 3);
            
            $ads = new WCDC_Admin_Ads();
            $ads->add_hooks();
        }

        public function action_enqueue_assets() {
            wp_register_style('WCDisableCategories-style', WCDC_PLUGIN_URL . '/assets/css/style.css');
            wp_enqueue_style('WCDisableCategories-style');
            wp_enqueue_script('jquery');
        }

        /**
         * Enqueue backend dependencies.
         */
        public function action_enqueue_assets_admin() {
            wp_register_style('WCDisableCategories-style-admin', WCDC_PLUGIN_URL . '/assets/css/admin.css');
            wp_enqueue_style('WCDisableCategories-style-admin');
            wp_register_style('woocommerce_admin_styles', WC()->plugin_url() . '/assets/css/admin.css', array(), WC_VERSION);
            wp_enqueue_style('woocommerce_admin_styles');
            wp_enqueue_script('jquery');
            // Register scripts
            $suffix = defined('SCRIPT_DEBUG') && SCRIPT_DEBUG ? '' : '.min';
            wp_register_script('woocommerce_admin', WC()->plugin_url() . '/assets/js/admin/woocommerce_admin' . $suffix . '.js', array('jquery', 'jquery-blockui', 'jquery-ui-sortable', 'jquery-ui-widget', 'jquery-ui-core', 'jquery-tiptip'), WC_VERSION);
            wp_enqueue_script('woocommerce_admin');
        }

        public function add_plugin_meta($plugin_meta, $plugin_file) {
            if ($plugin_file == 'product-category-tree/init.php') {
                $plugin_meta['upgrade'] = '<a target="_blank" href="'.WCDC_PLUGIN_PRO_UPGRADE_URL.'">' . __('Update to pro', 'wc-disable-categories') . '</a>';
            }

            return $plugin_meta;
        }

    }

}
