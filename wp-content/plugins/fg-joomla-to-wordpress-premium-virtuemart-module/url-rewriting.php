<?php
/**
 * Virtuemart URL Rewriting module
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      1.16.0
 *
 * @package    FG_Joomla_to_WordPress_Premium_Virtuemart
 * @subpackage FG_Joomla_to_WordPress_Premium_Virtuemart/public
 */

if ( !class_exists('FG_Joomla_to_WordPress_Premium_Virtuemart_URL_Rewriting', false) ) {

	/**
	 * URL Rewriting class
	 *
	 * @package    FG_Joomla_to_WordPress_Premium_Virtuemart
	 * @subpackage FG_Joomla_to_WordPress_Premium_Virtuemart/public
	 * @author     Frédéric GILLES
	 */
	class FG_Joomla_to_WordPress_Premium_Virtuemart_URL_Rewriting {

		private static $rewrite_rules = array(
			array( 'rule' => '^.*/(\d+)/',		'view' => 'post',		'meta_key' => '_fgj2wp_old_vm_product_id'),
			array( 'rule' => '^.*/(\d+)-.*?',	'view' => 'post',		'meta_key' => '_fgj2wp_old_vm_product_id'),
		);

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    1.16.0
		 */
		public function __construct() {

			add_filter('fgj2wpp_rewrite_rules', array('FG_Joomla_to_WordPress_Premium_Virtuemart_URL_Rewriting', 'add_rewrite_rules'), 10, 1);
			add_action('fgj2wpp_post_404_redirect', array('FG_Joomla_to_WordPress_Premium_Virtuemart_URL_Rewriting', 'post_404_redirect'));
		}
		
		/**
		 * Add the Virtuemart rewrite rules for redirecting the URLs
		 * 
		 * @param array $rewrite_rules Rewrite rules
		 * @return array Rewrite rules
		 */
		public static function add_rewrite_rules($rewrite_rules) {
			$rewrite_rules = array_merge($rewrite_rules, self::$rewrite_rules);
			return $rewrite_rules;
		}
		
		/**
		 * Try to redirect the Virtuemart URLs
		 * 
		 */
		public static function post_404_redirect() {
			$matches = array();
			$seo_suffix = get_option('fgj2wp_virtuemart_seosuffix');
			$url = preg_replace('#.*/#', '', $_SERVER['REQUEST_URI']);
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
					if ( count($posts) > 0 ) {
						$new_url = get_permalink($posts[0]->ID);
						//die($new_url);
						wp_redirect($new_url, 301);
						wp_reset_query();
						exit;
					}
				}
			} else {
				// Try to find a product category
				$taxonomy = 'product_cat';
				$term = get_term_by('slug', $url, $taxonomy);
				if ( $term ) {
					$new_url = get_term_link($term, $taxonomy);
					//die($new_url);
					wp_redirect($new_url, 301);
					wp_reset_query();
					exit;
				}
			}
		}
		
	}
}
