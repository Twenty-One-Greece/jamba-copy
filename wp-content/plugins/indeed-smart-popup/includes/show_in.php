<?php 
global $wpdb;
$post_type = '';

//POST,PAGE ID
global $post;
if(isset($post)){
	$the_postid = $post->ID;
	$post_type = get_post_type($the_postid);
}

if($post_type=='page' && !is_home() && !is_category() && !is_archive() && isset($the_postid)){
////////////////////////PAGES
	$popup_arr = '';
	$popup_arr = $wpdb->get_results("SELECT popup_id FROM {$wpdb->prefix}popup_meta
										WHERE meta_name='showin_pages'
										AND meta_value=1;");
	if($popup_arr!='' && count($popup_arr)>0){
		foreach($popup_arr as $popupid){
			$ipsw_arr = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}popup_meta
												WHERE meta_name='excluded_pages'
												AND popup_id={$popupid->popup_id};");
			$ips_show = 1;
			if(isset($ipsw_arr) && count($ipsw_arr)>0){
				if(isset($ipsw_arr[0]->meta_value) && $ipsw_arr[0]->meta_value!=''){
					$excluded_pages =  unserialize(base64_decode($ipsw_arr[0]->meta_value));
					if(is_array($excluded_pages) && count($excluded_pages)>0){
						if(in_array($the_postid, $excluded_pages)) $ips_show = 0;
					}
				}	
			}
			if(ips_is_shortcode_page($the_postid)) $ips_show = 0; //prevent infinite loop
			
			if($ips_show==1) $popups_to_show[] = $popupid->popup_id;
			else $popups_to_hide[] = $popupid->popup_id;
		}
	}
}elseif($post_type=='post' && !is_home() && !is_category() && !is_archive() && isset($the_postid)){
////////////////POSTS
	$popup_arr = '';
	$popup_arr = $wpdb->get_results("SELECT popup_id FROM {$wpdb->prefix}popup_meta
								 	     WHERE meta_name='showin_posts'
										 AND meta_value=1;");
	if($popup_arr!='' && count($popup_arr)>0){
		foreach($popup_arr as $popupid){
			$ipsw_arr = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}popup_meta
												WHERE meta_name='excluded_posts'
												AND popup_id={$popupid->popup_id};");
			$ips_show = 1;
			if(isset($ipsw_arr) && count($ipsw_arr)>0){
				if(isset($ipsw_arr[0]->meta_value) && $ipsw_arr[0]->meta_value!=''){
					$excluded_posts =  unserialize(base64_decode($ipsw_arr[0]->meta_value));
					if(is_array($excluded_posts) && count($excluded_posts)>0){
						if(in_array($the_postid, $excluded_posts)) $ips_show = 0;
					}
				}
			}
			if($ips_show==1 ) $popups_to_show[] = $popupid->popup_id;
			else $popups_to_hide[] = $popupid->popup_id;
		}
	}
}elseif(!is_home() && !is_category() && !is_archive() && isset($the_postid)){
	$popup_arr = '';
	$popup_arr = $wpdb->get_results("SELECT popup_id FROM {$wpdb->prefix}popup_meta
										WHERE meta_name='showin_posts'
										AND meta_value=1;");
	if($popup_arr!='' && count($popup_arr)>0){
		foreach($popup_arr as $popupid){
			$ipsw_arr = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}popup_meta
												WHERE meta_name='excluded_posts'
												AND popup_id={$popupid->popup_id};");
			$ips_show = 1;
			if(isset($ipsw_arr) && count($ipsw_arr)>0){
				if(isset($ipsw_arr[0]->meta_value) && $ipsw_arr[0]->meta_value!=''){
					$excluded_posts_types =  unserialize(base64_decode($ipsw_arr[0]->meta_value));
					if(is_array($excluded_posts_types) && count($excluded_posts_types)>0){
						if(in_array($post_type, $excluded_posts_types)) $ips_show = 0;
					}
				}
			}
			if($ips_show==1) $popups_to_show[] = $popupid->popup_id;
			else $popups_to_hide[] = $popupid->popup_id;
		}
	}
}

