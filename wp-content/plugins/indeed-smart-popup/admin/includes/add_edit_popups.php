<?php
global $wpdb;
/************************************** DEFAULT VALUES ****************************************/
$p_name = 'Smart PopUp';//name of popup
$meta_arr = defaultValues();//the metas
$utc_arr = ips_return_utc();
$timezone_sel = array();
$the_id = ips_get_current_p_id();//current id

/*
 * Load the template 
 */
$template = '';
@$template = $_REQUEST['template'];
if (strlen($template) > 0) {
	require_once ISP_DIR_PATH.'templates/'.$template.'/data.php';
	$meta_arr['html_content'] = str_replace('isp_form_##', "isp_form_$the_id", $meta_arr['html_content']);
}


/************************************** IF EDIT ****************************************/
if(isset($_REQUEST['p_id']) && $_REQUEST['p_id']!=''){
    $p_name = ips_return_popup_name($_REQUEST['p_id']);
	$meta_arr = ips_get_metas($_REQUEST['p_id']);
    $edit = 1;
    $the_id = $_REQUEST['p_id'];
    if(isset($meta_arr['html_content'])) $meta_arr['html_content'] = stripslashes($meta_arr['html_content']);
    if(isset($meta_arr["timezone"]) && $meta_arr["timezone"]!='') $timezone_sel = unserialize($meta_arr["timezone"]);
}


$shortcode = "[indeed_popups id=$the_id]";
?>
<script>
	var dir_path = '<?php echo ISP_DIR_URL;?>';
	jQuery(document).ready(function(){
	    jQuery(".cke_wysiwyg_frame").contents().find("head").append("<style type='text/css'>html{background-color: red;}</style>");  
	});
</script>

<style type="text/css">
	#cke_1_contents, #cke_2_contents{
			margin: 0 auto;
		<?php
			if($meta_arr['height_type']=='px'){
				?>
					min-height: <?php echo $meta_arr['general_height']?>px;
				<?php 
			}
			if($meta_arr['width_type'] && $meta_arr['general_width']<802){
				?>
					max-width: <?php echo $meta_arr['general_width']+1;?>px;
				<?php 
			}
			if($meta_arr['box_bk_color']!='' && $meta_arr['bk_img_box']!='') echo "background: {$meta_arr['box_bk_color']} url({$meta_arr['bk_img_box']});";
			elseif($meta_arr['box_bk_color']!='') echo "background: {$meta_arr['box_bk_color']};";
			elseif($meta_arr['bk_img_box']!='') echo "background: url({$meta_arr['bk_img_box']});";
			?>
			background-position: <?php echo $meta_arr['box_bk_position_y'] . ' ' . $meta_arr['box_bk_position_x'];?>; 
			background-repeat: <?php echo $meta_arr['bk_box_repeat'];?>;
			<?php 
		?>
	}/*end of cke_X_contents*/
	.cke_inner{
		background-color: rgba(0,0,0,<?php echo $meta_arr['general_bk_opacity'];?>);	
	}
	.cke_1.cke.cke_reset, .cke_2.cke.cke_reset{
			background: url(<?php echo ISP_DIR_URL.'admin/assets/img/transparent.png';?>);
	}	
</style>

