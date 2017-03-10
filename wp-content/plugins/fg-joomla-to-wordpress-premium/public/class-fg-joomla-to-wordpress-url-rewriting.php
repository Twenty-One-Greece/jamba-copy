<?php

/**
 * URL Rewriting module
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      2.0.0
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/public
 */

if ( !class_exists('FG_Joomla_to_WordPress_URL_Rewriting', false) ) {

	/**
	 * URL Rewriting class
	 *
	 * @package    FG_Joomla_to_WordPress_Premium
	 * @subpackage FG_Joomla_to_WordPress_Premium/public
	 * @author     Frédéric GILLES
	 */
	class FG_Joomla_to_WordPress_URL_Rewriting {

		private static $rewrite_rules = array(
			array( 'rule' => '^.*/(\d+)-',				'view' => 'post',		'meta_key' => '_fgj2wp_old_id'),
			array( 'rule' => '^.*/(\d+)$',				'view' => 'post',		'meta_key' => '_fgj2wp_old_id'),
			array( 'rule' => '^.*/view/(\d+)',			'view' => 'post',		'meta_key' => '_fgj2wp_old_id'),
			array( 'rule' => '^.*/category/\d+/(\d+)',	'view' => 'category',	'meta_key' => '_fgj2wp_old_category_id'),
		);

		/**
		 * Initialize the class and set its properties.
		 *
		 * @since    2.0.0
		 */
		public function __construct() {

			$premium_options = get_option('fgj2wpp_options');
			$do_redirect = isset($premium_options['url_redirect']) && !empty($premium_options['url_redirect']);
			$do_redirect = apply_filters('fgj2wpp_do_redirect', $do_redirect);
			if ( $do_redirect ) {
				// Hook on template redirect
				add_action('template_redirect', array($this, 'template_redirect'));
				// for Joomla non SEF URLs
				add_filter('query_vars', array($this, 'add_query_vars'));
				add_action('fgj2wpp_pre_404_redirect', array($this, 'pre_404_redirect'));
			}
		}
		
		/**
		 * Add the query vars
		 *
		 * @param array $vars Query vars
		 * @return array $vars Query vars
		 */
		public function add_query_vars($vars) {
			
			$vars[] = 'id'; // Joomla post ID without URL rewriting
			$vars[] = 'view';
			$vars[] = 'task';
			return $vars;
		}
		
		/**
		 * Redirection to the new URL
		 */
		public function template_redirect() {
			$matches = array();
			do_action('fgj2wpp_pre_404_redirect');
			
			if ( !is_404() ) { // A page is found, don't redirect
				return;
			}
			
			do_action('fgj2wpp_post_404_redirect');

			// Process the rewrite rules
			$rewrite_rules = apply_filters('fgj2wpp_rewrite_rules', self::$rewrite_rules);
			// Joomla configured with SEF URLs
			foreach ( $rewrite_rules as $rewrite_rule ) {
				// Note: Can't use filter_input(INPUT_SERVER, 'REQUEST_URI') because of FastCGI side-effect
				// http://php.net/manual/fr/function.filter-input.php#77307
				if ( preg_match('#'.$rewrite_rule['rule'].'#', $_SERVER['REQUEST_URI'], $matches) ) {
					$old_id = $matches[1];
					if ( isset($rewrite_rule['callback']) ) {
						$old_id = $rewrite_rule['callback']($old_id);
					}
					self::redirect($rewrite_rule['meta_key'], $old_id, $rewrite_rule['view']);
				}
			}
		}
		
		/**
		 * Try to redirect the Joomla non SEF URLs
		 */
		public function pre_404_redirect() {
			$matches = array();
			// Joomla configured without SEF URLs: view=article&id=xxx
			$view = get_query_var('view');
			if ( in_array($view, array('article', 'category', 'section')) ) {
				if ( preg_match('/(\d+)/', get_query_var('id'), $matches) ) {
					$old_id = $matches[1];
					switch ( $view ) {
						case 'article':
							self::redirect('_fgj2wp_old_id', $old_id, 'post');
							break;
						case 'category':
							self::redirect('_fgj2wp_old_category_id', $old_id, 'category');
							break;
						case 'section':
							self::redirect('_fgj2wp_old_category_id', 's' . $old_id, 'category');
							break;
					}
				}
			}
			
			// Joomla configured without SEF URLs: task=view&id=xxx
			$task = get_query_var('task');
			if ( $task == 'view' ) {
				if ( preg_match('/(\d+)/', get_query_var('id'), $matches) ) {
					$old_id = $matches[1];
					self::redirect('_fgj2wp_old_id', $old_id);
				}
			}
		}
		
		/**
		 * Query and redirect to the new URL
		 *
		 * @param string $meta_key Meta Key to search in the postmeta table
		 * @param int $old_id Joomla ID
		 * @param string $view post|category
		 */
		public static function redirect($meta_key, $old_id, $view='post') {
			if ( !empty($old_id) && !empty($meta_key) ) {
				switch ( $view ) {
					case 'post':
						// Get the post by its old ID
						query_posts( array(
							'post_type' => 'any',
							'meta_key' => $meta_key,
							'meta_value' => $old_id,
							'ignore_sticky_posts' => 1,
						) );
						if ( have_posts() ) {
							the_post();
							$url = get_permalink();
							//die($url);
							wp_redirect($url, 301);
							wp_reset_query();
							exit;
						}
						break;
					
					case 'category':
						$args = array(
							'hide_empty' => false, // also retrieve terms which are not used yet
							'meta_query' => array(
								array(
								   'key'       => $meta_key,
								   'value'     => $old_id,
								   'compare'   => '='
								)
							)
						);
						$terms = get_terms('category', $args);
						if ( count($terms) > 0 ) {
							$url = get_term_link($terms[0]->term_id);
							//die($url);
							wp_redirect($url, 301);
							wp_reset_query();
							exit;
						}
						break;
				}
				// else continue the normal workflow
			}
		}
	}
}
