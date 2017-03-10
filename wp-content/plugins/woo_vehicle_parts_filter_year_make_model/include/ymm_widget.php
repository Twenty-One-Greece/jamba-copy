<?php
/* 
Class Name: Widget Parts Dropdown

Author: Bigboss555
Author URI: http://sakosys.com
Copyright sakosys.com. All rights reserved
*/

function ymm_filter_info(){
	if(isset($_REQUEST['ymm_data'])){
		$term_id = $_REQUEST['ymm_data'];
		$termchildren = get_term_children( $term_id, 'bb_YMM_Filter' );
		foreach ( $termchildren as $child ) {
			$term = get_term_by( 'id', $child, 'bb_YMM_Filter' );
			if(isset($term->parent) && $term->parent != '' && $term->parent == $term_id){
				$output .= '<option value="'.$term->term_id.'">'.$term->name.'</option>';
			}
		}
		echo $output;
	}
	exit;
}
add_action( 'wp_ajax_nopriv_ymm_filter_info', 'ymm_filter_info' );
add_action( 'wp_ajax_ymm_filter_info', 'ymm_filter_info' );

function bbYMMFilterData($term_id,$selected){
	$termchildren = get_term_children( $term_id, 'bb_YMM_Filter' );
	foreach ( $termchildren as $child ) {
		$term = get_term_by( 'id', $child, 'bb_YMM_Filter' );
		if(isset($term->parent) && $term->parent != '' && $term->parent == $term_id){
			if($term->term_id == $selected){
				$output .= '<option selected="selected" value="'.$term->term_id.'">'.$term->name.'</option>';
			}
			else{
				$output .= '<option value="'.$term->term_id.'">'.$term->name.'</option>';
			}
		}
	}
	return $output;
}

class bbYMM_list_widget extends WP_Widget {

	function __construct() {
		parent::__construct(false, $name = __('Vehicle Year/Make/Model'));
	}
	
	function form( $instance ) {
		if(isset($instance['title'])) {
			$title  = $instance['title'];
		}
		else {
			$title  = 'Find Your Perfect Parts';
		}
		if(isset($instance['bb_year_title'])) {
			$year_title  = $instance['bb_year_title'];
		}
		else {
			$year_title  = 'Year';
		}
		if(isset($instance['bb_make_title'])) {
			$make_title  = $instance['bb_make_title'];
		}
		else {
			$make_title  = 'Make';
		}
		if(isset($instance['bb_model_title'])) {
			$model_title  = $instance['bb_model_title'];
		}
		else {
			$model_title  = 'Model';
		}
		if(isset($instance['bb_filterbutton_title'])) {
			$bb_filterbutton_title  = $instance['bb_filterbutton_title'];
		}
		else {
			$bb_filterbutton_title  = 'Filter Parts';
		}
		if(isset($instance['bb_widget_box'])) {
			$bb_widget_box  = $instance['bb_widget_box'];
		}
		else {
			$bb_widget_box  = '';
		}
		if(isset($instance['bb_button_align'])) {
			$bb_button_align  = $instance['bb_button_align'];
		}
		else {
			$bb_button_align  = 'left';
		}
		
		?>

<p>
  <label><?php echo __( 'Title:', 'bbYMMFilter' ); ?></label>
  <input class="widefat" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
</p>
<p>
  <label><?php echo __( 'Year Title:', 'bbYMMFilter' ); ?></label>
  <input class="widefat" name="<?php echo $this->get_field_name( 'year_title' ); ?>" type="text" value="<?php echo esc_attr( $year_title ); ?>" />
</p>
<p>
  <label><?php echo __( 'Make Title:', 'bbYMMFilter' ); ?></label>
  <input class="widefat" name="<?php echo $this->get_field_name( 'make_title' ); ?>" type="text" value="<?php echo esc_attr( $make_title ); ?>" />
</p>
<p>
  <label><?php echo __( 'Model Title:', 'bbYMMFilter' ); ?></label>
  <input class="widefat" name="<?php echo $this->get_field_name( 'model_title' ); ?>" type="text" value="<?php echo esc_attr( $model_title ); ?>" />
</p>
<p>
  <label><?php echo __( 'Filter Button Align:', 'bbYMMFilter' ); ?></label>
  <select class="widefat" name="<?php echo $this->get_field_name( 'bb_button_align' ); ?>">
  	<option value="left" <?php if(esc_attr( $bb_button_align ) == ''){echo 'left';}?>>Left</option>
	<option value="center" <?php if(esc_attr( $bb_button_align ) == ''){echo 'center';}?>>Center</option>
	<option value="right" <?php if(esc_attr( $bb_button_align ) == ''){echo 'right';}?>>Right</option>
  </select>
</p>
<p>
  <label><?php echo __( 'Filter Button Text:', 'bbYMMFilter' ); ?></label>
  <input class="widefat" name="<?php echo $this->get_field_name( 'bb_filterbutton_title' ); ?>" type="text" value="<?php echo esc_attr( $bb_filterbutton_title ); ?>" />
</p>
<p>
  <label><?php echo __( 'Widget Box Style:', 'bbYMMFilter' ); ?></label>
  <select class="widefat" name="<?php echo $this->get_field_name( 'bb_widget_box' ); ?>">
  	<option value="" <?php if(esc_attr( $bb_widget_box ) == ''){echo 'selected';}?>>Default Theme Style</option>
	<option value="white" <?php if(esc_attr( $bb_widget_box ) == 'white'){echo 'selected';}?>>Theme A</option>
	<option value="red" <?php if(esc_attr( $bb_widget_box ) == 'red'){echo 'selected';}?>>Theme B</option>
	<option value="blue" <?php if(esc_attr( $bb_widget_box ) == 'blue'){echo 'selected';}?>>Theme C</option>
	<option value="yellow" <?php if(esc_attr( $bb_widget_box ) == 'yellow'){echo 'selected';}?>>Theme D</option>
	<option value="green" <?php if(esc_attr( $bb_widget_box ) == 'green'){echo 'selected';}?>>Theme E</option>
	<option value="aqua" <?php if(esc_attr( $bb_widget_box ) == 'aqua'){echo 'selected';}?>>Theme F</option>
	<option value="olive" <?php if(esc_attr( $bb_widget_box ) == 'olive'){echo 'selected';}?>>Theme G</option>
  </select>
</p>
<?php
	}
	function update( $new_instance, $old_instance ) {
		$instance               			= $old_instance;
		$instance['title']      			= sanitize_text_field( $new_instance['title'] );
		$instance['bb_year_title'] 			= sanitize_text_field( $new_instance['year_title'] );
		$instance['bb_make_title'] 			= sanitize_text_field( $new_instance['make_title'] );
		$instance['bb_model_title'] 		= sanitize_text_field( $new_instance['model_title'] );
		$instance['bb_filterbutton_title']  = sanitize_text_field( $new_instance['bb_filterbutton_title'] );
		$instance['bb_widget_box'] 			= sanitize_text_field( $new_instance['bb_widget_box'] );
		$instance['bb_button_align'] 		= sanitize_text_field( $new_instance['bb_button_align'] );
		return $instance;
	}

