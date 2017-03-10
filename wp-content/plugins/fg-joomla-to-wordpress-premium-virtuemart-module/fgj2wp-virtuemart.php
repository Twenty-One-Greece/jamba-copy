<?php
/**
 * Plugin Name: FG Joomla to WordPress Premium Virtuemart module
 * Depends:		FG Joomla to WordPress Premium
 * Plugin Uri:  https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * Description: A plugin to migrate Virtuemart content (Joomla) to WooCommerce (WordPress)
 * 				Needs the plugin «FG Joomla to WordPress Premium» to work
 * Version:     2.10.1
 * Author:      Frédéric GILLES
 */

// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

require_once 'url-rewriting.php';
new FG_Joomla_to_WordPress_Premium_Virtuemart_URL_Rewriting();

add_action( 'admin_init', 'fgj2wp_virtuemart_test_requirements' );

if ( !function_exists( 'fgj2wp_virtuemart_test_requirements' ) ) {
	function fgj2wp_virtuemart_test_requirements() {
		new fgj2wp_virtuemart_requirements();
	}
}

if ( !class_exists('fgj2wp_virtuemart_requirements', false) ) {
	class fgj2wp_virtuemart_requirements {
		private $parent_plugin = 'fg-joomla-to-wordpress-premium/fg-joomla-to-wordpress-premium.php';
		private $required_premium_version = '3.12.0';

		public function __construct() {
			load_plugin_textdomain( 'fgj2wp_virtuemart', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
			if ( !is_plugin_active($this->parent_plugin) ) {
				add_action( 'admin_notices', array($this, 'fgj2wp_virtuemart_error') );
			} else {
				$plugin_data = get_plugin_data(WP_PLUGIN_DIR . '/' . $this->parent_plugin);
				if ( !$plugin_data or version_compare($plugin_data['Version'], $this->required_premium_version, '<') ) {
					add_action( 'admin_notices', array($this, 'fgj2wp_virtuemart_version_error') );
				}
			}
		}
		
		/**
		 * Print an error message if the Premium plugin is not activated
		 */
		function fgj2wp_virtuemart_error() {
			echo '<div class="error"><p>[fgj2wp_virtuemart] '.__('The Virtuemart module needs the «FG Joomla to WordPress Premium» plugin to work. Please install and activate <strong>FG Joomla to WordPress Premium</strong>.', 'fgj2wp_virtuemart').'<br /><a href="https://www.fredericgilles.net/fg-joomla-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-joomla-to-wordpress/</a></p></div>';
		}
		
		/**
		 * Print an error message if the Premium plugin is not at the required version
		 */
		function fgj2wp_virtuemart_version_error() {
			printf('<div class="error"><p>[fgj2wp_virtuemart] '.__('The Virtuemart module needs at least the <strong>version %s</strong> of the «FG Joomla to WordPress Premium» plugin to work. Please install and activate <strong>FG Joomla to WordPress Premium</strong> at least the <strong>version %s</strong>.', 'fgj2wp_virtuemart').'<br /><a href="https://www.fredericgilles.net/fg-joomla-to-wordpress/" target="_blank">https://www.fredericgilles.net/fg-joomla-to-wordpress/</a></p></div>', $this->required_premium_version, $this->required_premium_version);
		}
	}
}

if ( !defined('WP_LOAD_IMPORTERS') && !defined('DOING_AJAX') ) return;

add_action( 'plugins_loaded', 'fgj2wp_virtuemart_load', 25 );

if ( !function_exists( 'fgj2wp_virtuemart_load' ) ) {
	function fgj2wp_virtuemart_load() {
		if ( !defined('FGJ2WPP_LOADED') ) return;

		load_plugin_textdomain( 'fgj2wp_virtuemart', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
		
		global $fgj2wpp;
		new fgj2wp_virtuemart($fgj2wpp);
	}
}

if ( !class_exists('fgj2wp_virtuemart', false) ) {
	class fgj2wp_virtuemart {
		
		public $vm_users_count = 0;
		public $vm_images_count = 0;
		private $vm_version = 0;
		private $vm_options = array();
		private $brand_taxonomy = '';
		private $language_code;
		private $product_types = array();
		private $prices = array();
		private $global_tax_rate = 1.0;
		private $stock_handles = array();
		private $attribute_values = array();
		private $imported_brands = array();
		private $tax_classes = array();
		
		/**
		 * Sets up the plugin
		 *
		 */
		public function __construct($plugin) {
			
			$this->plugin = $plugin;
			
			add_filter( 'fgj2wp_pre_display_joomla_info', array($this, 'get_virtuemart_info'), 10, 1 );
			add_action( 'fgj2wp_post_empty_database', array($this, 'delete_woocommerce_data') );
			add_action( 'fgj2wp_pre_import', array ($this, 'set_posts_autoincrement') );
			add_action( 'fgj2wp_pre_import', array ($this, 'remove_keep_joomla_id'));
			add_filter( 'fgj2wp_vm_pre_insert_post', array ($this, 'add_import_id'), 10, 2);
			add_action( 'fgj2wpp_post_import_users', array($this, 'import_users_info'), 10, 1 );
			add_action( 'fgj2wp_post_import', array($this, 'import'), 11);
			add_filter( 'fgj2wp_get_woocommerce_product_categories', array($this, 'get_woocommerce_product_categories') );
			add_action( 'fgj2wp_pre_import_menus', array($this, 'get_imported_products') );
			add_filter( 'fgj2wp_get_menus_add_extra_criteria', array($this, 'add_menus_extra_criteria'), 10, 1 );
			add_filter( 'fgj2wp_get_menu_item', array($this, 'get_menu_item'), 10, 3 );
			
			add_action( 'fgj2wp_import_notices', array($this, 'display_vm_users_count') );
			add_action( 'fgj2wp_import_notices', array($this, 'display_vm_images_count') );
			
			add_filter( 'fgj2wp_pre_display_admin_page', array($this, 'process_admin_page'), 11, 1 );
			add_filter( 'fgj2wp_get_database_info', array ($this, 'get_database_info') );
			add_filter( 'fgj2wp_get_total_elements_count', array ($this, 'get_total_elements_count') );

			add_filter( 'fgj2wpp_post_init_premium_options', array($this, 'init_premium_options'), 10, 1 );
			add_action( 'fgj2wp_post_display_partial_import_options', array($this, 'display_partial_import_options'), 10, 1 );
			add_filter( 'fgj2wpp_validate_form_premium_info', array($this, 'validate_form_premium_info'), 10, 1 );
			add_action( 'fgj2wp_post_display_behavior_options', array($this, 'display_virtuemart_options') );
			add_action( 'fgj2wp_help_options', array($this, 'display_virtuemart_options_help') );
			add_action( 'fgj2wp_post_save_plugin_options', array($this, 'save_virtuemart_options') );
			add_filter( 'fgj2wp_get_wp_post_from_joomla_url', array ($this, 'get_wc_product_from_virtuemart_url'), 10, 2 );
			add_filter( 'fgj2wp_joomfish_pre_insert_post', array($this, 'set_product_translation'), 10, 3 );
			add_filter( 'fgj2wp_joomfish_post_insert_post', array($this, 'set_product_metadata_translation'), 10, 3 );
			
			// Default values
			$this->vm_options = array(
				'vm_price'						=> 'without_tax',
				'vm_first_image_not_in_gallery'	=> false,
				'vm_stock_management'			=> true,
				'keep_vm_id'					=> false,
			);
			$options = get_option('fgj2wp_virtuemart_options');
			if ( is_array($options) ) {
				$this->vm_options = array_merge($this->vm_options, $options);
			}
		}
		
		/**
		 * Init the Premium options
		 *
		 */
		public function init_premium_options($options) {
			$options['skip_products'] = false;
			$options['skip_attributes'] = false;
			$options['skip_variations'] = false;
			$options['skip_orders'] = false;
			$options['skip_reviews'] = false;
			$options['skip_coupons'] = false;
			$options['skip_manufacturers'] = false;
			return $options;
		}
		
		/**
		 * Save the Virtuemart options
		 *
		 */
		public function save_virtuemart_options() {
			$this->vm_options = array_merge($this->vm_options, $this->validate_form_info());
			update_option('fgj2wp_virtuemart_options', $this->vm_options);
		}
		
		/**
		 * Validate POST info
		 *
		 * @return array Form parameters
		 */
		private function validate_form_info() {
			$vm_price = filter_input(INPUT_POST, 'vm_price', FILTER_SANITIZE_STRING);
			return array(
				'vm_price'						=> ($vm_price == 'with_tax')? $vm_price: 'without_tax',
				'vm_first_image_not_in_gallery'	=> filter_input(INPUT_POST, 'vm_first_image_not_in_gallery', FILTER_VALIDATE_BOOLEAN),
				'vm_stock_management'			=> filter_input(INPUT_POST, 'vm_stock_management', FILTER_VALIDATE_BOOLEAN),
				'keep_vm_id'					=> filter_input(INPUT_POST, 'keep_vm_id', FILTER_VALIDATE_BOOLEAN),
			);
		}
		
		/**
		 * Validate Premium POST info
		 *
		 * @param array $options Options
		 * @return array Form options
		 */
		public function validate_form_premium_info($options) {
			$options['skip_products'] = filter_input(INPUT_POST, 'skip_products', FILTER_VALIDATE_BOOLEAN);
			$options['skip_attributes'] = filter_input(INPUT_POST, 'skip_attributes', FILTER_VALIDATE_BOOLEAN);
			$options['skip_variations'] = filter_input(INPUT_POST, 'skip_variations', FILTER_VALIDATE_BOOLEAN);
			$options['skip_orders'] = filter_input(INPUT_POST, 'skip_orders', FILTER_VALIDATE_BOOLEAN);
			$options['skip_reviews'] = filter_input(INPUT_POST, 'skip_reviews', FILTER_VALIDATE_BOOLEAN);
			$options['skip_coupons'] = filter_input(INPUT_POST, 'skip_coupons', FILTER_VALIDATE_BOOLEAN);
			$options['skip_manufacturers'] = filter_input(INPUT_POST, 'skip_manufacturers', FILTER_VALIDATE_BOOLEAN);
			return $options;
		}
		
		/**
		 * Get Virtuemart info
		 *
		 * @param string $message Message to display when displaying Joomla info
		 * @return string Message
		 */
		public function get_virtuemart_info($message) {
			$this->vm_version = $this->get_virtuemart_version();
			
			// Virtuemart products
			$virtuemart_products_count = $this->get_virtuemart_products_count();
			$message .= sprintf(_n('%d Virtuemart product', '%d Virtuemart products', $virtuemart_products_count, 'fgj2wp_virtuemart'), $virtuemart_products_count) . '<br />';
			
			
			// Virtuemart orders
			$virtuemart_orders_count = $this->get_virtuemart_orders_count();
			$message .= sprintf(_n('%d Virtuemart order', '%d Virtuemart orders', $virtuemart_orders_count, 'fgj2wp_virtuemart'), $virtuemart_orders_count) . '<br />';
			
			return $message;
		}
		
		/**
		 * Update the number of total elements found in Joomla
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			$this->vm_version = $this->get_virtuemart_version();
			if ( !isset($this->plugin->premium_options['skip_products']) || !$this->plugin->premium_options['skip_products'] ) {
				$count += $this->get_virtuemart_product_categories_count();
				$count += $this->get_virtuemart_products_count();
			}
			if ( !isset($this->plugin->premium_options['skip_orders']) || !$this->plugin->premium_options['skip_orders'] ) {
				$count += $this->get_virtuemart_orders_count();
			}
			if ( !isset($this->plugin->premium_options['skip_reviews']) || !$this->plugin->premium_options['skip_reviews'] ) {
				$count += $this->get_virtuemart_reviews_count();
			}
			if ( !isset($this->plugin->premium_options['skip_coupons']) || !$this->plugin->premium_options['skip_coupons'] ) {
				$count += $this->get_virtuemart_coupons_count();
			}
			return $count;
		}
		
		/**
		 * Get the number of Virtuemart product categories
		 * 
		 * @return int Number of products categories
		 */
		private function get_virtuemart_product_categories_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$table = 'vm_category';
			} else {
				// Joomla 2.5
				$table = 'virtuemart_categories';
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}${table}
			";
			$result = $this->plugin->joomla_query($sql);
			$count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $count;
		}
		
		/**
		 * Get the number of Virtuemart products
		 * 
		 * @return int Number of products
		 */
		private function get_virtuemart_products_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				$table = 'vm_product';
			} else {
				$table = 'virtuemart_products';
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}${table}
			";
			$result = $this->plugin->joomla_query($sql);
			$count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $count;
		}
		
		/**
		 * Get the number of Virtuemart orders
		 * 
		 * @return int Number of orders
		 */
		private function get_virtuemart_orders_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				$table = 'vm_orders';
			} else {
				$table = 'virtuemart_orders';
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}${table}
			";
			$result = $this->plugin->joomla_query($sql);
			$count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $count;
		}
		
		/**
		 * Get the number of Virtuemart reviews
		 * 
		 * @return int Number of reviews
		 */
		private function get_virtuemart_reviews_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				$table = 'vm_product_reviews';
			} else {
				$table = 'virtuemart_rating_reviews';
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}${table}
			";
			$result = $this->plugin->joomla_query($sql);
			$count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $count;
		}
		
		/**
		 * Get the number of Virtuemart coupons
		 * 
		 * @return int Number of coupons
		 */
		private function get_virtuemart_coupons_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				$table = 'vm_coupons';
			} else {
				$table = 'virtuemart_coupons';
			}
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}${table}
			";
			$result = $this->plugin->joomla_query($sql);
			$count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $count;
		}
		
		/**
		 * Delete all woocommerce data
		 *
		 */
		public function delete_woocommerce_data() {
			global $wpdb;
			global $wc_product_attributes;
			
			$wpdb->show_errors();
			
			$sql_queries = array();
			$sql_queries[] = <<<SQL
-- Delete WooCommerce attribute taxonomies
TRUNCATE {$wpdb->prefix}woocommerce_attribute_taxonomies
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce tax rates
TRUNCATE {$wpdb->prefix}woocommerce_tax_rates
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce order items
TRUNCATE {$wpdb->prefix}woocommerce_order_items
SQL;

			$sql_queries[] = <<<SQL
-- Delete WooCommerce order item metas
TRUNCATE {$wpdb->prefix}woocommerce_order_itemmeta
SQL;

			// Execute SQL queries
			if ( count($sql_queries) > 0 ) {
				foreach ( $sql_queries as $sql ) {
					$wpdb->query($sql);
				}
			}
			
			// Reset the WC pages flags
			$wc_pages = array('shop', 'cart', 'checkout', 'myaccount');
			foreach ( $wc_pages as $wc_page ) {
				update_option('woocommerce_' . $wc_page . '_page_id', 0);
			}
			
			// Empty attribute WooCommerce caches
			delete_transient('wc_attribute_taxonomies');
			$wc_product_attributes = array();
			$this->delete_tax_rates_transient();
			
			// Reset the Joomla last imported IDs
			update_option('fgj2wp_last_virtuemart_product_id', 0);
			update_option('fgj2wp_last_virtuemart_order_id', 0);
			update_option('fgj2wp_last_virtuemart_review_id', 0);
			update_option('fgj2wp_last_virtuemart_coupon_id', 0);
			
			// Delete the WooCommerce product category cache
			delete_option("product_cat_children");
			
			$wpdb->hide_errors();
			
			$this->plugin->display_admin_notice(__('WooCommerce data deleted', __CLASS__));
			
			// Recreate WooCommerce default data
			if ( class_exists('WC_Install') ) {
				WC_Install::create_pages();
				$this->plugin->display_admin_notice(__('WooCommerce default data created', __CLASS__));
			}
		}
		
		/**
		 * Import the Virtuemart data
		 *
		 */
		public function import() {
			if ( !$this->test_woocommerce_activation() ) {
				return 0;
			}
			
			$this->vm_version = $this->get_virtuemart_version();
			$this->import_configuration();
			$this->product_types = $this->create_woocommerce_product_types(); // (Re)create the WooCommerce product types
			$this->imported_users = $this->plugin->get_imported_joomla_users();
			$this->import_tax_rates();
			if ( !isset($this->plugin->premium_options['skip_manufacturers']) || !$this->plugin->premium_options['skip_manufacturers'] ) {
				$this->brand_taxonomy = $this->get_brand_taxonomy();
				$this->import_manufacturers();
			}
			if ( !isset($this->plugin->premium_options['skip_attributes']) || !$this->plugin->premium_options['skip_attributes'] ) {
				$this->import_custom_fields();
			}
			if ( !isset($this->plugin->premium_options['skip_products']) || !$this->plugin->premium_options['skip_products'] ) {
				$this->import_product_categories();
				$this->import_products();
			}
			$this->get_imported_products();
			$this->import_stock_status();
			if ( !isset($this->plugin->premium_options['skip_orders']) || !$this->plugin->premium_options['skip_orders'] ) {
				$this->import_orders();
			}
			if ( !isset($this->plugin->premium_options['skip_reviews']) || !$this->plugin->premium_options['skip_reviews'] ) {
				$this->import_reviews();
			}
			if ( !isset($this->plugin->premium_options['skip_coupons']) || !$this->plugin->premium_options['skip_coupons'] ) {
				$this->import_coupons();
			}
		}
		
		/**
		 * Test if the WooCommerce plugin is activated
		 *
		 * @since 2.8.0
		 * 
		 * @return bool True if the WooCommerce plugin is activated
		 */
		private function test_woocommerce_activation() {
			if ( !class_exists('WooCommerce', false) ) {
				$this->plugin->display_admin_error(__('Error: the <a href="https://wordpress.org/plugins/woocommerce/" target="_blank">WooCommerce plugin</a> must be installed and activated to import the products.', __CLASS__));
				return false;
			}
			return true;
		}

		/**
		 * Determine the brand taxonomy
		 * 
		 * @return string Brand taxonomy
		 */
		private function get_brand_taxonomy() {
			$taxonomies = array('product_brand', 'brand');
			foreach ( $taxonomies as $taxonomy ) {
				if ( taxonomy_exists($taxonomy) ) {
					return $taxonomy;
				}
			}
			return '';
		}
		
		/**
		 * Import the Virtuemart configuration and sets globals
		 *
		 */
		private function import_configuration() {
			$config = $this->get_configuration();
			$parameters = explode('|', $config);
			$configs = array();
			foreach ( $parameters as $parameter ) {
				if ( !empty($parameter) && (strpos($parameter, '=') >= 0) ) {
					list($key, $value) = explode('=', $parameter, 2);
					$unserialized_value = @unserialize($value);
					if ( $unserialized_value !== false ) {
						$value = $unserialized_value;
					}
					$configs[$key] = $value;
				}
			}
			
			// Default Virtuemart language
//			$active_languages = isset($configs['active_languages'])? $configs['active_languages'] : '';
//			if ( is_array($active_languages) && count($active_languages) > 0) {
//				$this->language_code = $active_languages[0];
//			} else {
				$this->language_code = $this->plugin->get_joomla_language(); // Joomla installation language
//			}
			$this->language_code = strtolower(strtr($this->language_code, '-', '_'));
			
			// SEO suffix
			if ( isset($configs['seo_sufix']) && isset($configs['seo_disabled']) ) {
				$seo_suffix = ($configs['seo_disabled'] == 0)? $configs['seo_sufix'] : '';
				update_option('fgj2wp_virtuemart_seosuffix', $seo_suffix);
			}
		}
		
		/**
		 * Create the WooCommerce product types
		 *
		 * @return array Product types
		 */
		private function create_woocommerce_product_types() {
			$tab_types = array();
			$taxonomy = 'product_type';
			$product_types = array(
				'simple',
				'grouped',
				'variable',
				'external',
			);
			
			foreach ( $product_types as $product_type ) {
				$term = get_term_by('slug', $product_type, $taxonomy);
				if ( !empty($term) ) {
					$tab_types[$product_type] = $term->term_id;
				} else {
					$new_term = wp_insert_term($product_type, $taxonomy);
					if ( !is_wp_error($new_term) ) {
						$tab_types[$product_type] = $new_term['term_id'];
					}
				}
			}
			return $tab_types;
		}
		
		/**
		 * Import the Virtuemart products
		 *
		 */
		private function import_product_categories() {
			$terms = array();
			$taxonomy = 'product_cat';
			$term_metakey = '_fgj2wp_old_category_id';
			
			$this->plugin->log(__('Importing product categories...', __CLASS__));
			
			$categories = $this->get_product_categories();
			$cat_count = count($categories);
			foreach ( $categories as $category ) {
				
				// Check if the category is already imported
				if ( array_key_exists($category['id'], $this->plugin->imported_categories) ) {
					continue; // Do not import already imported category
				}
				
				// Date
				if ( isset($category['created_on']) ) {
					$date = $category['created_on'];
				} else {
					$date = date('Y-m-d H:i:s', $category['cdate']);
				}
				
				// Insert the category
				$new_category = array(
					'description'	=> isset($category['description'])? $category['description']: '',
					'slug'			=> sanitize_title(FG_Joomla_to_WordPress_Tools::convert_to_latin($category['name'])), // slug
				);
				
				// Hook before inserting the category
				$new_category = apply_filters('fgj2wp_pre_insert_category', $new_category, $category);
				
				$new_term = wp_insert_term($category['title'], $taxonomy, $new_category);
				if ( !is_wp_error($new_term) ) {
					$new_cat_id = $new_term['term_id'];
					$terms[] = $new_cat_id;
					$this->plugin->imported_categories[$category['id']] = $new_cat_id;
					
					// Store the category ID
					add_term_meta($new_cat_id, $term_metakey, $category['id'], true);
					
					// Category ordering
					if ( function_exists('wc_set_term_order') ) {
						wc_set_term_order($new_cat_id, $category['ordering'], $taxonomy);
					}
					
					// SEO Meta data
					$this->set_category_meta_data($new_cat_id, $category);
					
					// Category thumbnails
					if ( !$this->plugin->plugin_options['skip_media'] ) {
						$category_thumbnail = $category['file_url'];
						if ( !empty($category_thumbnail) && function_exists('update_woocommerce_term_meta') ) {
							$category_thumbnail = $this->add_media_prefix($category_thumbnail, 'category');
							$attachment_id = $this->plugin->import_media($category['file_description'], $category_thumbnail, $date);
							if ( $attachment_id ) {
								$this->vm_images_count++;
								update_woocommerce_term_meta($new_cat_id, 'thumbnail_id', $attachment_id);
							}
						}
					}
					
					// Hook after inserting the category
					do_action('fgj2wp_post_insert_category', $new_cat_id, $category);
				}
			}
			
			// Update the categories with their parent ids
			foreach ( $categories as $category ) {
				if ( array_key_exists($category['id'], $this->plugin->imported_categories) && array_key_exists($category['parent_id'], $this->plugin->imported_categories) ) {
					$cat_id = $this->plugin->imported_categories[$category['id']];
					$parent_cat_id = $this->plugin->imported_categories[$category['parent_id']];
					$cat = get_term_by('term_taxonomy_id', $cat_id, $taxonomy);
					$parent_cat = get_term_by('term_taxonomy_id', $parent_cat_id, $taxonomy);
					if ( $cat && $parent_cat ) {
						wp_update_term($cat->term_id, $taxonomy, array('parent' => $parent_cat->term_id));
					}
				}
			}
			
			// Hook after importing all the categories
			do_action('fgj2wp_post_import_categories', $categories);
			
			$this->plugin->progressbar->increment_current_count($cat_count);
			$product_categories_count = count($terms);
			$this->plugin->display_admin_notice(sprintf(_n('%d Virtuemart product category imported', '%d Virtuemart product categories imported', $product_categories_count, __CLASS__), $product_categories_count));

			// Update cache
			if ( !empty($terms) ) {
				clean_term_cache($terms, $taxonomy);
			}
		}
		
		/**
		 * Add the path to the media filename if it is not included (for Joomla 1.5)
		 *
		 * @param string $file_path File path
		 * @param string $type product | category
		 * @return string File path
		 */
		private function add_media_prefix($file_path, $type) {
			// file path (for Joomla 1.5)
			if ( (strpos($file_path, 'images/stories') === false) && (strpos($file_path, 'http://') === false) && (strpos($file_path, 'https://') === false) ) {
				$file_path = "components/com_virtuemart/shop_image/$type/$file_path";
			}
			return $file_path;
		}
		
		/**
		 * Import the Virtuemart products
		 *
		 */
		private function import_products() {
			$media_count = 0;
			$imported_products_count = 0;
			
			$this->plugin->log(__('Importing products...', __CLASS__));
			
			do {
				if ( $this->plugin->import_stopped() ) {
					break;
				}
				
				$products = $this->get_products($this->plugin->chunks_size);
				$products_count = count($products);
				foreach ( $products as $product ) {
					
					list($post_media, $product_medias) = $this->build_post_media($product);
					$media_count = count($post_media) + count($product_medias);
					$new_post = $this->build_post($product, $post_media);
					
					// Hook for modifying the WordPress post just before the insert
					$new_post = apply_filters('fgj2wp_vm_pre_insert_post', $new_post, $product);
					
					$new_post_id = wp_insert_post($new_post);
					
					if ( $new_post_id ) {
						$imported_products_count++;
						
						// Product galleries
						$medias_id = $product_medias;
						if ( $this->vm_options['vm_first_image_not_in_gallery'] ) {
							// Don't include the first image into the product gallery
							array_shift($medias_id);
						}
						$gallery = implode(',', $medias_id);
						
						// Tax and discount
						$tax_class = isset($this->tax_classes[$product['product_tax_id']])? $this->tax_classes[$product['product_tax_id']]: '';
						$tax_rate = $this->calculate_tax_rate($product['product_tax_id'], $product['tax_rate']);
						$discount_rate = $this->calculate_discount_rate($product['product_discount_id'], $product['discount_rate']);
						
						// Regular price
						$price = !empty($product['product_price'])? $product['product_price']: $product['parent_product_price'];
						$price = $this->calculate_price($price, $tax_rate, $discount_rate); // Add the tax and the discount if needed
						
						// Discounted price
						$sale_price = $this->calculate_sale_price($product, $tax_rate, $discount_rate);
						
						// Custom fields = product attributes
						if ( !isset($this->plugin->premium_options['skip_attributes']) || !$this->plugin->premium_options['skip_attributes'] ) {
							$this->import_product_attributes($new_post_id, $product);
						}
						
						$variable_product = false;
						
						// Import Joomla 1.5 product attributes
						if ( isset($product['attribute']) && !empty($product['attribute']) ) {
							$this->import_j15_product_attributes($new_post_id, $product['attribute']);
						}
						
						// Product variations
						if ( !isset($this->plugin->premium_options['skip_variations']) || !$this->plugin->premium_options['skip_variations'] ) {
							$variable_product = $this->import_product_variations($new_post_id, $product, $price, $sale_price, $tax_rate, $discount_rate);
						}
						
						// Product type (simple, external or variable)
						if ( $variable_product ) {
							$product_type = $this->product_types['variable'];
						} elseif ( !empty($product['product_url']) ) {
							$product_type = $this->product_types['external'];
						} else {
							$product_type = $this->product_types['simple'];
						}
						wp_set_object_terms($new_post_id, intval($product_type), 'product_type', true);
						
						// Stock
						$manage_stock = $this->vm_options['vm_stock_management']? 'yes': 'no';
						
						// Add the meta data
						$stock_status = (!$this->vm_options['vm_stock_management'] || ($product['product_in_stock'] > 0))? 'instock': 'outofstock';
						add_post_meta($new_post_id, '_visibility', 'visible', true);
						add_post_meta($new_post_id, '_stock_status', $stock_status, true);
						add_post_meta($new_post_id, '_tax_class', $tax_class, true);
						add_post_meta($new_post_id, '_regular_price', floatval($price), true);
						if ( $sale_price != 0 ) {
							add_post_meta($new_post_id, '_price', floatval($sale_price), true);
							add_post_meta($new_post_id, '_sale_price', floatval($sale_price), true);
						} else {
							add_post_meta($new_post_id, '_price', floatval($price), true);
						}
						add_post_meta($new_post_id, '_featured', !empty($product['product_special'])? 'yes': 'no', true);
						add_post_meta($new_post_id, '_weight', floatval($product['product_weight']), true);
						add_post_meta($new_post_id, '_length', floatval($product['product_length']), true);
						add_post_meta($new_post_id, '_width', floatval($product['product_width']), true);
						add_post_meta($new_post_id, '_height', floatval($product['product_height']), true);
						add_post_meta($new_post_id, '_sku', $product['product_sku'], true);
						add_post_meta($new_post_id, '_stock', $product['product_in_stock'], true);
						add_post_meta($new_post_id, '_manage_stock', $manage_stock, true);
						add_post_meta($new_post_id, '_backorders', 'no', true);
						add_post_meta($new_post_id, '_product_image_gallery', $gallery, true);
						if ( !empty($product['product_url']) ) {
							add_post_meta($new_post_id, '_product_url', $product['product_url'], true);
						}
						
						// Add links between the post and its medias
						$this->plugin->add_post_media($new_post_id, $new_post, $product_medias, true);
						$this->plugin->add_post_media($new_post_id, $new_post, $post_media, false);
						
						// Add the Virtuemart ID as a post meta
						add_post_meta($new_post_id, '_fgj2wp_old_vm_product_id', $product['product_id'], true);
						
						// Brands (manufacturers)
						if ( !isset($this->plugin->premium_options['skip_manufacturers']) || !$this->plugin->premium_options['skip_manufacturers'] ) {
							$product_manufacturers = $this->get_manufacturers_from_product($product['product_id']);
							foreach ( $product_manufacturers as $manufacturer_id ) {
								if ( array_key_exists($manufacturer_id, $this->imported_brands) ) {
									$brand_id = $this->imported_brands[$manufacturer_id];
									wp_set_object_terms($new_post_id, intval($brand_id), $this->brand_taxonomy, true);
								}
							}
						}
						
						// Hook for doing other actions after inserting the post
						do_action('fgj2wp_post_insert_product', $new_post_id, $product);
					}
					
					// Increment the Joomla last imported product ID
					update_option('fgj2wp_last_virtuemart_product_id', $product['product_id']);

				}
				$this->plugin->progressbar->increment_current_count($products_count);
			} while ( ($products != null) && ($products_count > 0) );
			$this->vm_images_count += $media_count;
			$this->plugin->display_admin_notice(sprintf(_n('%d Virtuemart product imported', '%d Virtuemart products imported', $imported_products_count, __CLASS__), $imported_products_count));
		}
		
		/**
		 * Build the post data before inserting it in the post table
		 * 
		 * @param array $product Product data
		 * @param array $post_media Media list
		 * @return array Post data
		 */
		private function build_post($product, $post_media) {
			// Product categories
			$categories_ids = array();
			$product_categories = $this->get_categories_from_product($product['product_id']);
			if ( isset($product['product_parent_id']) ) {
				$product_categories = array_merge($product_categories, $this->get_categories_from_product($product['product_parent_id']));
			}
			foreach ( $product_categories as $cat ) {
				if ( array_key_exists($cat, $this->plugin->imported_categories) ) {
					$categories_ids[] = $this->plugin->imported_categories[$cat];
				}
			}

			// Tags
			$tags = array();
			if ( $this->plugin->plugin_options['meta_keywords_in_tags'] && !empty($product['metakey']) ) {
				$tags = explode(',', $product['metakey']);
			}

			// Process content
			$content = $this->plugin->process_content($product['product_desc'], $post_media);

			// Build the post
			$new_post = array(
				'post_content'		=> $content,
				'post_date'			=> $this->get_date($product),
				'post_excerpt'		=> $product['product_s_desc'],
				'post_status'		=> (($product['published'] == 1) || ($product['published'] == 'Y'))? 'publish': 'draft',
				'post_title'		=> $product['product_name'],
				'post_name'			=> isset($product['slug'])? $product['slug'] : sanitize_title($product['product_name']),
				'post_type'			=> 'product',
				'menu_order'        => isset($product['pordering'])? $product['pordering'] : 0,
				'tax_input'			=> array(
					'product_cat'	=> $categories_ids,
					'product_tag'	=> $tags,
				),
			);
			return $new_post;
		}
		
		/**
		 * Returns the product creation date
		 * 
		 * @param array $product Product data
		 * @return date Date
		 */
		private function get_date($product) {
			if ( isset($product['created_on']) ) {
				$date = $product['created_on'];
			} elseif ( isset($product['cdate']) ) {
				$date = date('Y-m-d H:i:s', $product['cdate']);
			} else {
				$date = date('Y-m-d H:i:s');
			}
			return $date;
		}
		
		/**
		 * Build the product media list
		 * 
		 * @param array $product Product data
		 * @return array [post media list, product media list]
		 */
		private function build_post_media($product) {
			$product_medias = array();
			$post_media = array();
			
			// Product images
			if ( !$this->plugin->plugin_options['skip_media'] ) {
				$images = $this->get_product_images($product['product_id']);
				if ( isset($product['product_full_image']) ) { // Joomla 1.5
					$images = array_merge(
						array(array(
							'file_url'	=> $this->add_media_prefix($product['product_full_image'], 'product'),
							'file_description'	=> ''
						)),
						$images);
				}
				// Import product images
				$product_medias = array();
				foreach ( $images as $image ) {
					// Add the path if it is not included in the file url
					if ( (strpos($image['file_url'], 'images/stories') === false) && (strpos($image['file_url'], 'components/com_virtuemart') === false) && (strpos($image['file_url'], 'http://') === false) && (strpos($image['file_url'], 'https://') === false) ) {
						$image['file_url'] = 'images/stories/virtuemart/product/' . $image['file_url'];
					}
					$attachment_id = $this->plugin->import_media($image['file_description'], $image['file_url'], $this->get_date($product));
					if ( $attachment_id ) {
						$product_medias[] = $attachment_id;
					}
				}

				// Import content media
				$result = $this->plugin->import_media_from_content($product['product_desc'], $this->get_date($product));
				$post_media = $result['media'];
			}
			return array($post_media, $product_medias);
		}
		
		/**
		 * Calculate the product tax rate
		 * 
		 * @param int $tax_rate_id Tax rate ID (-1, 0 or n)
		 * @param float $tax_rate Tax rate
		 * @return float Product tax rate
		 */
		private function calculate_tax_rate($tax_rate_id, $tax_rate) {
			$product_tax_rate = 1;
			switch ( $tax_rate_id ) {
				case -1:
					break;
				case 0:
					$product_tax_rate = $this->global_tax_rate;
					break;
				default:
					$product_tax_rate = 1 + $tax_rate;
			}
			return $product_tax_rate;
		}
		
		/**
		 * Calculate the product discount rate
		 * 
		 * @param int $discount_rate_id Discount rate ID (-1, 0 or n)
		 * @param float $discount_rate Discount rate
		 * @return float Product discount rate
		 */
		private function calculate_discount_rate($discount_rate_id, $discount_rate) {
			$product_discount_rate = 1;
			switch ( $discount_rate_id ) {
				case -1:
				case 0:
					break;
				default:
					$product_discount_rate = 1 - $discount_rate;
			}
			return $product_discount_rate;
		}
		
		/**
		 * Calculate the price with or without tax
		 * 
		 * @param float $price Price without tax
		 * @param float $tax_rate Tax rate
		 * @param float $discount_rate Discount rate
		 * @return float Calculated price
		 */
		private function calculate_price($price, $tax_rate, $discount_rate) {
			if ( $this->vm_options['vm_price'] == 'with_tax' ) {
				$price *= $tax_rate;
				$price *= $discount_rate;
			}
			return $price;
		}
		
		/**
		 * Calculate the sale price (discounted price)
		 * 
		 * @param array $product Product data
		 * @param float $tax_rate Tax rate
		 * @param float $discount_rate Discount rate
		 * @return float Sale price
		 */
		private function calculate_sale_price($product, $tax_rate, $discount_rate) {
			$sale_price = 0;
			if ( !empty($product['product_override_price']) && floatval($product['product_override_price']) != 0.0 ) {
				switch ( $product['override'] ) {
					case -1: $sale_price = $product['product_override_price']; break; // Tax excluded
					case 1: $sale_price = $product['product_override_price'] / $tax_rate; break; // Tax included
					case 0:
					default: $sale_price = 0; break; // disabled
				}
			} else {
				// Get the parent override price
				switch ( $product['parent_override'] ) {
					case -1: $sale_price = $product['parent_product_override_price']; break; // Tax excluded
					case 1: $sale_price = $product['parent_product_override_price'] / $tax_rate; break; // Tax included
					case 0:
					default: $sale_price = 0; break; // disabled
				}
			}
			$sale_price = $this->calculate_price($sale_price, $tax_rate, $discount_rate); // Add the tax and the discount if needed
			return $sale_price;
		}
		
		/**
		 * Import the Virtuemart custom fields
		 *
		 */
		private function import_custom_fields() {
			$this->attribute_values = array();
			$taxonomies = array();
			$custom_fields = $this->get_custom_fields();
			foreach ( $custom_fields as $custom_field ) {
				
				// Create the attribute
				$attribute_type = in_array($custom_field['field_type'], array('V', 'A'))? 'select': 'text'; // V: variant, A: child variant
				$taxonomy = $this->create_woocommerce_attribute($custom_field['custom_title'], $attribute_type);
				$taxonomies[] = $taxonomy;
				
				// Create the attributes values
				$product_custom_fields = $this->get_product_custom_fields_from_custom_id($custom_field['virtuemart_custom_id']);
				$terms = array();
				foreach ( $product_custom_fields as $product_custom_field ) {
					// Create the attribute value
					$attribute_values_terms = $this->create_woocommerce_attribute_value($taxonomy, $product_custom_field['custom_value'], $product_custom_field['ordering']);
					foreach ( $attribute_values_terms as $term ) {
						$terms[] = $term['term_id'];
						$this->attribute_values[$product_custom_field['virtuemart_customfield_id']] = $term;
					}
				}

				// Update cache
				if ( !empty($terms) ) {
					clean_term_cache($terms, $taxonomy);
				}
			}
			
			// Empty attribute taxonomies cache
			delete_transient('wc_attribute_taxonomies');
		}
		
		/**
		 * Import the Virtuemart products attributes
		 *
		 * @param int $new_product_id WordPress ID
		 * @param array $product Virtuemart product
		 */
		private function import_product_attributes($new_product_id, $product) {
			$i = 0;
			$this->prices = array();
			$product_custom_fields = $this->get_product_custom_fields($product['product_id']);
			foreach ( $product_custom_fields as $product_custom_field ) {
				$attribute_name = $this->normalize_attribute_name($product_custom_field['custom_title']);
				$taxonomy = 'pa_' . $attribute_name;
				$is_variation = $product_custom_field['is_cart_attribute'];
				
				// Assign the attribute group to the product
				$this->create_woocommerce_product_attribute($new_product_id, $taxonomy, $is_variation, $product_custom_field['ordering']);
				
				// Set the relationship between the product and the attribute values
				$attribute_id = $product_custom_field['virtuemart_customfield_id'];
				if ( isset($this->attribute_values[$attribute_id]) ) {
					$this->set_object_terms($new_product_id, $this->attribute_values[$attribute_id]['term_taxonomy_id'], $i++);
				}
				
				// Store the price
				$price = $product_custom_field['custom_price'];
				if ( !is_null($price) ) {
					$attribute_value = $product_custom_field['custom_value'];
					$this->prices[$taxonomy][sanitize_title($attribute_value)] = $price;
				}
			}
		}
		
		/**
		 * Create a product attribute value
		 *
		 * @param string $product_id Product ID
		 * @param string $taxonomy Taxonomy
		 * @param int $is_variation Is it a variation?
		 * @param int $attribute_ordering Attribute ordering
		 */
		private function create_woocommerce_product_attribute($product_id, $taxonomy, $is_variation, $attribute_ordering = 0) {
			// Assign the attribute to the product
			$product_attributes = get_post_meta($product_id, '_product_attributes', true);
			if ( empty($product_attributes) ) {
				$product_attributes = array();
			}
			if ( !array_key_exists($taxonomy, $product_attributes) ) {
				$product_attributes = array_merge($product_attributes, array(
					$taxonomy => array(
						'name'			=> $taxonomy,
						'value'			=> '',
						'position'		=> $attribute_ordering,
						'is_visible'	=> 1,
						'is_variation'	=> $is_variation,
						'is_taxonomy'	=> 1,
					)
				));
				update_post_meta($product_id, '_product_attributes', $product_attributes);
			}
		}
		
		/**
		 * Import the Virtuemart product attributes (Joomla 1.5 only)
		 *
		 */
		private function import_j15_product_attributes($product_id, $attribute_string) {
			$matches = array();
			
			$attributes = $this->explode_attribute_string($attribute_string, ';', ',');
			$attribute_rank = 0;
			foreach ( $attributes as $attribute_label => $attribute_value_string ) {
				$attribute_rank++;
				
				// Create the attribute
				$taxonomy = $this->create_woocommerce_attribute($attribute_label, 'select');
				
				// Create the attributes values
				$terms = array();
				$attribute_values = explode(',', $attribute_value_string);
				$attribute_value_rank = 0;
				foreach ( $attribute_values as $attribute_value ) {
					$attribute_value_rank++;
					
					// Get the price
					if ( preg_match("/(.*)\[=?(.*)\]/", $attribute_value, $matches) ) {
						$attribute_value = $matches[1];
						$price = floatval($matches[2]);

					} else {
						$price = 0.0;
					}
					
					// Create the attribute value
					$attribute_values_terms = $this->create_woocommerce_attribute_value($taxonomy, $attribute_value, $attribute_value_rank);
					$this->create_woocommerce_product_attribute($product_id, $taxonomy, 1, $attribute_rank);

					// Set the relationship between the product and the attribute values
					foreach ( $attribute_values_terms as $term ) {
						if ( !is_wp_error($term) && isset($term['term_id']) ) {
							$this->set_object_terms($product_id, intval($term['term_taxonomy_id']), $attribute_value_rank);
						}
					}

					// Store the price
					if ( !is_null($price) ) {
						$this->prices[$taxonomy][sanitize_title($attribute_value)] = $price;
					}
					$terms = array_merge($terms, $attribute_values_terms);
				}
				
				// Update cache
				if ( !empty($terms) ) {
					clean_term_cache($terms, $taxonomy);
				}
			}
			
			// Empty attribute taxonomies cache
			delete_transient('wc_attribute_taxonomies');
		}
		
		/**
		 * Create a product attribute
		 *
		 * @param string $attribute_label Attribute label
		 * @param string $attribute_type select | text
		 * @return string Taxonomy
		 */
		private function create_woocommerce_attribute($attribute_label, $attribute_type) {
			global $wpdb;
			global $wc_product_attributes;
			
			$attribute_name = $this->normalize_attribute_name($attribute_label);
			$taxonomy = 'pa_' . $attribute_name;
			
			if ( !array_key_exists($taxonomy, $wc_product_attributes) ) {
				// Create the taxonomy
				$attribute_taxonomy = array(
					'attribute_name'	=> $attribute_name,
					'attribute_label'	=> $attribute_label,
					'attribute_type'	=> $attribute_type,
					'attribute_orderby'	=> 'menu_order',
				);
				$wpdb->insert($wpdb->prefix . 'woocommerce_attribute_taxonomies', $attribute_taxonomy);

				// Register the taxonomy
				register_taxonomy($taxonomy,
					apply_filters('woocommerce_taxonomy_objects_' . $taxonomy, array('product')),
					apply_filters('woocommerce_taxonomy_args_' . $taxonomy, array(
						'hierarchical' => true,
						'show_ui' => false,
						'query_var' => true,
						'rewrite' => false,
					))
				);
				$wc_product_attributes[$taxonomy] = $attribute_taxonomy; // useful for wc_set_term_order()
			}
			return $taxonomy;
		}
		
		/**
		 * Normalize the attribute name
		 * 
		 * @param string $attribute_label Attribute label
		 * @return string Normalized attribute name
		 */
		private function normalize_attribute_name($attribute_label) {
			$attribute_name = substr(sanitize_key(FG_Joomla_to_WordPress_Tools::convert_to_latin($attribute_label)), 0, 29); // The taxonomy is limited to 32 characters in WordPress
			return $attribute_name;
		}
		
		/**
		 * Create an attribute value
		 *
		 * @param string $taxonomy Taxonomy
		 * @param string $attribute_value Attribute value
		 * @param int $attribute_value_ordering Attribute value ordering
		 * @return array Terms created
		 */
		private function create_woocommerce_attribute_value($taxonomy, $attribute_value, $attribute_value_ordering = 0) {
			$terms = array();
			
			// Create one term by custom value
			$attribute_value = substr($attribute_value, 0, 200); // term name is limited to 200 characters
			$attribute_value_slug = $this->normalize_attribute_name($attribute_value);
			$term = get_term_by('slug', $attribute_value_slug, $taxonomy, ARRAY_A);
			if ( $term !== false ) {
				$terms[] = $term;
			} elseif ( !empty($attribute_value) ) {
				$newterm = wp_insert_term($attribute_value, $taxonomy, array('slug' => $attribute_value_slug));
				if ( !is_wp_error($newterm) ) {
					add_term_meta($newterm['term_id'], '_fgj2wp_imported', 1, true);
					$term = get_term_by('id', $newterm['term_id'], $taxonomy, ARRAY_A);
					$terms[] = $term;
					// Category ordering
					if ( function_exists('wc_set_term_order') ) {
						wc_set_term_order($term['term_id'], $attribute_value_ordering, $taxonomy);
					}
				}
			}
			return $terms;
		}
		
		/**
		 * Same function as wp_set_object_terms but with the term_order parameter
		 *
		 * @param int $object_id Object ID
		 * @param int $term_taxonomy_id Term taxonomy ID
		 * @param int $term_order Term order
		 */
		private function set_object_terms($object_id, $term_taxonomy_id, $term_order) {
			global $wpdb;
			
			$wpdb->hide_errors(); // to prevent the display of an error if the term relashionship already exists
			$wpdb->insert($wpdb->prefix . 'term_relationships', array(
				'object_id'			=> $object_id,
				'term_taxonomy_id'	=> $term_taxonomy_id,
				'term_order'		=> $term_order,
			));
			$wpdb->show_errors();
		}
		
		/**
		 * Import the product variations
		 *
		 * @param int $product_id WordPress ID
		 * @param array $product Virtuemart product
		 * @param float $regular_price Regular price
		 * @param float $tax_rate Tax rate
		 * @param float $discount_rate Discount rate
		 * @return bool Is the product variable?
		 */
		private function import_product_variations($product_id, $product, $regular_price, $sale_price, $tax_rate, $discount_rate) {
			
			// Get the product attributes
			$product_attributes = get_post_meta($product_id, '_product_attributes', true);
			$attributes = array();
			if ( is_array($product_attributes) ) {
				foreach ( $product_attributes as $product_attribute ) {
					if ( $product_attribute['is_variation'] == 1 ) {
						$attributes[] = $product_attribute['name'];
					}
				}
			}
			
			if ( !empty($attributes) ) {
				// Get the attributes values with their prices
				$attributes_values = array();
				foreach ( $attributes as $taxonomy ) {
					// Get the attribute values
					$terms = wp_get_object_terms(array($product_id), $taxonomy, array('orderby' => 'term_order'));
					$attribute_values = array();
					if ( !is_wp_error($terms) ) {
						foreach ( $terms as $term ) {
							$price = isset($this->prices[$taxonomy][$term->slug])? $this->prices[$taxonomy][$term->slug]: 0;
							$attribute_values[$term->slug] = array(
								'attributes'	=> array($term->taxonomy => $term->slug),
								'price'			=> $price,
							);
						}
						$attributes_values[$taxonomy] = $attribute_values;
					}
				}
				
				// Calculate the variations
				$variations = $this->get_variations($attributes_values);

				// Create the variations (posts)
				$i = 0;
				foreach ( $variations as $variation ) {
					$i++;
					$new_post = array(
						'post_title'	=> 'Variation # of ' . $product['product_name'],
						'post_name'		=> "product-$product_id-variation" . (($i == 1)? '': "-$i"),
						'post_parent'	=> $product_id,
						'menu_order'	=> 0,
						'post_type'		=> 'product_variation',
						'post_status'	=> 'publish',
					);
					$new_post_id = wp_insert_post($new_post);

					if ( $new_post_id ) {
						add_post_meta($new_post_id, '_fgj2wp_imported', 1, true);
						wp_update_post(array(
							'ID'			=> $new_post_id,
							'post_title'	=> "Variation #$new_post_id of " . $product['product_name'],
						));
						foreach ( $variation['attributes'] as $attribute => $attribute_value ) {
							add_post_meta($new_post_id, 'attribute_' . $attribute, $attribute_value, true);
						}
						$variation_price = $this->calculate_price($variation['price'], $tax_rate, $discount_rate); // Add the tax and the discount if needed
						$price = $regular_price + $variation_price;
						add_post_meta($new_post_id, '_regular_price', $price, true);
						if ( $sale_price != 0 ) {
							$variation_sale_price = $sale_price + $variation_price;
							add_post_meta($new_post_id, '_price', floatval($variation_sale_price), true);
							add_post_meta($new_post_id, '_sale_price', floatval($variation_sale_price), true);
						} else {
							add_post_meta($new_post_id, '_price', floatval($price), true);
						}
					}
				}
				return ($i > 0);
			}
			return false;
		}
		
		/**
		 * Get all the variations recursively
		 * 
		 * @param array $attributes Attributes with their prices
		 * @return array Variations with the calculated prices
		 *
		 */
		private function get_variations($attributes) {
			$variations = array();
			if ( is_array($attributes) && (count($attributes) > 0) ) {
				$attribute = array_shift($attributes);
				foreach ($attribute as $key => $value) {
					if ( empty($attributes) ) {
						$variations[$key] = $value;
					} else {
						$children_variations = $this->get_variations($attributes);
						foreach ($children_variations as $vkey => $vvalue ) {
							$variations[$key.'-'.$vkey] = array(
								'attributes'	=> array_merge($value['attributes'], $vvalue['attributes']),
								'price'			=> $vvalue['price'] + $value['price'],
							);
						}
					}
				}
			}
			return $variations;
		}
		
		/**
		 * Import the Virtuemart tax rates
		 *
		 */
		private function import_tax_rates() {
			global $wpdb;
			
			if ( $this->vm_options['vm_price'] == 'without_tax' ) {
				// Import the taxes : the product prices will be imported without tax
				$tax_rates = $this->get_tax_rates();
				$priority = 1;
				foreach ( $tax_rates as $tax_rate ) {
					$tax_rate_name = trim($tax_rate['calc_name']);
					if ( $priority == 1 ) {
						$tax_rate_class = ''; // Use the standard class for the first tax
					} else {
						$tax_rate_class = sanitize_title($tax_rate_name);
						$this->tax_classes[$tax_rate['virtuemart_calc_id']] = $tax_rate_class;
						$this->add_tax_rate_class($tax_rate_name);
					}
					$wpdb->insert(
						$wpdb->prefix . "woocommerce_tax_rates",
						array(
							'tax_rate'          => wc_format_decimal($tax_rate['calc_value'], 4),
							'tax_rate_name'     => $tax_rate_name,
							'tax_rate_priority' => $priority++,
							'tax_rate_shipping' => 1,
							'tax_rate_class'	=> $tax_rate_class,
						)
					);
				}
			} else {
				// Calculate the global tax rate : the product prices will be imported with tax
				$tax_rates = $this->get_global_tax_and_discount_rates();
				$this->global_tax_rate = 1.0;
				foreach ( $tax_rates as $tax_rate ) {
					switch ( $tax_rate['calc_value_mathop'] ) {
						case '+%': $this->global_tax_rate *= 1.0 + ($tax_rate['calc_value'] / 100); break;
						case '-%': $this->global_tax_rate *= 1.0 - ($tax_rate['calc_value'] / 100); break;
					}
				}
			}
			$this->delete_tax_rates_transient();
		}
		
		/**
		 * Add a tax rate class in WooCommerce
		 * 
		 * @since 2.7.0
		 * 
		 * @param string $tax_rate_name Tax rate name
		 */
		private function add_tax_rate_class($tax_rate_name) {
			$woocommerce_tax_classes_option = get_option('woocommerce_tax_classes');
			$woocommerce_tax_classes = explode("\n", $woocommerce_tax_classes_option);
			if ( !in_array($tax_rate_name, $woocommerce_tax_classes) ) {
				$woocommerce_tax_classes[] = $tax_rate_name;
				$woocommerce_tax_classes_option = implode("\n", $woocommerce_tax_classes);
				update_option('woocommerce_tax_classes', $woocommerce_tax_classes_option);
			}
		}
		
		/**
		 * Delete the wc_tax_rates transient
		 * 
		 */
		public function delete_tax_rates_transient() {
			global $wpdb;
			$wpdb->query( "DELETE FROM `$wpdb->options` WHERE `option_name` LIKE ('_transient_wc_tax_rates_%') OR `option_name` LIKE ('_transient_timeout_wc_tax_rates_%')" );
		}
		
		/**
		 * Import the Virtuemart stock status
		 *
		 */
		private function import_stock_status() {
			$this->stock_handles = $this->get_stock_status();
		}
		
		/**
		 * Import the Virtuemart orders
		 *
		 */
		private function import_orders() {
			global $wpdb;
			$imported_orders_count = 0;
			
			$this->plugin->log(__('Importing orders...', __CLASS__));
			
			$users = $this->plugin->get_imported_users();
			$products_ids = $this->get_woocommerce_products();
			$shipment_methods = $this->get_shipments_methods();
			
			do {
				if ( $this->plugin->import_stopped() ) {
					break;
				}
				
				$orders = $this->get_orders($this->plugin->chunks_size);
				$orders_count = count($orders);
				foreach ( $orders as $order ) {

					// Date
					if ( isset($order['created_on']) ) {
						$date = $order['created_on'];
					} else {
						$date = date('Y-m-d H:i:s', $order['cdate']);
					}
					
					// Order status
					$order_status = $this->map_order_status($order['order_status']);
					
					// Insert the post
					$new_post = array(
						'post_date'			=> $date,
						'post_title'		=> 'Order &ndash;' . $date,
						'post_excerpt'		=> isset($order['customer_note'])? $order['customer_note']: '',
						'post_status'		=> $order_status,
						'ping_status'		=> 'closed',
						'post_type'			=> 'shop_order',
						'post_password'		=> $order['order_pass'],
					);

					$new_post_id = wp_insert_post($new_post);

					if ( $new_post_id ) {
						$imported_orders_count++;

						add_post_meta($new_post_id, '_fgj2wp_old_vm_order_id', $order['virtuemart_order_id'], true);
						
						$user_id = isset($users[$order['virtuemart_user_id']])? $users[$order['virtuemart_user_id']] : 0;

						// Shipping address
						if ( empty($order['st_address_1']) ) {
							$shipping_country = $order['bt_country'];
							$shipping_first_name = $order['bt_first_name'];
							$shipping_last_name = $order['bt_last_name'];
							$shipping_company = $order['bt_company'];
							$shipping_address_1 = $order['bt_address_1'];
							$shipping_address_2 = $order['bt_address_2'];
							$shipping_postcode = $order['bt_zip'];
							$shipping_city = $order['bt_city'];
							$shipping_state = $order['bt_state'];
						} else {
							$shipping_country = $order['st_country'];
							$shipping_first_name = $order['st_first_name'];
							$shipping_last_name = $order['st_last_name'];
							$shipping_company = $order['st_company'];
							$shipping_address_1 = $order['st_address_1'];
							$shipping_address_2 = $order['st_address_2'];
							$shipping_postcode = $order['st_zip'];
							$shipping_city = $order['st_city'];
							$shipping_state = $order['st_state'];
						}
						
						add_post_meta($new_post_id, '_billing_country', $order['bt_country'], true);
						add_post_meta($new_post_id, '_billing_first_name', $order['bt_first_name'], true);
						add_post_meta($new_post_id, '_billing_last_name', $order['bt_last_name'], true);
						add_post_meta($new_post_id, '_billing_company', $order['bt_company'], true);
						add_post_meta($new_post_id, '_billing_address_1', $order['bt_address_1'], true);
						add_post_meta($new_post_id, '_billing_address_2', $order['bt_address_2'], true);
						add_post_meta($new_post_id, '_billing_postcode', $order['bt_zip'], true);
						add_post_meta($new_post_id, '_billing_city', $order['bt_city'], true);
						add_post_meta($new_post_id, '_billing_state', $order['bt_state'], true);
						add_post_meta($new_post_id, '_billing_email', $order['email'], true);
						add_post_meta($new_post_id, '_billing_phone', $order['bt_phone_1'], true);
						add_post_meta($new_post_id, '_shipping_country', $shipping_country, true);
						add_post_meta($new_post_id, '_shipping_first_name', $shipping_first_name, true);
						add_post_meta($new_post_id, '_shipping_last_name', $shipping_last_name, true);
						add_post_meta($new_post_id, '_shipping_company', $shipping_company, true);
						add_post_meta($new_post_id, '_shipping_address_1', $shipping_address_1, true);
						add_post_meta($new_post_id, '_shipping_address_2', $shipping_address_2, true);
						add_post_meta($new_post_id, '_shipping_postcode', $shipping_postcode, true);
						add_post_meta($new_post_id, '_shipping_city', $shipping_city, true);
						add_post_meta($new_post_id, '_shipping_state', $shipping_state, true);
						add_post_meta($new_post_id, '_payment_method', $order['payment_method'], true);
						add_post_meta($new_post_id, '_payment_method_title', $order['payment_name'], true);
						add_post_meta($new_post_id, '_order_shipping', $order['order_shipment'], true);
						add_post_meta($new_post_id, '_order_discount', $order['coupon_discount'], true);
						add_post_meta($new_post_id, '_cart_discount', 0, true);
						add_post_meta($new_post_id, '_order_tax', $order['order_tax'], true);
						add_post_meta($new_post_id, '_order_shipping_tax', $order['order_shipment_tax'], true);
						add_post_meta($new_post_id, '_order_total', $order['order_total'], true);
						add_post_meta($new_post_id, '_order_key', $order['order_number'], true);
						add_post_meta($new_post_id, '_customer_user', $user_id, true);
						add_post_meta($new_post_id, '_order_currency', $order['currency_code_3'], true);
						add_post_meta($new_post_id, '_prices_include_tax', 'no', true);
						add_post_meta($new_post_id, '_customer_ip_address', $order['ip_address'], true);
						add_post_meta($new_post_id, '_customer_user_agent', '', true);
						add_post_meta($new_post_id, '_recorded_sales', 'yes', true);
						add_post_meta($new_post_id, '_recorded_coupon_usage_counts', 'yes', true);

						// Order items
						$order_items = $this->get_order_items($order['virtuemart_order_id']);
						foreach ( $order_items as $order_item ) {
							if ( $wpdb->insert($wpdb->prefix . 'woocommerce_order_items', array(
								'order_item_name'	=> $order_item['order_item_name'],
								'order_item_type'	=> 'line_item',
								'order_id'			=> $new_post_id,
							)) ) {
								$wc_order_item_id = $wpdb->insert_id;
								$product_id = isset($products_ids[$order_item['virtuemart_product_id']])? $products_ids[$order_item['virtuemart_product_id']]: 0;
								$this->add_wc_order_itemmeta($wc_order_item_id, '_qty', $order_item['product_quantity']);
								$this->add_wc_order_itemmeta($wc_order_item_id, '_tax_class', '');
								$this->add_wc_order_itemmeta($wc_order_item_id, '_product_id', $product_id);
								$this->add_wc_order_itemmeta($wc_order_item_id, '_variation_id', 0);
								$this->add_wc_order_itemmeta($wc_order_item_id, '_line_subtotal', $order_item['product_discountedPriceWithoutTax'] * $order_item['product_quantity']);
								$this->add_wc_order_itemmeta($wc_order_item_id, '_line_total', $order_item['product_discountedPriceWithoutTax'] * $order_item['product_quantity']);
								$this->add_wc_order_itemmeta($wc_order_item_id, '_line_tax', $order_item['product_tax'] * $order_item['product_quantity']);
								$this->add_wc_order_itemmeta($wc_order_item_id, '_line_subtotal_tax', $order_item['product_tax'] * $order_item['product_quantity']);

								// Product attributes
								$product_attributes = $this->get_order_item_attributes($order_item['product_attribute']);
								foreach ( $product_attributes as $key => $value ) {
									$this->add_wc_order_itemmeta($wc_order_item_id, $key, $value);
								}
							}
						}
						
						// Payment fee
						if ( $order['order_payment'] > 0 ) {
							$this->add_fee($new_post_id, $order['order_payment'], $order['order_payment_tax']);
						}
						
						// Shipping
						$shipment_method = isset($shipment_methods[$order['virtuemart_shipmentmethod_id']])? $shipment_methods[$order['virtuemart_shipmentmethod_id']] : '';
						if ( $wpdb->insert($wpdb->prefix . 'woocommerce_order_items', array(
							'order_item_name'	=> $shipment_method,
							'order_item_type'	=> 'shipping',
							'order_id'			=> $new_post_id,
						)) ) {
							$wc_order_item_id = $wpdb->insert_id;
							$this->add_wc_order_itemmeta($wc_order_item_id, 'method_id', 0);
							$this->add_wc_order_itemmeta($wc_order_item_id, 'cost', $order['order_shipment']);
						}
						
						// Taxes
						if ( $wpdb->insert($wpdb->prefix . 'woocommerce_order_items', array(
							'order_item_name'	=> 'Tax',
							'order_item_type'	=> 'tax',
							'order_id'			=> $new_post_id,
						)) ) {
							$wc_order_item_id = $wpdb->insert_id;
							$bill_tax = json_decode($order['order_billTax'], true);
							$tax_label = isset($bill_tax[1]['calc_name'])? $bill_tax[1]['calc_name']: '';
							$this->add_wc_order_itemmeta($wc_order_item_id, 'rate_id', 0);
							$this->add_wc_order_itemmeta($wc_order_item_id, 'label', $tax_label);
							$this->add_wc_order_itemmeta($wc_order_item_id, 'compound', 0);
							$this->add_wc_order_itemmeta($wc_order_item_id, 'tax_amount', $order['order_tax'] + $order['order_payment_tax']);
							$this->add_wc_order_itemmeta($wc_order_item_id, 'shipping_tax_amount', $order['order_shipment_tax']);
						}
					}
					
					// Increment the Joomla last imported order ID
					update_option('fgj2wp_last_virtuemart_order_id', $order['virtuemart_order_id']);
				}
				$this->plugin->progressbar->increment_current_count($orders_count);
			} while ( ($orders != null) && ($orders_count > 0) );
			$this->plugin->display_admin_notice(sprintf(_n('%d Virtuemart order imported', '%d Virtuemart orders imported', $imported_orders_count, __CLASS__), $imported_orders_count));
		}
		
		/**
		 * Mapping between Virtuemart and WooCommerce status
		 *
		 * @param string $vm_order_status Virtuemart order status
		 * @return string WooCommerce order status
		 */
		private function map_order_status($vm_order_status) {
			switch ( $vm_order_status ) {
				case 'P': $status = 'wc-pending'; break;
				case 'U': $status = 'wc-processing'; break;
				case 'C': $status = 'wc-processing'; break;
				case 'X': $status = 'wc-cancelled'; break;
				case 'R': $status = 'wc-refunded'; break;
				case 'S': $status = 'wc-completed'; break;
				default:
					$stock_handle = isset($this->stock_handles[$vm_order_status])? $this->stock_handles[$vm_order_status] : '';
					switch($stock_handle) {
						case 'A': $status = 'wc-cancelled'; break; // is_available
						case 'O': $status = 'wc-completed'; break; // is_removed
						default: $status = 'wc-pending'; break; // is_reserved
					}
			}
			return $status;
		}
		
		/**
		 * Get a list of attributes contained in a string
		 *
		 * @param string $attribute_string Attribute string
		 * @return array (key, value) of attributes
		 */
		private function get_order_item_attributes($attribute_string) {
			$attributes = array();
			$matches = array();
			if ( preg_match_all("#<span.*?>(.*?)</span><span.*?>(.*?)</span>#", stripslashes($attribute_string), $matches, PREG_SET_ORDER) ) {
				foreach ( $matches as $match ) {
					$attributes[$match[1]] = $match[2];
				}
			} elseif ( strpos($attribute_string, ';') !== false ) {
				$attributes = $this->explode_attribute_string($attribute_string, ';');
			} elseif ( strpos($attribute_string, '<br/>') !== false ) {
				$attributes = $this->explode_attribute_string($attribute_string, '<br/>');
			}
			return $attributes;
		}
		
		/**
		 * Get a list of attributes contained in a string
		 *
		 * @param string $attribute_string Attribute string
		 * @param string $separator Separator
		 * @return array (key, value) of attributes
		 */
		private function explode_attribute_string($attribute_string, $separator, $sub_separator = ':') {
			$attributes = array();
			$items = explode($separator, $attribute_string);
			if ( is_array($items) ) {
				foreach ( $items as $item ) {
					if ( strpos($item, $sub_separator) !== false ) {
						list($key, $value) = explode($sub_separator, $item, 2);
						$key = trim($key);
						$value = trim($value);
						$attributes[$key] = $value;
					}
				}
			}
			return $attributes;
		}
		
		/**
		 * Add a fee to the order
		 *
		 * @param int $order_id Product ID
		 * @param float $price
		 * @param float $tax
		 */
		private function add_fee($order_id, $price, $tax) {
			global $wpdb;
			if ( $wpdb->insert($wpdb->prefix . 'woocommerce_order_items', array(
					'order_item_name'	=> 'Payment fee',
					'order_item_type'	=> 'fee',
					'order_id'			=> $order_id,
				)) ) {
				$wc_order_item_id = $wpdb->insert_id;
				$this->add_wc_order_itemmeta($wc_order_item_id, '_tax_class', '');
				$this->add_wc_order_itemmeta($wc_order_item_id, '_line_total', $price);
				$this->add_wc_order_itemmeta($wc_order_item_id, '_line_tax', $tax);
				$this->add_wc_order_itemmeta($wc_order_item_id, '_line_subtotal', $price);
				$this->add_wc_order_itemmeta($wc_order_item_id, '_line_subtotal_tax', $tax);
			}
		}
		
		/**
		 * Add an order item meta
		 *
		 * @param int $order_item_id Order item ID
		 * @param string $meta_key Meta key
		 * @param string $meta_value Meta value
		 * @return int order item meta ID
		 */
		private function add_wc_order_itemmeta($order_item_id, $meta_key, $meta_value) {
			global $wpdb;
			if ( $wpdb->insert($wpdb->prefix . 'woocommerce_order_itemmeta', array(
				'order_item_id'	=> $order_item_id,
				'meta_key'		=> $meta_key,
				'meta_value'	=> $meta_value,
			)) ) {
				return $wpdb->insert_id;
			} else {
				return 0;
			}
		}
		
		/**
		 * Import the Virtuemart reviews
		 *
		 */
		private function import_reviews() {
			$imported_reviews_count = 0;
			$this->plugin->log(__('Importing reviews...', __CLASS__));
			
			$reviews = $this->get_reviews();
			$reviews_count = count($reviews);
			foreach ( $reviews as $review ) {
				$product_id = array_key_exists($review['virtuemart_product_id'], $this->plugin->imported_products)? $this->plugin->imported_products[$review['virtuemart_product_id']]: 0;
				if ( $product_id != 0 ) {
					$user_id = array_key_exists($review['created_by'], $this->imported_users)? $this->imported_users[$review['created_by']]: 0;
					$date = isset($review['time'])? date('Y-m-d H:i:s', $review['time']) : $review['created_on'];
					$comment = array(
						'comment_post_ID'		=> $product_id,
						'comment_author'		=> $review['author'],
						'comment_author_email'	=> $review['author_email'],
						'comment_content'		=> $review['comment'],
						'user_id'				=> $user_id,
						'comment_author_IP'		=> $review['lastip'],
						'comment_date'			=> $date,
						'comment_approved'		=> !empty($review['published'])? 1 : 0,
					);
					$comment_id = wp_insert_comment($comment);
					if ( !empty($comment_id) ) {
						$imported_reviews_count++;
						add_comment_meta($comment_id, '_fgj2wp_old_vm_rating_review_id', $review['virtuemart_rating_review_id'], true);
						add_comment_meta($comment_id, 'rating', $review['review_rating'], true);
					}
				}
				// Increment the Joomla last imported review ID
				update_option('fgj2wp_last_virtuemart_review_id', $review['virtuemart_rating_review_id']);
			}
			$this->plugin->progressbar->increment_current_count($reviews_count);
			$this->plugin->display_admin_notice(sprintf(_n('%d Virtuemart review imported', '%d Virtuemart reviews imported', $imported_reviews_count, __CLASS__), $imported_reviews_count));
		}
		
		/**
		 * Import the Virtuemart coupons
		 *
		 */
		private function import_coupons() {
			$imported_coupons_count = 0;
			$this->plugin->log(__('Importing coupons...', __CLASS__));
			
			$coupons = $this->get_coupons();
			$coupons_count = count($coupons);
			foreach ( $coupons as $coupon ) {
				$user_id = array_key_exists($coupon['created_by'], $this->imported_users)? $this->imported_users[$coupon['created_by']]: 0;
				if ( $coupon['coupon_start_date'] != '0000-00-00 00:00:00' ) {
					$date = $coupon['coupon_start_date'];
					$post_status = 'future';
				} else {
					$date = $coupon['created_on'];
					$post_status = 'publish';
				}
				$data = array(
					'post_type'			=> 'shop_coupon',
					'post_author'		=> $user_id,
					'post_date'			=> $date,
					'post_title'		=> $coupon['coupon_code'],
					'post_status'		=> ($coupon['published'] == 1)? $post_status: 'draft',
					'comment_status'	=> 'closed',
					'ping_status'		=> 'closed',
				);
				$coupon_id = wp_insert_post($data);
				if ( !empty($coupon_id) ) {
					$imported_coupons_count++;
					add_post_meta($coupon_id, '_fgj2wp_old_vm_coupon_id', $coupon['virtuemart_coupon_id'], true);
					$discount_type = ($coupon['percent_or_total'] == 'percent')? 'percent': 'fixed_cart';
					if ( $coupon['coupon_expiry_date'] == '0000-00-00 00:00:00' ) {
						$expiry_date = '';
					} else {
						$expiry_date = substr($coupon['coupon_expiry_date'], 0, 10); // Remove the hour
					}
					$usage_limit = ($coupon['coupon_type'] == 'permanent')? 0: 1;
					add_post_meta($coupon_id, 'minimum_amount', $coupon['coupon_value_valid'], true);
					add_post_meta($coupon_id, 'expiry_date', $expiry_date, true);
					add_post_meta($coupon_id, 'coupon_amount', $coupon['coupon_value'], true);
					add_post_meta($coupon_id, 'discount_type', $discount_type, true);
					add_post_meta($coupon_id, 'usage_limit', $usage_limit, true);
				}
				// Increment the Joomla last imported coupon ID
				update_option('fgj2wp_last_virtuemart_coupon_id', $coupon['virtuemart_coupon_id']);
			}
			$this->plugin->progressbar->increment_current_count($coupons_count);
			$this->plugin->display_admin_notice(sprintf(_n('%d Virtuemart coupon imported', '%d Virtuemart coupons imported', $imported_coupons_count, __CLASS__), $imported_coupons_count));
		}
		
		/**
		 * Import the Virtuemart manufacturers as WooCommerce brands
		 * 
		 */
		private function import_manufacturers() {
			$imported_manufacturers_count = 0;
			$term_metakey = '_fgj2wp_old_vm_manufacturer_id';
			$terms = array();
			
			// Set the list of previously imported brands
			$this->imported_brands = $this->plugin->get_term_metas_by_metakey($term_metakey);
			
			$manufacturers = $this->get_manufacturers();
			foreach ( $manufacturers as $manufacturer ) {

				// Check if the category is already imported
				if ( array_key_exists($manufacturer['virtuemart_manufacturer_id'], $this->imported_brands) ) {
					continue; // Do not import already imported category
				}
				
				// Insert the category
				$new_brand = array(
					'description'	=> isset($manufacturer['mf_desc'])? $manufacturer['mf_desc']: '',
					'slug'			=> $manufacturer['slug'],
				);
				
				// Hook before inserting the brand
				$new_brand = apply_filters('fgj2wp_pre_insert_brand', $new_brand, $manufacturer);
				
				$new_term = wp_insert_term($manufacturer['mf_name'], $this->brand_taxonomy, $new_brand);
				if ( !is_wp_error($new_term) ) {
					$imported_manufacturers_count++;
					$terms[] = $new_term['term_id'];
					
					// Store the Virtuemart manufacturer ID
					add_term_meta($new_term['term_id'], $term_metakey, $manufacturer['virtuemart_manufacturer_id'], true);
					
					// Manufacturer thumbnail
					if ( !$this->plugin->plugin_options['skip_media'] ) {
						$manufacturer_thumbnail = $this->get_manufacturer_thumbnail($manufacturer['virtuemart_manufacturer_id']);
						if ( !empty($manufacturer_thumbnail) && function_exists('update_woocommerce_term_meta') ) {
							$manufacturer_thumbnail_url = $manufacturer_thumbnail['file_url'];
							$alt_desc = !empty($manufacturer_thumbnail['file_description'])? $manufacturer_thumbnail['file_description'] : $manufacturer_thumbnail['file_title'];
							$attachment_id = $this->plugin->import_media($alt_desc, $manufacturer_thumbnail_url, '');
							if ( $attachment_id ) {
								$this->vm_images_count++;
								update_woocommerce_term_meta($new_term['term_id'], 'thumbnail_id', $attachment_id);
							}
						}
					}
					
					// Hook after inserting the brand
					do_action('fgj2wp_post_insert_brand', $new_term['term_id'], $manufacturer);
				}
			}
			
			// Set the list of previously imported brands
			$this->imported_brands = $this->plugin->get_term_metas_by_metakey($term_metakey);
			
			// Hook after importing all the brands
			do_action('fgj2wp_post_import_brands', $manufacturers);
			
			$this->plugin->display_admin_notice(sprintf(_n('%d Virtuemart manufacturer imported', '%d Virtuemart manufacturers imported', $imported_manufacturers_count, __CLASS__), $imported_manufacturers_count));
			
			// Update cache
			if ( !empty($terms) ) {
				clean_term_cache($terms, $this->brand_taxonomy);
			}
		}
		
		/**
		 * Import the Virtuemart users info
		 *
		 * @param array $users Users
		 */
		public function import_users_info($users) {
			global $wpdb;
			
			$this->vm_version = $this->get_virtuemart_version();
			
			// Get the states (for Joomla 1.5 only)
			$states = $this->get_states();
			
			// Get the country codes (for Joomla 1.5 only)
			$countries = $this->get_countries();
			
			$users_info = $this->get_users_info();
			foreach ( $users_info as $user_info ) {
				$user_id = $user_info['user_id'];
				if ( array_key_exists($user_id, $users) ) { // the user exists in the users table
					$user = $users[$user_id];
					if ( isset($user['new_id']) ) {
						$state = isset($states[$user_info['state']])? $states[$user_info['state']] : $user_info['state'];
						$country = isset($countries[$user_info['country']])? $countries[$user_info['country']] : $user_info['country'];
						if ( $user_info['address_type'] == 'ST' ) { // ShipTo
							update_user_meta($user['new_id'], 'shipping_company', $user_info['company']);
							update_user_meta($user['new_id'], 'shipping_last_name', $user_info['last_name']);
							update_user_meta($user['new_id'], 'shipping_first_name', $user_info['first_name']);
							update_user_meta($user['new_id'], 'shipping_address_1', $user_info['address_1']);
							update_user_meta($user['new_id'], 'shipping_address_2', $user_info['address_2']);
							update_user_meta($user['new_id'], 'shipping_city', $user_info['city']);
							update_user_meta($user['new_id'], 'shipping_state', $state);
							update_user_meta($user['new_id'], 'shipping_country', $country);
							update_user_meta($user['new_id'], 'shipping_postcode', $user_info['zip']);
						} else { // BillTo
							update_user_meta($user['new_id'], 'billing_company', $user_info['company']);
							update_user_meta($user['new_id'], 'billing_last_name', $user_info['last_name']);
							update_user_meta($user['new_id'], 'billing_first_name', $user_info['first_name']);
							update_user_meta($user['new_id'], 'billing_phone', $user_info['phone_1']);
							update_user_meta($user['new_id'], 'billing_address_1', $user_info['address_1']);
							update_user_meta($user['new_id'], 'billing_address_2', $user_info['address_2']);
							update_user_meta($user['new_id'], 'billing_city', $user_info['city']);
							update_user_meta($user['new_id'], 'billing_state', $state);
							update_user_meta($user['new_id'], 'billing_country', $country);
							update_user_meta($user['new_id'], 'billing_postcode', $user_info['zip']);
							update_user_meta($user['new_id'], 'billing_email', $user_info['user_email']);

							$this->vm_users_count++;
						}
						// Set the customer role
						$roles = get_user_meta($user['new_id'], $wpdb->prefix . 'capabilities', true);
						if ( empty($roles['administrator']) ) { // Don't modify the administrator users, otherwise they won't have access to the posts anymore (perhaps a WooCommerce bug?)
							if ( isset($roles['subscriber']) ) {
								unset($roles['subscriber']);
							}
							$roles['customer'] =  true;
							update_user_meta($user['new_id'], $wpdb->prefix . 'capabilities', $roles);
						}
					}
				}
			}
		}

		/**
		 * Get Virtuemart version
		 *
		 * @return string Virtuemart version
		 */
		private function get_virtuemart_version() {
			$version = '';
			if ( $this->plugin->table_exists('virtuemart_products') ) {
				if ( $this->plugin->column_exists('virtuemart_product_customfields', 'customfield_value') ) {
					$version = '3.0';
				} else {
					$version = '2.0';
				}
			} elseif ( $this->plugin->table_exists('vm_product') ) {
				if ( $this->plugin->column_exists('vm_product_attribute', 'attribute_id') ) {
					$version = '1.5';
				} else {
					$version = '1.0';
				}
			}
			return $version;
		}
		

		/**
		 * Get Virtuemart configuration
		 *
		 * @return string configuration
		 */
		private function get_configuration() {
			$config = '';
			$prefix = $this->plugin->plugin_options['prefix'];

			if ( version_compare($this->vm_version, '2.0', '<') ) {
				return $config;
			} else {
				// Joomla 2.5
				$sql = "
					SELECT c.config
					FROM ${prefix}virtuemart_configs c
					ORDER BY c.virtuemart_config_id DESC
					LIMIT 1
				";
			}
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$config = $row['config'];
			}
			return $config;
		}
		
		/**
		 * Get Virtuemart user info
		 *
		 * @return array of User infos
		 */
		private function get_users_info() {
			$users_info = array();
			$prefix = $this->plugin->plugin_options['prefix'];

			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT `user_id`,
					`address_type`,
					`company`,
					`last_name`,
					`first_name`,
					`phone_1`,
					`address_1`,
					`address_2`,
					`city`,
					`state`,
					`country`,
					`zip`,
					`user_email`
					FROM ${prefix}vm_user_info
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT `virtuemart_user_id` AS user_id,
					`address_type`,
					`company`,
					`last_name`,
					`first_name`,
					`phone_1`,
					`address_1`,
					`address_2`,
					`city`,
					s.`state_name` AS state,
					c.country_2_code AS `country`,
					`zip`,
					u.email AS user_email
					FROM ${prefix}virtuemart_userinfos ui
					LEFT JOIN ${prefix}virtuemart_states s ON s.virtuemart_state_id = ui.virtuemart_state_id
					LEFT JOIN ${prefix}virtuemart_countries c ON c.virtuemart_country_id = ui.virtuemart_country_id
					LEFT JOIN ${prefix}users u ON u.id = ui.virtuemart_user_id
				";
			}
			$users_info = $this->plugin->joomla_query($sql);
			return $users_info;
		}
		
		/**
		 * Get the states
		 *
		 * @return array of states
		 */
		private function get_states() {
			$states = array();
			if ( $this->plugin->table_exists('vm_state') ) {
				$prefix = $this->plugin->plugin_options['prefix'];
				$sql = "
					SELECT state_2_code, state_name
					FROM ${prefix}vm_state
				";
				$result = $this->plugin->joomla_query($sql);
				foreach ( $result as $row ) {
					$states[$row['state_2_code']] = $row['state_name'];
				}
			}
			return $states;
		}
		
		/**
		 * Get the countries
		 *
		 * @return array of countries
		 */
		private function get_countries() {
			$countries = array();
			if ( $this->plugin->table_exists('vm_country') ) {
				$prefix = $this->plugin->plugin_options['prefix'];

				$sql = "
					SELECT country_2_code, country_3_code
					FROM ${prefix}vm_country
				";
				$result = $this->plugin->joomla_query($sql);
				foreach ( $result as $row ) {
					$countries[$row['country_3_code']] = $row['country_2_code'];
				}
			}
			return $countries;
		}
		
		/**
		 * Get the products
		 * 
		 * @param int $limit Number of products max
		 * @return array of products
		 */
		private function get_products($limit=1000) {
			$products = array();

			$last_virtuemart_product_id = (int)get_option('fgj2wp_last_virtuemart_product_id'); // to restore the import where it left
			$default_shopper_group_id = $this->get_default_shopper_group();

			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.0 or 1.5
				$sql = "
					SELECT p.product_id, p.product_parent_id, p.product_sku, p.product_weight, p.product_length, p.product_width, p.product_height, p.product_in_stock, p.product_special, p.product_sales, p.product_publish AS published, 0 AS pordering, p.cdate, 0 AS created_by, p.product_url, p.product_s_desc, p.product_desc, p.product_name, p.product_name AS slug, p.product_full_image, p.attribute, i.product_price, p.product_tax_id, -1 AS product_discount_id, t.tax_rate, 0 AS discount_rate, -1 AS override, i.product_price - pd.amount/(1+t.tax_rate) AS product_override_price, pi.product_price AS parent_product_price, -1 AS parent_override, pi.product_price - ppd.amount/(1+pt.tax_rate) AS parent_product_override_price
					FROM ${prefix}vm_product p
					LEFT JOIN ${prefix}vm_product_price i ON i.product_id = p.product_id AND (i.shopper_group_id = '$default_shopper_group_id' OR i.shopper_group_id = 0 OR i.shopper_group_id IS NULL) AND (i.price_quantity_start = 0 OR i.price_quantity_start = 1 OR i.price_quantity_start IS NULL)
					LEFT JOIN ${prefix}vm_product_discount pd ON pd.discount_id = p.product_discount_id
					LEFT JOIN ${prefix}vm_tax_rate t ON t.tax_rate_id = p.product_tax_id
					LEFT JOIN ${prefix}vm_product pp ON pp.product_id = p.product_parent_id
					LEFT JOIN ${prefix}vm_product_price pi ON pi.product_id = pp.product_id AND (pi.shopper_group_id = '$default_shopper_group_id' OR pi.shopper_group_id = 0 OR pi.shopper_group_id IS NULL) AND (pi.price_quantity_start = 0 OR pi.price_quantity_start = 1 OR pi.price_quantity_start IS NULL)
					LEFT JOIN ${prefix}vm_product_discount ppd ON ppd.discount_id = pp.product_discount_id
					LEFT JOIN ${prefix}vm_tax_rate pt ON pt.tax_rate_id = pp.product_tax_id
					WHERE p.product_id > '$last_virtuemart_product_id'
					ORDER BY p.product_id
					LIMIT $limit
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT p.virtuemart_product_id AS product_id, p.product_parent_id, p.product_sku, p.product_weight, p.product_length, p.product_width, p.product_height, p.product_in_stock, p.product_special, p.product_sales, p.published, p.pordering, p.created_on, p.created_by, p.product_url, t.product_s_desc, t.product_desc, t.product_name, t.metadesc, t.metakey, t.customtitle AS metatitle, t.slug, i.product_price, i.product_tax_id, i.product_discount_id, c.calc_value/100 AS tax_rate, cd.calc_value/100 AS discount_rate, i.override, i.product_override_price, pi.product_price AS parent_product_price, pi.override AS parent_override, pi.product_override_price AS parent_product_override_price
					FROM ${prefix}virtuemart_products p
					INNER JOIN ${prefix}virtuemart_products_{$this->language_code} t ON t.virtuemart_product_id = p.virtuemart_product_id
					LEFT JOIN ${prefix}virtuemart_product_prices i ON i.virtuemart_product_id = p.virtuemart_product_id AND (i.virtuemart_shoppergroup_id = '$default_shopper_group_id' OR i.virtuemart_shoppergroup_id = 0 OR i.virtuemart_shoppergroup_id IS NULL) AND (i.price_quantity_start = 0 OR i.price_quantity_start = 1 OR i.price_quantity_start IS NULL)
					LEFT JOIN ${prefix}virtuemart_calcs c ON c.virtuemart_calc_id = i.product_tax_id
					LEFT JOIN ${prefix}virtuemart_calcs cd ON cd.virtuemart_calc_id = i.product_discount_id
					LEFT JOIN ${prefix}virtuemart_products pp ON pp.virtuemart_product_id = p.product_parent_id
					LEFT JOIN ${prefix}virtuemart_product_prices pi ON pi.virtuemart_product_id = pp.virtuemart_product_id AND (pi.virtuemart_shoppergroup_id = '$default_shopper_group_id' OR pi.virtuemart_shoppergroup_id = 0 OR pi.virtuemart_shoppergroup_id IS NULL) AND (pi.price_quantity_start = 0 OR pi.price_quantity_start = 1 OR pi.price_quantity_start IS NULL)
					WHERE p.virtuemart_product_id > '$last_virtuemart_product_id'
					ORDER BY p.virtuemart_product_id
					LIMIT $limit
				";
			}
			$products = $this->plugin->joomla_query($sql);
			return $products;
		}
		
		/**
		 * Get the default shopper group ID (required for getting the prices)
		 *
		 * @return int Default shopper group ID
		 */
		private function get_default_shopper_group() {
			$shopper_group_id = 0;
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT shopper_group_id
					FROM ${prefix}vm_shopper_group
					ORDER BY `default` DESC
					LIMIT 1
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT virtuemart_shoppergroup_id AS shopper_group_id
					FROM ${prefix}virtuemart_shoppergroups
					ORDER BY `default` DESC
					LIMIT 1
				";
			}
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				if ( isset($row['shopper_group_id']) ) {
					$shopper_group_id = $row['shopper_group_id'];
				}
			}
			return $shopper_group_id;
		}
		
		/**
		 * Get the product categories
		 *
		 * @return array of categories
		 */
		private function get_product_categories() {
			$categories = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT CONCAT('v', c.category_id) AS id, c.category_name AS title, c.category_description AS description, '' AS metadesc, '' AS metakey, '' AS customtitle, c.category_name AS name, CONCAT('v', x.category_parent_id) AS parent_id, c.list_order AS ordering, c.cdate, c.category_full_image AS file_url, '' AS file_description
					FROM ${prefix}vm_category c
					LEFT JOIN ${prefix}vm_category_xref x ON x.category_child_id = c.category_id
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT CONCAT('v', c.virtuemart_category_id) AS id, t.category_name AS title, t.category_description AS description, t.metadesc, t.metakey, t.customtitle, t.slug AS name, CONCAT('v', p.category_parent_id) AS parent_id, c.ordering, c.created_on, m.file_url, m.file_description
					FROM ${prefix}virtuemart_categories c
					INNER JOIN ${prefix}virtuemart_categories_{$this->language_code} t ON t.virtuemart_category_id = c.virtuemart_category_id
					LEFT JOIN ${prefix}virtuemart_category_categories p ON p.category_child_id = c.virtuemart_category_id
					LEFT JOIN ${prefix}virtuemart_category_medias cm ON cm.virtuemart_category_id = c.virtuemart_category_id
					LEFT JOIN ${prefix}virtuemart_medias m ON m.virtuemart_media_id = cm.virtuemart_media_id
				";
			}
			$categories = $this->plugin->joomla_query($sql);
			return $categories;
		}
		
		/**
		 * Get the categories from a product
		 *
		 * $param int $product_id Virtuemart product ID
		 * @return array of categories
		 */
		private function get_categories_from_product($product_id) {
			$categories = array();
			
			if ( $product_id != 0 ) {
				$prefix = $this->plugin->plugin_options['prefix'];
				if ( version_compare($this->vm_version, '2.0', '<') ) {
					// Joomla 1.5
					$sql = "
						SELECT CONCAT('v', pc.category_id) AS id
						FROM ${prefix}vm_product_category_xref pc
						WHERE pc.product_id = $product_id
					";
				} else {
					// Joomla 2.5
					$sql = "
						SELECT CONCAT('v', pc.virtuemart_category_id) AS id
						FROM ${prefix}virtuemart_product_categories pc
						WHERE pc.virtuemart_product_id = $product_id
						ORDER BY pc.ordering
					";
				}
				$result = $this->plugin->joomla_query($sql);
				foreach ( $result as $row ) {
					$categories[] = $row['id'];
				}
			}
			return $categories;
		}
		
		/**
		 * Get the product images
		 *
		 * $param int $product_id Product ID
		 * @return array of images
		 */
		private function get_product_images($product_id) {
			$images = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT m.file_title, m.file_description, m.file_url
					FROM ${prefix}vm_product_files m
					WHERE m.file_product_id = $product_id
					AND m.file_is_image = 1
					AND m.file_published = 1
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT m.file_title, m.file_description, m.file_url
					FROM ${prefix}virtuemart_medias m
					INNER JOIN ${prefix}virtuemart_product_medias pm ON pm.virtuemart_media_id = m.virtuemart_media_id AND pm.virtuemart_product_id = $product_id
					WHERE (m.file_mimetype LIKE 'image%' OR m.file_mimetype = '')
					ORDER BY pm.ordering
				";
			}
			$images = $this->plugin->joomla_query($sql);
			return $images;
		}
		
		/**
		 * Get the custom fields
		 *
		 * @return array of custom fields
		 */
		private function get_custom_fields() {
			$custom_fields = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT DISTINCT attribute_name AS virtuemart_custom_id, attribute_name AS custom_title, 'V' AS field_type, 0 AS ordering
					FROM ${prefix}vm_product_attribute_sku
					ORDER BY attribute_name
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT virtuemart_custom_id, custom_title, field_type, ordering
					FROM ${prefix}virtuemart_customs
					WHERE published = 1
					AND field_type NOT IN ('R', 'Z')
					ORDER BY virtuemart_custom_id
				";
			}
			$custom_fields = $this->plugin->joomla_query($sql);
			return $custom_fields;
		}
		
		/**
		 * Get the product custom fields
		 *
		 * @param int $product_id Product ID
		 * @return array of product custom fields
		 */
		private function get_product_custom_fields($product_id) {
			$product_custom_fields = array();

			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '1.5', '<') ) {
				// Virtuemart 1 on Joomla 1.0
				$sql = "
					SELECT CONCAT(a.product_id, a.attribute_name) AS virtuemart_customfield_id, a.attribute_name AS virtuemart_custom_id, a.attribute_name AS custom_title, 0 AS is_cart_attribute, a.attribute_value AS custom_value, 0 AS custom_price, 0 AS ordering
					FROM ${prefix}vm_product_attribute a
					WHERE a.product_id = '$product_id'
					AND a.attribute_value != 'product_sku'
				";
			} elseif ( version_compare($this->vm_version, '2.0', '<') ) {
				// Virtuemart 1 on Joomla 1.5
				$sql = "
					SELECT a.attribute_id AS virtuemart_customfield_id, a.attribute_name AS virtuemart_custom_id, a.attribute_name AS custom_title, 0 AS is_cart_attribute, a.attribute_value AS custom_value, 0 AS custom_price, a.attribute_id AS ordering
					FROM ${prefix}vm_product_attribute a
					WHERE a.product_id = '$product_id'
					AND a.attribute_value != 'product_sku'
					ORDER BY a.attribute_id
				";
			} elseif ( version_compare($this->vm_version, '3.0', '<') ) {
				// Virtuemart 2 on Joomla 2.5
				$sql = "
					SELECT pc.virtuemart_customfield_id, pc.virtuemart_custom_id, c.custom_title, c.is_cart_attribute, pc.custom_value, pc.custom_price, pc.ordering
					FROM ${prefix}virtuemart_product_customfields pc
					INNER JOIN ${prefix}virtuemart_customs c ON c.virtuemart_custom_id = pc.virtuemart_custom_id
					WHERE pc.virtuemart_product_id = '$product_id'
					AND pc.custom_value != 'product_sku'
					ORDER BY pc.ordering
				";
			} else {
				// Virtuemart 3 on Joomla 2.5
				$sql = "
					SELECT pc.virtuemart_customfield_id, pc.virtuemart_custom_id, c.custom_title, c.is_cart_attribute, pc.customfield_value AS custom_value, pc.customfield_price AS custom_price, pc.ordering
					FROM ${prefix}virtuemart_product_customfields pc
					INNER JOIN ${prefix}virtuemart_customs c ON c.virtuemart_custom_id = pc.virtuemart_custom_id
					WHERE pc.virtuemart_product_id = '$product_id'
					AND pc.customfield_value != 'product_sku'
					ORDER BY pc.ordering
				";
			}
			$product_custom_fields = $this->plugin->joomla_query($sql);
			return $product_custom_fields;
		}
		
		/**
		 * Get the product custom fields
		 *
		 * @param int $custom_id Custom ID
		 * @return array of product custom fields
		 */
		private function get_product_custom_fields_from_custom_id($custom_id) {
			$product_custom_fields = array();

			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '1.5', '<') ) {
				// Virtuemart 1 on Joomla 1.0
				$sql = "
					SELECT CONCAT(a.product_id, a.attribute_name) AS virtuemart_customfield_id, a. product_id AS virtuemart_product_id, a.attribute_name AS virtuemart_custom_id, a.attribute_name AS custom_title, a.attribute_value AS custom_value, 0 AS custom_price, 0 AS ordering
					FROM ${prefix}vm_product_attribute a
					WHERE a.attribute_name = '$custom_id'
				";
			} elseif ( version_compare($this->vm_version, '2.0', '<') ) {
				// Virtuemart 1 on Joomla 1.5
				$sql = "
					SELECT a.attribute_id AS virtuemart_customfield_id, a. product_id AS virtuemart_product_id, a.attribute_name AS virtuemart_custom_id, a.attribute_name AS custom_title, a.attribute_value AS custom_value, 0 AS custom_price, a.attribute_id AS ordering
					FROM ${prefix}vm_product_attribute a
					WHERE a.attribute_name = '$custom_id'
					ORDER BY a.attribute_id
				";
			} elseif ( version_compare($this->vm_version, '3.0', '<') ) {
				// Virtuemart 2 on Joomla 2.5
				$sql = "
					SELECT pc.virtuemart_customfield_id, pc.virtuemart_product_id, pc.virtuemart_custom_id, c.custom_title, pc.custom_value, pc.custom_price, pc.ordering
					FROM ${prefix}virtuemart_product_customfields pc
					INNER JOIN ${prefix}virtuemart_customs c ON c.virtuemart_custom_id = pc.virtuemart_custom_id
					WHERE pc.virtuemart_custom_id = '$custom_id'
					ORDER BY pc.ordering
				";
			} else {
				// Virtuemart 3 on Joomla 2.5
				$sql = "
					SELECT pc.virtuemart_customfield_id, pc.virtuemart_product_id, pc.virtuemart_custom_id, c.custom_title, pc.customfield_value AS custom_value, pc.customfield_price AS custom_price, pc.ordering
					FROM ${prefix}virtuemart_product_customfields pc
					INNER JOIN ${prefix}virtuemart_customs c ON c.virtuemart_custom_id = pc.virtuemart_custom_id
					WHERE pc.virtuemart_custom_id = '$custom_id'
					ORDER BY pc.ordering
				";
			}
			$product_custom_fields = $this->plugin->joomla_query($sql);
			return $product_custom_fields;
		}
		
		/**
		 * Set the WooCommerce products global variable
		 *
		 */
		public function get_imported_products() {
			$this->plugin->imported_products = $this->get_woocommerce_products();
			ksort($this->plugin->imported_products);
		}
		
		/**
		 * Get the WooCommerce products
		 *
		 * @return array of products mapped with the Virtuemart products ids
		 */
		private function get_woocommerce_products() {
			global $wpdb;
			$products = array();
			
			try {
				$sql = "
					SELECT post_id, meta_value
					FROM $wpdb->postmeta
					WHERE meta_key = '_fgj2wp_old_vm_product_id'
				";
				$rows = $wpdb->get_results($sql);
				foreach ( $rows as $row ) {
					$products[$row->meta_value] = $row->post_id;
				}
			} catch ( PDOException $e ) {
				$this->plugin->display_admin_error(__('Error:', get_class($this->plugin)) . $e->getMessage());
			}
			return $products;
		}
		
		/**
		 * Get the WooCommerce product categories
		 * (Used by fg2wp-sh404sef.php)
		 *
		 * @return array of product categories
		 */
		public function get_woocommerce_product_categories() {
			$products_categories = array();
			
			$products_categories_objects = get_terms('product_cat', array('hide_empty' => false));
			if ( !is_wp_error($products_categories_objects) ) {
				foreach ( $products_categories_objects as $term ) {
					$products_categories[$term->slug] = $term->term_id;
				}
			}
			return $products_categories;
		}
		
		/**
		 * Get the Virtuemart tax rates
		 *
		 * @return array of tax rates
		 */
		private function get_tax_rates() {
			$tax_rates = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT tax_rate_id AS virtuemart_calc_id, tax_rate_id AS calc_name, tax_rate*100 AS calc_value
					FROM ${prefix}vm_tax_rate
					ORDER BY tax_rate_id
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT virtuemart_calc_id, calc_name, calc_value
					FROM ${prefix}virtuemart_calcs
					WHERE published = 1
					AND calc_kind = 'VatTax'
					ORDER BY ordering
				";
			}
			$tax_rates = $this->plugin->joomla_query($sql);
			return $tax_rates;
		}
		
		/**
		 * Get the Virtuemart global tax and discount rates
		 *
		 * @return array of tax rates
		 */
		private function get_global_tax_and_discount_rates() {
			$tax_rates = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				return array();
			} else {
				// Joomla 2.5
				$sql = "
					SELECT c.calc_name, c.calc_value, c.calc_value_mathop
					FROM ${prefix}virtuemart_calcs c
					LEFT JOIN ${prefix}virtuemart_calc_categories cc ON cc.virtuemart_calc_id = c.virtuemart_calc_id
					WHERE c.published = 1
					AND cc.virtuemart_category_id IS NULL
					ORDER BY c.ordering
				";
			}
			$tax_rates = $this->plugin->joomla_query($sql);
			return $tax_rates;
		}
		
		/**
		 * Get the Virtuemart stock status and their stock handle
		 *
		 * @return array of status
		 */
		private function get_stock_status() {
			$status = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				return array();
			} else {
				// Joomla 2.5
				$sql = "
					SELECT order_status_code, order_stock_handle
					FROM ${prefix}virtuemart_orderstates
				";
			}
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$status[$row['order_status_code']] = $row['order_stock_handle'];
			}
			return $status;
		}
		
		
		/**
		 * Get the Virtuemart orders
		 *
		 * @param int $limit Number of orders max
		 * @return array of orders
		 */
		private function get_orders($limit=1000) {
			$orders = array();
			$last_virtuemart_order_id = (int)get_option('fgj2wp_last_virtuemart_order_id'); // to restore the import where it left
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Virtuemart 1 on Joomla 1.5
				$sql = "
					SELECT DISTINCT o.order_id AS virtuemart_order_id, o.user_id AS virtuemart_user_id, o.order_shipping AS order_shipment, o.coupon_discount, o.order_tax, o.ship_method_id AS virtuemart_shipmentmethod_id, o.order_shipping_tax AS order_shipment_tax, o.order_tax_details AS order_billTax, -o.order_discount AS order_payment, 0 AS order_payment_tax, o.order_total, o.order_number, '' AS order_pass, o.customer_note, o.order_status, o.ip_address, o.cdate,
					pm.payment_method_code AS payment_method, pm.payment_method_name AS payment_name,
					c.currency_code AS currency_code_3,
					uibt.first_name AS bt_first_name, uibt.last_name AS bt_last_name, uibt.company AS bt_company, uibt.phone_1 AS bt_phone_1, uibt.address_1 AS bt_address_1, uibt.address_2 AS bt_address_2, uibt.zip AS bt_zip, uibt.user_email AS email, uibt.city AS bt_city,
					sbt.`state_name` AS bt_state,
					cbt.country_2_code AS bt_country,
					uist.first_name AS st_first_name, uist.last_name AS st_last_name, uist.company AS st_company, uist.phone_1 AS st_phone_1, uist.address_1 AS st_address_1, uist.address_2 AS st_address_2, uist.zip AS st_zip, uist.city AS st_city,
					sst.`state_name` AS st_state,
					cst.country_2_code AS st_country
					FROM ${prefix}vm_orders o
					LEFT JOIN ${prefix}vm_order_payment op ON op.order_id = o.order_id
					LEFT JOIN ${prefix}vm_payment_method pm ON pm.payment_method_id = op.payment_method_id
					LEFT JOIN ${prefix}vm_currency c ON c.currency_id = o.order_currency
					LEFT JOIN ${prefix}vm_order_user_info uibt ON uibt.order_id = o.order_id AND uibt.user_id = o.user_id AND uibt.address_type = 'BT'
					LEFT JOIN ${prefix}vm_state sbt ON sbt.state_2_code = uibt.state
					LEFT JOIN ${prefix}vm_country cbt ON cbt.country_3_code = uibt.country
					LEFT JOIN ${prefix}vm_order_user_info uist ON uist.order_id = o.order_id AND uist.user_id = o.user_id AND uist.address_type = 'ST'
					LEFT JOIN ${prefix}vm_state sst ON sst.state_2_code = uist.state
					LEFT JOIN ${prefix}vm_country cst ON cst.country_3_code = uist.country
					WHERE o.order_id > '$last_virtuemart_order_id'
					ORDER BY o.order_id
					LIMIT $limit
				";
			} else {
				if ( version_compare($this->vm_version, '3.0', '<') ) {
					// Virtuemart 2 on Joomla 2.5
					$customer_note_field = 'o.customer_note';
				} else {
					// Virtuemart 3 on Joomla 2.5
					$customer_note_field = 'uibt.customer_note';
				}
				$sql = "
					SELECT DISTINCT o.virtuemart_order_id, o.virtuemart_user_id, o.order_shipment, o.coupon_discount, o.order_tax, o.virtuemart_shipmentmethod_id, o.order_shipment_tax, o.order_billTax, o.order_payment, o.order_payment_tax, o.order_total, o.order_number, o.order_pass, $customer_note_field, o.order_status, o.ip_address, o.created_on,
					pm.slug AS payment_method, pm.payment_name,
					c.currency_code_3,
					uibt.first_name AS bt_first_name, uibt.last_name AS bt_last_name, uibt.company AS bt_company, uibt.phone_1 AS bt_phone_1, uibt.address_1 AS bt_address_1, uibt.address_2 AS bt_address_2, uibt.zip AS bt_zip, uibt.email, uibt.city AS bt_city,
					sbt.`state_name` AS bt_state,
					cbt.country_2_code AS bt_country,
					uist.first_name AS st_first_name, uist.last_name AS st_last_name, uist.company AS st_company, uist.phone_1 AS st_phone_1, uist.address_1 AS st_address_1, uist.address_2 AS st_address_2, uist.zip AS st_zip, uist.city AS st_city,
					sst.`state_name` AS st_state,
					cst.country_2_code AS st_country
					FROM ${prefix}virtuemart_orders o
					LEFT JOIN ${prefix}virtuemart_paymentmethods_{$this->language_code} pm ON pm.virtuemart_paymentmethod_id = o.virtuemart_paymentmethod_id
					LEFT JOIN ${prefix}virtuemart_currencies c ON c.virtuemart_currency_id = o.order_currency
					LEFT JOIN ${prefix}virtuemart_order_userinfos uibt ON uibt.virtuemart_order_id = o.virtuemart_order_id AND uibt.virtuemart_user_id = o.virtuemart_user_id AND uibt.address_type = 'BT'
					LEFT JOIN ${prefix}virtuemart_states sbt ON sbt.virtuemart_state_id = uibt.virtuemart_state_id
					LEFT JOIN ${prefix}virtuemart_countries cbt ON cbt.virtuemart_country_id = uibt.virtuemart_country_id
					LEFT JOIN ${prefix}virtuemart_order_userinfos uist ON uist.virtuemart_order_id = o.virtuemart_order_id AND uist.virtuemart_user_id = o.virtuemart_user_id AND uist.address_type = 'ST'
					LEFT JOIN ${prefix}virtuemart_states sst ON sst.virtuemart_state_id = uist.virtuemart_state_id
					LEFT JOIN ${prefix}virtuemart_countries cst ON cst.virtuemart_country_id = uist.virtuemart_country_id
					WHERE o.virtuemart_order_id > '$last_virtuemart_order_id'
					ORDER BY o.virtuemart_order_id
					LIMIT $limit
				";
			}
			$orders = $this->plugin->joomla_query($sql);
			return $orders;
		}
		
		/**
		 * Get the Virtuemart order items
		 *
		 * @param int $order_id Order ID
		 * @return array of order items
		 */
		private function get_order_items($order_id) {
			$order_items = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT order_item_id AS virtuemart_order_item_id, product_id AS virtuemart_product_id, order_item_name, product_quantity, product_final_price - product_item_price AS product_tax, product_item_price AS product_discountedPriceWithoutTax, product_final_price, product_attribute
					FROM ${prefix}vm_order_item
					WHERE order_id = '$order_id'
				";
			} else {
				// Compatitibility issue
				if ( $this->plugin->column_exists('virtuemart_order_items', 'product_discountedPriceWithoutTax') ) {
					$discounted_price_column = 'product_discountedPriceWithoutTax';
				} else {
					$discounted_price_column = 'product_item_price AS product_discountedPriceWithoutTax';
				}
				// Joomla 2.5
				$sql = "
					SELECT virtuemart_order_item_id, virtuemart_product_id, order_item_name, product_quantity, product_tax, $discounted_price_column, product_final_price, product_attribute
					FROM ${prefix}virtuemart_order_items
					WHERE virtuemart_order_id = '$order_id'
				";
			}
			$order_items = $this->plugin->joomla_query($sql);
			return $order_items;
		}
		
		/**
		 * Get the Virtuemart shipment methods
		 *
		 * @return array of shipment methods
		 */
		private function get_shipments_methods() {
			$shipment_methods = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				return array();
			} else {
				// Joomla 2.5
				$sql = "
					SELECT virtuemart_shipmentmethod_id, shipment_name
					FROM ${prefix}virtuemart_shipmentmethods_{$this->language_code}
				";
			}
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$shipment_methods[$row['virtuemart_shipmentmethod_id']] = $row['shipment_name'];
			}
			return $shipment_methods;
		}
		
		/**
		 * Get the Virtuemart reviews
		 *
		 * @return array of reviews
		 */
		private function get_reviews() {
			$reviews = array();
			$last_review_id = (int)get_option('fgj2wp_last_virtuemart_review_id'); // to restore the import where it left
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '1.5', '<') ) {
				// Joomla 1.0
				$sql = "
					SELECT r.time AS virtuemart_rating_review_id, r.product_id AS virtuemart_product_id, r.comment, r.user_rating AS review_rating, '' AS lastip, 1 AS published, r.time, userid AS created_by,
					u.name AS author, u.email AS author_email
					FROM ${prefix}vm_product_reviews r
					LEFT JOIN ${prefix}users u ON u.id = r.userid
					WHERE r.time > '$last_review_id'
					ORDER BY r.time
				";
			} elseif ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT r.review_id AS virtuemart_rating_review_id, r.product_id AS virtuemart_product_id, r.comment, r.user_rating AS review_rating, '' AS lastip, r.published, r.time, userid AS created_by,
					u.name AS author, u.email AS author_email
					FROM ${prefix}vm_product_reviews r
					LEFT JOIN ${prefix}users u ON u.id = r.userid
					WHERE r.review_id > '$last_review_id'
					ORDER BY r.review_id
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT r.virtuemart_rating_review_id, r.virtuemart_product_id, r.comment, r.review_rating, r.lastip, r.published, r.created_on, r.created_by,
					u.name AS author, u.email AS author_email
					FROM ${prefix}virtuemart_rating_reviews r
					LEFT JOIN ${prefix}users u ON u.id = r.created_by
					WHERE r.virtuemart_rating_review_id > '$last_review_id'
					ORDER BY r.virtuemart_rating_review_id
				";
			}
			$reviews = $this->plugin->joomla_query($sql);
			return $reviews;
		}
		
		/**
		 * Get the Virtuemart coupons
		 *
		 * @return array of coupons
		 */
		private function get_coupons() {
			$coupons = array();
			
			$last_coupon_id = (int)get_option('fgj2wp_last_virtuemart_coupon_id'); // to restore the import where it left
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT coupon_id AS virtuemart_coupon_id, coupon_code, percent_or_total, coupon_type, coupon_value, '0000-00-00 00:00:00' AS coupon_start_date, '0000-00-00 00:00:00' AS coupon_expiry_date, 0 AS coupon_value_valid, 1 AS published, NOW() AS created_on, 0 AS created_by
					FROM ${prefix}vm_coupons
					WHERE coupon_id > '$last_coupon_id'
					ORDER BY coupon_id
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT virtuemart_coupon_id, coupon_code, percent_or_total, coupon_type, coupon_value, coupon_start_date, coupon_expiry_date, coupon_value_valid, published, created_on, created_by
					FROM ${prefix}virtuemart_coupons
					WHERE virtuemart_coupon_id > '$last_coupon_id'
					ORDER BY virtuemart_coupon_id
				";
			}
			$coupons = $this->plugin->joomla_query($sql);
			return $coupons;
		}
		
		/**
		 * Get the Virtuemart manufacturers
		 * 
		 * @return array of manufacturers
		 */
		private function get_manufacturers() {
			$manufacturers = array();
			
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT m.manufacturer_id AS virtuemart_manufacturer_id, m.mf_name, m.mf_desc, m.mf_name AS slug
					FROM ${prefix}vm_manufacturer m
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT m.virtuemart_manufacturer_id, ml.mf_name, ml.mf_desc, ml.slug
					FROM ${prefix}virtuemart_manufacturers m
					LEFT JOIN ${prefix}virtuemart_manufacturers_{$this->language_code} ml ON ml.virtuemart_manufacturer_id = m.virtuemart_manufacturer_id
					WHERE m.published = 1
				";
			}
			$manufacturers = $this->plugin->joomla_query($sql);
			return $manufacturers;
		}
		
		/**
		 * Get the Virtuemart manufacturer thumbnail
		 * 
		 * @param int $manufacturer_id Manufacturer ID
		 * @return array [file_title, file_description, file_url] or false
		 */
		private function get_manufacturer_thumbnail($manufacturer_id) {
			$thumbnail = false;
			
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '>=') ) {
				// Joomla 2.5
				$sql = "
					SELECT m.file_title, m.file_description, m.file_url
					FROM ${prefix}virtuemart_medias m
					INNER JOIN ${prefix}virtuemart_manufacturer_medias mm ON mm.virtuemart_media_id = m.virtuemart_media_id
					WHERE mm.virtuemart_manufacturer_id = '$manufacturer_id'
					ORDER BY mm.ordering ASC
					LIMIT 1
				";
				$thumbnails = $this->plugin->joomla_query($sql);
				if ( count($thumbnails) > 0 ) {
					$thumbnail = $thumbnails[0];
				}
			}
			return $thumbnail;
		}
		
		/**
		 * Get the Virtuemart manufacturers for a product
		 * 
		 * @param int $product_id Product ID
		 * @return array Manufacturers IDs
		 */
		private function get_manufacturers_from_product($product_id) {
			$manufacturers_ids = array();
			
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.5
				$sql = "
					SELECT pm.manufacturer_id AS virtuemart_manufacturer_id
					FROM ${prefix}vm_product_mf_xref pm
					WHERE pm.product_id = '$product_id'
				";
			} else {
				// Joomla 2.5
				$sql = "
					SELECT pm.virtuemart_manufacturer_id
					FROM ${prefix}virtuemart_product_manufacturers pm
					WHERE pm.virtuemart_product_id = '$product_id'
				";
			}
			$manufacturers = $this->plugin->joomla_query($sql);
			foreach ( $manufacturers as $manufacturer ) {
				$manufacturers_ids[] = $manufacturer['virtuemart_manufacturer_id'];
			}
			return $manufacturers_ids;
		}
		
		/**
		 * Add the Virtuemart menus in the menu query
		 */
		public function add_menus_extra_criteria($extra_criteria) {
			$sql = "
					OR (type = 'component'
						AND link LIKE '%option=com_virtuemart%'
						AND (link LIKE '%id=%')
						)
			";
			return $extra_criteria . $sql;
		}
		
		/**
		 * Get the menu item data (object_id, type, url, object)
		 * 
		 * @param array $menu_item Menu item row
		 * @param array $menu Menu
		 * @param string $post_type Post type
		 * @return array Menu item
		 */
		public function get_menu_item($menu_item, $menu, $post_type) {
			$matches = array();
			if ( !is_null($menu_item) ) {
				return $menu_item;
			}
			$menu_item_object_id = 0;
			$menu_item_type = '';
			$menu_item_url = '';
			$menu_item_object = '';
			switch ( $menu['type'] ) {
				case 'component':
					if ( preg_match('/view=productdetails(&.*)?&virtuemart_product_id=(\d+)/', $menu['link'], $matches) ) {
						// Virtuemart product
						$menu_item_type = 'post_type';
						$menu_item_object = 'product';
						$product_id = $matches[2];
						if ( array_key_exists($product_id, $this->plugin->imported_products) ) {
							$menu_item_object_id = $this->plugin->imported_products[$product_id];
						} else {
							return;
						}
						
					} elseif ( preg_match('/view=category(&.*)?&virtuemart_category_id=(\d+)/', $menu['link'], $matches) ) {
						// Virtuemart product category
						$menu_item_type = 'taxonomy';
						$menu_item_object = 'product_cat';
						$jterm_id = 'v' . $matches[2];
						if ( array_key_exists($jterm_id, $this->plugin->imported_categories) ) {
							$menu_item_object_id = $this->plugin->imported_categories[$jterm_id];
						} else {
							return;
						}

					} else {
						return;
					}
					break;
				
				default: return;
			}
			
			return array(
				'object_id'	=> $menu_item_object_id,
				'type'		=> $menu_item_type,
				'url'		=> $menu_item_url,
				'object'	=> $menu_item_object,
			);
		}
		
		/**
		 * Get a matched WooCommerce product from a Virtuemart URL
		 * Used to modify Joomla internal links
		 *
		 * @param Post $post WordPress post
		 * @param string $url Virtuemart URL
		 * @return Post WordPress post
		 */
		public function get_wc_product_from_virtuemart_url($post, $url) {
			$matches = array();
			$seo_suffix = get_option('fgj2wp_virtuemart_seosuffix');
			$url = preg_replace('#.*/#', '', $url);
			$url = preg_replace('#\.html#', '', $url);
			
			// Try to find the product by its slug
			if ( preg_match('#(.*)' . $seo_suffix . '$#', $url, $matches) ) {
				$slug = $matches[1];
				if ( !empty($slug) ) {
					$posts = get_posts(array(
						'name'           => $slug,
						'post_type'      => 'product',
						'post_status'    => 'publish',
						'posts_per_page' => 1
					));
					if ( is_array($posts) && (count($posts) > 0) ) {
						$post = $posts[0];
					}
				}
			}
			return $post;
		}
		
		/**
		 * Set the product translation (called by the Joom!Fish module)
		 * 
		 * @param array $new_post New post data
		 * @param array $product Virtuemart product
		 * @param array $translation Translation data
		 */
		public function set_product_translation($new_post, $product, $translation) {
			if ( $new_post['post_type'] == 'product' ) {
				// Save the translated taxonomies
				$tax_input = $new_post['tax_input'];
				
				$product = array_merge($product, $translation);
				list($post_media, $product_medias) = $this->build_post_media($product);
				$new_post = $this->build_post($product, $post_media);
				// Restore the translated taxonomies
				$new_post['tax_input'] = $tax_input;
			}
			return $new_post;
		}
		
		/**
		 * Set the product meta data translations (called by the Joom!Fish module)
		 * 
		 * @param int $new_post_id Post ID
		 * @param array $product Virtuemart product
		 * @param int $new_translated_post_id Translated post ID
		 */
		public function set_product_metadata_translation($new_post_id, $product, $new_translated_post_id) {
			if ( isset($product['product_id']) ) {
				// Copy the original metadata to the translated post
				$metadata = get_post_meta($new_post_id);
				foreach ($metadata as $metakey => $metavalues ) {
					foreach ( $metavalues as $metavalue ) {
						add_post_meta($new_translated_post_id, $metakey, $metavalue, false);
					}
				}
			}
		}
		
		/**
		 * Set the category meta data
		 * 
		 * @since 2.4.0
		 * 
		 * @param type $term_id WooCommerce category ID
		 * @param type $category VM category data
		 */
		private function set_category_meta_data($term_id, $category) {
			if ( $this->plugin->premium_options['import_meta_seo'] ) {
				$set = false;
				$wpseo_taxonomy_meta = get_option('wpseo_taxonomy_meta');
				
				// SEO title
				if ( array_key_exists('customtitle', $category) && !empty($category['customtitle']) ) {
					$wpseo_taxonomy_meta['product_cat'][$term_id]['wpseo_title'] = $category['customtitle'];
					$set = true;
				}
				
				// Meta description
				if ( array_key_exists('metadesc', $category) && !empty($category['metadesc']) ) {
					$wpseo_taxonomy_meta['product_cat'][$term_id]['wpseo_desc'] = $category['metadesc'];
					$set = true;
				}
				
				// Meta keywords
				if ( array_key_exists('metakey', $category) && !empty($category['metakey']) ) {
					$wpseo_taxonomy_meta['product_cat'][$term_id]['wpseo_metakey'] = $category['metakey'];
					$set = true;
				}
				
				if ( $set ) {
					update_option('wpseo_taxonomy_meta', $wpseo_taxonomy_meta);
				}
			}
		}
		
		/**
		 * Remove the filter "keep Joomla IDs" if the "keep Virtuemart products IDs" option is set
		 * 
		 */
		public function remove_keep_joomla_id() {
			if ( $this->vm_options['keep_vm_id'] ) {
				remove_filter('fgj2wp_pre_insert_post', array($this->plugin, 'add_import_id'));
			}
		}
		
		/**
		 * Set the posts table autoincrement to the last Virtuemart ID + 100
		 * 
		 */
		public function set_posts_autoincrement() {
			global $wpdb;
			if ( $this->vm_options['keep_vm_id'] ) {
				$last_vm_article_id = $this->get_last_vm_product_id() + 100;
				$sql = "ALTER TABLE $wpdb->posts AUTO_INCREMENT = $last_vm_article_id";
				$wpdb->query($sql);
			}
		}
		
		/**
		 * Get the last Virtuemart product ID
		 *
		 * @return int Last Virtuemart product ID
		 */
		private function get_last_vm_product_id() {
			$max_id = 0;
			$prefix = $this->plugin->plugin_options['prefix'];

			if ( version_compare($this->vm_version, '2.0', '<') ) {
				// Joomla 1.0 or 1.5
				$sql = "
					SELECT MAX(p.product_id) AS max_id
					FROM ${prefix}vm_product p
				";
			} else {
				// Joomla 2.5+
				$sql = "
					SELECT MAX(p.virtuemart_product_id) AS max_id
					FROM ${prefix}virtuemart_products p
				";
			}
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$max_id = $row['max_id'];
				break;
			}
			return $max_id;
		}
		
		/**
		 * Keep the Virtuemart ID
		 * 
		 * @param array $new_post New post
		 * @param array $product Virtuemart product
		 * @return array Post
		 */
		public function add_import_id($new_post, $product) {
			if ( $this->vm_options['keep_vm_id'] ) {
				$new_post['import_id'] = $product['product_id'];
			}
			return $new_post;
		}
		
		/**
		 * Display the number of imported Virtuemart users
		 * 
		 */
		public function display_vm_users_count() {
			$this->plugin->display_admin_notice(sprintf(_n('%d Virtuemart user info imported', '%d Virtuemart users infos imported', $this->vm_users_count, __CLASS__), $this->vm_users_count));
		}

		/**
		 * Display the number of imported Virtuemart images
		 * 
		 */
		public function display_vm_images_count() {
			$this->plugin->display_admin_notice(sprintf(_n('%d Virtuemart image imported', '%d Virtuemart images imported', $this->vm_images_count, __CLASS__), $this->vm_images_count));
		}

		/**
		 * Add information to the admin page
		 * 
		 * @param array $data
		 * @return array
		 */
		public function process_admin_page($data) {
			$data['title'] .= ' ' . __('+ Virtuemart module', __CLASS__);
			$data['description'] .= "<br />" . __('The Virtuemart module will also import the Virtuemart content (products, orders, users infos).', __CLASS__);
			
			return $data;
		}
		
		/**
		 * Get the WordPress database info
		 * 
		 * @param string $database_info Database info
		 * @return string Database info
		 */
		public function get_database_info($database_info) {
			
			// Products
			$products_count = $this->plugin->count_posts('product');
			$database_info .= sprintf(_n('%d product', '%d products', $products_count, __CLASS__), $products_count) . "<br />";
			
			// Orders
			$orders_count = $this->plugin->count_posts('shop_order');
			$database_info .= sprintf(_n('%d order', '%d orders', $orders_count, __CLASS__), $orders_count) . "<br />";
			
			return $database_info;
		}

		/**
		 * Add Virtuemart partial import options to the admin page
		 * 
		 */
		public function display_partial_import_options($data) {
			echo '					<br />';
			echo '					<input id="skip_products" name="skip_products" type="checkbox" value="1" ' . checked($data['skip_products'], 1, 0) .' /> <label for="skip_products" >' . __('Don\'t import the products', __CLASS__) . '</label>';
			echo '					<br />';
			echo '					<input id="skip_attributes" name="skip_attributes" type="checkbox" value="1" ' . checked($data['skip_attributes'], 1, 0) .' /> <label for="skip_attributes" >' . __('Don\'t import the attributes', __CLASS__) . '</label>';
			echo '					<br />';
			echo '					<input id="skip_variations" name="skip_variations" type="checkbox" value="1" ' . checked($data['skip_variations'], 1, 0) .' /> <label for="skip_variations" >' . __('Don\'t import the variations', __CLASS__) . '</label>';
			echo '					<br />';
			echo '					<input id="skip_orders" name="skip_orders" type="checkbox" value="1" ' . checked($data['skip_orders'], 1, 0) .' /> <label for="skip_orders" >' . __('Don\'t import the orders', __CLASS__) . '</label>';
			echo '					<br />';
			echo '					<input id="skip_reviews" name="skip_reviews" type="checkbox" value="1" ' . checked($data['skip_reviews'], 1, 0) .' /> <label for="skip_reviews" >' . __('Don\'t import the reviews', __CLASS__) . '</label>';
			echo '					<br />';
			echo '					<input id="skip_coupons" name="skip_coupons" type="checkbox" value="1" ' . checked($data['skip_coupons'], 1, 0) .' /> <label for="skip_coupons" >' . __('Don\'t import the coupons', __CLASS__) . '</label>';
			echo '					<br />';
			echo '					<input id="skip_manufacturers" name="skip_manufacturers" type="checkbox" value="1" ' . checked($data['skip_manufacturers'], 1, 0) .' /> <label for="skip_manufacturers" >' . __('Don\'t import the manufacturers', __CLASS__) . '</label>';
		}
		
		/**
		 * Add Virtuemart options to the admin page
		 * 
		 */
		public function display_virtuemart_options() {
			echo '<tr><th>' . __('Virtuemart:', __CLASS__) . '</th><td>';
			echo __('Import prices:', __CLASS__);
			echo '&nbsp;&nbsp;<input type="radio" name="vm_price" id="vm_price_without_tax" value="without_tax" ' . checked($this->vm_options['vm_price'], 'without_tax', false) . ' /> <label for="vm_price_without_tax">' . __('excluding tax', __CLASS__) . '</label>';
			echo '&nbsp;&nbsp;<input type="radio" name="vm_price" id="vm_price_with_tax" value="with_tax" ' . checked($this->vm_options['vm_price'], 'with_tax', false) . ' /> <label for="vm_price_with_tax">' . __('including tax', __CLASS__) . '</label><br />';
			echo '<input id="vm_first_image_not_in_gallery" name="vm_first_image_not_in_gallery" type="checkbox" value="1" ' . checked($this->vm_options['vm_first_image_not_in_gallery'], 1, false) . ' /> <label for="vm_first_image_not_in_gallery" >' . __("Don't include the first image into the product gallery", __CLASS__) . '</label><br />';
			echo '<input id="vm_stock_management" name="vm_stock_management" type="checkbox" value="1" ' . checked($this->vm_options['vm_stock_management'], 1, false) . ' /> <label for="vm_stock_management" >' . __("Enable stock management", __CLASS__) . '</label><br />';
			echo '<input type="checkbox" name="keep_vm_id" id="keep_vm_id" value="1" ' . checked($this->vm_options['keep_vm_id'], 1, false) . ' /> <label for="keep_vm_id">' . __('Keep the Virtuemart products IDs. <sub>Note that the Joomla articles IDs won\'t be kept.</sub>', __CLASS__) . '</label><br />';
			echo '</td></tr>';
		}
		
		/**
		 * Add Virtuemart options help to the help tab
		 * 
		 */
		public function display_virtuemart_options_help() {
			echo '<h2>Virtuemart options</h2>
<p><strong>Import prices:</strong> You can import the prices including tax or excluding tax.</p>
<p><strong>Remove the first image from the product gallery:</strong> The first product image will remain as the featured product image but will not be included into the product gallery.</p>
<p><strong>Keep the Virtuemart products IDs:</strong> With this option checked, the WooCommerce products IDs will be the same as the Virtuemart products. If you choose this option, you need to empty all the WordPress content before the import. This option is not compatible with the "Keep the Joomla articles IDs" option.</p>
';
		}
		
	}
}