if(is_category()){
/////////////////////////////CATEGORIES
	$cats = get_the_category();
	if(isset($cats) && count($cats) > 0){
		$cat_id = $cats[0]->cat_ID;
	}else{
		$cat_id = get_query_var('cat');
	}
	$popup_arr = '';
	$popup_arr = $wpdb->get_results("SELECT popup_id FROM {$wpdb->prefix}popup_meta
										WHERE meta_name='showin_cats'
										AND meta_value=1;");
	if($popup_arr!='' && count($popup_arr)>0){
		foreach($popup_arr as $popupid){
			$ipsw_arr = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}popup_meta
												WHERE meta_name='excluded_cats'
												AND popup_id={$popupid->popup_id};");
			$ips_show = 1;
			if(isset($ipsw_arr) && count($ipsw_arr)>0){
				if(isset($ipsw_arr[0]->meta_value) && $ipsw_arr[0]->meta_value!=''){
					$excluded_cats =  unserialize(base64_decode($ipsw_arr[0]->meta_value));
					if(is_array($excluded_cats) && count($excluded_cats)>0){
						if(in_array($cat_id, $excluded_cats)) $ips_show = 0;
					}
				}
			}
			if($ips_show==1) $popups_to_show[] = $popupid->popup_id;
			else $popups_to_hide[] = $popupid->popup_id;
		}
	}
}

if(is_archive()){
///////////////////ARCHIVE
	$popup_arr = '';
	$popup_arr = $wpdb->get_results("SELECT popup_id FROM {$wpdb->prefix}popup_meta
										WHERE meta_name='showin_arhive'
										AND meta_value=1;");
	if(isset($popup_arr) && count($popup_arr)>0){
		foreach($popup_arr as $popupid){
			$popups_to_show[] = $popupid->popup_id;
		}
	}
}

if(isset($the_postid)){
//////////////////////TEMPLATES
	$tmpl_name = basename(get_post_meta( $the_postid, '_wp_page_template', true ));
	if(isset($tmpl_name)){
		$popup_arr = '';
		$popup_arr = $wpdb->get_results("SELECT popup_id FROM {$wpdb->prefix}popup_meta
											WHERE meta_name='showin_templates'
											AND meta_value=1;");
		if($popup_arr!='' && count($popup_arr)>0){
			foreach($popup_arr as $popupid){
				$ipsw_arr = $wpdb->get_results("SELECT meta_value FROM {$wpdb->prefix}popup_meta
													WHERE meta_name='excluded_templates'
													AND popup_id={$popupid->popup_id};");
				$ips_show = 1;
				if(isset($ipsw_arr) && count($ipsw_arr)>0){
					if(isset($ipsw_arr[0]->meta_value) && $ipsw_arr[0]->meta_value!=''){
						$excluded_tmpl =  unserialize(base64_decode($ipsw_arr[0]->meta_value));
						if(is_array($excluded_tmpl) && count($excluded_tmpl)>0){
							if(in_array($tmpl_name, $excluded_tmpl)) $ips_show = 0;
						}
					}
				}
				if($ips_show==1) $popups_to_show[] = $popupid->popup_id;
				else $popups_to_hide[] = $popupid->popup_id;
			}
		}
	}
}

if(is_home()){
////////////////////////HOME
	$popup_arr = $wpdb->get_results("SELECT popup_id FROM {$wpdb->prefix}popup_meta
										WHERE meta_name='showin_home' AND meta_value=1;");
	if(isset($popup_arr) && count($popup_arr)>0){
		foreach($popup_arr as $popupid){
			$popups_to_show[] = $popupid->popup_id;
		}
	}
}

//SPECIFIED PAGE
$current_pg_url = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
$popup_arr = $wpdb->get_results("SELECT popup_id FROM {$wpdb->prefix}popup_meta
									WHERE meta_name='s_page_show_in' AND meta_value='$current_pg_url';");
if(isset($popup_arr) && count($popup_arr)>0){
	foreach($popup_arr as $popupid){
		$popups_to_show[] = $popupid->popup_id;
	}
}

if(isset($popups_to_show) && count($popups_to_show)>0){
	if(isset($popups_to_hide) && count($popups_to_hide)>0) $the_popups = array_diff($popups_to_show, $popups_to_hide);
	else $the_popups = $popups_to_show;
	foreach($the_popups as $popup_id){
		if(is_Popop_Under($popup_id)){
			//PopUp UNDER
			do_shortcode("[indeed_popup_under id={$popup_id}]");
		}
		else{
			// Normal PopUp
			do_shortcode("[indeed_popups id={$popup_id}]");
		}
	}
}

?>