<?php
/**
 * SCREETS © 2016
 *
 * Full chat box template
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

if ( ! defined( 'ABSPATH' ) ) exit; ?>

<!-- Widget -->
<div id="scx-widget" class="scx-w scx-w-fixed">

	<!-- 
	 ## Chat button
	-->
	<div id="scx-btn" class="scx-chat-btn">
		<i class="scx-ico-l scx-ico-chat"></i>
		<span class="scx-title"></span> <i class="scx-ico-r <?php echo fn_scx_get_chat_icon(); ?>"></i>
		<div class="scx-count">0</div>
	</div>

	<!-- 
	 ## Initial popup 
	-->
	<div id="scx-popup-init" class="scx-popup scx-popup-init" data-mode="init">
		
		<!-- Popup header -->
		<div class="scx-header">
			<span class="scx-title"><?php _e( 'Connecting', 'schat' ) ;?></span><i class="scx-ico-r <?php echo fn_scx_get_chat_icon( 'open' ); ?>"></i>
		</div>

		<!-- Popup content -->
		<div class="scx-content">
			<div class="scx-ntf scx-ntf-init"></div>

			<div class="scx-ntf scx-active"><div class="scx-wait"><?php _e( 'Please wait', 'schat' ); ?>...</div></div>
		</div>
	
	</div>

	<!-- 
	 ## Offline popup 
	-->
	<div id="scx-popup-offline" class="scx-popup scx-popup-offline" data-mode="offline">
		
		<!-- Popup header -->
		<div class="scx-header">
			<span class="scx-title"><?php echo scx__( 'offline-title' ) ;?></span><i class="scx-ico-r <?php echo fn_scx_get_chat_icon( 'open' ); ?>"></i>
		</div>

		<!-- Popup content -->
		<div class="scx-content">
			
			<div class="scx-ntf scx-ntf-offline-top"></div>

			<div class="scx-lead"><?php echo scx__( 'offline-greeting' ) ;?></div>

			<form action="<?php echo SCX_URL; ?>" class="scx-form scx-form-offline" data-name="offline">
				<div class="scx-inner">

					<?php echo fn_scx_build_form( 'offline' ); ?>
				
				</div>

				<div class="scx-ntf scx-ntf-offline"></div>

				<div class="scx-send">
					<a href="javascript:;" class="scx-button scx-send-btn scx-send-offline scx-primary" rel="nofollow"><i class="scx-ico-send"></i> <?php echo scx__( 'offline-btn' ) ;?></a>
				</div>
			</form>

			<div class="scx-footer"><?php echo scx__( 'offline-footer' ) ;?></div>

			<?php echo fn_scx_get_social_links( 'offline' ); ?>

			<?php echo fn_scx_screets_logo(); ?>
		</div>
	</div>

	<!-- 
	 ## Pre-chat popup 
	-->
	<div id="scx-popup-prechat" class="scx-popup scx-popup-prechat" data-mode="prechat">
		
		<!-- Popup header -->
		<div class="scx-header">
			<span class="scx-title"><?php echo scx__( 'prechat-title' ) ;?></span><i class="scx-ico-r <?php echo fn_scx_get_chat_icon( 'open' ); ?>"></i>
		</div>

		<!-- Popup content -->
		<div class="scx-content">
			
			<div class="scx-lead"><?php echo scx__( 'prechat-greeting' ) ;?></div>
			
			<?php 
			if( current_user_can('scx_answer_visitor' ) && !is_admin() ):
				echo __( 'Operators cannot test the plugin from here. Please use other browser or computer to test the plugin', 'schat' ); 
			else:
			?>
			<form action="<?php echo SCX_URL; ?>" class="scx-form scx-form-prechat" data-name="prechat">
				<div class="scx-inner">
					
					<?php echo fn_scx_build_form( 'prechat' ); ?>
				
				</div>

				<div class="scx-ntf scx-ntf-prechat"></div>
				
				<div class="scx-send">
					<a href="javascript:;" class="scx-button scx-send-btn scx-send-prechat scx-primary" rel="nofollow"><?php echo scx__( 'prechat-btn' ) ;?></a>
				</div>
			</form>
			<?php endif; ?>

			<?php echo fn_scx_screets_logo(); ?>
		</div>
	</div>

	<!-- 
	 ## Online popup 
	-->
	<div id="scx-popup-online" class="scx-popup scx-popup-online" data-mode="online">

		<div class="scx-header">
			<span class="scx-title"><?php echo scx__( 'online-title' ) ;?></span><i class="scx-ico-r <?php echo fn_scx_get_chat_icon( 'open' ); ?>"></i>
		</div>

		
		<div class="scx-content">

			<?php 
			if( current_user_can('scx_answer_visitor' ) && !is_admin() ): ?>
				
				<div class="scx-ntf scx-info scx-active">
					<?php echo __( 'Operators cannot test the plugin from here. Please use other browser or computer to test the plugin', 'schat' );  ?>
				</div>
			
			<?php else: ?>
				
				<!-- Notifications -->
				<div class="scx-ntf scx-ntf-online"></div>
				
				<!-- Conversation -->
				<div class="scx-cnv"></div>

				<!-- Links -->
				<ul class="scx-links">
					<li class="scx-typing"></li>
					<li><a href="#" class="scx-btn-end-chat"><i class="scx-ico-logout"></i><?php _e( 'End chat', 'schat' ); ?></a></li>
				</ul>
				
				<!-- Reply box -->
				<div class="scx-reply-box scx-row">
					<span class="scx-col">
						<textarea name="msg" class="scx-reply" placeholder="<?php echo scx__( 'str-reply-ph'  ) ;?>" disabled="disabled"></textarea>
					</span>
					<span class="scx-col">
						<a href="javascript:void(0);" class="scx-reply-send scx-button scx-primary scx-small"><?php echo scx__(  'str-reply-send' ); ?></a>
					</span>
				</div>
			
			<?php endif; ?>
			
		</div>

	</div>

	<!-- 
	 ## Postchat popup 
	-->
	<div id="scx-popup-postchat" class="scx-popup scx-popup-postchat" data-mode="postchat">
		
		<div class="scx-header">
			<span class="scx-title"><?php echo scx__( 'postchat-title' ) ;?></span><i class="scx-ico-r <?php echo fn_scx_get_chat_icon( 'open' ); ?>"></i>
		</div>
		
		<!-- Popup content -->
		<div class="scx-content">
			<div class="scx-lead"><?php echo scx__( 'postchat-greeting' ) ;?></div>
			
			<!-- Notifications -->
			<div class="scx-ntf scx-ntf-postchat"></div>

			<!-- Rate our support -->
			<div class="scx-subtitle"><?php echo scx__( 'poschat-feedback-title' ); ?></div>
			<ul class="scx-vote">
				<li><a href="#" class="scx-button scx-small scx-btn-vote" data-vote="like"><i class="scx-ico-like"></i><span><?php echo scx__( 'poschat-feedback-like' ); ?></span></a></li>
				<li><a href="#" class="scx-button scx-small scx-btn-vote" data-vote="dislike"><i class="scx-ico-dislike"></i><span><?php echo scx__( 'poschat-feedback-dislike' ); ?></span></a></li>
			</ul>

			<!-- <div class="scx-feedback-box"><textarea name="feedback" id="scx-feedback" class="scx-feedback" placeholder="<?php _e( 'Your feedback', 'schat' ); ?>"></textarea></div> -->

			<ul class="scx-links">
				<li>
					<a href="#" class="btn-email-chat"><span class="scx-ico-mail"></span> <?php echo scx__( 'postchat-btn-email' ); ?></a>
					
					<!-- Email field -->
					<div class="scx-row scx-form-email">
						<div class="scx-col"><input type="email" name="email" class="scx-f-email" placeholder="<?php echo scx__( 'postchat-f-email' ); ?>"></div>
						<div class="scx-col"><a href="#" class="scx-button scx-send"><?php echo scx__( 'postchat-btn-send' ); ?></a></div>
					</div>
				</li>
				<li>
					<a href="#" class="btn-done"><span class="scx-ico-ok"></span> <?php echo scx__( 'postchat-btn-done' ); ?></a>
				</li>
			</ul>

			<!-- <div class="scx-button-wrap">
				<a href="#" class="btn-done scx-button scx-primary"><?php echo scx__( 'postchat-btn-done' ); ?></a>
			</div> -->

			<?php echo fn_scx_get_social_links( 'postchat' ); ?>

			<?php echo fn_scx_screets_logo(); ?>
			
		</div>

	</div>

</div>