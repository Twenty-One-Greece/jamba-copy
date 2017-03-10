<?php
/*
Plugin Name: Indeed Smart PopUp
Plugin URI: http://www.wpindeed.com/
Description: Plugin that generate Smart Popups
Version: 4.6
Author: indeed
Author URI: http://www.wpindeed.com
*/
//PATHS
define('ISP_DIR_PATH', plugin_dir_path(__FILE__));
define('ISP_DIR_URL', plugin_dir_url(__FILE__));

include_once ISP_DIR_PATH.'functions.php';

////// ACTIVATE PLUGIN - CREATE TABLES
register_activation_hook ( __FILE__, 'create_ips_tbl' );
function create_ips_tbl() {
	include_once ISP_DIR_PATH.'admin/functions.php';
	ips_create_meta_tables();
}
/////// DATABASE UPDATE
add_action( 'init', 'update_ips_table' );
function update_ips_table(){
	include_once ISP_DIR_PATH.'admin/functions.php';
	////////It add two tables : WP_POPUP_COUNTRY_D and wp_popup_form_results
	ips_update_the_db();
}

////// SET MENU
add_action ( 'admin_menu', 'ips_menu', 81 );
function ips_menu() {
	add_menu_page( 'Indeed PopUp', 'Indeed PopUp', 'manage_options',
                     'ips_admin', 'ips_admin', ISP_DIR_URL.'admin/assets/img/ed-gray.png' );
}

function ips_admin(){
	include_once ISP_DIR_PATH . 'admin/functions.php';
	include_once ISP_DIR_PATH . 'admin/includes/main_page.php';
}

