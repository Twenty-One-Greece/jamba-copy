<?php
// Exit if accessed directly
if (!defined('ABSPATH'))
    exit;

/**
 * Enqueue backend dependencies.
 */
if (!function_exists('wcdc_action_enqueue_adminPluginPage_assets')) {

    function wcdc_action_enqueue_adminPluginPage_assets() {
        wp_register_style('WCDC-style-admin', WCDC_PLUGIN_URL . '/assets/css/custom-style.css');
        wp_enqueue_style('WCDC-style-admin');
        wp_register_style('WCDCJqueryUi-style', WCDC_PLUGIN_URL . '/assets/css/jquery-ui.css');
        wp_enqueue_style('WCDCJqueryUi-style');

        wp_register_style('WCDC-LightboxStyle', WCDC_PLUGIN_URL . '/assets/css/lightbox.css');
        wp_enqueue_style('WCDC-LightboxStyle');

        wp_enqueue_script('jquery-ui-core');
        wp_register_script('WCDC-cuctomScript', WCDC_PLUGIN_URL . '/assets/js/admin-custom.js');
        wp_enqueue_script('WCDC-cuctomScript');
        wp_register_script('WCDC-LightboxScript', WCDC_PLUGIN_URL . '/assets/js/jquery.lightbox.js');
        wp_enqueue_script('WCDC-LightboxScript');
        wp_localize_script('WCDC-cuctomScript', 'wcdc_ajaxScript', array('ajax_url' => admin_url('admin-ajax.php')));
    }

}

