<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( dirname( __DIR__ ) ) . 'includes/class-aiko-developer-core.php';

class Aiko_Developer_Ajax_Framework {
	public function __construct() {
		if ( class_exists( 'Aiko_Developer_Core_Lite' ) ) {
			$this->core = new Aiko_Developer_Core_Lite();
		} elseif ( class_exists( 'Aiko_Developer_Core' ) ) {
			$this->core = new Aiko_Developer_Core();
		}
	}

	public function aiko_developer_handle_download_zip() {
		if ( isset( $_POST['php_code'], $_POST['js_code'], $_POST['css_code'], $_POST['post_id'] ) ) {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'aiko_developer_nonce' )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! $this->core->get_aiko_developer_is_user_admin() ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			$php_code  = $this->core->get_aiko_developer_sanitize_from_post( $_POST, 'php_code' );
			if ( $this->core->get_aiko_developer_is_code_not_allowed( $php_code ) ) {
				wp_send_json_error( 'error-restricted-code' );
			}
			$js_code   = $this->core->get_aiko_developer_sanitize_from_post( $_POST, 'js_code' );
			$css_code  = $this->core->get_aiko_developer_sanitize_from_post( $_POST, 'css_code' );
			$post_id   = intval( sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) );
			$post      = get_post( $post_id );
			$post_slug = sanitize_title( $post->post_name );

			$upload_dir = wp_upload_dir();
			$zip_path   = $upload_dir['path'] . '/' . $post_slug . '.zip';
			$zip_url    = $upload_dir['url'] . '/' . $post_slug . '.zip';

			if ( wp_parse_url( home_url(), PHP_URL_SCHEME ) !== wp_parse_url( $zip_url, PHP_URL_SCHEME ) ) {
				$zip_url = str_replace( wp_parse_url( $zip_url, PHP_URL_SCHEME ) . '://', wp_parse_url( home_url(), PHP_URL_SCHEME ) . '://', $zip_url );
			}

			$php_file = $upload_dir['path'] . '/plugin-file.php';
			$js_file  = $upload_dir['path'] . '/plugin-scripts.js';
			$css_file = $upload_dir['path'] . '/plugin-styles.css';

			global $wp_filesystem;

			if ( empty( $wp_filesystem ) ) {
				WP_Filesystem();
			}

			$wp_filesystem->put_contents( $php_file, $php_code, FS_CHMOD_FILE );
			$wp_filesystem->put_contents( $js_file, $js_code, FS_CHMOD_FILE );
			$wp_filesystem->put_contents( $css_file, $css_code, FS_CHMOD_FILE );

