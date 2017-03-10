<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

if (!function_exists('wcdc_productCatDisableColumn')) {

    function wcdc_productCatDisableColumn($columns, $column, $id) {

        if ('disable-cat' == $column) {
            $disabledCats = get_option('woo_disabled_categories');
            $returnUrl = $_SERVER['REQUEST_URI'];
            $columns .='<input type="hidden" name=""  value="' . $id . '" />';
            $paged = isset($_GET['paged']) ? $_GET['paged'] : 1;

            if (!empty($disabledCats) && is_array($disabledCats) && in_array($id, $disabledCats)) {
                $queryArgs = add_query_arg(array(
                    'woo-action' => 'activate-cat',
                    'wooCatId' => $id,
                    'goTo' => $paged,
                ));

                $columns .='<img data-tip="' . __("The functionality for deactivating categories is only available in the pro version.", "wc-disable-categories") . '" class="help_tip pro-notice" src="' . WCDC_PLUGIN_URL . '/assets/images/disable.png" alt="' . __("Is deactive", "wc-disable-categories") . '" />';
            } else {
                $queryArgs = add_query_arg(array(
                    'woo-action' => 'deactivate-cat',
                    'wooCatId' => $id,
                    'goTo' => $paged,
                ));

                $columns .='<img data-tip="' . __("The functionality for deactivating categories is only available in the pro version.", "wc-disable-categories") . '" class="help_tip pro-notice" src="' . WCDC_PLUGIN_URL . '/assets/images/enable.png" alt="' . __("Is active", "wc-disable-categories") . '" />';
            }
        }

        return $columns;
    }

}

if (!function_exists('wcdc_productCatDisableColumns')) {

    function wcdc_productCatDisableColumns($columns) {
        $new_columns = array();
        $new_columns['disable-cat'] = __('Status', 'wc-disable-categories');
        return array_merge($new_columns, $columns);
    }

}