if (!function_exists('wcdc_fun_togi_woo_categories')) {

    function wcdc_fun_togi_woo_categories() {
        wcdc_action_enqueue_adminPluginPage_assets();
        ?>
        <style type="text/css">
            .dashicons, .dashicons-before::before {
                vertical-align: middle;
                width: 35px;
            }
        </style>
        <div class="wrap nosubsub ace_cat_start_top">
            <h2 class="dashicons-before dashicons-networking"><?php _e('Product Category Tree', 'wc-disable-categories'); ?></h2>
            <div id="col-container">

                <div id="col-right">
                    <div class="col-wrap">
                        <div class="ace_expand_col">
                            <ul>
                                <li><a id="ace_expand" href="javascript:void(0);"><?php _e('Expand all', 'wc-disable-categories'); ?></a></li>
                                <li><a id="ace_collapse" href="javascript:void(0);"><?php _e('Collapse all', 'wc-disable-categories'); ?></a></li>
                            </ul>
                        </div>
                        <?php
                        if (isset($_POST['acesubmit']) && !empty($_POST['acesubmit'])) {
                            wp_update_term(intval($_POST['cid']), 'product_cat', array(
                                'name' => sanitize_text_field($_POST['cname']),
                                'slug' => sanitize_text_field($_POST['slug'])
                            ));
                        }

                        if (isset($_POST['submit']) && !empty($_POST['submit'])) {
                            if ($_POST['tag-name'] != '') {
                                wp_insert_term(
                                        sanitize_text_field($_POST['tag-name']), // the term
                                        'product_cat', // the taxonomy
                                        array(
                                    'description' => $_POST['description'],
                                    'slug' => sanitize_text_field($_POST['slug']),
                                    'parent' => intval($_POST['parent'])
                                        )
                                );
                            } else {
                                
                            }
                        }
                        ?>

                        <?php
                        $args = array('type' => 'product',
                            'parent' => 0,
                            'hide_empty' => 0,
                            'taxonomy' => 'product_cat'
                        );

                        $categories = get_categories($args);
                        echo '<div id="Accord" class="accord">
                            <ul class="ace-cat-top">
                            <li class="ace-cname">' . __('Name', 'wc-disable-categories') . '</li>
                            <li class="ace-cdescription">' . __('Description', 'wc-disable-categories') . '</li>
                            <li class="ace-cimage">' . __('Image', 'wc-disable-categories') . '</li>
                            <li class="ace-cstatus">' . __('Status', 'wc-disable-categories') . '</li>
                            <li class="ace-cslug">' . __('Slug', 'wc-disable-categories') . '</li>
                            <li class="ace-ccount">' . __('Count', 'wc-disable-categories') . '</li>
                            </ul><div id="contentLeft">
                            <ul class="ace-cat">';

                        foreach ($categories as $category) {
                            wcdc_getSubCat($category);


                            //--------- start sub level 1 category ----------
                            $argssub = array('type' => 'product', 'parent' => $category->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                            $subcat = get_categories($argssub);
                            if (!empty($subcat)) {
                                echo '<ul parent="' . $category->term_id . '" class="sub level-1">';
                                foreach ($subcat as $category) {
                                    wcdc_getSubCat($category);

                                    //--------- start sub level 2 category ----------
                                    $argssub2 = array('type' => 'product', 'parent' => $category->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                                    $subcat2 = get_categories($argssub2);
                                    if (!empty($subcat2)) {
                                        echo '<ul parent="' . $category->term_id . '" class="sub level-2">';
                                        foreach ($subcat2 as $category) {
                                            wcdc_getSubCat($category);
                                            //--------- start sub level 3 category ----------
                                            $argssub3 = array('type' => 'product', 'parent' => $category->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                                            $subcat3 = get_categories($argssub3);
                                            if (!empty($subcat3)) {
                                                echo '<ul parent="' . $category->term_id . '" class="sub level-3">';
                                                foreach ($subcat3 as $category) {
                                                    wcdc_getSubCat($category);
                                                    //--------- start sub level 4 category ----------
                                                    $argssub4 = array('type' => 'product', 'parent' => $category->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                                                    $subcat4 = get_categories($argssub4);
                                                    if (!empty($subcat4)) {
                                                        echo '<ul parent="' . $category->term_id . '" class="sub level-4">';
                                                        foreach ($subcat4 as $category) {
                                                            wcdc_getSubCat($category);
                                                            //--------- start sub level 5 category ----------
                                                            $argssub5 = array('type' => 'product', 'parent' => $category->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                                                            $subcat5 = get_categories($argssub5);
                                                            if (!empty($subcat5)) {
                                                                echo '<ul parent="' . $category->term_id . '" class="sub level-5">';
                                                                foreach ($subcat5 as $category) {
                                                                    wcdc_getSubCat($category);
                                                                    //--------- start sub level 6 category ----------
                                                                    $argssub6 = array('type' => 'product', 'parent' => $category->term_id, 'hide_empty' => 0, 'taxonomy' => 'product_cat');
                                                                    $subcat6 = get_categories($argssub6);
                                                                    if (!empty($subcat6)) {
                                                                        echo '<ul parent="' . $category->term_id . '" class="sub level-6">';
                                                                        foreach ($subcat6 as $category) {
                                                                            wcdc_getSubCat($category);

                                                                            echo '</div></li>';
                                                                        }
                                                                        echo '</ul>';
                                                                    } //-------------- end if sub level 6 cat not empty ----------
                                                                    echo '</div></li>';
                                                                }
                                                                echo '</ul>';
                                                            } //-------------- end if sub level 5 cat not empty ----------
                                                            echo '</div></li>';
                                                        }
                                                        echo '</ul>';
                                                    } //-------------- end if sub level 4 cat not empty ----------
                                                    echo '</div></li>';
                                                }
                                                echo '</ul>';
                                            } //-------------- end if sub level 3 cat not empty ----------
                                            echo '</div></li>';
                                        }
                                        echo '</ul>';
                                    } //-------------- end if sub level 2 cat not empty ----------
                                    echo '</div></li>';
                                }
                                echo '</ul>';
                            } //-------------- end if sub level 1 cat not empty ----------
                            echo '</div></li>';
                        }
                        echo '</ul></div></div>';
                        ?>

                    </div>
                    <div class="add-wrap">
                        <?php
                        /** @ignore */
                        do_action('wcdc_admin_after_product_category_tree');
                        ?>
                    </div>
                </div>
                <div id="col-left">
                    <div class="col-wrap">
                        <p><?php _e('Product categories for your store can be managed here.', 'wc-disable-categories'); ?></p>
                        <div class="form-wrap">
                            <h3><?php _e('Add New Product Category', 'wc-disable-categories'); ?></h3>

                            <form class="validate" action="<?php echo get_admin_url(); ?>admin.php?page=togi_woo_categories" method="post" id="addtag">
                                <div class="form-field form-required term-name-wrap">
                                    <label for="tag-name"><?php _e('Name', 'wc-disable-categories'); ?></label>
                                    <input type="text" aria-required="true" size="40" value="" id="tag-name" name="tag-name">
                                    <p><?php _e('The name is how it appears on your site.', 'wc-disable-categories'); ?></p>
                                </div>
                                <div class="form-field term-slug-wrap">
                                    <label for="tag-slug"><?php _e('Slug', 'wc-disable-categories'); ?></label>
                                    <input type="text" size="40" value="" id="tag-slug" name="slug">
                                    <p><?php _e('The "slug" is the URL-friendly version of the name. It is usually all lowercase and contains only letters, numbers, and hyphens.', 'wc-disable-categories'); ?></p>
                                </div>
                                <div class="form-field term-parent-wrap">
                                    <label for="parent"><?php _e('Parent', 'wc-disable-categories'); ?></label>
                                    <select class="postform" id="parent" name="parent">
                                        <option value="0"><?php _e('None', 'wc-disable-categories'); ?></option>
                                        <?php
                                        $terms = get_terms('product_cat', array('hide_empty' => 0, 'hierarchical' => true));

                                        if (!empty($terms) && !is_wp_error($terms)) {
                                            foreach ($terms as $term) {
                                                echo '<option value="' . $term->term_id . '">' . esc_html($term->name) . '</option>';
                                            }
                                        }
                                        ?>
                                    </select>
                                </div>
                                <div class="form-field term-description-wrap">
                                    <label for="tag-description"><?php _e('Description', 'wc-disable-categories'); ?></label>
                                    <textarea cols="40" rows="5" id="tag-description" name="description"></textarea>
                                    <p><?php _e('The description is not prominent by default; however, some themes may show it.', 'wc-disable-categories'); ?></p>
                                </div>

                                <p class="submit"><input type="submit" value="<?php _e('Add New Product Category', 'wc-disable-categories'); ?>" class="button button-primary" id="submit" name="submit"></p></form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <?php
    }

}
?>