	function widget( $args, $instance ) {
		extract( $args );
		/*here we check get info and selected based on that*/
		$_bb_get_year = '';
		$_bb_get_make = '';
		$_bb_get_model = '';
		if(isset($_GET['bb_year']) && $_GET['bb_year'] != '-1'){
			$_bb_get_year = $_GET['bb_year'];
		}
		if(isset($_GET['bb_make']) && $_GET['bb_make'] != '-1'){
			$_bb_get_make = bbYMMFilterData($_GET['bb_year'],$_GET['bb_make']);
		}
		if(isset($_GET['bb_model']) && $_GET['bb_model'] != '-1'){
			$_bb_get_model = bbYMMFilterData($_GET['bb_make'],$_GET['bb_model']);
		}
		
		$ymm_theme_style = $instance['bb_widget_box'];
		$bb_button_align = $instance['bb_button_align'];
		if ( $instance['title'] ) {
			if($ymm_theme_style != ''){
				echo "<div class='bbYMMbox'><div class='box-heading-".$ymm_theme_style."'>{$instance['title']}</div>";
			}
			else{
				echo "<h3>{$instance['title']}</h3>";
			}
		}?>
<?php 
			if($ymm_theme_style != ''){
				echo '<div class="box-content-'.$ymm_theme_style.'" align="center" style="position:relative;">';
			}
			else{
				echo '<div class="widget woocommerce" align="center" style="position:relative;">';
			}
			echo '<form class="bb_filter_form" action="'.get_site_url().'" method="get"><span id="bbymmLoader"><img src=' . plugins_url() . '/woo_vehicle_parts_filter_year_make_model/img/loader.gif /></span>';
	
			$year_list = get_terms( 'bb_YMM_Filter', array(
				'orderby'    	=> 'name',
				'parent'        => '0',
				'order'         => 'ASC',
				'hide_empty'	=> $hide_empty
			));

			if ( !empty( $year_list ) && !is_wp_error( $year_list ) ){
				echo "<div class='bbymm_year'><select class='ymm_year_box' onchange=bbYMM_create_request_widget(this,2,'".admin_url( 'admin-ajax.php' )."'); name='bb_year'><option value='-1'>{$instance['bb_year_title']}</option>";
				foreach ( $year_list as $year_item ) {
					if($_bb_get_year != '' && $_bb_get_year == $year_item->term_id){
						echo '<option selected value="'.$year_item->term_id.'">'.$year_item->name.'</option>';
					}
					else{
						echo '<option value="'.$year_item->term_id.'">'.$year_item->name.'</option>';
					}
				}
				echo '</select></div>';
				echo "<div class='bbymm_make'><select class='ymm_make_box' name='bb_make' onchange=bbYMM_create_request_widget(this,3,'".admin_url( 'admin-ajax.php' )."'); class='mmy_make_box'><option value='-1'>{$instance['bb_make_title']}</option>".$_bb_get_make."</select></div>";
				echo "<div class='bbymm_model'><select class='ymm_model_box' name='bb_model' class='mmy_model_box'><option value='-1'>{$instance['bb_model_title']}</option>".$_bb_get_model."</select></div>";
				echo "<div align='".$bb_button_align."'><input type='submit' class='button' value='{$instance['bb_filterbutton_title']}'></div><input type='hidden' name='post_type' value='product'></form>";
			}
			if($ymm_theme_style != ''){
				echo '</div>';
			}
			?>
			<script type="text/javascript">
			function mmy_default_behave(token){
				if(token == 2){
					jQuery(".ymm_make_box").html('<option value="-1"><?php echo $instance['bb_make_title']; ?></option>');
					jQuery(".ymm_model_box").html('<option value="-1"><?php echo $instance['bb_model_title']; ?></option>');
				}
				if(token == 3){
					jQuery(".ymm_model_box").html('<option value="-1"><?php echo $instance['bb_model_title']; ?></option>');
				}
			}
			</script>
</div>
<?php
	}
}
?>
