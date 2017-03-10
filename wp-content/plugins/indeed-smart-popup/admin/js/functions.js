function noEmptyField(target, value) {
	if (jQuery(target).val() == '')
		jQuery(target).val(value);
}

function deleteFieldVal(target, standard_value) {
	if (jQuery(target).val() == standard_value)
		jQuery(target).val('');
}

function selectAllC(from, where) {
	if (jQuery(from).is(':checked')) {
		jQuery(where).prop('checked', true);
	} else {
		jQuery(where).prop('checked', false);
	}
}

function check_and_h(from, where) {
	if (jQuery(from).is(":checked")) {
		jQuery(where).val(1);
	} else {
		jQuery(where).val(0);
	}
}

function co_menu(menu_id, content_id, value) {
  /////CONTENT OPTIONS MENU
    unselected_class = "co_m_item";
    selected_class = "co_m_item_selected";
    
    var menu_ids = ['#co_m_html',
                    '#co_m_if',
                    '#co_m_v',
                    '#co_m_div',
                    '#co_m_postpag',
                    '#co_m_imgSlider',
                    '#co_m_FBlikebox',
                    '#co_m_googleMaps',
                    '#co_m_opt_in',
                    '#co_m_shortcode' 
                    ];
    var div_ids = ['#html_c',
                   '#thev_c',
                   '#if_c',
                   '#imgSlider',
                   '#div_c',
                   '#the_post_pag_div',
                   '#fb_likebox_c',
                   '#google_map_c',
                   '#opt_in_c', 
                   '#shortcode_c'             
                   ];
    for(i=0;i<menu_ids.length;i++){
    	if(menu_ids[i]==menu_id) jQuery(menu_ids[i]).attr('class', selected_class);
    	else jQuery(menu_ids[i]).attr('class', unselected_class);
    }
    for(i=0;i<div_ids.length;i++){
    	if(div_ids[i]==content_id) jQuery(div_ids[i]).css('display', 'block');
    	else jQuery(div_ids[i]).css('display', 'none');
    }
    jQuery("#c_type").val(value);
}

function checkAndH_boolean( checkID, targetID ){
	if (jQuery(checkID).is(":checked")) {
		jQuery(targetID).val('true');
	} else {
		jQuery(targetID).val('false');
	}
}

function changePos_tb(from, type) {
	if (type == 'bt') {
		jQuery("#pos_tb_bottom").css('display', 'none');
		jQuery("#pos_tb_top").css('display', 'none');
		if (jQuery(from).val() == 'top') {
			jQuery("#pos_tb_top").css('display', 'block');
			jQuery("#gt_top_bottom").val('top');
		} else {
			jQuery("#pos_tb_bottom").css('display', 'block');
			jQuery("#gt_top_bottom").val('bottom');
		}
	} else if (type == 'rl') {
		jQuery("#pos_rl_left").css('display', 'none');
		jQuery("#pos_rl_right").css('display', 'none');
		if (jQuery(from).val() == 'left') {
			jQuery("#pos_rl_left").css('display', 'block');
			jQuery("#gt_right_left").val('left');
		} else {
			jQuery("#pos_rl_right").css('display', 'block');
			jQuery("#gt_right_left").val('right');
		}
	}
}

jQuery(document).ready(function() {
	jQuery("#date_pick_from").datepicker({
		defaultDate : "+1w",
		showOn : "button",
		minDate : 0,
		buttonImage : window.dir_path + "admin/assets/img/calendar_icon1.png",
		buttonImageOnly : true,
		changeMonth : true,
		onClose : function(selectedDate) {
			jQuery("#date_pick_until").datepicker("option", "minDate", selectedDate);
		}
	});
	jQuery("#date_pick_until").datepicker({
		defaultDate : "+1w",
		minDate : 0,
		showOn : "button",
		minDate : 0,
		buttonImage : window.dir_path + "admin/assets/img/calendar_icon1.png",
		buttonImageOnly : true,
		changeMonth : true,
		onClose : function(selectedDate) {
			jQuery("#date_pick_from").datepicker("option", "maxDate", selectedDate);
		}
	});
});

jQuery(document).ready(function() {
	jQuery("#time_pick_from").timepicker({
		showOn : "button",
		buttonImage : window.dir_path + "admin/assets/img/Time.png",
		buttonImageOnly : true
	});

	jQuery("#time_pick_until").timepicker({
		showOn : "button",
		buttonImage : window.dir_path + "admin/assets/img/Time.png",
		buttonImageOnly : true
	});
});

function checkfh(id) {
	var str = jQuery(id).val();
	var correct_str = new Array();
	if (str.indexOf(',') > -1) {
		var arr_str = str.split(',');
		for (i = 0; i < arr_str.length; i++) {
			if (arr_str[i].indexOf('//') > -1) {
				var n_arr = arr_str[i].split('//');
				correct_str.push(n_arr[1]);
			} else {
				correct_str.push(arr_str[i]);
			}
		}
		var final_str = correct_str.join();
		jQuery(id).val(final_str);
	} else if (str.indexOf('//') > -1) {
		var n_arr = str.split('//');
		jQuery(id).val(n_arr[1]);
	}
}

