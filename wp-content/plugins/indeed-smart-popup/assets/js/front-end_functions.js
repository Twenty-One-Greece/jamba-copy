function setCookie(name,value,days){
	if (days) {
		var date = new Date();
		date.setTime(date.getTime()+(days*24*60*60*1000));
		var expires = '; expires='+date.toGMTString();
	}else var expires = '';
	document.cookie = name+'='+value+expires+'; path=/';
}
function getCookie(cname){
	var name = cname + '=';
	var ca = document.cookie.split(';');
	for(var i=0; i<ca.length; i++) {
		var c = ca[i];
		while (c.charAt(0)==' ') c = c.substring(1);
		if (c.indexOf(name) != -1) return c.substring(name.length, c.length);
	}
	
	return '';
}

function ips_update_form_id(ips_id){
	jQuery('#ips_main_'+ips_id+' form').attr('id', 'isp_form_'+ips_id);
}
function ips_subscribe_check(type, subscribe, ips_id){
	if(type=='opt_in'){
		jQuery('#ips_main_'+ips_id+' form').append('<input type="hidden" value="'+subscribe+'" name="ips_subscribe_type" />');
	}
}

function ips_return_json_values(str){
    try{
        obj = JSON.parse(str);
		if(obj && typeof obj==="object" && obj!==null){
            return obj;
        }
    }catch(e){
        return false;
    }
	return false;
}

function ips_return_error_msg(type, isp_id, err_msg){
	if(type=='opt_in'){
		var submit = jQuery('#isp_form_'+isp_id).find(':submit');
		submit.after('<span class="ips_error_addr_mail" style="margin-left: 10px;">'+err_msg+'</span>');
	}return '';
}

/////////////////////
function ips_load_facebook(){
    (function(d, s, id) {
    var js, fjs = d.getElementsByTagName(s)[0];
    if (d.getElementById(id)) return;
    js = d.createElement(s);
    js.id = id;
    js.src = "//connect.facebook.net/en_EN/sdk.js#xfbml=1&version=v2.0&status=0";
    fjs.parentNode.insertBefore(js, fjs);
    }
    (document, 'script', 'facebook-jssdk'));
}

function isp_save_statistic_data_js(the_id){
	ips_user = getCookie('ips_visitor');
    jQuery.ajax({
        type : "post",
        url : window.isp_base_url+'/wp-admin/admin-ajax.php',
        data : {
                action: "SaveStatisticsData",
                ips_id: the_id,
                ips_user_cookie: ips_user,
            },
        success: function (data) {
        }
    });
}