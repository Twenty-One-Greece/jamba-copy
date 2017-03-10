<?php
/* 
Class Name: Shortcode Parts Dropdown

Author: Bigboss555
Author URI: http://sakosys.com
Copyright sakosys.com. All rights reserved
*/

function ymm_filter_info_shortcode(){
	if(isset($_REQUEST['ymm_data'])){
		$term_id = $_REQUEST['ymm_data'];
		$termchildren = get_term_children( $term_id, 'bb_YMM_Filter' );
		foreach ( $termchildren as $child ) {
			$term = get_term_by( 'id', $child, 'bb_YMM_Filter' );
			if(isset($term->parent) && $term->parent != '' && $term->parent == $term_id){
				$output .= '<option value="'.$term->term_id.'">'.$term->name.'</option>';
			}
		}
		echo $output;
	}
	exit;
}
add_action( 'wp_ajax_nopriv_ymm_filter_info_shortcode', 'ymm_filter_info_shortcode' );
add_action( 'wp_ajax_ymm_filter_info_shortcode', 'ymm_filter_info_shortcode' );
add_action( 'init', 'register_woocommerce_bbYMM_shortcodes');

function bbYMMFilterDataSc($term_id,$selected){
	$termchildren = get_term_children( $term_id, 'bb_YMM_Filter' );
	foreach ( $termchildren as $child ) {
		$term = get_term_by( 'id', $child, 'bb_YMM_Filter' );
		if(isset($term->parent) && $term->parent != '' && $term->parent == $term_id){
			if($term->term_id == $selected){
				$output .= '<option selected="selected" value="'.$term->term_id.'">'.$term->name.'</option>';
			}
			else{
				$output .= '<option value="'.$term->term_id.'">'.$term->name.'</option>';
			}
		}
	}
	return $output;
}