//////ADMIN JS AND CSS
add_action("admin_enqueue_scripts", 'ips_header');
function ips_header(){
	if(isset($_REQUEST['page']) && $_REQUEST['page']=='ips_admin'){
		//assets front-end
		wp_enqueue_style ( 'isp_owl_carousel_css', ISP_DIR_URL.'assets/css/owl-carousel/owl.carousel.css', array(), null );
		wp_enqueue_style ( 'isp_owl_theme_css', ISP_DIR_URL.'assets/css/owl-carousel/owl.theme.css', array(), null );
		wp_enqueue_style ( 'isp_owl_transitions_css', ISP_DIR_URL.'assets/css/owl-carousel/owl.transitions.css', array(), null );
		
		wp_enqueue_script ( 'isp_jquery_ui_js', ISP_DIR_URL.'assets/js/jquery-ui-1.10.4.custom.min.js', array(), null );
		wp_enqueue_script ( 'isp_owl_carousel_js', ISP_DIR_URL.'assets/js/owl-carousel/owl.carousel.js', array(), null );
		wp_enqueue_script ( 'isp_owl_carousel_min_js', ISP_DIR_URL.'assets/js/owl-carousel/owl.carousel.min.js', array(), null );
		wp_enqueue_script ( 'isp_front_end_js', ISP_DIR_URL.'assets/js/front-end_functions.js', array(), null );
		wp_enqueue_script ( 'isp_googlemaps', 'http://maps.google.com/maps/api/js?sensor=false', array(), null );
		
		//assets admin
		wp_enqueue_style ( 'isp_jquery_ui_css', ISP_DIR_URL.'admin/assets/css/jquery-ui.css', array(), null );
		wp_enqueue_style ( 'isp_bootstrap_min_css', ISP_DIR_URL.'admin/assets/css/bootstrap.min.css', array(), null );
		wp_enqueue_style ( 'isp_bootstrap-responsive_min_css', ISP_DIR_URL.'admin/assets/css/bootstrap-responsive.min.css', array(), null );
		wp_enqueue_style ( 'isp_style', ISP_DIR_URL.'admin/assets/css/style.css', array(), null );
		wp_enqueue_style ( 'isp_colorpicker', ISP_DIR_URL.'admin/assets/css/colorpicker.css', array(), null);
		wp_enqueue_style ( 'isp_isp-style', ISP_DIR_URL.'admin/assets/css/ips_style.css', array(), null);
		wp_enqueue_style ( 'isp_icheck_minimal', ISP_DIR_URL.'admin/assets/css/icheck_minimal_all.css', array(), null);
		wp_enqueue_style ( 'isp_datepicker', ISP_DIR_URL.'admin/assets/css/datepicker.css', array(), null);
		wp_enqueue_style ( 'isp_jquery_timepicker', ISP_DIR_URL.'admin/assets/css/jquery.timepicker.css', array(), null);

		if( function_exists( 'wp_enqueue_media' ) ){
			wp_enqueue_media();
			wp_enqueue_script ( 'isp_open_media_3_5', ISP_DIR_URL.'admin/js/open_media_3_5.js', array(), null );
		}else{
			wp_enqueue_style( 'thickbox' );
			wp_enqueue_script( 'thickbox' );
			wp_enqueue_script( 'media-upload' );
			wp_enqueue_script ( 'isp_open_media_3_4', ISP_DIR_URL.'admin/js/open_media_3_4.js', array(), null );
		}

		wp_enqueue_script ( 'isp_bootstrap_min_js', ISP_DIR_URL.'admin/js/bootstrap.min.js', array(), null );
		wp_enqueue_script ( 'isp_jquery_icheck_min_js', ISP_DIR_URL.'admin/js/jquery.icheck.min.js', array(), null );
		wp_enqueue_script ( 'isp_bootstrap_colorpicker_js', ISP_DIR_URL.'admin/js/bootstrap-colorpicker.js', array(), null );
		wp_enqueue_script ( 'isp_ckeditor_js', ISP_DIR_URL.'admin/js/ckeditor/ckeditor.js', array(), null );
		wp_enqueue_script ( 'isp_jquery-ui-timepicker-addon', ISP_DIR_URL.'admin/js/jquery-ui-timepicker-addon.js', array(), null);
		wp_enqueue_script ( 'isp_jquery_adaptor', ISP_DIR_URL.'admin/js/jquery.adaptor.js', array(), null );
		wp_enqueue_script ( 'isp_functions_js', ISP_DIR_URL.'admin/js/functions.js', array(), null );

		if ((isset($_REQUEST['tab']) && $_REQUEST['tab'] == 'ips_stats') || !isset($_REQUEST['tab'])) {
			wp_enqueue_script ( 'isp_jquery_flot', ISP_DIR_URL.'admin/js/stats/flot/jquery.flot.js', array(), null, false );
			wp_enqueue_script ( 'isp_jquery_flot_pie', ISP_DIR_URL.'admin/js/stats/flot/jquery.flot.pie.js', array(), null, false);
			wp_enqueue_script ( 'isp_jquery_flot_time', ISP_DIR_URL.'admin/js/stats/flot/jquery.flot.time.js', array(), null, false);
			wp_enqueue_script ( 'isp_jquery_vmap', ISP_DIR_URL.'admin/js/stats/vmap/jquery.vmap.min.js', array(), null, false );
			wp_enqueue_script ( 'isp_jquery_vmap_world', ISP_DIR_URL.'admin/js/stats/vmap/jquery.vmap.world.js', array(), null, false);
		}
	}
}

///////////FRONT END JS AND CSS
add_action('wp_enqueue_scripts', 'ips_front_end_head');//'wp_head'
function ips_front_end_head(){
	wp_enqueue_style ( 'isp_owl_carousel_css', ISP_DIR_URL.'assets/css/owl-carousel/owl.carousel.css', array(), null );
	wp_enqueue_style ( 'isp_owl_theme_css', ISP_DIR_URL.'assets/css/owl-carousel/owl.theme.css', array(), null );
	wp_enqueue_style ( 'isp_owl_transitions_css', ISP_DIR_URL.'assets/css/owl-carousel/owl.transitions.css', array(), null );

	wp_enqueue_script ( 'jquery' );
	wp_enqueue_script ( 'isp_jquery_ui_js', ISP_DIR_URL.'assets/js/jquery-ui-1.10.4.custom.min.js', array(), null );
	wp_enqueue_script ( 'isp_owl_carousel_js', ISP_DIR_URL.'assets/js/owl-carousel/owl.carousel.js', array(), null );
	wp_enqueue_script ( 'isp_owl_carousel_min_js', ISP_DIR_URL.'assets/js/owl-carousel/owl.carousel.min.js', array(), null );
	wp_enqueue_script ( 'isp_front_end_js', ISP_DIR_URL.'assets/js/front-end_functions.js', array(), null );
	wp_enqueue_script ( 'isp_googlemaps', 'http://maps.google.com/maps/api/js?sensor=false', array(), null );
}

