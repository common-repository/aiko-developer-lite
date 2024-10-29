<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class Aiko_Developer_Core_Framework {
	private function aiko_developer_enqueue_scripts() {
		global $typenow;

		$screen = get_current_screen();
		$page   = preg_replace( '/.*page_/', '', $screen->id );

		if ( 'aiko_developer' === $typenow || 'aiko-developer-home' === $page || 'aiko-developer-settings' === $page ) {
			wp_enqueue_style( 'aiko-developer-style', plugin_dir_url( __DIR__ ) . 'css/style.css', array(), filemtime( plugin_dir_path( dirname( __DIR__ ) ) . 'framework/css/style.css' ) );
			wp_enqueue_script( 'aiko-developer-script', plugin_dir_url( dirname( __DIR__ ) ) . 'assets/js/script.js', array( 'jquery' ), filemtime( plugin_dir_path( dirname( __DIR__ ) ) . 'assets/js/script.js' ), true );
			wp_localize_script(
				'aiko-developer-script',
				'aiko_developer_object',
				array(
					'ajax_url'       => admin_url( 'admin-ajax.php' ),
					'api_key'        => get_option( 'aiko_developer_api_key', '' ),
					'link'           => admin_url( 'admin.php?page=aiko-developer-settings' ),
					'pluginMessages' => $this->get_aiko_developer_messages_localize(),
				)
			);
		}
	}

	private function aiko_developer_activate() {
		update_option( 'aiko_developer_activated', true );
	}

	public function get_aiko_developer_activate() {
		$this->aiko_developer_activate();
	}

	public function get_aiko_developer_enqueue_scripts() {
		$this->aiko_developer_enqueue_scripts();
	}

	private function aiko_developer_is_user_admin() {
		if ( function_exists( 'wp_get_current_user' ) ) {
			$user          = wp_get_current_user();
			$is_user_admin = in_array( 'administrator', $user->roles );
		} else { 
			/** 
			 * Oxygen Builder is causing problems, so checking is the user Administrator is disabled
			 * if the function wp_get_current_user() is not defined.
			 * Non-Administrator users can access the Instant Plugins page only with URL. 
			*/
			$is_user_admin = true;
		}

		return $is_user_admin;
	}

	public function get_aiko_developer_is_user_admin() {
		return $this->aiko_developer_is_user_admin();
	}

	private function aiko_developer_redirect_after_activation() {
		if ( get_option( 'aiko_developer_activated', false ) ) {
			delete_option( 'aiko_developer_activated' );

			wp_safe_redirect( admin_url( 'admin.php?page=aiko-developer-home' ) );
			exit;
		}
	}

	public function get_aiko_developer_redirect_after_activation() {
		$this->aiko_developer_redirect_after_activation();
	}

	private function aiko_developer_normalize_array_structure( $array ) {
		if ( is_array( $array ) && count( $array ) === 1 && is_array( $array[0] ) ) {
			return $array[0];
		}
		return $array;
	}

	public function get_aiko_developer_normalize_array_structure( $array ) {
		return $this->aiko_developer_normalize_array_structure( $array );
	}

	private function aiko_developer_load_textdomain() {
		load_plugin_textdomain( 'aiko-developer-lite', false, dirname( plugin_basename( dirname( __DIR__ ) ) ) . '/languages' );
	}

	public function get_aiko_developer_load_textdomain() {
		$this->aiko_developer_load_textdomain();
	}

	public function aiko_developer_messages_localize() {
		$messages = array();

		$messages['error-general']                  = esc_html__( 'Error: There was an error.', 'aiko-developer-lite' );
		$messages['error-unauthorized-access']      = esc_html__( 'Error: Unauthorized access.', 'aiko-developer-lite' );
		$messages['error-isset-post']               = esc_html__( 'Error: Invalid data.', 'aiko-developer-lite' );
		$messages['error-openai-unable-to-connect'] = esc_html__( 'Error: Unable to connect to OpenAI API; please try again.', 'aiko-developer-lite' );
		$messages['error-openai']                   = esc_html__( 'OpenAI Error: ', 'aiko-developer-lite' );
		$messages['error-save-loopback']            = esc_html__( 'Error: We were unable to check for errors, your plugin is not activated or saved. You can try again or download the ZIP and try to activate it manually.', 'aiko-developer-lite' );
		$messages['error-unable-to-save']           = esc_html__( 'Error: There might be an error in your plugin, it is not activated or saved.', 'aiko-developer-lite' );
		$messages['error-save-php']                 = esc_html__( 'Error: Plugin could not be activated, there is an error in PHP file.', 'aiko-developer-lite' );
		$messages['error-zip-fail']                 = esc_html__( 'Error: Failed to create zip file', 'aiko-developer-lite' );
		$messages['error-restricted-code']          = esc_html__( 'Error: Restricted code detected', 'aiko-developer-lite' );
		$messages['error-no-title']                 = esc_html__( 'Error: Plugin title is required.', 'aiko-developer-lite' );
		$messages['error-no-slug']                  = esc_html__( 'Error: Plugin slug is required.', 'aiko-developer-lite' );
		$messages['error-no-api-key']               = esc_html__( 'Error: API key is required. Click OK to be redirected.', 'aiko-developer-lite' );
		$messages['error-empty-copy']               = esc_html__( 'Error: You cannot copy empty code.', 'aiko-developer-lite' );
		$messages['error-empty-edit']               = esc_html__( 'Error: You cannot submit empty Functional Requirements.', 'aiko-developer-lite' );
		$messages['error-empty-fr']                 = esc_html__( 'Error: Cannot save the plugin without Functional Requirements.', 'aiko-developer-lite' );
		$messages['error-empty-fr-rephrase']        = esc_html__( 'Error: The plugin cannot be saved without Functional Requirements', 'aiko-developer-lite' );
		$messages['error-empty-comment-rephrase']   = esc_html__( 'Error: There is no text that could be rephrased.', 'aiko-developer-lite' );
		$messages['error-no-improvements']          = esc_html__( 'No improvements have been selected.', 'aiko-developer-lite' );
		$messages['success-rephrase']               = esc_html__( 'We have rephrased your text. Press UPDATE to generate new code.', 'aiko-developer-lite' );
		$messages['success-rephrase-first']         = esc_html__( 'We have rephrased your text. Press PUBLISH to generate code.', 'aiko-developer-lite' );
		$messages['error-rephrase']                 = esc_html__( 'Error occured. We could not rephrase your text.', 'aiko-developer-lite' );
		$messages['success-add-and-rephrase']       = esc_html__( 'We have rephrased your text. Press UPDATE to generate new code.', 'aiko-developer-lite' );
		$messages['success-undo-rephrase']          = esc_html__( 'Undo successful. Rephrased Functional Requirements are not saved.', 'aiko-developer-lite' );
		$messages['success-edit-code']              = esc_html__( 'Code has beed saved.', 'aiko-developer-lite' );
		$messages['success-edit-fr']                = esc_html__( 'Functional Requirements are saved. Press UPDATE to generate new code.', 'aiko-developer-lite' );
		$messages['success-copy']                   = esc_html__( 'Code copied successfully.', 'aiko-developer-lite' );
		$messages['success-save']                   = esc_html__( 'Plugin is saved and activated successfully.', 'aiko-developer-lite' );
		$messages['success-refresh']                = esc_html__( 'Page refreshed successfully, now you can ask AI Developer to generate your code', 'aiko-developer-lite' );
		$messages['success-generate']               = esc_html__( 'New code is ready. Now you can test it and use it. If you are not satisfied you can revert to the previous version using revisions.', 'aiko-developer-lite' );
		$messages['notice-refresh-after-api-key']   = esc_html__( 'Please refresh the page after you have entered the API key.', 'aiko-developer-lite' );
		$messages['notice-apply-improvements']      = esc_html__( 'Improvement suggestions are ready. Now you can add them to Functional Requirements.', 'aiko-developer-lite' );
		$messages['notice-comment-not-added']       = esc_html__( 'There are some improvements which are not included in Functional Requirements, so we did it for you. If you accept we will generate the code.', 'aiko-developer-lite' );
		$messages['confirm-cancel-edit']            = esc_html__( 'Are you sure you want to cancel? Edits will not be saved.', 'aiko-developer-lite' );
		$messages['label-first']                    = esc_html__( 'Functional Requirements', 'aiko-developer-lite' );
		$messages['label-first-description']        = esc_html__( 'Write your initial idea and technical requirements. We highly recommend using the Rephrase option before publishing.', 'aiko-developer-lite' );
		$messages['label-after']                    = esc_html__( 'Improvements', 'aiko-developer-lite' );
		$messages['label-after-description']        = esc_html__( 'If you want to improve the Functional Requirements, write your idea.', 'aiko-developer-lite' );
		
		return $messages;
	}

	public function get_aiko_developer_messages_localize() {
		return $this->aiko_developer_messages_localize();
	}

	private function aiko_developer_extract_code( $data, $start_tag, $end_tag ) {
		$start_index = strpos( $data, $start_tag );
		$end_index   = strpos( $data, $end_tag, $start_index + strlen( $start_tag ) );

		if ( false !== $start_index && false !== $end_index ) {
			$code = substr( $data, $start_index + strlen( $start_tag ), $end_index - ( $start_index + strlen( $start_tag ) ) );
			$code = trim( $code );
			return $code;
		}

		return '';
	}

	public function get_aiko_developer_extract_code( $data, $start_tag, $end_tag ) {
		return $this->aiko_developer_extract_code( $data, $start_tag, $end_tag );
	}

	private function aiko_developer_array_flatten( $input ) {
		$result = array();

		if ( is_array( $input ) ) {
			foreach ( $input as $item ) {
				if ( is_array( $item ) ) {
					$result = array_merge( $result, $this->get_aiko_developer_array_flatten( $item ) );
				} elseif ( is_string( $item ) ) {
					$result[] = $item;
				}
			}
		} elseif ( is_string( $input ) ) {
			$result[] = $input;
		}

		return $result;
	}

	public function get_aiko_developer_array_flatten( $input ) {
		return $this->aiko_developer_array_flatten( $input );
	}

	private function aiko_developer_old_model_fallback( $model, $role ) {
		if ( 'gpt-3.5-turbo' === $model ) {
			if ( 'developer' === $role ) {
				update_option( 'aiko_developer_model', 'gpt-4o' );
				return 'gpt-4o';
			} elseif ( 'reviewer' === $role ) {
				update_option( 'aiko_developer_reviewer_model', 'gpt-4o' );
				return 'gpt-4o';
			} else {
				update_option( 'aiko_developer_consultant_model', 'gpt-4o-mini' );
				return 'gpt-4o-mini';
			}
		} else {
			return $model;
		}
	}

	public function get_aiko_developer_old_model_fallback( $model, $role ) {
		return $this->aiko_developer_old_model_fallback( $model, $role );
	}

	private function aiko_developer_is_code_not_allowed( $code ) {
		if ( preg_match_all( '/(base64_decode|error_reporting|ini_set|eval)\s*\(/i', $code, $matches ) ) {
			if ( count( $matches[0] ) > 5 ) {
				return true;
			}
		}
		if ( preg_match( '/dns_get_record/i', $code ) ) {
			return true;
		}

		return false;
	}

	public function get_aiko_developer_is_code_not_allowed( $code ) {
		return $this->aiko_developer_is_code_not_allowed( $code );
	}

	private function aiko_developer_sanitize_from_post( $post_array, $arg ) {
		/**
		 * $post_array is $_POST
		 * $arg is element in $_POST array
		 * esc_html before sanitize_textarea_field is used to preserve HTML and <?php tags inside the code
		 * If there is no esc_html the code would be stripped of HTML and <?php tags, and when user tries
		 * to Edit the code or Download as ZIP, the code wouldn't be full, and that functionality wouldn't work as expected
		 */
		return htmlspecialchars_decode( sanitize_textarea_field( esc_html( wp_unslash( $post_array[ $arg ] ) ) ), ENT_QUOTES );
	}

	public function get_aiko_developer_sanitize_from_post( $post_array, $arg ) {
		return $this->aiko_developer_sanitize_from_post( $post_array, $arg );
	}
}
