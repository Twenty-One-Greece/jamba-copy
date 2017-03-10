<?php
/**
 * SCREETS © 2016
 *
 * Chat Console
 *
 * COPYRIGHT © 2016 Screets d.o.o. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 *
 * @package Chat X
 * @author Screets
 *
 */

if ( ! defined( 'ABSPATH' ) ) exit;
global $ChatX;
?>

<div id="scx-console" class="scx-w scx-container">
	
	<!-- Settings popup -->
	<div id="scx-settings" class="scx-settings">
		<div class="scx-wrap">

			<h3><?php _e( 'Operator Settings', 'schat' ); ?></h3>
			
			<div class="scx-ntf scx-ntf-settings"></div>

			<form action="" class="scx-form-settings">
				<div class="scx-field">
					<label><span class="scx-red">*</span> <?php _e( 'Pushover Device Name', 'schat' ); ?>: <a class="scx-tooltip scx-tooltip-r" data-title="To find your device name, open your Pushover app in your mobile/desktop and go to settings.">[?]</a></label>
					<input type="text" name="pushover-device" id="scx-f-pushover-device" placeholder="Your device name">
					<small><?php _e( 'Separate multiple devices by a comma ","', 'schat' ); ?></small>
				</div>

				<div class="scx-field">
					<label><?php _e( 'Notifications', 'schat' ); ?>:</label>

					<label>
						<small>
							<input type="checkbox" name="no-ntf-new-visitor" id="scx-f-no-ntf-new-visitor" value="1"> <?php _e( 'Disable new visitor notifications', 'schat' ); ?></small>
					</label>

					<label>
						<small>
							<input type="checkbox" name="no-ntf-new-msg" id="scx-f-no-ntf-new-msg" value="1"> <?php _e( 'Disable new message notifications', 'schat' ); ?></small>
					</label>
				</div>
				
				<div class="scx-field">
					<label>Tools:</label>
					<a href="javascript:;" id="scx-btn-reset" class="button scx-btn-reset scx-tooltip " style="color:#e54045" data-title="<?php _e( 'It is helpful if something wrong in chat connections or chat transmissions. It doesn\'t delete chat data', 'schat' ); ?>"><?php _e( 'Restart', 'schat' ); ?></a>

				</div>

			</form>
		</div>
	</div>
	<div class="scx-overlay"></div>

	<!-- Console header -->
	<div class="scx-top-nav scx-group">
		<div class="scx-logo">
			<img src="<?php echo SCX_URL; ?>/assets/img/NB-logo-150px.png" alt="Night Bird">
		</div>

		<div class="scx-version"><?php echo SCX_VERSION; ?></div>
		
		<!-- Top navigation -->
		<ul class="scx-nav">
			<li><a href="<?php echo SCX_PLUGIN_URL; ?>" target="_blank"><strong><?php echo SCX_NAME_SHORT; ?></strong> <?php echo SCX_EDITION; ?></a></li>
			<li><a href="javascript:;" id="scx-btn-conn" class="scx-btn-conn _scx-connecting"><span class="dashicons dashicons-update"></span> <?php _e( 'Connecting', 'schat' ); ?>...</a></li>
			<li><a href="javascript:;" id="scx-btn-settings" class="scx-btn-settings"><i class="scx-ico scx-ico-options"></i><?php _e( 'Settings', 'schat' ); ?></a></li>
		</ul>

	</div>

	<!-- Main notifications -->
	<div class="scx-ntf scx-ntf-main"></div>

	<!-- Console main content -->
	<div class="scx-main-content scx-group">
		
		<!-- Sidebar -->
		<div id="scx-sidebar" class="scx-sidebar scx-span_1_of_4 scx-col">
			<div class="scx-wrap">

				<!-- Desktop notifications -->
				<div class="scx-desk-ntf-alert">
					<div class="scx-title"><?php _e( 'Get notified of new messages', 'schat' ); ?></div>
					<a id="scx-btn-turn-on" href="javascript:;"><?php _e( 'Turn on desktop notifications', 'schat' ); ?></a>
				</div>

				<!-- Sidebar notifications -->
				<div class="scx-ntf scx-ntf-side"></div>
				
				<!-- Tabs -->
				<ul class="scx-tabs">
					<li><a href="#scx-list-users" class="scx-active"><i class="scx-ico-user"></i></a></li>
					<?php if( current_user_can( 'scx_see_logs' ) ): ?>
						<li><a href="#scx-list-chats"><i class="scx-ico-chat"></i></a></li>
					<?php endif; ?>
				</ul>

				<!-- Users list -->
				<div id="scx-list-users" class="scx-tab-content scx-users-wrap">
					<h3 class="scx-title"><?php _e( 'Users', 'schat' ); ?></h3>
					<ul id="scx-ops" class="scx-users scx-list scx-ops"></ul>
					<ul id="scx-visitors" class="scx-users scx-list scx-visitors"></ul>

					<h3 class="scx-title"><?php _e( 'Web visitors', 'schat' ); ?></h3>
					<ul id="scx-web-visitors" class="scx-users scx-list scx-web-visitors"></ul>
				</div>

				<!-- Chats history -->
				<div id="scx-list-chats" class="scx-tab-content scx-chats-wrap">
					<h3 class="scx-title"><?php _e( 'Chat history', 'schat' ); ?></h3>
					
					<div class="scx-chats-search"><input type="search" name="s" placeholder="<?php _e( 'Search in chats', 'schat' ); ?>" id=""></div>

					<ul id="scx-chats" class="scx-chats scx-list"></ul>
				</div>
			</div>
		</div>

		<!-- Main -->
		<div id="scx-main" class="scx-main scx-span_2_of_4 scx-col">
			<div class="scx-wrap">

				<div id="scx-tab" class="scx-tab">
					
					<!-- Current user tab header -->
					<div class="scx-ntf scx-ntf-tab-header"></div>
					<div id="scx-tab-header" class="scx-tab-header">
						<small>Please select a user from left...</small>
					</div>
					
					<!-- Current chat conversation-->
					<div id="scx-tab-cnv" class="scx-tab-cnv">
						<div class="scx-cnv"></div>
					</div>

				</div>
				
			</div>
		</div>
	
		<!-- Right sidebar -->
		<div id="scx-sidebar2" class="scx-sidebar2 scx-span_1_of_4 scx-col"></div>
	</div>
</div>