<div class="row-fluid" id="cl_wrap">
<form method="post" action="<?php echo admin_url().'?page=ips_admin&tab=ips_manage';?>" class="form-horizontal form-bordered">
	<div class="span6" id="cl_one">
	  <div class="box" id="isp_wrap_options_div" style="/*width: <?php echo $meta_arr['general_width']+20;?>px*/">		
        <div class="box-content box_wrapp">
			<div class="add-popup-wrapper" id="a1">
				
				<div class="isp-left-menu">
				<div class="isp-left-top"><i class="icon-th-list"></i>PopUp Configuration</div>
				<ul class="isp-left-ul-menu-add-edit-admin">  
					<li class="isp-hover"><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#a1" href="#b1">
						    <i class="icon-form-background"></i>General Options
					</a><span class="hover-bk isp_bkcolor10"></span></li>
					<li> <a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#a1" href="#b7">
						    <i class="icon-event"></i>Event Handler
					</a><span class="hover-bk isp_bkcolor9"></span></li>
					<li><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#a1" href="#b6">
						    <i class="icon-form-show_in"></i>Smart Targeting Display
						</a><span class="hover-bk isp_bkcolor3"></span></li>
					<li><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#a1" href="#b10">
						    <i class="icon-scheduale"></i>Scheduling Time
						</a><span class="hover-bk isp_bkcolor4"></span></li>
					<li><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#a1" href="#b8">
						    <i class="icon-mobile"></i>Mobile Features
						</a><span class="hover-bk isp_bkcolor5"></span></li>
					<li><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#a1" href="#b2">
						    <i class="icon-out"></i>Close Button
						</a><span class="hover-bk isp_bkcolor7"></span></li>
					<li><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#a1" href="#b9">
						    <i class="icon-show_in"></i>Show In
						</a><span class="hover-bk isp_bkcolor6"></span></li>
					<li><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#a1" href="#b3">
						    <i class="icon-fields"></i>Content Box
						</a><span class="hover-bk isp_bkcolor1"></span></li>
					<li><a class="accordion-toggle collapsed" data-toggle="collapse" data-parent="#a1" href="#b4">
						    <i class="icon-submit"></i>Content Options
						</a><span class="hover-bk isp_bkcolor2"></span></li>
				</ul>
				</div>
				<div class="isp-right-options">				
				  <div class="isp-right-options-inner">
				   <div class="add-popup-top">
					<div class="left-side">					
					<!----------------------------PopUp NAME-------------------------------------------->
						PopUp Name
						<div class="input-group">
							<span class="before-input"> <i class="icon-isp-home"></i></span>
							<input type="text" class="" value="<?php echo $p_name;?>" name="p_name" onClick="deleteFieldVal(this, 'Untitled');" onBlur="noEmptyField(this, 'Untitled');"/>
						</div>
					</div>
					<div class="right-side">
					 <!----------------------------PopUp ShortCode-------------------------------------------->
					  <?php echo $shortcode;?>	
					</div>
					<div class="clear"></div>
				   </div>
					<div id="b1" class="accordion-body collapse option_box" style="height: auto;">
					<div class="isp_info_block">Set your PopUp Position (static or dinamic) and the Dimension based on pixels or %.</div>
                        <div class="control-group">
                            <label for="" class="control-label">Position</label>
                                <div class="controls">
                                    Wrapper:
                                        <div style="">
                                            <?php
                                                $checked = '';
                                                if($meta_arr['position_parent']=='absolute') $checked = 'checked="checked"';
                                            ?>
                                            <div class="check-line" style="float:left; width:100px;">
                                                <input type="radio" class="icheck-me" name="position_parent_a" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#position_parent_type').val('absolute');"/> <label class="inline">Absolute</label>
                                            </div>
                                            <?php
                                                $checked = '';
                                                if($meta_arr['position_parent']=='fixed') $checked = 'checked="checked"';
                                            ?>
                                            <div class="check-line" style="float:left; width:100px;">
                                                <input type="radio" class="icheck-me" name="position_parent_a" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#position_parent_type').val('fixed');"/> <label class="inline">Fixed</label>
                                            </div>
                                            <input type="hidden" id="position_parent_type" value="<?php echo $meta_arr['position_parent'];?>" name="ips_meta[position_parent]" />
									<div class="clear"></div>	
                                        </div>
									<i class="icon-info info-second"></i><span class="info info-second">"Fixed" keeps the popup on screen all the time unconcerned by scroll action.</span>	
                                </div>
                                <div class="controls">
                                    PopUp:
                                                <div style="">
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['position']=='relative') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:100px;">
                                                          <input type="radio" class="icheck-me" name="position" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#position_type').val('relative');" /> <label class="inline">Relative</label>
                                                      </div>
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['position']=='absolute') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:100px;">
                                                          <input type="radio" class="icheck-me" name="position" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#position_type').val('absolute');" /> <label class="inline">Absolute</label>
                                                      </div>
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['position']=='fixed') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:100px;">
                                                          <input type="radio" class="icheck-me" name="position" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#position_type').val('fixed');" /> <label class="inline">Fixed</label>
                                                      </div>
                                                          <input type="hidden" id="position_type" value="<?php echo $meta_arr['position'];?>" name="ips_meta[position]" />
                                                      <div class="clear"></div>
                                                </div>
												  <i class="icon-info"></i><span class="info">For Mobile Version the "Relative" option is desirable</span>
                                </div>
                                <div class="controls">
                                        <div class="check-line">
                                            <?php
                                                $checked = '';
                                                if($meta_arr['auto_center']==1) $checked = 'checked="checked"';
                                            ?>
                                            <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#auto_center')" />  <label class="inline">Auto-Center</label>
                                            <input type="hidden" value="<?php echo $meta_arr['auto_center'];?>" name="ips_meta[auto_center]" id="auto_center" />
                                        </div>
										<i class="icon-info"></i><span class="info">Available only with Position: "Relative" selected</span>
                                </div>
                                <div class="controls">
                                    <select onChange="changePos_tb(this, 'bt');">
                                    <?php
                                        $select = "";
                                        if($meta_arr['gt_top_bottom']=='top') $select = 'selected="selected"';
                                    ?>
                                            <option value="top" <?php echo $select;?> >Top</option>
                                    <?php
                                        $select = "";
                                        if($meta_arr['gt_top_bottom']=='bottom') $select = 'selected="selected"';
                                    ?>
                                            <option value="bottom" <?php echo $select;?> >Bottom</option>
                                    </select>
                                      <?php
                                            $display = 'none';
                                            if($meta_arr['gt_top_bottom']=='top') $display = 'block';
                                      ?>
                                      <div id="pos_tb_top" style="display: <?php echo $display;?>;">
                                                <?php
                                                    $display = 'none';
                                                    if($meta_arr['bt_top_type']=='px') $display = 'block';
                                                ?>
                                                <div id="pos_tb_top_px" style="display: <?php echo $display;?>;">
                                                      <div>Top: <span id="top_ui_v"><?php echo $meta_arr['general_top'];?></span>px</div>
                                                      <div style="width: 470px; float: left;">
                                                            <div id="general_top_size" title="<?php echo $meta_arr['general_top'];?>px" style=""></div>
                                                                <script type="text/javascript">
                                                                jQuery(document).ready(function() {
                                                                      var from = "#general_top_size";
                                                                      var hidden_i = "#general_top";
                                                                      jQuery(from).slider({
                                                                      min: -800, //minimum value
                                                                      max: 800, //maximum value
                                                                      value: "<?php echo $meta_arr['general_top'];?>", //default value
                                                                      slide: function(event, ui) {
                                                                        var the_val = ui.value;
                                                                        jQuery(hidden_i).val(the_val);
                                                                        jQuery(from).attr("title", the_val+"px");
                                                                        jQuery("#top_ui_v").html(the_val);
                                                                      }
                                                                  });
                                                                });
                                                                </script>
                                                            <input type="hidden" value="<?php echo $meta_arr['general_top'];?>" name="ips_meta[general_top]" id="general_top" />
                                                      </div>
                                                </div>
                                                <?php
                                                    $display = 'none';
                                                    if($meta_arr['bt_top_type']=='percent') $display = 'block';
                                                ?>
                                                <div id="pos_tb_top_percent" style="display: <?php echo $display;?>;">
                                                      <div>Top: <span id="top_ui_v_pecent"><?php echo $meta_arr['general_top_percent'];?></span>%</div>
                                                      <div style="width: 470px; float: left;">
                                                            <div id="general_top_size_percent" title="<?php echo $meta_arr['general_top_percent'];?>%" style=""></div>
                                                                <script type="text/javascript">
                                                                jQuery(document).ready(function() {
                                                                      var from = "#general_top_size_percent";
                                                                      var hidden_i = "#general_top_percent";
                                                                      jQuery(from).slider({
                                                                      min: -50, //minimum value
                                                                      max: 50, //maximum value
                                                                      value: "<?php echo $meta_arr['general_top_percent'];?>", //default value
                                                                      slide: function(event, ui) {
                                                                        var the_val = ui.value;
                                                                        jQuery(hidden_i).val(the_val);
                                                                        jQuery(from).attr("title", the_val+"px");
                                                                        jQuery("#top_ui_v_pecent").html(the_val);
                                                                      }
                                                                  });
                                                                });
                                                                </script>
                                                            <input type="hidden" value="<?php echo $meta_arr['general_top_percent'];?>" name="ips_meta[general_top_percent]" id="general_top_percent" />
                                                      </div>
                                                </div>
                                                 <div style="float:left;margin-left: 25px;">
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['bt_top_type']=='px') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="bt_top_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#bt_top_type').val('px');jQuery('#pos_tb_top_px').css('display', 'block');jQuery('#pos_tb_top_percent').css('display', 'none');" /> <label class="inline">px</label>
                                                      </div>
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['bt_top_type']=='percent') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="bt_top_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#bt_top_type').val('percent');jQuery('#pos_tb_top_px').css('display', 'none');jQuery('#pos_tb_top_percent').css('display', 'block');" /> <label class="inline">%</label>
                                                      </div>
                                                          <input type="hidden" id="bt_top_type" value="<?php echo $meta_arr['bt_top_type'];?>" name="ips_meta[bt_top_type]" />
                                                      <div class="clear"></div>
                                                  </div>
                                            <div class="clear"></div>
                                      </div>
                                      <?php
                                            $display = 'none';
                                            if($meta_arr['gt_top_bottom']=='bottom') $display = 'block';
                                      ?>
                                      <div id="pos_tb_bottom" style="display: <?php echo $display;?>;">
                                                <?php
                                                    $display = 'none';
                                                    if($meta_arr['bt_bottom_type']=='px') $display = 'block';
                                                ?>
                                                <div id="pos_tb_bottom_px" style="display: <?php echo $display;?>;">
                                                      <div>Bottom: <span id="bottom_ui_v"><?php echo $meta_arr['general_bottom'];?></span>px</div>
                                                      <div style="width: 470px; float: left;">
                                                            <div id="general_bottom_size" title="<?php echo $meta_arr['general_bottom'];?>px"></div>
                                                                <script type="text/javascript">
                                                                jQuery(document).ready(function() {
                                                                      var from = "#general_bottom_size";
                                                                      var hidden_i = "#general_bottom";
                                                                      jQuery(from).slider({
                                                                      min: -800, //minimum value
                                                                      max: 800, //maximum value
                                                                      value: "<?php echo $meta_arr['general_bottom'];?>", //default value
                                                                      slide: function(event, ui) {
                                                                        var the_val = ui.value;
                                                                        jQuery(hidden_i).val(the_val);
                                                                        jQuery(from).attr("title", the_val+"px");
                                                                        jQuery("#bottom_ui_v").html(the_val);
                                                                      }
                                                                  });
                                                                });
                                                                </script>
                                                            <input type="hidden" value="<?php echo $meta_arr['general_bottom'];?>" name="ips_meta[general_bottom]" id="general_bottom" />
                                                      </div>
                                                </div>
                                                <?php
                                                    $display = 'none';
                                                    if($meta_arr['bt_bottom_type']=='percent') $display = 'block';
                                                ?>
                                                <div id="pos_tb_bottom_percent" style="display: <?php echo $display;?>;">
                                                      <div>Bottom: <span id="bottom_ui_v_pecent"><?php echo $meta_arr['general_bottom_percent'];?></span>%</div>
                                                      <div style="width: 470px; float: left;">
                                                            <div id="general_bottom_size_percent" title="<?php echo $meta_arr['general_bottom_percent'];?>%" style=""></div>
                                                                <script type="text/javascript">
                                                                jQuery(document).ready(function() {
                                                                      var from = "#general_bottom_size_percent";
                                                                      var hidden_i = "#general_bottom_percent";
                                                                      jQuery(from).slider({
                                                                      min: -50, //minimum value
                                                                      max: 50, //maximum value
                                                                      value: "<?php echo $meta_arr['general_bottom_percent'];?>", //default value
                                                                      slide: function(event, ui) {
                                                                        var the_val = ui.value;
                                                                        jQuery(hidden_i).val(the_val);
                                                                        jQuery(from).attr("title", the_val+"px");
                                                                        jQuery("#bottom_ui_v_pecent").html(the_val);
                                                                      }
                                                                  });
                                                                });
                                                                </script>
                                                            <input type="hidden" value="<?php echo $meta_arr['general_bottom_percent'];?>" name="ips_meta[general_bottom_percent]" id="general_bottom_percent" />
                                                      </div>
                                                </div>
                                                 <div style="float:left;margin-left: 25px;">
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['bt_bottom_type']=='px') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="bt_bottom_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#bt_bottom_type').val('px');jQuery('#pos_tb_bottom_px').css('display', 'block');jQuery('#pos_tb_bottom_percent').css('display', 'none');" /> <label class="inline">px</label>
                                                      </div>
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['bt_bottom_type']=='percent') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="bt_bottom_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#bt_bottom_type').val('percent');jQuery('#pos_tb_bottom_px').css('display', 'none');jQuery('#pos_tb_bottom_percent').css('display', 'block');" /> <label class="inline">%</label>
                                                      </div>
                                                          <input type="hidden" id="bt_bottom_type" value="<?php echo $meta_arr['bt_bottom_type'];?>" name="ips_meta[bt_bottom_type]" />
                                                      <div class="clear"></div>
                                                  </div>
                                            <div class="clear"></div>
                                      </div>
                                      <input type="hidden" value="<?php echo $meta_arr['gt_top_bottom'];?>" name="ips_meta[gt_top_bottom]" id="gt_top_bottom"/>
                                	<i class="icon-info"></i><span class="info">For Mobile Devices the "%" option is desirable</span>	
								</div>
								
                                <div class="controls">
                                    <select onChange="changePos_tb(this, 'rl');">
                                        <?php
                                            $select = '';
                                            if($meta_arr['gt_right_left']=='left') $select = 'selected="selected"';
                                        ?>
                                            <option value="left" <?php echo $select;?> >Left</option>
                                        <?php
                                            $select = '';
                                            if($meta_arr['gt_right_left']=='right') $select = 'selected="selected"';
                                        ?>
                                            <option value="right" <?php echo $select;?> >Right</option>
                                    </select>
                                      <?php
                                            $display = 'none';
                                            if($meta_arr['gt_right_left']=='left') $display = 'block';
                                      ?>
                                      <div id="pos_rl_left" style="display: <?php echo $display;?>;">
                                                <?php
                                                    $display = 'none';
                                                    if($meta_arr['rl_left_type']=='px') $display = 'block';
                                                ?>
                                                <div id="pos_rl_left_px" style="display: <?php echo $display;?>;">
                                                      <div>Left: <span id="left_ui_v"><?php echo $meta_arr['general_left'];?></span>px</div>
                                                      <div style="width: 470px; float: left;">
                                                            <div id="general_left_size" title="<?php echo $meta_arr['general_left'];?>px" style=""></div>
                                                                <script type="text/javascript">
                                                                jQuery(document).ready(function() {
                                                                      var from = "#general_left_size";
                                                                      var hidden_i = "#general_left";
                                                                      jQuery(from).slider({
                                                                      min: -800, //minimum value
                                                                      max: 800, //maximum value
                                                                      value: "<?php echo $meta_arr['general_left'];?>", //default value
                                                                      slide: function(event, ui) {
                                                                        var the_val = ui.value;
                                                                        jQuery(hidden_i).val(the_val);
                                                                        jQuery(from).attr("title", the_val+"px");
                                                                        jQuery("#left_ui_v").html(the_val);
                                                                      }
                                                                  });
                                                                });
                                                                </script>
                                                            <input type="hidden" value="<?php echo $meta_arr['general_left'];?>" name="ips_meta[general_left]" id="general_left" />
                                                      </div>
                                                </div>
                                                <?php
                                                    $display = 'none';
                                                    if($meta_arr['rl_left_type']=='percent') $display = 'block';
                                                ?>
                                                <div id="pos_rl_left_percent" style="display: <?php echo $display;?>;">
                                                      <div>Left: <span id="left_ui_v_pecent"><?php echo $meta_arr['general_left_percent'];?></span>%</div>
                                                      <div style="width: 470px; float: left;">
                                                            <div id="general_left_size_percent" title="<?php echo $meta_arr['general_left_percent'];?>%" style=""></div>
                                                                <script type="text/javascript">
                                                                jQuery(document).ready(function() {
                                                                      var from = "#general_left_size_percent";
                                                                      var hidden_i = "#general_left_percent";
                                                                      jQuery(from).slider({
                                                                      min: -50, //minimum value
                                                                      max: 50, //maximum value
                                                                      value: "<?php echo $meta_arr['general_left_percent'];?>", //default value
                                                                      slide: function(event, ui) {
                                                                        var the_val = ui.value;
                                                                        jQuery(hidden_i).val(the_val);
                                                                        jQuery(from).attr("title", the_val+"px");
                                                                        jQuery("#left_ui_v_pecent").html(the_val);
                                                                      }
                                                                  });
                                                                });
                                                                </script>
                                                            <input type="hidden" value="<?php echo $meta_arr['general_left_percent'];?>" name="ips_meta[general_left_percent]" id="general_left_percent" />
                                                      </div>
                                                </div>
                                                 <div style="float:left;margin-left: 25px;">
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['rl_left_type']=='px') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="rl_left_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#rl_left_type').val('px');jQuery('#pos_rl_left_px').css('display', 'block');jQuery('#pos_rl_left_percent').css('display', 'none');" /> <label class="inline">px</label>
                                                      </div>
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['rl_left_type']=='percent') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="rl_left_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#rl_left_type').val('percent');jQuery('#pos_rl_left_px').css('display', 'none');jQuery('#pos_rl_left_percent').css('display', 'block');" /> <label class="inline">%</label>
                                                      </div>
                                                          <input type="hidden" id="rl_left_type" value="<?php echo $meta_arr['rl_left_type'];?>" name="ips_meta[rl_left_type]" />
                                                      <div class="clear"></div>
                                                  </div>
                                            <div class="clear"></div>
                                      </div>
                                      <?php
                                            $display = 'none';
                                            if($meta_arr['gt_right_left']=='right') $display = 'block';
                                      ?>
                                      <div id="pos_rl_right" style="display: <?php echo $display;?>;">
                                                <?php
                                                    $display = 'none';
                                                    if($meta_arr['rl_right_type']=='px') $display = 'block';
                                                ?>
                                                <div id="pos_rl_right_px" style="display: <?php echo $display;?>;">
                                                      <div>Right: <span id="right_ui_v"><?php echo $meta_arr['general_right'];?></span>px</div>
                                                      <div style="width: 470px; float: left;">
                                                            <div id="general_right_size" title="<?php echo $meta_arr['general_right'];?>px" style=""></div>
                                                                <script type="text/javascript">
                                                                jQuery(document).ready(function() {
                                                                      var from = "#general_right_size";
                                                                      var hidden_i = "#general_right";
                                                                      jQuery(from).slider({
                                                                      min: -800, //minimum value
                                                                      max: 800, //maximum value
                                                                      value: "<?php echo $meta_arr['general_right'];?>", //default value
                                                                      slide: function(event, ui) {
                                                                        var the_val = ui.value;
                                                                        jQuery(hidden_i).val(the_val);
                                                                        jQuery(from).attr("title", the_val+"px");
                                                                        jQuery("#right_ui_v").html(the_val);
                                                                      }
                                                                  });
                                                                });
                                                                </script>
                                                            <input type="hidden" value="<?php echo $meta_arr['general_right'];?>" name="ips_meta[general_right]" id="general_right" />
                                                      </div>
                                                </div>
                                                <?php
                                                    $display = 'none';
                                                    if($meta_arr['rl_right_type']=='percent') $display = 'block';
                                                ?>
                                                <div id="pos_rl_right_percent" style="display: <?php echo $display;?>;">
                                                      <div>Right: <span id="right_ui_v_pecent"><?php echo $meta_arr['general_right_percent'];?></span>%</div>
                                                      <div style="width: 470px; float: left;">
                                                            <div id="general_right_size_percent" title="<?php echo $meta_arr['general_right_percent'];?>%" style=""></div>
                                                                <script type="text/javascript">
                                                                jQuery(document).ready(function() {
                                                                      var from = "#general_right_size_percent";
                                                                      var hidden_i = "#general_right_percent";
                                                                      jQuery(from).slider({
                                                                      min: -50, //minimum value
                                                                      max: 50, //maximum value
                                                                      value: "<?php echo $meta_arr['general_right_percent'];?>", //default value
                                                                      slide: function(event, ui) {
                                                                        var the_val = ui.value;
                                                                        jQuery(hidden_i).val(the_val);
                                                                        jQuery(from).attr("title", the_val+"px");
                                                                        jQuery("#right_ui_v_pecent").html(the_val);
                                                                      }
                                                                  });
                                                                });
                                                                </script>
                                                            <input type="hidden" value="<?php echo $meta_arr['general_right_percent'];?>" name="ips_meta[general_right_percent]" id="general_right_percent" />
                                                      </div>
                                                </div>
                                                 <div style="float:left;margin-left: 25px;">
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['rl_right_type']=='px') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="rl_right_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#rl_right_type').val('px');jQuery('#pos_rl_right_px').css('display', 'block');jQuery('#pos_rl_right_percent').css('display', 'none');" /> <label class="inline">px</label>
                                                      </div>
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['rl_right_type']=='percent') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="rl_right_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#rl_right_type').val('percent');jQuery('#pos_rl_right_px').css('display', 'none');jQuery('#pos_rl_right_percent').css('display', 'block');" /> <label class="inline">%</label>
                                                      </div>
                                                          <input type="hidden" id="rl_right_type" value="<?php echo $meta_arr['rl_right_type'];?>" name="ips_meta[rl_right_type]" />
                                                      <div class="clear"></div>
                                                  </div>
                                            <div class="clear"></div>
                                      </div>
                                      <input type="hidden" value="<?php echo $meta_arr['gt_right_left'];?>" name="ips_meta[gt_right_left]" id="gt_right_left"/>
                                </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Dimension (Size)</label>
                                 <div class="controls">
                                     <?php
                                        $display = 'none';
                                        if($meta_arr['width_type']=='px') $display = 'block';
                                    ?>
                                    <div id="px_width" style="width: 470px; float: left; display: <?php echo $display;?>">
                                        <div>Width: <span id="width_t_ui_v"><?php echo $meta_arr['general_width'];?></span>px</div>
                                        <div id="general_w" title="<?php echo $meta_arr['general_width'];?>px"></div>
                                            <script type="text/javascript">
                                            jQuery(document).ready(function() {
                                                  var from = "#general_w";
                                                  var hidden_i = "#general_width";
                                                  jQuery(from).slider({
                                                  min: 10, //minimum value
                                                  max: 1600, //maximum value
                                                  value: "<?php echo $meta_arr['general_width'];?>", //default value
                                                  slide: function(event, ui) {
                                                    var the_val = ui.value;
													add_val = (parseInt(the_val)+parseInt('20'));
													
                                                    jQuery(hidden_i).val(the_val);
                                                    jQuery(from).attr("title", the_val+"px");
                                                    jQuery("#width_t_ui_v").html(the_val);
                                                    jQuery("#span_bk_width").html(the_val);
                                                    //jQuery('#isp_wrap_options_div').css('width', add_val);
                                                    val = parseInt(add_val)+1;
                                                    jQuery('#cke_1_contents, #cke_2_contents').css('max-width', val);//redimension for editor
                                                    jQuery('.wrap-the-ckeditor-resizer').css('width', add_val);                                                    
                                                  }
                                              });
                                            });
                                            </script>
                                        <input type="hidden" value="<?php echo $meta_arr['general_width'];?>" name="ips_meta[general_width]" id="general_width" />
                                    </div>
                                    <?php
                                        $display = 'none';
                                        if($meta_arr['width_type']=='percent') $display = 'block';
                                    ?>
                                    <div id="percent_width" style="width: 470px; float: left;display: <?php echo $display;?>">
                                            <div>Width: <span id="width_t_ui_v_percent"><?php echo $meta_arr['general_width_percent'];?></span>%</div>
                                            <div id="general_w_percent" title="<?php echo $meta_arr['general_width_percent'];?>%"></div>
                                                <script type="text/javascript">
                                                jQuery(document).ready(function() {
                                                      var from = "#general_w_percent";
                                                      var hidden_i = "#general_width_percent";
                                                      jQuery(from).slider({
                                                      min: 1, //minimum value
                                                      max: 100, //maximum value
                                                      value: "<?php echo $meta_arr['general_width_percent'];?>", //default value
                                                      slide: function(event, ui) {
                                                        var the_val = ui.value;
                                                        jQuery(hidden_i).val(the_val);
                                                        jQuery(from).attr("title", the_val+"%");
                                                        jQuery("#width_t_ui_v_percent").html(the_val);
                                                        jQuery("#span_bk_width_percent").html(the_val);
                                                      }
                                                  });
                                                });
                                                </script>
                                            <input type="hidden" value="<?php echo $meta_arr['general_width_percent'];?>" name="ips_meta[general_width_percent]" id="general_width_percent" />
                                    </div>
                                                <div style="float:left;margin-left: 25px; margin-top: 15px;">
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['width_type']=='px') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="width_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#width_type').val('px');jQuery('#percent_width').css('display', 'none');jQuery('#px_width').css('display', 'block');" /> <label class="inline">px</label>
                                                      </div>
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['width_type']=='percent') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="width_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#width_type').val('percent');jQuery('#percent_width').css('display', 'block');jQuery('#px_width').css('display', 'none');" /> <label class="inline">%</label>
                                                      </div>
                                                          <input type="hidden" id="width_type" value="<?php echo $meta_arr['width_type'];?>" name="ips_meta[width_type]" />
                                                      <div class="clear"></div>
                                                </div>
                                            <div class="clear"></div>
                                </div>
                                <div class="controls">
                                     <?php
                                        $display = 'none';
                                        if($meta_arr['height_type']=='px') $display = 'block';
                                    ?>
                                    <div id="px_height" style="width: 470px; float: left; display: <?php echo $display;?>">
											<div>Height: <span id="height_t_ui_v"><?php echo $meta_arr['general_height'];?></span>px</div>
											<div id="general_h" title="<?php echo $meta_arr['general_height'];?>px"></div>
												<script type="text/javascript">
												jQuery(document).ready(function() {
													  var from = "#general_h";
													  var hidden_i = "#general_height";
													  jQuery(from).slider({
													  min: 10, //minimum value
													  max: 1600, //maximum value
													  value: "<?php echo $meta_arr['general_height'];?>", //default value
													  slide: function(event, ui) {
														var the_val = ui.value;
														jQuery(hidden_i).val(the_val);
														jQuery(from).attr("title", the_val+"px");
														jQuery("#height_t_ui_v").html(the_val);
														jQuery("#span_bk_height").html(the_val);
														jQuery('#cke_1_contents').css('min-height', the_val);
														jQuery('#cke_2_contents').css('min-height', the_val);
													  }
												  });
												});
												</script>
											<input type="hidden" value="<?php echo $meta_arr['general_height'];?>" name="ips_meta[general_height]" id="general_height" />
                                    </div>
                                    <?php
                                        $display = 'none';
                                        if($meta_arr['height_type']=='percent') $display = 'block';
                                    ?>
                                    <div id="percent_height" style="width: 470px; float: left;display: <?php echo $display;?>">
											<div>Height: <span id="height_t_ui_v_percent"><?php echo $meta_arr['general_height_percent'];?></span>%</div>
											<div id="general_h_percent" title="<?php echo $meta_arr['general_height_percent'];?>%"></div>
												<script type="text/javascript">
												jQuery(document).ready(function() {
													  var from = "#general_h_percent";
													  var hidden_i = "#general_height_percent";
													  jQuery(from).slider({
													  min: 1, //minimum value
													  max: 100, //maximum value
													  value: "<?php echo $meta_arr['general_height_percent'];?>", //default value
													  slide: function(event, ui) {
														var the_val = ui.value;
														jQuery(hidden_i).val(the_val);
														jQuery(from).attr("title", the_val+"%");
														jQuery("#height_t_ui_v_percent").html(the_val);
														jQuery("#span_bk_height").html(the_val);
													  }
												  });
												});
												</script>
											<input type="hidden" value="<?php echo $meta_arr['general_height_percent'];?>" name="ips_meta[general_height_percent]" id="general_height_percent" />
                                    </div>
                                                <div style="float:left;margin-left: 25px; margin-top: 15px;">
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['height_type']=='px') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="height_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#height_type').val('px');jQuery('#percent_height').css('display', 'none');jQuery('#px_height').css('display', 'block');" /> <label class="inline">px</label>
                                                      </div>
                                                      <?php
                                                          $checked = '';
                                                          if($meta_arr['height_type']=='percent') $checked = 'checked="checked"';
                                                      ?>
                                                      <div class="check-line" style="float:left; width:50px;">
                                                          <input type="radio" class="icheck-me" name="height_type" data-skin="minimal" <?php echo $checked;?> onClick="jQuery('#height_type').val('percent');jQuery('#percent_height').css('display', 'block');jQuery('#px_height').css('display', 'none');" /> <label class="inline">%</label>
                                                      </div>
                                                          <input type="hidden" id="height_type" value="<?php echo $meta_arr['height_type'];?>" name="ips_meta[height_type]" />
                                                      <div class="clear"></div>
                                                </div>
                                            <div class="clear"></div>
                                </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Background Opacity</label>
                                <div class="controls">
                                    <div>Opacity: <span id="opacity_ui_v"><?php echo $meta_arr['general_bk_opacity'];?></span></div>
                                    <div id="general_opacity" title="<?php echo $meta_arr['general_bk_opacity'];?>"></div>
                                        <script type="text/javascript">
                                        jQuery(document).ready(function() {
                                              var from = "#general_opacity";
                                              var hidden_i = "#general_bk_opacity";
                                              jQuery(from).slider({
                                              min: 0, //minimum value
                                              max: 1, //maximum value
                                              step: 0.01,
                                              value: "<?php echo $meta_arr['general_bk_opacity'];?>", //default value
                                              slide: function(event, ui) {
                                                var the_val = ui.value;
                                                jQuery(hidden_i).val(the_val);
                                                jQuery(from).attr("title", the_val);
                                                jQuery("#opacity_ui_v").html(the_val);
                                                jQuery('.cke_inner').css('background-color', 'rgba(0,0,0,'+the_val+')');
                                              }
                                          });
                                        });
                                        </script>
                                    <input type="hidden" value="<?php echo $meta_arr['general_bk_opacity'];?>" name="ips_meta[general_bk_opacity]" id="general_bk_opacity" />
                                </div>
                        </div>
						<div class="control-group">
                            <label for="" class="control-label">Background Image URL</label>
                            <div class="controls">
                                <input type="text" value="<?php echo $meta_arr['bk_img_general'];?>" class="isp_media_upload_input" id="bk_img_general" name="ips_meta[bk_img_general]" onClick="ips_open_media_up(this);" />
                                <span class="clear_color_picker" onclick="jQuery('#bk_img_general').val('');"><i class="icon-clear-e"></i></span>
                            </div>
                        </div>
						<div class="control-group">
                            <label for="" class="control-label">Disable Background</label>
                                <div class="controls">
                                <?php
                                    $checked = '';
                                    if($meta_arr['disable_bk']==1) $checked = 'checked="checked"';
                                ?>
                                  <div class="check-line">
                                      <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#disable_bkn')"/>
                                      <input type="hidden" value="<?php echo $meta_arr['disable_bk'];?>" name="ips_meta[disable_bk]" id="disable_bkn"/>
                                  </div>
                                </div>
                        </div>
          
                     </div>
				 	<div id="b2" class="accordion-body collapse option_box" style="height: 0px;">					
                    <div class="isp_info_block">Decide how and if the PopUp will be close by your visitors. You can restrict some main closing options.</div>
                        <div class="control-group">
                            <label for="" class="control-label">Choice Design</label>
                                <div class="controls">
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_1.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio" class="icheck-me" data-skin="minimal" value="close_1.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);"/> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_1.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_2.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_2.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_2.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_3.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_3.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_3.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_4.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_4.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_4.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_5.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_5.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_5.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_6.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_6.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_6.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_7.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_7.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_7.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_8.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_8.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_8.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_9.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_9.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_9.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_10.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_10.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_10.png" class="close_bttn_v" /></label>
                                </div>
                                <?php
                                    $checked = '';
                                    if($meta_arr['close_design']=='close_12.png') $checked = 'checked="checked"';
                                ?>
                                <div class="check-line">
                                    <input type="radio"  class="icheck-me" data-skin="minimal" value="close_12.png" name="close_bttn_i" <?php echo $checked;?> onChange="jQuery('#close_design').val(this.value);" /> <label class="inline"><img src="<?php echo ISP_DIR_URL;?>assets/img/close_12.png" class="close_bttn_v" /></label>
                                </div>
                                    <input type="hidden" value="<?php echo $meta_arr['close_design'];?>" id="close_design" name="ips_meta[close_design]" />
                                </div>
                        </div>
						<div class="control-group">
                            <label for="" class="control-label">Disable Close Button</label>
                                <div class="controls">
                                <?php
                                    $checked = '';
                                    if($meta_arr['disable_button']==1) $checked = 'checked="checked"';
                                ?>
                                  <div class="check-line">
                                      <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#disable_btn')"/>
                                      <input type="hidden" value="<?php echo $meta_arr['disable_button'];?>" name="ips_meta[disable_button]" id="disable_btn"/>
                                  </div>
								  <div class="clear"></div>
								  <i class="icon-info info-second"></i><span class="info info-second">Desirable for Locker PopUps</span>
                                </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Position</label>
                                <div class="controls">
                                    <select name="ips_meta[close_position]">
                                        <?php
                                            $selected = '';
                                            if($meta_arr["close_position"] == 'top-left') $selected = 'selected="selected"';
                                        ?>
                                        <option value="top-left" <?php echo $selected;?>>Top-Left</option>
                                        <?php
                                            $selected = '';
                                            if($meta_arr["close_position"] == 'top-right') $selected = 'selected="selected"';
                                        ?>
                                        <option value="top-right" <?php echo $selected;?>>Top-Right</option>
                                        <?php
                                            $selected = '';
                                            if($meta_arr["close_position"] == 'bottom-left') $selected = 'selected="selected"';
                                        ?>
                                        <option value="bottom-left" <?php echo $selected;?>>Bottom-Left</option>
                                        <?php
                                            $selected = '';
                                            if($meta_arr["close_position"] == 'bottom-right') $selected = 'selected="selected"';
                                        ?>
                                        <option value="bottom-right" <?php echo $selected;?>>Bottom-Right</option>
                                    </select>
                                </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Additional Custom Position</label>
                                <div class="controls">
                                    <table>
                                    <tr>
                                        <td class="td_acp_label">Top:</td>
                                        <td class="td_acp_field" ><input type="number" class="num_field" value="<?php echo $meta_arr['abp_top'];?>" name="ips_meta[abp_top]" /></td>
                                        <td class="td_acp_label">Left:</td>
                                        <td class="td_acp_field" ><input type="number" class="num_field" value="<?php echo $meta_arr['abp_left'];?>" name="ips_meta[abp_left]" /></td>
                                    </tr>
                                    <tr>
                                        <td class="td_acp_label" >Bottom:</td>
                                        <td class="td_acp_field" ><input type="number" class="num_field" value="<?php echo $meta_arr['abp_bottom'];?>" name="ips_meta[abp_bottom]" /></td>
                                        <td class="td_acp_label" >Right:</td>
                                        <td class="td_acp_field" ><input type="number" class="num_field" value="<?php echo $meta_arr['abp_right'];?>" name="ips_meta[abp_right]" /></td>
                                    </tr>
                                    </table>
                                </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Disable Escape (Esc) Key</label>
                                <div class="controls">
                                <?php
                                    $checked = '';
                                    if($meta_arr['disable_escape']==1) $checked = 'checked="checked"';
                                ?>
                                  <div class="check-line">
                                      <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#disable_esc')"/>
                                      <input type="hidden" value="<?php echo $meta_arr['disable_escape'];?>" name="ips_meta[disable_escape]" id="disable_esc"/>
                                  </div>
								  <div class="clear"></div>
								  <i class="icon-info info-second"></i><span class="info info-second">Desirable for Locker PopUps</span>
                                </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Disable Click Out</label>
                                <div class="controls">
                                <?php
                                    $checked = '';
                                    if($meta_arr['disable_clickout']==1) $checked = 'checked="checked"';
                                ?>
                                    <div class="check-line" style="float:left; top:6px; margin-right:0px; min-width: 30px;">
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#click_out_h')"/>
                                        <input type="hidden" value="<?php echo $meta_arr['disable_clickout'];?>" name="ips_meta[disable_clickout]" id="click_out_h"/>
                                    </div>
									<div style="float:left; margin-top:6px;"><i class="icon-info"></i><span class="info">Desirable for multiple PopUps on the same page!</span></div>
                                </div>
                        </div>
						<div class="control-group">
                            <label for="" class="control-label">Loker Code</label>
                                <div class="controls">
                                
                                    <div class="check-line">
                                        <input type="text" value='onClick="locker(<?php echo $the_id;?>);"' disabled="disabled" style="width:220px; height:26px;">
										<div class="clear"></div>
										<i class="icon-info"></i><span class="info">Set that code on any object/HTML tag or text you want to close the PopUp</span>
										<div class="clear"></div>
								  		<i class="icon-info info-second" style="padding-left: 10px;"></i><span class="info info-second">Example: &lt;img src="...." onClick="locker(<?php echo $the_id;?>);" /&gt;</span>
                                    </div>
                                </div>
                        </div>
                    </div>
				 	<div id="b3" class="accordion-body collapse option_box" style="height: 0px;">			
                    <div class="isp_info_block">Customize your Popup Box adding some style features.</div>
                        <div class="control-group">
                            <label for="" class="control-label">Background Image URL</label>
                            <div class="controls">
                                <input type="text" value="<?php echo $meta_arr['bk_img_box'];?>" id="bk_img_box" name="ips_meta[bk_img_box]" onClick="ips_open_media_up(this, 'isp_update_popup_content_box');" />
                                <span class="clear_color_picker" onclick="jQuery('#bk_img_box').val('');isp_update_popup_content_box();"><i class="icon-clear-e"></i></span>
                                <span class="adv-recommanded">(Recommended: <span id="span_bk_width"><?php echo $meta_arr['general_width'];?></span>px X <span id="span_bk_height"><?php echo $meta_arr['general_height'];?></span>px)</span>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Background Image Repeat</label>
                                <div class="controls">
                                <select name="ips_meta[bk_box_repeat]" id="isp_bk_box_repeat" onChange="isp_update_popup_content_box();">
                                    <?php
                                        $selected = '';
                                        if($meta_arr['bk_box_repeat']=='repeat') $selected = 'selected="selected"';?>
                                    <option value="repeat" <?php echo $selected;?>>repeat</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['bk_box_repeat']=='repeat-x') $selected = 'selected="selected"';?>
                                    <option value="repeat-x" <?php echo $selected;?>>repeat-x</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['bk_box_repeat']=='repeat-y') $selected = 'selected="selected"';?>
                                    <option value="repeat-y" <?php echo $selected;?>>repeat-y</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['bk_box_repeat']=='no-repeat') $selected = 'selected="selected"';?>
                                    <option value="no-repeat" <?php echo $selected;?>>no-repeat</option>
                                </select>
                                </div>
                        </div>
                                <div class="control-group">
                                    <label for="" class="control-label">Background Position</label>
                                        <div class="controls">
                                            <table>
                                                <tr>
                                                    <td>Horizontally:</td>
                                                    <td style="padding-left: 10px;width: 100px;">
                                                        <select name="ips_meta[box_bk_position_x]" id="isp_box_bk_position_x" style="width: 100px;" onChange="isp_update_popup_content_box();">
                                                              <?php
                                                                  $selected = '';
                                                                  if($meta_arr['box_bk_position_x']=='left') $selected = 'selected="selected"';
                                                              ?>
                                                                  <option value="left" <?php echo $selected;?>>Left</option>
                                                              <?php
                                                                  $selected = '';
                                                                  if($meta_arr['box_bk_position_x']=='center') $selected = 'selected="selected"';
                                                              ?>
                                                                  <option value="center" <?php echo $selected;?>>Center</option>
                                                              <?php
                                                                  $selected = '';
                                                                  if($meta_arr['box_bk_position_x']=='right') $selected = 'selected="selected"';
                                                              ?>
                                                                  <option value="right" <?php echo $selected;?>>Right</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td>Vertical: </td>
                                                    <td style="padding-left: 10px;width: 100px;">
                                                        <select name="ips_meta[box_bk_position_y]" id="isp_box_bk_position_y" style="width: 100px;" onChange="isp_update_popup_content_box();">
                                                          <?php
                                                              $selected = '';
                                                              if($meta_arr['box_bk_position_y']=='top') $selected = 'selected="selected"';
                                                          ?>
                                                              <option value="top" <?php echo $selected;?>>Top</option>
                                                          <?php
                                                              $selected = '';
                                                              if($meta_arr['box_bk_position_y']=='center') $selected = 'selected="selected"';
                                                          ?>
                                                              <option value="center" <?php echo $selected;?>>Center</option>
                                                          <?php
                                                              $selected = '';
                                                              if($meta_arr['box_bk_position_y']=='bottom') $selected = 'selected="selected"';
                                                          ?>
                                                              <option value="bottom" <?php echo $selected;?>>Bottom</option>
                                                          </select>
                                                    </td>
                                                </tr>
                                            </table>
                                        </div>
                                </div>
                                <div class="control-group">
                                    <label for="" class="control-label">Background Color</label>
                                        <div class="controls">
                                            <input type="text" value="<?php echo $meta_arr['box_bk_color'];?>" name="ips_meta[box_bk_color]" id="box_bk_color" onBlur="isp_update_popup_content_box();" onKeyOut="isp_update_popup_content_box();" class="input-mini colorpick" />
                                                <span class="clear_color_picker" id="clear_one" onClick="jQuery('#box_bk_color').removeAttr('value');isp_update_popup_content_box();"><i class="icon-clear-e"></i></span>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function(){
                                                        jQuery("#box_bk_color").colorpicker();
                                                    });
                                                </script>
                                        </div>
                                </div>
                                <div class="control-group">
                                    <label for="" class="control-label">Border Color</label>
                                        <div class="controls">
                                            <input type="text" value="<?php echo $meta_arr['box_bk_border_color'];?>" name="ips_meta[box_bk_border_color]" id="box_bk_border_color" class="input-mini colorpick"/>
                                                <span class="clear_color_picker" onClick="jQuery('#box_bk_border_color').removeAttr('value');"><i class="icon-clear-e"></i></span>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function(){
                                                        jQuery("#box_bk_border_color").colorpicker();
                                                    });
                                                </script>
                                        </div>
                                </div>
                                <div class="control-group">
                                    <label for="" class="control-label">Border Width</label>
                                        <div class="controls">
                                            <div>Width: <span id="bw_ui_v"><?php echo $meta_arr['box_border_width'];?></span>px</div>
                                            <div id="bb_width_s" title="<?php echo $meta_arr['box_border_width'];?>px"></div>
                                                <script type="text/javascript">
                                                jQuery(document).ready(function() {
                                                      var from = "#bb_width_s";
                                                      var hidden_i = "#box_border_width";
                                                      jQuery(from).slider({
                                                      min: 0, //minimum value
                                                      max: 20, //maximum value
                                                      step: 1,
                                                      value: "<?php echo $meta_arr['box_border_width'];?>", //default value
                                                      slide: function(event, ui) {
                                                        var the_val = ui.value;
                                                        jQuery(hidden_i).val(the_val);
                                                        jQuery(from).attr("title", the_val+"px");
                                                        jQuery("#bw_ui_v").html(the_val);
                                                      }
                                                  });
                                                });
                                                </script>
                                            <input type="hidden" value="<?php echo $meta_arr['box_border_width'];?>" name="ips_meta[box_border_width]" id="box_border_width" />
                                        </div>
                                </div>
                                <div class="control-group">
                                    <label for="" class="control-label">Border Radius</label>
                                        <div class="controls">
                                            <div>Value: <span id="bwr_ui_v"><?php echo $meta_arr['box_border_radius'];?></span>px</div>
                                            <div id="bb_width_r" title="<?php echo $meta_arr['box_border_radius'];?>px"></div>
                                                <script type="text/javascript">
                                                jQuery(document).ready(function() {
                                                      var from = "#bb_width_r";
                                                      var hidden_i = "#box_border_radius";
                                                      jQuery(from).slider({
                                                      min: 0, //minimum value
                                                      max: 30, //maximum value
                                                      step: 1,
                                                      value: "<?php echo $meta_arr['box_border_radius'];?>", //default value
                                                      slide: function(event, ui) {
                                                        var the_val = ui.value;
                                                        jQuery(hidden_i).val(the_val);
                                                        jQuery(from).attr("title", the_val+"px");
                                                        jQuery("#bwr_ui_v").html(the_val);
                                                      }
                                                  });
                                                });
                                                </script>
                                            <input type="hidden" value="<?php echo $meta_arr['box_border_radius'];?>" name="ips_meta[box_border_radius]" id="box_border_radius" />
                                        </div>
                                </div>
                    </div>
				 	<div id="b4" class="accordion-body collapse option_box" style="height: 0px;">					
                    <div class="isp_info_block">Select the desired Content Type that will Show Up into the Popup. The selected Tab it will be the active option to show.</div>
                        <div class="co_m_item_w">
                        <?php
                            $class = "co_m_item";
                            if($meta_arr['c_type']=='html') $class = "co_m_item_selected";
                        ?>
                            <div id="co_m_html" class="<?php echo $class;?>" onClick="co_menu('#co_m_html', '#html_c', 'html');">HTML</div>
						 <?php
                            $class = "co_m_item";
                            if($meta_arr['c_type']=='opt_in') $class = "co_m_item_selected";
                        ?>
                        <div id="co_m_opt_in" class="<?php echo $class;?>" onClick="co_menu('#co_m_opt_in', '#opt_in_c', 'opt_in');">Opt In</div>
 						 <?php
                            $class = "co_m_item";
                            if($meta_arr['c_type']=='shortcode') $class = "co_m_item_selected";
                        ?>
                            <div id="co_m_shortcode" class="<?php echo $class;?>" onClick="co_menu('#co_m_shortcode', '#shortcode_c', 'shortcode');">Shortcode</div>  
                        <?php
                            $class = "co_m_item";
                            if($meta_arr['c_type']=='iframe') $class = "co_m_item_selected";
                        ?>
                            <div id="co_m_if" class="<?php echo $class;?>" onClick="co_menu('#co_m_if', '#if_c', 'iframe');">IFrame</div>
                        <?php
                            $class = "co_m_item";
                            if($meta_arr['c_type']=='video') $class = "co_m_item_selected";
                        ?>
                            <div id="co_m_v" class="<?php echo $class;?>" onClick="co_menu('#co_m_v', '#thev_c', 'video');">Video</div>
						 <?php
                            $class = "co_m_item";
                            if($meta_arr['c_type']=='imgSlider') $class = "co_m_item_selected";
                        ?>
                            <div id="co_m_imgSlider" class="<?php echo $class;?>" onClick="co_menu('#co_m_imgSlider', '#imgSlider', 'imgSlider');">Image Slider</div>
                        <?php
                            $class = "co_m_item";
                            if( $meta_arr['c_type']=='fb_likebox' ) $class = "co_m_item_selected";
                        ?>
                            <div id="co_m_FBlikebox" class="<?php echo $class;?>" onClick="co_menu('#co_m_FBlikebox', '#fb_likebox_c', 'fb_likebox');">Facebook LikeBox</div>
                        <?php
                            $class = "co_m_item";
                            if( $meta_arr['c_type']=='google_maps' ) $class = "co_m_item_selected";
                        ?>
                            <div id="co_m_googleMaps" class="<?php echo $class;?>" onClick="co_menu('#co_m_googleMaps', '#google_map_c', 'google_maps');">Google Maps</div>
						 <?php
                            $class = "co_m_item";
                            if($meta_arr['c_type']=='content_id') $class = "co_m_item_selected";
                        ?>
                            <div id="co_m_div" class="<?php echo $class;?>" onClick="co_menu('#co_m_div', '#div_c', 'content_id');">Content ID</div>
						 <?php
                            $class = "co_m_item";
                            if($meta_arr['c_type']=='the_postpag_v') $class = "co_m_item_selected";
                        ?>
                            <div id="co_m_postpag" class="<?php echo $class;?>" onClick="co_menu('#co_m_postpag', '#the_post_pag_div', 'the_postpag_v');">A Post/Page</div>                                               
                            <input type="hidden" value="<?php echo $meta_arr['c_type'];?>" name="ips_meta[c_type]" id="c_type"/>
                        <div class="clear"></div>
                        </div>
                        
