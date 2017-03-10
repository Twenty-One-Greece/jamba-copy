<?php
/**
 * SCREETS © 2016
 *
 * Offline functions
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

/**
 * Send email with current template 
 *
 * @since Chat X (2.0)
 * @return mixed Returns true if the email contents were sent successfully
 */
function fn_scx_send_email( $to, $subject, $body, $headers = array() ) {

	global $ChatX;

	require_once SCX_PATH . '/core/class.template.php';

	// Create email template
	$email = new SCX_template( apply_filters( 'scx_email_template_file', SCX_PATH . '/core/templates/tpl/email-basic.tpl' ) );

	$site_logo = $img = wp_get_attachment_image_src( $ChatX->opts->getOption( 'site-logo' ), 'full' );

	// Setup email header and footer contents
	$email->set( 'title', $subject );
	$email->set( 'logo', $site_logo[0] );
	$email->set( 'site_url', $ChatX->opts->getOption( 'site-url' ) );
	$email->set( 'site_name', $ChatX->opts->getOption( 'site-name' ) );
	$email->set( 'footer_note', '' );
	$email->set( 'radius', $ChatX->opts->getOption('radius') );
	$email->set( 'body', $body );

	// Email content
	$msg = $email->render();

	// Email subject
	$subject = $ChatX->opts->getOption( 'site-name' ) . ' - ' . $subject;

	// Try to send email
	if( !wp_mail( $to, $subject, $msg, $headers ) ) {
		throw new Exception( __( 'Something went wrong! Please try again', 'schat' ) );
	}

	return true;

}

/**
 * Add offline message
 *
 * @since Chat X (2.0)
 * @param string $msg
 * @param array $meta  Message meta data
 * @return int $msg_id
 */
function fn_scx_create_offline_msg( $msg, $meta ) {

	// Get title
	if( !empty( $meta['name'] ) )
		$title = $meta['name'];

	elseif( !empty( $meta['email'] ) )
		$title = $meta['email'];
	
	elseif( !empty( $meta['phone'] ) )
		$title = $meta['phone'];

	else
		$title = $meta['ip_addr'];

	// Prepare post data
	$data = array(
		'post_type' 	=> 'scx_offline_msg',
		'post_title'	=> $title,
		'post_content' 	=> $msg,
		'post_status'	=> 'publish'
	);

	// Add offline message
	$msg_id = wp_insert_post( $data );

	// Add/update custom fields
	foreach( $meta as $k => $v ) {
		if( !empty( $v ) )
			add_post_meta( $msg_id, $k, $v, true ) || update_post_meta( $msg_id, $k, $v );
	}

	return $msg_id;

}