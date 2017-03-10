<?php
/**
 * SCREETS © 2016
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
 
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * ChatX_Install Class.
 */
class ChatX_Install {

	/**
	 * Hook in tabs.
	 */
	public static function init() {
		add_action( 'init', array( __CLASS__, 'check_version' ), 5 );
	}


	/**
	 * Check the plugin version and run the updater if it is required.
	 *
	 */
	public static function check_version() {
		
		if ( ! defined( 'IFRAME_REQUEST' ) && get_option( 'scx_version' ) !== SCX_VERSION ) {
			self::install();
			do_action( 'scx_updated' );
		}

	}

	/**
	 * Install the plugin.
	 */
	public static function install() {
		global $wpdb;

		if ( ! defined( 'SCX_INSTALLING' ) ) {
			define( 'SCX_INSTALLING', true );
		}

		// Setup the plugin basics
		self::setup();

		// Queue upgrades/setup wizard
		$current_version = get_option( 'scx_version', null );

		// Update the plugin version
		self::update_version();

		// Flush rules after install
		flush_rewrite_rules();

		// Trigger action
		do_action( 'scx_installed' );

	}

	/**
	 * Setup the plugin.
	 */
	private static function setup() {

		// Register operator role
		add_role(
			'cx_op',
			'Chat X Operator',
			array(
				'read' => true, // True allows that capability
				'edit_posts' => true,
				'delete_posts' => false // Use false to explicitly deny
			)
		);

		// Get roles
		$admin_role = get_role( 'administrator' );
		$op_role = get_role( 'cx_op' );

		// Add chat capability to admin and operators
		$admin_role->add_cap( 'scx_answer_visitor' );
		$admin_role->add_cap( 'scx_see_logs' );
		$admin_role->add_cap( 'scx_manage_chat_options' );

		$op_role->add_cap( 'scx_answer_visitor' );
		$op_role->add_cap( 'scx_see_logs' );

	}

	/**
	 * Update the plugin version to current.
	 */
	private static function update_version() {
		delete_option( 'scx_version' );
		add_option( 'scx_version', SCX_VERSION );
	}

	/**
	 * Update DB version to current.
	 */
	private static function update_db_version( $version = null ) {
		delete_option( 'scx_db_version' );
		add_option( 'scx_db_version', is_null( $version ) ? SCX_VERSION : $version );
	}

}

ChatX_Install::init();
