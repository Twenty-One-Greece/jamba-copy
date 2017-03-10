<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      2.0.0
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/admin
 * @author     Frédéric GILLES
 */
class FG_Joomla_to_WordPress_Premium_Admin extends FG_Joomla_to_WordPress_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	public $premium_options = array();				// Options specific for the Premium version
	
	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    2.0.0
	 * @param    string    $plugin_name       The name of this plugin.
	 * @param    string    $version           The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		parent::__construct($plugin_name, $version);
		
		$this->faq_url = 'https://www.fredericgilles.net/fg-joomla-to-wordpress/faq/';

	}

	/**
	 * Initialize the plugin
	 */
	public function init() {
		$this->deactivate_free_version();

		// Default options values
		$this->premium_options = array(
			'create_submenus'			=> false,
			'import_meta_seo'			=> false,
			'get_metadata_from_menu'	=> false,
			'get_slug_from_menu'		=> false,
			'keep_joomla_id'			=> false,
			'url_redirect'				=> false,
			'skip_categories'			=> false,
			'skip_articles'				=> false,
			'skip_weblinks'				=> false,
			'skip_users'				=> false,
			'skip_menus'				=> false,
			'skip_modules'				=> false,
		);
		$this->premium_options = apply_filters('fgj2wpp_post_init_premium_options', $this->premium_options);
		$options = get_option('fgj2wpp_options');
		if ( is_array($options) ) {
			$this->premium_options = array_merge($this->premium_options, $options);
		}

		parent::init();
	}

	/**
	 * Deactivate the free version of FG Joomla to WordPress to avoid conflicts between both plugins
	 */
	private function deactivate_free_version() {
		deactivate_plugins( 'fg-joomla-to-wordpress/fg-joomla-to-wordpress.php' );
	}
	
	/**
	 * Add information to the admin page
	 * 
	 * @param array $data
	 * @return array
	 */
	public function process_admin_page($data) {
		$data['title'] = __('Import Joomla Premium (FG)', $this->plugin_name);
		$data['description'] = __('This plugin will import sections, categories, posts, tags, medias (images, attachments), web links, navigation menus and users from a Joomla database into WordPress.', $this->plugin_name);
		$data['description'] .= "<br />\n" . sprintf(__('For any issue, please read the <a href="%s" target="_blank">FAQ</a> first.', $this->plugin_name), $this->faq_url);

		// Premium options
		foreach ( $this->premium_options as $key => $value ) {
			$data[$key] = $value;
		}
		return $data;
	}

	/**
	 * Get the WordPress database info
	 * 
	 * @param string $database_info Database info
	 * @return string Database info
	 */
	public function get_premium_database_info($database_info) {
		// Users
		$count_users = count_users();
		$users_count = $count_users['total_users'];
		$database_info .= sprintf(_n('%d user', '%d users', $users_count, $this->plugin_name), $users_count) . "<br />";

		// Navigation menus
		$menus_count = $this->count_posts('nav_menu_item');
		$database_info .= sprintf(_n('%d menu item', '%d menu items', $menus_count, $this->plugin_name), $menus_count) . "<br />";

		return $database_info;
	}
	
	/**
	 * Save the Premium options
	 *
	 */
	public function save_premium_options() {
		$this->premium_options = array_merge($this->premium_options, $this->validate_form_premium_info());
		update_option('fgj2wpp_options', $this->premium_options);
	}

	/**
	 * Validate POST info
	 *
	 * @return array Form parameters
	 */
	private function validate_form_premium_info() {
		$result = array(
			'create_submenus'			=> filter_input(INPUT_POST, 'create_submenus', FILTER_VALIDATE_BOOLEAN),
			'import_meta_seo'			=> filter_input(INPUT_POST, 'import_meta_seo', FILTER_VALIDATE_BOOLEAN),
			'get_metadata_from_menu'	=> filter_input(INPUT_POST, 'get_metadata_from_menu', FILTER_VALIDATE_BOOLEAN),
			'get_slug_from_menu'		=> filter_input(INPUT_POST, 'get_slug_from_menu', FILTER_VALIDATE_BOOLEAN),
			'keep_joomla_id'			=> filter_input(INPUT_POST, 'keep_joomla_id', FILTER_VALIDATE_BOOLEAN),
			'url_redirect'				=> filter_input(INPUT_POST, 'url_redirect', FILTER_VALIDATE_BOOLEAN),
			'skip_categories'			=> filter_input(INPUT_POST, 'skip_categories', FILTER_VALIDATE_BOOLEAN),
			'skip_articles'				=> filter_input(INPUT_POST, 'skip_articles', FILTER_VALIDATE_BOOLEAN),
			'skip_weblinks'				=> filter_input(INPUT_POST, 'skip_weblinks', FILTER_VALIDATE_BOOLEAN),
			'skip_users'				=> filter_input(INPUT_POST, 'skip_users', FILTER_VALIDATE_BOOLEAN),
			'skip_menus'				=> filter_input(INPUT_POST, 'skip_menus', FILTER_VALIDATE_BOOLEAN),
			'skip_modules'				=> filter_input(INPUT_POST, 'skip_modules', FILTER_VALIDATE_BOOLEAN),
		);
		$result = apply_filters('fgj2wpp_validate_form_premium_info', $result);
		return $result;
	}

	/**
	 * Set the truncate option in order to keep use the "keep Joomla ID" feature
	 * 
	 * @param string $action	newposts = removes only new imported posts
	 * 							all = removes all
	 */
	public function set_truncate_option($action) {
		if ( $action == 'all' ) {
			update_option('fgj2wp_truncate_posts_table', 1);
		} else {
			delete_option('fgj2wp_truncate_posts_table');
		}
	}

	/**
	 * Actions to do before the import
	 * 
	 */
	public function pre_import_check() {
		if ( $this->premium_options['keep_joomla_id'] ) {
			if ( !get_option('fgj2wp_truncate_posts_table') ) { 
				$this->display_admin_error(__('You need to fully empty the database if you want to use the "Keep Joomla ID" feature.', 'fgj2wpp'));
				return false;
			}
		}
		return true;
	}

	/**
	 * Set the posts table autoincrement to the last Joomla ID + 100
	 * 
	 */
	public function set_posts_autoincrement() {
		global $wpdb;
		if ( $this->premium_options['keep_joomla_id'] ) {
			$last_joomla_article_id = $this->get_last_joomla_article_id() + 100;
			$sql = "ALTER TABLE $wpdb->posts AUTO_INCREMENT = $last_joomla_article_id";
			$wpdb->query($sql);
		}
	}

	/**
	 * Get the last Joomla article ID
	 *
	 * @return int Last Joomla article ID
	 */
	private function get_last_joomla_article_id() {
		$prefix = $this->plugin_options['prefix'];
		$sql = "
			SELECT max(id) AS max_id
			FROM ${prefix}content
		";
		$result = $this->joomla_query($sql);
		$max_id = isset($result[0]['max_id'])? $result[0]['max_id'] : 0;
		return $max_id;		
	}

	/**
	 * Keep the Joomla ID
	 * 
	 * @param array $new_post New post
	 * @param array $post Joomla Post
	 * @return array Post
	 */
	public function add_import_id($new_post, $post) {
		if ( $this->premium_options['keep_joomla_id'] ) {
			$new_post['import_id'] = $post['id'];
		}
		return $new_post;
	}

	/**
	 * Sets the meta fields used by the SEO by Yoast plugin
	 * 
	 * @param int $new_post_id WordPress ID
	 * @param array $post Joomla Post
	 */
	public function set_meta_seo($new_post_id, $post) {
		if ( $this->premium_options['import_meta_seo'] ) {
			if ( array_key_exists('metatitle', $post) && !empty($post['metatitle']) ) {
				update_post_meta($new_post_id, '_yoast_wpseo_title', $post['metatitle']);
			}
			if ( array_key_exists('metadesc', $post) && !empty($post['metadesc']) ) {
				update_post_meta($new_post_id, '_yoast_wpseo_metadesc', $post['metadesc']);
			}
			if ( array_key_exists('metakey', $post) && !empty($post['metakey']) ) {
				update_post_meta($new_post_id, '_yoast_wpseo_metakeywords', $post['metakey']);
			}
			if ( array_key_exists('canonical', $post) && !empty($post['canonical']) ) {
				update_post_meta($new_post_id, '_yoast_wpseo_canonical', $post['canonical']);
			}
		}
	}

	/**
	 * Get the imported users mapped with their Joomla IDs
	 * 
	 * @return array [Joomla user ID => WP user ID]
	 */
	public function get_imported_users() {
		global $wpdb;
		$tab_users = array();
		$sql = "
			SELECT user_id, meta_value
			FROM $wpdb->usermeta
			WHERE meta_key = '_fgj2wp_old_user_id'
		";
		foreach ( $wpdb->get_results($sql) as $usermeta ) {
			$tab_users[$usermeta->meta_value] = $usermeta->user_id;
		}
		return $tab_users;
	}

}
