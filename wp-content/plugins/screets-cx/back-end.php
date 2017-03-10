<?php
/**
 * SCREETS © 2016
 *
 * Initialization the plug-in for back-end
 *
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

if ( ! defined( 'ABSPATH' ) ) exit; // Exit if accessed directly

/**
 * Back-end initialization
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_admin_init() {
	
	global $ChatX;

	// Check for new updates
	$ChatX->update();

	// Get current page
	$current_page = ( !empty( $_GET['page'] ) ) ? $_GET['page'] : null;

	// Load back-end styles and scripts
	add_action( 'admin_enqueue_scripts', 'fn_scx_backend_scripts', 10 );
	add_action( 'admin_enqueue_scripts', 'fn_scx_console_render', 20 );

	// Add extra profile fields to operator users
	if( defined( 'SCX_OP' ) ) {
		add_action( 'show_user_profile', 'fn_scx_op_profile', 10 );
		add_action( 'edit_user_profile', 'fn_scx_op_profile', 10 );
		add_action( 'personal_options_update', 'fn_scx_save_op_profile' );
		add_action( 'edit_user_profile_update', 'fn_scx_save_op_profile' );
	}

	if( $current_page == 'scx_console-options' ) {

		// Add editor stylesheet
		add_editor_style( SCX_URL . '/assets/css/scx.editor-style.css' );

	}

}
add_action( 'admin_init', 'fn_scx_admin_init', 5 );

/**
 * Register translatable strings.
 */
function fn_scx_register_strings() {
	global $ChatX;

	$opts = TitanFramework::getInstance( SCX_SLUG );

	// Register strings to Polylang
	if( function_exists( 'pll_register_string' ) ) {
		foreach( $ChatX->__opts as $id => $opt ) {
			$string = $opts->getOption( $id );
			pll_register_string( $opt['name'], $string, SCX_NAME, $opt['multiline'] );
		}
	}

	// Register strings to WPML 3.2+
	if( has_action( 'wpml_register_single_string' ) ) {
		foreach( $ChatX->__opts as $id => $opt ) {
			$string = $opts->getOption( $id );
			do_action( 'wpml_register_single_string', SCX_NAME, $id, $string );
		}
	}
}
add_action( 'tf_done', 'fn_scx_register_strings', 20 );

