<?php 
$url = admin_url('admin.php');
?>
<style>
/*TO EXCLUDE THE UPDATE INFO*/
.update-nag, .updated{
display:none;
}
</style>
<div class="popup_header">
	<div class="isp_left_side">
	<a href="admin.php?page=ips_admin"><img src="<?php echo ISP_DIR_URL;?>admin/assets/img/dashboard-logo.jpg"/></a>
	</div>
	<div class="isp_right_side">
		<ul>
			<li class="<?php if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='add_edit_page')echo 'selected';?>">
				<a href="<?php echo $url;?>?page=ips_admin&tab=add_edit_page">
					<div class="isp_page_title">
					<i class="icon-edit"></i>
					Add New</div>
				</a>
			</li>

			<?php /****************** PopUp Under **************/ ?>
				
					<li class="<?php if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='popup_under')echo 'selected';?>">
					  <?php $add_class = '';
							if( is_plugin_active('indeed-popup-under/indeed-popup-under.php') ){ ?>
								<a href="<?php echo $url;?>?page=ips_admin&tab=popup_under">
									<div class="isp_page_title isp-addon <?php echo $add_class; ?>">
										<i class="icon-popup_under"></i>
										PopUp Under
									</div>
								</a>
							<?php }else{ ?>								
							<div class="isp_page_title isp-addon isp-deactivate">
								<i class="icon-popup_under"></i>
								PopUp Under
							</div>	
							<?php }	?>	
						
					</li>

			
			<li class="<?php if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='add_from_template')echo 'selected';?>">
				<a href="<?php echo $url;?>?page=ips_admin&tab=add_from_template">
					<div class="isp_page_title">
					<i class="icon-dashboard"></i>

						Use Templates</div>
				</a>
			</li>
			<li class="<?php if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='ips_manage')echo 'selected';?>">
				<a href="<?php echo $url;?>?page=ips_admin&tab=ips_manage">
					<div class="isp_page_title">
					<i class="icon-list-ul"></i>
						Manage</div>
				</a>
			</li>
			<li class="<?php if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='opt_in_settings')echo 'selected';?>">
				<a href="<?php echo $url;?>?page=ips_admin&tab=opt_in_settings">
					<div class="isp_page_title">
					<i class="icon-cog"></i>
						Opt-in Settings</div>
					</li>
				</a>
			<li class="<?php if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='ips_stats')echo 'selected';?>">
				<a href="<?php echo $url;?>?page=ips_admin&tab=ips_stats">
					<div class="isp_page_title">
					<i class="icon-bar-chart"></i>
						Statistics</div>
				</a>
			</li>
			<li class="<?php if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='export_stats')echo 'selected';?>">
				<a href="<?php echo $url;?>?page=ips_admin&tab=export_stats">
					<div class="isp_page_title">
					<i class="icon-download-alt"></i>
						Export Results</div>
				</a>
			</li>
			<li class="<?php if(isset($_REQUEST['tab']) && $_REQUEST['tab']=='help')echo 'selected';?>">
			<div class="isp_page_title">
				<a href="<?php echo $url;?>?page=ips_admin&tab=help">
					<i class="icon-question-sign"></i>
						Help</div>
				</a>
			</li>
		</ul>
	</div>
	<div class="clear"></div>
</div>
<div class="popup_header_underline"></div>
<?php 
if(isset($_REQUEST['tab']) && $_REQUEST['tab']!=''){
	switch($_REQUEST['tab']){
		case 'ips_manage':
			include_once ISP_DIR_PATH.'admin/includes/ips_manage.php';
		break;
		case 'add_edit_page':
			include_once ISP_DIR_PATH.'admin/includes/add_edit_popups.php';
		break;
		case 'add_from_template':
			include_once ISP_DIR_PATH.'admin/includes/add_from_template.php';
		break;
		case 'opt_in_settings':
			include_once ISP_DIR_PATH.'admin/includes/opt_in_settings.php';
		break;
		case 'ips_stats':
			include_once ISP_DIR_PATH.'admin/includes/ips_stats.php';
		break;	
		case 'export_stats':
			include_once ISP_DIR_PATH.'admin/includes/export_stats.php';
		break;	
		case 'help':
			include_once ISP_DIR_PATH.'admin/includes/help.php';
		break;	
		/********************** PopUp Under *****************/
		case 'popup_under':
			if( is_plugin_active('indeed-popup-under/indeed-popup-under.php') && function_exists('ipu_admin') ){
				ipu_admin();
			}else{
				//popup under is not active go to manage page
				include_once ISP_DIR_PATH.'admin/includes/ips_manage.php';
			}
		break;
		/********************** PopUp Under *****************/
		default:
			if(ips_has_items()) include_once ISP_DIR_PATH.'admin/includes/ips_home.php';
			else include_once ISP_DIR_PATH.'admin/includes/add_edit_popups.php';
		break;
	}	
}else{
	if(ips_has_items()) include_once ISP_DIR_PATH.'admin/includes/ips_home.php';
	else include_once ISP_DIR_PATH.'admin/includes/add_edit_popups.php';
}
?>