function writeTagValue(the_value) {
	var tags_div = '#tags_field';
	var hidden_field = '#hidden_country_list';
	var span_tag_count = jQuery('#input_tag_num').val();
	//console.log(span_tag_count);
	if (span_tag_count == 0) {
		jQuery(tags_div).append("<div class='clear'></div>");
	}
	var str = jQuery(hidden_field).val();
	if (str.indexOf(the_value) > -1)
		return false;
	else {
		var html_val = '<span class="tag_item" id="tag_num_'
		+ span_tag_count
		+ '"><span>'
		+ the_value
		+ '&nbsp;&nbsp;</span><span class="remove_tag" onClick="removeTag('
		+ span_tag_count + ', \'' + the_value
		+ '\');" title="Removing tag">x</span></span>';
		jQuery(tags_div).prepend(html_val);
		if (str != '')
			new_str = str + ',' + the_value;
		else
			new_str = the_value;
		jQuery(hidden_field).val(new_str);
		span_tag_count++;
		jQuery('#input_tag_num').val(span_tag_count);
	}
}

function removeTag(num, the_value) {
	jQuery('#tag_num_' + num).remove();
	var hidden_id = '#hidden_country_list';
	var str = jQuery(hidden_id).val();
	if (str.indexOf(',') > -1) {
		var arr = str.split(',');
		var new_arr = new Array();
		for (i = 0; i < arr.length; i++) {
			if (arr[i] != the_value)
				new_arr.push(arr[i]);
		}
		var new_str = new_arr.join(',');
	} else {
		new_str = '';
		jQuery('#tags_field').empty();
		jQuery('#input_tag_num').val(0);
	}
	jQuery(hidden_id).val(new_str);
}

/***** OPT IN **************/
function ips_connect_aweber( textarea ){
    jQuery.ajax({
            type : "post",
            url : window.base_url+'/wp-admin/admin-ajax.php',
            data : {
                    action: "ips_update_aweber",
                    auth_code: jQuery( textarea ).val()
                },
            success: function (data) {
                alert('Connected');
            }
    });
}

function ips_get_cc_list( ips_cc_user,ips_cc_pass ){
    jQuery("#ips_cc_list").find('option').remove();
	jQuery.ajax({
            type : "post",
			dataType: 'JSON',
            url : window.base_url+'/wp-admin/admin-ajax.php',
            data : {
                    action: "ips_get_cc_list",
                    ips_cc_user: jQuery( ips_cc_user ).val(),
                    ips_cc_pass: jQuery( ips_cc_pass ).val()
                },
            success: function (data) {
					jQuery.each(data, function(i, option){
						jQuery("<option/>").val(i).text(option.name).appendTo("#ips_cc_list");
					});
				}
    });
}

jQuery(function() {
	jQuery( 'textarea.editors' ).ckeditor();
});

function ips_delete_popup(value, name){
	var confirm_box = confirm("Are You Sure You Want To Delete '"+name+"'?");
	if(confirm_box){
		jQuery('#hidden_tmp_delete').val(value);
		jQuery('#manage_templates').submit();		
	}
}

function ips_confirm_multiple_delete(){
	var confirm_box = confirm("Are You Sure You Want To Delete Selected Items?");
	if(confirm_box)	jQuery('#manage_templates').submit();	
}

function ips_change_item_status(id, icon){
	if(jQuery('#item_'+id+'_status').val()=='active') newval = 'inactive';
	else newval = 'active';
	////AJAX
    jQuery.ajax({
        type : "post",
        url : window.base_url+'/wp-admin/admin-ajax.php',
        data : {
                action: "ips_update_popup_status",
                status: newval,
                the_id: id
            },
        success: function (data) {
        	if(data==1){
            	jQuery('#item_'+id+'_status').val(newval);
            	jQuery(icon).attr('class', 'ips_item_status_'+newval);  	
        	}      	
        }
    });
}

function isp_update_popup_content_box(){
	position_x = jQuery('#isp_box_bk_position_x').val();
	position_y = jQuery('#isp_box_bk_position_y').val();
	repeat_bk = jQuery('#isp_bk_box_repeat').val();
	background_color = jQuery('#box_bk_color').val();
	background_image = jQuery('#bk_img_box').val();
	obj = {};
	if(background_color!='' && background_image!='') obj['background'] = background_color +' url('+background_image+')'; 
	else if(background_color!='') obj['background'] = background_color;
	else if(background_image!='') obj['background'] = 'url('+background_image+')';
	obj['background-position'] = position_y + ' ' +position_x; 
	obj['background-repeat'] = repeat_bk;	
	obj['min-height'] = jQuery('#general_height').val()+"px";
	jQuery('#cke_1_contents, #cke_2_contents').css(obj);
	//console.log(obj);
}

jQuery(document).ready(function(){
	isp_update_popup_content_box();	
});

jQuery(document).ready(function(){
	jQuery('.isp-left-ul-menu-add-edit-admin li a').on('click', function(){
		jQuery('.isp-left-ul-menu-add-edit-admin li').each(function(){
			jQuery(this).attr('class', '');
		});
		jQuery(this).parent().addClass('isp-hover');
		selector = jQuery(this).attr('href');
		console.log('select: '+selector);
		jQuery('.isp-right-options-inner .accordion-body').each(function(){
			current = '#'+jQuery(this).attr('id');
			
			if (current!=selector){
				//console.log('current: '+current);
				jQuery(this).css('height', '0px');
				jQuery(this).css('min-height', '0px');
				jQuery(this).removeClass('in');
			}
		});
		jQuery(selector).css('height', 'auto');
		jQuery(selector).css('min-height', '300px');
	})
});