/**
 * Render console
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_console_render() {

	global $ChatX;

	// Get current user data
	$user = fn_scx_get_user_data_by_array();

	// Get user agent data
	$agent = fn_scx_get_agent();

	// Get DB url
	$app_db = trim( $ChatX->opts->getOption( 'app-db' ) );
	if( strpos( $app_db, 'https://' ) === false ) {
		$app_db = 'https://' . $app_db;
	}

	// Default console application options
	$opts = apply_filters( 'scx_console_js_opts', array(
		'app_key' => trim( $ChatX->opts->getOption( 'app-key' ) ),
		'app_auth' => trim( $ChatX->opts->getOption( 'app-auth' ) ),
		'app_db' => $app_db,
		'app_bucket' => trim( $ChatX->opts->getOption( 'app-bucket' ) ),
		'token' => $ChatX->get_token(),
		'max_msgs' => 70, // Total number of chat messages to load per chat
		'user' => array(
			'name' => $user['name'],
			'avatar' => $user['avatar'],
			'email' => $user['email'],
			'platform' => array(
				'user_agent'=> $agent['user_agent'],
				'browser'=> $agent['browser'],
				'browser_version'=> $agent['browser_version'],
				'os'=> $agent['os'],
				'ip'=> fn_scx_ip_addr()
			)
		),
		'user_pass' => md5( $user['email'] ),

		'pushover' => array(
			'active' => $ChatX->opts->getOption('pushover-active'),
			'token' => $ChatX->opts->getOption('pushover-api-token'),
			'user_key' => $ChatX->opts->getOption('pushover-user-key')
		),

		'site_name'=> $ChatX->opts->getOption('site-name'),
		'ip'=> fn_scx_ip_addr(),

		'is_home' => false,
		'plugin_url' => SCX_URL,
		'ajax_url' => $ChatX->ajax_url(),
		'console_url' => admin_url( 'admin.php?page=scx_console' ),
		'opts_url' => admin_url( 'admin.php?page=scx_console-options' ),

		'is_ssl' => ( is_ssl() ) ? true : false,

		'_online' => __( 'Online', 'schat' ),
		'_offline' => __( 'Offline', 'schat' ),
		'_saved' => __( 'Saved', 'schat' ),
		'_wait' => __( 'Please wait', 'schat' ),
		'_conn_success' => __( 'Connected', 'schat' ),
		'_active' => __( 'Active', 'schat' ),
		'_you' => __( 'You', 'schat' ),
		'_op' => __( 'Operator', 'schat' ),
		'_web_visitor' => __( 'Web visitor', 'schat' ),
		'_in_chat' => __( 'In chat', 'schat' ),
		'_join_chat' => __( 'Join chat', 'schat' ),
		'_leave_chat' => __( 'Leave chat', 'schat' ),
		'_end_chat' => __( 'End chat', 'schat' ),
		'_in_chat' => __( 'In chat', 'schat' ),
		'_ignore' => __( 'Ignore', 'schat' ),
		'_transfer' => __( 'Transfer', 'schat' ),
		'_del' => __( 'Delete', 'schat' ),
		'_look_at' => __( 'Looking at', 'schat' ),
		'_open' => __( 'Open', 'schat' ),
		'_close' => __( 'Close', 'schat' ),
		'_popup' => __( 'Popup', 'schat' ),
		'_mode' => __( 'Mode', 'schat' ),
		'_feedback' => __( 'Feedback', 'schat' ),
		'_like' => $ChatX->opts->getOption( 'poscx-feedback-like' ),
		'_dislike' => $ChatX->opts->getOption( 'poscx-feedback-dislike' ),
		'_chat_history' => __( 'Chat history', 'schat' ),
		'_no_conn' => __( 'No internet connection', 'schat' ),
		'_remove_usr' => __( 'Remove user', 'schat' ),
		'_ask_del' => __( "Are you sure you want to PERMANENTLY delete it?", 'schat' ),
		'_sure_end' => __( "Visitor won't be able to reply. Do you really want to end chat?", 'schat' ),
		'_reply_ph' => __( 'Your message', 'schat' ),
		'_invalid_conf' => sprintf( __( 'Your Firebase isn\'t configured correctly! <a href="%s">Go settings &raquo;</a>', 'schat' ), admin_url( 'admin.php?page=scx_console-options&tab=integrations' ) ),
		'_user_joined_chat' => __( '%s has joined the chat', 'schat' ),
		'_user_typing' => __( '%s is typing', 'schat' ),
		'_user_left_chat' => __( '%s has left the chat', 'schat' ),
		'_ask_end_chat' => __( 'Are you sure you want to end this chat?', 'schat' ),
		'_ask_leave_chat' => __( 'Are you sure you want to leave chat?', 'schat' ),
		'_ask_page_exit' => __( 'Are you sure you want to exit? You will be OFFLINE', 'schat' ),
		'_ntf_in_chat_already' => __( 'This visitor is talking with <strong>%s</strong>', 'schat' ),
		'_ntf_reset' => __( 'You will re-connect the chat!', 'schat' ),
		'_ntf_start_chat' => __( 'Send a message to initiate chat', 'schat' ),
		'_ntf_wait_reply' => __( '%s is waiting for a reply', 'schat' ),
		'_ntf_new_visitor' => __( '%s is online', 'schat' ),
		'_ntf_dn_active' => sprintf( __( '%s desktop notifications activated :) Good luck with sales', 'schat' ), SCX_NAME ),
		
		// Time strings
		'_time' => array(
			'prefix' => '',
			'suffix' => '',
			'seconds' => '',
			'minute' => __( '1m', 'schat' ),
			'minutes' => __( '%dm', 'schat' ),
			'hour' => __( '1h', 'schat' ),
			'hours' => __( '%dh', 'schat' ),
			'day' => __( '1d', 'schat' ),
			'days' => __( '%dd', 'schat' ),
			'month' => __( '1m', 'schat' ),
			'months' => __( '%dm', 'schat' ),
			'year' => __( '1y', 'schat' ),
			'years' => __( '%dy', 'schat' )
	    )
	));

	// Moment JS
	wp_register_script(
		'moment',
		SCX_URL . '/assets/js/moment.js',
		array( 'jquery' ),
		SCX_VERSION,
		true
	);
	wp_enqueue_script( 'ˀmoment' );

	// Livestamp JS
	wp_register_script(
		'livestamp',
		SCX_URL . '/assets/js/livestamp.js',
		array( 'moment' ),
		SCX_VERSION,
		true
	);
	wp_enqueue_script( 'livestamp' );

	// Application JS
	wp_register_script(
		'scx-app',
		SCX_URL . '/assets/js/scx.app.js',
		array( 'twemoji', 'firebase', 'scx-firebase', 'livestamp' ),
		SCX_VERSION,
		true
	);
	wp_enqueue_script( 'scx-app' );

	// Console user interface JS
	wp_register_script(
		'scx-console-ui',
		SCX_URL . '/assets/js/scx.console.ui.js',
		array( 'scx-polyfill', 'scx-firebase', 'firebase' ),
		SCX_VERSION,
		true
	);
	wp_enqueue_script( 'scx-console-ui' );

	// Localize global data
	wp_localize_script( 'scx-firebase', 'scx_opts', $opts );

}

/**
 * Back-end styles and scripts
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_backend_scripts( $hook ) {

	global $is_IE, $ChatX;

	$taxonomy = !( empty( $_GET['taxonomy'] ) ) ? $_GET['taxonomy'] : null;

	// Admin styles (common)
	wp_register_style(
		'scx-admin',
		SCX_URL . '/assets/css/scx.admin.css'
	);
	wp_enqueue_style( 'scx-admin' );

	// Icons
	wp_register_style(
		'scx-icons',
		SCX_URL . '/assets/css/scx.icons.css',
		array(),
		SCX_VERSION
	);
	wp_enqueue_style( 'scx-icons' );

	switch( $hook ) {

		// 
		// Chat console scripts
		//
		case 'toplevel_page_scx_console':

			// Load common scripts
			$ChatX->common_scripts();

			// Get console dependencies
			$console_dependecies = array( 'twemoji', 'firebase', 'scx-firebase' );

			if( !empty( $gm_api ) ) {
				$console_dependecies[] = 'google-maps';
			}

			// Console styles
			wp_register_style(
				'scx-console',
				SCX_URL . '/assets/css/scx.console.css',
				null,
				SCX_VERSION
			);
			wp_enqueue_style( 'scx-console' );

			break;


		//
		// Options, widgets and customize pages
		//
		case 'chat-x_page_scx_console-options':
		case 'widgets.php';

			// Firebase JS
			wp_register_script(
				'firebase',
				SCX_URL . '/assets/js/firebase.min.js',
				null,
				SCX_FIREBASE_VERSION,
				true
			);
			wp_enqueue_script( 'firebase' );

			// Admin JS
			wp_register_script(
				'scx-admin-opts',
				SCX_URL . '/assets/js/scx.admin.opts.js',
				array('firebase'),
				SCX_VERSION,
				true
			);
			wp_enqueue_script( 'scx-admin-opts' );

			// Icons
			wp_register_style(
				'scx-icons',
				SCX_URL . '/assets/css/scx.icons.css',
				array(),
				SCX_VERSION
			);
			wp_enqueue_style( 'scx-icons' );

			// Admin styles
			wp_register_style(
				'scx-admin-opts',
				SCX_URL . '/assets/css/scx.admin.opts.css',
				null,
				SCX_VERSION
			);
			wp_enqueue_style( 'scx-admin-opts' );

			// Get admin options
			$admin_opts = $opts = array(
				'app-key' => $ChatX->opts->getOption('app-key'),
				'app-auth' => $ChatX->opts->getOption('app-auth'),
				'app-db' => $ChatX->opts->getOption('app-db'),
				'app-bucket' => $ChatX->opts->getOption('app-bucket'),
				'site-name' => $ChatX->opts->getOption('site-name'),
				'site-url' => $ChatX->opts->getOption('site-url'),
				'site-email' => $ChatX->opts->getOption('site-email'),
				'site-reply-to' => $ChatX->opts->getOption('site-reply-to'),
				'guest-prefix' => scx__( 'guest-prefix' )
			);
			
			// Localize global data
			wp_localize_script( 'scx-admin-opts', 'scx_admin_opts', $admin_opts );

			break;

	}

	//
	// The plugin some custom post types and options page styles
	//
	if( 
		$hook == 'user-new.php' ||
		$hook == 'chat-x_page_scx_console-options' ||
		( $hook == 'edit-tags.php' && $taxonomy == 'scx_support_cat' ) 
	) {

		// scx-admin.js custom data
		$admin_js_vars = array(
			'ajax_url'   		=> $ChatX->ajax_url(),
			'plugin_url'   		=> SCX_URL,
			'op_caps' 			=> fn_scx_get_op_cap_names(),
			'user_role' 		=> !empty( $_GET['role'] ) ? $_GET['role'] : ''
		);

		// Admin JS
		wp_register_script(
			'scx-admin',
			SCX_URL . '/assets/js/scx.admin.js',
			array( 'jquery' ),
			SCX_VERSION,
			true
		);
		wp_enqueue_script( 'scx-admin' );

		wp_localize_script( 'scx-admin', 'scx', $admin_js_vars );

	}

}

/**
 * Admin menus
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_admin_menu() {

	// Chat X menu
	add_menu_page(
		'', // Page title
		SCX_NAME_SHORT, // Menu title
		'scx_answer_visitor',
		'scx_console',
		'fn_scx_console_ui',
		SCX_URL . '/assets/img/NB-ico-64px.png',
		'25.19865'
	);

	// Console
	$console_page = add_submenu_page(
		'scx_console',
		__( 'Chat Console', 'schat' ),
		__( 'Chat Console', 'schat' ),
		'scx_answer_visitor',
		'scx_console'
	);
	add_action( 'admin_footer-'. $console_page, 'fn_scx_console_footer' );

	/*add_submenu_page(
		'scx_console',
		__( 'Offline Messages', 'schat' ),
		__( 'Offline Messages', 'schat' ),
		'scx_answer_visitor',
		'edit.php?post_type=scx_offline_msg'
	);*/

	// Support Categories
	/*add_submenu_page(
		'scx_console',
		__( 'Support Categories', 'schat' ),
		__( 'Support Categories', 'schat' ),
		'scx_manage_chat_options',
		'edit-tags.php?taxonomy=scx_support_cat'
	);

	// Support Topics
	add_submenu_page(
		'scx_console',
		__( 'Support Topics', 'schat' ),
		__( 'Support Topics', 'schat' ),
		'manage_options',
		'edit.php?post_type=scx_topic'
	);*/

	// Customize
	/*add_submenu_page(
		'scx_console',
		__( 'Customize', 'schat' ),
		__( 'Customize', 'schat' ),
		'scx_manage_chat_options',
		'customize.php?return=admin.php?page=scx_console-options'
	);*/

	// Support
	add_submenu_page(
		'scx_console',
		__( 'Support', 'schat' ),
		__( 'Support', 'schat' ),
		'scx_answer_visitor',
		'admin.php?page=scx_console-options&tab=support'
	);

	// Remove "All items" link from the menu
	remove_submenu_page( 'scx_console', 'edit.php?post_type=scx_offline_msg' );
	
	
}

