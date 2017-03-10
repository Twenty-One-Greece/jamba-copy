<div class="wrap">
<div class="ips-dashboard-title">Smart Popup - <span class="second-text">Predefined Templates</span></div>
<?php
$templates_dir = ISP_DIR_PATH . 'templates/' ;
if(is_readable($templates_dir)){
	if($handle = opendir( $templates_dir )) {
    	while (false !== (@$entry = readdir($handle))) {
        	if($entry!='.' && $entry!='..'){
            	@$handdle_arr = explode('_', $entry);
                	if(isset($handdle_arr[1])) $template_arr[$handdle_arr[1]] = $entry;
            }                                        	
        }
    	ksort($template_arr);
    }
    closedir($handle);
}
if(isset($template_arr) && count($template_arr)>0){
	foreach($template_arr as $entry){
		$class = "";
		@$data = file_get_contents(ISP_DIR_PATH.'templates/' . $entry . '/details.json');
		@$details = json_decode( $data );
		$title = (isset($details->title) ? $details->title : '');
		$description = (isset($details->description) ? $details->description : '');
		$version = (isset($details->version) ? $details->version : 'unknown');
		
		echo "<div class='isp_template'>
		<div class='isp_screenshot'>
		<img  src=\"".ISP_DIR_URL."templates/$entry/images/preview.jpg\" />
		</div>
		<span class='isp_more-details'>
		$description
		</span>
		<div class='isp_details'>
		$title
		<div class='isp_actions'>
		<a class='button button-primary activate' href=\"".admin_url('admin.php')."?page=ips_admin&tab=add_edit_page&template=".urlencode($entry)."\">New PopUp</a>
				</div>
				</div>
				</div>";
		unset($data);
		unset($details);	
	}
}
?>
</div>