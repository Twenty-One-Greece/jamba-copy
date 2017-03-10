<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Class WCDC_Admin_Ads
 *
 * @ignore
 * @access private
 */
class WCDC_Admin_Ads {

    /**
     * @return bool Adds hooks
     */
    public function add_hooks() {

        // don't hook if Premium is activated
        if (defined('WCDC_PLUGIN_PREMIUM')) {
            return false;
        }

        add_filter('wcdc_admin_after_product_category_tree', array($this, 'plugin_pro_add'));
        return true;
    }

    /**
     * Add text row to "Form > Appearance" tab.
     */
    public function plugin_pro_add() {
        ?>
        <div class="wcdc-add-container">
            <h1><?php _e('Product Category Tree PREMIUM VERSION', 'wc-disable-categories'); ?></h1>
            <p><?php _e('Feel free to check out our premium version of the plugin, where we have added these awesome extra features:', 'wc-disable-categories'); ?></p>
            <ul>
                <li>
                    <b><?php _e('Tree structure when adding/editing a product', 'wc-disable-categories'); ?></b>
                    <p><?php _e('You will also have the tree structure for the product categories when you are creating/editing a product. This makes it easier to find the correct category for your product', 'wc-disable-categories'); ?></p>
                </li>
                <li>
                    <b><?php _e('Disable an entire product category with a single click', 'wc-disable-categories'); ?></b>
                    <p><?php _e('If you want to hide an entire product category from your shop, just deactivate this category with a single click. The category will no longer be displayed in your shop, and neither will the products in this category', 'wc-disable-categories'); ?></p>
                </li>
            </ul>
            <div class="actions">
                <?php echo '<a target="_blank" href="http://togidata.dk/en/shop/product-category-tree/" class="btn-green">' . __('Upgrade now', 'wc-disable-categories') . '</a>'; ?>
                <?php echo '<a target="_blank" href="' . WCDC_PLUGIN_PRO_UPGRADE_URL . '" class="btn-white">' . __('Read more and see screenshots', 'wc-disable-categories') . '</a>'; ?>
            </div>
        </div>
        <?php
    }

}