<!--------------- HTML ------------------>
                        <?php
                            $display = "none";
                            if($meta_arr['c_type']=='html') $display = "block";
                        ?>
                        <div id="html_c" style="display: <?php echo $display;?>;">
                            <div class="control-group">
                                <div class="wrap-the-ckeditor-resizer" style="width: <?php echo $meta_arr['general_width']+20;?>px;">
                                    <textarea name="ck" class='editors'><?php 
                                    	$content = stripslashes($meta_arr['html_content']); 
                                    	$content = htmlspecialchars($content);
										echo $content;
										?>
                                    </textarea>
									<div style="margin-top:6px;"><i class="icon-info"></i><span class="info">Use the "Source" option if you want to include/manage a HTML code</span></div>
                                </div>
                            </div>
                        </div>
<!--------------- OPT IN  ----------------------->  
                        <?php
                            $display = "none";
                            if($meta_arr['c_type']=='opt_in') $display = "block";
                        ?>
						<div id="opt_in_c" style="display: <?php echo $display;?>;">
                            <div class="control-group">
                                <div>
									<div class="control-group">
										<label for="" class="control-label">Subscribe Type:</label>
									    <div class="controls">
									    	<select name="ips_meta[ips_subscribe_type]">
			                                    <?php
			                                        $subscribe_types = array(
			                                                                    'aweber' => 'AWeber',
			                                                                    'campaign_monitor' => 'CampaignMonitor',
			                                                                    'constant_contact' => 'Constant Contact',
			                                                                    'email_list' => 'E-mail List',
			                                                                    'get_response' => 'GetResponse',
			                                                                    'icontact' => 'IContact',
			                                                                    'madmimi' => 'Mad Mimi',
			                                                                    'mailchimp' => 'MailChimp',
			                                                                    'mymail' => 'MyMail',
			                                                                    'wysija' => 'Wysija',
			                                                                 );
			                                        foreach($subscribe_types as $k=>$v){
			                                        	$selected = '';
			                                            if($meta_arr['ips_subscribe_type']==$k) $selected = 'selected="selected"';
			                                            ?>
			                                                <option value="<?php echo $k;?>" <?php echo $selected;?> ><?php echo $v;?></option>
			                                            <?php
			                                        }
			                                    ?>
			                            	</select>
											<div class="clear"></div>
											<i class="icon-info"></i><span class="info">Set Email Marketing Settings from <a href="<?php echo $url;?>?page=ips_admin&tab=opt_in_settings">here</a></span>
										</div>
									</div>
									<div class="control-group">
										<label for="" class="control-label">Error Message:</label>
									    <div class="controls"> 
									    	<textarea name="ips_meta[opt_in_err_msg]" style="min-width: 400px;"><?php 
									    		echo $meta_arr['opt_in_err_msg'];
									    	?></textarea>
									    </div>
									</div>
										<div style="width: <?php echo $meta_arr['general_width']+20;?>px;" class="wrap-the-ckeditor-resizer">
		                                    <textarea name="ips_meta[opt_in_content]" class='editors'><?php 
		                                    	$content = stripslashes($meta_arr['opt_in_content']); 
		                                    	$content = htmlspecialchars($content);
												echo $content;
												?>
		                                    </textarea>									
										</div>

										<div style="margin-top:6px;">
											<i class="icon-info"></i>
											<span class="info">Use the "Source" option if you want to include/manage a HTML code</span>
										</div>
                                </div>
                            </div>
						</div>
