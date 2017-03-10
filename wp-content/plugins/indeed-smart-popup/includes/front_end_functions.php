<?php
function CheckRefferer($meta_arr){
	if($meta_arr['se_google']== 1 || $meta_arr['se_yahoo'] == 1 || $meta_arr['se_bing']== 1 || $meta_arr['visit_from_sref']!=''){
		if(isset($_SERVER['HTTP_REFERER'])){
			$the_ref = $_SERVER['HTTP_REFERER'];
			if($meta_arr['se_google']== 1 && strpos($the_ref, 'google')!== false)  return TRUE; 
			if($meta_arr['se_yahoo']== 1 && strpos($the_ref, 'yahoo')!== false)  return TRUE; 
			if($meta_arr['se_bing']==1 && strpos($the_ref, 'bing')!== false)  return TRUE; 
			if($meta_arr['visit_from_sref']!=''){
				$other_refs = explode(',', $meta_arr['visit_from_sref']);
				if(count($other_refs > 0 )){
					foreach($other_refs as $ref_o){
						if(strpos($the_ref, $ref_o)!== false) return TRUE;
					}
				}
			}
		}
		return FALSE;
	}
	return TRUE;
}
function CheckRegUser($meta_arr){
	if($meta_arr['registered_user'] == 1 || $meta_arr['unregistered_user'] == 1){
		if($meta_arr['registered_user'] == 1 && is_user_logged_in() == 1) return TRUE;
		if($meta_arr['unregistered_user'] == 1 && is_user_logged_in() != 1) return TRUE;	
	  return FALSE;	
	}
	return TRUE;
}
function CheckComUser($meta_arr){
	global $wpdb;
	if($meta_arr['show_if_comments'] == 1 || $meta_arr['n_show_comments'] == 1){
		
		$u_id = get_current_user_id();
		$comments = $wpdb->get_results("SELECT comment_ID FROM {$wpdb->prefix}comments
                                            WHERE user_id = $u_id
                                            AND comment_approved = 1;");
											
		if($meta_arr['show_if_comments'] == 1 && count($comments) > 0) return TRUE;
		if($meta_arr['n_show_comments'] && count($comments) < 1) return TRUE;	
	  return FALSE;	
	}
	return TRUE;
}
function CheckFirstTimeUser($meta_arr, $ips_id){
	global $wpdb;
	if($meta_arr['first_time_visit'] == 1){
        if(isset($_COOKIE['ips_visitor'])){
            $ips_u_cookie = $_COOKIE['ips_visitor'];
            $the_visits = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_visits WHERE
                                                {$wpdb->prefix}popup_visits.visitor = '$ips_u_cookie'
                                                AND {$wpdb->prefix}popup_visits.popup_id = $ips_id;");
            if(isset($the_visits) && count($the_visits)>0){
				if($meta_arr['rev_popup_after']!='' && $meta_arr['rev_popup_after']!==0){
					$last_visit = $wpdb->get_results("SELECT visit FROM {$wpdb->prefix}popup_visits
													  WHERE visitor = '$ips_u_cookie'
													  AND popup_id = $ips_id
													  ORDER BY id DESC LIMIT 1;");
					$the_last_visit = strtotime($last_visit[0]->visit);
					
					$after_tm_val = $meta_arr['rev_popup_after'] * 3600;
					$after_tm_val = intval($after_tm_val);
					$available_time = $the_last_visit + $after_tm_val;
					$current_time = strtotime(date("Y-m-d H:i:s"));
					if($current_time>=$available_time) return TRUE;
				}
			   return FALSE;	
			}
        }	
    }
	return TRUE;
}


function CheckLocation($meta_arr, &$current_country, $ips_id){
    $current_country = '';
	if(!isset($_COOKIE['country'][$ips_id])){
		$data_location = isp_return_data_from_url( "http://api.hostip.info/get_json.php?ip=".$_SERVER['REMOTE_ADDR'] ); 	
    	if(isset($data_location) && $data_location!='' && is_object($data_location)){
    		$location = json_decode($data_location);
    	}
		if(isset($location->country_code) && $location->country_code!='') $current_country = $location->country_code;
	}elseif(isset($_COOKIE['country'][$ips_id]) && $_COOKIE['country'][$ips_id]!=''){
		$current_country = $_COOKIE['country'][$ips_id];
	}else return TRUE;		

	if(isset($meta_arr['show_in_country']) && $meta_arr['show_in_country']!=''){
        if(strpos($meta_arr['show_in_country'], ',')!==FALSE){
            $countries = explode(',', $meta_arr['show_in_country']);
        }else{
            $countries[] = $meta_arr['show_in_country'];
        }
        if(isset($current_country) && $current_country!='' && $current_country!='XX'){
            if(in_array($current_country, $countries)) return TRUE;
            else return FALSE;
        }
        return TRUE;
    }
	return TRUE;
}
function CheckMobileCond($meta_arr, &$device_type){
	$mobile_devices = "/Mobile|Android|BlackBerry|iPhone|iPad|Windows Phone/";
	$ios_devices = "/iPhone|iPad/";
	$android_devices = "/Android/";
	
	if (preg_match($mobile_devices, $_SERVER['HTTP_USER_AGENT'])) $device_type = 'mobile';
    else $device_type = 'web';
    if($meta_arr['web_mobile_display']== 'Mobile' && $device_type == 'web') return FALSE;
    if($meta_arr['web_mobile_display']== 'Web' && $device_type== 'mobile') return FALSE;
		
    if($device_type == 'mobile'){
        if($meta_arr['ios_checked']== 1 || $meta_arr['android_checked']== 1){
      	    	
            if($meta_arr['ios_checked']== 1 && preg_match($ios_devices, $_SERVER['HTTP_USER_AGENT'])) return TRUE;
            if($meta_arr['android_checked']== 1 && preg_match($android_devices, $_SERVER['HTTP_USER_AGENT'])) return TRUE;
		  return FALSE;	
        }
	}
	return TRUE;
}
function ManageSession($meta_arr, $ips_id, $visit_pages_arr, $country){
	if(isset($_COOKIE['session_time'][$ips_id])){
        $session_time = $_COOKIE['session_time'][$ips_id];
    }else{
    	///////////////////////////////SET SESSTION TIME COOKIE
		$session_time = time();
		?>
		<script name='SmartPopUp'>
			setCookie('session_time[<?php echo $ips_id;?>]', <?php echo $session_time;?>, 365);	
		</script>
		<?php 
	}

	
	$end_session = (int)$session_time + ((int)$meta_arr['time_reset_cookie']*60); 
	$current_time = (int)time();
	
	if($current_time > $end_session){ ////////////////////RESET COOKIES
       	$visit_views = 0;
       	$visit_pages = 1;
        ?>
    	    <script name='SmartPopUp'>
    	    
				setCookie('visit_views[<?php echo $ips_id;?>]', '', -1);
				setCookie('visit_pages[<?php echo $ips_id;?>]', '', -1);
				setCookie('country[<?php echo $ips_id;?>]', '', -1);
				setCookie('session_time[<?php echo $ips_id;?>]', '', -1);

				setCookie('visit_views[<?php echo $ips_id;?>]', <?php echo $visit_views;?>, 365);
				setCookie('visit_pages[<?php echo $ips_id;?>]', <?php echo $visit_pages;?>, 365);
				setCookie('country[<?php echo $ips_id;?>]', '<?php echo $country;?>', 365);
				setCookie('session_time[<?php echo $ips_id;?>]', <?php echo $current_time;?>, 365);
			
	        </script>
        <?php
    }else{
		if(isset($_COOKIE['visit_views'][$ips_id])) 
			$visit_views = $_COOKIE['visit_views'][$ips_id];
   		else {
        	$visit_views = 0;
        	?>
            <script name='SmartPopUp'>
            	setCookie('visit_views[<?php echo $ips_id;?>]', '', -1);
            	setCookie('visit_views[<?php echo $ips_id;?>]', <?php echo $visit_views;?>, 365);
            </script>
        	<?php
    	}
		 if(isset($visit_pages_arr[$ips_id]) && $visit_pages_arr[$ips_id]!=''){
        	 $visit_pages = $visit_pages_arr[$ips_id];
    	}else{
			$visit_pages = 1;
			?>
			<script name='SmartPopUp'>
				setCookie('visit_pages[<?php echo $ips_id;?>]', '', -1);
				setCookie('visit_pages[<?php echo $ips_id;?>]', <?php echo $visit_pages;?>, 365);
			</script>
			<?php
		}
		if(!isset($_COOKIE['country'][$ips_id])){
			?>
			<script name='SmartPopUp'>
				setCookie('country[<?php echo $ips_id;?>]', '', -1);
				setCookie('country[<?php echo $ips_id;?>]', '<?php echo $country;?>', 365);
			</script>
			<?php
		}
	}
	return array('visit_views' => $visit_views, 'visit_pages' => $visit_pages);
}
function CheckSessionCond($meta_arr, $sessionData){
	if($sessionData['visit_views'] > $meta_arr['max_show_session'] || $sessionData['visit_views'] < 0) return FALSE;
    if($sessionData['visit_pages'] < $meta_arr['d_after_vis_pag']) return FALSE;
	elseif($sessionData['visit_pages']%$meta_arr['d_on_evr_vis_pag'] != 0) return FALSE;
	return TRUE;
}
function CheckSchedulingTime($meta_arr, $ips_id){
	echo "\n
	<script name='SmartPopup Schedule Time JS'>
	function pad(n) { return (\"0\" + n).slice(-2); }
	if (typeof schedule_array == 'undefined') {
			var schedule_array = [];
		}";
	
	if ($meta_arr['ti_until'] == '' && $meta_arr['ti_from']  == '' &&  $meta_arr['timezone']  == '' &&  $meta_arr['time_int_from'] == '' &&  $meta_arr['time_int_until'] == '' && $meta_arr['date_b_repeat'] == 'daily' ) {
		echo "\n 
		var allow = 1;
		";
		
	}else{
	
	echo "\n
		var allow = 1;
		var current_date = new Date();
		var current_time = current_date.getHours()+':'+current_date.getMinutes();";
	$timezone = explode('"',$meta_arr['timezone']);
	foreach($timezone as $k => $v) {
		if ($k % 2 == 1) $timezones[] = (int)$v;
	}
	if (isset ($timezones) ) { 
		$timezones = implode ("|", $timezones); 
		echo "
		var local_timezone = -(new Date().getTimezoneOffset()/60);
		var timezones = '".$timezones."';
		if (timezones.length)
		var timezones_array = timezones.split('|');
		else
		var timezones_array = null;
		if (jQuery.isArray(timezones_array))
			if ((jQuery.inArray(String(local_timezone), timezones_array)) == -1) allow =  2;
		";
	}
	
	$ti_from = explode('/', $meta_arr['ti_from']);	
	if (isset($ti_from[2]) ) {
		echo "\n
		var ti_from = new Date(".$ti_from[2].", ".($ti_from[0]-1).", ".$ti_from[1].");
		if (current_date < ti_from) allow =  5; ";
	}
	
	$ti_until = explode('/', $meta_arr['ti_until']);
	if (isset($ti_until[2]) ) {
		echo "\n
		var ti_until = new Date(".$ti_until[2].", ".($ti_until[0]-1).", ".$ti_until[1].", 23, 59, 59);
		if (ti_until < current_date) allow =  6; ";
	}
	
	if ($meta_arr['time_int_from']) {
		$meta_arr['time_int_from'] = explode(":", $meta_arr['time_int_from']);
		$meta_arr['time_int_from'] = implode( ":", array((int)$meta_arr['time_int_from'][0], (int)$meta_arr['time_int_from'][1] ));
		echo "\n
		var start_time = '". $meta_arr['time_int_from']."';
		start_time = start_time.split(':');
		start_time = pad(start_time[0])+':'+pad(start_time[1]);
		if (current_time < start_time) allow =  3;
		";
	}
	if ($meta_arr['time_int_until']) {
		$meta_arr['time_int_until'] = explode(":", $meta_arr['time_int_until']);
		$meta_arr['time_int_until'] = implode( ":", array((int)$meta_arr['time_int_until'][0], (int)$meta_arr['time_int_until'][1] ));
		echo "\n
		var end_time = '".$meta_arr['time_int_until']."';
		end_time = end_time.split(':');
		end_time = pad(end_time[0])+':'+pad(end_time[1]);
		if (end_time < current_time) allow =  4;";
	}
	
	if ($meta_arr['date_b_repeat'] != 'daily') {
	echo "
	var repeat_day = '".$meta_arr['date_b_repeat']."';
	var day_of_week = current_date.getDay();
	switch (repeat_day) {
		case 'daily':
			1;
			break;
		case 'week_day':
			if (day_of_week <= 1 && day_of_week >= 5) allow =  7;
			break;
		case 'week_end':
			if (day_of_week == 0 || day_of_week == 6) {} else allow =  8;
			break;
		}";	
	}
	}
	echo " schedule_array[".$ips_id."] = allow;";
	echo "\n
	</script>
	\n";
	
	return TRUE;
}
function CheckDuplicates($ips_active_items, $ips_id){
	if(is_array($ips_active_items) && in_array($ips_id, $ips_active_items)) return FALSE;
	return TRUE;
}

///////////////////////////////////Style Functions
function ips_return_slider_extra_style($type, $ips_id){
	if($type=='imgSlider'){
		return "#slider_".$ips_id." .item img{
					display: block;
					max-width: 100%;
					max-height:100%;
					margin:0 auto;
				}
				#bar{
					width: 0%;
					max-width: 100%;
					height: 4px;
					background: #EDEDED;
					opacity:0.7;
				}
				#progressBar{
					width: 100%;
				}";
	}else return '';
}
function Get_IpsWrappAbClass($meta_arr, $ips_id){
	$result = '';	
		
	$hide = '';
	if($meta_arr['disable_bk']==1){
		$hide = "display: none !important;";
	}
	if(isset($meta_arr['bk_img_general']) && $meta_arr['bk_img_general']!='')
		$background = "background:url({$meta_arr['bk_img_general']}) left top repeat;
		  opacity:{$meta_arr['general_bk_opacity']};";
	  else
		$background = "background-color: rgba(0,0,0,{$meta_arr['general_bk_opacity']});";
	
	$result .= "
	.red-border {
		border: 1px solid red;
		color: red;
	}
	.ips_wrapp_ab_{$ips_id}{
		  position: fixed;
		  z-index: 999991;
		  top: 0px;
		  height: auto;
		  left: 0px;
		  width: 100%;
		  min-height: 100%;
		  $background
		  $hide
	}";
	
	return $result;
}
function Get_IpsWPCClass($meta_arr, $ips_id){
	$result = '';	
	
	if($meta_arr['disable_bk'] == 1){
		$hide = 'width: 100%; 
		height: 0px;';
	}else{
		$hide = 'width: 100%; 
		min-height: 100%;';
	}
	$position = "position: ".$meta_arr['position_parent'].";";
	
	$result .= "
	.ips_w_p_c_{$ips_id}{
		$position
		top: 0px;
		left: 0px;
		margin: 0px;
		padding: 0px;
		z-index: 999991;
		$hide
	}";
	
	return $result;
}
function Get_IpsWClass($meta_arr, $ips_id, $device_type){
	$result = '';
	$margin = '';
	if($meta_arr['auto_center']==1) $margin = "margin: 0px auto;";
	
	$position_type = "position: ".$meta_arr['position'].";";
	
	$vertical_pos = '';
	if($meta_arr['gt_top_bottom']== 'top'){
		if($meta_arr['bt_top_type']=='px') $vertical_pos = "top: ".$meta_arr['general_top'].'px;';
		else $vertical_pos = "top: ".$meta_arr['general_top_percent'].'%;';
	}else{
		if($meta_arr['bt_bottom_type']=='px') $vertical_pos = "bottom: ".$meta_arr['general_bottom'].'px;';
		else $vertical_pos = "bottom: ".$meta_arr['general_bottom_percent'].'%;';
	}
	
	$horizontal_pos = '';
	if($meta_arr['gt_right_left']=='left'){
		if($meta_arr['rl_left_type']=='px') $horizontal_pos = "left: ".$meta_arr['general_left'].'px;';
		else $horizontal_pos = "left: ".$meta_arr['general_left_percent'].'%;';
	}else{
		if($meta_arr['rl_right_type']=='px') $horizontal_pos = "right: ".$meta_arr['general_right'].'px;';
		else $horizontal_pos = "right: ".$meta_arr['general_right_percent'].'%;';
	}
	
	$width = '';
	if($meta_arr['width_type']=='px'){
		$width = "width: ".$meta_arr['general_width'].'px;';
	}else{
		$width = "width: ".$meta_arr['general_width_percent'].'%;';
	}
	
	$height = '';
	if($meta_arr['height_type']=='px'){
		$height = "height: ".$meta_arr['general_height'].'px;';
	}else{
		$height = "height: ".$meta_arr['general_height_percent'].'%;';
	}
	$background = '';
	if($meta_arr['box_bk_color']!='' && $meta_arr['bk_img_box']!='')$background = "background: {$meta_arr['box_bk_color']} url({$meta_arr['bk_img_box']});";
	elseif($meta_arr['box_bk_color']!='') $background .= "background: {$meta_arr['box_bk_color']};";
	elseif($meta_arr['bk_img_box']!='') $background .= "background: url({$meta_arr['bk_img_box']});";
	
	$border = '';
	if($meta_arr['box_bk_border_color']!='') $border = " border: {$meta_arr['box_border_width']}px solid {$meta_arr['box_bk_border_color']};";
	
	// MOBILE: MAX-WIDTH, MIN-HEIGHT, CLOSE ON TAP
	//$max_width = '';
	$max_width = "max-width:".$meta_arr['max_width_mobile'].'%;';
	$min_height = '';
	if(isset($device_type) && $device_type=='mobile'){
		//$max_width = "max-width:".$meta_arr['max_width_mobile'].'%;';
		if($meta_arr['min_height_mob'] == 1)
		$min_height = "min-".$height;
	}
		
	$result .= "
	.ips_w_{$ips_id}{
			z-index: 999992;
			$position_type
			$vertical_pos
			$horizontal_pos
			$margin
			$width
			$height
			$max_width
			$min_height
			$background
			background-position: {$meta_arr['box_bk_position_y']} {$meta_arr['box_bk_position_x']};
			background-repeat: {$meta_arr['bk_box_repeat']};
			$border
			-moz-border-radius: {$meta_arr['box_border_radius']}px;
			-webkit-border-radius: {$meta_arr['box_border_radius']}px;
			-khtml-border-radius: {$meta_arr['box_border_radius']}px;
			border-radius: {$meta_arr['box_border_radius']}px;
	}";  
	return $result;
}
function Get_IpsCloseBttnClass($meta_arr, $ips_id){
	$result = '';
	
	$position = '';
	if(isset($meta_arr['close_position'])){
		switch($meta_arr['close_position']){
		  case 'top-left':
				$position = 'top: -10px;
				left: -10px;';
				break;
		  case 'top-right':
				$position = 'top: -10px;
				right: -10px;';
				break;
		  case 'bottom-left':
				$position = 'bottom: -10px;
				left: -10px;';
				break;
		  case 'bottom-right':
				$position = 'bottom: -10px;
				right: -10px;';
				break;
		  default:
				break;		
		}
	}
	if($meta_arr['abp_top']!='') $position .= 'top: '.$meta_arr['abp_top'].'px;';
	if($meta_arr['abp_left']!='') $position .= 'left: '.$meta_arr['abp_left'].'px;';
	if($meta_arr['abp_right']!='') $position .= 'right: '.$meta_arr['abp_right'].'px;';
	if($meta_arr['abp_bottom']!='') $position .= 'bottom: '.$meta_arr['abp_bottom'].'px;';
	
	$disable = "";
	if($meta_arr['disable_button']==1) $disable = 'display:none;';
	  
	$result .= "
	.ips_close_bttn_{$ips_id}{
			cursor: pointer;
			position: absolute;
			z-index:9999;
			$position
			$disable
	}";
		
	
	$result .= "
	.ips_close_bttn_{$ips_id} img{
			width: 20px;
			height: 20px;
	}";
	
	return $result;
}
function Get_IpsContent($ips_id){
	$result = '';
	
	$result .= "
	.ips_h_content_{$ips_id}{
			width: 100%;
			height: 100%;
	}";
	
	return $result;
}
///////////////////////////////////JavaScript Functions
function ips_is_preview_r_schedule($preview, $ips_id){
	if($preview==FALSE || $preview!=1){
		return "if (schedule_array[".$ips_id."] != 1) {
					the_popup_".$ips_id." = null;
				}";
	}return '';
}
function GetContent($meta_arr, $ips_id){
	$html_content = '';
	$js_content = '';
	$js_action = "prependTo";// prependTo without quotes , first it was 'prependTo', generate js errors
	
	switch($meta_arr['c_type']){
		
		case 'imgSlider':
			if(isset($meta_arr['slider']) && count($meta_arr['slider'])>0){
				$slides = (unserialize(base64_decode($meta_arr['slider'])));
				if(is_array($slides)) {
					$html_content = '<div id="slider_'.$ips_id.'" class="owl-carousel">';
					foreach($slides as $k=>$v) {
						$html_content .= '<div class="item"><img src="http://'.$v.'" /></div>';
					}
					$html_content .= '</div>';
				}					
			}
			break;
	  case 'video': 
			//VIDEO
			if(substr( $meta_arr['youtube_id_v'], 0, 4 ) === "http"){
				$vs_arr = explode('=',$meta_arr['youtube_id_v']);
				$video_id = $vs_arr[1];
			}else{
			  $video_id = $meta_arr['youtube_id_v'];
			}
			
			//OPTIONS
			if($meta_arr['yt_autoplay']==1) $yt_arr[] = "autoplay=1";
			else $yt_arr[] = "autoplay=0";
			if($meta_arr['yt_controls']==1) $yt_arr[] = "controls=1";
			else $yt_arr[] = "controls=0";
			if($meta_arr['yt_loop']==1) {
			  $yt_arr[] = "loop=1";
			  $yt_arr[] = "playlist=$video_id";
			}
			else $yt_arr[] = "loop=0";
			if($meta_arr['yt_autohide']==1) $yt_arr[] = "autohide=1";
			else $yt_arr[] = "autohide=0";
			$yt_arr[] = "theme=".$meta_arr["yt_theme"];
			if($meta_arr['h_annotations']==1) $yt_arr[] = "iv_load_policy=3";
			else $yt_arr[] = "iv_load_policy=1";
			$yt_opt = implode('&', $yt_arr);
			
			$html_content = "<iframe id=\"ytplayer_{$ips_id}\" type=\"text/html\" width=\"100%\" style=\"min-height:100%; margin-bottom:-3%; border:none;\" src=\"http://www.youtube.com/embed/{$video_id}?$yt_opt\"></iframe>";
	  break;
	
	  case 'iframe':
		//IFRAME
		$ips_ifrm_lnk = $meta_arr['the_ifrm_link'];
		$html_content = "<iframe src=\"$ips_ifrm_lnk\"  width=\"100%\" style=\"min-height:100%; margin-bottom:-3%; border:none;\"></iframe>";
	  break;
	
	  case 'html':
		//HMTL
		$html_content = $meta_arr['html_content'];
		$html_content = str_replace('&quot;', '"', $html_content);
		$html_content = str_replace('&#39;', "\'", $html_content);
		//SHORTCODE CHECK
		$shortcode_regex = get_shortcode_regex();
		if(preg_match("/".$shortcode_regex."/", $html_content, $the_sorthc)){
		  if(strpos($the_sorthc[0], "[indeed_popups id=") === false){
			$shortcode_content = do_shortcode($the_sorthc[0]);
			$shortcode_content = str_replace(array("\r\n", "\n", "\r"), '', $shortcode_content);
			$shortcode_content = addslashes($shortcode_content);
			$ips_pieces = explode($the_sorthc[0], $html_content);
			$html_content = '';
			if(isset($ips_pieces[0])) $html_content .= $ips_pieces[0];
			$html_content .= $shortcode_content;
			if(isset($ips_pieces[1])) $html_content .= $ips_pieces[1];
		  }
		}
		$js_pos_num = strpos($html_content, '<script');
		if( $js_pos_num !== false){
			//on the beginning
			if($js_pos_num < 2) $js_action = "appendTo";// appendTo without quotes , first it was 'appendTo', generate js errors
			$html_str = '';
			$js_str = '';
			$i = 1;
			while($i>0){
					$split_1 = explode("<script", $html_content);
					$html_handler = "";
					$js_handler = '';
	
					if($split_1[0]!= ""){
						$html_handler = $split_1[0];
						$html_str .= $html_handler;
					}
	
					$split_2 = explode("</script>", $split_1[1]);
					$js_handler = "<script".$split_2[0]."</script>";
					$js_str .= $js_handler;
					$str_rep = $html_handler.$js_handler;
	
					$html_content = str_replace($str_rep, "", $html_content);
					if($html_content!=''){
						if(strpos($html_content, "<script")===false){
							$i = 0;
							$html_str .= $html_content;
						}
					}else{
						$i = 0;
					}
				}
			$html_content = $html_str;
			$js_content = $js_str;
		}
	  break;
	
	  case 'content_id':
	  		if($meta_arr['the_content_id']!=''){
	  			$div_cdi_html = "'+div_cdi_html+'";
	  			$div_cdi_class = "'+div_cdi_class+'";
	  			$div_cdi_style = "'+div_cdi_style+'";
	  			$html_content = '<div id="'.$meta_arr['the_content_id'].'" class="'.$div_cdi_class.'" style="'.$div_cdi_style.'">'.$div_cdi_html.'</div>';
	  		}
	  break;
	
	  case 'the_postpag_v':
	  global $wpdb;
		   $the_post_content = $wpdb->get_results("SELECT post_content FROM {$wpdb->prefix}posts WHERE ID={$meta_arr['the_postpag_val']};");
		   if(isset($the_post_content[0]->post_content)){
				$ipsc_content = wpautop($the_post_content[0]->post_content);
				$html_content = str_replace (array("\r\n", "\n", "\r"), '<br/>', $ipsc_content);
				$html_content = addslashes($html_content);
				//SHORTCODE CHECK
				$shortcode_regex = get_shortcode_regex();
				if(preg_match("/".$shortcode_regex."/", $html_content, $the_sorthc)){
				  if(strpos($the_sorthc[0], "[indeed_popups id=") === false){
					$html_content = str_replace($the_sorthc[0], "", $html_content);
					$shortcode_content = do_shortcode($the_sorthc[0]);
					$shortcode_content = str_replace("\n", "", $shortcode_content);
					$shortcode_content = addslashes($shortcode_content);
					$ips_pieces = explode($the_sorthc[0], $html_content);
					$html_content = '';
					if(isset($ips_pieces[0])) $html_content .= $ips_pieces[0];
					$html_content .= $shortcode_content;
					if(isset($ips_pieces[1])) $html_content .= $ips_pieces[1];
					}
				}
		   }
	  break;
       case "fb_likebox":
	   		if (strpos($meta_arr['fb_url'], "https://www.facebook.com/") !== false){
				$fb_url = $meta_arr['fb_url'];
			}else{
				$fb_url = "https://www.facebook.com/".$meta_arr['fb_url'];
			}
	   		//<div id=\"fb-root\"></div> to html_content
            $html_content .= "<div id=\"fb-root\"></div><div class=\"fb-like-box\" data-href=\"".$fb_url."\" data-width=\"".$meta_arr['fb_width']."\" data-height=\"".$meta_arr['fb_height']."\" data-colorscheme=\"".$meta_arr['fb_color_scheme']."\" data-show-faces=\"".$meta_arr['fb_show_faces']."\" data-header=\"".$meta_arr['fb_header']."\" data-stream=\"".$meta_arr['fb_stream']."\" data-show-border=\"".$meta_arr['fb_border']."\"></div>";
      break;
      case "google_maps":
            global $g_maps_id;
            $g_maps_id = "indeedGmaps_".rand(1,100);
            $html_content .= "<div id=\"$g_maps_id\" style=\"width: ".$meta_arr['google_width']."px;height: ".$meta_arr['google_height']."px;\"></div>";
      break;
      
      case 'shortcode':
      	//IFRAME
		global $wpdb;
      	if(isset($meta_arr['custom_page_shortcode_id']) && $meta_arr['custom_page_shortcode_id']!=''){
      		if(get_post_status($meta_arr['custom_page_shortcode_id'])=='trash') return;
      		$link = get_permalink($meta_arr['custom_page_shortcode_id']);
      		if(isset($link) && $link!=FALSE){
      			$html_content = "<iframe src=\"$link\"  width=\"100%\" style=\"min-height:100%; margin-bottom:-3%; border:none;\" class=\"isp_iframe_shortcode\"></iframe>";
      		}else return;
      	}
      break;
      
      case 'opt_in':
      	$html_content = $meta_arr['opt_in_content'];
      	$html_content = str_replace('&quot;', '"', $html_content);
        $html_content = str_replace('&#39;', "\'", $html_content);  
    		
      break;
	}
	$content = array('html_content' => $html_content, 'js_content' => $js_content, 'js_action' => $js_action);
	
	return $content;
}



