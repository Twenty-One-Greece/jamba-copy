<?php 
global $wpdb;
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
?>
<script>
base_url = '<?php echo get_site_url();?>';
</script>
<div class="wrap">
<div class="ips-dashboard-title">Smart Popup - <span class="second-text">Export Results</span></div>
<?php
if(isset($popups) && count($popups)>0){
?>
<form action="" method="post" id="manage_templates" class="form_manage_tmp" style="max-width: 780px; float:left;">
        	<table class="wp-list-table widefat fixed posts icltable" cellspacing="0">
                <thead>
        		    <tr>
        			    <th class="manage-column column" style=" width:200px;">Name:</th>
                        <th class="manage-column column shortcode_td">Shortcode</th>
                        <th class="manage-column column" style="width:100px; text-align:center;">Submissions</th>
						<th class="manage-column column" style="width:100px; text-align:center;">Generate</th>
						<th style="width:110px; text-align:center;">Download</th>
                    </tr>
                </thead>
<?php
    $j = 0;
    foreach($popups as $k=>$v){
    	unset($web_mobile);
    	$web_mobile = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_meta as a WHERE a.meta_name = 'web_mobile_display' AND a.popup_id = $v->id");
        $class = "";
        if(!is_Popop_Under($v->id)){
        	if($j%2==0) $class = "alternate";
        	?>
        	                <tr class="<?php echo $class;?>">
        	                    <td style="color: #21759b; font-weight:bold; width:120px;"><?php echo $v->name;?></td>
        	                    <td style="color: #396;">[indeed_popups id=<?php echo $v->id;?>]</td>
        	                    <td style=" text-align:center; font-weight:bold;"><?php echo $form_res[$v->id];?></td>
        						<td style=" text-align:center;">
        	                        <?php
        	                            if(isset($form_res[$v->id]) && $form_res[$v->id]>0){
        	                                    if($form_res[$v->id]==1) $results_num = "1 Submission";
        	                                    else $results_num = $form_res[$v->id]." Submissions";
        	                                ?>
        	                                    <span class="download_csv" onClick="jQuery('#csv_results').val('<?php echo $v->id;?>');jQuery('#manage_templates').submit();" title="<?php echo $results_num;?>">
        	                                        Generate CSV
        	                                    </span>
        	                                <?php
        	                            }
        	                        ?>
        	                    </td>  
        	                    <td id="lnk_<?php echo $v->id;?>" style=" text-align:center;"></td>
        	                </tr>
        	              <?php
        	              $j++;        	
        }//end of check if is popup under type
    }
?>
                <tfoot>
        		    <tr>
        			    <th class="manage-column column" style=" width:200px;">Name:</th>
                        <th class="manage-column column shortcode_td">Shortcode</th>
                        <th class="manage-column column" style="width:90px; text-align:center;">Submissions</th>
						<th class="manage-column column" style="width:100px; text-align:center;">Generate</th>
						<th style="width:110px; text-align:center;">Download</th>
                    </tr>
                </tfoot>
        </table>
			<input type="hidden" id="csv_results" value="" name="results_CSV"/>
    </form>
	<div class="clear"></div>
<?php 
}
else{
    echo " No PopUps available!";
}

?>
</div>                             
<?php
//////////////CSV
if(isset($_REQUEST['results_CSV']) && $_REQUEST['results_CSV']!=''){
	$meta_names = $wpdb->get_results("
                                      SELECT distinct (meta_name) FROM `{$wpdb->prefix}popup_form_results`
                                      WHERE popup_id = {$_REQUEST['results_CSV']} order by meta_name;
                                     ");
    $csv_str = "";
    $csv_str .= " ,";
    foreach($meta_names as $meta_n){
    	$csv_str .= $meta_n->meta_name . ',';
    }
    $csv_str .= '\n';

    $the_entries = $wpdb->get_results("
                                       SELECT distinct (concat(`timestamp`, `user_ip`)) as `unique_val`, `user_ip`, `timestamp`
                                       FROM `{$wpdb->prefix}popup_form_results`
                                       WHERE popup_id = {$_REQUEST['results_CSV']}
                                       group by `unique_val`;
                                      ");

    if(isset($the_entries) && count($the_entries)>0){
    	foreach($the_entries as $entry){
        	$form_results = $wpdb->get_results("
                                                SELECT * FROM wp_popup_form_results
                                                WHERE popup_id = {$_REQUEST['results_CSV']}
                                                AND timestamp = '{$entry->timestamp}'
                                                AND user_ip = '{$entry->user_ip}'
                                                order by meta_name;
                                               ");
            $csv_str .= $entry->timestamp . ',';
            foreach($form_results as $form_r){
            	$csv_str .= $form_r->meta_value . ',';
            }
            $csv_str .= '\n';
        }
	}
	$dir_path = ISP_DIR_PATH.'tmp/';
    $file_name = "form_results_".$_REQUEST['results_CSV'].".csv";
    if(file_exists($dir_path.$file_name)) unlink($dir_path.$file_name);
    $fh = fopen($dir_path.$file_name, 'w') or die();
    $str_arr = explode('\n', $csv_str);

    foreach($str_arr as $line){
    	$val = explode(",", $line);
        fputcsv($fh, $val);
    }

    fclose($fh);
    $target_lnk = ISP_DIR_URL.'tmp/'.$file_name;
    ?>
    <script type="text/javascript">
    	var after_a = "#lnk_<?php echo $_REQUEST['results_CSV'];?>";
        jQuery(after_a).html("<a href='<?php echo $target_lnk;?>' target='_blank' style='color:#333; text-decoration:none;'><i class='icon-ips-download'></i> Download</a>");
    </script>
    <?php
    }//end of make csv
?>                    