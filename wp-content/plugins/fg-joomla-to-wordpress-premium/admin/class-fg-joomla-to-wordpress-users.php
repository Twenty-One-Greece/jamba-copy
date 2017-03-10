<?php

/**
 * Users module
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      2.0.0
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Joomla_to_WordPress_Users', false) ) {

	/**
	 * Users class
	 *
	 * @package    FG_Joomla_to_WordPress_Premium
	 * @subpackage FG_Joomla_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Joomla_to_WordPress_Users {

		private $users = array();
		private $users_count = 0;

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
		 * Add user cols in the get_posts request
		 *
		 * @param string $cols
		 * @return string Cols separating by commas (with a comma at start)
		 */
		public function add_user_cols_in_get_posts($cols) {
			$cols .= ', p.created_by, p.created_by_alias';
			return $cols;
		}

		/**
		 * Delete all users except the current user
		 *
		 * @param string $action all|imported
		 */
		public function delete_users($action) {
			global $wpdb;
			
			$sql_queries = array();

			if ( $action == 'all' ) {
				
				// Delete all users except the current user
				$current_user = get_current_user_id();
				if ( is_multisite() ) {
					$blogusers = get_users(array('exclude' => $current_user));
					foreach ( $blogusers as $user ) {
						wp_delete_user($user->ID);
					}
				} else { // monosite (quicker)
					$sql_queries[] = <<<SQL
-- Delete User meta
DELETE FROM $wpdb->usermeta
WHERE user_id != '$current_user'
SQL;

				$sql_queries[] = <<<SQL
-- Delete Users
DELETE FROM $wpdb->users
WHERE ID != '$current_user'
SQL;

					// Execute SQL queries
					if ( count($sql_queries) > 0 ) {
						foreach ( $sql_queries as $sql ) {
							$wpdb->query($sql);
						}
					}
				}
				$this->reset_users_autoincrement();
				
			} else {
				
				// Delete only the imported users
				
				// Truncate the temporary table
				$sql_queries[] = <<<SQL
TRUNCATE {$wpdb->prefix}fg_data_to_delete;
SQL;
				
				// Insert the imported users IDs in the temporary table
				$sql_queries[] = <<<SQL
INSERT IGNORE INTO {$wpdb->prefix}fg_data_to_delete (`id`)
SELECT user_id FROM $wpdb->usermeta
WHERE meta_key LIKE '_fgj2wp_%'
SQL;
				
				$sql_queries[] = <<<SQL
-- Delete Users and user metas
DELETE u, um
FROM $wpdb->users u
LEFT JOIN $wpdb->usermeta um ON um.user_id = u.ID
INNER JOIN {$wpdb->prefix}fg_data_to_delete del
WHERE u.ID = del.id;
SQL;

				// Execute SQL queries
				if ( count($sql_queries) > 0 ) {
					foreach ( $sql_queries as $sql ) {
						$wpdb->query($sql);
					}
				}

			}
			wp_cache_flush();
			
			// Reset the Joomla last imported user ID
			update_option('fgj2wp_last_user_id', 0);

			$this->plugin->display_admin_notice(__('Users deleted', $this->plugin->get_plugin_name()));
		}

		/**
		 * Reset the wp_users autoincrement
		 */
		private function reset_users_autoincrement() {
			global $wpdb;
			
			$sql = "SELECT IFNULL(MAX(ID), 0) + 1 FROM $wpdb->users";
			$max_id = $wpdb->get_var($sql);
			$sql = "ALTER TABLE $wpdb->users AUTO_INCREMENT = $max_id";
			$wpdb->query($sql);
		}
		
		/**
		 * Define the array of users
		 * 
		 */
		public function get_users_array() {
			$users = $this->get_authors();
			$users = apply_filters('fgj2wpp_post_get_authors', $users);
			foreach ( $users as $user ) {
				$user['roles'] = $this->user_roles($user);
				$this->users[$user['id']] = $user;
			}
		}
		
		/**
		 * Get all the Joomla users
		 * 
		 * @param int $limit Number of users max
		 * @return array Users
		 */
		protected function get_users($limit=1000) {
			$users = array();
			$last_user_id = (int)get_option('fgj2wp_last_user_id'); // to restore the import where it left
			$prefix = $this->plugin->plugin_options['prefix'];
			$extra_cols = '';
			if ( version_compare($this->plugin->joomla_version, '1.5', '<=') ) {
				$extra_cols = ', u.usertype'; // User group
			}
			$sql = "
				SELECT u.id, u.name, u.username, u.email, u.password, u.registerDate
				$extra_cols
				FROM ${prefix}users u
				WHERE u.id > '$last_user_id'
				ORDER BY u.id
				LIMIT $limit
			";
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$users[$row['id']] = $row;
			}
			return $users;
		}
		
		/**
		 * Get all the Joomla authors
		 * 
		 * @return array Users
		 */
		protected function get_authors() {
			$users = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			$extra_cols = '';
			if ( version_compare($this->plugin->joomla_version, '1.5', '<=') ) {
				$extra_cols = ', u.usertype'; // User group
			}
			$sql = "
				SELECT DISTINCT u.id, u.name, u.username, u.email, u.password, u.registerDate
				$extra_cols
				FROM ${prefix}users u
				INNER JOIN ${prefix}content c ON c.created_by = u.id
			";
			$users = $this->plugin->joomla_query($sql);
			return $users;
		}
		
		/**
		 * Determine the Joomla user roles
		 *
		 * @param array $user
		 * @return array User roles
		 */
		protected function user_roles($user) {
			$user_roles = array();
			if ( version_compare($this->plugin->joomla_version, '1.5', '<=') ) {
				if ( isset($user['usertype']) ) {
					$roles = array($user['usertype']);
				} else {
					$roles = array();
				}
			} else {
				$roles = $this->get_user_roles($user['id']);
			}
			foreach ( $roles as $role ) {
				$user_roles[] = FG_Joomla_to_WordPress_Tools::convert_to_latin($role);
			}
			return $user_roles;
		}

		/**
		 * Get the Joomla user roles
		 *
		 * @param int $user_id User ID
		 * @return array User roles
		 */
		protected function get_user_roles($user_id) {
			$user_roles = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT ug.title AS role
				FROM ${prefix}usergroups ug
				INNER JOIN ${prefix}user_usergroup_map m ON m.group_id = ug.id AND m.user_id = '$user_id'
			";
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$user_roles[] = $row['role'];
			}
			return $user_roles;
		}

		/**
		 * Import the author of a post
		 * 
		 * @param array $newpost WordPress post
		 * @param array $joomla_post Joomla post
		 * @return array WordPress post
		 */
		public function import_author($newpost, $joomla_post) {
			$joomla_user_id = $joomla_post['created_by'];
			if ( array_key_exists($joomla_user_id, $this->users) ) {
				$user = $this->users[$joomla_user_id];
				// Check if the user is administrator or not
				$role = $this->is_admin($user)? 'administrator': 'author';
				$user_id = $this->add_user($user['name'], $user['username'], $user['email'], $user['password'], $joomla_user_id, $user['registerDate'], $role);
				if ( !is_wp_error($user_id) ) {
					$newpost['post_author'] = $user_id;
				}
			}
			return $newpost;
		}

		/**
		 * Import the author alias of a post
		 * 
		 * @param int $new_post_id WordPress post ID
		 * @param array $joomla_post Joomla post
		 */
		public function import_author_alias($new_post_id, $joomla_post) {
			if ( !empty($joomla_post['created_by_alias']) ) {
				update_post_meta($new_post_id, 'author_alias', $joomla_post['created_by_alias']);
			}
		}

		/**
		 * Import all the users
		 * 
		 */
		public function import_users() {
			if ( isset($this->plugin->premium_options['skip_users']) && $this->plugin->premium_options['skip_users'] ) {
				return;
			}
			if ( $this->plugin->import_stopped() ) {
				return;
			}
			
			$this->plugin->log(__('Importing users...', $this->plugin->get_plugin_name()));
			
			// Hook for other actions
			do_action('fgj2wpp_pre_import_users', $this->users);
			
			do {
				if ( $this->plugin->import_stopped() ) {
					return;
				}
				$users = $this->get_users($this->plugin->chunks_size);
				$users_count = count($users);
				foreach ( $users as &$user ) {
					// Check if the user is administrator or not
					$user['roles'] = $this->user_roles($user);
					$role = $this->is_admin($user)? 'administrator': 'subscriber';
					$user_id = $this->add_user($user['name'], $user['username'], $user['email'], $user['password'], $user['id'], $user['registerDate'], $role);
					do_action('fgj2wpp_post_add_user', $user_id, $user);
					if ( !is_wp_error($user_id) ) {
						$user['new_id'] = $user_id;
					}
					// Increment the Joomla last imported user ID
					update_option('fgj2wp_last_user_id', $user['id']);
				}
				
				// Hook for other actions
				do_action('fgj2wpp_post_import_users', $users);
				
				$this->plugin->progressbar->increment_current_count($users_count);
				
			} while ( ($users != null) && ($users_count > 0) );
			
			$this->plugin->display_admin_notice(sprintf(_n('%d user imported', '%d users imported', $this->users_count, $this->plugin->get_plugin_name()), $this->users_count));
		}
		
		/**
		 * Test if the user is an administrator
		 */
		private function is_admin($user) {
			foreach ( $user['roles'] as $role ) {
				if ( (stripos($role, 'Admin') !== false) || (stripos($role, 'Super') !== false) ) {
					return true;
				}
			}
			return false;
		}
		
		/**
		 * Add a user if it does not exists
		 *
		 * @param string $name User's name
		 * @param string $login Login
		 * @param string $email User's email
		 * @param string $password User's password in Joomla
		 * @param int $joomla_user_id User's id in Joomla
		 * @param string $register_date Registration date
		 * @param string $role User's role - default: subscriber
		 * @return int User ID
		 */
		private function add_user($name, $login, $email, $password, $joomla_user_id, $register_date='', $role='subscriber') {
			$matches = array();
			
			$login = FG_Joomla_to_WordPress_Tools::convert_to_latin(remove_accents($login));
			$login = sanitize_user($login, true);
			$email = sanitize_email($email);

			$display_name = $name;

			// Get the first and last name
			if ( preg_match("/(\w+) *(.*)/u", $name, $matches) ) {
				$first_name = $matches[1];
				$last_name = $matches[2];
			}
			else {
				$first_name = $name;
				$last_name = '';
			}
			$user = get_user_by('slug', $login);
			if ( !$user ) {
				$user = get_user_by('email', $email);
			}
			if ( !$user ) {
				// Create a new user
				$userdata = array(
					'user_login'		=> $login,
					'user_pass'			=> wp_generate_password( 12, false ),
					'user_email'		=> $email,
					'display_name'		=> $display_name,
					'first_name'		=> $first_name,
					'last_name'			=> $last_name,
					'user_registered'	=> $register_date,
					'role'				=> $role,
				);
				$userdata = apply_filters('fgj2wpp_pre_insert_user', $userdata);
				$user_id = wp_insert_user( $userdata );
				if ( is_wp_error($user_id) ) {
					//$this->plugin->display_admin_error(sprintf(__('Creating user %s: %s', $this->plugin->get_plugin_name()), $login, $user_id->get_error_message()));
				} else {
					$this->users_count++;
					add_user_meta($user_id, '_fgj2wp_old_user_id', $joomla_user_id, true);
					if ( !empty($password) ) {
						// Joomla password to authenticate the users
						add_user_meta($user_id, 'joomlapass', $password, true);
					}
					//$this->plugin->display_admin_notice(sprintf(__('User %s created', $this->plugin->get_plugin_name()), $login));
				}
			}
			else {
				$user_id = $user->ID;
				global $blog_id;
				if ( is_multisite() && $blog_id && !is_user_member_of_blog($user_id) ) {
					// Add user to the current blog (in multisite)
					add_user_to_blog($blog_id, $user_id, $role);
					$this->users_count++;
				}
			}
			return $user_id;
		}
		
		/**
		 * Update the number of total elements found in Joomla
		 * 
		 * @param int $count Number of total elements
		 * @return int Number of total elements
		 */
		public function get_total_elements_count($count) {
			if ( !isset($this->plugin->premium_options['skip_users']) || !$this->plugin->premium_options['skip_users'] ) {
				$count += $this->get_users_count();
			}
			return $count;
		}

		/**
		 * Get the number of Joomla users
		 * 
		 * @return int Number of users
		 */
		private function get_users_count() {
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT COUNT(*) AS nb
				FROM ${prefix}users u
			";
			$result = $this->plugin->joomla_query($sql);
			$users_count = isset($result[0]['nb'])? $result[0]['nb'] : 0;
			return $users_count;
		}

		/**
		 * Get users info
		 *
		 * @param string $message Message to display when displaying Joomla info
		 * @return string Message
		 */
		public function get_users_info($message) {
			// Users
			$users_count = $this->get_users_count();
			$message .= sprintf(_n('%d user', '%d users', $users_count, $this->plugin->get_plugin_name()), $users_count) . '<br />';
			
			return $message;
		}
		
	}
}