function ClosePopup($meta_arr, $ips_id){
	
	//INIT VALUES
	$close_actions = '';
	
	if($meta_arr['close_effects'] != '')
    $close_effect = $meta_arr['close_effects'];//initialy was $close_effect = "'".$meta_arr['close_effects']."'";, generate js error
	else $close_effect = '';
	
	if($meta_arr['close_effect_duration'] != '')
	$close_duration = $meta_arr['close_effect_duration'];
	else $close_duration = 0;
	
	$disable_escape = $meta_arr['disable_escape'];	
    $disable_co = $meta_arr['disable_clickout'];
    /**********CLOSE WITH EFFECT ****************/
	if($close_effect!='') {
	  $close_actions .= "
		function close_popup_{$ips_id}(){
            var id_bk = '#ips_wr_{$ips_id}';
            var id_wrapp = '#ips_w_p_{$ips_id}';
            var id_popup = '#ips_main_{$ips_id}';
			stop_croll = 0;
			display_once = 0;
			stop_custom_event = 0;";
        if($close_effect=='fadeOut'){
            /***********CLOSE WITH FADEOUT************/
        	  $close_actions .= "
        			jQuery(id_popup).fadeOut( {$close_duration} );
        	";
        }else{
            /*************CLOSE WITH HIDE EFFECT********/
	        $options_effect = "{}";
    	    if(strpos($close_effect, 'slide_')!==FALSE){
                $e_type = explode('_', $close_effect);
                $options_effect = "{direction: '{$e_type[1]}', mode : 'hide' }";
                $close_effect = 'slide';
    	    }
        	  $close_actions .= "
        		   jQuery(id_popup).hide( '{$close_effect}', $options_effect, {$close_duration});
        	";
        }
      $close_actions .= "
            setTimeout(function(){jQuery(id_bk).fadeOut();}, {$close_duration});
            setTimeout(function(){jQuery(id_wrapp).fadeOut();}, {$close_duration});
            setTimeout(function(){jQuery('.ui-effects-wrapper').remove();}, {$close_duration});
        }//end of close_popup_{$ips_id}
      ";
	}else{
	  /**********CLOSE WITHOUT EFFECT ****************/
	  $close_actions .= "       
		function close_popup_{$ips_id}(){
            var id_bk = '#ips_wr_{$ips_id}';
            var id_wrapp = '#ips_w_p_{$ips_id}';
            var id_popup = '#ips_main_{$ips_id}';
			stop_croll = 0;
			display_once = 0;
			stop_custom_event = 0;
            jQuery(id_popup).remove();
            jQuery(id_wrapp).remove();
            jQuery(id_bk).remove();
            jQuery('.ui-effects-wrapper').remove();
    	}//end of close_popup_{$ips_id}
	";
	}
	
    /**************LOCKER FUNCTION*************/
	  $close_actions .= "
		 function locker(id){
			stop_croll = 0;
			display_once = 0;
			stop_custom_event = 0;
            close_popup_{$ips_id}();
		}
	";
	 
	$close_actions .= "
		function dontShow(id){console.log(id);
			setCookie('visit_views['+id+']',-1, 365);
			locker(id);
   		}
	";
    if($disable_escape == 0){
	  $close_actions .= "
	  	window.onkeyup = function (event) {
			if (event.keyCode == 27) {
				close_popup_{$ips_id}();
				disable_escape = 1;
			}
		}
	  ";
	}
	
    if($disable_co == 0){
	  $close_actions .= "
	  	jQuery(document).click(function(event) {
			if ( jQuery(event.target).closest('.ips_w_{$ips_id}').get(0) == null ) {
				close_popup_{$ips_id}();
				disable_co = 1;
				}
		});
	   ";
    }
	
	return $close_actions;
}
function JsContent($meta_arr, $ips_id, $device_type, $html_content){
	//MOBILE CONDITION
	$onTap = '';
	$content = '';
	$special_content = '';
	
	if($meta_arr['c_type'] == 'content_id' && $meta_arr['the_content_id']!=''){
	$special_content .= "var div_cdi_html = jQuery('#".$meta_arr['the_content_id']."').html(); 
			var div_cdi_class = jQuery('#".$meta_arr['the_content_id']."').attr('class');
            var div_cdi_style = jQuery('#".$meta_arr['the_content_id']."').attr('style');
			if(typeof div_cdi_html=='undefined') div_cdi_html = '';
			if(typeof div_cdi_class=='undefined') div_cdi_class = '';
			if(typeof div_cdi_style=='undefined') div_cdi_style = '';	
			";
	
	}
	
	if(isset($device_type) && $device_type=='mobile')
		if($meta_arr['mob_tap_close']==1)
			$onTap = "onClick=\"close_popup_{$ips_id}();\"";
	
	$content .= "<div id=\"ips_wr_{$ips_id}\" class=\"ips_wrapp_ab_{$ips_id}\"></div>";
	$content .= "<div id=\"ips_w_p_{$ips_id}\" class=\"ips_w_p_c_{$ips_id} isp_div_parent_w\">";
	$content .= "  <div class=\"ips_w_{$ips_id}\" id=\"ips_main_{$ips_id}\" {$onTap}>";
	$content .= "	  <div id=\"ips_close_{$ips_id}\" class=\"ips_close_bttn_{$ips_id}\" onClick=\"close_popup_{$ips_id}();\">";
	$content .= "		 <img src=\"".ISP_DIR_URL."assets/img/{$meta_arr['close_design']}\"/>";
	$content .= "	  </div>";
	$content .= "	  <div class=\"ips_h_content_{$ips_id}\">$html_content</div>";
	$content .= "  </div>";
	$content .= "</div>";
	
	$content = $special_content."
		the_popup_".$ips_id." = '".$content."';";
	
	return $content;	
}

