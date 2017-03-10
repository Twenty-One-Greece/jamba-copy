/*!
 * Screets Chat X - Application
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
		prev_chat_x = root.ChatX;

	function ChatX( opts ) {

		var self = this;

		// Setup and establish options
		this.opts = opts;

		// Create real-time database
		if( !opts.no_db ) {
			this.db = new SCX_FB( this.opts ); // Firebase
			
			// Get reference
			this.ref = this.db.ref;

			// Bind data events
			this._bind_data_events();
		}


		// User data
		this.user = {};
		this.user_id = '';
		this.username = '';

		// Useful variables
		this.retina = this._is_retina();

		// Sounds
		this.sounds = {};
		this._add_sound( 'connected' );
		this._add_sound( 'disconnected' );
		this._add_sound( 'new-msg' );
		this._add_sound( 'new-ntf' );
		this._add_sound( 'new-visitor' );

		// Some useful regexes
		this.url_pattern = /\b(?:https?|ftp):\/\/[a-z0-9-+&@#\/%?=~_|!:,.;]*[a-z0-9-+&@#\/%=~_|]/gim;
		this.pseudo_url_pattern = /(^|[^\/])(www\.[\S]+(\b|$))/gim;

	}

	//
	// Internal methods
	// ------------------

	ChatX.prototype = {

		/**
		 * Bind data events
		 */
		_bind_data_events : function() {

			this.db.on( 'user-update', this._on_user_update.bind( this ) );

		},


		/**
		 * Listen user updates
		 */
		_on_user_update : function( user ) {

			// User is deleted!
			if( !user || !user.id ) {
				
				// Disconnect for 
				/*if( !this.opts.is_frontend ) {
					var user = firebase.auth().currentUser;
					var credential = firebase.auth.EmailPasswordAuthProvider.credential( this.opts.user.email, this.opts.user.password );

					if( user ) {

						user.reauthenticate(credential).then(function() {
							// User re-authenticated.
							D.querySelector('.scx-overlay').click();
						}, function(err) {
							// An error happened.
							console.error( err );
						});

					}
				}*/
				
				/*setTimeout( function() {
					W.location.reload();
				}, 0 );*/

				return;
			}

			// Update our current user state and render latest user name
			this.user = user;

			// Update user id
			this.user_id = user.id.toString();
			this.username = user.name.toString();

			// FIXME!
			// Update our interface to reflect which users are muted or not.
			/*
			var mutedUsers = this._user.muted || {};
			$('[data-event="firechat-user-mute-toggle"]').each(function(i, el) {
				var userId = $(this).closest('[data-user-id]').data('user-id');
				$(this).toggleClass('red', !!mutedUsers[userId]);
			});

			// Ensure that all messages from muted users are removed.
			for (var userId in mutedUsers) {
				$('.message[data-user-id="' + userId + '"]').fadeOut();
			}
			*/
		},

		/**
		 * Setup UI for operators
		 */
		_setup_op_ui : function() {},

		/**
		 * Add sound
		 */
		_add_sound : function( name ) {
			var source = D.createElement('source'),
				filename = this.opts.plugin_url + '/assets/sounds/' + name;
			
			this.sounds[name] = D.createElement('audio');
			
			if ( this.sounds[name].canPlayType('audio/mpeg;' ) ) {
				source.type= 'audio/mpeg';
				source.src= filename + '.mp3';

			} else {
				source.type= 'audio/ogg';
				source.src= filename + '.ogg';
			}
			this.sounds[name].appendChild(source);
		},

		/**
		 * Is URL an image
		 */
		_is_retina : function( uri ) {
			
			if ( W.matchMedia ) {
				var mq = W.matchMedia( "only screen and (min--moz-device-pixel-ratio: 1.3), only screen and (-o-min-device-pixel-ratio: 2.6/2), only screen and (-webkit-min-device-pixel-ratio: 1.3), only screen  and (min-device-pixel-ratio: 1.3), only screen and (min-resolution: 1.3dppx)" );
				return ( mq && mq.matches || ( W.devicePixelRatio > 1 ) ); 
			}

			return false;

		},

		/**
		 * Is URL an image
		 *
		 * Source: http://stackoverflow.com/a/19395606/272478
		 */
		_is_img : function( uri ) {
			
			// Make sure we remove any nasty GET params 
			uri = uri.split('?')[0];

			// Moving on now
			var parts = uri.split('.'),
				extension = parts[parts.length-1],
				imageTypes = ['jpg','jpeg','tiff','png','gif','bmp'];

			if( imageTypes.indexOf( extension ) !== -1 ) {
				return true;
			}

		},


	};

	// Run the script in "noConflict" mode
	ChatX.noConflict = function noConflict() {
		root.ChatX = prev_chat_x;
		return ChatX;
	};

	// Export the object as global
	root.ChatX = ChatX;

	/**
	 * Exposes internal chat bindings via this external interface
	 */
	ChatX.prototype.on = function( event_type, callback ) {
		this.db.on( event_type, callback );
	};

	/**
	 * Initialize an authenticated session with a user id and name.
	 * This method assumes that the underlying Firebase reference has
	 * already been authenticated
	 */
	ChatX.prototype.set_user = function( user_id, username ) {

		var self = this;

		// Initialize data events
		self.db.set_user( user_id, username, function( user ) {

			self.user = user;

			// Bind operator UI events
			if( self.db.is_op ) {
				self._setup_op_ui();
			}

			// Resume session (don't resume when OP is in front-end)
			if( !( self.opts.is_frontend && self.db.is_op ) ) {
				self.db.resume_session();
			}

		});

	};

	/**
	 * Add message into chat conversation
	 */
	ChatX.prototype.add_msg = function( chat_id, raw_msg, reverse_direction ) {
		
		var obj_id = ['scx-cnv-',chat_id].join(''),
			$cnv = D.getElementById( obj_id ),
			pos = ( reverse_direction ) ? 'afterbegin' : 'beforeend',
			cls = [];

		if( !$cnv ) return;

		// Message data
		var is_you = ( raw_msg.user_id == this.db.user_id ),
		data = {
			id              : raw_msg.id,
			user_id         : raw_msg.user_id,
			name            : raw_msg.name,
			avatar          : this.render( 'avatar', raw_msg.avatar ),
			type            : raw_msg.type,
			time            : this.format_time( raw_msg.timestamp ),
			msg             : raw_msg.msg || '',
			self_msg        : is_you
		};

		// Sanitize the message
		data.msg = this.sanitize( raw_msg.msg );

		// Is it you?
		var _msg_owner = (is_you ) ? 'scx-you' : 'scx-other';
		cls.push( _msg_owner );

		// Is unread message
		if( raw_msg.unread ) {
			cls.push( 'scx-new' );
		}

		// Include type
		cls.push( 'scx-type-' + raw_msg.type );

		data._cls = cls.join(' ');

		$cnv.insertAdjacentHTML( pos, this.render( 'chat-msg', data ) );

		// Scroll down
		if( !reverse_direction ) {
			$cnv.scrollTop = 10000;
		}

	};

	/**
	 * Play a sound
	 */
	ChatX.prototype.play = function( name ) {
		this.sounds[name].play();
	};

	/**
	 * Create notification on UI
	 */
	ChatX.prototype.ntf = function( id, msg, type, section, ico, duration ) {
		
		var self = this,
			$ntf = '';

		// Update section
		section = section || 'main';
		section = [ '.scx-ntf-', section ].join('');

		var $all_ntf = D.querySelectorAll( section );

		if( $all_ntf ) {

			for( var i=0; i < $all_ntf.length; i++ ) {

				$ntf = $all_ntf[i];

				// Include id
				$ntf.id = [ 'scx-ntf-', id ].join('');

				// Get icon
				if( ico ) {
					ico = self.render( 'ico', ico );
				}

				// Render message
				$ntf.innerHTML = self.render( 'ntf', {
					msg: msg,
					type: type,
					ico: ico
				});

				// Set classes
				$ntf.className += ' scx-active';

				// Hide notification after a specific time
				if( duration ) {
					root.setTimeout( function() {
						self.hide_ntf( id );

					}, duration );
				
				// Hide chat for only main notifications in the console
				} else if( !self.opts.is_frontend && section === '.scx-ntf-main' ) {
					$ntf.addEventListener('click', function(e) {
						self.remove_class( this, 'scx-active' );
					});
				}

			}

		}

	};

	/**
	 * Create notification on UI
	 */
	ChatX.prototype.hide_ntf = function( id ) {

		id = [ 'scx-ntf-', id ].join('');
		$ntf = D.getElementById( id );

		if( $ntf ) {
			this.add_class( $ntf, 'scx-close' );
			this.remove_class( $ntf, 'scx-active' );
		}

	};

	/**
	 * Remove a DOM object
	 */
	ChatX.prototype.remove_obj = function( id ) {

		var $item = D.getElementById(id);

		if( $item ) { 
			$item.parentNode.removeChild( $item ); 
		}

	};

	/**
	 * Clear up UI from highlighted messages and counts
	 */
	ChatX.prototype.reset_ui = function() {
		var $highlighted = D.getElementsByTagName('scx-highlighted');

		if( $highlighted ) {
			for( var i=0; i < $highlighted.length; i++ ) {
				this.remove_class( $highlighted[i], 'scx-highlighted' );
			}
		}
	};

	/**
	 * Convert HTML tags to text and replace plain URLs with links
	 *
	 * @param    {string}
	 */
	ChatX.prototype.sanitize = function( str ) {
		
		var links = str.match( this.url_pattern ) || [],
			pseudo_links = str.match( this.pseudo_url_pattern ),
			link = '',
			href = '',
			rx = '';

		// Merge links with pseudos
		if( pseudo_links ) {
			for( var i in pseudo_links ) { links.push( pseudo_links[i].trim() ); }
		}

		// Convert HTML tags and new lines to text
		str = String(str).replace(/&/g, '&amp;').replace(/</g, '&lt;').replace(/>/g, '&gt;').replace(/"/g, '&quot;').replace( /\n/g, '<br>');

		// Twitter Emoji parse
		str = twemoji.parse( str );

		for( var i=0; i<links.length; ++i ) {
			link = links[i];

			// Clean http:// part from url
			href = link.replace( /.*?:\/\//g, "" );

			rx = new RegExp( link, 'g' );

			str = str.replace( rx, this.render( 'msg-link', href ) );
			
		}

		return str;
		
	};

	/**
	 * Get time difference between two dates
	 *
	 */
	ChatX.prototype.time_diff = function( first, sec, return_type ) {

		var diff = (first - sec); // milliseconds between two dates

		switch( return_type ) {

			case 'ms': // milliseconds
				return diff;

			case 'm': // minutes
				return Math.round(((diff % 86400000) % 3600000) / 60000);
				
			case 'h': // hours
				return Math.round((diff % 86400000) / 3600000);

			case 'd': // days
				return Math.round(diffMs / 86400000);

		}
	};

	/**
	 * Given a timestamp, format it in the form hh:mm am/pm. Defaults to now
	 * if the timestamp is undefined.
	 *
	 * @param    {Number}    timestamp
	 * @param    {string}    date
	 * @return   {string}
	 */
	ChatX.prototype.format_time = function( timestamp, full ) {

		var date = (timestamp) ? new Date(timestamp) : new Date(),
			year = date.getFullYear(),
			month = date.getMonth() + 1,
			day = date.getDate(),
			hours = date.getHours() || '0',
			minutes = '' + date.getMinutes();

		minutes = ( minutes.length < 2 ) ? '0' + minutes : minutes;
		day = ( day.length < 2 ) ? '0' + day : day;
		month = ( month.length < 2 ) ? '0' + month : month;

		if( !full ) {
			return '' + hours + ':' + minutes;
		} else {
			return year + '-' + month + '-' + day + ' ' + hours + ':' + minutes;
		}

	};

	/**
	 * Add new class(es)
	 */
	ChatX.prototype.add_class = function( el, new_cls ) {

		this.remove_class(el, new_cls );

		var cls = [el.className, new_cls].join(' ');

		el.className = cls;

	};

	/**
	 * Remove class(es)
	 */
	ChatX.prototype.remove_class = function( el, remove ) {

		if( !el ) return;
				var classes = remove.split( ' ' ),
			rx = '';

		for( var i=0; i < classes.length; i++ ) {
			rx = new RegExp( '(?:^|\\s)' + classes[i] + '(?!\\S)', 'g' );

			// Update class
			el.className = el.className.replace( rx , '' );

		}

	};

	/**
	 * Has class?
	 */
	ChatX.prototype.has_class = function( $el, selector ) {

		var class_name = ' ' + selector + ' ';

		if ( ( ' ' + $el.className + ' ' ).replace( /[\n\t]/g, ' ' ).indexOf( class_name ) > -1 ) {
			return true;
		}
		return false;

	};

	/**
	 * Check if a value exists in an array
	 *
	 * @param    {string}
	 */
	ChatX.prototype.in_array = function( val, array ) {
		
		var length = array.length;

		for(var i = 0; i < length; i++) {
			if( array[i] == val ) return true;
		}

		return false;
	};

	/**
	 * Validate email field
	 */
	ChatX.prototype.is_email = function( email ) {

		var re = /^([\w-]+(?:\.[\w-]+)*)@((?:[\w-]+\.)*\w[\w-]{0,66})\.([a-z]{2,6}(?:\.[a-z]{2})?)$/i;

		return re.test( email );

	};

	/**
	 * Send a post request to the server
	 */
	ChatX.prototype.post = function( url, data, callback ) {
		
		var self = this,
			xhr = new XMLHttpRequest(),
			fd = new FormData();

		xhr.open( "POST", url, true );

		// Handle response
		xhr.onreadystatechange = function() {

			if ( xhr.readyState == 4 ) {

				// Perfect!
				if( xhr.status == 200 ) {
					if( callback ) { callback( JSON.parse( xhr.responseText ) ); }

				// Something wrong!
				} else {
					if( callback ) { callback( null ); }
				}
			}
		};
		
		// Get data
		for( var k in data ) { fd.append( k, data[k] ) ; }

		// Initiate a multipart/form-data upload
		xhr.send( fd );

	};

	/**
	 * Render template
	 */
	ChatX.prototype.render = function( template, p ) {
		
		var arr = [];

		switch( template ) {

			// Notification
			case 'ntf':
				arr = [ '<div class="scx-wrap scx-', p.type,'">', p.ico, p.msg, '</div>' ];
				break;

			// Admin notification
			case 'admin-ntf':
				arr = [ '<div class="scx-wrap scx-', p.type,'">', p.ico, p.msg, '</div>' ];
				break;

			// Icon
			case 'ico':
				arr = [ '<span class="scx-ico ',p,'"></span>' ];
				break;

			// Normal link :)
			case 'msg-link':
				arr = [ '<a href="http://',p,'" target="_blank" rel="nofollow">', p,'</a>' ];
				break;

			// Chat message
			case 'chat-msg':
				arr = [ '<div id="SCX-msg-',p.id,'" class="scx-msg scx-msg-',p.id,' ',p._cls,'">',p.avatar,'<div class="scx-msg-wrap"><span class="scx-msg-time">',p.time,'</span><span class="scx-msg-author">',p.name,'</span><span class="scx-msg-content">',p.msg,'</span></div></div>' ];
				break;

			// Avatar
			case 'avatar':
				arr = [ '<span class="scx-user-avatar"><img src="',p,'" alt="" /></span>' ];
				break;

			// Title
			case 'title':
				arr = [ '' ];
				break;

		}

		return arr.join('');

	};


})();