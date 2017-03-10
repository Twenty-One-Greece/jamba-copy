/*!
 * Screets Chat X - Front-end User Interface
 * Author: @screetscom
 *
 * COPYRIGHT © 2016 Screets d.o.o. All rights reserved.
 * This  is  commercial  software,  only  users  who have purchased a valid
 * license  and  accept  to the terms of the  License Agreement can install
 * and use this program.
 */

;(function () {

	var W = window,
		D = document,
		title = D.title,
		root = this,
		prev_scx_ui = root.SCX_UI,
		// jQuery style selector for more complex queries (slower than getElementById)
		$ = function ( selector ) {
			return D.querySelector( selector );
		};


	function SCX_UI( opts ) {

		var self = this;

		// Default options
		var default_opts = {
			app_id 				: '', // Real-time application 
			max_msgs			: 70, // Total number of chat messages to load per chat
			default_mode		: '', // Initial mode
			hide_when_offline	: false, // Hide chat box when all operators offline
			show_btn 			: true, // Show chat button
			show_prechat 		: true, // Show login form to visitor before chat
			show_postchat 		: true, // Show feedback form to visitor after ending chat
			user 				: {}, // Default user data
			is_frontend			: true, // Is front-end
			minimized_before	: false // User minimized popup already on current page refresh
		};

		// Setup and establish options
		this.opts = default_opts;
		for( var k in opts ) { this.opts[k] = opts[k]; }

		// Get Chat X application
		this.app = new ChatX( this.opts );

		// Useful variables
		this.mode = 'init';
		this.btn_mode = null; // "open", null
		this.popup_mode = null; // "open", null
		this.chat_id = null;
		this.tmp_chat_id = null; // Temporarily saved ID for post-chat
		this.chat = {};
		this.chat_msgs = {};
		this.last_msg = '';
		this.ops = null;
		this.modes_list = '_scx-online _scx-offline _scx-prechat _scx-postchat';

		// Common UI objects
		this.$widget = D.getElementsByClassName( 'scx-w' );
		this.$btn = D.getElementsByClassName( 'scx-chat-btn' );
		this.$popup = D.getElementsByClassName( 'scx-popup-' + this.mode );
		this.$forms = D.getElementsByClassName( 'scx-form' );
		this.$attention = D.getElementById( 'scx-attention' );
		this.$cnv = D.getElementsByClassName( 'scx-cnv' );
		this.$reply = D.getElementsByClassName( 'scx-reply' );
		this.$reply_send = D.getElementsByClassName( 'scx-reply-send' );
		this.$btn_end = D.getElementsByClassName( 'scx-btn-end-chat' );
		this.$typing = D.getElementsByClassName( 'scx-typing' );
		
		// Shortcodes
		this.$sc_btn = D.getElementsByClassName( 'scx-shortcode-chat-btn' );

		// Setup common UI elements
		this._ui();
		
		// Setup binding data events
		this._bind_data_events();

		// Listens for changes to 
		// the user's authentication state
		firebase.auth().onAuthStateChanged( function( auth ) {

			if( auth ) {

				if( auth) {
					var user_id = auth.uid,
						username = self.opts.user.name;

					self.app.set_user( user_id, username );

				}
				
				// FIXME
				// Update user info
				// $('.scx-user-name').innerHTML = username;
				// addClass.apply( $('.scx-user-info'), ['scx-active'] );
			
			// Not logged in yet
			} else {

				self.app.db.login( 'anonymously', function( err, auth ) {

					// Something wrong with authentication
					if( err ) {
						
						// Continue with offline mode...
						self.update_mode( 'offline' );

						// Display error
						console.error( err ); 
					
					}

					/*if( auth) {
						var user_id = auth.uid,
							username = self.opts.user.name;

						self.app.set_user( user_id, username );

					}*/

				});

				// FIXME
				// Hide user info
				// removeClass.apply( $('.scx-user-info'), ['scx-active'] );

			}
		});

		// Show chat widget immediately after page load
		if( !this.opts.hide_when_offline && this.opts.show_btn && this.popup_mode !== 'open' ) {
			this.btn( 'init' )
		}

	}

	//
	// Internal methods
	// ------------------

	SCX_UI.prototype = {
		
		/**
		 * Setup binding data events
		 */
		_bind_data_events : function() {

			this.app.on( 'on-connect', this._on_connect.bind( this ) );
			this.app.on( 'op-update', this._on_op_update.bind( this ) );
			
			// Chat events
			this.app.on( 'chat-join', this._on_join_chat.bind( this ) );
			this.app.on( 'chat-update', this._on_update_chat.bind( this ) );
			this.app.on( 'chat-exit', this._on_leave_chat.bind( this ) );
			this.app.on( 'msg-new', this._on_new_msg.bind( this ) );
			this.app.on( 'msg-remove', this._on_removed_msg.bind( this ) );

			// Chat action
			this.app.on( 'chat-action', this._on_action.bind( this ) );
			this.app.on( 'chat-action-response', this._on_action_response.bind( this ) );


		},

		/**
		 * Setup common UI events
		 */
		_ui : function() {

			var self = this,
				typing = false;

			var fn_open_popup = function(e) {
				e.preventDefault();

				if( self.popup_mode === 'open' ) {
					fn_close_popup(e);
					return;
				}

				self.btn( 'hide' );
				self.popup( 'show' );

				var $reply = this.parentNode.querySelector('.scx-reply');

				if( self.mode === 'online' && $reply ) {
					root.setTimeout( function() { 
						$reply.focus(); // Wait for the end of the animation
					}, 500);
				}
			}

			var fn_close_popup = function(e) {
				e.preventDefault();
				self.popup( 'hide' );
				self.btn( 'show' );
			}

			var fn_done = function(e) {
				e.preventDefault();
				self.popup( 'hide' );
				self.btn( 'show' );

				// We done with chat, go prechat
				if( self.mode === 'postchat' ) {
					self.update_mode('online');
				}
			}

			/* Delay for a specified time */
			var fn_delay = ( function() {
				var timer = 0;
				
				return function( callback, ms ) {
					root.clearTimeout (timer);
					timer = root.setTimeout( callback, ms );
				};

			} )();

			// Listen chat button events
			if( self.$btn ) {

				var $btn = null;

				for( var i=0; i < self.$btn.length; i++ ) {
					$btn = self.$btn[i];

					$btn.addEventListener( 'click', fn_open_popup );
				}

			}
			// Listen action buttons
			var $action_btns = D.querySelectorAll('.scx-action-menu');
			if( $action_btns ) {
				for( var i=0; i<$action_btns.length; i++ ) {
					$action_btns[i].addEventListener( 'click', fn_open_popup );
				}
			}

			// Listen popup events
			var $popup_headers = D.querySelectorAll('.scx-popup .scx-header');
			if( $popup_headers ) {
				var $header = null;

				for( var i=0; i < $popup_headers.length; i++ ) {
					$header = $popup_headers[i];
					$header.addEventListener( 'click', fn_close_popup );
				}
			}

			// Listen "close popup" buttons
			var $btn_close_popup = D.getElementsByClassName( 'btn-close-popup' );
			if( $btn_close_popup ) {
				for( var i=0; i < $btn_close_popup.length; i++ ) {
					$btn_close_popup[i].addEventListener( 'click', fn_close_popup );
				}
			}

			// Listen "done" buttons
			var $btn_done = D.getElementsByClassName( 'btn-done' );
			if( $btn_done ) {
				for( var i=0; i < $btn_done.length; i++ ) {
					$btn_done[i].addEventListener( 'click', fn_done );
				}
			}

			// Setup all forms
			if( self.$forms ) {

				self._setup_forms();

				// Listen "End chat" buttons
				if( self.$btn_end ) {
					for( var i=0; i < self.$btn_end.length; i++ ) {
						self.$btn_end[i].addEventListener('click', function(e) {
							e.preventDefault();

							var ask = confirm( self.opts._ask_end_chat );

							if( ask ) {
								self.end_chat();
							}
						});
					}
				}
			}

			var fn_send = function() {

				var $reply = D.querySelector('.scx-reply'),
					msg = $reply.value.trim();

				// We aren't typing anymore
				typing = false;
				self.app.db.typing( null, self.chat_id );

				if( msg.length > 0 ) {

					// Clean the value
					$reply.value = '';

					// Focus reply box
					$reply.focus();

					// Push message
					if( self.chat_id ) {
						self.app.db.push_msg( self.chat_id, msg, null );

					// No private chat, so create new one.
					} else {
						
						var chat_data = { 
							name: self.app.db.username, 
							visitor_id: self.app.user_id 
						};

						self.app.db.create_chat( chat_data, 'private', function( new_chat_id ) {
							
							// Update current conversation id
							self.$cnv.id = [ 'scx-cnv-', new_chat_id ].join('');

							// Update private chat
							self.chat_id = new_chat_id;

							// Push message
							self.app.db.push_msg( new_chat_id, msg, null );
							
						});
					}
				}

			}

			var fn_reply = function(e) {

				if( e && e.keyCode === 13 && !e.shiftKey ) {
						
					fn_send();

					e.preventDefault();

				// Typing...
				} else {

					if( !typing ) {

						// Don't listen some keys
						switch( e.keyCode ) {
							case 17: // ctrl
							case 18: // alt
							case 16: // shift
							case 20: // capslock
							case 9: // tab
							case 8: // backspace
							case 224: // cmd (firefox)
							case 17:  // cmd (opera)
							case 91:  // cmd (safari/chrome) Left Apple
							case 93:  // cmd (safari/chrome) Right Apple
								return true;
						}

						// We're typing...
						self.app.db.typing( true, self.chat_id );

						// We're currently busy with typing
						typing = true;

					}

					// Remove user from typing list after the user has stopped typing 
					// for a specified amount of time
					fn_delay( function() {

						self.app.db.typing( null, self.chat_id );

						typing = false;
						
					}, 1000 );

					return true;
				}

			};

			// Listen reply boxes
			if( self.$reply ) {
				for( var i=0; i < self.$reply.length; i++ ) {
					self.$reply[i].addEventListener( 'keydown', fn_reply );
				}
			}

			// Listen send button clicks
			if( self.$reply_send ) {
				for( var i=0; i < self.$reply_send.length; i++ ) {
					self.$reply_send[i].addEventListener( 'click', fn_send );
				}
			}

			// Mark as read current messages
			var $online_popups = D.querySelectorAll('.scx-popup-online');
			if( $online_popups ) {
				for( var i=0; i < $online_popups.length; i++ ) {
					$online_popups[i].addEventListener('click', function(e) {
						self._read();
					});
				}
			}

			/* Email chat history */
			var fn_email_chat = function(e) {

				e.preventDefault();

				var $form = this.parentNode.querySelector('.scx-form-email'),
					$f_email = $('.scx-popup-postchat .scx-f-email'),
					$f_send = $('.scx-popup-postchat .scx-send'),
					working = false;

				// Show form
				self.app.add_class( $form, 'scx-active' );

				if( !self.chat_msgs ) {

					// No messages found notification
					self.ntf( 'error', self.opts._no_msg, 'error', self.mode );
					$f_email.disabled = true;

					return;
				}

				if( self.tmp_user.email ) {
					$f_email.value = self.tmp_user.email;
				}

				setTimeout( function() { $f_email.focus(); }, 400 );

				// Listen "Send" button
				$f_send.addEventListener( 'click', function( btn_e ) {
					btn_e.preventDefault();

					var email = $f_email.value.trim();

					// Validate email field
					if( !email || !self.app.is_email( email ) ) {

						self.app.add_class( $f_email, 'scx-error' );
						self.app.ntf( 'error', self.opts._invalid_email, 'error', self.mode );

						return;
					}

					if( working ) return;

					working = true;

					// Hide notification
					self.app.remove_class( $f_email, 'scx-error' );
					self.app.hide_ntf( 'error' );

					// Please wait notification
					self.app.ntf( 'wait', self.opts._wait, 'wait', self.mode );
					$f_email.disabled = true;
					self.app.add_class( $f_send, 'scx-disabled' );
					
					var msg = {},
						body = [];

					for( var id in self.chat_msgs ) {
						msg = self.chat_msgs[id];

						if( msg.user_id === self.app.db.user_id ) {
							msg.name = self.opts._you;
						}

						if( msg.type === 'default' ) {
							body.push( '<p><strong>' + msg.name + ':</strong> <span>' + msg.msg + '</span></p>' );
						}
					}

					var fd = {
						chat_id: self.tmp_chat_id, 
						name: self.tmp_user.name, 
						email: email, 
						content: body.join('')
					};


					self.app.post( self.opts.ajax_url + '?action=chatx_ajax_cb&mode=email_chat', fd, function( r ) {

						working = false;

						// Successfully sent!
						if( !r.error ) {
							self.app.hide_ntf( 'wait' );
							self.app.ntf( 'success', r.msg, 'success', self.mode, null, 3000 );

							// Re-activarte form
							$f_email.disabled = false;
							self.app.remove_class( $f_send, 'scx-disabled' );

							// Hide form
							self.app.remove_class( $form, 'scx-active' );

						} else {
							self.app.ntf( 'error', r.error, 'error', self.mode );
						}

					});
						
				});
			};

			// Listen Email chat history buttons
			var $btn_email_chat = D.getElementsByClassName( 'btn-email-chat' );
			if( $btn_email_chat ) {
				for( var i=0; i < $btn_email_chat.length; i++ ) {
					$btn_email_chat[i].addEventListener( 'click', fn_email_chat );
				}
			}

			var fn_vote = function(e) {
				e.preventDefault();

				var self_btn = this,
					vote = this.getAttribute('data-vote');

				// Update vote data
				self.app.db.set_chat_param( self.tmp_chat_id, 'vote', vote, function() {

					// "Saved. Thank you!" message
					self_btn.parentNode.parentNode.innerHTML = '<span class="scx-success">' + self.opts._vote_saved + '</span>';


				});

			};

			// Listen vote buttons
			var $btn_vote = D.getElementsByClassName( 'scx-btn-vote' );
			if( $btn_vote ) {
				for( var i=0; i < $btn_vote.length; i++ ) {
					$btn_vote[i].addEventListener( 'click', fn_vote );
				}
			}

			// Find chat button shortcodes
			if( self.$sc_btn ) {
				for( var i=0; i < self.$sc_btn.length; i++ ) {
					self.$sc_btn[i].addEventListener( 'click', fn_open_popup );
				}
			}

			var fn_refresh_ui = function(e) {

  				var $popup = D.getElementById( 'scx-popup-' + self.mode ),
  					$popup_header = $popup.querySelector('.scx-header'),
  					$popup_content = '',
  					e = D.documentElement,
					g = D.getElementsByTagName('body')[0],
					x = W.innerWidth || e.clientWidth || g.clientWidth,
					y = W.innerHeight|| e.clientHeight|| g.clientHeight,
					offset_x = self.opts.offset_x * 2,
					h = 0;

				// Calculate popup height
				h = y - offset_x - $popup_header.offsetHeight;

				// Find popup content
				switch( self.mode ) {
					case 'online':
						
						reply_h = $popup.querySelector('.scx-reply-box').offsetHeight;
						links_h = $popup.querySelector('.scx-links').offsetHeight;
						h = h - ( reply_h + links_h );

						$popup_content = $popup.querySelector('.scx-cnv');

						// Set margin bottom of conversation
						$popup_content.style.marginBottom = links_h+reply_h+'px';

						break;

					default:
						$popup_content = $popup.querySelector('.scx-content');

				}

				// Setup width for mobile
				if( $popup ) {
					if( x <= 420 ) {
						$popup.style.width = ( x - offset_x ) + 'px';

						if( self.opts.pos_x === 'left' ) {
							$popup.style.left = (offset_x/2) + 'px';
						} else {
							$popup.style.right = (offset_x/2) + 'px';
						}

					// Revert to default view
					} else {
						$popup.style.width = self.opts.popup_size + 'px';

						if( self.opts.pos_x === 'left' ) {
							$popup.style.left = self.opts.offset_y + 'px';
						} else {
							$popup.style.right = self.opts.offset_y + 'px';
						}
					}
				}

				if( $popup_content ) {

					// Setup height
					$popup_content.style.maxHeight = h + 'px';
				}


			}

			// Setup chat console layout
			W.addEventListener( 'resize', fn_refresh_ui );

		},

		/**
		 * Listen online operators updates
		 */
		_on_op_update : function( ops ) {

			var self = this,
				btn_title = '',
				can_reply = false;

			// Update current operators data
			this.ops = ops;

			// We're online!
			if( this.ops ) {

				btn_title = self.opts._btn_online;

				if( !self.popup_mode || ( self.popup_mode === 'open' && self.mode !== 'offline' &&  self.mode !== 'postchat' ) ) {
					self.update_mode( 'online' );
				
				// Notify user
				} else if( self.mode === 'offline' ) {
					var ntf = self.opts._we_online + ' <a id="_scx-btn-go-chat" href="javascript:;">' + self.opts._prechat_btn + ' &raquo;</a>';
					
					self.app.ntf( 'op-online', ntf, 'success', 'offline-top' );

					// "Login now" link in notification
					D.getElementById('_scx-btn-go-chat').addEventListener('click', function(e) {
						self.update_mode( 'online' );
					});

				} 

				if( self.mode === 'online' ) {

					// Re-activate reply boxes
					if( self.$reply ) {
						for( var i=0; i < self.$reply.length; i++ ) {
							self.$reply[i].disabled = false;
						}

						// Re-activate send buttons
						for( var i=0; i < self.$reply_send.length; i++ ) {
							self.app.remove_class( self.$reply_send[i], 'scx-disabled' );
						}
					}
				}

				self.app.hide_ntf( 'we-offline' );

				// Activate reply box
				can_reply = true;


			// No operators offline...
			} else {
				
				btn_title = self.opts._btn_offline;

				if( !self.popup_mode || ( self.popup_mode === 'open' && self.mode !== 'online' && self.mode !== 'postchat' ) ) {
					self.update_mode( 'offline' );

				}

				if( self.mode === 'online' ) {

					// Disable reply boxes
					if( self.$reply ) {
						for( var i=0; i < self.$reply.length; i++ ) {
							self.$reply[i].disabled = true;
						}

						// Disable send buttons
						for( var i=0; i < self.$reply_send.length; i++ ) {
							self.app.add_class( self.$reply_send[i], 'scx-disabled' );
						}
					}

				}

				// Show "We're offline now" notification
				self.app.ntf( 'we-offline', self.opts._we_offline, 'error', 'online' );

				// Hide "We're online" notification
				self.app.hide_ntf( 'op-online' );

			}

			// Show button
			if( ( !self.opts.hide_when_offline || ( self.opts.hide_when_offline && this.ops ) ) && self.popup_mode !== 'open' ) {
				self.btn( 'show' );
			} else {
				self.btn( 'hide' );
			}

			// Update button title
			for( var i=0; i < self.$btn.length; i++ ) {
				self.$btn[i].querySelector('.scx-title').innerHTML = btn_title;
			}

			// Update popup header title
			/*if( self.$popup ) {
				var $h_title = '';

				for( var i=0; i < self.$popup.length; i++ ) {
					$h_title = self.$popup[i].querySelector('.scx-header .scx-title');

					if( $h_title ) {
						$h_title.innerHTML = btn_title;
					}
				}
			}*/

			// Setup reply box
			self.reply( can_reply );

		},

		/**
		 * Event to monitor current connection info
		 */
		_on_connect : function( is_connected ) {
			
			var $w = '';

			// No connection!
			if( !is_connected ) {

				this.app.ntf( 'no-conn', this.opts._no_conn, 'error', this.mode );

				// Update widget(s) classes
				for( var i=0; i < this.$widget.length; i++ ) {
					$w = this.$widget[i];

					this.app.remove_class( $w, '_scx-connecting' );
					this.app.add_class( $w, '_scx-disconnect' );
					$w.setAttribute( 'data-status', 'disconnect' );
					
				}

				
			// Connected or re-connected
			} else {
				this.app.hide_ntf( 'no-conn' );

				// Update widget(s) classes
				for( var i=0; i < this.$widget.length; i++ ) {
					$w = this.$widget[i];

					this.app.remove_class( $w, '_scx-connecting' );
					this.app.add_class( $w, '_scx-connect' );
					$w.setAttribute( 'data-status', 'connect' );
					
				}
			}

		},

		/**
		 * Event to monitor chat actions
		 */
		_on_join_chat : function( chat ) {

			// Update chat data
			this.chat_id = chat.id;
			this.chat = chat;

			// Hide welcome message
			this.app.hide_ntf( 'scx-welc-msg' );

			// Go online
			this.update_mode( 'online' );

			if( this.$cnv ) {
				var cnv_id = [ 'scx-cnv-', chat.id ].join('');

				for( var i=0; i < this.$cnv.length; i++ ) {
					this.$cnv[i].id = cnv_id;
				}
			}

		},
		_on_update_chat : function( chat ) {

			// Update chat data
			this.chat = chat;
			
			// Show typing...
			if( this.$typing ) {

				for( var i=0; i < this.$typing.length; i++ ) {
					this.$typing[i].innerHTML = '';

					if( chat.typing ) {
						for( user_id in chat.typing ) {
							if( user_id !== this.app.db.user_id ) {
								this.$typing[i].insertAdjacentHTML( 'beforeend', this.render( 'typing', chat.typing[user_id] ) );
							}
						}
					}

				}

			}
		},
		_on_leave_chat : function( chat_id ) {

			this.tmp_chat_id = chat_id;
			this.tmp_user = this.app.db.user;

			// Reset data
			this.last_msg = '';
			this.chat = '';
			this.chat_id = '';

			// Clear conversation
			if( this.$cnv ) {
				for( var i=0; i < this.$cnv; i++ ) {
					this.$cnv[i].innerHTML = '';
				}
			}

			// Go post-chat mode
			if( this.opts.show_postchat ) {
				this.update_mode( 'postchat' );
			} else {
				this.update_mode( 'prechat' );
			}
		},

		_on_new_msg : function( chat_id, msg ) { 
			var user_id = msg.user_id;

			if( this.app.db.is_op ) return;

			if( !this.app.user || !this.app.user.muted || !this.app.user.muted[user_id] ) {

				this.last_msg = msg;
				this.chat_msgs[msg.id] = msg;

				var read_msgs = this.chat.read_msgs || {};

				// Check if the message is unread
				if( read_msgs[this.app.user_id] < msg.timestamp ) {
					msg.unread = true;

					if( msg.type === 'default' && msg.user_id !== this.app.user_id ) {
						this.app.play( 'new-msg' );

					} else if( msg.type === 'auto-ntf' ) {
						this.app.play( 'new-ntf' );
					}
				}

				// Popup on new messages
				if( !this.popup_mode && ( !this.opts.hide_when_offline || ( this.opts.hide_when_offline && this.ops ) ) ) {
					this.popup( 'show' );
				}

				// Render message
				this.app.add_msg( chat_id, msg );
			}

		},
		_on_removed_msg : function( chat_id, msg_id ) {

			var obj_id = ['scx-msg-', msg_id].join('');
			this.app.remove_obj( obj_id );

			if( this.last_msg.id === msg_id ) {
				this.last_msg = '';
			}
		},

		/**
		 * Events to monitor chat actions
		 */
		_on_action : function( action ) {

			var self = this;

			switch( action.type ) {

				// Invited chat by an operator
				case 'invite':
					self.app.db.response_action( action.id, 'accepted', function() {
						
						// Join chat
						self.app.db.join_chat( action.chat_id );
					});
					break;

				// Chat ended by an operator
				case 'end-chat':
					self.end_chat();
					break;
			}
			
		},

		/**
		 * Events to monitor chat action responses
		 */
		_on_action_response : function( action ) {

			if( !action.status ) return;
			
		},

		/**
		 * Setup all forms
		 */
		_setup_forms : function() {
			var self = this,
				form_name = ''
				$form = '';

			// Event to monitor for sending "offline" form
			var fn_offline_on_send = function( fd ) {
				
				// Include user geo data if exists
				if( self.app.db.session && self.app.db.session.geo ) {
					fd[ 'xtra-city'] = self.app.db.session.geo.city;
					fd[ 'xtra-country'] = self.app.db.session.geo.country.name;
					fd[ 'xtra-ip'] = self.app.db.session.geo.ip;
				}

				// Include other custom data
				fd['xtra-ip_addr'] = self.opts.ip; // "ip_addr" is required name for backward compatibility of the plugin
				fd['xtra-current-page'] = self.opts.user.current_page;

				// Send offline message
				self.app.post( self.opts.ajax_url + '?action=chatx_ajax_cb&mode=offline', fd, function( r ) {

					// Successfully sent!
					if( !r.error ) {
						self.app.hide_ntf( 'error' );
						self.app.hide_ntf( 'wait' );

						// Clear textarea
						$('.scx-field-question .scx-field').value = "";

						self.app.ntf( 'sent', r.msg, 'success', 'offline', null, 3000 );

						setTimeout(function() {
							self.popup( 'hide' );
							self.btn( 'show' );
						}, 3000 );

					} else {
						self.app.ntf( 'error', r.error, 'error', 'offline' );
					}

					// Re-enable form
					self._reenable_form( 'offline' );

				});
			};

			// Event to monitor for sending "pre-chat" form
			var fn_prechat_on_send = function( data ) {

				var user_data = {};

				for( var id in data ) { user_data[id] = data[id]; }

				// Include visitor id
				user_data.visitor_id = self.app.user_id;

				// Update user data
				self.app.db.update_user( self.app.user_id, user_data );

				// Re-enable form
				self._reenable_form( 'prechat' );

				// Create chat and go online mode
				self.app.db.create_chat( user_data, 'private', function( new_chat_id ) {
					if( user_data['question'] ) {

						// Send visitors first question
						self.app.db.push_msg( new_chat_id, user_data['question'] );

						// Send notification message
						root.setTimeout( function() {
							self.app.db.push_msg( new_chat_id, self.opts._first_reply, 'auto-ntf' );
						}, 2000 );
					}
				});
			};

			for( var i=0; i < self.$forms.length; i++ ) {

				$form = self.$forms[i];
				form_name = $form.getAttribute('data-name');

				// Listen submit event
				$form.addEventListener( 'submit', function(e) {
					e.preventDefault(); // Stop submitting form.
				});

				switch( form_name ) {
					case 'offline':
						self._setup_fields( $form, fn_offline_on_send );
						break;

					case 'prechat':
						self._setup_fields( $form, fn_prechat_on_send );
						break;
				}
			}
		},

		/**
		 * Setup form fields
		 */
		_setup_fields : function( $form, on_send ) {
			var self = this,
				valid_nodes = [ 'INPUT', 'SELECT', 'TEXTAREA' ],
				form_name = $form.getAttribute('data-name'),
				$field = {},
				$send_btn = $form.querySelector( '.scx-send-btn' ),
				$last_label = '';

			// Activate send button if possible
			var fn_check_send_btn = function() {

				var activate = true;

				for( var i = 0; i < $form.elements.length; i++ ) {
					if( $form[i].hasAttribute('required') && !self.app.has_class( $form[i], 'scx-valid' ) ) {
						activate = false;
					}
				}
				
				// Clean disabled class just in case 
				self.app.remove_class( $send_btn, 'scx-disabled' );

				// Keep the button disabled
				if( !activate ) {
					self.app.add_class( $send_btn, 'scx-disabled' );
				} 

			};

			// Listen send button "click" events
			$send_btn.addEventListener('click', function(e) {
				e.preventDefault();

				fn_check_send_btn();

				if( self.form_working ) return;

				self.form_working = true;

				var data = self._get_form();

				on_send( data );

			});

			// Show up form labels
			var fn_focus = function(e) {

				var $label = this.previousElementSibling;

				// Hide latest label first
				if( $last_label ) {
					self.app.remove_class( $last_label, '_scx-show' );
				}

				// Show up current form label
				if( $label ) {
					self.app.add_class( $label, '_scx-show' );
				}

			};

			var fn_blur = function(e) { 
				$last_label = this.previousElementSibling;
			};

			var fn_check_form = function(e) {
				e.preventDefault();

				if( !self.app.has_class( $send_btn, 'scx-disabled' ) && $field.nodeName !== 'TEXTAREA' && e && e.keyCode === 13 && !e.shiftKey ) {
					$send_btn.click();
				} else {
					// Validate the field
					self._validate( e.target );

					// Activate send button if possible
					fn_check_send_btn();

					return true;
				}
			};

			// Setup fields
			for( var i = 0; i < $form.elements.length; i++ ) {

				if( this.app.in_array( $form[i].nodeName, valid_nodes ) ) {
					
					// Get field
					$field = $form[i];

					// Show/hide current form field label
					$field.onfocus =  fn_focus;
					$field.onblur =  fn_blur;

					// Send the form when user clicks "enter"
					$field.addEventListener( 'keyup', fn_check_form );
					$field.addEventListener( 'change', fn_check_form );
					
				}

			}

			// Disable send button for the first view
			self.app.add_class( $send_btn, 'scx-disabled' );
		},

		/**
		 * Get form data
		 */
		_get_form : function() {

			var form_name = [ '.scx-popup-', this.mode, ' .scx-form' ].join(''),
				$form = D.querySelector( form_name ),
				valid_nodes = [ 'INPUT', 'SELECT', 'TEXTAREA' ],
				form_data = {},
				$field = '';

			if( !$form ) { return; }

			$fields = $form.elements;
			$send_btn = $form.querySelector('.scx-send-btn');

			// Disable send button
			this.app.add_class( $send_btn, 'scx-disabled' );

			// Show up "Please wait" notification
			this.app.ntf( 'wait', this.opts._wait + '...', 'wait', this.mode );

			for( var id in $fields ) {
				$field = $fields[id];

				if( $field.value && this.app.in_array( $field.nodeName, valid_nodes ) ) {

					// Disable fields
					$field.setAttribute( 'disabled', 'disabled' );

					// Include field to form data
					form_data[$field.name] = $field.value;
				}
			}

			// Update "name" with localdomain part of email if possible
			var name_field = form_data['name'],
				email_field = form_data['email'];

			if( !name_field && email_field ) {
				form_data['name'] = email_field.substring( 0, email_field.indexOf( '@' ) );
			}

			// Also update DB
			this.app.db.user.name = form_data['name'];
			this.app.db.username = form_data['name'];

			return form_data;


		},

		/**
		 * Validate a form field
		 */
		_validate : function( $el ) {
			
			var val = '',
				is_req = $el.hasAttribute( 'required' ),
				type = $el.getAttribute( 'type' );

			if( type === 'file' ) {
				val = $el.files[0];
			} else {
				val = $el.value.trim();
			}

			// Clean error(s)
			this.app.remove_class( $el, 'scx-error scx-valid' );

			if( is_req ) {

				// Don't allow required fields empty
				if( val === '' ) {
					this.app.add_class( $el, 'scx-error' );
					this.app.ntf( 'error', this.opts._req_field, 'error', this.mode );

					return;
		
				// Validate email
				} else if( type === 'email' && !this.app.is_email( val ) ) {
					this.app.add_class( $el, 'scx-error' );
					this.app.ntf( 'error', this.opts._invalid_email, 'error', this.mode );

					return;
				}

				// It is valid!
				this.app.add_class( $el, 'scx-valid' );

				// Clean notification
				this.app.hide_ntf( 'error' );

			}

		},

		/**
		 * Re-enable form
		 */
		_reenable_form : function( mode ) {
			
			var form_name = [ '.scx-popup-', mode, ' .scx-form' ].join(''),
				$form = D.querySelector( form_name ),
				btn_name = [ '.scx-send-', mode ].join('');

			this.app.remove_class( D.querySelector( btn_name ), 'scx-disabled' );

			if( $form ) {
				var fields = $form.elements;

				for( var id in fields ) {
					fields[id].disabled = false;
				}
			}
		},

		/**
		 * Mark as read messages
		 */
		_read : function() {
			var self = this;

			if( this.chat_id ) {
				if( this.last_msg ) {
					this.app.db.read( this.chat_id, this.last_msg.timestamp );
				}

				// Remove other new messages
				setTimeout( function() {

					if( self.$cnv ) {
						var $new_msgs = D.querySelectorAll( '.scx-cnv .scx-new' );

						for( var i=0; i < $new_msgs.length; i++ ) {
							self.app.remove_class( $new_msgs[i], 'scx-new' );
						}
					}

				},0);
			}
		}

	};

	// Run the script in "noConflict" mode
	SCX_UI.noConflict = function noConflict() {
		root.SCX_UI = prev_scx_ui;
		return SCX_UI;
	};

	// Export the object as global
	root.SCX_UI = SCX_UI;


	//
	// External methods
	// ------------------

	/**
	 * Update mode
	 */
	SCX_UI.prototype.update_mode = function( mode ) {
		
		var self = this,
			last_popup = [ 'scx-popup-', self.mode ].join('');

		// Show prechat form before go online
		if( mode === 'online' && !self.chat_id && self.opts.show_prechat ) {
			mode = 'prechat';
		}

		var current_popup = [ 'scx-popup-', mode ].join('');

		// Hide last open popup
		if( last_popup !== current_popup ) {
			self.app.remove_class( D.getElementById( last_popup ), '_scx-open scx-active' );
		}

		// Remove initial mode class from button
		if( self.$btn ) {
			for( var i=0; i<self.$btn.length; i++ ) {
				self.app.remove_class( self.$btn[i], '_scx-init' );
			}
		}

		// Update current mode
		self.mode = mode;
		self.$popup = D.getElementsByClassName( current_popup );

		// Update popup
		if( self.popup_mode === 'open' ) {
			self.popup( 'show' );
		}

		// Update widget classes
		for( var i=0; i < self.$widget.length; i++ ) {
			$w = self.$widget[i];

			self.app.remove_class( $w, self.modes_list );
			self.app.add_class( $w, '_scx-connect' );
			$w.setAttribute( 'data-status', 'connect' );
			
		}

		// Show welcome message if not chat in conversation
		if( self.mode === 'online' && !self.chat_id ) {
			self.app.ntf( 'scx-welc-msg', self.opts._welcome_msg, 'info', self.mode );
		}

	};

	/**
	 * Chat button events
	 */
	SCX_UI.prototype.btn = function( action ) {

		var self = this,
			$btn = '';

		if( self.$btn ) {

			for( var i=0; i < self.$btn.length; i++ ) {

				$btn = self.$btn[i];

				switch( action ) {
			
					// Initial view of chat button
					case 'init':
						self.app.add_class( $btn, 'scx-active _scx-init' );

						// Refresh mode
						self.btn_mode = 'init';

						break;
					
					// Show chat button (but don't change popup status)
					case 'show':

						// Button allowed to show?
						if( !self.opts.show_btn ) return;

						// Setup classes
						self.app.add_class( $btn, '_scx-open scx-active' );

						// Include initial mode class
						if( self.mode === 'init' ) {
							self.app.add_class( $btn, '_scx-init' );
						}

						// Refresh mode
						self.btn_mode = 'open';

						// Show attention grabber if possible
						// self.grabber( 'show' );

						break;

					// Show chat button WITHOUT closing popup
					case 'show-with-popup':
						self.app.add_class( $btn, 'scx-active' );

						// Refresh mode
						self.btn_mode = 'open';

						// Show attention grabber if possible
						// self.grabber( 'show' );

						break;

					// 
					// Toggle button display status
					//
					case 'toggle':

						if( self.btn_mode === 'open' ) {
							self.btn( 'hide' );
						} else {
							self.btn( 'show' );
						}

						break;

					// Hide chat button
					case 'hide':
						self.app.remove_class( $btn, 'scx-active' );

						// Refresh mode
						self.btn_mode = null;

						break;
				}

			}
		}

	};

	/**
	 * Popup events
	 */
	SCX_UI.prototype.popup = function( action ) {

		var self = this,
			$popup = '';


		if( self.$popup ) {

			for( var i=0; i < self.$popup.length; i++ ) {

				$popup = self.$popup[i];

				switch( action ) {

					// 
					// Show popup
					//
					case 'show':

						// Update popup mode
						self.popup_mode = 'open';

						// Hide chat button
						self.btn( 'hide' );

						// Activate current popup
						self.app.add_class( $popup, 'scx-active _scx-open' );

						var $first_field = (this.mode === 'online' ) ? $('.scx-reply') : $popup.querySelector('.scx-field');
						
						// Focus first input field
						if( $first_field ) {
							setTimeout( function() {
								$first_field.focus();
							}, 500 ); // Wait for the end of the animation
						}

						// Set popup status
						self.app.db.set_param( 'popup', 'open' );

						break;

					// 
					// Just hide popup (but don't change button status)
					//
					case 'hide':
						
						// Update popup mode
						self.popup_mode = null;

						// Deactivate popup
						self.app.remove_class( $popup, 'scx-active _scx-open' );

						// Set popup status
						self.app.db.set_param( 'popup', null );

						break;

					// 
					// Toggle popup display status
					//
					case 'toggle':

						if( self.popup_mode === 'open' ) {
							self.popup( 'hide-with-btn' );
						} else {
							self.popup( 'show' );
						}

						break;

					// 
					// Hide popup and chat button
					//
					case 'hide-with-btn':

						// Refresh mode
						self.popup_mode = null;

						// Deactivate popup
						self.app.remove_class( $popup, 'scx-active _scx-open' );

						// Set popup status
						self.app.db.set_param( 'popup', null );

						break;
				}

				// Refresh UI
				self.refresh_ui();
			}

		}

	};

	/**
	 * Reply box events
	 */
	SCX_UI.prototype.reply = function( can_reply ) {

		var self = this,
			$reply = '';

		if( self.$reply ) {

			for( var i=0; i < self.$reply.length; i++ ) {

				$reply = self.$reply[i];

				if( can_reply ) {
					$reply.disabled = false;
					$reply.focus();

					self.refresh_ui();

					// Read unread messages
					self._read();


				} else {
					$reply.disabled = true;

					// Disable send buttons
					for( var i=0; i < self.$reply_send.length; i++ ) {
						self.app.add_class( self.$reply_send[i], 'scx-disabled' );
					}
				}

			}
		}

	};

	/**
	 * Refresh user interface (chat widget)
	 */
	SCX_UI.prototype.refresh_ui = function() {

		// Trigger "resize" event on page load (works with IE)
		var e = W.document.createEvent( 'UIEvents' );
		e.initUIEvent('resize', true, false, W, 0);
		W.dispatchEvent( e );

		// Scroll down
		if( this.$cnv ) {
			for( var i=0; i<this.$cnv.length; i++ ) {
				this.$cnv[i].scrollTop = 10000
			}
		}
	};

	/**
	 * End current chat
	 */
	SCX_UI.prototype.end_chat = function() {

		if( this.chat_id ) {
			var msg = this.opts._user_left_chat.replace( '%s', this.app.db.username );

			this.app.db.push_msg( this.chat_id, msg, 'auto-ntf' );

			// Leave from the chat
			this.app.db.leave_chat( this.chat_id );
		}
	};

	/**
	 * Render template
	 */
	SCX_UI.prototype.render = function( template, p ) {
		
		var arr = [];

		switch( template ) {

			// User is typing...
			case 'typing':
				var msg = this.opts._user_typing.replace( '%s', p.name );
				arr = [ '<span class="scx-user-typing-',p.id,'"><i class="scx-ico scx-ico-pencil"></i>',msg,'...</span>' ];
				break;

		}

		return arr.join('');

	};

})();