<?php
/* 
Class Name: Import Module

Author: Bigboss555
Author URI: http://sakosys.com
Copyright sakosys.com. All rights reserved
*/

add_action( 'admin_menu', 'add_data_import_menu');
$bbymm_message = '';
function add_data_import_menu(){
	add_management_page('year_make_model_csv_import', 'Year/Make/Model CSV', 8, 'bbymm_csv_import', 'mt_manage_page');
}

function delete_custom_terms($taxonomy){
    global $wpdb;
	add_filter( 'http_request_timeout', 'bump_request_timeout');

    $query = 'SELECT t.name, t.term_id
            FROM ' . $wpdb->terms . ' AS t
            INNER JOIN ' . $wpdb->term_taxonomy . ' AS tt
            ON t.term_id = tt.term_id
            WHERE tt.taxonomy = "' . $taxonomy . '"';

    $terms = $wpdb->get_results($query);

    foreach ($terms as $term) {
		import_start();
		wp_delete_term( $term->term_id, $taxonomy );
    }
}

function import_start() {
	if ( function_exists( 'set_time_limit' ) && false === strpos( ini_get( 'disable_functions' ), 'set_time_limit' ) && ! ini_get( 'safe_mode' ) ) {
		@set_time_limit( 0 );
	}
	@ob_flush();
	@flush();
	@ini_set( 'auto_detect_line_endings', '1' );
}
 function bump_request_timeout( $val ) {
	return 60;
}

function mt_manage_page() {
	if(isset($_POST['hdntoken']) && $_POST['hdntoken'] == 'delete'){
		delete_custom_terms('bb_YMM_Filter');
		$bbymm_message = 'Delete All Filter Data Successfully';
	}
	else if(isset($_POST['hdntoken']) && $_POST['hdntoken'] == 'import'){
		add_filter( 'http_request_timeout', 'bump_request_timeout');
		$filename = $_FILES["csvfile"]["name"];
		$ext = substr(strrchr($filename, "."), 1);
		$tmpName = $_FILES["csvfile"]["tmp_name"];
		if(($handle = fopen($tmpName, 'r')) !== FALSE) {
			$row = 0;
			while(($data = fgetcsv($handle, 1000, ',')) !== FALSE) {
				import_start();
				$parent_term_id = '';
				$parent_term_id_make = '';
				if($row != 0){
					if(isset($data[0]) && $data[0] != ''){
						if(isset($data[0]) && $data[0] != ''){
							$parent_term = term_exists( $data[0], 'bb_YMM_Filter' ); // array is returned if taxonomy is given
							$parent_term_id = $parent_term['term_id']; // get numeric term id
							if($parent_term_id == ''){
								wp_insert_term(
								  trim($data[0]), // the term 
								  'bb_YMM_Filter', // the taxonomy
								  array(
									'description'=> '',
									'slug' => strtolower( trim($data[0]) ),
									'parent'=> $parent_term_id
								  )
								);
								$parent_term = term_exists( $data[0], 'bb_YMM_Filter' );
								$parent_term_id = $parent_term['term_id'];
							}
						}
					}
					if(isset($data[1]) && $data[1] != '' && $parent_term_id != '' && $parent_term_id != '0'){
						$x1_make_term = term_exists( $data[1], 'bb_YMM_Filter' );
						$x1_make_parent = $x1_make_term['parent'];
						if($parent_term_id != $x1_make_parent){
							wp_insert_term(
							  trim($data[1]), // the term 
							  'bb_YMM_Filter', // the taxonomy
							  array(
								'description'=> '',
								'slug' => strtolower( trim($data[1]) ),
								'parent'=> $parent_term_id
							  )
							);
							$parent_term_make = term_exists( $data[1], 'bb_YMM_Filter', (int)$parent_term_id );
							$parent_term_id_make = $parent_term_make['term_id'];
						}
						else{
							$parent_term_make = term_exists( $data[1], 'bb_YMM_Filter', (int)$parent_term_id );
							$parent_term_id_make = $parent_term_make['term_id'];
						}
					}
					if(isset($data[2]) && $data[2] != '' && $parent_term_id != '' && $parent_term_id != '0' && $parent_term_id_make != '' && $parent_term_id_make != '0'){
						if(isset($data[2]) && $data[2] != ''){
							$x1_model_term = term_exists( $data[2], 'bb_YMM_Filter' ); // array is returned if taxonomy is given
							$x1_model_parent = $x1_model_term['term_id']; // get numeric term id
							if($parent_term_id_make != $x1_model_parent){
								wp_insert_term(
								  trim($data[2]), // the term 
								  'bb_YMM_Filter', // the taxonomy
								  array(
									'description'=> '',
									'slug' => strtolower( trim($data[2]) ),
									'parent'=> $parent_term_id_make
								  )
								);
							}
						}
					}
				}
				$row++;
			}
			fclose($handle);
		}
		$bbymm_message = 'Import All Filter Data Successfully';
	}
	
	if($bbymm_message != ''){
		echo '<p></p><div class="updated notice is-dismissible" id="message"><p>'.$bbymm_message.'.</p><button class="notice-dismiss" type="button"><span class="screen-reader-text">Dismiss this notice.</span></button></div>';
	}
	echo "<h2 style='text-decoration:underline;'>Year/Make/Model CSV Import</h2>";
	echo "<div style='font-weight:bold;padding:3px;'>This is csv import module for car parts Year, Make and Model so Admin can upload csv file automatically. Any help or <br/>customization please contact with this email address <a href='mailto:devsolver@gmail.com'>devsolver@gmail.com</a> and skype: <a href='skype:bigboss_opu'>bigboss_opu</a> &nbsp;(From Bangladesh).<br/>We also expert on product listing into <span style='color:red;'>eBay</span> and <span style='color:red;'>Amazon</span>. More info For Development Work <a href='http://sakosys.com/' target='_blank'>sakosys.com</a></div><br/>";
	echo '<form method="post" enctype="multipart/form-data">';
	echo '<div style="float:left;"><table><tr>
  <td>Upload CSV File:</td>
  <td><input type="file" name="csvfile" /></td>
</tr>
<tr>
  <td>&nbsp;<input type="hidden" name="hdntoken" value="import"></td>
  <td><input type="submit" value="Submit" class="button button-primary" id="submit" name="submit"></td>
</tr></table></div>';
echo '<div style="float:left;color:red;font-weight:bold;">Example<br/><img style="border:solid 1px #ccc;" src=' . plugins_url() . '/woo_vehicle_parts_filter_year_make_model/img/csv.png /></div>';
echo '</form><div style="clear:both;"></div>';
}


?>