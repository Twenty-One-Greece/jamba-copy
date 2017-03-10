<?php

/**
 * Users authentication module
 * Authenticate the WordPress users using the imported Joomla passwords
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      2.0.0
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/public
 */

if ( !class_exists('FG_Joomla_to_WordPress_Users_Authenticate', false) ) {

	/**
	 * Users authentication class
	 *
	 * @package    FG_Joomla_to_WordPress_Premium
	 * @subpackage FG_Joomla_to_WordPress_Premium/public
	 * @author     Frédéric GILLES
	 */
	class FG_Joomla_to_WordPress_Users_Authenticate {

		/**
		 * Authenticate a user using his Joomla password
		 *
		 * @param WP_User $user User data
		 * @param string $username User login entered
		 * @param string $password Password entered
		 * @return WP_User User data
		 */
		public static function auth_signon($user, $username, $password) {
			
			if ( is_a($user, 'WP_User') ) {
				// User is already identified
				return $user;
			}
			
			if ( empty($username) || empty($password) ) {
				return $user;
			}
			
			$wp_user = get_user_by('login', $username);
			if ( !is_a($wp_user, 'WP_User') ) {
				// username not found in WP users
				return $user;
			}
			
			// Get the imported joomlapass
			$joomlapass = get_user_meta($wp_user->ID, 'joomlapass', true);
			if ( empty($joomlapass) ) {
				return $user;
			}
			
			// Authenticate the user using the joomla password
			if ( self::auth_joomla($username, $password, $joomlapass) ) {
				// Update WP user password
				add_filter('send_password_change_email', '__return_false'); // Prevent an email to be sent
				wp_update_user(array('ID' => $wp_user->ID, 'user_pass' => $password));
				// To prevent the user to log in again with his Joomla password once he has successfully logged in. The following times, his password stored in WordPress will be used instead.
				delete_user_meta($wp_user->ID, 'joomlapass');
				
				return $wp_user;
			}
			
			return $user;
		}
		
		/**
		 * Joomla user authentication
		 *
		 * @param string $username User login entered
		 * @param string $password Password entered
		 * @param string $joomlapass Password stored in the WP usermeta table
		 */
		private static function auth_joomla($username, $password, $joomlapass) {
			
			// If we are using phpass
			if (strpos($joomlapass, '$P$') === 0) {
				// Use PHPass's portable hashes with a cost of 10.
				return self::CheckPassword($password, $joomlapass);
				
			}
			elseif ($joomlapass[0] == '$') {
				return wp_check_password($password, $joomlapass);
				
			} else {
				if ( strpos($joomlapass, ':') > 0 ) {
					// encrypted password with salt
					list($hash, $salt) = explode(':', $joomlapass);
				} else {
					// encrypted password without salt
					$hash = $joomlapass;
					$salt = '';
				}
				$encrypted = ($salt) ? md5($password.$salt) : md5($password);
				return ($hash == $encrypted);
			}
		}
		
		private static function CheckPassword($password, $stored_hash)
		{
			$hash = self::crypt_private($password, $stored_hash);
			if ($hash[0] == '*') {
				$hash = crypt($password, $stored_hash);
			}

			return $hash == $stored_hash;
		}
		
		/**
		 * Joomla function to crypt a password
		 * @param string $password
		 * @param string $setting
		 * @return string
		 */
		private static function crypt_private($password, $setting) {
			$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			$output = '*0';
			if (substr($setting, 0, 2) == $output) {
				$output = '*1';
			}

			$id = substr($setting, 0, 3);
			# We use "$P$", phpBB3 uses "$H$" for the same thing
			if ($id != '$P$' && $id != '$H$') {
				return $output;
			}

			$count_log2 = strpos($itoa64, $setting[3]);
			if ($count_log2 < 7 || $count_log2 > 30) {
				return $output;
			}

			$count = 1 << $count_log2;

			$salt = substr($setting, 4, 8);
			if (strlen($salt) != 8) {
				return $output;
			}

			# We're kind of forced to use MD5 here since it's the only
			# cryptographic primitive available in all versions of PHP
			# currently in use.  To implement our own low-level crypto
			# in PHP would result in much worse performance and
			# consequently in lower iteration counts and hashes that are
			# quicker to crack (by non-PHP code).
			if (PHP_VERSION >= '5') {
				$hash = md5($salt . $password, TRUE);
				do {
					$hash = md5($hash . $password, TRUE);
				} while (--$count);
			} else {
				$hash = pack('H*', md5($salt . $password));
				do {
					$hash = pack('H*', md5($hash . $password));
				} while (--$count);
			}

			$output = substr($setting, 0, 12);
			$output .= self::encode64($hash, 16);

			return $output;
		}
		
		private static function encode64($input, $count) {
			$itoa64 = './0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
			$output = '';
			$i = 0;
			do {
				$value = ord($input[$i++]);
				$output .= $itoa64[$value & 0x3f];
				if ($i < $count) {
					$value |= ord($input[$i]) << 8;
				}
				$output .= $itoa64[($value >> 6) & 0x3f];
				if ($i++ >= $count) {
					break;
				}
				if ($i < $count) {
					$value |= ord($input[$i]) << 16;
				}
				$output .= $itoa64[($value >> 12) & 0x3f];
				if ($i++ >= $count) {
					break;
				}
				$output .= $itoa64[($value >> 18) & 0x3f];
			} while ($i < $count);

			return $output;
		}
	}
}