			$zip = new ZipArchive();
			if ( true === $zip->open( $zip_path, ZipArchive::CREATE ) ) {
				$zip->addFile( $php_file, 'plugin-file.php' );
				$zip->addFile( $js_file, 'plugin-scripts.js' );
				$zip->addFile( $css_file, 'plugin-styles.css' );
				$zip->close();

				wp_delete_file( $php_file );
				wp_delete_file( $js_file );
				wp_delete_file( $css_file );

				wp_send_json_success( $zip_url );
			} else {
				wp_send_json_error( 'error-zip-fail' );
			}
		} else {
			wp_send_json_error( 'error-isset-post' );
		}

		wp_die();
	}

	public function aiko_developer_handle_edit() {
		if ( isset( $_POST['edited'], $_POST['type'], $_POST['post_id'] ) ) {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'aiko_developer_nonce' )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! $this->core->get_aiko_developer_is_user_admin() ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			$type    = sanitize_text_field( wp_unslash( $_POST['type'] ) );
			$post_id = intval( sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) );

			if ( 'functional-requirements' !== $type ) {
				$edited	= $this->core->get_aiko_developer_sanitize_from_post( $_POST, 'edited' );
				$edited = str_replace( '\\', '\\\\', $edited );
				if ( 'php' === $type ) {
					if ( $this->core->get_aiko_developer_is_code_not_allowed( $edited ) ) {
						wp_send_json_error( 'error-restricted-code' );
					}
				}
				update_post_meta( $post_id, '_' . $type . '_output', $edited );
			} else {
				$edited = $this->core->get_aiko_developer_sanitize_from_post( $_POST, 'edited' );
				$edited = str_replace( '\\', '\\\\', $edited );
				update_post_meta( $post_id, '_functional_requirements', $edited );
				update_post_meta( $post_id, '_improvements', 'Functional Requirements were manualy edited.' );
				update_post_meta( $post_id, '_code_not_generated', true );
			}

			wp_send_json_success( 'success-edit' );
		} else {
			wp_send_json_error( 'error-isset-post' );
		}

		wp_die();
	}

	public function aiko_developer_handle_rephrase_user_prompt() {
		if ( isset( $_POST['user_prompt'], $_POST['post_id'] ) ) {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'aiko_developer_nonce' )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! $this->core->get_aiko_developer_is_user_admin() ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			$url                    = 'https://api.openai.com/v1/chat/completions';
			$api_key                = get_option( 'aiko_developer_api_key', '' );
			$model                  = get_option( 'aiko_developer_consultant_model', 'gpt-4o-mini' );
			$model                  = $this->core->get_aiko_developer_old_model_fallback( $model, 'consultant' );
			$prompts_json           = file_get_contents( plugin_dir_path( __DIR__ ) . 'json/prompts.json' );
			$prompts                = json_decode( $prompts_json, true );
			$consultant_message     = $prompts['consultant']['only-rephrase'] . $prompts['consultant']['only-rephrase-important-notes'] . $prompts['consultant']['only-rephrase-file-format'];
			$post_id                = intval( sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) );
			$consultant_temperature = floatval( get_option( 'aiko_developer_consultant_temperature', '0.1' ) );

			$o1_flag = 'o1-preview' === $model || 'o1-mini' === $model;

			$messages = array(
				array(
					'role'    => $o1_flag ? 'user' : 'system',
					'content' => $consultant_message,
				),
			);

			$user_prompt = sanitize_textarea_field( wp_unslash( $_POST['user_prompt'] ) );

			$messages[] = array(
				'role'    => 'user',
				'content' => $user_prompt,
			);

			$args = array(
				'timeout' => 200,
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				),
				'body'    => wp_json_encode(
					array(
						'model'                                           => $model,
						'messages'                                        => $messages,
						'temperature'                                     => $o1_flag ? 1 : $consultant_temperature,
						$o1_flag ? 'max_completion_tokens' : 'max_tokens' => $o1_flag ? 16384 : 4096,
						'top_p'                                           => 1,
						'frequency_penalty'                               => 0,
						'presence_penalty'                                => 0,
					)
				),
			);

			$response = wp_remote_post( $url, $args );

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( 'error-openai-unable-to-connect' );
			} else {
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );

				if ( isset( $data['error'] ) ) {
					wp_send_json_error(
						array(
							'code'    => 'error-openai',
							'message' => $data['error']['message'],
						)
					);
				} else {
					$rephrased = stripslashes( $this->core->get_aiko_developer_extract_code( $data['choices'][0]['message']['content'], '```functional_requirements', '```' ) );
					wp_send_json_success(
						array(
							'code'      => 'success-rephrase-first',
							'rephrased' => $rephrased,
							'old'       => stripslashes( $user_prompt ),
						)
					);
				}
			}
		} else {
			wp_send_json_error( 'error-isset-post' );
		}
		wp_die();
	}

	public function aiko_developer_handle_self_rephrase_functional_requirements() {
		if ( isset( $_POST['functional_requirements'], $_POST['post_id'] ) ) {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'aiko_developer_nonce' )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! $this->core->get_aiko_developer_is_user_admin() ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			$url                    = 'https://api.openai.com/v1/chat/completions';
			$api_key                = get_option( 'aiko_developer_api_key', '' );
			$model                  = get_option( 'aiko_developer_consultant_model', 'gpt-4o-mini' );
			$model                  = $this->core->get_aiko_developer_old_model_fallback( $model, 'consultant' );
			$prompts_json           = file_get_contents( plugin_dir_path( __DIR__ ) . 'json/prompts.json' );
			$prompts                = json_decode( $prompts_json, true );
			$consultant_message     = $prompts['consultant']['only-rephrase'] . $prompts['consultant']['only-rephrase-important-notes'] . $prompts['consultant']['only-rephrase-file-format'];
			$post_id                = intval( sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) );
			$consultant_temperature = floatval( get_option( 'aiko_developer_consultant_temperature', '0.1' ) ); 

			$o1_flag = 'o1-preview' === $model || 'o1-mini' === $model;

			$messages = array(
				array(
					'role'    => $o1_flag ? 'user' : 'system',
					'content' => $consultant_message,
				),
			);

			$functional_requirements = sanitize_textarea_field( wp_unslash( $_POST['functional_requirements'] ) );

			$messages[] = array(
				'role'    => 'user',
				'content' => $functional_requirements,
			);

			$args = array(
				'timeout' => 200,
				'headers' => array(
					'Content-Type'  => 'application/json',
					'Authorization' => 'Bearer ' . $api_key,
				),
				'body'    => wp_json_encode(
					array(
						'model'                                           => $model,
						'messages'                                        => $messages,
						'temperature'                                     => $o1_flag ? 1 : $consultant_temperature,
						$o1_flag ? 'max_completion_tokens' : 'max_tokens' => $o1_flag ? 16384 : 4096,
						'top_p'                                           => 1,
						'frequency_penalty'                               => 0,
						'presence_penalty'                                => 0,
					)
				),
			);

			$response = wp_remote_post( $url, $args );

			if ( is_wp_error( $response ) ) {
				wp_send_json_error( 'error-openai-unable-to-connect' );
			} else {
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );

				if ( isset( $data['error'] ) ) {
					wp_send_json_error(
						array(
							'code'    => 'error-openai',
							'message' => $data['error']['message'],
						)
					);
				} else {
					$rephrased = stripslashes( $this->core->get_aiko_developer_extract_code( $data['choices'][0]['message']['content'], '```functional_requirements', '```' ) );
					update_post_meta( $post_id, '_functional_requirements', $rephrased );
					wp_send_json_success(
						array(
							'code'      => 'success-rephrase',
							'rephrased' => $rephrased,
							'old'       => stripslashes( $functional_requirements ),
						)
					);
				}
			}
		} else {
			wp_send_json_error( 'error-isset-post' );
		}
		wp_die();
	}

	public function aiko_developer_handle_undo_rephrase() {
		if ( isset( $_POST['functional_requirements'], $_POST['post_id'], $_POST['old_code_not_generated'] ) ) {
			if ( ! isset( $_POST['nonce'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['nonce'] ) ), 'aiko_developer_nonce' )
			|| ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) || ! $this->core->get_aiko_developer_is_user_admin() ) {
				wp_send_json_error( 'error-unauthorized-access' );
				wp_die();
			}

			$post_id                 = intval( sanitize_text_field( wp_unslash( $_POST['post_id'] ) ) );
			$functional_requirements = sanitize_textarea_field( wp_unslash( $_POST['functional_requirements'] ) );
			$old_code_not_generated  = sanitize_text_field( wp_unslash( $_POST['old_code_not_generated'] ) );

			update_post_meta( $post_id, '_functional_requirements', $functional_requirements );
			update_post_meta( $post_id, '_code_not_generated', ! empty( $old_code_not_generated ) ? true : false );
			wp_send_json_success( 'success-undo-rephrase' );
		} else {
			wp_send_json_error( 'error-isset-post' );
		}
		wp_die();
	}
}