////// SET COOKIES
add_action('init', 'set_ips_cookie');
function set_ips_cookie(){
	if(is_admin()) return;
	if(!array_key_exists('ips_visitor',$_COOKIE) || !$_COOKIE['ips_visitor']){
		global $ips_user_cookie;
		$ips_user_cookie = substr(md5($_SERVER['SERVER_ADDR'].$_SERVER['REQUEST_TIME']),10);
		setcookie('ips_visitor', $ips_user_cookie, time() + ( 60*60*24*365 ),"/" );
	}
	global $visit_pages_arr;
    if(isset($_COOKIE['visit_pages']) && count($_COOKIE['visit_pages']) > 0){
        foreach($_COOKIE['visit_pages'] as $k=>$v){
            $visit_pages = $v+1;
            unset($_COOKIE['visit_pages'][$k]);
            setcookie("visit_pages[$k]", $visit_pages, time()+(60*60*24*365),"/");
            $visit_pages_arr[$k] = $visit_pages;
        }
    }
}

/////////SHORTCODE FUNCTION
add_shortcode ( 'indeed_popups', 'ips_shortcode' );
function ips_shortcode($id){
	include_once ISP_DIR_PATH.'includes/front_end_functions.php';
	wp_enqueue_script( 'its_functions', plugins_url( 'indeed-smart-popup/assets/js/ajax_form_submit.js' ), array(), null);
	wp_localize_script( 'its_functions', 'ajaxobject', array( 'ajaxurl' => admin_url( 'admin-ajax.php' )));
	//THE SHORTCODE
    extract ( shortcode_atts ( array ('id' => '' ), $id, 'indeed_popups' ) );

    global $ips_user_cookie;
	global $visit_pages_arr;
	global $ips_active_items;
    global $ips_arr_str;
	$current_country = '';
	$device_type = '';
	
	if(isset($id) && $id!=''){
		if(ips_check_item_disabled($id)) return;
		if(!ips_check_popup_exists($id)) return;
  		$ips_id = $id;
	    $show = FALSE;  
	    //GETTING THE METAS
	    @$meta_arr = ips_get_metas( $ips_id );
	    if($meta_arr && count($meta_arr)>0) $show = TRUE;
		
		//SMART SETTINGS
		$check1 = CheckRegUser($meta_arr);
		$check2 = CheckComUser($meta_arr);
		$check3 = CheckFirstTimeUser($meta_arr, $ips_id);
		$check4 = CheckRefferer($meta_arr);
	    $check5 = CheckLocation($meta_arr, $current_country, $ips_id);
		$sessionData = ManageSession($meta_arr, $ips_id, $visit_pages_arr, $current_country);
		$check6 = CheckSessionCond($meta_arr, $sessionData);
		
		//SCHEDULING TIME     
		$check7 = CheckSchedulingTime($meta_arr, $ips_id);
		
		//MOBILE FEATURES
		$check8 = CheckMobileCond($meta_arr, $device_type);
		
		//CHECK DUPLICATES
		$check9 = CheckDuplicates($ips_active_items, $ips_id);

		if($check9 == TRUE) $ips_active_items[] = $ips_id;
	
		if($check1 == FALSE || $check2 == FALSE || $check3 == FALSE ||
		   $check4 == FALSE || $check5 == FALSE || $check6 == FALSE || 
		   $check7 == FALSE || $check8 == FALSE || $check9 == FALSE){
				$show = FALSE;
		}

	    if($show == TRUE){
			//THE OUTPUT
			$string = '';
			$preview = 0;
			include ISP_DIR_PATH.'includes/ips_view.php'; 
			$ips_arr_str[] = $string;
			//SaveStatisticsData($ips_id, $ips_user_cookie, $current_country);
	    }
	}
}

