<?php
$string = '';

/////////////// STYLE //////////////////////
$style = "\n<style type='text/css' name='SmartPopUp'>";
	//slider extra style
	$style .= ips_return_slider_extra_style($meta_arr['c_type'], $ips_id);
	//GENERAL OPTIONS
	$style .= Get_IpsWrappAbClass($meta_arr, $ips_id);
	$style .= Get_IpsWPCClass($meta_arr, $ips_id);	 
	//CONTENT BOX
	$style .= Get_IpsWClass($meta_arr, $ips_id, $device_type);
	$style .= Get_IpsContent($ips_id);
	//CLOSE BUTTON
	$style .= Get_IpsCloseBttnClass($meta_arr, $ips_id); 
$style .= "\n</style>";
/////////////// STYLE //////////////////////

/////////////// JAVASCRIPT //////////////////////
//CONTENT OPTIONS
$content = GetContent($meta_arr, $ips_id);
$JsContent = JsContent($meta_arr, $ips_id, $device_type, $content['html_content']);
//EVENT HANDLER
$js_action = stripslashes($content['js_action']);
$JsEvent = OpenEvent($meta_arr, $js_action, $ips_id);
//CLOSE BUTTON
$JsClose = ClosePopUp($meta_arr, $ips_id);
$javascript = "\n<script type='text/javascript' name='SmartPopUp'>";
$javascript .= "isp_base_url='".get_site_url()."'";//base url for ajax call
$javascript .= $JsContent; 
$javascript .= $JsEvent;
$javascript .= $JsClose;
$javascript .= ips_is_preview_r_schedule(@$preview, $ips_id);
$javascript .= "\n</script>";
$javascript .= stripslashes($content['js_content']);
/////////////// JAVASCRIPT //////////////////////

//OUTPUT
$string .= $style;
$string .= $javascript;
?>