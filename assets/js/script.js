jQuery( document ).ready( function( $ ) { "use strict";
	// Variables
	var postID = $( '#post_ID' ).val();
	var ajaxUrl = aiko_developer_object.ajax_url
	var apiKey = aiko_developer_object.api_key;
	var settingsPage = aiko_developer_object.link;
	const messages = aiko_developer_object.pluginMessages;

	function aiko_developer_code_not_generated() {
		var code_not_generated = $( '#aiko-developer-after-title' ).data( 'code-not-generated' );
		if ( $( '#aiko-developer-first' ).val() !== '1' ) {
			if ( code_not_generated === 1 ) {
				$( '#aiko-developer-code-not-generated-wrapper' ).addClass( "aiko-developer-notice-show" );
			} else {
				$( '#aiko-developer-code-not-generated-wrapper' ).removeClass( "aiko-developer-notice-show" );
			}
		} else {
			$( '#aiko-developer-code-not-generated-wrapper' ).removeClass( "aiko-developer-notice-show" );
		}
	}

	// Get message from array
	function aiko_developer_get_message( code ) {
		return messages[code];
	}

	// Hidden meta boxes before first prompt
	function aiko_developer_toggle_meta_boxes() {
		var postStatus = $( '#original_post_status' ).val();

		if ( postStatus === 'publish' && $( '#aiko-developer-first' ).val() !== '1' ) {
			$( '#aiko-developer-input-label' ).text( aiko_developer_get_message( "label-after" ) );
			$( '#aiko-developer-description-label' ).text( aiko_developer_get_message( "label-after-description" ) );
			$( '#aiko-developer-download-meta-box, #aiko-developer-php-output-meta-box, #aiko-developer-js-output-meta-box, #aiko-developer-css-output-meta-box, #aiko-developer-functional-requirements-output, #aiko-developer-improvements-wrapper' ).show();
			$( '#aiko-developer-user-prompt-rephrase-wrapper' ).hide();

			// Notice when codes are empty
			var phpCode = $( '#aiko-developer-php-output-meta-box pre' ).text();
			var jsCode = $( '#aiko-developer-js-output-meta-box pre' ).text();
			var cssCode = $( '#aiko-developer-css-output-meta-box pre' ).text();
			if ( ! phpCode.trim() && ! jsCode.trim() && ! cssCode.trim() && ! $( '#aiko-developer-published-notice' ).hasClass( 'aiko-developer-notice-show' ) ) {
				var code_not_generated_is_shown = $( '#aiko-developer-code-not-generated-wrapper' ).hasClass( 'aiko-developer-notice-show' );
				$( '.aiko-developer-notice-show' ).removeClass( 'aiko-developer-notice-show' );
				if ( code_not_generated_is_shown ) {
					$( '#aiko-developer-code-not-generated-wrapper' ).addClass( 'aiko-developer-notice-show' );
				}
				var api_not_present_is_shown = $( '#aiko-developer-api-not-present-wrapper' ).hasClass( 'aiko-developer-notice-show' );
				if ( api_not_present_is_shown ) {
					$( '#aiko-developer-api-not-present-wrapper' ).addClass( 'aiko-developer-notice-show' );
				}
				$( '#aiko-developer-empty-codes-notice' ).addClass( 'aiko-developer-notice-show' );
				$( '#aiko-developer-php-output-meta-box, #aiko-developer-js-output-meta-box, #aiko-developer-css-output-meta-box' ).hide();
			}
		} else {
			$( '#aiko-developer-input-label' ).text( aiko_developer_get_message( "label-first" ) );
			$( '#aiko-developer-description-label' ).text( aiko_developer_get_message( "label-first-description" ) );
			$( '#aiko-developer-download-meta-box, #aiko-developer-php-output-meta-box, #aiko-developer-js-output-meta-box, #aiko-developer-css-output-meta-box, #aiko-developer-functional-requirements-output, #aiko-developer-improvements-wrapper' ).hide();
			$( '#aiko-developer-user-prompt-rephrase-wrapper' ).show();
		}
	}

	// Notices show after refresh
	function aiko_developer_show_notices_after_refresh() {
		$( '#aiko-developer-published-notice-text' ).text( aiko_developer_get_message( $( '#aiko-developer-published-notice-text' ).data( 'message' ) ) );
		$( "#aiko-developer-edited-code-notice-text" ).text( aiko_developer_get_message( $( '#aiko-developer-edited-code-notice-text' ).data( 'message' ) ) );
	}
	
	aiko_developer_toggle_meta_boxes();
	aiko_developer_show_notices_after_refresh();
	aiko_developer_code_not_generated();

	// Title enter bug
	$( '#title' ).on( 'keypress', function( e ) {
		if ( e.which === 13 ) {
			e.preventDefault();
		}
	});

	// Slug enter bug
	$( '#aiko-developer-post-slug' ).on( 'keypress', function( e ) {
		if ( e.which === 13 ) {
			e.preventDefault();
		}
	});

	// Close notification
	$( '.aiko-developer-notice .aiko-developer-notice-close' ).on( 'click', function() { 
		$( this ).parent().removeClass( 'aiko-developer-notice-show' );
	});
	
	// Alert OK
	$( '#aiko-developer-alert-ok' ).on( 'click', function() {
		event.preventDefault();
		if ( $( this ).data( 'action' ) === 'openai-api-url' ) {
			window.open( settingsPage, '_blank', 'noopener, noreferrer' );
			$( '#aiko-developer-refresh-popup-overlay' ).fadeIn();
			$( '#aiko-developer-refresh-text' ).text( aiko_developer_get_message( 'notice-refresh-after-api-key' ) );
			$( '#aiko-developer-alert-popup-overlay' ).fadeOut();
		} else if ( $( this ).data( 'action' ) === 'openai-api-url' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeOut();
		} else if ( $( this ).data( 'action' ) === 'error-no-title' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeOut();
		} else if ( $( this ).data( 'action' ) === 'error-no-slug' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeOut();
		} else if ( $( this ).data( 'action' ) === 'error-empty-fr' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeOut();
		}  else if ( $( this ).data( 'action' ) === 'error-empty-comment-rephrase' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeOut();
		} else {
			$( '#aiko-developer-alert-popup-overlay' ).fadeOut();
			$( '#aiko-developer-rephrase-comments-error-text' ).text( aiko_developer_get_message( 'error-general' ) );
		}
		
	});

	// Refresh page
	$( '#aiko-developer-refresh' ).on( 'click', function() {
		$( '#aiko-developer-refresh-popup-overlay' ).fadeOut();
		$( '#aiko-developer-status' ).val( '4' );
		$( '#aiko-developer-loader-overlay' ).fadeIn();
	});

	// Trigger publish
	$( '.aiko-developer-publish' ).on( 'click', function() {
		event.preventDefault();
		$( '#publish' ).trigger( 'click' );
	});

	// Publish actions
	$( '#publish' ).on( 'click', function() {
		if ( $( '#title' ).val() === '' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "error-no-title" ) );
			$( '#aiko-developer-alert-ok' ).data( 'action', 'error-no-title' );
			$( '#title' ).focus();
			event.preventDefault();
			return;
		} else if ( $( '#aiko-developer-post-slug' ).val() === '' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "error-no-slug" ) );
			$( '#aiko-developer-alert-ok' ).data( 'action', 'error-no-slug' );
			$( '#aiko-developer-post-slug' ).focus();
			event.preventDefault();
			return;
		} else if ( ! $( '#aiko-developer-input' ).val() && $( '#aiko-developer-first' ).val() === '1' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "error-empty-fr" ) );
			$( '#aiko-developer-alert-ok' ).data( 'action', 'error-empty-fr' );
			event.preventDefault();
			return;
		} else if ( apiKey === '' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "error-no-api-key" ) );
			$( '#aiko-developer-alert-ok' ).data( 'action', 'openai-api-url' );
			event.preventDefault();
			return;
		} else {
			$( '#aiko-developer-loader-overlay' ).fadeIn();
		}
	});

	// Download ZIP
	$( '.aiko-developer-download-zip' ).on( 'click', function() {
		event.preventDefault();
		$( '#aiko-developer-loader-overlay' ).fadeIn();
		
		var phpCode = $( '#aiko-developer-php-output-meta-box pre' ).text();
		var jsCode = $( '#aiko-developer-js-output-meta-box pre' ).text();
		var cssCode = $( '#aiko-developer-css-output-meta-box pre' ).text();

		$.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: 'download_zip',
				nonce: $( '#aiko_developer_nonce_field' ).val(),
				php_code: phpCode,
				js_code: jsCode,
				css_code: cssCode,
				post_id: postID
			},
			success: function( response ) {
				if ( response.success ) {
					window.location.href = response.data;
				} else {
					$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
					$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( response.data ) );
				}
				$( '#aiko-developer-loader-overlay' ).fadeOut();
			}
		});
	});

	// Edit
	$( '.aiko-developer-edit' ).on( 'click', function() {
		event.preventDefault();
		if ( $( this ).data( 'type' ) !== 'functional-requirements' ) {
			var metaBox = '#aiko-developer-' + $( this ).data( 'type' ) + '-output-meta-box ';
			var field = 'pre';
		} else {
			var metaBox = '#aiko-developer-functional-requirements-output ';
			var field = 'p.aiko-developer-block-content';
		}
		$( '#aiko-developer-edit-popup-overlay' ).fadeIn();
		$( '#aiko-developer-edit-textarea' ).val( $( metaBox + field ).text().trim() );
		$( '#aiko-developer-edit-submit' ).data( 'type', $( this ).data( 'type' ) );
		$( '#aiko-developer-edit-cancel' ).data( 'type', $( this ).data( 'type' ) );
	});

	// Submit edit
	$( '#aiko-developer-edit-submit' ).on( 'click', function() {
		event.preventDefault();
		$( '#aiko-developer-edit-popup-overlay' ).fadeOut();
		$( '#aiko-developer-status' ).val( $( '#aiko-developer-edit-submit' ).data( 'type' ) === 'functional-requirements' ? '2' : '1' );
		
		if ( $( '#aiko-developer-edit-submit' ).data( 'type' ) === 'functional-requirements' && ! $( '#aiko-developer-edit-textarea' ).val() ) {
			var code_not_generated_is_shown = $( '#aiko-developer-code-not-generated-wrapper' ).hasClass( 'aiko-developer-notice-show' );
			$( '.aiko-developer-notice-show' ).removeClass( 'aiko-developer-notice-show' );
			if ( code_not_generated_is_shown ) {
				$( '#aiko-developer-code-not-generated-wrapper' ).addClass( 'aiko-developer-notice-show' );
			}
			var api_not_present_is_shown = $( '#aiko-developer-api-not-present-wrapper' ).hasClass( 'aiko-developer-notice-show' );
			if ( api_not_present_is_shown ) {
				$( '#aiko-developer-api-not-present-wrapper' ).addClass( 'aiko-developer-notice-show' );
			}
			$( '#aiko-developer-empty-edit-notice' ).addClass( 'aiko-developer-notice-show' );
			$( '#aiko-developer-empty-edit-notice-text' ).text( aiko_developer_get_message( "error-empty-edit" ) );
			event.preventDefault();
		} else {
			$( '#aiko-developer-loader-overlay' ).fadeIn();
			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'edit',
					nonce: $( '#aiko_developer_nonce_field' ).val(),
					edited: $( '#aiko-developer-edit-textarea' ).val(),
					type: $( '#aiko-developer-edit-submit' ).data( 'type' ),
					post_id: postID
				},
				success: function( response ) {
					if ( response.success ) {
						$( '#aiko-developer-status' ).val( '5' );
						$( '#publish' ).trigger( 'click' );
					} else {
						$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
						$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( response.data ) );
						$( '#aiko-developer-loader-overlay' ).fadeOut();
					}
				}
			});
		}
	});

	// Cancel edit
	$( '#aiko-developer-edit-cancel' ).on( 'click', function() {
		event.preventDefault();
		if ( $( this ).data( 'type' ) !== 'functional-requirements' ) {
			var metaBox = '#aiko-developer-' + $( this ).data( 'type' ) + '-output-meta-box ';
			var field = 'pre';
		} else {
			var metaBox = '#aiko-developer-functional-requirements-output ';
			var field = 'p.aiko-developer-block-content';
		}
		var beforeEdit = $( metaBox + field ).text().trim();
		var afterEdit = $( '#aiko-developer-edit-textarea' ).val().trim();
		if ( beforeEdit !== afterEdit ) {
			$( '#aiko-developer-confirm-popup-overlay' ).fadeIn();
			$( '#aiko-developer-confirm-text' ).text( aiko_developer_get_message( "confirm-cancel-edit" ) );
		}
		$( '#aiko-developer-edit-popup-overlay' ).fadeOut();
	});

	// Confirm cancel yes
	$( '#aiko-developer-confirm-yes' ).on( 'click', function() {
		event.preventDefault();
		$( '#aiko-developer-edit-textarea' ).val( '' );
		$( '#aiko-developer-edit-submit' ).data( 'type', '' );
		$( '#aiko-developer-confirm-popup-overlay' ).fadeOut();
	});

	// Confirm cancel no
	$( '#aiko-developer-confirm-no' ).on( 'click', function() {
		event.preventDefault();
		$( '#aiko-developer-confirm-popup-overlay' ).fadeOut();
		$( '#aiko-developer-edit-popup-overlay' ).fadeIn();
	});

	// Rephrase functional requirements (without adding)
	$( '#aiko-developer-functional-requirements-rephrase' ).on( 'click', function() {
		event.preventDefault();

		if ( apiKey === '' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "error-no-api-key" ) );
			$( '#aiko-developer-alert-ok' ).data( 'action', 'openai-api-url' );
			return;
		}

		if ( $( '#aiko-developer-functional-requirements-output #aiko-developer-functional-requirements-text' ).text() ) {
			$( '#aiko-developer-loader-overlay' ).fadeIn();
			$( '#aiko-developer-rephrase-submit' ).data( 'type', 'without' );
			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'self_rephrase_functional_requirements',
					nonce: $( '#aiko_developer_nonce_field' ).val(),
					functional_requirements: $( '#aiko-developer-functional-requirements-output #aiko-developer-functional-requirements-text' ).text(),
					post_id: postID
				},
				success: function( response ) {
					if ( response.success ) {
						$( '#aiko-developer-rephrased-popup-overlay' ).fadeIn();
						$( '#aiko-developer-old-text' ).text( response.data.old );
						$( '#aiko-developer-rephrased-text' ).text( response.data.rephrased );
					} else {
						if ( response.data.code ) {
							$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
							$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( response.data.code ) + response.data.message );
						} else {
							$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
							$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( response.data ) );
						}
					}
					$( '#aiko-developer-loader-overlay' ).fadeOut();
				}
			});
		} else {
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "error-empty-fr-rephrase" ) );
		}
	});

	// Submit rephrased functional requirements
	$( '#aiko-developer-rephrase-submit' ).on( 'click', function() {
		$( '#aiko-developer-status' ).val( '3' );
		$( '#aiko-developer-loader-overlay' ).fadeIn();
		$( '#aiko-developer-rephrased-popup-overlay' ).fadeOut();
		$( '#aiko-developer-rephrase-submit' ).data( 'type', '' );
	});

	// Undo rephrase of functional requirements
	$( '#aiko-developer-rephrase-undo' ).on( 'click', function() {
		event.preventDefault();
		$( '#aiko-developer-loader-overlay' ).fadeIn();

		$.ajax({
			url: ajaxUrl,
			type: 'POST',
			data: {
				action: 'undo_rephrase',
				nonce: $( '#aiko_developer_nonce_field' ).val(),
				functional_requirements: $( '#aiko-developer-functional-requirements-output #aiko-developer-functional-requirements-text' ).text(),
				post_id: postID,
				old_code_not_generated: $( '#aiko-developer-old-code-not-generated' ).val()
			},
			success: function( response ) {
				$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
				$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( response.data ) );
				$( '#aiko-developer-rephrased-popup-overlay' ).fadeOut();
				$( '#aiko-developer-loader-overlay' ).fadeOut();
				$( '#aiko-developer-comment-not-added' ).hide();
				$( '#aiko-developer-comment-not-added-text' ).text( '' );
			}
		});
	});

	// Rephrase user prompt
	$( '#aiko-developer-user-prompt-rephrase' ).on( 'click', function() {
		event.preventDefault();

		if ( apiKey === '' ) {
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "error-no-api-key" ) );
			$( '#aiko-developer-alert-ok' ).data( 'action', 'openai-api-url' );
			return;
		}

		if ( $( '#aiko-developer-input' ).val() ) {
			$( '#aiko-developer-loader-overlay' ).fadeIn();

			$.ajax({
				url: ajaxUrl,
				type: 'POST',
				data: {
					action: 'rephrase_user_prompt',
					user_prompt: $( '#aiko-developer-input' ).val(),
					nonce: $( '#aiko_developer_nonce_field' ).val(),
					post_id: postID
				},
				success: function( response ) {
					if ( response.success ) {
						$( '#aiko-developer-rephrased-user-prompt-popup-overlay' ).fadeIn();
						$( '#aiko-developer-old-user-prompt-text' ).text( response.data.old );
						$( '#aiko-developer-rephrased-user-prompt-text' ).text( response.data.rephrased );
					} else {
						if ( response.data.code ) {
							$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
							$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( response.data.code ) + response.data.message );
						} else {
							$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
							$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( response.data ) );
							
						}
						$( '#aiko-developer-rephrase-comments-error-text' ).text( aiko_developer_get_message( 'error-rephrase' ) );
					}
					$( '#aiko-developer-loader-overlay' ).fadeOut();
				}
			});
		} else {
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "error-empty-comment-rephrase" ) );
			$( '#aiko-developer-alert-ok' ).data( 'action', 'error-empty-comment-rephrase' );
			$( '#aiko-developer-input' ).focus();
		}
	});

	// Submit rephrase of user prompt
	$( '#aiko-developer-rephrase-user-prompt-submit' ).on( 'click', function() {
		event.preventDefault();
		$( '#aiko-developer-rephrased-user-prompt-popup-overlay' ).fadeOut();
		$( '#aiko-developer-input' ).val( $( '#aiko-developer-rephrased-user-prompt-text' ).text().trim() );
		var code_not_generated_is_shown = $( '#aiko-developer-code-not-generated-wrapper' ).hasClass( 'aiko-developer-notice-show' );
		$( '.aiko-developer-notice-show' ).removeClass( 'aiko-developer-notice-show' );
		if ( code_not_generated_is_shown ) {
			$( '#aiko-developer-code-not-generated-wrapper' ).addClass( 'aiko-developer-notice-show' );
		}
		var api_not_present_is_shown = $( '#aiko-developer-api-not-present-wrapper' ).hasClass( 'aiko-developer-notice-show' );
		if ( api_not_present_is_shown ) {
			$( '#aiko-developer-api-not-present-wrapper' ).addClass( 'aiko-developer-notice-show' );
		}
		$( '#aiko-developer-rephrase-comments-notice' ).addClass( 'aiko-developer-notice-show' );
	});

	// Undo rephrase of user prompt
	$( '#aiko-developer-rephrase-user-prompt-undo' ).on( 'click', function() {
		event.preventDefault();
		$( '#aiko-developer-rephrased-user-prompt-popup-overlay' ).fadeOut();
	});

	// Copy code
	$( '.aiko-developer-copy-code' ).on( 'click', function() {
		event.preventDefault();

		var code = $( '#aiko-developer-' + $( this ).data( 'type' ) + '-output-meta-box pre' ).text();
		if ( code.trim() !== '' ) {
			copyToClipboard( code );
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "success-copy" ) );
		} else {
			$( '#aiko-developer-alert-popup-overlay' ).fadeIn();
			$( '#aiko-developer-alert-text' ).text( aiko_developer_get_message( "error-empty-copy" ) );
		}
	});

	// Copy function
	function copyToClipboard( text ) {
		if ( navigator.clipboard ) {
			navigator.clipboard.writeText( text ).then( function() {
				console.log( 'Text copied to clipboard' );
			}).catch( function( err ) {
				console.error( 'Error copying text: ', err );
			});
		} else {
			fallbackCopyToClipboard( text );
		}
	}

	function fallbackCopyToClipboard( text ) {
		var textArea = document.createElement( "textarea" );
		textArea.value = text;

		textArea.style.top = "0";
		textArea.style.left = "0";
		textArea.style.position = "fixed";

		document.body.appendChild( textArea );
		textArea.focus();
		textArea.select();

		try {
			var successful = document.execCommand( 'copy' );
			var msg = successful ? 'successful' : 'unsuccessful';
			console.log( 'Fallback: Copying text command was ' + msg );
		} catch ( err ) {
			console.error( 'Fallback: Oops, unable to copy', err );
		}

		document.body.removeChild( textArea );
	}

	// Start the WordPress Playground test of generated plugin
	$( '.aiko-developer-test-start' ).on( 'click', function() {
		event.preventDefault();
		
		var php = $( '#aiko-developer-php-output-meta-box pre' ).text().replace(/\\/g, '\\\\').replace(/"/g, '\\"').replace(/\n/g, '\\n');
		var js = $( '#aiko-developer-js-output-meta-box pre' ).text().replace(/\\/g, '\\\\').replace(/"/g, '\\"').replace(/\n/g, '\\n');
		var css = $( '#aiko-developer-css-output-meta-box pre' ).text().replace(/\\/g, '\\\\').replace(/"/g, '\\"').replace(/\n/g, '\\n');
		var bluePrint = btoa( '{ "preferredVersions": { "php": "latest", "wp": "latest" }, "landingPage": "/wp-admin/plugins.php", "phpExtensionBundles": [ "kitchen-sink" ], "features": { "networking": true }, "steps": [ { "step": "login", "username": "admin", "password": "password" }, { "step": "mkdir", "path": "/wordpress/wp-content/plugins/my-plugin" }, { "step": "writeFile", "path": "/wordpress/wp-content/plugins/my-plugin/plugin-file.php", "data": "' + php + '" }, { "step": "writeFile", "path": "/wordpress/wp-content/plugins/my-plugin/plugin-scripts.js", "data": "' + js + '" }, { "step": "writeFile", "path": "/wordpress/wp-content/plugins/my-plugin/plugin-styles.css", "data": "' + css + '" }, { "step": "activatePlugin", "pluginPath": "/wordpress/wp-content/plugins/my-plugin" } ] }' );
		var url = "https://playground.wordpress.net/#" + bluePrint;
		window.open( url, '_blank', 'noopener, noreferrer' );
	});
});