/////PRINT THE POPUPS
add_action('wp_footer', 'print_IPS');
function print_IPS(){
    global $ips_arr_str;
    if(isset($ips_arr_str) && count($ips_arr_str)>0){
        foreach($ips_arr_str as $the_ips_str){
			echo $the_ips_str;
        }
    }
}

//// SHOW IN - FRONT END
add_action('wp_head', 'ips_head_check', 20);
function ips_head_check(){
	include_once ISP_DIR_PATH.'includes/front_end_functions.php';
	include ISP_DIR_PATH.'includes/show_in.php';
}

//////// SUBSCRIBE SECTION
function isp_submit_form() {
	include ISP_DIR_PATH . 'includes/front_end_functions.php';	
	include ISP_DIR_PATH . 'includes/subscribe.php';
}
add_action('wp_ajax_isp_submit_form', 'isp_submit_form');
add_action('wp_ajax_nopriv_isp_submit_form', 'isp_submit_form');

////////TEMPLATE PAGE FOR SHORTCODE
add_filter(	'page_attributes_dropdown_pages_args', 'register_project_templates' );
add_filter( 'wp_insert_post_data', 'register_project_templates' );
function register_project_templates( $atts ) {
	$cache_key = 'page_templates-' . md5( get_theme_root() . '/' . get_stylesheet() );
	$templates = wp_get_theme()->get_page_templates();
	if ( empty( $templates ) ) {
		$templates = array();
	}
	wp_cache_delete( $cache_key , 'themes');
	$templates = array_merge( $templates, array( ISP_DIR_PATH.'includes/isp_template.php' => 'ISP PAGE TEMPLATE') );
	wp_cache_add( $cache_key, $templates, 'themes', 1800 );
	return $atts;
}

add_filter( 'template_include', 'view_project_template') ;
function view_project_template( $template ) {
	global $post;
	$isp_template_name = ISP_DIR_PATH.'includes/isp_template.php';
	if(isset($post->ID) && $post->ID!=''){
		if(get_post_type($post->ID)=='isp_s_post_type') return $isp_template_name;
		$current_template = get_post_meta($post->ID, '_wp_page_template', true	);
		if($current_template==$isp_template_name){
			return $isp_template_name;
		}
	}
	return $template;
}

add_action('wp_ajax_ips_update_aweber', 'ips_update_aweber');
add_action('wp_ajax_nopriv_ips_update_aweber', 'ips_update_aweber');
function ips_update_aweber(){
	include_once IPS_DIR_PATH .'includes/email_services/aweber/aweber_api.php';
	list($consumer_key, $consumer_secret, $access_key, $access_secret) = AWeberAPI::getDataFromAweberID( $_REQUEST['auth_code'] );
	if(get_option('ips_aweber_consumer_key')==false){
		add_option('ips_aweber_consumer_key', $consumer_key);
		add_option('ips_aweber_consumer_secret', $consumer_secret);
		add_option('ips_aweber_acces_key', $access_key);
		add_option('ips_aweber_acces_secret', $access_secret);
	}else{
		update_option( 'ips_aweber_consumer_key', $consumer_key );
		update_option( 'ips_aweber_consumer_secret', $consumer_secret );
		update_option( 'ips_aweber_acces_key', $access_key );
		update_option( 'ips_aweber_acces_secret', $access_secret );
	}
	echo 1;
	die();	
}

