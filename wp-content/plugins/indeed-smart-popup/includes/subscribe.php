<?php 
////////GETTING DATA 
$form_data_array = explode('&', $_REQUEST['form_data']);
$form_data_array = str_replace('%40', '@', $form_data_array);
foreach($form_data_array as $item) {
	$item_array = explode('=', $item);
	$items[$item_array[0]] = $item_array[1];
}

if(isset($items['ips_subscribe_type']) && $items['ips_subscribe_type']!=''){
	////search for email and name
	$mail = '';
	$name = '';
	$first_name = '';
	$last_name = '';
	$wrong_values = false;
	
	foreach($items as $k=>$v){
		if(preg_match("/mail/", $k)){
			$mail = $items[$k];
			if(!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $mail)){
				$wrong_values[] = $k;
			}
		}elseif(preg_match("/name/", $k)){
			$name = $items[$k];
			if($name!=''){
				$name = str_replace('+', ' ', $name);
				if(preg_match('/\s/', $name)){
					@$name_array = explode(' ',$name);
					if(isset($name_array[0])) $first_name = $name_array[0];
					if(isset($name_array[1])) $last_name = $name_array[1];
					if(isset($name_array[2])) $last_name .= ' '.$name_array[2];
					if(isset($name_array[3])) $last_name .= ' '.$name_array[3];
				}else $first_name = $name;				
			}else{
				//empty input name
				$wrong_values[] = $k;			
			}
		}else{
			//test if value is not empty
			if($items[$k]==''){
				$wrong_values[] = $k;
			}	
		}		
	}
	
	if($wrong_values && count($wrong_values)>0){
		echo json_encode($wrong_values);
		die();
	}else{
		///switch subscribe type
		include_once ISP_DIR_PATH.'includes/Ips_mail_services.php';
		$indeed_mail = new Ips_mail_services();
		$indeed_mail->dir_path = ISP_DIR_PATH . 'includes';
		switch($items['ips_subscribe_type']){
			case 'aweber':
				$aw_list = str_replace('awlist', '', get_option('ips_aweber_list'));
				$consumer_key = get_option( 'aweber_consumer_key' );
				$consumer_secret = get_option( 'aweber_consumer_secret' );
				$access_key = get_option( 'aweber_acces_key' );
				$access_secret = get_option( 'aweber_acces_secret' );
				$return = $indeed_mail->indeed_aWebberSubscribe( $consumer_key, $consumer_secret, $access_key, $access_secret, $aw_list, $mail, $name );
			break;
		
			case 'email_list':
				if(get_option('ips_email_list')===FALSE) add_option('ips_email_list', $mail);
				else{
					$email_list = get_option('ips_email_list');
					$email_list .= $mail . ',';
					update_option('ips_email_list', $email_list);
				}
				$return = 1;
			break;
		
			case 'mailchimp':
				$mailchimp_api = get_option( 'ips_mailchimp_api' );
				$mailchimp_id_list = get_option( 'ips_mailchimp_id_list' );
				$return = $indeed_mail->indeed_mailChimp( $mailchimp_api, $mailchimp_id_list, $mail, $first_name, $last_name );
			break;
		
			case 'get_response':
				$api_key = get_option('ips_getResponse_api_key');
				$token = get_option('ips_getResponse_token');
				$return = $indeed_mail->indeed_getResponse( $api_key, $token, $mail, $name );
			break;
		
			case 'campaign_monitor':
				$listId = get_option('ips_cm_list_id');
				$apiID = get_option('ips_cm_api_key');
				$return = $indeed_mail->indeed_campaignMonitor( $listId, $apiID, $mail, $name );
			break;
		
			case 'icontact':
				$appId = get_option('ips_icontact_appid');
				$apiPass = get_option('ips_icontact_pass');
				$apiUser = get_option('ips_icontact_user');
				$listId = get_option('ips_icontact_list_id');
				$return = $indeed_mail->indeed_iContact( $apiUser, $appId, $apiPass, $listId, $mail, $first_name, $last_name );
			break;
		
			case 'constant_contact':
				$apiUser = get_option('ips_cc_user');
				$apiPass = get_option('ips_cc_pass');
				$listId = get_option('ips_cc_list');
				$return = $indeed_mail->indeed_constantContact($apiUser, $apiPass, $listId, $mail, $first_name, $last_name );
			break;
		
			case 'wysija':
				$listID = get_option('ips_wysija_list_id');
				$return = $indeed_mail->indeed_wysija_subscribe( $listID, $mail, $first_name, $last_name );
			break;
		
			case 'mymail':
				$listID = get_option('ips_mymail_list_id');
				$return = $indeed_mail->indeed_myMailSubscribe( $listID, $mail, $first_name, $last_name );
			break;
		
			case 'madmimi':
				$username = get_option('ips_madmimi_username');
				$api_key =  get_option('ips_madmimi_apikey');
				$listName = get_option('ips_madmimi_listname');
				$return = $indeed_mail->indeed_madMimi($username, $api_key, $listName, $mail, $first_name, $last_name );
			break;
		}
	}	
}else{
	////////////////////// SEND MAIL - DEFAULT
	foreach($items as $k=>$v){
		//test if email address is correct
		if(preg_match("/mail/", $k)){
			$mail = $items[$k];
			if(!preg_match("/^[a-zA-Z0-9_.+-]+@[a-zA-Z0-9-]+\.[a-zA-Z0-9-.]+$/", $mail)){
				$arr[] = $k;
				echo json_encode($arr);
				die();
			}
		}
	}
	$return = isp_send_form_results_via_mail($items);
}

if(isset($return) && $return==1){
	//////////// STORE THE RESULTS INTO DB
	isp_insert_form_results_db($items);
	echo 1;
}else{
	echo 0;
}
die();

?>