function isp_insert_form_results_db($items){
	global $wpdb;
	$user_hash = $_COOKIE['ips_visitor'];
	if(!isset($user_hash) || $user_hash=='') $user_hash = $_SERVER['REMOTE_ADDR'];
	foreach($items as $k=>$v) {
		$wpdb->get_results("INSERT INTO {$wpdb->prefix}popup_form_results VALUES
		('', '".$_REQUEST['ips_id']."', '$k', '".urldecode($v)."', null, '".$user_hash."')");//
	}	
}

function isp_send_form_results_via_mail($items){
	@$to = get_option('ips_main_email');
	if($to===FALSE || $to==''){
		$to = $admin_email = get_option( 'admin_email' );
	}	
	$subject = 'Form result from popup ID: '.$_REQUEST['ips_id'];
	$message = '';
	foreach($items as $k=>$v) {
		$message .= $k.": ".urldecode($v)."\n";
	}
	$headers = 'From: Indeed Popup <'.$to.'>' . "\r\n";
	if(wp_mail( $to, $subject, $message, $headers)) {
		return 1;
	}else{
		return 0;
	}
}


function OpenEvent($meta_arr, $js_action, $ips_id){
  //INIT VALUES
  $event = '';
  $showEffect = '';
  $autoClose = '';
  
  if($meta_arr['general_effects'] != '')
  	$effect = $meta_arr['general_effects'];// effect string must be without quotes ('')
  else $effect = '';
  
  $add_cont_type = $js_action;
  if($meta_arr['general_effect_duration'] != '')
  $effect_duration = $meta_arr['general_effect_duration'];
  else $effect_duration = 0;
  
  $delay = (int)($meta_arr['general_delay']*1000);
  $total_duration = $effect_duration+$delay;
  if($meta_arr['general_duration']!='') $duration = $meta_arr['general_duration']*1000;
  else $duration = 0;
  
  if($meta_arr['general_duration']!='') $exit_mess = "'".$meta_arr['exit_mess']."'";
  else $exit_mess = '';
  
  $event_type = $meta_arr['open_e_tb'];
  $event_name = $meta_arr['open_event_name'];
  $event_action = $meta_arr['the_event'];
  
  $scroll_position = "'".$meta_arr['scroll_position']."'";
 
  if($effect != ''){
  //////////////////////////////////////////////////WITH EFFECT
            $showEffect = "            setTimeout (function () {            /* before */			var i=0;			function appendSomeItems() {
	             ";
        if(strpos($effect, 'show_')!==false){
          //SHOW EFFECTS
            $showEffect .= "
      				        jQuery(the_popup_{$ips_id}).{$add_cont_type}('body');
    						jQuery('#ips_main_{$ips_id}').css('display', 'none');
                          ";
			
            $e_show_type = explode('_', $effect);
            switch($e_show_type[1]){
                case 'scale':
                    $show_opt = "{percent: 100}";
                break;
                case 'slideup':
                    $show_opt = "{direction: 'up', mode: 'hide'}";
                    $e_show_type[1] = 'slide';
                break;
                case 'slidedown':
                    $show_opt = "{direction: 'down', mode: 'hide'}";
                    $e_show_type[1] = 'slide';
                break;
                case 'slideright':
                    $show_opt = "{direction: 'right', mode: 'hide'}";
                    $e_show_type[1] = 'slide';
                break;
                case 'slideleft':
                    $show_opt = "{direction: 'left', mode: 'hide'}";
                    $e_show_type[1] = 'slide';
                break;
                default:
                    $show_opt = "{}";
                break;
            }
            $showEffect .= "
                            jQuery('#ips_main_{$ips_id}').show('{$e_show_type[1]}', $show_opt, $effect_duration);
                           ";
        }else{
            switch($effect){
                case 'fadeIn':
                    $showEffect .= "
                                    jQuery(the_popup_".$ips_id.").{$add_cont_type}('body').hide().$effect($effect_duration);
                    ";
                    break;
                case 'show':
                    $showEffect .= "
                                    jQuery(the_popup_".$ips_id.").{$add_cont_type}('body').hide().show($effect_duration);
                    ";
                break;
                case 'vertical':
                    $showEffect .= "
                                var autocenter = {$meta_arr['auto_center']};
                                var position = '{$meta_arr['position']}';
                                jQuery(the_popup_".$ips_id.").{$add_cont_type}('body');
                                if(jQuery('#ips_main_".$ips_id."').css('top')!='auto'){
                                    var vertical = 'top';
                                    var v_init = parseInt(jQuery('#ips_main_".$ips_id."').css('top'));
                                }
                                else{
                                     var vertical = 'bottom';
                                     var v_init = parseInt(jQuery('#ips_main_".$ips_id."').css('bottom'));
                                }
                                    var e_width = jQuery('#ips_main_".$ips_id."').css('width');
                                    var e_height = jQuery('#ips_main_".$ips_id."').css('height');
                                    if(e_width.indexOf('px')>-1) var elm_width = parseInt(e_width);
                                    else var elm_width = (jQuery(window).width() * parseInt(e_width)) / 100;
                                    if(e_height.indexOf('px')>-1) var elm_height = parseInt(e_height);
                                    else var elm_height = jQuery(window).height(); //in this case height is 0
                                if(autocenter==1 && position=='relative'){
                                    v_start = v_init - ((jQuery(window).height() - elm_height)/2 + elm_height);
                                }else v_start = v_init - (v_init+elm_height);
                                var arr_start = {};
                                arr_start[vertical] = v_start+'px';
                                var arr_end = {};
                                arr_end[vertical] = v_init+'px';
                                jQuery('#ips_main_".$ips_id."').css(arr_start);
                                jQuery('#ips_main_".$ips_id."').animate(arr_end, $effect_duration);
                    ";
                break;
                case 'horizontal':
                    $showEffect .= "
                                var autocenter = {$meta_arr['auto_center']};
                                var position = '{$meta_arr['position']}';
                                 jQuery(the_popup_".$ips_id.").{$add_cont_type}('body');
                                if(jQuery('#ips_main_".$ips_id."').css('left')!='auto'){
                                     var horizontal = 'left';
                                     var h_init = parseInt(jQuery('#ips_main_".$ips_id."').css('left'));
                                }else{
                                     var horizontal = 'right';
                                     var h_init = parseInt(jQuery('#ips_main_".$ips_id."').css('right'));
                                }
                                    var e_width = jQuery('#ips_main_".$ips_id."').css('width');
                                    //var e_height = jQuery('#ips_main_".$ips_id."').css('height');
                                    if(e_width.indexOf('px')>-1) var elm_width = parseInt(e_width);
                                    else var elm_width = (jQuery(window).width() * parseInt(e_width)) / 100;
                                    if(e_width.indexOf('px')>-1) var elm_width = parseInt(e_width);
                                    else var elm_width = jQuery(window).width(); //in this case height is 0
                                if(autocenter==1 && position=='relative'){
                                    h_start = h_init - ((jQuery(window).width() - elm_width)/2 + elm_width);
                                }else h_start = h_init - (h_init+elm_width);
                                var arr_start = {};
                                arr_start[horizontal] = h_start+'px';
                                var arr_end = {};
                                arr_end[horizontal] = h_init+'px';
								jQuery('#ips_main_".$ips_id."').css(arr_start);
                                jQuery('#ips_main_".$ips_id."').animate(arr_end, $effect_duration);
                    ";
                break;
                case 'corner':
                    $showEffect .= "
                                var autocenter = {$meta_arr['auto_center']};
                                var position = '{$meta_arr['position']}';
                                jQuery(the_popup_".$ips_id.").{$add_cont_type}('body');
                                if(jQuery('#ips_main_".$ips_id."').css('top')!='auto'){
                                    var horizontal = 'top';
                                    var h_init = parseInt(jQuery('#ips_main_".$ips_id."').css('top'));
                                }else{
                                     var horizontal = 'bottom';
                                     var h_init = parseInt(jQuery('#ips_main_".$ips_id."').css('bottom'));
                                }
                                if(jQuery('#ips_main_".$ips_id."').css('left')!='auto'){
                                     var vertical = 'left';
                                     var v_init = parseInt(jQuery('#ips_main_".$ips_id."').css('left'));
                                }else{
                                     var vertical = 'right';
                                     var v_init = parseInt(jQuery('#ips_main_".$ips_id."').css('right'));
                                }
                                    var e_width = jQuery('#ips_main_".$ips_id."').css('width');
                                    var e_height = jQuery('#ips_main_".$ips_id."').css('height');
                                    if(e_width.indexOf('px')>-1) var elm_width = parseInt(e_width);
                                    else var elm_width = (jQuery(window).width() * parseInt(e_width)) / 100;
                                    if(e_height.indexOf('px')>-1) var elm_height = parseInt(e_height);
                                    else var elm_height = jQuery(window).height(); //in this case height is 0
                                if(autocenter==1 && position=='relative'){
                                    h_start = h_init - ((jQuery(window).width() - elm_width)/2 + elm_width);
                                    v_start = v_init - ((jQuery(window).height() - elm_height)/2 + elm_height);
                                }else {
                                    h_start = h_init - (h_init+elm_height);
                                    v_start = v_init - (v_init+elm_width);
                                }
                                var arr_start = {};
                                arr_start[vertical] = v_start+'px';
                                arr_start[horizontal] = h_start+'px';
                                var arr_end = {};
                                arr_end[vertical] = v_init+'px';
                                arr_end[horizontal] = h_init+'px';
                                jQuery('#ips_main_".$ips_id."').css(arr_start);
                                jQuery('#ips_main_".$ips_id."').animate(arr_end, $effect_duration);
                    ";
                break;
            }
        }
 /////////////////////////////////////////////////////////////////////////////       

	  if ($meta_arr['c_type'] == 'imgSlider') {
		  $slider_options = unserialize(base64_decode($meta_arr['slider_option']));
		  $navigation = (isset($slider_options['navigation'])) ? "navigation: ".$slider_options['navigation']."," : "";
		  $pagination = (isset($slider_options['pagination'])) ? "pagination: ".$slider_options['pagination']."," : "pagination: false,";
		  $slideSpeed = (isset($slider_options['slideSpeed'])) ? "slideSpeed: ".$slider_options['slideSpeed']."," : "";
		  $paginationSpeed = (isset($slider_options['paginationSpeed'])) ? "paginationSpeed: ".$slider_options['paginationSpeed']."," : "";
		  $autoPlay = (isset($slider_options['autoPlay'])) ? "autoPlay: ".$slider_options['paginationSpeed']."," : "";
          $stopOnHover = (isset($slider_options['stopOnHover'])) ? "stopOnHover: ".$slider_options['stopOnHover']."," : "stopOnHover:false,";
          if(isset($slider_options['css_transition']) && $slider_options['css_transition']!='none') $slider_effect = "'".$slider_options['css_transition']."'";
          else $slider_effect = 'false';
		  if (isset($slider_options['progressBar'])) {
		  	$showEffect .= "
		  	\n/* owl-carousel */
		  	\n jQuery(document).ready(function() {
			\n var time = ".($slider_options['paginationSpeed']/1000)."; // time in seconds
			 
			\n var \$progressBar,
			\n     \$bar,
			\n     \$elem, 
			\n     isPause, 
			\n     tick,
			\n     percentTime;
			
			jQuery(\"#slider_{$ips_id}\").owlCarousel({
		      $slideSpeed
		      $paginationSpeed
		      singleItem : true,
		      afterInit : progressBar,
		      afterMove : moved,
		      startDragging : pauseOnDragging,
              transitionStyle : $slider_effect,
		      $navigation
		      $pagination
		      $stopOnHover
		    });";
		  	if (isset($slider_options['stopOnHover'])) {
		  		$showEffect .= "
		  		jQuery(\"#slider_$ips_id\").on(\"mouseenter\", function() {
		  			isPause = true;
		  		});
		  		jQuery(\"#slider_$ips_id\").on(\"mouseleave\", function() {
		  			isPause = false;
		  		});";
		  		
		  	}
		  	
		    $showEffect .= "
		    function progressBar(elem){
		      \$elem = elem;
		      buildProgressBar();
		      start();
		    }
		    function buildProgressBar(){
		      \$progressBar = jQuery(\"<div>\",{
		        id:\"progressBar\"
		      });
		      \$bar = jQuery(\"<div>\",{
		        id:\"bar\"
		      });
		      \$progressBar.append(\$bar).prependTo(\$elem);
		    }
		    function start() {
		      percentTime = 0;
		      isPause = false;
		      tick = setInterval(interval, 10);
		    };
		    function interval() {
		      if(isPause === false){
		        percentTime += 1 / time;
		        \$bar.css({
		           width: percentTime+\"%\"
		         });
		        if(percentTime >= 100){ 
		          \$elem.trigger('owl.next')
		        }
		      }
		    }
		    
		    function pauseOnDragging(){
		      isPause = true;
		    }
		 
		    function moved(){
		      clearTimeout(tick);
		      start();
		    }
		    
		    });
		  	\n/* owl-carousel */
		  	";
		  	
		  } else {
		  	$showEffect .= "\n/* owl-carousel */
		  	\n
		  	\n
		  	\njQuery(document).ready(function() {
		  	\n
		  	\njQuery(\"#slider_$ips_id\").owlCarousel({
		  	\n	$navigation
		  	\n	$slideSpeed
		  	\n	$pagination
            \n  $stopOnHover
		  	\n	$paginationSpeed
		  	\n	$autoPlay
		  	\n	singleItem: true,
		  	\n	rewindNav: true,
            \n  transitionStyle : $slider_effect,
		  	\n});
		  	\n});
		  	\n
		  	\n /* owl-carousel */";
		  }
	  }elseif($meta_arr['c_type'] == 'google_maps'){
	    ////////////////////GOOGLE MAPS
            global $g_maps_id;;
            $showEffect .= "
                            	function LoadGmaps() {
                            		var myLatlng = new google.maps.LatLng(".$meta_arr['google_latlgt'].");
                            		var myOptions = {
                            			zoom: ".$meta_arr['google_zoom'].",
                            			center: myLatlng,
                            			disableDefaultUI: true,
                            			panControl: true,
                            			zoomControl: true,
                            			zoomControlOptions: {
                            				style: google.maps.ZoomControlStyle.DEFAULT
                            			},
                            			mapTypeControl: true,
                            			mapTypeControlOptions: {
                            				style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                            			},
                            			streetViewControl: true,
                            			mapTypeId: google.maps.MapTypeId.".$meta_arr['google_map_type']."
                            		}
                            		var map = new google.maps.Map(document.getElementById(\"$g_maps_id\"), myOptions);
                            		var marker = new google.maps.Marker({
                            			position: myLatlng,
                            			map: map,
                            			title:\"".$meta_arr['google_maker_label']."\"
                            		}); ";
            if($meta_arr['google_info_active']=='yes'){
                $showEffect .= "     		var infowindow = new google.maps.InfoWindow({
                            			content: \"".$meta_arr['google_info_content']."\"
                            		});";
                if($meta_arr['google_info_default_open']=='no'){
                    $showEffect .= " google.maps.event.addListener(marker, 'click', function() {
                          				infowindow.open(map, marker);
                                		});";
                    }else{
                        $showEffect .= "infowindow.open(map, marker);";
                    }
            }
                 $showEffect .= "
                            	}
                                jQuery(document).ready(function(){
                                    LoadGmaps();
                                });
                            ";
	  }elseif($meta_arr['c_type']=='fb_likebox'){
	    //FACEBOOK LIKEBOX
            $showEffect .= "/*
                                (function(d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (d.getElementById(id)) return;
                                js = d.createElement(s);
                                js.id = id;
                                js.src = \"//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.0&status=0\";
                                fjs.parentNode.insertBefore(js, fjs);
                                }
                                (document, 'script', 'facebook-jssdk'));
                             */
                             ips_load_facebook();
            				";
	  }

  ////////////////////////////////////////////////////////////////////////      
        $showEffect .= "        
        /* after */		
        i++;		
        if(i < 1) window.setTimeout(appendSomeItems, {$effect_duration});								
  }		
        appendSomeItems();	
	   		/////////////// SET FORM ID
	   		jQuery(document).ready(function(){
	   			ips_update_form_id({$ips_id});
	   			ips_subscribe_check('{$meta_arr['c_type']}', '{$meta_arr['ips_subscribe_type']}', {$ips_id});
  			});
	   		
	   		jQuery('#isp_form_{$ips_id}').submit(function(){
					if( jQuery('.ips_error_addr_mail') ) jQuery('.ips_error_addr_mail').remove(); 
        			var theForm = jQuery(this);
        			if( theForm.attr('action')==undefined || theForm.attr('action') == '#' ){
        			var submit = jQuery(this).find(':submit');
        			var fields = jQuery(this).find(':input');
        			jQuery(submit).removeAttr('name');
        			
					jQuery(\"#isp_form_{$ips_id} :input\").each(function(){
						jQuery(this).removeClass('red-border');	
					});
  						
        			submit.after('<img class=\"isp_spinner\" src=\"".ISP_DIR_URL."assets/img/wpspin_light.gif\" />');
        			submit.attr('disabled','disabled');
          	  		data = {
        	  					action: 'isp_submit_form',
        	  					form_data : jQuery(this).serialize(),
         	  					ips_id : {$ips_id}
  	  						};
  	  			  		jQuery.post(ajaxobject.ajaxurl, data, function(response){
  	  			  			submit = theForm.find(':submit');submit.removeAttr('disabled');
  	  			  			spinner = theForm.find('.isp_spinner');spinner.hide();
  	  			  			
  	  			  			obj = ips_return_json_values(response);
  	  			  			if(obj){
  	  			  				for(item in obj){
  	  			  					input = document.getElementsByName(obj[item]);
  	  			  					jQuery(input).addClass('red-border');
  	  			  				}
  	  			  			}
							response = parseInt(response);
  	  			  			if(response==1) close_popup_{$ips_id}();
  	  			  			else{
  	  			  				//data not save
  	  			  				ips_return_error_msg('{$meta_arr['c_type']}', {$ips_id}, '{$meta_arr['opt_in_err_msg']}');
  							}
		  	  	  		});	  		
		  	  		return false;		  
		  	  		}
		  	  		return true;
					
  			});//end of form submit		  
  }, {$delay});	 	 ";
  }else{
///////////////////////////////////////////////////WITHOUT EFFECT
	/************* on load *************/
  	
	  $showEffect = "
	  var i=0;
	  setTimeout (function () {
	  	function appendSomeItems() {
	  		jQuery(the_popup_{$ips_id}).{$add_cont_type}('body');
	  		i++;
	  		if (i < 1) window.setTimeout(appendSomeItems, 0);
  		}	
	  appendSomeItems();";
	  if ($meta_arr['c_type'] == 'imgSlider') {
		  $slider_options = unserialize(base64_decode($meta_arr['slider_option']));
		  $navigation = (isset($slider_options['navigation'])) ? "navigation: ".$slider_options['navigation']."," : "";
		  $pagination = (isset($slider_options['pagination'])) ? "pagination: ".$slider_options['pagination']."," : "pagination: false,";
		  $slideSpeed = (isset($slider_options['slideSpeed'])) ? "slideSpeed: ".$slider_options['slideSpeed']."," : "";
		  $paginationSpeed = (isset($slider_options['paginationSpeed'])) ? "paginationSpeed: ".$slider_options['paginationSpeed']."," : "";
		  $autoPlay = (isset($slider_options['autoPlay'])) ? "autoPlay: ".$slider_options['paginationSpeed']."," : "";
          $stopOnHover = (isset($slider_options['stopOnHover'])) ? "stopOnHover: ".$slider_options['stopOnHover']."," : "stopOnHover:false,";
          if(isset($slider_options['css_transition']) && $slider_options['css_transition']!='none') $slider_effect = "'".$slider_options['css_transition']."'";
          else $slider_effect = 'false';
		  if (isset($slider_options['progressBar'])) {
		  	$showEffect .= "
		  	\n/* owl-carousel */
		  	\n jQuery(document).ready(function() {
			\n var time = ".($slider_options['paginationSpeed']/1000)."; // time in seconds
			 
			\n var \$progressBar,
			\n     \$bar,
			\n     \$elem, 
			\n     isPause, 
			\n     tick,
			\n     percentTime;
			
			jQuery(\"#slider_$ips_id\").owlCarousel({
		      $slideSpeed
		      $paginationSpeed
		      singleItem : true,
		      afterInit : progressBar,
		      afterMove : moved,
		      startDragging : pauseOnDragging,
              transitionStyle : $slider_effect,
		      $navigation
		      $pagination
		      $stopOnHover
		    });";
		  	if (isset($slider_options['stopOnHover'])) {
		  		$showEffect .= "
		  		jQuery(\"#slider_$ips_id\").on(\"mouseenter\", function() {
		  			isPause = true;
		  		});
		  		jQuery(\"#slider_$ips_id\").on(\"mouseleave\", function() {
		  			isPause = false;
		  		});";
		  		
		  	}
		  	
		    $showEffect .= "
		    function progressBar(elem){
		      \$elem = elem;
		      buildProgressBar();
		      start();
		    }
		    function buildProgressBar(){
		      \$progressBar = jQuery(\"<div>\",{
		        id:\"progressBar\"
		      });
		      \$bar = jQuery(\"<div>\",{
		        id:\"bar\"
		      });
		      \$progressBar.append(\$bar).prependTo(\$elem);
		    }
		    function start() {
		      percentTime = 0;
		      isPause = false;
		      tick = setInterval(interval, 10);
		    };
		    function interval() {
		      if(isPause === false){
		        percentTime += 1 / time;
		        \$bar.css({
		           width: percentTime+\"%\"
		         });
		        if(percentTime >= 100){ 
		          \$elem.trigger('owl.next')
		        }
		      }
		    }
		    
		    function pauseOnDragging(){
		      isPause = true;
		    }
		 
		    function moved(){
		      clearTimeout(tick);
		      start();
		    }
		    
		    });
		  	\n/* owl-carousel */
		  	";
		  	
		  } else {
		  	$showEffect .= "\n/* owl-carousel */
		  	\n
		  	\n
		  	\njQuery(document).ready(function() {
		  	\n
		  	\njQuery(\"#slider_$ips_id\").owlCarousel({
		  	\n	$navigation
		  	\n	$slideSpeed
		  	\n	$pagination
            \n  $stopOnHover
		  	\n	$paginationSpeed
		  	\n	$autoPlay
		  	\n	singleItem: true,
		  	\n	rewindNav: true,
            \n  transitionStyle : $slider_effect,
		  	\n});
		  	\n});
		  	\n
		  	\n /* owl-carousel */";
		  }
	  }elseif($meta_arr['c_type'] == 'google_maps'){
	    ////////////////////GOOGLE MAPS
            global $g_maps_id;;
            $showEffect .= "
                            	function LoadGmaps() {
                            		var myLatlng = new google.maps.LatLng(".$meta_arr['google_latlgt'].");
                            		var myOptions = {
                            			zoom: ".$meta_arr['google_zoom'].",
                            			center: myLatlng,
                            			disableDefaultUI: true,
                            			panControl: true,
                            			zoomControl: true,
                            			zoomControlOptions: {
                            				style: google.maps.ZoomControlStyle.DEFAULT
                            			},
                            			mapTypeControl: true,
                            			mapTypeControlOptions: {
                            				style: google.maps.MapTypeControlStyle.HORIZONTAL_BAR
                            			},
                            			streetViewControl: true,
                            			mapTypeId: google.maps.MapTypeId.".$meta_arr['google_map_type']."
                            		}
                            		var map = new google.maps.Map(document.getElementById(\"$g_maps_id\"), myOptions);
                            		var marker = new google.maps.Marker({
                            			position: myLatlng,
                            			map: map,
                            			title:\"".$meta_arr['google_maker_label']."\"
                            		}); ";
            if($meta_arr['google_info_active']=='yes'){
                $showEffect .= "     		var infowindow = new google.maps.InfoWindow({
                            			content: \"".$meta_arr['google_info_content']."\"
                            		});";
                if($meta_arr['google_info_default_open']=='no'){
                    $showEffect .= " google.maps.event.addListener(marker, 'click', function() {
                          				infowindow.open(map, marker);
                                		});";
                    }else{
                        $showEffect .= "infowindow.open(map, marker);";
                    }
            }
                 $showEffect .= "
                            	}
                                jQuery(document).ready(function(){
                                    LoadGmaps();
                                });
                            ";
	  }elseif($meta_arr['c_type']=='fb_likebox'){
	    //FACEBOOK LIKEBOX
            $showEffect .= "/*
                                (function(d, s, id) {
                                var js, fjs = d.getElementsByTagName(s)[0];
                                if (d.getElementById(id)) return;
                                js = d.createElement(s);
                                js.id = id;
                                js.src = \"//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.0&status=0\";
                                fjs.parentNode.insertBefore(js, fjs);
                                }
                                (document, 'script', 'facebook-jssdk'));
                             */
                             ips_load_facebook();
            				";
	  }
	   $showEffect .= "
	   		/////////////// SET FORM ID
	   		jQuery(document).ready(function(){
	   			ips_update_form_id({$ips_id});
	   			ips_subscribe_check('{$meta_arr['c_type']}', '{$meta_arr['ips_subscribe_type']}', {$ips_id});
  			});
	   		
	   		jQuery('#isp_form_{$ips_id}').submit(function(){
					if( jQuery('.ips_error_addr_mail') ) jQuery('.ips_error_addr_mail').remove(); 
        			var theForm = jQuery(this);
        			if( theForm.attr('action')==undefined || theForm.attr('action') == '#' ){
        			var submit = jQuery(this).find(':submit');
        			var fields = jQuery(this).find(':input');
        			//remove submit name 
        			jQuery(submit).removeAttr('name');
        			
					jQuery(\"#isp_form_{$ips_id} :input\").each(function(){
						jQuery(this).removeClass('red-border');	
					});
  						
        			submit.after('<img class=\"isp_spinner\" src=\"".ISP_DIR_URL."assets/img/wpspin_light.gif\" />');
        			submit.attr('disabled','disabled');
          	  		data = {
        	  					action: 'isp_submit_form',
        	  					form_data : jQuery(this).serialize(),
         	  					ips_id : {$ips_id}
  	  						};
  	  			  		jQuery.post(ajaxobject.ajaxurl, data, function(response){
  	  			  			submit = theForm.find(':submit');submit.removeAttr('disabled');
  	  			  			spinner = theForm.find('.isp_spinner');spinner.hide();
  	  			  			
  	  			  			obj = ips_return_json_values(response);
  	  			  			if(obj){
  	  			  				for(item in obj){
  	  			  					input = document.getElementsByName(obj[item]);
  	  			  					jQuery(input).addClass('red-border');
  	  			  				}
  	  			  			}
  	  			  			response = parseInt(response);
  	  			  			if(response==1) close_popup_{$ips_id}();
  	  			  			else{
  	  			  				//data not save
  	  			  				ips_return_error_msg('{$meta_arr['c_type']}', {$ips_id}, '{$meta_arr['opt_in_err_msg']}');
  							}
		  	  	  		});	  		
		  	  		return false;		  
		  	  		}
		  	  		return true;
					
  			});//end of form submit		  
  }, {$delay});	 	 ";
  }
  
  if($duration > 0){
	$autoClose = "setTimeout(
			function(){
				close_popup_{$ips_id}();
				}, {$duration});
	";   
	}

	//INCREMENT VIEWS cookie, save statistic via ajax
	$showEffect .= "
		visit_views_cookie_val = getCookie('visit_views[".$ips_id."]');
		visit_views_cookie_val++;
		setCookie('visit_views[".$ips_id."]', visit_views_cookie_val, 365);
		//statistics
		isp_save_statistic_data_js(".$ips_id.");
	";
	
  switch($meta_arr['open_event']){
	case 'default':
		$event = "
		
		jQuery( document ).ready(function(){
		
				 	//check visit_views
				 	max_show_cookie_val = getCookie('visit_views[".$ips_id."]');
					if(typeof max_show_cookie_val!='undefined' && (max_show_cookie_val>={$meta_arr['max_show_session']} || max_show_cookie_val < 0) ) return;
		
			".$showEffect."
			".$autoClose."
		});
		";
	break;
	case 'exit':
		$event = "
		
		jQuery(window).on('beforeunload', function() {
		
				 	//check visit_views
					
				 	max_show_cookie_val = getCookie('visit_views[".$ips_id."]');
					if(typeof max_show_cookie_val!='undefined' && (max_show_cookie_val>={$meta_arr['max_show_session']} || max_show_cookie_val < 0) ) return;
		
			".$showEffect."
			".$autoClose."
			
			return {$exit_mess};
		   
		});
		";
	break;
	case 'leave':
		$event = "
		var stop_croll = 0;
		 jQuery( document ).ready(function(){
			jQuery(document).bind('mouseleave',function(e){
				var posPoint = jQuery(window).width() - e.pageX;
				if(posPoint > 20 && stop_croll == 0){ 
				 if(jQuery(the_popup_{$ips_id}).length > 0 ){	
				 	//check visit_views
				 	max_show_cookie_val = getCookie('visit_views[".$ips_id."]');
					if(typeof max_show_cookie_val!='undefined' && (max_show_cookie_val>={$meta_arr['max_show_session']} || max_show_cookie_val < 0) ) return;
				 
				   stop_croll = 1;	 
				   ".$showEffect."
				   ".$autoClose."
				 }
				}
			});
	
		});
		";
	break;
	case 'click_on_page':
		$event = "
			jQuery(document).click(function(){
				if(window.cop_".$ips_id."!=undefined && window.cop_".$ips_id."==1) return;
				else{
					window.cop_".$ips_id." = 1;
				 	//check visit_views
				 	max_show_cookie_val = getCookie('visit_views[".$ips_id."]');
					if(typeof max_show_cookie_val!='undefined' && (max_show_cookie_val>={$meta_arr['max_show_session']} || max_show_cookie_val < 0) ) return;
				
					".$showEffect."
					".$autoClose."  
				
  				}									
  			});
		";
	break;
	case 'custom':
		$event = "
		
		var stop_custom_event = 0;
		jQuery( document ).ready(function(){
			if('{$event_type}' != '' && '{$event_name}' != '' && '{$event_action}' != ''){
				jQuery('{$event_type}{$event_name}').{$event_action}(function(){
				 if(stop_custom_event == 0){
				 
				 	//check visit_views
				 	max_show_cookie_val = getCookie('visit_views[".$ips_id."]');
					if(typeof max_show_cookie_val!='undefined' && (max_show_cookie_val>={$meta_arr['max_show_session']} || max_show_cookie_val < 0) ) return;
				  
				  stop_custom_event = 1;
					".$showEffect."
					".$autoClose."
				 }
				});
			}
		 });
		";
	break;
	case 'scroll':
		$event = "
		
		var display_once = 0;
		jQuery(document).scroll(function(){
							
			if({$scroll_position} == 'bottom'){
				if( jQuery(window).scrollTop() + jQuery(window).height() != jQuery(document).height() || display_once == 1 ) return;

					//check visit_views
					max_show_cookie_val = getCookie('visit_views[".$ips_id."]');
					if(typeof max_show_cookie_val!='undefined' && (max_show_cookie_val>={$meta_arr['max_show_session']} || max_show_cookie_val < 0) ) return;
					
					display_once = 1;
					".$showEffect."
					".$autoClose."
			}else{
				if(jQuery(window).scrollTop() != 0 || display_once == 1) return;
					
					//check visit_views
					max_show_cookie_val = getCookie('visit_views[".$ips_id."]');
					if(typeof max_show_cookie_val!='undefined' && (max_show_cookie_val>={$meta_arr['max_show_session']} || max_show_cookie_val < 0) ) return;
				
					display_once = 1;
					".$showEffect."
					".$autoClose."
			}
		});
		";
	break;
  }
  $event .= '';
  
  return $event;
}

function ips_check_item_disabled($id){
	@$ips_items_status = get_option('ips_items_status');
	if(isset($ips_items_status[$id]) && $ips_items_status[$id]=='inactive') return TRUE;
	return FALSE;
}

function ips_is_shortcode_page($page_id){
	global $wpdb;
	$obj = $wpdb->get_results("SELECT meta_value
			FROM {$wpdb->prefix}popup_meta
			WHERE meta_name='custom_page_shortcode_id'");
	if(isset($obj) && count($obj)>0){
		foreach($obj as $id){
			if(isset($id->meta_value) && $id->meta_value==$page_id) return TRUE;
		}
	}
	return FALSE;
}
?>