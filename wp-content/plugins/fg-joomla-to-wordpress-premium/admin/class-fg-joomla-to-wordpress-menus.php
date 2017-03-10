<?php

/**
 * Navigation menus module
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      2.0.0
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Joomla_to_WordPress_Menus', false) ) {

	/**
	 * Navigation menus class
	 *
	 * @package    FG_Joomla_to_WordPress_Premium
	 * @subpackage FG_Joomla_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Joomla_to_WordPress_Menus {

		private $menus = array(); // Menus to import
		private $all_menus = array(); // All Joomla menus
		private $menus_count = 0;
		protected $post_type = 'post';		// post or page
		private $plugin;

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    2.0.0
		 * @param    object    $plugin       Admin plugin
		 */
		public function __construct( $plugin ) {

			$this->plugin = $plugin;

		}

		/**
		 * Import the navigation menus
		 * 
		 */
		public function import_menus() {
			if ( isset($this->plugin->premium_options['skip_menus']) && $this->plugin->premium_options['skip_menus'] ) {
				return;
			}
			if ( $this->plugin->import_stopped() ) {
				return;
			}
			
			$this->plugin->log(__('Importing menus...', $this->plugin->get_plugin_name()));
			
			// Get all the menus with their alias and parent IDs
			$this->all_menus = $this->get_all_menus();
			
			// Define the lists of imported posts, categories, custom posts and custom taxonomies to make the links between Joomla and WordPress objects
			$this->plugin->imported_posts = $this->plugin->get_imported_joomla_posts();
			do_action('fgj2wp_pre_import_menus');
			
			$this->post_type = ($this->plugin->plugin_options['import_as_pages'] == 1) ? 'page' : 'post';
			
			$this->menus = $this->get_menus();
			$all_menus_count = count($this->menus);
			$offset = 0;
			do {
				if ( $this->plugin->import_stopped() ) {
					return;
				}
				$imported_menus = array();
				$menus = array_slice($this->menus, $offset, $this->plugin->chunks_size, true);
				$menus_count = count($menus);
				foreach ( $menus as $menu ) {
					$new_menu = $this->add_menu($menu);
					if ( !empty($new_menu) ) {
						$imported_menus[$new_menu['joomla_menu_id']] = $new_menu;
					}
					// Increment the Joomla last imported menu ID
					update_option('fgj2wp_last_menu_id', $menu['id']);
				}
				
				$this->plugin->progressbar->increment_current_count($menus_count);
				
				do_action('fgj2wp_post_import_menus', $imported_menus);
				
				$offset += $this->plugin->chunks_size;
			} while ( $offset < $all_menus_count );
			
			$this->plugin->display_admin_notice(sprintf(_n('%d menu item imported', '%d menu items imported', $this->menus_count, $this->plugin->get_plugin_name()), $this->menus_count));
		}
		
		/**
		 * Get all the Joomla menus with their alias and parent ID
		 * 
		 */
		private function get_all_menus() {
			$menus = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->plugin->joomla_version, '1.0', '<=') ) {
				$alias_col = 'name AS alias';
			} else {
				$alias_col = 'alias';
			}
			if ( version_compare($this->plugin->joomla_version, '1.5', '<=') ) {
				$parent_col = 'parent';
			} else {
				$parent_col = 'parent_id AS parent';
			}
			$sql = "
				SELECT id, $alias_col, $parent_col
				FROM ${prefix}menu
				ORDER BY id
			";
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$menus[$row['id']] = $row;
			}
			return $menus;
		}
		
		/**
		 * Get the Joomla menus
		 * 
		 * @return array Menus
		 */
		protected function get_menus() {
			$menus = array();

			$last_menu_id = (int)get_option('fgj2wp_last_menu_id'); // to restore the import where it left
			$prefix = $this->plugin->plugin_options['prefix'];
			if ( version_compare($this->plugin->joomla_version, '1.0', '<=') ) {
				$alias_col = 'm.name AS alias';
			} else {
				$alias_col = 'm.alias';
			}
			if ( version_compare($this->plugin->joomla_version, '1.5', '<=') ) {
				$name_col = 'm.name';
				$parent_col = 'm.parent';
			} else {
				$name_col = 'm.title AS name';
				$parent_col = 'm.parent_id AS parent';
			}

			// Hooks for adding extra cols and extra criteria
			$extra_cols = apply_filters('fgj2wp_get_menus_add_extra_cols', '');
			$extra_joins = apply_filters('fgj2wp_get_menus_add_extra_joins', '');
			$extra_criteria = apply_filters('fgj2wp_get_menus_add_extra_criteria', '');

			$sql = "
				SELECT m.id, m.menutype, $name_col, $alias_col, m.link, m.type, $parent_col, m.params
				$extra_cols
				FROM ${prefix}menu m
				$extra_joins
				WHERE (m.type = 'url'
				OR (m.type = 'component'
					AND m.link LIKE '%option=com_content%'
					AND (m.link LIKE '%id=%'
						 OR m.link LIKE '%view=frontpage%'
						 OR m.link LIKE '%view=featured%'
						 )
					)
				OR (m.type = 'separator')
				OR (m.type = 'alias')
				OR (m.type = 'menulink')
					$extra_criteria
				)
				AND m.published = 1
				AND m.id > '$last_menu_id'
				ORDER BY m.id
			";
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$menus[$row['id']] = $row;
			}
			return $menus;
		}
		
		/**
		 * Update the number of total elements found in Joomla
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			if ( !isset($this->plugin->premium_options['skip_menus']) || !$this->plugin->premium_options['skip_menus'] ) {
				$count += $this->get_menus_count();
			}
			return $count;
		}
		
		/**
		 * Get the number of menus
		 * 
		 * @return int Number of menus
		 */
		private function get_menus_count() {
			$count = 0;
			$prefix = $this->plugin->plugin_options['prefix'];
			// Hooks for adding extra cols and extra criteria
			$extra_criteria = apply_filters('fgj2wp_get_menus_add_extra_criteria', '');

			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}menu
				WHERE (type = 'url'
				OR (type = 'component'
					AND link LIKE '%option=com_content%'
					AND (link LIKE '%id=%'
						 OR link LIKE '%view=frontpage%'
						 OR link LIKE '%view=featured%'
						 )
					)
				OR (type = 'separator')
				OR (type = 'alias')
				OR (type = 'menulink')
					$extra_criteria
				)
				AND published = 1
			";
			$result = $this->plugin->joomla_query($sql);
			if ( isset($result[0]['nb']) ) {
				$count = $result[0]['nb'];
			}
			return $count;
		}
		
		/**
		 * Add a menu
		 *
		 * @param array $menu Nav menu
		 * @return mixed (array: $new_menu Imported menu | false)
		 */
		private function add_menu($menu) {
			$new_menu = false;
			
			// Get the menu
			$menu_obj = wp_get_nav_menu_object($menu['menutype']);
			
			if ( $menu_obj ) {
				$menu_id = $menu_obj->term_id;
			} else {
				// Create the menu
				$menu_id = wp_create_nav_menu($menu['menutype']);
				add_term_meta($menu_id, '_fgj2wp_old_menu_id', $menu['menutype']);
				do_action('fgj2wp_post_create_nav_menu', $menu_id, $menu);
			}
			
			if ( !is_null($menu_id) && !is_a($menu_id, 'WP_Error') ) {
				// Get the menu item data
				$menu_item = $this->get_menu_item($menu, $this->post_type);
				if ( is_null($menu_item) ) {
					$menu_item = apply_filters('fgj2wp_get_menu_item', $menu_item, $menu, $this->post_type);
				}
				if ( !is_null($menu_item) ) {
					// Create the menu item
					$menu_item_id = $this->add_menu_item($menu_item, $menu_id, $this->get_parent_menu_id($menu['parent']), $menu);
					if ( is_int($menu_item_id) ) {
						// Add the Joomla ID as a post meta in order to not import it again
						add_post_meta($menu_item_id, '_fgj2wp_old_menu_item_id', $menu['id'], true);
						
						// Add a redirect for the menu item
						$this->add_menu_item_redirect($menu_item, $menu);
						
						$new_menu = array(
							'joomla_menu_id'	=> $menu['id'],
							'menutype'			=> $menu['menutype'],
							'name'				=> $menu['name'],
							'parent'			=> $menu['parent'],
							'object_id'			=> $menu_item['object_id'],
							'type'				=> $menu_item['type'],
							'url'				=> $menu_item['url'],
							'object'			=> $menu_item['object'],
						);
					}
				}
				
				do_action('fgj2wp_post_add_menu', $menu_item, $menu);
				
				return $new_menu;
			}
		}
		
		/**
		 * Get the menu item data (object_id, type, url, object)
		 * 
		 * @param array $menu Menu item row
		 * @param string $post_type Post type
		 * @return array Menu item || null
		 */
		private function get_menu_item($menu, $post_type) {
			$menu_item_object_id = 0;
			$menu_item_type = '';
			$menu_item_url = '';
			$menu_item_object = '';
			$matches = array();
			switch ( $menu['type'] ) {
				case 'component':
					if ( preg_match('/option=com_content/', $menu['link']) ) {
						if ( preg_match('/view=article(&.*)?&id=(\d+)/', $menu['link'], $matches) ) {
							// post
							$article_id = $matches[2];
							$menu_item_type = 'post_type';
							$menu_item_object = $post_type;
							if ( array_key_exists($article_id, $this->plugin->imported_posts) ) {
								$menu_item_object_id = $this->plugin->imported_posts[$article_id];
							} else {
								return;
							}

						} elseif ( preg_match('/view=(section|category)(&.*)?&id=(\d+)/', $menu['link'], $matches) ) {
							// section or category
							$menu_item_type = 'taxonomy';
							$menu_item_object = 'category';

							$taxonomy = $matches[1];
							$jterm_id = $matches[3];
							if ( $taxonomy == 'section' ) {
								$jterm_id = 's' . $jterm_id;
							}
							if ( array_key_exists($jterm_id, $this->plugin->imported_categories) ) {
								$menu_item_object_id = $this->plugin->imported_categories[$jterm_id];
							} else {
								return;
							}

						} elseif ( preg_match('/view=(frontpage|featured|categories)/', $menu['link'], $matches) ) {
							// front page
							$menu_item_type = 'custom';
							$menu_item_object = 'custom';
							$menu_item_url = home_url();

						} else {
							return;
						}
					} else {
						return;
					}
					break;
				
				case 'url':
					$menu_item_type = 'custom';
					$menu_item_object = 'custom';
					$menu_item_url = $menu['link'];
					if ( strpos($menu_item_url, 'http://') !== 0 ) { // relative URL
						$menu_item_url = home_url() . '/' . $menu['link'];
					}
					break;
				
				case 'alias': // Joomla > 1.5
					$params = json_decode($menu['params'], true);
					$menu_alias_id = $params['aliasoptions'];
					if ( array_key_exists($menu_alias_id, $this->menus) ) {
						return $this->get_menu_item($this->menus[$menu_alias_id], $post_type);
					}
					break;
				
				case 'menulink': // Joomla 1.5
					$params = parse_ini_string($menu['params'], false, INI_SCANNER_RAW);
					$menu_alias_id = $params['menu_item'];
					if ( array_key_exists($menu_alias_id, $this->menus) ) {
						return $this->get_menu_item($this->menus[$menu_alias_id], $post_type);
					}
					break;
				
				case 'separator':
					$menu_item_type = 'custom';
					$menu_item_object = 'custom';
					$menu_item_url = '';
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
		 * Get the WordPress menu ID from the first known Joomla parent menu
		 *
		 * @param int $joomla_menu_id Joomla menu ID
		 * @return int WordPress menu ID or 0 if not found
		 */
		private function get_parent_menu_id($joomla_menu_id) {
			$menu_item_id = $this->get_menu_id($joomla_menu_id);
			if ( $menu_item_id != 0 ) {
				return $menu_item_id;
			} elseif ( isset($this->all_menus[$joomla_menu_id]) ) {
				return $this->get_parent_menu_id($this->all_menus[$joomla_menu_id]['parent']); // Recursively find the first known parent
			} else {
				return 0;
			}
		}
		
		/**
		 * Add a menu item
		 * 
		 * @since 3.14.0
		 * 
		 * @param array $menu_item Menu item
		 * @param int $menu_id WordPress menu ID
		 * @param int $menu_parent_id Menu parent ID
		 * @param array $menu Menu
		 * @return int Menu item ID
		 */
		private function add_menu_item($menu_item, $menu_id, $menu_parent_id, $menu) {
			$menu_data = array(
				'menu-item-object-id'	=> $menu_item['object_id'],
				'menu-item-type'		=> $menu_item['type'],
				'menu-item-url'			=> $menu_item['url'],
				'menu-item-db-id'		=> 0,
				'menu-item-object'		=> $menu_item['object'],
				'menu-item-parent-id'	=> $menu_parent_id,
				'menu-item-position'	=> 0,
				'menu-item-title'		=> $menu['name'],
				'menu-item-description'	=> '',
				'menu-item-status'		=> 'publish',
			);
			$menu_item_id = wp_update_nav_menu_item($menu_id, 0, $menu_data);
			if ( is_int($menu_item_id) ) {
				$this->menus_count++;
				// Create submenus
				if ( $this->plugin->premium_options['create_submenus'] && ($menu_item['object'] == 'category') ) {
					$this->add_category_submenus($menu_item, $menu_item_id, $menu_id);
					
					do_action('fgj2wp_post_create_nav_menu_item', $menu_item_id, $menu);
				}
			}
			return $menu_item_id;
		}
		
		/**
		 * Get the menu item path. It is a concatenation of the parent aliases.
		 *
		 * @param int $joomla_menu_id Joomla menu ID
		 * @return string Menu item path
		 */
		private function get_menu_item_path($joomla_menu_id) {
			$menu_path = '';
			if ( isset($this->all_menus[$joomla_menu_id]) ) {
				$menu_path = $this->all_menus[$joomla_menu_id]['alias'];
				if ( $this->all_menus[$joomla_menu_id]['parent'] != 0 ) {
					$menu_path = $this->get_menu_item_path($this->all_menus[$joomla_menu_id]['parent']) . '/' . $menu_path; // Recursively find the parent path
				}
			}
			return $menu_path;
		}
		
		/**
		 * Get the WordPress menu ID from a Joomla menu ID
		 *
		 * @param int $joomla_menu_id Joomla menu ID
		 * @return int WordPress menu ID or 0 if not found
		 */
		private function get_menu_id($joomla_menu_id) {
			$menu_item_id = 0;
			if ( !empty($joomla_menu_id) ) {
				$parent_posts = get_posts(array(
					'posts_per_page'  => 1,
					'post_type'       => 'nav_menu_item',
					'meta_key'        => '_fgj2wp_old_menu_item_id',
					'meta_value'      => $joomla_menu_id,
				));
				if ( is_array($parent_posts) && count($parent_posts) > 0 ) {
					$menu_item_id = $parent_posts[0]->ID;
				}
			}
			return $menu_item_id;
		}
		
		/**
		 * Set the post meta data from the menu meta data
		 *
		 * @param array $menu_item Menu item
		 * @param array $menu Menu
		 */
		public function set_meta_data_from_menu($menu_item, $menu) {
			if ( $this->plugin->premium_options['get_metadata_from_menu'] ) {
				if ( !empty($menu_item['object_id']) ) {
					$params = json_decode($menu['params'], true);
					if ( is_array($params) ) {
						$metatitle = isset($params['page_title'])? $params['page_title'] : '';
						$metadesc = isset($params['menu-meta_description'])? $params['menu-meta_description'] : '';
						$metakey = isset($params['menu-meta_keywords'])? $params['menu-meta_keywords'] : '';
						if ( !empty($metatitle) || !empty($metadesc) || !empty($metakey) ) {
							// Update the post meta data
						   $this->plugin->set_meta_seo($menu_item['object_id'], array(
							   'metatitle'	=> $metatitle,
							   'metadesc'	=> $metadesc,
							   'metakey'	=> $metakey,
						   ));
						}
						// Set the post tags with the meta keys
						if ( $this->plugin->plugin_options['meta_keywords_in_tags'] && !empty($metakey) ) {
							$tags = explode(',', $metakey);
							wp_set_post_terms($menu_item['object_id'], $tags, 'post_tag');
						}
					}
				}
			}
		}
		
		/**
		 * Set the post slug from the menu slug
		 *
		 * @param array $menu_item Menu item
		 * @param array $menu Menu
		 */
		public function set_post_slugs_from_menu($menu_item, $menu) {
			if ( $this->plugin->premium_options['get_slug_from_menu'] ) {
				if ( in_array($menu_item['object'], array('post', 'page')) && !empty($menu_item['object_id']) ) {
					// Update the post name into the database
					wp_update_post(array(
						'ID'			=> $menu_item['object_id'],
						'post_name'		=> $menu['alias'],
					));
				}
			}
		}
		
		/**
		 * Set the parent pages from the menu hierarchy
		 *
		 * @param array $menus Menus
		 */
		public function set_parent_pages_from_menus($menus) {
			if ( $this->plugin->plugin_options['import_as_pages'] ) {
				foreach ( $menus as $menu ) {
					if ( ($menu['object'] == 'page') && !empty($menu['object_id']) && ($menu['parent'] != 0) ) {
						if ( array_key_exists($menu['parent'], $menus) ) {
							$parent_menu = $menus[$menu['parent']];
							if ( ($parent_menu['object'] == 'page') && !empty($parent_menu['object_id']) ) {
								// Update the post parent field into the database
								wp_update_post(array(
									'ID'			=> $menu['object_id'],
									'post_parent'	=> $parent_menu['object_id'],
								));
							}
						}
					}
				}
			}
		}
		
		/**
		 * Add a redirect for the menu item
		 */
		public function add_menu_item_redirect($menu_item, $menu) {
			$type = $menu_item['object'];
			if ( in_array($type, array('post', 'category')) ) {
				$url = $this->get_menu_item_path($menu['id']) . '.html';
				FG_Joomla_to_WordPress_Redirect::add_redirect($url, $menu_item['object_id'], $type);
			}
		}
		
		/**
		 * Add submenus for a category
		 * 
		 * @since 3.14.0
		 * 
		 * @param array $menu_item Menu item
		 * @param int $menu_item_id Menu item ID
		 * @param int $menu_id Menu ID
		 */
		private function add_category_submenus($menu_item, $menu_item_id, $menu_id) {
			// Get the subcategories
			$subcategories = get_categories(array('parent' => $menu_item['object_id']));
			foreach ( $subcategories as $subcategory ) {
				$submenu_item = array(
					'object_id' => $subcategory->term_id,
					'type' => 'taxonomy',
					'url' => '',
					'object' => 'category',
				);
				$menu = array('name' => $subcategory->name);
				$this->add_menu_item($submenu_item, $menu_id, $menu_item_id, $menu);
			}
		}
		
		/**
		 * Reset the stored last menu id when emptying the database
		 * 
		 */
		public function reset_last_menu_id() {
			update_option('fgj2wp_last_menu_id', 0);
		}

	}
}
