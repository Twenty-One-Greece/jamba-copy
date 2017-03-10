<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      2.0.0
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      2.0.0
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/includes
 * @author     Frédéric GILLES
 */
class FG_Joomla_to_WordPress_Premium {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      FG_Joomla_to_WordPress_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    2.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    2.0.0
	 */
	public function __construct() {

		$this->plugin_name = 'fgj2wpp';
		$this->parent_plugin_name = 'fg-joomla-to-wordpress';
		$this->version = '3.21.0';

		$this->load_dependencies();
		$this->set_locale();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - FG_Joomla_to_WordPress_Loader. Orchestrates the hooks of the plugin.
	 * - FG_Joomla_to_WordPress_i18n. Defines internationalization functionality.
	 * - FG_Joomla_to_WordPress_Admin. Defines all hooks for the admin area.
	 * - FG_Joomla_to_WordPress_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fg-joomla-to-wordpress-loader.php';

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fg-joomla-to-wordpress-i18n.php';

		// Load Importer API
		require_once ABSPATH . 'wp-admin/includes/import.php';
		if ( !class_exists( 'WP_Importer' ) ) {
			$class_wp_importer = ABSPATH . 'wp-admin/includes/class-wp-importer.php';
			if ( file_exists( $class_wp_importer ) ) {
				require_once $class_wp_importer;
			}
		}

		/**
		 * The class responsible for defining all actions that occur in the admin area.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-admin.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-premium-admin.php';

		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'includes/class-fg-joomla-to-wordpress-tools.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-compatibility.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-modules-check.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-weblinks.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-progressbar.php';

		/**
		 *  Premium features
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-users.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-menus.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-modules.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-joomla10.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-elxis.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-joomla25.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-tags.php';
		
		/**
		 *  FTP functions
		 */
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-base.php';
		require_once ABSPATH . 'wp-admin/includes/class-wp-filesystem-ftpext.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/class-fg-joomla-to-wordpress-ftp.php';

		/**
		 * The class responsible for defining all actions that occur in the public-facing
		 * side of the site.
		 */
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fg-joomla-to-wordpress-redirect.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fg-joomla-to-wordpress-url-rewriting.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fg-joomla-to-wordpress-users-alias.php';
		require_once plugin_dir_path( dirname( __FILE__ ) ) . 'public/class-fg-joomla-to-wordpress-users-authenticate.php';

		$this->loader = new FG_Joomla_to_WordPress_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the FG_Joomla_to_WordPress_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function set_locale() {

		$plugin_i18n = new FG_Joomla_to_WordPress_i18n();
		$plugin_i18n->set_domain( $this->get_plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

		// Load parent translation file
		$plugin_i18n_parent = new FG_Joomla_to_WordPress_i18n();
		$plugin_i18n_parent->set_domain( $this->get_parent_plugin_name() );
		$this->loader->add_action( 'plugins_loaded', $plugin_i18n_parent, 'load_plugin_textdomain' );

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		global $fgj2wpp;
		
		// Add links to the plugin page
		$this->loader->add_filter( 'plugin_action_links_fg-joomla-to-wordpress-premium/fg-joomla-to-wordpress-premium.php', $this, 'plugin_action_links' );
		
		/**
		 * The plugin is hooked to the WordPress importer
		 */
		if ( !defined('WP_LOAD_IMPORTERS') && !defined('DOING_AJAX') ) {
			return;
		}

		$plugin_admin = new FG_Joomla_to_WordPress_Premium_Admin( $this->get_plugin_name(), $this->get_version() );
		$fgj2wpp = $plugin_admin; // Used by add-ons

		$this->loader->add_action( 'admin_init', $plugin_admin, 'init' );
		$this->loader->add_action( 'fgj2wp_post_test_database_connection', $plugin_admin, 'get_joomla_info', 9 );
		$this->loader->add_action( 'load-importer-fgj2wp', $plugin_admin, 'add_help_tab', 20 );
		$this->loader->add_action( 'admin_footer', $plugin_admin, 'display_notices', 20 );
		$this->loader->add_action( 'wp_ajax_fgj2wpp_import', $plugin_admin, 'ajax_importer' );

		/*
		 * Modules checker
		 */
		$plugin_modules_check = new FG_Joomla_to_WordPress_Modules_Check( $plugin_admin );
		$this->loader->add_action( 'fgj2wp_post_test_database_connection', $plugin_modules_check, 'check_modules' );
		
		/*
		 * Weblinks
		 */
		$plugin_weblinks = new FG_Joomla_to_WordPress_Weblinks( $plugin_admin );
		$this->loader->add_action( 'fgj2wp_post_empty_database', $plugin_weblinks, 'empty_links' );
		$this->loader->add_action( 'fgj2wp_post_import', $plugin_weblinks, 'import_links' );
		$this->loader->add_filter( 'fgj2wp_get_database_info', $plugin_weblinks, 'get_database_info' );
		
		/*
		 * Premium features
		 */
		$this->loader->add_action( 'fgj2wp_pre_display_admin_page', $plugin_admin, 'process_admin_page' );
		$this->loader->add_action( 'fgj2wp_post_empty_database', $plugin_admin, 'set_truncate_option' );
		$this->loader->add_action( 'fgj2wp_post_save_plugin_options', $plugin_admin, 'save_premium_options' );
		$this->loader->add_filter( 'fgj2wp_pre_import_check', $plugin_admin, 'pre_import_check' );
		$this->loader->add_action( 'fgj2wp_pre_import', $plugin_admin, 'set_posts_autoincrement' );
		$this->loader->add_filter( 'fgj2wp_pre_insert_post', $plugin_admin, 'add_import_id', 10, 2);
		$this->loader->add_action( 'fgj2wp_post_insert_post', $plugin_admin, 'set_meta_seo', 10, 2);
		$this->loader->add_filter( 'fgj2wp_get_database_info', $plugin_admin, 'get_premium_database_info' );

		/*
		 * Joomla 1.0 / Mambo
		 */
		$plugin_joomla10 = new FG_Joomla_to_WordPress_Joomla10( $plugin_admin );
		$this->loader->add_filter( 'fgj2wp_get_sections_sql', $plugin_joomla10, 'get_sections_sql', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_get_categories_sql', $plugin_joomla10, 'get_categories_sql', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_get_posts_sql', $plugin_joomla10, 'get_posts_sql', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_get_posts_add_extra_cols', $plugin_joomla10, 'add_images_col_in_get_posts', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_pre_process_post', $plugin_joomla10, 'process_mosimage', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_pre_process_post', $plugin_joomla10, 'process_mospagebreak', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_pre_insert_post', $plugin_joomla10, 'process_static_pages', 10, 2 );
		$this->loader->add_filter( 'fgj2wp_get_menus_add_extra_criteria', $plugin_joomla10, 'add_menus_extra_criteria', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_get_menu_item', $plugin_joomla10, 'get_menu_item', 10, 3 );

		/*
		 * Elxis
		 */
		$plugin_joomla10 = new FG_Joomla_to_WordPress_Elxis( $plugin_admin );
		$this->loader->add_filter( 'fgj2wp_get_posts_sql', $plugin_joomla10, 'get_posts_sql', 9, 1 );

		/*
		 * Joomla 2.5
		 */
		$plugin_joomla25 = new FG_Joomla_to_WordPress_Joomla25( $plugin_admin );
		$this->loader->add_filter( 'fgj2wp_get_posts_add_extra_cols', $plugin_joomla25, 'add_images_col_in_get_posts', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_pre_import_media', $plugin_joomla25, 'process_featured_image', 10, 1 );

		/*
		 * Menus
		 */
		$plugin_menus = new FG_Joomla_to_WordPress_Menus( $plugin_admin );
		$this->loader->add_action( 'fgj2wp_post_empty_database', $plugin_menus, 'reset_last_menu_id', 10, 1 );
		$this->loader->add_action( 'fgj2wp_post_import', $plugin_menus, 'import_menus', 50 );
		$this->loader->add_action( 'fgj2wp_post_add_menu', $plugin_menus, 'set_meta_data_from_menu', 10, 2 );
		$this->loader->add_action( 'fgj2wp_post_add_menu', $plugin_menus, 'set_post_slugs_from_menu', 10, 2 );
		$this->loader->add_action( 'fgj2wp_post_import_menus', $plugin_menus, 'set_parent_pages_from_menus', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_get_total_elements_count', $plugin_menus, 'get_total_elements_count' );

		/*
		 * Modules
		 */
		$plugin_modules = new FG_Joomla_to_WordPress_Modules( $plugin_admin );
		$this->loader->add_action( 'fgj2wp_post_empty_database', $plugin_modules, 'reset_last_module_id', 10, 1 );
		$this->loader->add_action( 'fgj2wp_post_import', $plugin_modules, 'import_modules', 50 );
		$this->loader->add_filter( 'fgj2wp_get_total_elements_count', $plugin_modules, 'get_total_elements_count' );

		/*
		 * Tags
		 */
		$plugin_tags = new FG_Joomla_to_WordPress_Tags( $plugin_admin );
		$this->loader->add_filter( 'fgj2wp_pre_insert_post', $plugin_tags, 'import_tags', 10, 2 );
		$this->loader->add_filter( 'fgj2wp_import_notices', $plugin_tags, 'display_tags_count' );
		$this->loader->add_filter( 'fgj2wp_get_wp_term_from_joomla_url', $plugin_tags, 'get_tag_from_joomla_url', 10, 2 );

		/*
		 * Users
		 */
		$plugin_users = new FG_Joomla_to_WordPress_Users( $plugin_admin );
		$this->loader->add_action( 'fgj2wp_post_empty_database', $plugin_users, 'delete_users', 10, 1 );
		$this->loader->add_action( 'fgj2wp_pre_import', $plugin_users, 'get_users_array' );
		$this->loader->add_filter( 'fgj2wp_pre_insert_post', $plugin_users, 'import_author', 10, 2 );
		$this->loader->add_action( 'fgj2wp_post_insert_post', $plugin_users, 'import_author_alias', 10, 2 );
		$this->loader->add_action( 'fgj2wp_post_import', $plugin_users, 'import_users' );
		$this->loader->add_filter( 'fgj2wp_get_posts_add_extra_cols', $plugin_users, 'add_user_cols_in_get_posts', 10, 1 );
		$this->loader->add_filter( 'fgj2wp_pre_display_joomla_info', $plugin_users, 'get_users_info' );
		$this->loader->add_filter( 'fgj2wp_get_total_elements_count', $plugin_users, 'get_total_elements_count' );

		/*
		 * FTP connection
		 */
		if ( defined('FGJ2WPP_USE_FTP') ) {
			$plugin_ftp = new FG_Joomla_to_WordPress_FTP( $plugin_admin );
			$this->loader->add_filter( 'fgj2wp_post_display_settings_options', $plugin_ftp, 'display_ftp_settings' );
			$this->loader->add_filter( 'fgj2wp_post_save_plugin_options', $plugin_ftp, 'save_ftp_settings' );
			$this->loader->add_action( 'fgj2wp_dispatch', $plugin_ftp, 'test_ftp_connection', 10, 1 );
		}
	}

	/**
	 * Customize the links on the plugins list page
	 *
	 * @param array $links Links
	 * @return array Links
	 */
	public function plugin_action_links($links) {
		// Add the import link
		$import_link = '<a href="admin.php?import=fgj2wp">'. __('Import', $this->plugin_name) . '</a>';
		array_unshift($links, $import_link);
		return $links;
	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    2.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		/*
		 * Users alias
		 */
		$plugin_users_alias = new FG_Joomla_to_WordPress_Users_Alias();
		$this->loader->add_filter('the_author', $plugin_users_alias, 'the_author');

		/*
		 * URL redirect
		 */
		$plugin_redirect = new FG_Joomla_to_WordPress_Redirect();
		$this->loader->add_action( 'fgj2wp_post_empty_database', $plugin_redirect, 'empty_redirects' );
		$this->loader->add_action( 'fgj2wpp_post_404_redirect', $plugin_redirect, 'process_url' );

		/*
		 * URL rewriting
		 */
		new FG_Joomla_to_WordPress_URL_Rewriting();

		/*
		 * Users authentication
		 */
		$plugin_users_authenticate = new FG_Joomla_to_WordPress_Users_Authenticate();
		$this->loader->add_filter('authenticate', $plugin_users_authenticate, 'auth_signon', 30, 3);
	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    2.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The name of the parent plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     2.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_parent_plugin_name() {
		return $this->parent_plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     2.0.0
	 * @return    FG_Joomla_to_WordPress_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     2.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
