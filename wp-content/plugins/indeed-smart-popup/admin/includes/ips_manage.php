<?php
global $wpdb;
$url = admin_url('admin.php');

/**************************** SAVE POPUP ****************************************/
if(isset($_REQUEST['save_bttn'])){
	ips_save_metas();
}

/**************************** UPDATE POPUP **************************************/
if(isset($_REQUEST['update_bttn']) && isset($_REQUEST['popup_id']) && $_REQUEST['popup_id']!=''){
	ips_update_metas();
}



/******************************* POPUP UNDER *******************************/
if( is_plugin_active('indeed-popup-under/indeed-popup-under.php') ){
	include IPU_DIR_PATH . 'inc/functions.php';
	#SAVE 
	if(isset($_REQUEST['ipu_save_bttn'])){
		ipu_save_metas();
	}
	#UPDATE 
	if(isset($_REQUEST['ipu_update_bttn']) && isset($_REQUEST['popup_id']) && $_REQUEST['popup_id']!=''){
		ipu_update_metas();
	}
	#PREVIEW
	if(isset($_REQUEST['preview_p_under_id']) && $_REQUEST['preview_p_under_id']!=''){
		isu_preview_popup( $_REQUEST['preview_p_under_id'] );
	}
}
/******************************* END OF POPUP UNDER *******************************/




/**************************** DELETE POPUP **************************************/

if(isset($_REQUEST['deleteItem']) && is_array($_REQUEST['deleteItem']) && count($_REQUEST['deleteItem'])>0){
	//MULTIPLE ITEMS DELETE
	foreach($_REQUEST['deleteItem'] as $the_id){
        deletePopUp($the_id);
    }
}

if(isset($_REQUEST['deleteID']) && $_REQUEST['deleteID']!=''){
    //SINGLE ITEM DELETE
    deletePopUp($_REQUEST['deleteID']);
}

/******************************** DUPLICATE ******************************************/
if(isset($_REQUEST['duplicate']) && $_REQUEST['duplicate']!=''){
	isp_duplicate_popup($_REQUEST['duplicate']);
}

/******************************** PREVIEW *******************************************/
if(isset($_REQUEST['preview_id']) && $_REQUEST['preview_id']!=''){
	isp_preview_popup( $_REQUEST['preview_id'] );
}

/******************************* GET THE POPUPS **************************************/

$popups = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_windows
								ORDER BY {$wpdb->prefix}popup_windows.id DESC;");