<!--------------- IFRAME ----------------------->                        
                        <?php
                            $display = "none";
                            if($meta_arr['c_type']=='iframe') $display = "block";
                        ?>
                        <div id="if_c" style="display: <?php echo $display;?>;">
                            <div class="control-group">
                                <label for="" class="control-label">Src IFrame</label>
                                <div class="controls">
                                    <input type="text" name="ips_meta[the_ifrm_link]" id="the_ifrm_link" value="<?php echo $meta_arr['the_ifrm_link'];?>" style="width: 400px;" />
                                    <span class="adv-recommanded">(Full URL)</span>
                                </div>
                            </div>
                        </div>
<!--------------- VIDEO ----------------------->                         
                        <?php
                            $display = "none";
                            if($meta_arr['c_type']=='video') $display = "block";
                        ?>
                        <div id="thev_c" style="display: <?php echo $display;?>;">
                             <div class="control-group">
                                <label for="" class="control-label">URL or ID</label>
                                <div class="controls">
                                    <input type="text" name="ips_meta[youtube_id_v]" id="youtube_id_v" value="<?php echo $meta_arr['youtube_id_v'];?>" style="width: 400px;"/>
                                    <br/><span class="adv-recommanded">( Full URL exemple: http://www.youtube.com/watch?v=IDzQ8aJ02ac or ID exemple: IDzQ8aJ02ac )</span>
                                </div>
                            </div>
                             <div class="control-group">
                                <label for="" class="control-label">Autoplay</label>
                                <div class="controls">
                                    <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['yt_autoplay']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#yt_autoplay');" />
                                        <input type="hidden" name="ips_meta[yt_autoplay]" value="<?php echo $meta_arr['yt_autoplay'];?>" id="yt_autoplay"/>
                                    </div>
                                </div>
                            </div>
                             <div class="control-group">
                                <label for="" class="control-label">Controls</label>
                                <div class="controls">
                                    <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['yt_controls']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#yt_controls');" />
                                        <input type="hidden"  name="ips_meta[yt_controls]" id="yt_controls" value="<?php echo $meta_arr['yt_controls'];?>" />
                                    </div>
                                </div>
                            </div>
                             <div class="control-group">
                                <label for="" class="control-label">Loop</label>
                                <div class="controls">
                                    <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['yt_loop']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#yt_loop');"/>
                                        <input type="hidden"  name="ips_meta[yt_loop]" id="yt_loop" value="<?php echo $meta_arr['yt_loop'];?>" />
                                    </div>
                                </div>
                            </div>
                             <div class="control-group">
                                <label for="" class="control-label">Theme</label>
                                <div class="controls">
                                    <?php
                                        $checked = '';
                                        if($meta_arr['yt_theme']=='light') $checked = 'checked="checked"';
                                    ?>
                                    <div class="check-line">
                                        <input type="radio" class="icheck-me" data-skin="minimal" value="light" name="theme" <?php echo $checked;?> onChange="jQuery('#yt_theme').val(this.value);"/> <label class="inline">Light</label>
                                    </div>
                                    <?php
                                        $checked = '';
                                        if($meta_arr['yt_theme']=='dark') $checked = 'checked="checked"';
                                    ?>
                                    <div class="check-line">
                                        <input type="radio" class="icheck-me" data-skin="minimal" value="dark" name="theme" <?php echo $checked;?> onChange="jQuery('#yt_theme').val(this.value);"/> <label class="inline">Dark</label>
                                    </div>
                                    <input type="hidden" name="ips_meta[yt_theme]" id="yt_theme" value="<?php echo $meta_arr['yt_theme'];?>" />
                                </div>
                            </div>
                             <div class="control-group">
                                <label for="" class="control-label">Autohide</label>
                                <div class="controls">
                                    <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['yt_autohide']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#yt_autohide');"/>
                                        <input type="hidden" name="ips_meta[yt_autohide]" id="yt_autohide" value="<?php echo $meta_arr['yt_autohide'];?>" />
                                    </div>
                                </div>
                            </div>
                             <div class="control-group">
                                <label for="" class="control-label">Hide Annotations</label>
                                <div class="controls">
                                    <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['h_annotations']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#h_annotations');"/>
                                        <input type="hidden" name="ips_meta[h_annotations]" id="h_annotations" value="<?php echo $meta_arr['h_annotations'];?>" />
                                    </div>
                                </div>
                            </div>
                        </div>
						<?php
                            $display = "none";
                            if($meta_arr['c_type']=='content_id') $display = "block";
                        ?>
                        <div id="div_c" style="display: <?php echo $display;?>;">
                            <div class="control-group">
                                <label for="" class="control-label">Content ID</label>
                                <div class="controls">
									<span class="adv-recommanded">#</span>
                                    <input type="text" name="ips_meta[the_content_id]" id="the_content_id" value="<?php echo $meta_arr['the_content_id'];?>" style="width: 400px;" />
                                	<div style="margin-top:10px;"><i class="icon-info"></i><span class="info">Set the Target(like a DIV tag) ID that should be displayed in your PopUp. The Target should be available on your current page!</span></div>
								</div>
                            </div>
                        </div>
<!--------------- SPECIFIC POST/PAGE ----------------------->
						<?php
                            $display = "none";
                            if($meta_arr['c_type']=='the_postpag_v') $display = "block";
                        ?>
                        <div id="the_post_pag_div" style="display: <?php echo $display;?>;">
                            <div class="control-group">
                                <label for="" class="control-label">Post/ Page</label>
                                <div class="controls">
                                <?php
                                    $posts_list = $wpdb->get_results("SELECT post_title, ID, post_type
                                                                      FROM {$wpdb->prefix}posts
                                                                      WHERE post_status='publish'
                                                                      AND ( post_type='post' OR post_type='page' )
                                                                      ;");
                                    if(isset($posts_list) && count($posts_list)>0){
                                    ?>
                                        <select name="ips_meta[the_postpag_val]">
                                    <?php
                                        foreach($posts_list as $the_item){
                                            $selected = '';
                                            if($meta_arr['the_postpag_val']==$the_item->ID) $selected = 'selected="selected"';
                                          ?>
                                            <option value="<?php echo $the_item->ID;?>" <?php echo $selected;?> ><?php echo $the_item->post_title. " (".$the_item->post_type.")";?></option>
                                          <?php
                                        }
                                    ?>
                                        </select>
                                    <?php
                                        }else "No posts/pages available!";
                                    ?>
									<div style="margin-top:10px;"><i class="icon-info"></i><span class="info">Set the Target Page/Post that should be displayed in your PopUp</span></div>
                                </div>
                            </div>
                        </div>
<!--------------- IMAGE SLIDER ----------------------->                          
                        <?php
                            $display = "none";
                            if($meta_arr['c_type']=='imgSlider') $display = "block";
                        ?>                        
                        <div id="imgSlider" style="display: <?php echo $display;?>;">
                                <script>
        						  jQuery(function() {
        							  jQuery( "#sortable" ).sortable({
        						      revert: true
        						    });
        							  jQuery( "#draggable" ).draggable({
        						      connectToSortable: "#sortable",
        						      helper: "clone",
        						      revert: "invalid"
        						    });
        						    jQuery( "ul, li" ).disableSelection();
        						  });
        						</script>
                                <style>
                                    #add_row{
                                    	width:100%;
                                    	font-family: Helvetica,Arial,sans-serif;
                                    	font-size: 13px !important;
                                    	font-weight: bold;
                                    	color: #477db5;
                                    	cursor: pointer;
                                    	text-align:center;
                                    	padding:15px 0;
                                    	border-radius:3px;
                                    }
                                    #add_row:hover{
                                    	background: #cde3ed;
                                    	color: #666;
                                    }
                                </style>
                            <div class="control-group">
                                <label for="" class="control-label">Slides</label>
                                <div class="controls">
                                    <ul id="sortable">
                                    
									<?php 
									@$slides = (unserialize(base64_decode($meta_arr['slider']))); 
									if (is_array($slides)) {
										
										foreach($slides as $k=>$v) {
											echo '<li class="ui-state-default slider_li_t"><img onclick="jQuery(this).parent().remove();" class="close_slide_icon" src="'.ISP_DIR_URL.'assets/img/close_2.png" ><input type="text" value="'.$v.'" class="slider-input" name="ips_meta[slider][]" onclick="ips_open_media_up(this);" /></li>';
										}
									}
									?>									  
									</ul>
									<div id="add_row"><i class="icon-plus"></i> Slide</div>
                                </div>
                            </div>
							<div class="control-group">
                                <label for="" class="control-label">Slider Settings</label>
                                <div class="controls">
                                     <table>
										<?php
										if (isset($_REQUEST['p_id']) && $_REQUEST['p_id'] >0) {
											@$slider_options = unserialize(base64_decode($meta_arr['slider_option']));
										} else {
											$slider_options = $meta_arr['slider_option'];
											
										}
                                        ?>
                                        <tr>
                                            <td class="slider_left_td">
                                                <span class="slide_table_label">Bullets</span>
                                            </td>
                                            <td>
                                                <?php
                                                     $checked = '';
                                                     if(isset($slider_options['pagination']) && $slider_options['pagination']==true) $checked = "checked='checked'";
                                                ?>
                                                <input type="checkbox" name="ips_meta[slider_option][pagination]" value="true" <?php echo $checked;?> />
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="slider_left_td">
                                                <span class="slide_table_label">Navigation Buttons</span>
                                            </td>
                                            <td>
                                                <?php
                                                     $checked = '';
                                                     if(isset($slider_options['navigation']) && $slider_options['navigation']==true) $checked = "checked='checked'";
                                                ?>
                                                <input type="checkbox" name="ips_meta[slider_option][navigation]" value="true" <?php echo $checked;?> />
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="space_b_divs"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="slider_left_td">
                                                <span class="slide_table_label">Slide duration</span>
                                            </td>
                                            <td class="slider_left_td">
                                               
                                                <div id="pagination_speed_slide_div" title="<?php echo ($slider_options['paginationSpeed']/1000);?>s"></div>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function() {
                                                        var from = "#pagination_speed_slide_div";
                                                        var hidden_i = "#pagination_speed_h";
                                                        jQuery(from).slider({
                                                                            min: 0, //minimum value
                                                                            max: 10000, //maximum value
                                                                            step: 100,
                                                                            value: "<?php echo $slider_options['paginationSpeed'];?>", //default value
                                                                            slide: function(event, ui) {
                                                									   var the_val = ui.value;
                                                									   jQuery(hidden_i).val(the_val);
                                                                                       the_val /= 1000;
                                                									   jQuery(from).attr("title", the_val+"s");
                                                                                       jQuery('#pag_speed_slide').html(the_val+"s");
                                                                                   }
                                                        });
                                                    });
                                                </script>
                                                <input type="hidden" name="ips_meta[slider_option][paginationSpeed]" value="<?php echo  $slider_options['paginationSpeed'];?>" id="pagination_speed_h" />
                                            </td>
                                            <td>
                                                <div class="slide_table_label" id="pag_speed_slide">
                                                    <?php echo ($slider_options['paginationSpeed']/1000);?>s
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="slider_left_td">
                                                <span class="slide_table_label">Navigaton Speed</span>
                                            </td>
                                            <td class="slider_left_td">
                                               
                                                <div id="speed_slide_div" title="<?php echo ($slider_options['slideSpeed']/1000);?>s"></div>
                                                <script type="text/javascript">
                                                    jQuery(document).ready(function() {
                                                        var from = "#speed_slide_div";
                                                        var hidden_i = "#slide_speed_h";
                                                        jQuery(from).slider({
                                                                            min: 0, //minimum value
                                                                            max: 10000, //maximum value
                                                                            step: 100,
                                                                            value: "<?php echo $slider_options['slideSpeed'];?>", //default value
                                                                            slide: function(event, ui) {
                                                									   var the_val = ui.value;
                                                									   jQuery(hidden_i).val(the_val);
                                                                                       the_val /= 1000;
                                                									   jQuery(from).attr("title", the_val+"s");
                                                                                       jQuery('#ddd_speed_slide').html(the_val+"s");
                                                                                   }
                                                        });
                                                    });
                                                </script>
                                                <input type="hidden" name="ips_meta[slider_option][slideSpeed]" value="<?php echo $slider_options['slideSpeed']; ?>" id="slide_speed_h" />
                                            </td>
                                            <td>
                                                <div class="slide_table_label" id="ddd_speed_slide">
                                                    <?php echo ($slider_options['slideSpeed']/1000);?>s
                                                </div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="space_b_divs"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="slider_left_td">
                                                <span class="slide_table_label">Auto Play</span>
                                            </td>
                                            <td>
                                                <?php
                                                     $checked = '';
                                                     if(isset($slider_options['autoPlay']) && $slider_options['autoPlay']==true) $checked = "checked='checked'";
                                                ?>
                                                <input type="checkbox" name="ips_meta[slider_option][autoPlay]" value="true" <?php echo $checked;?>  />
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="slider_left_td">
                                                <span class="slide_table_label">Stop On hover</span>
                                            </td>
                                            <td>
                                                <?php
                                                     $checked = '';
                                                     if(isset($slider_options['stopOnHover']) && $slider_options['stopOnHover']==true) $checked = "checked='checked'";
                                                ?>
                                                <input type="checkbox" name="ips_meta[slider_option][stopOnHover]" value="true" <?php echo $checked;?> />
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td class="slider_left_td">
                                                <span class="slide_table_label">Progress Bar</span>
                                            </td>
                                            <td>
                                                <?php
                                                     $checked = '';
                                                     if(isset($slider_options['progressBar']) && $slider_options['progressBar']==true) $checked = "checked='checked'";
                                                ?>
                                                <input type="checkbox" name="ips_meta[slider_option][progressBar]" value="true" <?php echo $checked;?> />
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                            <td colspan="3">
                                                <div class="space_b_divs"></div>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td class="slider_left_td">
                                                <span class="slide_table_label">CSS3 Transition</span>
                                            </td>
                                            <td>
                                                <select name="ips_meta[slider_option][css_transition]">
                                                        <?php $selected = ips_checkIfSelected( $slider_options['css_transition'], 'none', 'select' );?>
                                                    <option value="none" <?php echo $selected;?> >None</option>
                                                        <?php $selected = ips_checkIfSelected( $slider_options['css_transition'], 'fade', 'select' );?>
                                                    <option value="fade" <?php echo $selected;?> >fade</option>
                                                        <?php $selected = ips_checkIfSelected( $slider_options['css_transition'], 'backSlide', 'select' );?>
                                                    <option value="backSlide" <?php echo $selected;?> >backSlide</option>
                                                        <?php $selected = ips_checkIfSelected( $slider_options['css_transition'], 'goDown', 'select' );?>
                                                    <option value="goDown" <?php echo $selected;?> >goDown</option>
                                                        <?php $selected = ips_checkIfSelected( $slider_options['css_transition'], 'fadeUp', 'select' );?>
                                                    <option value="fadeUp" <?php echo $selected;?> >fadeUp</option>
                                                </select>
                                            </td>
                                            <td></td>
                                        </tr>
                                        <tr>
                                        	<td></td>
                                        	<td>
                                        		<span class="isp_warning_grey_span">May not work on some browsers!</span>
                                        	</td>
                                        	<td></td>	
                                        </tr>
                                    </table>
                                </div>
                            </div>
                        </div> <!--end of #imgSlider-->
