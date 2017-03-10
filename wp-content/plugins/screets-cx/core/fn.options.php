<?php
/**
 * SCREETS © 2016
 *
 * Plugin Options
 *
 * COPYRIGHT © 2016 Screets d.o.o. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 *
 * @package Chat X
 * @author Screets
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;


require SCX_PATH . '/core/metaboxes.php';
require SCX_PATH . '/core/options.php';

/**
 * Plugin options (Hook Titan Framework)
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_hook_options() {

	global $ChatX;

	// Framework
	$fw = TitanFramework::getInstance( SCX_SLUG );

	// Get all custom post types (public ones)
	$post_types = get_post_types( array( '_builtin' => false, 'public' => true ) );

	// Add default post types
	array_push( $post_types, 'post', 'page' );

	//
	// Metaboxes
	//
	/*$meta_posts = $fw->createMetaBox( array(
		'name' => SCX_NAME,
		'post_type' => $post_types
	));*/

	$meta_topics = $fw->createMetaBox( array(
		'name' => SCX_NAME,
		'post_type' => 'scx_support_cat'
	));

	// Get post metaboxes
	foreach( fn_scx_get_metaboxes( 'post' ) as $m ) {
		$meta_posts->createOption( $m );
	}

	foreach( fn_scx_get_metaboxes( 'topic' ) as $m ) {
		$meta_topics->createOption( $m );
	}

	//
	// Options page
	//
	$opts = $fw->createAdminPanel( array(
		'name' => 'Options',
		'title' => '<img src="' . SCX_URL . '/assets/img/night-bird-logo-100px.png" class="scx-logo" style="float:left; max-width:50px; margin-right:15px;">' . sprintf( __( '%s Options', 'schat' ), 'Screets Chat X' ),
		'desc' => '<strong style="font-style:normal;">' . SCX_NAME . ' - ' . SCX_EDITION . '</strong> - <span class="scx-highlight">Version ' . SCX_VERSION . '</span> &nbsp;&dash;&nbsp; <a href="http://apps.screets.org/chat-x-night-bird/changelog/" target="_blank">Changelog</a> <span class="scx-ico-new-win" style="color:#ccc;"></span>',
		'parent' => 'scx_console',
		'capability' => 'scx_manage_chat_options',
		'position' => 100
	));

	// Create option tabs
	$tab_general = $opts->createTab( array( 'name'	=> __( 'General', 'schat' ) ));
	$tab_site_info = $opts->createTab( array( 'name'	=> __( 'Site info', 'schat' ) ));
	$tab_design = $opts->createTab( array( 'name'	=> __( 'Design', 'schat' ) ));
	$tab_popups = $opts->createTab( array( 'name'	=> __( 'Popups', 'schat' ) ));
	// $tab_templates = $opts->createTab( array( 'name'	=> __( 'Templates', 'schat' ) ));
	$tab_users = $opts->createTab( array( 'name'	=> __( 'Users', 'schat' ) ));
	$tab_integrations = $opts->createTab( array( 'name'	=> __( 'Integrations', 'schat' ) ));
	$tab_advanced = $opts->createTab( array( 'name'	=> __( 'Advanced', 'schat' ) ));
	$tab_support = $opts->createTab( array(
		'name'	=> __( 'Support', 'schat' ),
		'desc' =>'<strong>' . SCX_NAME . ' ' . SCX_EDITION . '</strong><br><em>Version: ' . SCX_VERSION . '</em> &nbsp;&bull;&nbsp; <small class="description"><a href="http://codecanyon.net/item/chat-x-wordpress-chat-plugin-for-sales-support/6639389?ref=screets" target="_blank">Check the latest version</a> <i class="scx-ico-new-win"></i> &nbsp; <a href="http://screets.org/apps/chatx/forums" target="_blank">Go forum</a> <i class="scx-ico-new-win"></i></small><br><small><strong>PHP Version:</strong> ' . phpversion() . '</small><br><small><strong>Language folder:</strong> ' . WP_LANG_DIR . '</small>'
				.'<iframe frameborder="0" src="http://screets.org/apps/chatx/forums/" style="height: 100vh; width:100%;"></iframe>'
	));

	// Get admin options
	foreach( fn_scx_get_opts() as $k => $opt_list ) {
		foreach( $opt_list as $opt) {
			switch( $k ) {
				case 'general':
					$tab_general->createOption( $opt ); break;

				case 'site-info':
					$tab_site_info->createOption( $opt ); break;

				case 'design':
					$tab_design->createOption( $opt ); break;

				case 'popups':
					$tab_popups->createOption( $opt ); break;

				/*case 'templates':
					$tab_templates->createOption( $opt ); break;*/

				case 'users':
					$tab_users->createOption( $opt ); break;

				case 'integrations':
					$tab_integrations->createOption( $opt ); break;

				case 'advanced':
					$tab_advanced->createOption( $opt ); break;

				case 'support':
					$tab_support->createOption( $opt ); break;

			}

			// Translatable options
			if( !empty( $opt['translate'] ) ) {
				$multiline = ( $opt['type'] == 'textarea' ) ? true : false; // Translation text field is multiline?
				$name = ( !empty( $opt['name'] ) ) ? $opt['name'] : $opt['default'];


				$ChatX->__opts[$opt['id']] = array(
					'name' => $name,
					'multiline' => $multiline 
				);

			}
			
		}
	}

	// Save button
	$opts->createOption( array( 'type' => 'save' ) );

}


/**
 * Save admin options (Hook Titan Framework)
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_hook_options_saved() {

	// Update operator capabilities
	fn_scx_update_op_caps();


}

// Hook Titan Framework
add_action( 'tf_create_options', 'fn_scx_hook_options' );
add_action( 'tf_admin_options_saved_' . SCX_SLUG, 'fn_scx_hook_options_saved' );