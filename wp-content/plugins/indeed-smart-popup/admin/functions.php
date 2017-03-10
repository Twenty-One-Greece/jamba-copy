<?php 
///////////CREATE TABLE AND UPDATE COUNTRY TABLE
function ips_create_meta_tables(){
	global $wpdb;
	require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
	// WP_POPUP_WINDOWS
	$table_name = $wpdb->prefix . "popup_windows";
	if ($wpdb->get_var ( "show tables like '$table_name'" ) != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
		id int(9) NOT NULL AUTO_INCREMENT,
		name varchar(200),
		PRIMARY KEY (id)
		);";
		dbDelta ( $sql );
	}

	//WP_POPUP_META
	$table_name = $wpdb->prefix . "popup_meta";
	if ($wpdb->get_var ( "show tables like '$table_name'" ) != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
		id int(9) NOT NULL AUTO_INCREMENT,
		popup_id int(9),
		meta_name varchar(200),
		meta_value text,
		PRIMARY KEY (id)
		);";
		dbDelta ( $sql );
	}

	//WP_POPUP_VISITS
	$table_name = $wpdb->prefix . "popup_visits";
	if ($wpdb->get_var ( "show tables like '$table_name'" ) != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
		id int(9) NOT NULL AUTO_INCREMENT,
		popup_id int(9),
		visitor varchar(25) NOT NULL,
		visit TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		ref varchar(255),
		user_agent varchar(255),
		PRIMARY KEY (id)
		);";
		dbDelta ( $sql );
	}

	//WP_POPUP_COUNTRY_D
	$table_name = $wpdb->prefix . "popup_country_d";
	if ($wpdb->get_var ( "show tables like '$table_name'" ) != $table_name) {
		$sql = "CREATE TABLE " . $table_name . " (
		`ctry` varchar(2) NOT NULL DEFAULT '',
		`cntry` varchar(10) DEFAULT NULL,
		`country` varchar(150) DEFAULT NULL,
		PRIMARY KEY (`ctry`),
		KEY `cntry` (`cntry`)
		);";
		dbDelta ( $sql );
		$tbl_content = file_get_contents(ISP_DIR_PATH.'admin/countries.sql');
		$x = $wpdb->query("INSERT INTO {$wpdb->prefix}popup_country_d (`ctry`, `cntry`, `country`)
		VALUES $tbl_content");
	}

	//wp_popup_form_results
	$table_name = $wpdb->prefix . "popup_form_results";
	if ($wpdb->get_var ( "show tables like '$table_name'" ) != $table_name) {
		require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE " . $table_name . " (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`popup_id` int(11),
		`meta_name` varchar(255),
		`meta_value` varchar(255),
		`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`user_ip` varchar(255),
		PRIMARY KEY (`id`)
		);";
		dbDelta ( $sql );
	}
}

function ips_update_the_db(){
	global $wpdb;
	$exists = $wpdb->get_results("SHOW COLUMNS FROM `{$wpdb->prefix}popup_visits` LIKE 'ref'");
	if($exists==false){
		$wpdb->query("ALTER TABLE {$wpdb->prefix}popup_visits ADD ref VARCHAR(255)");
	}
	$exists = $wpdb->get_results("SHOW COLUMNS FROM `{$wpdb->prefix}popup_visits` LIKE 'user_agent'");//user_agent
	if($exists==false){
		$wpdb->query("ALTER TABLE {$wpdb->prefix}popup_visits ADD user_agent VARCHAR(255)");
	}
	$exists = $wpdb->get_results("SHOW COLUMNS FROM `{$wpdb->prefix}popup_visits` LIKE 'country'");//user_agent
	if($exists==false){
		$wpdb->query("ALTER TABLE {$wpdb->prefix}popup_visits ADD country VARCHAR(25)");
	}
	//WP_POPUP_COUNTRY_D
	$table_name = $wpdb->prefix . "popup_country_d";
	if ($wpdb->get_var ( "show tables like '$table_name'" ) != $table_name) {
		require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE " . $table_name . " (
		`ctry` varchar(2) NOT NULL DEFAULT '',
		`cntry` varchar(10) DEFAULT NULL,
		`country` varchar(150) DEFAULT NULL,
		PRIMARY KEY (`ctry`),
		KEY `cntry` (`cntry`)
		);";
		dbDelta ( $sql );
		$tbl_content = file_get_contents(ISP_DIR_PATH.'admin/countries.sql');
		if($tbl_content && $tbl_content!=''){
			$x = $wpdb->query("INSERT INTO {$wpdb->prefix}popup_country_d (`ctry`, `cntry`, `country`)
								VALUES $tbl_content");			
		}
	}
	//wp_popup_form_results
	$table_name = $wpdb->prefix . "popup_form_results";
	if ($wpdb->get_var ( "show tables like '$table_name'" ) != $table_name) {
		require_once (ABSPATH . 'wp-admin/includes/upgrade.php');
		$sql = "CREATE TABLE " . $table_name . " (
		`id` int(11) NOT NULL AUTO_INCREMENT,
		`popup_id` int(11),
		`meta_name` varchar(255),
		`meta_value` varchar(255),
		`timestamp` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
		`user_ip` varchar(255),
		PRIMARY KEY (`id`)
		);";
		dbDelta ( $sql );
	}
	
}

///////STATS FUNCTIONS
function getMapVisits($wpdb,$start_date,$end_date,$id){
	global $wpdb;
	$mapVisits = $wpdb->get_results("SELECT country, count(visit) as counts
										FROM {$wpdb->prefix}popup_visits
										WHERE {$wpdb->prefix}popup_visits.visit >= '$start_date' 
										AND {$wpdb->prefix}popup_visits.visit <= '$end_date' 
										AND popup_id = '$id' 
										group by {$wpdb->prefix}popup_visits.country asc");
	return $mapVisits;
}
function getDevices($visits){
	$counts = array ('Android' => 0, 'IOS' => 0, 'BlackBerry' => 0, 'Windows Phone' => 0, 'Computer' => 0);
	if(isset($visits) && count($visits)> 0){
		foreach($visits as $value){
			if(strstr($value->user_agent,'Android')) $counts['Android']++;
			elseif(strstr($value->user_agent,'iPhone') || strstr($value->user_agent,'iPad') ) $counts['IOS']++;
			elseif(strstr($value->user_agent,'BlackBerry')) $counts['BlackBerry']++;
			elseif(strstr($value->user_agent,'Windows Phone') ) $counts['Windows Phone']++;
			else{
				$counts['Computer']++;
			}
		}
	}
	return $counts;
}
function getBrowsers($visits){
	$counts = array ('Chrome' => 0, 'FireFox' => 0, 'Safari' => 0, 'Opera' => 0, 'Internet Explorer' => 0 , 'Other' => 0);
	if(isset($visits) && count($visits)> 0){
		foreach($visits as $value){
			if(preg_match('/MSIE/i',$value->user_agent) && !preg_match('/Opera/i',$value->user_agent)){
				$counts['Internet Explorer']++;
			}elseif(preg_match('/Firefox/i',$value->user_agent)){
				$counts['FireFox']++;
			}elseif(preg_match('/Chrome/i',$value->user_agent)) {
				$counts['Chrome']++;
			}elseif(preg_match('/Safari/i',$value->user_agent)){
				$counts['Safari']++;
			}elseif(preg_match('/Opera/i',$value->user_agent)){
				$counts['Opera']++;
			}else{
				$counts['Other']++;
			}
		}
	}
	return $counts;
}

////////////PREVIEW POPUP
function isp_preview_popup( $ips_id ){
	include ISP_DIR_PATH.'includes/front_end_functions.php';
	if( !ips_check_popup_exists( $ips_id ) ) return; //if popup doesn't exists return 
	$meta_arr = ips_get_metas( $ips_id );
	$string = '';
	$preview = 1;
	$device_type='';
	include ISP_DIR_PATH.'includes/ips_view.php';
	echo $string;
}


//////UTILITY
function ips_checkIfSelected($val1, $val2, $type){
	// check if val1 is equal with val2 and return an select attribute for checkbox, radio or select tag
	if( isset($val1) && isset($val2) && $val1 == $val2 ){
		if( $type=='checkbox' ) return 'checked="checked"';
		else return 'selected="selected"';
	}else return '';
}

////////METAS
function ips_update_metas(){
	global $wpdb;
	$wpdb->query( "UPDATE {$wpdb->prefix}popup_windows
						SET name = '{$_REQUEST['p_name']}'
						WHERE id = {$_REQUEST['popup_id']};"
				);
	if(isset($_REQUEST['ips_meta']) && count($_REQUEST['ips_meta'])>0){
		@$_REQUEST['ips_meta']['custom_page_shortcode_id'] = ips_check_shortcode_type($_REQUEST['ips_meta']['c_type'], $_REQUEST['popup_id'], @$_REQUEST['ips_meta']['shortcode'], @$_REQUEST['ips_meta']['custom_page_shortcode_id']);	
		$timesonze_set = 0;

		//opt in
		$_REQUEST['ips_meta']['opt_in_content'] = str_replace("\r\n","",$_REQUEST['ips_meta']['opt_in_content']);
		$_REQUEST['ips_meta']['opt_in_content'] = addslashes($_REQUEST['ips_meta']['opt_in_content']);
		
		foreach($_REQUEST['ips_meta'] as $meta_name=>$meta_value){
			if($meta_name=="timezone"){
				$meta_value = serialize($meta_value);
				$timesonze_set = 1;
			}elseif($meta_name=="google_info_content"){
				$meta_value = strip_tags($meta_value);
			}
			elseif(is_array($meta_value)){
				$v = base64_encode(serialize($meta_value));
				$meta_value = $v;
				unset($v);
			}
			$exist = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}popup_meta
										WHERE  meta_name = '$meta_name'
										AND popup_id = {$_REQUEST['popup_id']};");
			if(isset($exist) && $exist > 0){
				$wpdb->query("UPDATE {$wpdb->prefix}popup_meta
								SET meta_value = '$meta_value'
								WHERE meta_name = '$meta_name'
								AND popup_id = {$_REQUEST['popup_id']};"
							);
			}else{
					$wpdb->query("INSERT INTO {$wpdb->prefix}popup_meta 
									VALUES(null, {$_REQUEST['popup_id']}, '$meta_name', '$meta_value');");
			}
			unset($meta_name);
			unset($meta_value);
			unset($exist);
		}
	//HTML CK FIELD
	if(isset($_REQUEST['ck'])){
		$h_c = $_REQUEST['ck'];
		unset($_REQUEST['ck']);
		$h_c = str_replace("\r\n","",$h_c);
		$h_c = addslashes($h_c);
		$exist = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}popup_meta
									WHERE  meta_name = 'html_content'
									AND popup_id = {$_REQUEST['popup_id']};");
		if(isset($exist) && $exist > 0){
			$wpdb->query("UPDATE {$wpdb->prefix}popup_meta
							SET meta_value = '$h_c'
							WHERE meta_name = 'html_content'
							AND popup_id = {$_REQUEST['popup_id']};");
		}else{
			$wpdb->query("INSERT INTO {$wpdb->prefix}popup_meta 
							VALUES(null, {$_REQUEST['popup_id']}, 'html_content', '$h_c');");
		}
		unset($exist);
	}
	if($timesonze_set==0){
			$meta_name = 'timezone';
			$meta_value = '';
			$exist = $wpdb->get_var("SELECT COUNT(*) FROM {$wpdb->prefix}popup_meta
										WHERE meta_name = '$meta_name'
										AND popup_id = {$_REQUEST['popup_id']};");
		if(isset($exist) && $exist > 0){
			$wpdb->query("UPDATE  {$wpdb->prefix}popup_meta
							SET meta_value = '$meta_value'
							WHERE meta_name = '$meta_name'
							AND popup_id = {$_REQUEST['popup_id']};"
						);
		}else{
			$wpdb->query("INSERT INTO {$wpdb->prefix}popup_meta 
							VALUES(null, {$_REQUEST['popup_id']}, '$meta_name', '$meta_value');");
		}
		unset($exist);
	}
	unset($_REQUEST['ips_meta']);
	}	
}

function deletePopUp($the_id){
	global $wpdb;
	/////DELETE SHORCODE PAGE
	$page_id = ips_return_shortcode_content_type_id_page($the_id);
	if(isset($page_id) && $page_id!=''){
		wp_delete_post( $page_id, true );
	}
	$wpdb->query("DELETE FROM {$wpdb->prefix}popup_windows WHERE id=$the_id");
	$wpdb->query("DELETE FROM {$wpdb->prefix}popup_meta WHERE popup_id=$the_id");
	$wpdb->query("DELETE FROM {$wpdb->prefix}popup_visits WHERE popup_id=$the_id");
	$wpdb->query("DELETE FROM {$wpdb->prefix}popup_form_results WHERE popup_id=$the_id");
}

function ips_return_shortcode_content_type_id_page($ips_id){
	global $wpdb;
	@$id = $wpdb->get_var("SELECT meta_value
									FROM {$wpdb->prefix}popup_meta
									WHERE {$wpdb->prefix}popup_meta.popup_id = {$ips_id}
									AND {$wpdb->prefix}popup_meta.meta_name='custom_page_shortcode_id';");
	if(isset($id) && $id!='') return $id;
	else return '';
}

function isp_duplicate_popup($id){
	global $wpdb;
	$the_popup_name = ips_return_popup_name($id);

	if(strpos($the_popup_name,'(Duplicate')===FALSE) $the_popup_name .= " (Duplicate)";
	else{
		if(strpos($the_popup_name,'(Duplicate)')!==FALSE){
			$popup_name_arr = explode('(Duplicate)', $the_popup_name);
			$the_popup_name = $popup_name_arr[0]." (Duplicate 2)";
		}else{
			$popup_name_arr = explode('(Duplicate ', $the_popup_name);
			if(isset($popup_name_arr[1])){
				$num = intval($popup_name_arr[1]);
				$num += 1;
				$the_popup_name = $popup_name_arr[0]." (Duplicate $num)";
			}
		}
	}
	
	$wpdb->query("INSERT INTO {$wpdb->prefix}popup_windows VALUES(null, '$the_popup_name');");
	$new_id = $wpdb->insert_id;
	$meta_arr = ips_get_metas( $id );
	@$meta_arr['custom_page_shortcode_id'] = ips_check_shortcode_type($meta_arr['c_type'], $new_id, @$meta_arr['shortcode'], '');
	if(isset($meta_arr) && count($meta_arr)>0){
		foreach($meta_arr as $meta_name=>$meta_value){
			$wpdb->query("INSERT INTO {$wpdb->prefix}popup_meta VALUES(null, $new_id, '{$meta_name}', '{$meta_value}');");
		}		
	}
}

function ips_return_utc(){
	$arr = array(
			"-12" => "Baker Island Time",
			"-11" => "Pacific/Midway",
			"-10" => "Pacific/Honolulu",
			"-9" => "US/Alaska",
			"-8" => "America/Los Angeles",
			"-7" => "US/Arizona",
			"-6" => "America/Mexico City, America/Managua, US/Central",
			"-5" => "America/Bogota, US/Eastern, America/Lima",
			"-4" => "Canada/Atlantic, America/Santiago",
			"-3" => "America/Sao_Paulo, America/Argentina/Buenos_Aires",
			"-2" => "America/Noronha",
			"-1" => "Atlantic/Azores, Atlantic/Cape_Verde",
			"0" => "Africa/Casablanca, Europe/London, Europe/Lisbon, UTC",
			"+1" => "Europe/Amsterdam, Europe/Belgrade, Europe/Berlin, Europe/Rome",
			"+2" => "Europe/Bucharest, Europe/Athens, Africa/Cairo, Asia/Jerusalem",
			"+3" => "Asia/Baghdad, Asia/Kuwait, Europe/Minsk, Africa/Nairobi",
			"+4" => "Europe/Moscow, Asia/Muscat, Asia/Tbilisi",
			"+5" => "Asia/Karachi, Asia/Karachi",
			"+6" => "Asia/Dhaka",
			"+7" => "Asia/Bangkok, Asia/Jakarta",
			"+8" => "Asia/Hong_Kong, Asia/Singapore, Asia/Taipei",
			"+9" => "Asia/Tokyo, Asia/Seoul",
			"+10" => "Australia/Melbourne, Australia/Sydney",
			"+11" => "Asia/Vladivostok",
			"+12" => "Pacific/Fiji"
	);
	return $arr;
}

function ips_get_current_p_id(){
	//get last id from db, increment and return for current popup
	global $wpdb;
	$last_id = $wpdb->get_results("SELECT id FROM {$wpdb->prefix}popup_windows
										ORDER BY id DESC LIMIT 1;");
	if(isset($last_id[0]->id)) {
		$the_id = $last_id[0]->id;
		$the_id++;
	}else $the_id = 1;	
	return $the_id;
}

function ips_save_metas(){
	global $wpdb;
	if(isset($_REQUEST['p_name'])){
		//if(isset($_REQUEST['popup_id'])) $popup_id = $_REQUEST['popup_id'];
		$wpdb->query("INSERT INTO {$wpdb->prefix}popup_windows VALUES({$_REQUEST['popup_id']}, '{$_REQUEST['p_name']}');");
	}
	if(isset($_REQUEST['ips_meta']) && count($_REQUEST['ips_meta'])>0){
		@$_REQUEST['ips_meta']['custom_page_shortcode_id'] = ips_check_shortcode_type($_REQUEST['ips_meta']['c_type'], $_REQUEST['popup_id'], @$_REQUEST['ips_meta']['shortcode'], @$_REQUEST['ips_meta']['custom_page_shortcode_id']);
		$timezone = 0;
		//opt in
		$_REQUEST['ips_meta']['opt_in_content'] = str_replace("\r\n","",$_REQUEST['ips_meta']['opt_in_content']);
		$_REQUEST['ips_meta']['opt_in_content'] = addslashes($_REQUEST['ips_meta']['opt_in_content']);
		
		foreach($_REQUEST['ips_meta'] as $meta_name => $meta_value){
			if($meta_name=="timezone"){
				$meta_value = serialize($meta_value);
				$timesonze_set = 1;
			}elseif($meta_name=="google_info_content"){
				$meta_value = strip_tags($meta_value);
			}elseif(is_array($meta_value)){
				$v = base64_encode(serialize($meta_value));
				$meta_value = $v;
				unset($v);
			}
			$wpdb->query("INSERT INTO {$wpdb->prefix}popup_meta VALUES(null, {$_REQUEST['popup_id']}, '$meta_name', '$meta_value');");
		}
		unset($_REQUEST['ips_meta']);
		//HTML CK FIELD
		if(isset($_REQUEST['ck'])){
			$h_c = $_REQUEST['ck'];
			unset($_REQUEST['ck']);
			$h_c = str_replace("\r\n","",$h_c);
			$h_c = addslashes($h_c);
			$wpdb->query("INSERT INTO {$wpdb->prefix}popup_meta VALUES(null, {$_REQUEST['popup_id']}, 'html_content', '$h_c');");
		}
		if($timezone==0){
			$meta_name = 'timezone';
			$meta_value = '';
			$wpdb->query("INSERT INTO {$wpdb->prefix}popup_meta VALUES(null, {$_REQUEST['popup_id']}, '$meta_name', '$meta_value');");
		}
	}	
}

function ips_check_shortcode_type($content_type, $ips_id, $shortcode_content, $shortcode_page_id){
	/////function to check if c_type is 'shortcode', it adds a page with our template
	global $wpdb;	
	if($content_type=='shortcode'){
		if(isset($shortcode_page_id) && $shortcode_page_id!=''){
			//page probably exists
			$post_title = $wpdb->get_var("SELECT post_title FROM {$wpdb->posts} WHERE ID={$shortcode_page_id};");
			if(!isset($post_title) || $post_title==FALSE){
				//CREATE POST
				$page_meta = array(
						'post_type' => 'isp_s_post_type',//'post_type'	   => 'page',
						'post_title'   => 'isp_page_'.$ips_id,
						'post_status'  => 'publish',
						'page_template'=> ISP_DIR_PATH.'includes/isp_template.php',
						'post_content' => $shortcode_content,
				);
				$shortcode_page_id = wp_insert_post($page_meta, false);				
			}else{
				//UPDATE PAGE
				$page_meta = array(
						'ID'           => $shortcode_page_id,
						'post_content' => $shortcode_content
				);
				$shortcode_page_id = wp_update_post( $page_meta );			
			}					
		}else{
			//CREATE POST
			$page_meta = array(
					'post_type' => 'isp_s_post_type',//'post_type'	   => 'page',
					'post_title'   => 'isp_page_'.$ips_id,
					'post_status'  => 'publish',
					'page_template'=> ISP_DIR_PATH.'includes/isp_template.php',
					'post_content' => $shortcode_content,
			);
			$shortcode_page_id = wp_insert_post($page_meta, false);		
		}
		return $shortcode_page_id;
	}else return '';
}

function ips_get_shortcode_content_page_custom( $ips_id ){
	global $wpdb;
	@$content = $wpdb->get_var("SELECT post_content FROM {$wpdb->posts} WHERE post_title='isp_page_{$ips_id}';");
	if(!isset($content) || $content==FALSE){
		@$content = $wpdb->get_var(	"SELECT meta_value
									FROM {$wpdb->prefix}popup_meta
									WHERE {$wpdb->prefix}popup_meta.popup_id = {$ips_id}
									AND {$wpdb->prefix}popup_meta.meta_name='shortcode';");
	}
	if(!isset($content) || $content==FALSE){
		$content = "Write your shortcode here!";
	}
	return $content;
}

function ips_return_popup_name( $id ){
	global $wpdb;
	$lt = $wpdb->get_row("SELECT name FROM {$wpdb->prefix}popup_windows
								WHERE id={$id};");
	if(isset($lt->name)) return $lt->name;	
	else return '';
}

//opt in
function ips_default_opt_in_metas(){
	$arr = array(
			//MAIN E_MAIL
			'ips_main_email' => '',
			//AWEBER
			'ips_aweber_list' => '',
			'ips_aweber_auth_code' => '',
			//MAILCHIMP
			'ips_mailchimp_api' => '',
			'ips_mailchimp_id_list' => '',
			//GET RESPONSE
			'ips_getResponse_api_key' => '',
			'ips_getResponse_token' => '',
			//CAMPAIGN MONITOR
			'ips_cm_list_id' => '',
			'ips_cm_api_key' => '',
			//ICONTACT
			'ips_icontact_appid' => '',
			'ips_icontact_pass' => '',
			'ips_icontact_user' => '',
			'ips_icontact_list_id' => '',
			//CONSTANT CONTACT
			'ips_cc_user' => '',
			'ips_cc_pass' => '',
			'ips_cc_list' => '',
			//WYSIJA
			'ips_wysija_list_id' => '',
			//MY MAIL
			'ips_mymail_list_id' => '',
			//MAD MIMI
			'ips_madmimi_username' => '',
			'ips_madmimi_apikey' => '',
			'ips_madmimi_listname' => ''
	);	
	return $arr;
}
function ips_return_opt_in_metas(){
	$arr = ips_default_opt_in_metas();
	$meta_arr = array();
	foreach($arr as $key=>$value){
		@$val = get_option($key);
		if(isset($val) && $val!=FALSE) $meta_arr[$key] = $val;
		else $meta_arr[$key] = $value;//''
	}	
	return $meta_arr;
}
function ips_update_opt_in_metas(){
	$arr = ips_default_opt_in_metas();
	foreach($arr as $key=>$value){
		if(get_option($key)===FALSE){
			//SAVE
			add_option($key, @$_REQUEST[$key]);
		}else{
			//UPDATE
			update_option($key, @$_REQUEST[$key]);
		}
	}	
}

function ips_has_items(){
	global $wpdb;
	$popups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_windows;");
	if($popups && count($popups)>0) return TRUE;
	return FALSE;
}

function ips_get_number_of_popups($all=true){
	global $wpdb;
	$data = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_windows
				ORDER BY {$wpdb->prefix}popup_windows.id DESC;");
	if ($data){
		if ($all){
			return count($data);
		} else {
			// only active
			$active_popups = 0;
			$status_arr = get_option('ips_items_status');
			foreach ($data as $k=>$v){
				if (!isset($status_arr[$v->id]) || $status_arr[$v->id]=='active'){
					$active_popups++;
				}				
			}
			return $active_popups;
		}		
	}
	return 0;
}

function ips_get_number_of_visitors(){
	global $wpdb;
	$visitor_arr = $wpdb->get_results('SELECT count(distinct visitor) as visitors FROM '.$wpdb->prefix.'popup_visits;');
	if (isset($visitor_arr[0]->visitors)) return (int)$visitor_arr[0]->visitors;
	return 0;
}

function ips_get_number_of_visits(){
	global $wpdb;
	$visits = $wpdb->get_results('SELECT count(visit) as visits FROM '.$wpdb->prefix.'popup_visits;');
	if (isset($visits[0]->visits) ){
		return (int)$visits[0]->visits;
	}
	return 0;
}

function ips_get_average_hits(){
	$visitors = ips_get_number_of_visitors();
	$visits = ips_get_number_of_visits();
	if ($visitors && $visits){
		return number_format($visits / $visitors, 2);
	}
	return 'N/A';
}

function ips_format_num_for_dashboard($num){
	if ($num>999){
		if ($num>999999){
			$number = $num/1000000;
			$type_num = 'M';
		} else {
			$number = $num/1000;
			$type_num = 'k';
		}
		$number = round($number, 2);
		$num = $number . $type_num;
	}
	return $num;
}

function ips_get_best_overall_popup(){
	global $wpdb;
	$popups = $wpdb->get_results( 'SELECT * FROM '.$wpdb->prefix.'popup_windows
									ORDER BY '.$wpdb->prefix.'popup_windows.id DESC;');
	$best = 0;
	if ($popups){
		foreach ($popups as $v){
			$visit_arr = $wpdb->get_results( 'SELECT count(visit) as visits
												FROM '.$wpdb->prefix.'popup_visits
												WHERE popup_id = '.$v->id.';');
			if ($visit_arr && isset($visit_arr[0]->visits) && $visit_arr[0]->visits>$best ){
				$best = $visit_arr[0]->visits;
				$name = $v->name;
			}
		};
		if (isset($name) && $name) return $name;	
	}
	return FALSE;
}

function ips_get_popup_under_total_num(){
	global $wpdb;
	$popups = $wpdb->get_results("SELECT DISTINCT popup_id FROM {$wpdb->prefix}popup_meta
									WHERE meta_name = 'popupType';");
	if ($popups) return count($popups);
	return 0;
}

function ips_get_most_popular_browser(){
	global $wpdb;
	$visits = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_visits WHERE user_agent !='' ORDER BY id LIMIT 1000 ");
	if ($visits){
		$browsers = getBrowsers($visits);
		if ($browsers && count($browsers)){
			$max=0;
			foreach ($browsers as $k=>$v){
				if ($v>$max){
					$max = $v;
					$name = $k;
				}
			}
			if (isset($name)) return array($name,$max);
		}
	}
	return FALSE;
}

function ips_get_visits_num_from_mobile(){
	global $wpdb;
	$visits = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_visits  ORDER BY id LIMIT 1000 ");
	if ($visits){
		$all_devices = getDevices($visits);
		if (isset($all_devices['Computer'])) unset($all_devices['Computer']); 
		if ($all_devices && is_array($all_devices)){
			rsort($all_devices);
			return (int)array_sum($all_devices);
		}			
	}
	return 0;
}

function ips_get_count_submissions(){
	global $wpdb;
	$data = $wpdb->get_results("SELECT count(distinct (concat(`timestamp`, `user_ip`))) as form_results
			FROM {$wpdb->prefix}popup_form_results");
	if (isset($data[0]->form_results)) return (int)$data[0]->form_results;
	return FALSE;
}

function ips_get_last_five_popups(){
	global $wpdb;
	$popups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_windows
					ORDER BY {$wpdb->prefix}popup_windows.id DESC LIMIT 5;");
	return $popups;
}

function ips_getMapVisits_for_dashboard(){
	global $wpdb;
	$mapVisits = $wpdb->get_results("SELECT country, count(visit) as counts
										FROM {$wpdb->prefix}popup_visits
										group by {$wpdb->prefix}popup_visits.country ASC;");
	return $mapVisits;
	
}

function ips_get_popup_type_dashboard($id){
	if (is_Popop_Under($id)){
		return 'PopUp Under';
	}
	else {
		global $wpdb;
		$event = $wpdb->get_results("SELECT meta_value AS value FROM {$wpdb->prefix}popup_meta as a WHERE a.meta_name = 'open_event' AND a.popup_id = $id");
		if (isset($event[0]->value)){
			switch($event[0]->value){
				case 'default':
					$type = 'Load Popup';
					break;
				case 'exit':
					$type = 'Exit Popup';
					break;
				case 'leave':
					$type = 'Leave Popup';
					break;
				case 'click_on_page':
					$type = 'Click on Page';
					break;
				case 'scroll':
					$type = 'Scroll Popup';
					break;
				case 'custom':
					$type = 'Custom Popup';
					break;
				default:
					$type = 'Popup';
			}
			return $type;			
		}
		return 'Popup';
	}
}