//VISITS AND VISITORS
foreach($popups as $v){
    $visit_arr = $wpdb->get_results("SELECT count(distinct visitor) as visitors, count(visit) as visits
                                     FROM {$wpdb->prefix}popup_visits
                                     WHERE popup_id = {$v->id};");
    if(isset($visit_arr) && count($visit_arr)>0){
      if(isset($visit_arr[0]->visits)) $visits[$v->id] = $visit_arr[0]->visits;
      else $visits[$v->id] = 0;
      if(isset($visit_arr[0]->visitors))  $visitors[$v->id] = $visit_arr[0]->visitors;
      else $visitors[$v->id] = 0;
    }
}

//FORM RESULTS
foreach($popups as $v){
        $forms_results_arr = $wpdb->get_results("SELECT count(distinct (concat(`timestamp`, `user_ip`))) as form_results
                                     FROM {$wpdb->prefix}popup_form_results
                                     WHERE popup_id = {$v->id};");
    if(isset($forms_results_arr) && count($forms_results_arr)>0){
        if(isset($forms_results_arr[0]->form_results)) $form_res[$v->id] = $forms_results_arr[0]->form_results;
        else $form_res[$v->id] = 0;
    }
}

@$ips_items_status = get_option('ips_items_status');
?>
<script>
	base_url = '<?php echo get_site_url();?>';
</script>
<div class="wrap">
<?php								 
if(isset($popups) && count($popups)>0){
?>
<div class="ips-dashboard-title">Smart Popup - <span class="second-text">Manage Popups</span></div>
<form action="" method="post" id="manage_templates" class="form_manage_tmp" style="min-width:900px; width:80%; float:left;">
	        <input type="button" value="Delete" class="button action top_bttn" name="deleteButton" onClick="ips_confirm_multiple_delete();"/>
        	<table class="wp-list-table widefat fixed posts icltable" cellspacing="0">
                <thead>
        		    <tr>
                        <th class="manage-column column-cb check-column"><input type="checkbox" onClick="selectAllC(this, '.checkbox_delete');" /></th>
        			    <th class="manage-column column" style=" width:120px;">Name:</th>
                        <th class="manage-column column shortcode_td">Shortcode</th>
                        <th align="center" class="manage-column column" style="width:85px; text-align:center;">Type</th>
                        <th align="center" class="manage-column column" style="width:85px; text-align:center;">Content</th>
                        <th align="center" class="manage-column column" style="width:40px; text-align:center;">Visits</th>
                        <th align="center" class="manage-column column" style="width:50px; text-align:center;">Visitors</th>
						<th class="manage-column column" style="width:80px;">Device</th>
                        <th class="manage-column column" style="width:40px;">Active</th>
                        <th class="manage-column column" style="width:60px;">Preview</th>
                        <th class="manage-column column" style="width:40px;">Edit</th>
						<th class="manage-column column" style="width:80px;">Duplicate</th>
                        <th class="manage-column column" style="width:50px;">Delete</th>
                    </tr>
                </thead>
<?php
    $j = 0;
    foreach($popups as $k=>$v){
    	unset($web_mobile);
    	$web_mobile = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_meta as a WHERE a.meta_name = 'web_mobile_display' AND a.popup_id = $v->id");
        $class = "";
    	if($j%2==0) $class = "alternate";
		$open_event = $wpdb->get_results("SELECT meta_value AS value FROM {$wpdb->prefix}popup_meta as a WHERE a.meta_name = 'open_event' AND a.popup_id = $v->id");
		$type_class = '';
		$type_name = '';
		switch($open_event[0]->value){
			case 'default':
					$type_class = 'load-popup';
					$type_name = 'Load Popup';
					break;
			case 'exit':
					$type_class = 'exit-popup';
					$type_name = 'Exit Popup';
					break;
			case 'leave':
					$type_class = 'leave-popup';
					$type_name = 'Leave Popup';
					break;
			case 'click_on_page':
					$type_class = 'click-popup';
					$type_name = 'Click on Page';
					break;	
			case 'scroll':
					$type_class = 'scroll-popup';
					$type_name = 'Scroll Popup';
					break;
			case 'custom':
					$type_class = 'custom-popup';
					$type_name = 'Custom Popup';
					break;	
			default:
					$type_class = 'default';
					$type_name = 'Popup';							
		}
    	$popup_under = 0;
    	if(is_Popop_Under($v->id)) $popup_under = 1;
              ?>
                <tr class="<?php echo $class;?>">
                    <td><input type="checkbox" class="checkbox_delete" name="deleteItem[]" value="<?php echo $v->id;?>"/></td>
                    <td style="color: #21759b; font-weight:bold; width:120px;"><?php echo $v->name;?></td>
                    <td style="color: #396;">
                    	<?php 
                    	$shortcode_str = 'indeed_popups';
                    	if($popup_under==1) $shortcode_str = 'indeed_popup_under';
                    			?>
                    				[<?php echo $shortcode_str;?> id=<?php echo $v->id;?>]               	
                    </td>
                    <td align="center">
                    	<?php 
                    		if($popup_under==1) echo '<span class="manage-type-popup under-popup">PopUp Under</span>';
                    		else echo '<span class="manage-type-popup '.$type_class.'">'.$type_name.'</span>';
                    	?>   
                    </td>
					<td align="center">
					<span class="manage-type-content">
					<?php 
					if($popup_under == 1) echo 'Under';
					else{
						$content_type = $wpdb->get_results("SELECT meta_value AS value FROM {$wpdb->prefix}popup_meta as a WHERE a.meta_name = 'c_type' AND a.popup_id = $v->id");
						switch($content_type[0]->value){
							case 'html':
									echo 'HTML';
									break;
							case 'opt_in':
									echo 'Opt-In';
									break;
							case 'shortcode':
									echo 'Shortcode';
									break;
							case 'iframe':
									echo 'iFrame';
									break;
							case 'video':
									echo 'YT Video';
									break;								
							case 'imgSlider':
									echo 'IMG Slider';
									break;
							case 'fb_likebox':
									echo 'FB LikeBox';
									break;		
							case 'google_maps':
									echo 'G Maps';
									break;
							case 'content_id':
									echo 'Content Box';
									break;
							case 'the_postpag_v':
									echo 'Page/Post';
									break;				
						}
					}
					?>
					</span>
					</td>
                    <td align="center"><?php echo $visits[$v->id];?></td>
                    <td align="center"><?php echo $visitors[$v->id];?></td>
					<td><?php echo $web_mobile[0]->meta_value;?></td>
                    <td style="padding-left:20px;">
                    	<?php 
                    		$is_active = 'active';
                    		if($ips_items_status!==FALSE && count($ips_items_status)>0){
                    			if(isset($ips_items_status[$v->id]) && $ips_items_status[$v->id]=='inactive'){
									$is_active = 'inactive';
                    			}
                    		}
                    	?>
                    	<div title="Deactivate/Pause the PopUp" class="ips_item_status_<?php echo $is_active;?>" onClick="ips_change_item_status(<?php echo $v->id;?>, this);"></div>
                    	<input type="hidden" value="<?php echo $is_active;?>" id="item_<?php echo $v->id;?>_status" />
                    </td>
                    <td style="padding-left:20px;">
                    	<?php 
                    		if($popup_under==1){
                    			?>
                    				<a href="<?php echo $url;?>?page=ips_admin&tab=ips_manage&preview_p_under_id=<?php echo $v->id;?>"><i class="icon-preview"></i></a>
                    			<?php 
                    		}else{
                    			?>
                    				<a href="<?php echo $url;?>?page=ips_admin&tab=ips_manage&preview_id=<?php echo $v->id;?>"><i class="icon-preview"></i></a>
                    			<?php 	
                    		}
                    	?>                    	
                    </td>
                    <td style="padding-left:10px;">
                    	<?php 
                    		if($popup_under==1){
                    			?>
                        			<a href="<?php echo $url;?>?page=ips_admin&tab=popup_under&p_id=<?php echo $v->id;?>" title="Edit this item"><i class="icon-edit-e"></i></a>                    			
                    			<?php 
                    		}else{
                    			?>
                        			<a href="<?php echo $url;?>?page=ips_admin&tab=add_edit_page&p_id=<?php echo $v->id;?>" title="Edit this item"><i class="icon-edit-e"></i></a>                    			
                    			<?php 	
                    		}
                    	?>
                    </td>
					<td  style="padding-left:20px;">
						 <div class="div_pointer"  title="Duplicate this item">
                            <i class="icon-duplicate-e"onclick="jQuery('#hidden_duplicate_popup').val('<?php echo $v->id;?>');jQuery('#manage_templates').submit();"></i>
                         </div>
					</td>

                    <td style="padding-left:15px;">
                        <div class="div_pointer"><i class="icon-remove-e" onclick="ips_delete_popup(<?php echo $v->id;?>, '[<?php echo $shortcode_str;?> id=<?php echo $v->id;?>]');"></i></div>
                    </td>
                </tr>
              <?php
              $j++;
    }
?>
                <tfoot>
        		    <tr>
                        <th class="manage-column column-cb check-column"><input type="checkbox" onClick="selectAllC(this, '.checkbox_delete');" /></th>
           			    <th class="manage-column column">Name:</th>
                        <th class="manage-column column shortcode_td">Shortcode</th>
                        <th class="manage-column column"  style="text-align:center;">Type</th>
						<th class="manage-column column"  style="text-align:center;">Content</th>
                        <th class="manage-column column"  style="text-align:center;">Visits</th>
                        <th class="manage-column column"  style="text-align:center;">Visitors</th>
						<th class="manage-column column">Device</th>
                        <th class="manage-column column">Active</th>
                        <th class="manage-column column">Preview</th>
                        <th class="manage-column column">Edit</th>
						<th class="manage-column column">Duplicate</th>
                        <th class="manage-column column">Delete</th>
                    </tr>
                </tfoot>
        </table>
        <input type="button" value="Delete" class="button action bottom_bttn" name="deleteButton" onClick="ips_confirm_multiple_delete();"/>
        <input type="hidden" name="set_default_template" value="" id="hidden_tmp_id"/>
        <input type="hidden" name="deleteID" value="" id="hidden_tmp_delete" />
        <input type="hidden" name="duplicate" value="" id="hidden_duplicate_popup" />
    </form>

	<div class="clear"></div>

<?php 
}
else{
    echo " No PopUps available!";
}

?>
</div>