add_action( 'admin_menu', 'fn_scx_admin_menu' );

/**
 * Add extra fields to operator user profile page
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_op_profile( $user ) {?>

		<a id="SCX-op-options"></a>
		<hr style="margin-bottom:30px;">

		<img src="<?php echo SCX_URL; ?>/assets/img/cx-badge.png" style="float:left; max-width:80px; margin-right:15px;">
		<h3 style="margin-top:5px; margin-bottom:10px;"><?php _e( 'Operator Options', 'schat' ); ?></h3>
		<span class="description" style="color: #e54045;"><?php _e( 'Click <strong>Restart</strong> button in Chat Console > Settings after updating operator information', 'schat' ); ?></span>

		<table class="form-table">

			<!-- Operator Name -->
			<tr>
				<th><?php _e( 'Operator Name', 'schat' ); ?></th>
				<td>
					<input type="text" name="cx_op_name" id="f_chat_op_name" value="<?php echo esc_attr( get_the_author_meta( 'cx_op_name', $user->ID ) ); ?>" class="regular-text" />
					<br>
				</td>
			</tr>

		</table>

		<hr>


	<?php }


/**
 * Save extra fields of operator users
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_save_op_profile( $user_id ) {

	if ( !current_user_can( 'edit_user', $user_id ) )
		return false;

	// Op name isn't defined yet, create new one for user
	if( empty( $_POST['cx_op_name'] ) ) {

		$current_user = wp_get_current_user();

		$op_name = $current_user->display_name;


	// OP name
	} else
		$op_name = $_POST['cx_op_name'];


	// Update user meta now
	update_user_meta( $user_id, 'cx_op_name', $op_name );
}


/**
 * Console page JS
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_console_footer() { ?>

	<script type="text/javascript">

		document.addEventListener( 'DOMContentLoaded', function() {

			var chatx = new SCX_UI( scx_opts );

		}, false );

	</script>

	<?php
}


/**
 * Chat console user interface
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_console_ui() {
	require SCX_PATH . '/core/templates/console.php';
}


/**
 * Add extra fields to support categories (insert mode)
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_xtra_support_cat_fields() { ?>

	<input type="hidden" name="cx_xtra_support_cat[featured_img]" id="CX_field_featured_img" value="">

	<div class="form-field cx-cat-img-wrap">
		<label for="cx_xtra_support_cat[featured_img]"><?php _e( 'Category image', 'schat' ); ?></label>
		<p><a href="javascript:;" class="button cx-media-uploader" data-id="featured_img"><?php _e( 'Set featured image', 'schat' ); ?></a></p>
		<div id="CX_media_featured_img" class="cx-media-wrap hidden">
			<img src="" alt="" title="" />
		</div>
		<p><a href="javascript:;" class="cx-media-remove hidden" id="CX_remove_featured_img" data-id="featured_img"><?php _e( 'Remove image', 'schat' ); ?></a></p>
	</div>
<?php }

add_action( 'scx_support_cat_add_form_fields', 'fn_scx_xtra_support_cat_fields', 10, 2 );

/**
 * Add extra fields to support categories (edit mode)
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_edit_xtra_support_cat_fields( $term ) {

	// Put the term ID into a variable
	$term_id = $term->term_id;

	// Retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$term_id" );


	// Get featured image
	$featured_img_id = ( !empty( $term_meta['featured_img'] ) ) ? esc_attr( $term_meta['featured_img'] ) : ''; 
	$featured_img_url = wp_get_attachment_url( $featured_img_id );

	// Hide featured image
	$hide_featured_img = ( empty( $featured_img_url ) ) ? 'hidden' : '';

	?>
	
	<input type="hidden" name="cx_xtra_support_cat[featured_img]" id="CX_field_featured_img" value="<?php echo $featured_img_id; ?>">

	<tr class="form-field cx-cat-img-wrap">
		<th scope="row" valign="top">
			<label for="cx_xtra_support_cat[featured_img]"><?php _e( 'Category image', 'schat' ); ?></label>
		</th>
		<td>
			<p><a href="javascript:;" class="button cx-media-uploader" data-id="featured_img"><?php _e( 'Set featured image', 'schat' ); ?></a></p>
			<div id="CX_media_featured_img" class="cx-media-wrap <?php echo $hide_featured_img; ?>">
				<img src="<?php echo @$featured_img_url; ?>" alt="" title="" />
			</div>
			<p><a href="javascript:;" class="cx-media-remove <?php echo $hide_featured_img; ?>" id="CX_remove_featured_img" data-id="featured_img"><?php _e( 'Remove image', 'schat' ); ?></a></p>
		</td>
	</tr>

<?php }

add_action( 'scx_support_cat_edit_form_fields', 'fn_scx_edit_xtra_support_cat_fields', 10, 2 );

/**
 * Save extra fields of suport topics
 *
 * @since Chat X (2.0)
 * @return void
 */
