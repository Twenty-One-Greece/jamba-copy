<?php

// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

add_action('wp_ajax_nopriv_wcdc_updateCategory', 'wcdc_updateCategory');
add_action('wp_ajax_wcdc_updateCategory', 'wcdc_updateCategory');

if (!function_exists('wcdc_updateCategory')) {

    function wcdc_updateCategory() {
        global $wpdb;
        $return['msg'] = '';
        if (!empty($_POST['action']) && $_POST['action'] == "wcdc_updateCategory") {
            $updateRecordsArray = isset($_POST['recordsArray']) ? $_POST['recordsArray'] : '';
            $parent = isset($_POST['p']) ? intval($_POST['p']) : '';

            $termMetaTable = $wpdb->prefix . "woocommerce_termmeta";
            $termMetaKey = 'woocommerce_term_id';
            $checkTableExist = $wpdb->get_var("SHOW TABLES LIKE '$termMetaTable'") == $termMetaTable ? true : false;

            if (empty($checkTableExist)) {
                $termMetaKey = 'term_id';
                $termMetaTable = $wpdb->prefix . 'termmeta';
            }

            $listingCounter = 1;

            $table1 = $wpdb->prefix . 'term_taxonomy';
            foreach ($updateRecordsArray as $key => $value) {
                //echo $key.' '.$value.' - ';
                $wpdb->update(
                        $termMetaTable, array(
                    'meta_value' => $key
                        ), array($termMetaKey => $value, 'meta_key' => 'order'), array(
                    '%s', // value1
                    '%d' // value2
                        ), array('%d')
                );
                if (isset($parent)) {
                    $wpdb->update(
                            $table1, array(
                        'parent' => $parent
                            ), array('term_id' => $value, 'taxonomy' => 'product_cat'), array(
                        '%s', // value1
                        '%d' // value2
                            ), array('%d')
                    );
                }

                //$query = "UPDATE records SET recordListingID = " . $listingCounter . " WHERE recordID = " . $recordIDValue;
                //mysql_query($query) or die('Error, insert query failed');
                $listingCounter = $listingCounter + 1;
            }

            $return['msg'] = 'success';
        }

        wp_send_json($return);
    }

}


add_action('wp_ajax_nopriv_wcdc_deleteCategory', 'wcdc_deleteCategory');
add_action('wp_ajax_wcdc_deleteCategory', 'wcdc_deleteCategory');

if (!function_exists('wcdc_deleteCategory')) {

    function wcdc_deleteCategory() {
        global $wpdb;
        $return['msg'] = '';
        if (!empty($_POST['action']) && $_POST['action'] == "wcdc_deleteCategory") {
            $delId = isset($_POST['delId']) ? intval($_POST['delId']) : '';
            if ($delId) {

                $catdelete = array('type' => 'product', 'parent' => $delId, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                $categories = get_categories($catdelete);
                if (!empty($categories)) {
                    foreach ($categories as $category) {

                        //--------- start sub level 1 category ----------
                        $argssub = array('type' => 'product', 'parent' => $category->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                        $subcat = get_categories($argssub);
                        if (!empty($subcat)) {

                            foreach ($subcat as $level2) {
                                //--------- start sub level 2 category ----------
                                $argssub2 = array('type' => 'product', 'parent' => $level2->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                                $subcat2 = get_categories($argssub2);
                                if (!empty($subcat2)) {

                                    foreach ($subcat2 as $level3) {
                                        //--------- start sub level 3 category ----------
                                        $argssub3 = array('type' => 'product', 'parent' => $level3->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                                        $subcat3 = get_categories($argssub3);
                                        if (!empty($subcat3)) {

                                            foreach ($subcat3 as $level4) {
                                                //--------- start sub level 4 category ----------
                                                $argssub4 = array('type' => 'product', 'parent' => $level4->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                                                $subcat4 = get_categories($argssub4);
                                                if (!empty($subcat4)) {

                                                    foreach ($subcat4 as $level5) {
                                                        //--------- start sub level 5 category ----------
                                                        $argssub5 = array('type' => 'product', 'parent' => $level5->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                                                        $subcat5 = get_categories($argssub5);
                                                        if (!empty($subcat5)) {
                                                            foreach ($subcat5 as $level6) {

                                                                //echo "Level-6 id".$level6->term_id;
                                                                wp_delete_term($level6->term_id, 'product_cat'); //-------------- end if sub level 6 cat not empty ----------
                                                            }
                                                        }
                                                        //echo "Level-5 id".$level5->term_id;
                                                        wp_delete_term($level5->term_id, 'product_cat'); //-------------- end if sub level 5 cat not empty ----------
                                                    }
                                                }
                                                // echo "Level-4 id".$level4->term_id;
                                                wp_delete_term($level4->term_id, 'product_cat'); //-------------- end if sub level 4 cat not empty ----------
                                            }
                                        }
                                        //echo "Level-3 id". $level3->term_id;
                                        wp_delete_term($level3->term_id, 'product_cat'); //-------------- end if sub level 3 cat not empty ----------
                                    }
                                }
                                //echo "Level-2 id".$level2->term_id;
                                wp_delete_term($level2->term_id, 'product_cat'); //-------------- end if sub level 2 cat not empty ----------
                            }
                        }
                        //echo "Level-1 id".$category->term_id;
                        wp_delete_term($category->term_id, 'product_cat'); //-------------- end if sub level 1 cat not empty ----------
                    }
                }
                //echo "Base id".$_GET['ace_delete_cat'];
                wp_delete_term($delId, 'product_cat');
                $return['msg'] = 'success';
            }
        }

        wp_send_json($return);
    }

}
?>