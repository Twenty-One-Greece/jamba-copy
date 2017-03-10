<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

add_action('admin_menu', 'wcdc_register_disable_cat_page');

if (!function_exists('wcdc_register_disable_cat_page')) {

    function wcdc_register_disable_cat_page() {
        //add_submenu_page('TogiData Woo Categories', 'TogiData Woo Categories', 10, 'togi_woo_categories', 'togi_woo_categories','dashicons-networking');
        add_submenu_page('edit.php?post_type=product', __('Category Tree', "wc-disable-categories"), __('Category Tree', "wc-disable-categories"), 'manage_options', 'togi_woo_categories', 'wcdc_fun_togi_woo_categories');
    }

}


if (!function_exists('wcdc_getSubCat')) {

    function wcdc_getSubCat($category) {

        $thumbnail_id = get_woocommerce_term_meta($category->term_id, 'thumbnail_id', true);
        $image = wp_get_attachment_url($thumbnail_id);
        //$image = '';

        echo '<li id="recordsArray_' . $category->term_id . '" class=""><div class="ace-li-inner"><div class="ace-links-parent">
<div class="ace-category-start"><div class="ace-cname">	' . $category->name . ' </div><div class="ace-cdescription"> ' . substr(strip_tags($category->description), 0, 30) . '</div><div class="ace-cimage">';
        if ($image) {
            echo '<a class="lightbox" href="' . $image . '"><img src="' . $image . '" alt="cat-thumb"/></a>';
        } else {
            echo '<img src="' . WCDC_PLUGIN_URL . '/assets/images/placeholder.png" alt="catthumb"/>';
        }

        echo '</div>';

        //Customized by 2hats.
        $disabledCats = get_option('woo_disabled_categories');
        echo '<div class="ace-cstatus" style="float:left;">';
        if (!empty($disabledCats) && is_array($disabledCats) && in_array($category->term_id, $disabledCats)) {
            $queryArgs = add_query_arg(array(
                'woo-action' => 'activate-cat',
                'wooCatId' => $category->term_id,
                'goTo' => 0,
            ));
            echo '<img src="' . WCDC_PLUGIN_URL . '/assets/images/disable.png" alt="' . __("Is deactive", "wc-disable-categories") . '" data-tip="' . __("The functionality for deactivating categories is only available in the pro version.", "wc-disable-categories") . '" class="help_tip pro-notice" />';
        } else {
            $queryArgs = add_query_arg(array(
                'woo-action' => 'deactivate-cat',
                'wooCatId' => $category->term_id,
                'goTo' => 0,
            ));
            echo '<img src="' . WCDC_PLUGIN_URL . '/assets/images/enable.png" alt="' . __("Is active", "wc-disable-categories") . '" data-tip="' . __("The functionality for deactivating categories is only available in the pro version.", "wc-disable-categories") . '" class="help_tip pro-notice" />';
        }
        echo '</div>';
        //Customized by 2hats. ends..

        echo '<div class="ace-cslug"> ' . $category->slug . '</div><div class="ace-ccount"><a href="/?product_cat=' . $category->slug . '" target="_blank">' . $category->count . '</a></div>

</div><span class="ace-links"><a target="_blank" href="' . get_admin_url() . 'edit-tags.php?action=edit&taxonomy=product_cat&tag_ID=' . $category->term_id . '&post_type=product">' . __("Edit", "wc-disable-categories") . '</a> <a href="javascript:void(0);" class="ace-quick-edit">' . __("Quick Edit", "wc-disable-categories") . '</a> <a href="' . get_term_link($category->term_id) . '" target="_blank">' . __("View", "wc-disable-categories") . '</a>';

        echo '<a href="javascript:void(0);" onclick="deleteli(' . $category->term_id . ')" id="cat_delete" class="">' . __("Delete", "wc-disable-categories") . '</a>';


        echo '</span></div>';

        echo '<form class="ace-cat-update-form" name="update-cat" method="post" action="' . get_admin_url() . 'admin.php?page=togi_woo_categories">
<input type="hidden" name="cid" value="' . $category->term_id . '">' . __("Name", "wc-disable-categories") . '<input type="text" name="cname" value="' . $category->name . '"> ' . __("Slug", "wc-disable-categories") . '<input type="text" name="slug" value="' . $category->slug . '"> <input class="button button-primary" type="submit" name="acesubmit" value="' . __("Update", "wc-disable-categories") . '"> <input type="button" name="cancel" value="' . __("Cancel", "wc-disable-categories") . '" class="ace-form-close button button-primary"/>
</form>';
    }

}