function fn_scx_save_xtra_support_cats( $term_id ) {

	if ( isset( $_POST['cx_xtra_support_cat'] ) ) {
		$tid = $term_id;

		$term_meta = get_option( "taxonomy_$tid" );
		$cat_keys = array_keys( $_POST['cx_xtra_support_cat'] );

		foreach ( $cat_keys as $key ) {
			if ( isset ( $_POST['cx_xtra_support_cat'][$key] ) ) {
				$term_meta[$key] = $_POST['cx_xtra_support_cat'][$key];
			}
		}
		// Save the option array.
		update_option( "taxonomy_$tid", $term_meta );

	}
}

add_action( 'edited_scx_support_cat', 'fn_scx_save_xtra_support_cats', 10, 2 );
add_action( 'create_scx_support_cat', 'fn_scx_save_xtra_support_cats', 10, 2 );

/**
 * Add extra column to support catgories
 *
 * @since Chat X (2.0)
 * @return array $columns
 */
function fn_scx_support_add_xtra_columns( $columns ) {
	
	unset( $columns['description'] );
	unset( $columns['slug'] );
	unset( $columns['posts'] );

	$columns['cx_featured_img'] = '';

	return $columns;

}

/**
 * Edit extra column of support catgories
 *
 * @since Chat X (2.0)
 * @return array $columns
 */
