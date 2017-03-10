<?php

/**
 * Users alias module
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      2.0.0
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/public
 */

if ( !class_exists('FG_Joomla_to_WordPress_Users_Alias', false) ) {

	/**
	 * Users alias class
	 *
	 * @package    FG_Joomla_to_WordPress_Premium
	 * @subpackage FG_Joomla_to_WordPress_Premium/public
	 * @author     Frédéric GILLES
	 */
	class FG_Joomla_to_WordPress_Users_Alias {

		/**
		 * Use the author_alias defined in the postmeta instead of the author display name
		 *
		 * @param string $author_name Author name
		 * @return string Author name
		 */
		public static function the_author($author_name) {
			
			global $post;
			
			if ( is_a($post, 'WP_Post') && !is_author() ) {
				$author_alias = get_post_meta($post->ID, 'author_alias', true);
				if ( !empty($author_alias) ) {
					$author_name = $author_alias;
				}
			}
			return $author_name;
		}
	}
}
