<?php
/**
* SCREETS © 2016
*
* COPYRIGHT © 2016 Screets d.o.o. All rights reserved.
* This  is  commercial  software,  only  users  who have purchased a valid
* license  and  accept  to the terms of the  License Agreement can install
* and use this program.
*
* @package SCX
* @author Screets
*
*/
if ( ! defined( 'ABSPATH' ) ) { exit; }

/**
 * Admin navigation menus
 */
add_action( 'admin_init', array( 'SCX_NavMenu', 'init' ) );

class SCX_NavMenu {
    const LANG = 'scx';

    public static function init() {
        $class = __CLASS__;
        new $class;
    }

    public function __construct() {
        // Abort if not on the nav-menus.php admin UI page - avoid adding elsewhere
        global $pagenow;
        
        if ( 'nav-menus.php' !== $pagenow )
            return;

        $this->add_some_meta_box();
    }

    /**
     * Adds the meta box container
     */
    public function add_some_meta_box(){
        add_meta_box(
            'scx-links',
            __( 'Live Chat', self::LANG ),
            array( $this, 'render_meta_box_content' ),
            'nav-menus', // important !!!
            'side', // important, only side seems to work!!!
            'low'
        );
    }

    /**
     * Render Meta Box content
     */
    public function render_meta_box_content() {
        $menu_links = array(
            'open-popup' => array(
                'title' => __( 'Talk to us', 'scx' ),
                'desc' => __( 'Shows up chat chat popup if it is hidden.', 'scx' )
            )
        );
        ?>
        <div id="posttype-scx-links" class="posttypediv">
            <div id="tabs-panel-scx-links" class="tabs-panel tabs-panel-active">
                <ul id="scx-links-checklist" class="categorychecklist form-no-clear">
                    <?php
                    $i = -1;
                    foreach ( $menu_links as $id => $link ) {
                    ?>
                    <li>
                        <label class="menu-item-title">
                            <input type="checkbox" class="menu-item-checkbox" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-object-id]" value="<?php echo esc_attr( $i ); ?>" /> <?php echo esc_html( $link['title'] ); ?>
                            <br><small class="description"><?php echo $link['desc']; ?></small>
                        </label>
                        <input type="hidden" class="menu-item-type" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-type]" value="custom" />
                        <input type="hidden" class="menu-item-title" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-title]" value="<?php echo esc_html( $link['title'] ); ?>" />
                        <input type="hidden" class="menu-item-url" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-url]" value="#<?php echo esc_attr( $id ); ?>" />
                        <input type="hidden" class="menu-item-classes" name="menu-item[<?php echo esc_attr( $i ); ?>][menu-item-classes]" value="scx-action-menu" />
                    </li>
                    <?php
                        $i --;
                    }
                    ?>
                </ul>

                <p class="button-controls">
                    <span class="list-controls">
                        <a href="<?php echo admin_url( 'nav-menus.php?page-tab=all&selectall=1#posttype-scx-links' ); ?>" class="select-all"><?php _e( 'Select All', 'scx' ); ?></a>
                    </span>
                    <span class="add-to-menu">
                        <input type="submit" class="button-secondary submit-add-to-menu right" value="<?php esc_attr_e( 'Add to Menu', 'scx' ); ?>" name="add-post-type-menu-item" id="submit-posttype-scx-links">
                        <span class="spinner"></span>
                    </span>
                </p>
            </div>
        </div>
    <?php
    }
}