function fn_scx_support_edit_xtra_columns( $out, $column_name, $term_id ) {
	
	// Retrieve the existing value(s) for this meta field. This returns an array
	$term_meta = get_option( "taxonomy_$term_id" );

	switch ( $column_name ) {

		case 'cx_featured_img' :
			
			// Get featured image
			$featured_img_id = ( !empty( $term_meta['featured_img'] ) ) ? esc_attr( $term_meta['featured_img'] ) : ''; 
			$featured_img_url = wp_get_attachment_url( $featured_img_id );
			
			if( !empty( $featured_img_url ) ) {
				return '<img src="' . $featured_img_url . '" alt="" class="cx-featured-img">';
			} else {
				return '<img src="' . SCX_URL . '/assets/img/cx-no-img.png" alt="" class="cx-featured-img">';
			}

			break;

	}

}

add_filter( 'manage_edit-scx_support_cat_columns', 'fn_scx_support_add_xtra_columns', 10, 1 );
add_filter( 'manage_scx_support_cat_custom_column', 'fn_scx_support_edit_xtra_columns', 10, 3 );


function fn_scx_replace_admin_menu_icons_css() { 
	?>
	<style>
		#toplevel_page_scx_console .wp-menu-image img { display: none; }
		#toplevel_page_scx_console .wp-menu-image::before {
			font-family: scx;
			content: "\6e";
		}
		#toplevel_page_scx_console:hover .wp-menu-image::before,
		#toplevel_page_scx_console.wp-menu-open .wp-menu-image::before {
			content: "\4c";
		}
	</style>

    <?php
}

add_action( 'admin_head', 'fn_scx_replace_admin_menu_icons_css' );