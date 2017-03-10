<?php

/**
 * Tags module
 *
 * @link       https://www.fredericgilles.net/fg-joomla-to-wordpress/
 * @since      2.0.0
 *
 * @package    FG_Joomla_to_WordPress_Premium
 * @subpackage FG_Joomla_to_WordPress_Premium/admin
 */

if ( !class_exists('FG_Joomla_to_WordPress_Tags', false) ) {

	/**
	 * Tags class
	 *
	 * @package    FG_Joomla_to_WordPress_Premium
	 * @subpackage FG_Joomla_to_WordPress_Premium/admin
	 * @author     Frédéric GILLES
	 */
	class FG_Joomla_to_WordPress_Tags {

		private $tags_count = 0;

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
		 * Import the tags related to a post
		 * 
		 * @param array $newpost WordPress post
		 * @param array $joomla_post Joomla post
		 * @return array WordPress post
		 */
		public function import_tags($newpost, $joomla_post) {
			if ( version_compare($this->plugin->joomla_version, '3.1', '>=') ) {
				// the tags are available for Joomla ≥ 3.1 only
				$tags = $this->get_tags($joomla_post['id']);
				$this->plugin->import_tags($tags, 'post_tag');
				if ( !empty($tags) ) {
					$newpost['tags_input'] = array_merge($newpost['tags_input'], $tags);
					$this->tags_count += count($tags);
				}
			}
			return $newpost;
		}
		
		/**
		 * Get all the Joomla tags
		 * 
		 * @param int $post_id Joomla article ID
		 * @return array Tags
		 */
		protected function get_tags($post_id) {
			$tags = array();
			$prefix = $this->plugin->plugin_options['prefix'];
			$sql = "
				SELECT t.title
				FROM ${prefix}tags t
				INNER JOIN ${prefix}contentitem_tag_map m ON m.tag_id = t.id
				WHERE m.content_item_id ='$post_id'
				AND m.type_alias = 'com_content.article'
			";
			$result = $this->plugin->joomla_query($sql);
			foreach ( $result as $row ) {
				$tags[] = $row['title'];
			}
			return $tags;
		}
		
		/**
		 * Display the number of imported tags
		 * 
		 */
		public function display_tags_count() {
			if ( version_compare($this->plugin->joomla_version, '3.1', '>=') ) {
				// the tags are available for Joomla ≥ 3.1 only
				$this->plugin->display_admin_notice(sprintf(_n('%d tag imported', '%d tags imported', $this->tags_count, get_class($this->plugin)), $this->tags_count));
			}
		}
		
		/**
		 * Get a WordPress tag that matches a Joomla URL
		 * 
		 * @since 3.19.0
		 * 
		 * @param $term WP_Term WordPress term | null
		 * @param string $url URL
		 * @return WP_Term WordPress term | null
		 */
		public function get_tag_from_joomla_url($term, $url) {
			if ( !$term ) {
				$matches = array();

				// Try to find tags that match the patterns /tag/XXX or /tag/DD-XXX
				if ( preg_match('#/tag/(\d+-)?(.*)#', $url, $matches) ) {
					$term_slug = $matches[2];
					$terms = get_terms( array(
						'slug' => $term_slug,
						'hide_empty' => false,
					) );
					if ( count($terms) > 0 ) {
						$term = $terms[0];
					}
				}
			}
			return $term;
		}
		
	}
}
