/************************widget js*****************************/
function bbYMM_create_request_widget(obj,token,url){
	if(obj.value > 0){
		jQuery("#bbymmLoader").show();
		mmy_default_behave(token);
		var wp_ajax_url = url;
		jQuery.ajax({
			type: "POST",
			url: wp_ajax_url,
			data : {
				'action': 'ymm_filter_info',
				'ymm_data': obj.value,
				'ymm_token': token,
			},
			success: function(response){
				if(token == 2){
					jQuery(".ymm_make_box").append(response);
				}
				if(token == 3){
					jQuery(".ymm_model_box").append(response);
				}
				jQuery("#bbymmLoader").hide();
			}
		});
	}
	else{
		mmy_default_behave(token);
	}
}
/*****************************shortcode************************************/

function bbYMM_create_request_shortcode(obj,token,url){
	if(obj.value > 0){
		jQuery("#bbymmLoader_sc").show();
		mmy_default_behave_sc(token);
		var wp_ajax_url = url;
		jQuery.ajax({
			type: "POST",
			url: wp_ajax_url,
			data : {
				'action': 'ymm_filter_info',
				'ymm_data': obj.value,
				'ymm_token': token,
			},
			success: function(response){
				if(token == 2){
					jQuery(".ymm_make_box_sc").append(response);
				}
				if(token == 3){
					jQuery(".ymm_model_box_sc").append(response);
				}
				jQuery("#bbymmLoader_sc").hide();
			}
		});
	}
	else{
		mmy_default_behave_sc(token);
	}
}

function remove_bbymm_data(){
	var answer = confirm("Are you sure you want to delete all filter data ?");
	if(answer){
		jQuery("#frm_bbymm").submit();	
	}
}
