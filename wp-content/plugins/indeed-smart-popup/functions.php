<?php
function defaultValues(){
  $arr = array(
                    "position" => 'relative',
                    "position_parent" => 'fixed',
                    "auto_center" => 1,
                    "general_top" => 100,
                    "general_top_percent" => 0,
                    "general_bottom" => 100,
                    "general_bottom_percent" => 0,
                    "gt_top_bottom" => 'top',
                    "bt_top_type" => 'px',
                    "bt_bottom_type" => 'px',
                    "general_left" => 0,
                    "gt_right_left" => 'left',
                    "rl_left_type" => 'px',
                    "general_left_percent" => 0,
                    "rl_right_type" => 'px',
                    "general_right" => 0,
                    "rl_right_type" => 'px',
                    "general_right_percent" => 0,
                    "general_bk_opacity" => '0.50',
                    "width_type" => 'px',
                    "general_width" => 600,
                    "general_width_percent" => 30,
                    "height_type" => 'px',
                    "general_height" => 400,
                    "general_height_percent" => 30,
                    "general_delay" => 0,
                    "general_effects" => '',
                    "general_effect_duration" => 1000,
                    "close_effect_duration" => 1000,
                    "close_effects" => '',
                    "general_duration" => 0,
                    "close_design" => 'close_2.png',
                    "close_position" => 'top-right',
                    "abp_top" => '',
                    "abp_left" => '',
                    "abp_right" => '',
                    "abp_bottom" => '',
                    "disable_escape" => 0,
					"disable_button" => 0,
					"disable_bk" => 0,
                    "disable_clickout" => 1,
                    "bk_img_box" => '',
					"bk_img_general" => '',
                    "bk_box_repeat" => 'no-repeat',
                    "box_bk_position_x" => 'center',
                    "box_bk_position_y" => 'top',
                    "box_bk_color" => '#f7f7f7',
                    "box_bk_border_color" => '',
                    "box_border_width" => 0,
                    "box_border_radius" => 0,
                    "c_type" => 'html',
                    "html_content" => '',
                    "the_ifrm_link" => '',
					"the_content_id" => '',
                    "the_postpag_val" => '',
                    "youtube_id_v" => '',
                    "yt_autoplay" => 0,
                    "yt_controls" => 0,
                    "yt_loop" => 0,
                    "yt_theme" => 'light',
                    "yt_autohide" => 0,
                    "h_annotations" => 0,
                    "unregistered_user" => 1,
                    "registered_user" => 1,
                    "first_time_visit" => 0,
                    "show_if_comments" => 0,
                    "n_show_comments" => 0,
                    "ti_from" => '',
                    "ti_until" => '',
                    "date_b_repeat" => 'daily',
                    "time_int_from" => '',
                    "time_int_until" => '',
					'open_event' => 'default',
					'open_event_name' => '',
					'the_event' => 'click',
                    'open_e_tb' => '',
                    'timezone' => '',
					'scroll_position' => 'top',
					'exit_mess' => '',
                    'web_mobile_display' => 'Web&Mobile',
                    'rev_popup_after' => '24',
                    'max_width_mobile' => 100,
                    'min_height_mob' => 1,
                    'mob_tap_close' => 0,
                    'android_checked' => 0,
                    'ios_checked' => 0,
                    'showin_home' => 0,
                    'showin_cats' => 0,
                    'showin_pages' => 0,
                    'showin_posts' => 0,
  					'showin_posts_types' => 0,
                    'showin_arhive' => 0,
                    'showin_templates' => 0,
                    'excluded_cats' => '',
                    'excluded_pages' => '',
                    'excluded_posts' => '',
					'excluded_posts_types' => '',
                    'excluded_templates' => '',
                    's_page_show_in' => '',
                    'se_google' => 0,
                    'se_yahoo' => 0,
                    'se_bing' => 0,
                    'visit_from_sref' => '',
                    'time_reset_cookie' => 24,
                    'max_show_session' => 99,
                    'd_on_evr_vis_pag' => 1,
                    'd_after_vis_pag' => 1,
                    'show_in_country' => '',
  					'slider' => array(),
  					'slider_option' => array('pagination' => 'true', 'autoPlay' => 'true', 'stopOnHover' => 'true', 'paginationSpeed' => 5000, 'slideSpeed'=> 1000),
                    ////FB LIKEBOX OPTIONS
                    'fb_url' => '',
                    'fb_color_scheme' => 'light',
                    'fb_show_faces' => 'true',
                    'fb_header' => 'false',
                    'fb_stream' => 'false',
                    'fb_border' => 'true',
                    'fb_width' => 600,
                    'fb_height' => 400,
                    /////GOOGLE MAP OPTIONS
                    'google_latlgt' => '40.751426, -73.994072',
                    'google_zoom' => 12,
                    'google_map_type' => 'ROADMAP',
                    'google_info_active' => 'no',
                    'google_info_content' => '',
                    'google_info_default_open' => 'no',
                    'google_maker_label' => '',
                    'google_width' => 600,
                    'google_height' => 400,
  					'shortcode' => 'Write your shortcode here!',
  					'custom_page_shortcode_id' => '',
  					//////OPT IN
  					'opt_in_content' => '',
  					'ips_subscribe_type' => '',
  					'opt_in_err_msg' => 'An Error Has Occurred. Please Try Again!',
                    );
	return $arr;
}
function ips_get_metas( $id ){
	global $wpdb;
	$meta_arr = defaultValues();
	$metas = $wpdb->get_results("SELECT meta_name, meta_value
									FROM {$wpdb->prefix}popup_meta
									WHERE {$wpdb->prefix}popup_meta.popup_id = {$id}");
	if(isset($metas) && count($metas)>0){
		foreach($metas as $k=>$v){
			$meta_arr[$v->meta_name] = $v->meta_value;
		}
	}
	return $meta_arr;	
}

function ips_check_popup_exists( $id ){
	global $wpdb;
	$obj = $wpdb->get_row("SELECT name FROM {$wpdb->prefix}popup_windows
								WHERE id={$id};");
	if(isset($obj->name)) return TRUE;
	else return FALSE;
}

function isp_return_data_from_url( $url ){
	if(in_array('curl', get_loaded_extensions())){
		///////////////////CURL
		$ch = curl_init();
		$timeout = 5;
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, $timeout);
		try{
			$data = curl_exec($ch);
		}catch(Exception $e){
			$data = '';
		}
		curl_close($ch);
	}elseif( function_exists('file_get_contents') ){
		//FILE GET CONTENTS
		try{
			$data = file_get_contents( $url );
		}catch(Exception $e){
			$data = '';
		}
	}else{
		$data = '';
	}
	return $data;
}

function is_Popop_Under( $id ){
	global $wpdb;
	$exist = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}popup_meta
								WHERE  meta_name = 'popupType'
								AND popup_id = {$id};");
	if(isset($exist) && $exist > 0){
		return TRUE;
	}
	return FALSE;
}
?>