function register_woocommerce_bbYMM_shortcodes(){
   add_shortcode('bbYMM_filter', 'bbYMM_shortcode_html_fun');
}
function bbYMM_shortcode_html_fun( $atts ){ 
	$_bb_view_mode = 'V';
	$_bb_top_title = '';
	$_bb_year_label = 'Select Year';
	$_bb_make_label = 'Select Make';
	$_bb_model_label = 'Select Model';
	$_bb_filter_button_label = 'Filter Parts';

	$output = '<div class="ymm_shortcode woocommerce">';
	if(isset($atts['top_title']) && $atts['top_title'] != ''){
		$_bb_top_title = $atts['top_title'];
	}
	if(isset($atts['view_mode']) && $atts['view_mode'] != ''){
		$_bb_view_mode = $atts['view_mode'];
	}
	if(isset($atts['year_label']) && $atts['year_label'] != ''){
		$_bb_year_label = $atts['year_label'];
	}
	if(isset($atts['make_label']) && $atts['make_label'] != ''){
		$_bb_make_label = $atts['make_label'];
	}
	if(isset($atts['model_label']) && $atts['model_label'] != ''){
		$_bb_model_label = $atts['model_label'];
	}
	if(isset($atts['button_label']) && $atts['button_label'] != ''){
		$_bb_filter_button_label = $atts['button_label'];
	}
	
	$_bb_get_year_sc = '';
	$_bb_get_make_sc = '';
	$_bb_get_model_sc = '';
	if(isset($_GET['bb_year']) && $_GET['bb_year'] != '-1'){
		$_bb_get_year_sc = $_GET['bb_year'];
	}
	if(isset($_GET['bb_make']) && $_GET['bb_make'] != '-1'){
		$_bb_get_make_sc = bbYMMFilterDataSc($_GET['bb_year'],$_GET['bb_make']);
	}
	if(isset($_GET['bb_model']) && $_GET['bb_model'] != '-1'){
		$_bb_get_model_sc = bbYMMFilterDataSc($_GET['bb_make'],$_GET['bb_model']);
	}
	
	if($_bb_view_mode == 'V'){
		$output .= '<form class="bb_filter_form" action="'.get_site_url().'" method="get"><span id="bbymmLoader_sc"><img src=' . plugins_url() . '/woo_vehicle_parts_filter_year_make_model/img/loader.gif /></span>';
		$year_list = get_terms( 'bb_YMM_Filter', array(
				'orderby'    	=> 'name',
				'parent'        => '0',
				'order'         => 'ASC',
				'hide_empty'	=> $hide_empty
			));

			if ( !empty( $year_list ) && !is_wp_error( $year_list ) ){
				$output .= "<div style='padding:5px'><h2>".$_bb_top_title."</h2></div>";
				$output .= "<div style='padding:5px'><select class='ymm_year_box_sc sc_select_width' style='color:#000 !important;' onchange=bbYMM_create_request_shortcode(this,2,'".admin_url( 'admin-ajax.php' )."'); name='bb_year'><option value='-1'>".$_bb_year_label."</option>";
				foreach ( $year_list as $year_item ) {
					if($_bb_get_year_sc != '' && $_bb_get_year_sc == $year_item->term_id){
						$output .= '<option selected value="'.$year_item->term_id.'">'.$year_item->name.'</option>';	
					}
					else{
						$output .= '<option value="'.$year_item->term_id.'">'.$year_item->name.'</option>';
					}
				}
				$output .= '</select></div>';
				$output .= "<div style='padding:5px'><select class='ymm_make_box_sc sc_select_width' style='color:#000 !important;' name='bb_make' onchange=bbYMM_create_request_shortcode(this,3,'".admin_url( 'admin-ajax.php' )."');><option value='-1'>".$_bb_make_label."</option>".$_bb_get_make_sc."</select></div>";
				$output .= "<div style='padding:5px'><select class='ymm_model_box_sc sc_select_width' name='bb_model'><option value='-1'>".$_bb_model_label."</option>".$_bb_get_model_sc."</select></div>";
				$output .= "<div align='left' style='padding:6px'><input type='submit' class='button' value='".$_bb_filter_button_label."'></div><input type='hidden' name='post_type' value='product'></form>";
			}
	}
	else{
		$output .= '<form class="bb_filter_form" action="'.get_site_url().'" method="get"><span id="bbymmLoader_sc"><img src=' . plugins_url() . '/woo_vehicle_parts_filter_year_make_model/img/loader.gif /></span>';
		$year_list = get_terms( 'bb_YMM_Filter', array(
				'orderby'    	=> 'name',
				'parent'        => '0',
				'order'         => 'ASC',
				'hide_empty'	=> $hide_empty
			));

			if ( !empty( $year_list ) && !is_wp_error( $year_list ) ){
				$output .= "<div style='padding:5px'><h2>".$_bb_top_title."</h2></div>";
				$output .= "<div style='padding:5px;float:left;'><select class='ymm_year_box_sc shortcodeSelect' onchange=bbYMM_create_request_shortcode(this,2,'".admin_url( 'admin-ajax.php' )."'); name='bb_year'><option value='-1'>".$_bb_year_label."</option>";
				foreach ( $year_list as $year_item ) {
					if($_bb_get_year_sc != '' && $_bb_get_year_sc == $year_item->term_id){
						$output .= '<option selected value="'.$year_item->term_id.'">'.$year_item->name.'</option>';	
					}
					else{
						$output .= '<option value="'.$year_item->term_id.'">'.$year_item->name.'</option>';
					}
				}
				$output .= '</select></div>';
				$output .= "<div style='padding:5px;float:left;'><select class='ymm_make_box_sc shortcodeSelect' name='bb_make' onchange=bbYMM_create_request_shortcode(this,3,'".admin_url( 'admin-ajax.php' )."');><option value='-1'>".$_bb_make_label."</option>".$_bb_get_make_sc."</select></div>";
				$output .= "<div style='padding:5px;float:left;'><select class='ymm_model_box_sc shortcodeSelect' name='bb_model'><option value='-1'>".$_bb_model_label."</option>".$_bb_get_model_sc."</select></div>";
				$output .= "<div align='left' style='padding:6px'><input type='submit' class='button' value='".$_bb_filter_button_label."'></div><input type='hidden' name='post_type' value='product'><div style='clear:both;'></div></form>";
			}
	}
	$output .= '<script type="text/javascript">
	function mmy_default_behave_sc(token){
		if(token == 2){
			jQuery(".ymm_make_box_sc").html("<option value=-1>'.$_bb_make_label.'</option>");
			jQuery(".ymm_model_box_sc").html("<option value=-1>'.$_bb_model_label.'</option>");
		}
		if(token == 3){
			jQuery(".ymm_model_box_sc").html("<option value=-1>'.$_bb_model_label.'</option>");
		}
	}
	</script>';
	$output .= '</div>';
	return $output;
	
}
?>