add_action('wp_ajax_ips_get_cc_list', 'ips_get_cc_list');
add_action('wp_ajax_nopriv_ips_get_cc_list', 'ips_get_cc_list');
function ips_get_cc_list(){
	echo json_encode(ips_return_cc_list($_REQUEST['ips_cc_user'],$_REQUEST['ips_cc_pass']));
	die();	
}
function ips_return_cc_list($ips_cc_user, $ips_cc_pass){
	include_once ISP_DIR_PATH .'includes/email_services/constantcontact/class.cc.php';
	$list = array();
	$cc = new cc($ips_cc_user, $ips_cc_pass);
	$lists = $cc->get_lists('lists');
	if ($lists){
		foreach ((array) $lists as $v){
			$list[$v['id']] = array('name' => $v['Name']);
		}
	}
	return $list; 
}

//////POPUP STATUS ACTIVE/INACTIVE
add_action('wp_ajax_ips_update_popup_status', 'ips_update_popup_status');
add_action('wp_ajax_nopriv_ips_update_popup_status', 'ips_update_popup_status');
function ips_update_popup_status(){
	if(isset($_REQUEST['status']) && isset($_REQUEST['the_id'])){
		@$ips_items_status = get_option('ips_items_status');
		if($ips_items_status!==FALSE && count($ips_items_status)>0){
			$ips_items_status[$_REQUEST['the_id']] = $_REQUEST['status'];
			update_option('ips_items_status', $ips_items_status);			
		}else{
			$ips_items_status[$_REQUEST['the_id']] = $_REQUEST['status'];
			add_option('ips_items_status', $ips_items_status);			
		}
		echo 1;
	}else echo 0;
	die();
}

//SaveStatisticsData VISITS 
add_action('wp_ajax_SaveStatisticsData', 'SaveStatisticsData');
add_action('wp_ajax_nopriv_SaveStatisticsData', 'SaveStatisticsData');
function SaveStatisticsData(){
	global $wpdb;
	if(!isset($_REQUEST['ips_user_cookie']) && $_REQUEST['ips_user_cookie']!='') $ips_user_cookie = $_COOKIE['ips_visitor'];
	else $ips_user_cookie = $_REQUEST['ips_user_cookie'];
	$current_time = date("Y-m-d H:i:s");
	$the_ref = '';
	$user_agent = '';
	$country = '';

	if(isset($_SERVER['HTTP_REFERER'])){
		$the_ref = $_SERVER['HTTP_REFERER'];
	}

	if(isset($_SERVER['HTTP_USER_AGENT'])){
		$user_agent = $_SERVER['HTTP_USER_AGENT'];
	}
	if(isset($_COOKIE['country'][$ips_id])){
		$country = $_COOKIE['country'][$ips_id];
	}

	$wpdb->query("INSERT INTO {$wpdb->prefix}popup_visits VALUES(null,'{$_REQUEST['ips_id']}', '$ips_user_cookie', '$current_time', '$the_ref', '$user_agent', '$country');");
	die();
}

/////ISP POST TYPE
add_action( 'init', 'register_isp_shortcode_post_type' );
function register_isp_shortcode_post_type() {
	$labels = array(
			'name'               => 'ISP Post',
			'singular_name'      => 'ISP Post',
			'menu_name'          => 'ISP Post',
			'name_admin_bar'     => 'ISP Post',
			'add_new'            => 'Add New ISP Post',
			'add_new_item'       => 'Add New ISP Post',
			'new_item'           => 'New ISP Post',
			'edit_item'          => 'Edit',
			'view_item'          => 'View',
			'all_items'          => 'All ISP Posts',
			'search_items'       => 'Search ISP Post',
			'parent_item_colon'  => 'Parent ISP Post:',
			'not_found'          => 'No ISP Post found.',
			'not_found_in_trash' => 'No ISP Post found in Trash.'
	);

	$args = array(
			'labels'             => $labels,
			'public'             => true,
			'publicly_queryable' => true,
			'show_ui'            => true,
			'show_in_menu'       => true,
			'query_var'          => true,
			'rewrite'            => array( 'slug' => 'isp_s_post_type' ),
			'capability_type'    => 'post',
			'has_archive'        => true,
			'hierarchical'       => false,
			'menu_position'      => null,
			'supports'           => array( 'title', 'editor')
	);

	register_post_type( 'isp_s_post_type', $args );
}