<!------------------ FB LIKEBOX ------------------>
						<?php
                            $display = "none";
                            if($meta_arr['c_type']=='fb_likebox') $display = "block";
                        ?>
                        <div id="fb_likebox_c" style="display: <?php echo $display;?>">
                            <div class="control-group">
                                <label for="" class="control-label">Facebook Page URL</label>
                                <div class="controls">
                                    <input type="text" value="<?php echo $meta_arr['fb_url'];?>" name="ips_meta[fb_url]" style="width:350px; height:26px;"/>
									<div class="clear"></div>
									<i class="icon-info" style="margin-left:-10px;"></i><span class="info">Be sure that you add the proper Full FaceBook Page URL!</span>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Color Scheme</label>
                                <div class="controls">
                                    <select name="ips_meta[fb_color_scheme]" >
                                            <?php $selected = ips_checkIfSelected( $meta_arr['fb_color_scheme'], 'light', 'select' );?>
                                        <option value="light" <?php echo $selected;?> >Light</option>
                                            <?php $selected = ips_checkIfSelected( $meta_arr['fb_color_scheme'], 'dark', 'select' );?>
                                        <option value="dark" <?php echo $selected;?> >Dark</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Show Friends' Faces</label>
                                <div class="controls">
                                        <?php $checked = ips_checkIfSelected( $meta_arr['fb_show_faces'], 'true', 'checkbox' );?>
                                    <input type="checkbox" onClick="checkAndH_boolean(this, '#fb_show_faces_h');" <?php echo $checked;?>/>
                                    <input type="hidden" value="<?php echo $meta_arr['fb_show_faces'];?>" name="ips_meta[fb_show_faces]" id="fb_show_faces_h" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Show Header</label>
                                <div class="controls">
                                        <?php $checked = ips_checkIfSelected( $meta_arr['fb_header'], 'true', 'checkbox' );?>
                                    <input type="checkbox" onClick="checkAndH_boolean(this, '#fb_header_h');" <?php echo $checked;?>/>
                                    <input type="hidden" value="<?php echo $meta_arr['fb_header'];?>" name="ips_meta[fb_header]" id="fb_header_h" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Stream</label>
                                <div class="controls">
                                        <?php $checked = ips_checkIfSelected( $meta_arr['fb_stream'], 'true', 'checkbox' );?>
                                    <input type="checkbox" onClick="checkAndH_boolean(this, '#fb_stream_h');" <?php echo $checked;?>/>
                                    <input type="hidden" value="<?php echo $meta_arr['fb_stream'];?>" name="ips_meta[fb_stream]" id="fb_stream_h" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Show Border</label>
                                <div class="controls">
                                        <?php $checked = ips_checkIfSelected( $meta_arr['fb_border'], 'true', 'checkbox' );?>
                                    <input type="checkbox" onClick="checkAndH_boolean(this, '#fb_border_h');" <?php echo $checked;?>/>
                                    <input type="hidden" value="<?php echo $meta_arr['fb_border'];?>" name="ips_meta[fb_border]" id="fb_border_h" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Width</label>
                                <div class="controls">
                                    <div>Width: <span id="fb_width_title"><?php echo $meta_arr['fb_width'];?></span>px</div>
                                    <div id="fb_likebox_width" title="<?php echo $meta_arr['fb_width'];?>px"></div>
                                        <script type="text/javascript">
                                            jQuery(document).ready(function() {
                                                var from = "#fb_likebox_width";
                                                var hidden_i = "#fb_width";
                                                        jQuery(from).slider({
                                                            min: 10, //minimum value
                                                            max: 800, //maximum value
                                                            step: 1,
                                                            value: "<?php echo $meta_arr['fb_width'];?>", //default value
                                                            slide: function(event, ui) {
                                                			    var the_val = ui.value;
                                                				jQuery(hidden_i).val(the_val);
                                                				jQuery(from).attr("title", the_val);
                                                                jQuery('#fb_width_title').html(the_val);
                                                            }
                                                        });
                                            });
                                        </script>
                                    <input type="hidden" name="ips_meta[fb_width]" value="<?php echo $meta_arr['fb_width'];?>" id="fb_width" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Height</label>
                                <div class="controls">
                                    <div>Height: <span id="fb_height_title"><?php echo $meta_arr['fb_height'];?></span>px</div>
                                    <div id="fb_likebox_height" title="<?php echo $meta_arr['fb_height'];?>px"></div>
                                        <script type="text/javascript">
                                            jQuery(document).ready(function() {
                                                var from = "#fb_likebox_height";
                                                var hidden_i = "#fb_height";
                                                        jQuery(from).slider({
                                                            min: 10, //minimum value
                                                            max: 800, //maximum value
                                                            step: 1,
                                                            value: "<?php echo $meta_arr['fb_height'];?>", //default value
                                                            slide: function(event, ui) {
                                                			    var the_val = ui.value;
                                                				jQuery(hidden_i).val(the_val);
                                                				jQuery(from).attr("title", the_val);
                                                                jQuery('#fb_height_title').html(the_val);
                                                            }
                                                        });
                                            });
                                        </script>
                                    <input type="hidden" name="ips_meta[fb_height]" value="<?php echo $meta_arr['fb_height'];?>" id="fb_height" />
                                </div>
                            </div>
                        </div>
<!--------------- GOOGLE MAPS ----------------------->  
						<?php
                            $display = "none";
                            if($meta_arr['c_type']=='google_maps') $display = "block";
                        ?>
                        <div id="google_map_c" style="display: <?php echo $display;?>;">
                            <div class="control-group">
                                <label for="" class="control-label">Coordinates</label>
                                <div class="controls">
                                    <input type="text" value="<?php echo $meta_arr['google_latlgt'];?>" name="ips_meta[google_latlgt]" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Zoom</label>
                                <div class="controls">
                                    <input type="number" min="1" max="21" value="<?php echo $meta_arr['google_zoom'];?>" class="number_input" name="ips_meta[google_zoom]" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Map Type</label>
                                <div class="controls">
                                    <select name="ips_meta[google_map_type]">
                                            <?php $selected = ips_checkIfSelected( $meta_arr['google_map_type'], 'HYBRID', 'select' );?>
                                        <option value="HYBRID" <?php echo $selected;?> >Hybrid</option>
                                            <?php $selected = ips_checkIfSelected( $meta_arr['google_map_type'], 'ROADMAP', 'select' );?>
                                        <option value="ROADMAP" <?php echo $selected;?> >Roadmap</option>
                                            <?php $selected = ips_checkIfSelected( $meta_arr['google_map_type'], 'SATELLITE', 'select' );?>
                                        <option value="SATELLITE" <?php echo $selected;?> >Satellite</option>
                                            <?php $selected = ips_checkIfSelected( $meta_arr['google_map_type'], 'TERRAIN', 'select' );?>
                                        <option value="TERRAIN" <?php echo $selected;?> >Terrain</option>
                                    </select>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Info Window - Active</label>
                                <div class="controls">
                                    <div>
                                        <?php $checkbox = ips_checkIfSelected( $meta_arr['google_info_active'], 'yes', 'checkbox' );?>
                                        <input type="radio" value="yes" name="ips_meta[google_info_active]" <?php echo $checkbox;?> /> Yes
                                    </div>
                                    <div>
                                        <?php $checkbox = ips_checkIfSelected( $meta_arr['google_info_active'], 'no', 'checkbox' );?>
                                        <input type="radio" value="no" name="ips_meta[google_info_active]" <?php echo $checkbox;?> /> No
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Info Window - Content</label>
                                <div class="controls">
                                    <textarea name="ips_meta[google_info_content]"><?php echo $meta_arr['google_info_content'];?></textarea>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Info Window - Default Open</label>
                                <div class="controls">
                                    <div>
                                        <?php $checkbox = ips_checkIfSelected( $meta_arr['google_info_default_open'], 'yes', 'checkbox' );?>
                                        <input type="radio" value="yes" name="ips_meta[google_info_default_open]" <?php echo $checkbox;?> /> Yes
                                    </div>
                                    <div>
                                        <?php $checkbox = ips_checkIfSelected( $meta_arr['google_info_default_open'], 'no', 'checkbox' );?>
                                        <input type="radio" value="no" name="ips_meta[google_info_default_open]" <?php echo $checkbox;?> /> No
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Marker Label</label>
                                <div class="controls">
                                    <input type="text" name="ips_meta[google_maker_label]" value="<?php echo $meta_arr['google_maker_label'];?>" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Width</label>
                                <div class="controls">
                                    <div>Width: <span id="google_width_title"><?php echo $meta_arr['google_width'];?></span>px</div>
                                    <div id="google_slider_width" title="<?php echo $meta_arr['google_width'];?>px"></div>
                                        <script type="text/javascript">
                                            jQuery(document).ready(function() {
                                                var from = "#google_slider_width";
                                                var hidden_i = "#google_width";
                                                        jQuery(from).slider({
                                                            min: 10, //minimum value
                                                            max: 800, //maximum value
                                                            step: 1,
                                                            value: "<?php echo $meta_arr['google_width'];?>", //default value
                                                            slide: function(event, ui) {
                                                			    var the_val = ui.value;
                                                				jQuery(hidden_i).val(the_val);
                                                				jQuery(from).attr("title", the_val);
                                                                jQuery('#google_width_title').html(the_val);
                                                            }
                                                        });
                                            });
                                        </script>
                                    <input type="hidden" name="ips_meta[google_width]" value="<?php echo $meta_arr['google_width'];?>" id="google_width" />
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Height</label>
                                <div class="controls">
                                    <div>Height: <span id="google_height_title"><?php echo $meta_arr['google_height'];?></span>px</div>
                                    <div id="google_slider_height" title="<?php echo $meta_arr['google_height'];?>px"></div>
                                        <script type="text/javascript">
                                            jQuery(document).ready(function() {
                                                var from = "#google_slider_height";
                                                var hidden_i = "#google_height";
                                                        jQuery(from).slider({
                                                            min: 10, //minimum value
                                                            max: 800, //maximum value
                                                            step: 1,
                                                            value: "<?php echo $meta_arr['google_height'];?>", //default value
                                                            slide: function(event, ui) {
                                                			    var the_val = ui.value;
                                                				jQuery(hidden_i).val(the_val);
                                                				jQuery(from).attr("title", the_val);
                                                                jQuery('#google_height_title').html(the_val);
                                                            }
                                                        });
                                            });
                                        </script>
                                    <input type="hidden" name="ips_meta[google_height]" value="<?php echo $meta_arr['google_height'];?>" id="google_height" />
                                </div>
                            </div>
                        </div><!--end of #google_map_c-->
