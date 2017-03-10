<?php
/*
Plugin Name: WooCommerce Vehicle Parts Filter Year/Make/Model
Plugin URI:
Description: Easily Find your Vehicle Parts By Year Make and Model
Version: 1.1
Author: Bigboss555
Author URI:http://sakosys.com
*/

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

if ( ! class_exists( 'bbYMMFilter' ) ) {
	require_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo_vehicle_parts_filter_year_make_model" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "ymm_widget.php";
	require_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo_vehicle_parts_filter_year_make_model" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "ymm_shortcode.php";
	require_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo_vehicle_parts_filter_year_make_model" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "ymm_product_tab.php";
	require_once WP_PLUGIN_DIR . DIRECTORY_SEPARATOR . "woo_vehicle_parts_filter_year_make_model" . DIRECTORY_SEPARATOR . "include" . DIRECTORY_SEPARATOR . "ymm_csv_import.php";
}

class bigbossYMMCore {
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'ob_install' ) );
		register_deactivation_hook( __FILE__, array( $this, 'ob_uninstall' ) );

		/**
		 * add action of plugin
		 */
		add_action( 'init', array( $this, 'register_bb_ymmm_filter_taxonomy'));
		
		add_action( 'init', array( $this, 'init_bb_ymmm_filter_taxonomy_meta'));

		add_action( 'admin_init', array( $this, 'obScriptInit' ) );
		
		add_action( 'init', array( $this, 'obScriptInitFrontend' ) );
		
		add_filter( 'woocommerce_page_title', array( $this, 'woo_shop_page_title' ) );
		
		add_filter( 'wp_title',  array( $this, 'wc_custom_shop_archive_title' ) );
		
		add_action( 'widgets_init', array( $this, 'bbYMMFilter_register_widgets' ) );
		
		add_action( 'pre_get_posts',  array( $this, 'rc_modify_query_get_design_projects' ) );
				
	}
	
	function woo_shop_page_title( $page_title ) {
		if( 'Shop' == $page_title && is_shop() && isset($_GET['bb_year']) && isset($_GET['bb_make']) && isset($_GET['bb_model'])) {
			return $this->returnPerfectTitleForFilter($_GET['bb_year'],$_GET['bb_make'],$_GET['bb_model']);
		}
		else{
			return $page_title;
		}
	}
	
	function wc_custom_shop_archive_title( $title ) 
	{
		if( is_shop() && isset($_GET['bb_year']) && isset($_GET['bb_make']) && isset($_GET['bb_model'])) {
			return str_replace( __( 'Products', 'woocommerce' ), $this->returnPerfectTitleForFilter($_GET['bb_year'],$_GET['bb_make'],$_GET['bb_model']), $title );
		}
	
		return $title;
	}
	
	function returnPerfectTitleForFilter($year,$make,$model){
		$sHtml = '';
		if($year != '' && $year != '-1'){
			$objTerm = get_term_by('id', $_GET['bb_year'], 'bb_YMM_Filter');
			if(isset($objTerm->name) && $objTerm->name != ''){
				$sHtml .= $objTerm->name;
			}
		}
		if($make != '' && $make != '-1'){
			$objTerm = get_term_by('id', $_GET['bb_make'], 'bb_YMM_Filter');
			if(isset($objTerm->name) && $objTerm->name != ''){
				$sHtml .= '/'.$objTerm->name;
			}
		}
		if($model != '' && $model != '-1'){
			$objTerm = get_term_by('id', $_GET['bb_model'], 'bb_YMM_Filter');
			if(isset($objTerm->name) && $objTerm->name != ''){
				$sHtml .= '/'.$objTerm->name;
			}
		}
		return $sHtml;
	}
	function rc_modify_query_get_design_projects( $query ) {
		if (!$query->is_main_query() || !is_shop()) return;
		if (!is_admin() && isset($_GET['bb_year']) && isset($_GET['bb_make']) && isset($_GET['bb_model'])) {
			if((int)$_GET['bb_year'] > 0 && (int)$_GET['bb_make'] > 0 && (int)$_GET['bb_model'] > 0){
				$query->set('tax_query', array(
					'relation' => 'AND',
					array(
						  'taxonomy' => 'bb_YMM_Filter',
						  'field' => 'id',
						  'terms' => trim($_GET['bb_year']),
						  ),
					array(
						  'taxonomy' => 'bb_YMM_Filter',
						  'field' => 'id',
						  'terms' => trim($_GET['bb_make']),
						  ),
					array(
						  'taxonomy' => 'bb_YMM_Filter',
						  'field' => 'id',
						  'terms' => trim($_GET['bb_model']),
						  )
					)
				);
			}
			else if((int)$_GET['bb_year'] > 0 && (int)$_GET['bb_make'] > 0){
				$query->set('tax_query', array(
					'relation' => 'AND',
					array(
						  'taxonomy' => 'bb_YMM_Filter',
						  'field' => 'id',
						  'terms' => trim($_GET['bb_year']),
						  ),
					array(
						  'taxonomy' => 'bb_YMM_Filter',
						  'field' => 'id',
						  'terms' => trim($_GET['bb_make']),
						  )
					)
				);
			}
			else if((int)$_GET['bb_year'] > 0){
				$query->set('tax_query', array(
					'relation' => 'AND',
					array(
						  'taxonomy' => 'bb_YMM_Filter',
						  'field' => 'id',
						  'terms' => trim($_GET['bb_year']),
						  )
					)
				);
			}
		}
	}
	
	
	function ob_install() {
		global $wp_version;
		If ( version_compare( $wp_version, "2.9", "<" ) ) {
			deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
			wp_die( "This plugin requires WordPress version 2.9 or higher." );
		}
		/**
		 * Check if WooCommerce is active or not
		 **/
		if ( !in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
		    deactivate_plugins( basename( __FILE__ ) ); // Deactivate our plugin
			wp_die( "This plugin required WooCommerce plugin installed and activated. Please <a href='http://www.woothemes.com/woocommerce/' target='_blank'>download and install WooCommerce plugin</a>." );
		}

	}
	
	/**
	 * Register widget
	 */
	function bbYMMFilter_register_widgets() {
		register_widget( 'bbYMM_list_widget' );
	}
	
	function ob_uninstall() {
		//do something when uninstall
	}
	/**
	 * Function set up include javascript, css.
	 */
	function obScriptInit() {
		//wp_enqueue_script( 'bbYMM-script-admin', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/js/jscolor.js', array(), false, true );
		wp_enqueue_style( 'bbYMM-style-admin', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/css/widget.css' );
	}

	function obScriptInitFrontend() {
		wp_enqueue_script( 'bbYMM-script-frontend', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/js/bb_ymm.js', array(), false, true );
		wp_enqueue_style( 'bbYMM-style-frontend', plugin_dir_url( '' ) . basename( dirname( __FILE__ ) ) . '/css/style.css' );
	}
	
	/**
	 * This function register custom tyrefilter taxonomy
	 */
	function register_bb_ymmm_filter_taxonomy() {

		$labels = array(
			'name' => _x( 'Year Make Model', 'General Name' ),
			'singular_name' => _x( 'YMMfilter', 'taxonomy singular name' ),
			'search_items' =>  __( 'Search Vehicle Parts' ),
			'all_items' => __( 'All Parts' ),
			'parent_item' => __( 'Parent Tyre' ),
			'parent_item_colon' => __( 'Parent Tyre:' ),
			'edit_item' => __( 'Edit Tyre' ),
			'update_item' => __( 'Update Tyre' ),
			'add_new_item' => __( 'Add New Parts Info' ),
			'new_item_name' => __( 'New Parts Name' ),
			'menu_name' => __( 'Year/Make/Model' ),
		);    

	    register_taxonomy("bb_YMM_Filter",
	     array("product"),
	     array(
			 'hierarchical' 		=> true,
		     'labels' 				=> $labels,
		   	 'show_ui'		    	=> true,
    		 'query_var' 			=> true,
		     'rewrite' 				=> array( 'slug' => 'type', 'with_front' => true ),
		     'show_admin_column' 	=> true
	     ));
	}
	function init_bb_ymmm_filter_taxonomy_meta() {
		$prefix = 'bbymm_';

		$config = array(
			'id' => 'bbymm_box',          // meta box id, unique per meta box
			'title' => 'Year/Make/Model settings',          // meta box title
			'pages' => array('bbymm_filter'),        // taxonomy name, accept categories, post_tag and custom taxonomies
			'context' => 'normal',            // where the meta box appear: normal (default), advanced, side; optional
			'fields' => array(),            // list of meta fields (can be added by field arrays)
			'local_images' => false,          // Use local or hosted images (meta box images for add/remove)
			'use_with_theme' => false        //change path if used with theme set to true, false for a plugin or anything else for a custom path(default false).
		);
	
	}
}
$bbYMMFilter = new bigbossYMMCore();
?>
