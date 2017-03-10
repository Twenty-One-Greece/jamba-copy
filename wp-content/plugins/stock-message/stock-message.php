<?php
/**
 * Plugin Name: Stock Message
 * Plugin URI: https://www.wordpress.org/plugins/stock-message
 * Author Name: Aftabul Islam
 * Version: 1.0
 * Author Email: toaihimel@gmail.com
 * License: GPLv3
 *
 * Copyright 2015  Aftabul Islam  (email : toaihimel@gmail.com)
 *
 */

class WCCSM{

	private $prefix,
		$path
		;

	public function __construct(){
		// Prohibiting Direct Access
		defined('ABSPATH') or die(require_once('four_zero_four.php'));
		$this->prefix = 'woocommerce-custom-stock-message';
		$this->path = __FILE__;
		register_activation_hook($this->path, array($this, 'activate'));
		register_deactivation_hook($this->path, array($this, 'deactivate'));

		// Loading assets
		add_action('wp_enqueue_scripts', array($this, 'assets'));
		add_action('admin_enqueue_scripts', array($this, 'admin_assets'));
		add_action('admin_menu', array($this, 'menu'));
		add_filter('woocommerce_stock_html', array($this, 'stock_html'));
	}

	public function stock_html($html){
		global $product;
		$manage_stocks = get_metadata('post', $product->id);
		$manage_stocks = $manage_stocks['_manage_stock'][0];
		$status = get_post_meta($product->id, '_stock_status', true);
		$amount = (int) get_post_meta($product->id, '_stock', true);

		if($manage_stocks != 'yes') return $html;

		if(!empty($status) && $status == 'instock' && $amount > 0){
			$html = '<p class="wcoos-instock"><span class="wcoos-amount">'.$amount.'</span> '.get_option($this->__('in-stock-message')).'</p>';
		} else {
			$html = '<p class="wcoos-comming-soon">'.get_option($this->__('out-of-stock-message')).'</p>';
		}
		return $html;
	}

	// Activation Function
	public function activate(){
		if(!class_exists('Woocommerce')) deactivate_plugins($this->path);
		add_option($this->__('out-of-stock-message'), 0, $deprecated = '', $atuoload = 'yes');
		add_option($this->__('in-stock-message'), 0, $deprecated = '', $atuoload = 'yes');
	}

	// Deactivation Function
	public function deactivate(){
		delete_option($this->__('out-of-stock-message'));
		delete_option($this->__('in-stock-message'));
	}

	// Adds Admin Menu
	public function menu(){
		add_submenu_page('woocommerce', 'WooCommerce Custom Stock Message', 'Stock Message', 'manage_options', 'woocommerce-custom-stock-message', array($this, 'admin_control_form'));
	}

	// Saving Admin Form Data
	public function save_admin_form_data(){
		$post = $_POST;
		// Out of stock message
		if(!empty($post) && (!empty($post[$this->__('out-of-stock-message')])  || !empty($post[$this->__('in-stock-message')]) ) ) {

			if(!empty($post[$this->__('out-of-stock-message')])) update_option($this->__('out-of-stock-message'), $post[$this->__('out-of-stock-message')]);
			if(!empty($post[$this->__('in-stock-message')])) update_option($this->__('in-stock-message'), $post[$this->__('in-stock-message')]);
			echo '<div class="alert alert-success" role="alert">Saved Successfully</div>';
		}
	}

	// Admin Controll Form
	public function admin_control_form(){

		if(!current_user_can('manage_options')) wp_die(__('You don\'t have permission to access this page'));

		require_once('admin-contorl-form.php');


	}

	public function assets(){
		wp_enqueue_style($this->__('style'), $this->url('css/style.css'), false);
	}

	public function admin_assets(){
		wp_enqueue_script('jquery');
		wp_enqueue_style($this->__('bootstrap_css'), $this->url('bootstrap-3.3.5/css/bootstrap.min.css'), false);
		wp_enqueue_script($this->__('bootstrap_js'), $this->url('bootstrap-3.3.5/css/bootstrap.min.js'), false);
	}

	// Prefixes all the option names
	private function __($string){return $string = $this->prefix.'__'.$string;}

	// Absolute file path inside the plugin
	private function url($string){return plugins_url('/'.$this->prefix.'/'.$string);}

}
$wccsm = new WCCSM();