<!---------------------------- SHORTCODE ------------------------>                       
						<?php
                            $display = "none";
                            if($meta_arr['c_type']=='shortcode') $display = "block";
                        ?>
                        <div id="shortcode_c" style="display: <?php echo $display;?>;">
                            <div class="control-group">
                                <label for="" class="control-label">Shortcode:</label>
                                <div class="controls">
								<i class="icon-info"></i><span class="info">Any Shortcode can be used to display a special content (like Rev Slider, NextGen Gallery) into the Popup. <br/><span style="color:#9b4449">This feature use Custom Post Type (ISP Post): "isp_page_<?php echo $the_id;?>". <strong>Don't delete</strong> this post!</span></span>
								<br/><br/>
								<div class="clear"></div>
                                    <?php 
                                    	$meta_arr['shortcode'] = ips_get_shortcode_content_page_custom( $the_id );
                                    	$arr = array(
                                    				  'textarea_name' => 'ips_meta[shortcode]'	
                                    					);
                                    	wp_editor( $meta_arr['shortcode'], 'shortcode_content_type', $arr );
                                    ?>
                                    <input type="hidden" name="ips_meta[custom_page_shortcode_id]" value="<?php echo $meta_arr['custom_page_shortcode_id'];?>" />
                                </div>
                            </div>
                        </div>
                    </div>
				 	<div id="b6" class="accordion-body collapse option_box" style="height: 0px;">
                    <div class="isp_info_block">Set your <strong>targetted audience</strong> and how often they will see the Popup</div>
					<div class="isp_warning">This Section may restrict the PopUp to show up and you will not be able to see it. Leave the settings default for <strong>Testing</strong> stage!</div>
					    <div class="control-group">
                            <label for="" class="control-label"><strong>Only</strong> Unregistered User</label>
                            <div class="controls">
                                <div class="check-line" style="top: 6px;">
                                <?php
                                    $checked = '';
                                    if($meta_arr['unregistered_user']==1) $checked = 'checked="checked"';
                                ?>
                                    <input type="checkbox"  class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="check_and_h(this, '#unregistered_user');" />
                                    <input type="hidden" value="<?php echo $meta_arr['unregistered_user'];?>" name="ips_meta[unregistered_user]" id="unregistered_user" />
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label"><strong>Only</strong> Registered User</label>
                            <div class="controls">
                                <div class="check-line" style="top: 6px;">
                                <?php
                                    $checked = '';
                                    if($meta_arr['registered_user']==1) $checked = 'checked="checked"';
                                ?>
                                    <input type="checkbox"  class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="check_and_h(this, '#registered_user');" />
                                    <input type="hidden" value="<?php echo $meta_arr['registered_user'];?>" name="ips_meta[registered_user]" id="registered_user" />
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">First Time Visit</label>
                            <div class="controls">
                                <div class="check-line" style="top: 6px; float:left;">
                                <?php
                                    $checked = '';
                                    if($meta_arr['first_time_visit']==1) $checked = 'checked="checked"';
                                ?>
                                    <input type="checkbox"  class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="check_and_h(this, '#first_time_visit');" />
                                    <input type="hidden" value="<?php echo $meta_arr['first_time_visit'];?>" name="ips_meta[first_time_visit]" id="first_time_visit" />
									
                                </div>
								<div style="float:left; margin-top:6px;"><i class="icon-info"></i><span class="info" style="color:#9b4449;">If you select this option you will see the plugin just once. Don't miss it!</span></div>
                            </div>
                        </div>
                        <div class="control-group">
                              <label for="" class="control-label">Reset 'First Time Visit' After:</label>
                                    <div class="controls">
                                        <div class="check-line" style="top: 6px;">
                                            <input type="number" min="1" value="<?php echo $meta_arr['rev_popup_after'];?>" id="reset_ftv" name="ips_meta[rev_popup_after]" style="padding:0px 5px;"/> Hours
                                            <span class="clear_color_picker" onclick="jQuery('#reset_ftv').val('');"><i class="icon-clear-e"></i></span>
                                        </div>
                                    </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Has Commented</label>
                            <div class="controls">
                                <div class="check-line" style="top: 6px;">
                                <?php
                                    $checked = '';
                                    if($meta_arr['show_if_comments']==1) $checked = 'checked="checked"';
                                ?>
                                    <input type="checkbox"  class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="check_and_h(this, '#show_ff_comments');" />
                                    <input type="hidden" value="<?php echo $meta_arr['show_if_comments'];?>" name="ips_meta[show_if_comments]" id="show_ff_comments" />
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">No Commented yet</label>
                            <div class="controls">
                                <div class="check-line" style="top: 6px;">
                                <?php
                                    $checked = '';
                                    if($meta_arr['n_show_comments']==1) $checked = 'checked="checked"';
                                ?>
                                    <input type="checkbox"  class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="check_and_h(this, '#not_yet_comments');" />
                                    <input type="hidden" value="<?php echo $meta_arr['n_show_comments'];?>" name="ips_meta[n_show_comments]" id="not_yet_comments" />
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                              <label for="" class="control-label">Visits <strong>Only</strong> From Specific Search Engines</label>
                                    <div class="controls">
                                        <div class="check-line" style="top: 6px;">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['se_google']==1) $checked = 'checked="checked"';
                                        ?>
                                            <input type="checkbox"  class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="check_and_h(this, '#vfrom_google');" /><label class="inline"> Google</label>
                                            <input type="hidden" value="<?php echo $meta_arr['se_google'];?>" name="ips_meta[se_google]" id="vfrom_google" />
                                        </div>
                                        <div class="check-line" style="top: 6px;">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['se_yahoo']==1) $checked = 'checked="checked"';
                                        ?>
                                            <input type="checkbox"  class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="check_and_h(this, '#vfrom_yahoo');" /><label class="inline"> Yahoo</label>
                                            <input type="hidden" value="<?php echo $meta_arr['se_yahoo'];?>" name="ips_meta[se_yahoo]" id="vfrom_yahoo" />
                                        </div>
                                        <div class="check-line" style="top: 6px;">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['se_bing']==1) $checked = 'checked="checked"';
                                        ?>
                                            <input type="checkbox"  class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="check_and_h(this, '#vfrom_bing');" /><label class="inline"> Bing</label>
                                            <input type="hidden" value="<?php echo $meta_arr['se_bing'];?>" name="ips_meta[se_bing]" id="vfrom_bing" />
                                        </div>
                                    </div>
                        </div>
						
                        <div class="control-group">
                              <label for="" class="control-label">Visits <strong>Only</strong> From Specific Ref</label>
                                    <div class="controls" style="vertical-align: top;">
                                        http:// <textarea onBlur="checkfh(this);" name="ips_meta[visit_from_sref]"><?php echo $meta_arr['visit_from_sref'];?></textarea>
                                    </div>
                        </div>
						<div class="control-group">
                              <label for="" class="control-label"><strong>Only</strong> Target Location: </label>
                                    <div class="controls">
                                            <?php if (!isset($k)) $k = 0;?>
                                              <input type="hidden" id='input_tag_num' value="<?php echo $k;?>" />
                                              <input type="hidden" id="hidden_country_list" name="ips_meta[show_in_country]" value="<?php echo $meta_arr['show_in_country'];?>"/>
												<select name="a" id="a" onChange="writeTagValue(this.value);" style="width: auto;">
                                                <?php
                                                $country_arr = $wpdb->get_results("SELECT * FROM {$wpdb->prefix}popup_country_d;");
  
                                                if(isset($country_arr) && count($country_arr)>0){
                                                    foreach($country_arr as $country){
                                                        ?>
                                                        <option value="<?php echo $country->ctry;?>"><?php echo $country->country;?></option>
                                                        <?php
                                                    }
                                                }
                                                ?>
												</select>
												<div id="tags_field" style="margin-top: 10px;">
                                                <?php
                                                    $k = 0;
                                                    if($meta_arr['show_in_country']!=''){
                                                      if(strpos($meta_arr['show_in_country'], ',')!==FALSE) $countries = explode(',', $meta_arr['show_in_country']);
                                                      else $countries[] = $meta_arr['show_in_country'];			
                                                      foreach($countries as $country_str){?>
                                                         <span class="tag_item" id="tag_num_<?php echo $k;?>"><span style="padding-right:10px;"><?php echo (string)$country_str;?></span><span class="remove_tag" onClick="removeTag(<?php echo $k;?>, '<?php echo (string)$country_str;?>');" title="Removing tag">x</span></span>
                                                         <?php
                                                         $k++;
                                                      }
                                                      ?>
                                                      <div class="clear"></div>
                                                      <?php
                                                    }
                                                ?>
                                            </div>
											<div class="clear"></div>
											<i class="icon-info"></i><span class="info" style="color:#9b4449;">May not work if the GeoLocation script is not able to identify the location based on IP. In this case the restriction will be disabled and the Popup will show up.</span>
									</div>
                        </div>
                        <div class="control-group">
                              <label for="" class="control-label">Session Time</label>
                                    <div class="controls">
                                        <input type="number" value="<?php echo $meta_arr['time_reset_cookie'];?>" name="ips_meta[time_reset_cookie]" min="1" class="number_input"/> minutes
										<div class="clear"></div>
										<i class="icon-info info-second"></i><span class="info info-second">Available for the below restricted features.</span>	
                                    </div>
                        </div>
                        <div class="control-group">
                              <label for="" class="control-label"><strong>Max</strong> Show On Session</label>
                                    <div class="controls">
                                        <input type="number" value="<?php echo $meta_arr['max_show_session'];?>" name="ips_meta[max_show_session]" min="1" class="number_input" style="float:left;"/>
										<div style="float:left;"><i class="icon-info"></i><span class="info">Set a higher number if you don't want to be miss it!</span></div>
                                    </div>
                        </div>
                        <div class="control-group">
                              <label for="" class="control-label">Display On Every <b id="d_visited_pge"><?php echo $meta_arr['d_on_evr_vis_pag'];?></b> Visited Pages</label>
                                    <div class="controls">
                                        <input type="number" value="<?php echo $meta_arr['d_on_evr_vis_pag'];?>" name="ips_meta[d_on_evr_vis_pag]" min="1" class="number_input" onChange="jQuery('#d_visited_pge').html(this.value);"/> page(s)
                                    </div>
                        </div>
                        <div class="control-group">
                              <label for="" class="control-label">Display After <b id="after_v_pages"><?php echo $meta_arr['d_after_vis_pag'];?></b> Visited Pages</label>
                                    <div class="controls">
                                        <input type="number" value="<?php echo $meta_arr['d_after_vis_pag'];?>" name="ips_meta[d_after_vis_pag]" min="1" class="number_input" onChange="jQuery('#after_v_pages').html(this.value);"/> page(s)
                                    </div>
                        </div>
						<div class="control-group">
                            <label for="" class="control-label">"Don't Show Again"</label>
                                <div class="controls">
                                
                                    <div class="check-line">
                                        <input type="text" value='onClick="dontShow(<?php echo $the_id;?>);"' disabled="disabled" style="width:220px; height:26px;">
										<div class="clear"></div>
										<i class="icon-info"></i><span class="info">Set that code on any object or text you want to do this action</span>
										<div class="clear"></div>
										<i class="icon-info info-second" style="padding-left: 10px;"></i><span class="info info-second">Example: &lt;span onClick="dontShow(<?php echo $the_id;?>);" &gt; Dont show the Popup &lt;/span&gt;  </span>
                                    </div>
                                </div>
                        </div>
                        
                    </div>
				 	<div id="b7" class="accordion-body collapse option_box" style="height: 0px;">
					<div class="isp_info_block">Decide <strong>how</strong> and <strong>when</strong> the Popup will Show up based on a certain Action (like scrolling, onClick) or specific time.</div>
						<div class="control-group" style="">
                            <label for="" class="control-label">Open Effects</label>
                                <div class="controls">
                                Type:
								<select name="ips_meta[general_effects]" style="width:150px;">
									<?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='') $selected = 'selected="selected"';?>
                                    <option value="" <?php echo $selected;?>></option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='fadeIn') $selected = 'selected="selected"';?>
                                    <option value="fadeIn" <?php echo $selected;?>>Fade In</option>
									<?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='vertical') $selected = 'selected="selected"';?>
                                    <option value="vertical" <?php echo $selected;?>>Slide Vertical</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='horizontal') $selected = 'selected="selected"';?>
                                    <option value="horizontal" <?php echo $selected;?>>Slide Horizontal</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='corner') $selected = 'selected="selected"';?>
                                    <option value="corner" <?php echo $selected;?>>Slide Diagonal</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show') $selected = 'selected="selected"';?>
                                    <option value="show" <?php echo $selected;?>>Show</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_blind') $selected = 'selected="selected"';?>
                                    <option value="show_blind" <?php echo $selected;?>>Blind</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_bounce') $selected = 'selected="selected"';?>
                                    <option value="show_bounce" <?php echo $selected;?>>Bounce</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_clip') $selected = 'selected="selected"';?>
                                    <option value="show_clip" <?php echo $selected;?>>Clip</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_drop') $selected = 'selected="selected"';?>
                                    <option value="show_drop" <?php echo $selected;?>>Drop</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_explode') $selected = 'selected="selected"';?>
                                    <option value="show_explode" <?php echo $selected;?>>Explode</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_fold') $selected = 'selected="selected"';?>
                                    <option value="show_fold" <?php echo $selected;?>>Fold</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_highlight') $selected = 'selected="selected"';?>
                                    <option value="show_highlight" <?php echo $selected;?>>Highlight</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_puff') $selected = 'selected="selected"';?>
                                    <option value="show_puff" <?php echo $selected;?>>Puff</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_pulsate') $selected = 'selected="selected"';?>
                                    <option value="show_pulsate" <?php echo $selected;?>>Pulsate</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_scale') $selected = 'selected="selected"';?>
                                    <option value="show_scale" <?php echo $selected;?>>Scale</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_shake') $selected = 'selected="selected"';?>
                                    <option value="show_shake" <?php echo $selected;?>>Shake</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_slideup') $selected = 'selected="selected"';?>
                                    <option value="show_slideup" <?php echo $selected;?>>Drag Down</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_slidedown') $selected = 'selected="selected"';?>
                                    <option value="show_slidedown" <?php echo $selected;?>>Drag Up</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_slideleft') $selected = 'selected="selected"';?>
                                    <option value="show_slideleft" <?php echo $selected;?>>Drag Left</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['general_effects']=='show_slideright') $selected = 'selected="selected"';?>
                                    <option value="show_slideright" <?php echo $selected;?>>Drag Right</option>
                                    
                                </select>
								<div class="clear"></div>
								<i class="icon-info"></i><span class="info">Some Effects are correlated with "General Options" settings</span>
								</div>
                                <div class="controls">
								Duration:
								<input type="text" value="<?php echo $meta_arr['general_effect_duration'];?>" name="ips_meta[general_effect_duration]" style="width:50px;" /> miliseconds
								
                                </div>
                        </div>
						<div class="control-group" style="">
                            <label for="" class="control-label">Open Event</label>
                                <div class="controls">
								  <div class="left-buttons">
								    <div class="check-line" style="top: 6px;">
                                          <?php
                                              $checked = '';
                                              if($meta_arr['open_event']=='default') $checked = 'checked="checked"';
                                          ?>
                                              <input type="radio" name="type_of_oe" value="default" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#t_of_op_e').val(this.value);" onClick="jQuery('#op_e_custom').css('display', 'none');jQuery('#scroll_controll').css('display', 'none');jQuery('#exit_message').css('display', 'none');"/><label class="inline">On Load Page</label>
                                    </div>
									<div class="check-line" style="top: 6px;">
                                          <?php
                                              $checked = '';
                                              if($meta_arr['open_event']=='exit') $checked = 'checked="checked"';
                                          ?>
                                              <input type="radio" name="type_of_oe" value="exit" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#t_of_op_e').val(this.value);" onClick="jQuery('#op_e_custom').css('display', 'none');jQuery('#scroll_controll').css('display', 'none');jQuery('#exit_message').css('display', 'block');"/><label class="inline">On Close/Leave Page</label>
                                    </div>
									<div class="check-line" style="top: 6px;">
                                          <?php
                                              $checked = '';
                                              if($meta_arr['open_event']=='leave') $checked = 'checked="checked"';
                                          ?>
                                              <input type="radio" name="type_of_oe" value="leave" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#t_of_op_e').val(this.value);" onClick="jQuery('#op_e_custom').css('display', 'none');jQuery('#scroll_controll').css('display', 'none');jQuery('#exit_message').css('display', 'none');"/><label class="inline">Try to Leave the Page</label>
                                    </div>
									<div class="check-line" style="top: 6px;">
                                          <?php
                                              $checked = '';
                                              if($meta_arr['open_event']=='click_on_page') $checked = 'checked="checked"';
                                          ?>
                                              <input type="radio" name="type_of_oe" value="click_on_page" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#t_of_op_e').val(this.value);" onClick="jQuery('#op_e_custom').css('display', 'none');jQuery('#scroll_controll').css('display', 'none');jQuery('#exit_message').css('display', 'none');"/><label class="inline">Click On Page</label>
                                    </div>                                  
									<div class="check-line" style="top: 6px;">
                                          <?php
                                              $checked = '';
                                              if($meta_arr['open_event']=='scroll') $checked = 'checked="checked"';
                                          ?>
                                              <input type="radio" name="type_of_oe" value="scroll" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#t_of_op_e').val(this.value);"  onClick="jQuery('#op_e_custom').css('display', 'none');jQuery('#scroll_controll').css('display', 'block');jQuery('#exit_message').css('display', 'none');"/><label class="inline">Scroll</label>
                                    </div>
								    <div class="check-line" style="top: 6px;">
                                          <?php
                                              $checked = '';
                                              if($meta_arr['open_event']=='custom') $checked = 'checked="checked"';
                                          ?>
                                              <input type="radio" name="type_of_oe" value="custom" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#t_of_op_e').val(this.value);"  onClick="jQuery('#op_e_custom').css('display', 'block');jQuery('#scroll_controll').css('display', 'none');jQuery('#exit_message').css('display', 'none');"/><label class="inline">Custom</label>
                                    </div>
								    
									<input type="hidden" value="<?php echo $meta_arr['open_event'];?>" name="ips_meta[open_event]" id="t_of_op_e"/>
								  </div>
									<?php		
										$show = 'none';	
										if($meta_arr['open_event']=='custom') $show = 'block';
									?>
									<div id="op_e_custom" style="display: <?php echo $show;?>; width:100%;" class="right-options">
										<div style="margin-bottom:10px;">
											<div class="labell">Target by:</div>
											<select name="ips_meta[open_e_tb]">
											<?php
												$selected= "";
												if($meta_arr['open_e_tb']=='#') $selected = "selected='selected'";
											?>
												<option value="#" <?php echo $selected;?> >ID</option>
											<?php
												$selected= "";
												if($meta_arr['open_e_tb']=='.') $selected = "selected='selected'";
											?>
												<option value="." <?php echo $selected;?> >Class</option>
											</select>
											<div class="clear"></div>
											
										</div>
										<div style="margin-bottom:10px;">
											<div class="labell">Target Name:</div>
												<input type="text" value="<?php echo $meta_arr['open_event_name'];?>" name="ips_meta[open_event_name]" style="width:220px; height:26px;" />
											<div class="clear"></div>
										</div>
										<div>
											<div class="labell">Event:</div> 
											<select name="ips_meta[the_event]">
											<?php
												$selected= "";
												if($meta_arr['the_event']=='click') $selected = "selected='selected'";
											?>
												<option value="click" <?php echo $selected;?> >Click</option>
											<?php
												$selected= "";
												if($meta_arr['the_event']=='mouseover') $selected = "selected='selected'";
											?>
												<option value="mouseover" <?php echo $selected;?> >Mouse Over</option>
											</select>
											<div class="clear"></div>
										</div>
										<i class="icon-info" style="margin-left: -10px;"></i><span class="info">Set targeted object from your Content Page that will open the PopPup</span>
										<div class="clear"></div>
										<i class="icon-info" style="margin-left: -10px;"></i><span class="info" style="color:#9b4449">The object should be already set into the Content Page!</span>
										<div class="clear"></div>
										<br/>
										<i class="icon-info info-second"></i><span class="info info-second">Ex for Click: &lt;img src="...." id="trigger" /&gt; -> Target by:ID, Target Name:trigger, Event:Click</span>	
										<div class="clear"></div>
										<i class="icon-info info-second"></i><span class="info info-second">Ex for Hover: &lt;button class="click-here" /&gt; -> Target by:Class, Target Name:click-here, Event:Over</span>
									</div>
									<?php		
										$show = 'none';	
										if($meta_arr['open_event']=='scroll') $show = 'block';
									?>									
									<div id="scroll_controll" style="display: <?php echo $show;?>; max-width:300px;" class="right-options">
										Scroll Position:
										<br/>
										<select name="ips_meta[scroll_position]">
											<?php
												$selected= "";
												if($meta_arr['scroll_position']=='top') $selected = "selected='selected'";
											?>
												<option value="top" <?php echo $selected;?> >Top</option>
											<?php
												$selected= "";
												if($meta_arr['scroll_position']=='bottom') $selected = "selected='selected'";
											?>
												<option value="bottom" <?php echo $selected;?> >Bottom</option>														
										</select>
										<div class="clear"></div>
										<i class="icon-info info-second"></i><span class="info info-second">The Popup will show up when the scroll will arrive on this position after was moved.</span>	
									</div>
									<?php		
										$show = 'none';	
										if($meta_arr['open_event']=='exit') $show = 'block';
									?>									
									<div id="exit_message" style="display: <?php echo $show;?>" class="right-options">
										Exit Message:
										<br/>
										<input type="text" name="ips_meta[exit_mess]" style="width:350px; height:26px;" value="<?php echo $meta_arr['exit_mess']; ?>"/>
										<br/>
										<i class="icon-info"></i><span class="info">Use "\n" for a new line.</span>	
									</div>
									
								</div>
						</div>
						<div class="control-group" style="">
                            <label for="" class="control-label">Close Effects</label>
                                <div class="controls">
                                Type:
								<select name="ips_meta[close_effects]" style="width:150px;">
									<?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='') $selected = 'selected="selected"';?>
                                    <option value="" <?php echo $selected;?>></option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='fadeOut') $selected = 'selected="selected"';?>
                                    <option value="fadeOut" <?php echo $selected;?>>Fade Out</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='blind') $selected = 'selected="selected"';?>
                                    <option value="blind" <?php echo $selected;?>>Blind</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='bounce') $selected = 'selected="selected"';?>
                                    <option value="bounce" <?php echo $selected;?>>Bounce</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='clip') $selected = 'selected="selected"';?>
                                    <option value="clip" <?php echo $selected;?>>Clip</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='drop') $selected = 'selected="selected"';?>
                                    <option value="drop" <?php echo $selected;?>>Drop</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='explode') $selected = 'selected="selected"';?>
                                    <option value="explode" <?php echo $selected;?>>Explode</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='fold') $selected = 'selected="selected"';?>
                                    <option value="fold" <?php echo $selected;?>>Fold</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='highlight') $selected = 'selected="selected"';?>
                                    <option value="highlight" <?php echo $selected;?>>Highlight</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='puff') $selected = 'selected="selected"';?>
                                    <option value="puff" <?php echo $selected;?>>Puff</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='pulsate') $selected = 'selected="selected"';?>
                                    <option value="pulsate" <?php echo $selected;?>>Pulsate</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='scale') $selected = 'selected="selected"';?>
                                    <option value="scale" <?php echo $selected;?>>Scale</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='shake') $selected = 'selected="selected"';?>
                                    <option value="shake" <?php echo $selected;?>>Shake</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='slide_up') $selected = 'selected="selected"';?>
                                    <option value="slide_up" <?php echo $selected;?>>Drag Down</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='slide_down') $selected = 'selected="selected"';?>
                                    <option value="slide_down" <?php echo $selected;?>>Drag Up</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='slide_left') $selected = 'selected="selected"';?>
                                    <option value="slide_left" <?php echo $selected;?>>Drag Left</option>
                                    <?php
                                        $selected = '';
                                        if($meta_arr['close_effects']=='slide_right') $selected = 'selected="selected"';?>
                                    <option value="slide_right" <?php echo $selected;?>>Drag Right</option>
                                </select>
								<div class="clear"></div>
								<i class="icon-info"></i><span class="info">Some Effects are correlated with "General Options" settings</span>
								</div>
                                <div class="controls">
								Duration:
								<input type="text" value="<?php echo $meta_arr['close_effect_duration'];?>" name="ips_meta[close_effect_duration]" style="width:50px;" /> miliseconds
                                </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Delay</label>
                                <div class="controls">
                                    <div>Delay: <span id="delay_ui_v"><?php echo $meta_arr['general_delay'];?></span>s</div>
                                    <div id="general_d" title="<?php echo $meta_arr['general_delay'];?>s"></div>
                                        <script type="text/javascript">
                                        jQuery(document).ready(function() {
                                              var from = "#general_d";
                                              var hidden_i = "#general_delay";
                                              jQuery(from).slider({
                                              min: 0, //minimum value
                                              max: 30, //maximum value
                                              step: 0.1,
                                              value: "<?php echo $meta_arr['general_delay'];?>", //default value
                                              slide: function(event, ui) {
                                                var the_val = ui.value;
                                                jQuery(hidden_i).val(the_val);
                                                jQuery(from).attr("title", the_val+"s");
                                                jQuery("#delay_ui_v").html(the_val);
                                              }
                                          });
                                        });
                                        </script>
                                    <input type="hidden" value="<?php echo $meta_arr['general_delay'];?>" name="ips_meta[general_delay]" id="general_delay" />
                                </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Duration</label>
                                <div class="controls">
                                    <div>Duration: <span id="duration_ui_v"><?php echo $meta_arr['general_duration'];?></span>s</div>
                                    <div id="general_duration_s" title="<?php echo $meta_arr['general_duration'];?>s"></div>
                                        <script type="text/javascript">
                                        jQuery(document).ready(function() {
                                              var from = "#general_duration_s";
                                              var hidden_i = "#general_duration";
                                              jQuery(from).slider({
                                              min: 0, //minimum value
                                              max: 120, //maximum value
                                              step: 1,
                                              value: "<?php echo $meta_arr['general_duration'];?>", //default value
                                              slide: function(event, ui) {
                                                var the_val = ui.value;
                                                jQuery(hidden_i).val(the_val);
                                                jQuery(from).attr("title", the_val+"s");
                                                jQuery("#duration_ui_v").html(the_val);
                                              }
                                          });
                                        });
                                        </script>
                                    <input type="hidden" value="<?php echo $meta_arr['general_duration'];?>" name="ips_meta[general_duration]" id="general_duration" />
									<i class="icon-info info-second"></i><span class="info info-second">The Popup will auto-close after a certain time.</span>	
                                </div>
                        </div>
                     </div>
				 	<div id="b8" class="accordion-body collapse option_box" style="height: 0px;">					
                    <div class="isp_info_block">Build different Popup for different devices or Customize the Popup for Mobile devices with the most on-demand features.</div>
                        <div class="control-group">
                              <label for="" class="control-label">Display on <strong>Only</strong> Users Device</label>
                                    <div class="controls">
                                        <div class="check-line" style="top: 6px;">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['web_mobile_display']=='Web&Mobile') $checked = "checked='checked'";
                                        ?>
                                            <input type="radio" name="webmod_type" value="Web&Mobile" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#web_or_mobile').val(this.value);jQuery('#mobile_features_div').attr('class', 'display_true_div');" /><label class="inline">Web & Mobile</label>
                                        </div>
                                        <div class="check-line" style="top: 6px;">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['web_mobile_display']=='Mobile') $checked = "checked='checked'";
                                        ?>
                                            <input type="radio" name="webmod_type" value="Mobile" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#web_or_mobile').val(this.value);jQuery('#mobile_features_div').attr('class', 'display_true_div');" /><label class="inline">Only Mobile</label>
                                        </div>
                                        <div class="check-line" style="top: 6px;">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['web_mobile_display']=='Web') $checked = "checked='checked'";
                                        ?>
                                            <input type="radio" name="webmod_type" value="Web" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#web_or_mobile').val(this.value);jQuery('#mobile_features_div').attr('class', 'display_false_div');" /><label class="inline">Only Web</label>
                                        </div>
                                        <input type="hidden" value="<?php echo $meta_arr['web_mobile_display'];?>" name="ips_meta[web_mobile_display]" id="web_or_mobile" />
                                    </div>
                        </div>
                        <?php
                            $class = 'display_false_div';
                            if($meta_arr['web_mobile_display']!='web') $class = 'display_true_div';
                        ?>
                    <div id="mobile_features_div" class="<?php echo $class;?>">
                         <div class="control-group">
                              <label for="" class="control-label">Max Width</label>
                                    <div class="controls">
                                            <div>Max Width: <span id="mx_wd_mob"><?php echo $meta_arr['max_width_mobile'];?></span>%</div>
                                                      <div style="width:100%; max-width: 600px;">
                                                            <div id="wd_mx_mob" title="<?php echo $meta_arr['max_width_mobile'];?>%" style=""></div>
                                                                <script type="text/javascript">
                                                                jQuery(document).ready(function() {
                                                                      var from = "#wd_mx_mob";
                                                                      var hidden_i = "#max_w_mob";
                                                                      jQuery(from).slider({
                                                                      min: 1, //minimum value
                                                                      max: 100, //maximum value
                                                                      value: "<?php echo $meta_arr['max_width_mobile'];?>", //default value
                                                                      slide: function(event, ui) {
                                                                        var the_val = ui.value;
                                                                        jQuery(hidden_i).val(the_val);
                                                                        jQuery(from).attr("title", the_val+"%");
                                                                        jQuery("#mx_wd_mob").html(the_val);
                                                                      }
                                                                  });
                                                                });
                                                                </script>
                                                            <input type="hidden" value="<?php echo $meta_arr['max_width_mobile'];?>" name="ips_meta[max_width_mobile]" id="max_w_mob" />
                                                      </div>
									</div>
                         </div>
                         <div class="control-group">
                              <label for="" class="control-label">Active Min-Height</label>
                                    <div class="controls">
                                         <?php
                                            $checked = '';
                                            if($meta_arr['min_height_mob']==1) $checked = 'checked="checked"';
                                        ?>
                                          <div class="check-line" style="float:left;">
                                              <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#min_h_mobile')"/>
                                              <input type="hidden" value="<?php echo $meta_arr['min_height_mob'];?>" name="ips_meta[min_height_mob]" id="min_h_mobile"/>
                                          </div>
										   <i class="icon-info"></i><span class="info">Select a dynamic Height for resizable PopUp (Responsive Recommended) </span>
                                    </div>
                         </div>
                          <div class="control-group">
                              <label for="" class="control-label">Close by Click on PopUp</label>
                                    <div class="controls">
                                          <?php
                                            $checked = '';
                                            if($meta_arr['mob_tap_close']==1) $checked = 'checked="checked"';
                                          ?>
                                          <div class="check-line">
                                              <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#mobile_tap_cl')"/>
                                              <input type="hidden" value="<?php echo $meta_arr['mob_tap_close'];?>" name="ips_meta[mob_tap_close]" id="mobile_tap_cl"/>
                                          </div>
                                    </div>
                         </div>
                          <div class="control-group">
                              <label for="" class="control-label">Display <strong>Only</strong> On Android</label>
                                    <div class="controls">
                                           <?php
                                            $checked = '';
                                            if($meta_arr['android_checked']==1) $checked = 'checked="checked"';
                                          ?>
                                          <div class="check-line" style="float:left;">
                                              <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#andr_checked')"/>
                                              <input type="hidden" value="<?php echo $meta_arr['android_checked'];?>" name="ips_meta[android_checked]" id="andr_checked"/>
                                          </div>
										  <i class="icon-info"></i><span class="info">Set the PopUp available only for Android Devices </span>
                                    </div>
                         </div>
                           <div class="control-group">
                              <label for="" class="control-label">Display <strong>Only</strong> On IOS</label>
                                    <div class="controls">
                                            <?php
                                            $checked = '';
                                            if($meta_arr['ios_checked']==1) $checked = 'checked="checked"';
                                          ?>
                                          <div class="check-line" style="float:left;">
                                              <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#ios_checked')"/>
                                              <input type="hidden" value="<?php echo $meta_arr['ios_checked'];?>" name="ips_meta[ios_checked]" id="ios_checked"/>
                                          </div>
										   <i class="icon-info"></i><span class="info">Set the PopUp available only for IOS Devices </span>
                                    </div>
                         </div>
                    </div>
                    </div>
					<div id="b9" class="accordion-body collapse option_box" style="height: 0px;">			
                    <div class="isp_info_block">This section will Activate the Popup and is very useful to showing Popups on multiple pages on the same time automatically.</div>
					<div class="isp_warning">May not work for special special custom WP structure. In this case use the <strong>ShortCode</strong> option. Use <strong>Except</strong> just to <strong>exclude</strong> some pages!</div>
                            <div class="control-group">
                                <label for="" class="control-label">Home</label>
                                <div class="controls">
                                    <div class="check-line" style="float:left;">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['showin_home']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#show_in_home');"/>
                                        <input type="hidden" name="ips_meta[showin_home]" id="show_in_home" value="<?php echo $meta_arr['showin_home'];?>" />
                                    </div>
									<div style="float:left;"><i class="icon-info"></i><span class="info">For Custom HomePage select your Post Type also!</span></div>
                                </div>
                            </div>
							<div class="control-group">
                                <label for="" class="control-label">Pages</label>
                                <div class="controls">
                                    <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['showin_pages']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#show_in_pages');jQuery('#hide_the_pages').toggle();"/>
                                        <input type="hidden" name="ips_meta[showin_pages]" id="show_in_pages" value="<?php echo $meta_arr['showin_pages'];?>" />
                                        <?php
                                            $class = 'showin_hide';
                                            if($meta_arr['showin_pages']==1) $class = 'showin_show';
                                        ?>
                                        <div id="hide_the_pages" class="<?php echo $class;?>">
                                            <?php
                                               $excluded_pages = array();
                                               if($meta_arr['excluded_pages']!='') $excluded_pages = unserialize(base64_decode($meta_arr['excluded_pages']));
                                               $h_pages = get_pages();
                                               if(isset($h_pages) && count($h_pages)>0){
                                            ?>
                                               <b>Except:</b>
                                               <div class="showin_content">
                                               <?php
                                                        foreach($h_pages as $h_page){
                                                            $checked = '';
                                                            if(in_array($h_page->ID, $excluded_pages)) $checked = 'checked="checked"';
                                                            ?>
                                                                <div><input type="checkbox" <?php echo $checked;?> value="<?php echo $h_page->ID;?>" name="ips_meta[excluded_pages][]" /> <?php echo $h_page->post_name;?></div>
                                                            <?php
                                                        }
                                                ?>
                                                </div>
                                                <?php
                                                    }
                                                ?>
                                                <input type="hidden" value="" name="ips_meta[excluded_pages][]"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Posts</label>
                                <div class="controls">
                                    <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['showin_posts']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#show_in_posts');jQuery('#hide_the_posts').toggle();"/>
                                        <input type="hidden" name="ips_meta[showin_posts]" id="show_in_posts" value="<?php echo $meta_arr['showin_posts'];?>" />
                                        <?php
                                            $class = 'showin_hide';
                                            if($meta_arr['showin_posts']==1) $class = 'showin_show';
                                        ?>
                                        <div id="hide_the_posts" class="<?php echo $class;?>">
                                                <?php
                                                    $excluded_posts = array();
                                                    if($meta_arr['excluded_posts']!='') $excluded_posts = unserialize(base64_decode($meta_arr['excluded_posts']));
                                                    $h_posts = $wpdb->get_results("SELECT post_title, ID, post_type
                                                    									FROM {$wpdb->prefix}posts
                                                    									WHERE post_status='publish'
                                                    									AND post_type='post'
                                                    									LIMIT 10000
                                                    								;");
                                                    if(isset($h_posts) && count($h_posts)>0){
                                                ?>
                                                <b>Except:</b>
                                                <div class="showin_content">
                                                    <?php
                                                        foreach($h_posts as $h_post){
                                                            $checked = '';
                                                            if(in_array($h_post->ID, $excluded_posts)) $checked = 'checked="checked"';
                                                            ?>
                                                                <div><input type="checkbox" <?php echo $checked;?> value="<?php echo $h_post->ID;?>" name="ips_meta[excluded_posts][]" /> <?php echo $h_post->post_title;?></div>
                                                            <?php
                                                        }
                                                        ?>
                                                  </div>
                                                <?php
                                                    }
                                                ?>
                                                <input type="hidden" value="" name="ips_meta[excluded_posts][]"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Custom Post Types</label>
                                <div class="controls">
                                    <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['showin_posts_types']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#show_in_posts_types');jQuery('#hide_the_posts_types').toggle();"/>
                                        <input type="hidden" name="ips_meta[showin_posts_types]" id="show_in_posts_types" value="<?php echo $meta_arr['showin_posts_types'];?>" />
                                        <?php
                                            $class = 'showin_hide';
                                            if($meta_arr['showin_posts_types']==1) $class = 'showin_show';
                                        ?>
                                        <div id="hide_the_posts_types" class="<?php echo $class;?>">
                                                <?php
                                                    $excluded_posts = array();
                                                    if(@$meta_arr['excluded_posts_types']!='') @$excluded_posts = unserialize(base64_decode($meta_arr['excluded_posts_types']));
													$post_types = get_post_types( );
                                                    if(isset($post_types) && count($post_types)>0){
                                                ?>
                                                    <b>Except:</b>
                                                    <div class="showin_content">
                                                <?php
                                                        foreach($post_types as $post_type){
    													  if($post_type != 'page' && $post_type != 'post'){
                                                            $checked = '';
                                                            if(in_array($post_type, $excluded_posts)) $checked = 'checked="checked"';
                                                            ?>
                                                                <div><input type="checkbox" <?php echo $checked;?> value="<?php echo $post_type ;?>" name="ips_meta[excluded_posts_types][]" /> <?php echo $post_type;?></div>
                                                            <?php
    													  }
                                                        }
                                                    ?>
                                                    </div>
                                                <?php
                                                    }
                                                ?>
                                                <input type="hidden" value="" name="ips_meta[excluded_posts_types][]"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Categories</label>
                                <div class="controls">
                                     <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['showin_cats']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#show_in_cats');jQuery('#hide_the_cats').toggle();"/>
                                        <input type="hidden" name="ips_meta[showin_cats]" id="show_in_cats" value="<?php echo $meta_arr['showin_cats'];?>" />
                                        <?php
                                            $class = 'showin_hide';
                                            if($meta_arr['showin_cats']==1) $class = 'showin_show';
                                        ?>
                                        <div id="hide_the_cats" class="<?php echo $class;?>">
                                            <?php
                                                $excluded_cats = array();
                                                if($meta_arr['excluded_cats']!='') $excluded_cats = unserialize(base64_decode($meta_arr['excluded_cats']));
                                                $args = array('hide_empty' => 0);
                                                $categories = get_categories($args);
                                                if(isset($categories) && count($categories)>0){
                                            ?>
                                                <b>Except:</b>
                                                <div class="showin_content">
                                                <?php
                                                    foreach($categories as $cat){
                                                        $checked = '';
                                                        if(in_array($cat->cat_ID, $excluded_cats)) $checked = 'checked="checked"';
                                                        ?>
                                                            <div><input type="checkbox" <?php echo $checked;?> value="<?php echo $cat->cat_ID;?>" name="ips_meta[excluded_cats][]" /> <?php echo $cat->name;?></div>
                                                        <?php
                                                    }
                                            ?>
                                                </div>
                                            <?php
                                                }
                                            ?>
                                                <input type="hidden" value="" name="ips_meta[excluded_cats][]"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="control-group">
                                <label for="" class="control-label">Archive</label>
                                <div class="controls">
                                    <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['showin_arhive']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#show_in_arhive');"/>
                                        <input type="hidden" name="ips_meta[showin_arhive]" id="show_in_arhive" value="<?php echo $meta_arr['showin_arhive'];?>" />
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Templates</label>
                                <div class="controls">
                                     <div class="check-line">
                                        <?php
                                            $checked = '';
                                            if($meta_arr['showin_templates']==1) $checked = 'checked="checked"';
                                        ?>
                                        <input type="checkbox" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onClick="check_and_h(this, '#show_in_templates');jQuery('#excluded_temp').toggle();"/>
                                        <input type="hidden" name="ips_meta[showin_templates]" id="show_in_templates" value="<?php echo $meta_arr['showin_templates'];?>" />
                                        <?php
                                            $class = 'showin_hide';
                                            if($meta_arr['showin_templates']==1) $class = 'showin_show';
                                        ?>
                                        <div id="excluded_temp" class="<?php echo $class;?>">
                                            <?php
                                                $excluded_templates = array();
                                                if($meta_arr['excluded_templates']!='') $excluded_templates = unserialize(base64_decode($meta_arr['excluded_templates']));
                                                $templates = get_page_templates();
                                                if(isset($templates) && count($templates)>0){
                                            ?>
                                                <b>Except:</b>
                                                <div class="showin_content">
                                                <?php
                                                    foreach($templates as $key => $h_tempt){
                                                        $checked = '';
                                                        if(in_array(basename($h_tempt), $excluded_templates)) $checked = 'checked="checked"';
                                                        ?>
                                                            <div><input type="checkbox" <?php echo $checked;?> value="<?php echo basename($h_tempt);?>" name="ips_meta[excluded_templates][]" /> <?php echo basename($key);?></div>
                                                        <?php
                                                        }
                                                        ?>
                                                </div>
                                            <?php
                                                }
                                            ?>
                                            <input type="hidden" value="" name="ips_meta[excluded_templates][]"/>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="control-group">
                                <label for="" class="control-label">Specified Page</label>
                                <div class="controls">
                                    <input type="text" onBlur="checkfh(this);" value="<?php echo $meta_arr['s_page_show_in'];?>" name="ips_meta[s_page_show_in]" style="width: 500px;"/>
									<div class="clear"></div>
									<i class="icon-info"></i><span class="info">Set the Full URL</span>
                                </div>
                            </div>
                    </div> 
					<div id="b10" class="accordion-body collapse option_box" style="height: 0px;">
					<div class="isp_info_block">Customize Special Campaigns where the Time is critical or Leave a message when your schedule is off.</div>
					<div class="isp_warning">This Section may restrict the PopUp to show up and you will not be able to see it. Leave the settings default for <strong>Testing</strong> stage!</div>
                        
						<div class="control-group">
                            <label for="" class="control-label"><strong>Only</strong> Users from Timezone</label>
                            <div class="controls">
                                <div class="check-line" style="top: 6px;">
                                    <select multiple name="ips_meta[timezone][]">
                                    <?php
                                        foreach($utc_arr as $k=>$v){
                                            $selected = '';
                                            if(in_array($k, $timezone_sel)) $selected = 'selected="selected"';
                                        ?>
                                            <option value="<?php echo $k;?>" <?php echo $selected;?>><?php echo $k;?> ( <?php echo $v;?> )</option>
                                        <?php
                                        }
                                    ?>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="control-group">
                              <label for="" class="control-label">Date Interval</label>
                                    <div class="controls" style="top: 6px;">
                                    <table>
                                        <tr>
                                            <td>From: </td>
                                            <td>
                                                <input type="text" class="date_pick" id="date_pick_from" value="<?php echo $meta_arr['ti_from'];?>" name="ips_meta[ti_from]"/>
                                            </td>
                                        </tr>
                                        <tr>
                                            <td>Until: </td>
                                            <td>
                                                <input type="text" class="date_pick" id="date_pick_until" value="<?php echo $meta_arr['ti_until'];?>" name="ips_meta[ti_until]"/>
                                            </td>
                                        </tr>
                                    </table>			
									<i class="icon-info"></i><span class="info">Set blank for an unset date Interval</span>
                                    </div>
                        </div>
                        <div class="control-group">
                              <label for="" class="control-label">Date Behavior</label>
                                    <div class="controls">
                                        <div>Repeat:</div>
                                          <div class="check-line" style="top: 6px;">
                                          <?php
                                              $checked = '';
                                              if($meta_arr['date_b_repeat']=='daily') $checked = 'checked="checked"';
                                          ?>
                                              <input type="radio" name="date_behavior" value="daily" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#repeat_type_behavior').val(this.value);" /><label class="inline">Daily</label>
                                          </div>
                                          <div class="check-line" style="top: 6px;">
                                          <?php
                                              $checked = '';
                                              if($meta_arr['date_b_repeat']=='week_day') $checked = 'checked="checked"';
                                          ?>
                                              <input type="radio" name="date_behavior" value="week_day" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#repeat_type_behavior').val(this.value);" /><label class="inline">Week Day</label>
                                          </div>
                                          <div class="check-line" style="top: 6px;">
                                          <?php
                                              $checked = '';
                                              if($meta_arr['date_b_repeat']=='week_end') $checked = 'checked="checked"';
                                          ?>
                                              <input type="radio" name="date_behavior" value="week_end" class="icheck-me" data-skin="minimal" <?php echo $checked;?> onChange="jQuery('#repeat_type_behavior').val(this.value);" /><label class="inline">Week-End</label>
                                          </div>
                                          <input type="hidden" value="<?php echo $meta_arr['date_b_repeat'];?>" name="ips_meta[date_b_repeat]" id="repeat_type_behavior"/>
                                    </div>
                        </div>
                        <div class="control-group">
                            <label for="" class="control-label">Repeat Time Interval: </label>
                                    <div class="controls" style="top: 6px;">
                                        <table>
                                            <tr>
                                                <td>From: </td>
                                                <td>
                                                    <input type="text" value="<?php echo $meta_arr['time_int_from'];?>" name="ips_meta[time_int_from]" class="time_pick" id="time_pick_from" />
                                                </td>
                                            </tr>
                                            <tr>
                                                <td>Until: </td>
                                                <td>
                                                    <input type="text" value="<?php echo $meta_arr['time_int_until'];?>" name="ips_meta[time_int_until]" class="time_pick" id="time_pick_until" />
                                                </td>
                                            </tr>
                                        </table>
										<i class="icon-info"></i><span class="info">Set blank for an unset time Interval</span>
                                    </div>
                        </div>
                </div> 				
					<div class="clear"></div>
				 </div>
				 <div class="clear"></div>
				</div>
				<div class="clear"></div>                        
				<div class="isp_info_block" style="box-sizing:border-box; height:45px;">To activate the PopUp and make it show up you need to use the ShortCode or the "Show" In section</div>
            </div>		
<!----------------------------Submit Button-------------------------------------------->			
            <div class="bttn_wrap">
                        <input type='hidden' value="<?php echo $the_id;?>" name="popup_id" id="popup_id" />
                    <?php if(isset($edit) && $edit==1){?>
                        <input type="submit" value="Update" name="update_bttn" class="form_bttn" />
                    <?php
                    }else{
                    ?>
                        <input type="submit" value="Save" name="save_bttn" class="form_bttn" />
                    <?php
                    }
                    ?>
            </div>
        </div>
      </div><!-- end of isp_wrap_options_div -->
    </div>
</form>
</div>

<script>
/*
 * Slider admin functions
 */
jQuery("#add_row").click(function(){
	var parent = jQuery(this).parent();
	var ul = parent.find('ul');
	ul.append('<li class="ui-state-default slider_li_t"><img onclick="jQuery(this).parent().remove();" class="close_slide_icon" src="<?php echo ISP_DIR_URL;?>assets/img/close_2.png" ><input type="text" class="slider-input" name="ips_meta[slider][]" onclick="ips_open_media_up(this);" /></li>');
});
function trim(str, chars) {  
  	return ltrim(rtrim(str, chars), chars);  
}  
function ltrim(str, chars) {  
  	chars = chars || "\\s";  
  	return str.replace(new RegExp("^[" + chars + "]+", "g"), "");  
}  
function rtrim(str, chars) {  
  	chars = chars || "\\s";  
  	return str.replace(new RegExp("[" + chars + "]+$", "g"), "");  
}  
function MakeLinkSafe(){
 	var e = jQuery('.slider-input');
  	e.each(function(){
		str = trim(jQuery(this).val());
	    if(str.substr(0, 7) == 'http://'){  
	    	str= str.substr(7);  
	    }
	    if(str.substr(0, 8) == 'https://'){  
	    	str= str.substr(7);  
	    }
	    jQuery(this).val(str);   
    });  
  	return true;  
}  	  
jQuery(".form_bttn").click(function(event){
	MakeLinkSafe();
});

/*
 * Slider admin functions end
 */
</script>

<!--textarea rows="50" cols="150">
<?php 
/*
 //debug
foreach($meta_arr as $k=>$v) {
	$v = stripslashes(htmlspecialchars($v));
	echo '$meta_arr'."['$k'] = '$v'; \n";
}
*/
?>
</textarea -->