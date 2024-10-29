<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Aiko_Developer_Core_Framework' ) ) {
	require_once plugin_dir_path( __DIR__ ) . '/framework/includes/class-aiko-developer-core-framework.php';
}

class Aiko_Developer_Core_Lite extends Aiko_Developer_Core_Framework {
	private function aiko_developer_save_generate( $post_id ) {
		if ( empty( get_post_meta( $post_id, '_generation', true ) ) ) {
			update_post_meta( $post_id, '_generation', array( true, 'created', '' ) );
		}

		if ( ! isset( $_POST['aiko_developer_nonce_field'] ) || ! wp_verify_nonce( sanitize_text_field( wp_unslash( $_POST['aiko_developer_nonce_field'] ) ), 'aiko_developer_nonce' )
		|| ! $this->get_aiko_developer_is_user_admin() || 'aiko_developer' !== get_post_type( $post_id ) ) {
			$generation = get_post_meta( $post_id, '_generation', true );
			if ( 'created' !== $generation[1] && 'revision_restored' !== $generation[1] ) {
				update_post_meta( $post_id, '_generation', array( false, 'error-unauthorized-access', '' ) );
			}
			return;
		}

		if ( ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) ) {
			update_post_meta( $post_id, '_generation', array( true, 'no_prompt', '' ) );
			return;
		}

		if ( isset( $_GET['action'] ) && 'restore' === $_GET['action'] ) {
			update_post_meta( $parent_post_id, '_generation', array( true, 'revision_restored', '' ) );
			return;
		}

		$post = get_post( $post_id );
		if ( isset( $_POST['aiko-developer-post-slug'] ) && '' !== sanitize_title( wp_unslash( $_POST['aiko-developer-post-slug'] ) ) && sanitize_title( wp_unslash( $_POST['aiko-developer-post-slug'] ) ) !== $post->post_name ) {
			remove_action( 'save_post_aiko_developer', array( $this, 'get_aiko_developer_save_generate' ) );

			$post_data = array(
				'ID'        => $post_id,
				'post_name' => sanitize_title( wp_unslash( $_POST['aiko-developer-post-slug'] ) ),
			);
			wp_update_post( $post_data );
			update_post_meta( $post_id, '_post_slug', sanitize_title( wp_unslash( $_POST['aiko-developer-post-slug'] ) ) );

			add_action( 'save_post_aiko_developer', array( $this, 'get_aiko_developer_save_generate' ) );
		}

		$status = isset( $_POST['aiko-developer-status'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['aiko-developer-status'] ) ) ) : 0;
		// 0 - Code can be generated
		// 1 - Code is edited, no generation
		// 2 - FR are edited, no generation
		// 3 - FR are rephrased, no generation
		// 4 - Refresh after API key, no generation
		// 5 - Code edit failed, no generation
		if ( 0 !== $status ) {
			update_post_meta( $post_id, '_generation', array( true, 'status', $status ) );
			return;
		} else {
			update_post_meta( $post_id, '_can_generate', 1 );
		}

		$can_generate = intval( get_post_meta( $post_id, '_can_generate', true ) );
		$revisions    = wp_get_post_revisions( $post_id );
		$last         = reset( $revisions );
		if ( false !== $last ) {
			$old_functional_requirements = get_post_meta( $last->ID, '_functional_requirements', true );
		} else {
			$old_functional_requirements = 'empty';
		}
		$current_functional_requirements = get_post_meta( $post_id, '_functional_requirements', true );
		$is_first                        = isset( $_POST['aiko-developer-first'] ) ? intval( sanitize_text_field( wp_unslash( $_POST['aiko-developer-first'] ) ) ) : 0;
		if ( $is_first && isset( $_POST['aiko-developer-input'] ) && $this->get_aiko_developer_sanitize_from_post( $_POST, 'aiko-developer-input' ) === '' ) {
			return;
		}
		if ( ( ! empty( $can_generate ) && 1 === $can_generate ) || $is_first ) {
			$improvement = isset( $_POST['aiko-developer-input'] ) ? $this->get_aiko_developer_sanitize_from_post( $_POST, 'aiko-developer-input' ) : '';
			$flag        = get_post_meta( $post_id, '_flag', true );

			if ( empty( $flag ) && empty( $current_functional_requirements ) && ! empty( $improvement ) ) {
				update_post_meta( $post_id, '_functional_requirements', $improvement );
				$current_functional_requirements = $improvement;
				update_post_meta( $post_id, '_flag', 1 );
			} else {
				$improvement = get_post_meta( $post_id, '_improvements', true );
			}

			$url     = 'https://api.openai.com/v1/chat/completions';
			$api_key = get_option( 'aiko_developer_api_key', '' );

			$model   = get_option( 'aiko_developer_model', 'gpt-4o' );
			$model   = $this->get_aiko_developer_old_model_fallback( $model, 'developer' );
			$o1_flag = 'o1-preview' === $model || 'o1-mini' === $model;

			$post_title = get_the_title( $post_id );

			$prompts_json = file_get_contents( plugin_dir_path( __DIR__ ) . 'framework/json/prompts.json' );
			$prompts      = json_decode( $prompts_json, true );

			$developer_prompt_base               = $prompts['developer']['base'];
			$developer_prompt_instructions_first = $prompts['developer']['instructions-first'];
			$developer_prompt_requirements       = str_replace( '[plugin name]', $post_title, $prompts['developer']['requirements'] );
			$developer_prompt_file_format        = $prompts['developer']['file-format'];

			$code_fixer_prompt_base         = $prompts['code-fixer']['base'];
			$code_fixer_prompt_requirements = str_replace( '[plugin name]', $post_title, $prompts['code-fixer']['requirements'] );
			$code_fixer_prompt_elementor    = $prompts['code-fixer']['additional-requirements']['elementor'];
			$code_fixer_prompt_woocommerce  = $prompts['code-fixer']['additional-requirements']['woocommerce'];

			$developer_message  = $developer_prompt_base . $developer_prompt_requirements;
			$code_fixer_message = $code_fixer_prompt_base . $code_fixer_prompt_requirements;

			if ( str_contains( strtolower( $current_functional_requirements ), 'elementor' ) ) {
				$code_fixer_message = $code_fixer_message . "\n" . $code_fixer_prompt_elementor;
			}
			
			if ( str_contains( strtolower( $current_functional_requirements ), 'woocommerce' ) ) {
				$code_fixer_message = $code_fixer_message . "\n" . $code_fixer_prompt_woocommerce;
			}

			$messages = array(
				array(
					'role'    => $o1_developer_flag ? 'user' : 'system',
					'content' => $developer_message,
				),
			);

			$code_fixer_messages = array( 
				array(
					'role'    => $o1_flag ? 'user' : 'system',
					'content' => $code_fixer_message,
				),
			);

			if ( ! empty( $flag ) ) {
				$current_php  = get_post_meta( $post_id, '_php_output', true );
				$current_js   = get_post_meta( $post_id, '_js_output', true );
				$current_css  = get_post_meta( $post_id, '_css_output', true );
				$current_code = "```php\n" . esc_html( $current_php ) . "\n```\n```js\n" . esc_html( $current_js ) . "\n```\n```css\n" . esc_html( $current_css ) . "\n```\n";
				
				$messages[] = array(
					'role'    => 'user',
					'content' => "Old Functional Requirements:\n" . $old_functional_requirements . "\nCurrent Functional Requirements:\n" . $current_functional_requirements . "\nExisting Plugin Code:\n" . $current_code,
				);
			} else {
				$messages[] = array(
					'role'    => 'user',
					'content' => "Functional Requirements:\n" . $current_functional_requirements,
				);
			}

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
						'temperature'                                     => $o1_flag ? 1 : 0,
						$o1_flag ? 'max_completion_tokens' : 'max_tokens' => $o1_flag ? 16384 : 4096,
						'top_p'                                           => 1,
						'frequency_penalty'                               => 0,
						'presence_penalty'                                => 0,
					)
				),
			);

			$response = wp_remote_post( $url, $args );

			if ( is_wp_error( $response ) ) {
				update_post_meta( $post_id, '_generation', array( false, 'error-openai-unable-to-connect', '' ) );
				return;
			} else {
				$body = wp_remote_retrieve_body( $response );
				$data = json_decode( $body, true );

				if ( isset( $data['error'] ) ) {
					update_post_meta( $post_id, '_generation', array( false, 'error-openai', $data['error']['message'] ) );
					return;
				} else {
					$generated_code         = $data['choices'][0]['message']['content'];
					$escaped_generated_code = str_replace( '\\', '\\\\', $generated_code );

					update_post_meta( $post_id, '_functional_requirements', empty( $flag ) ? $improvement : $current_functional_requirements );

					$functional_requirements = empty( $flag ) ? $improvement : $current_functional_requirements;

					$code_fixer_messages[] = array(
						'role' => 'user',
						'content' => "Functional Requirements:\n" . $functional_requirements . "\n\n" . $escaped_generated_code,
					);

					$code_fixer_args = array(
						'timeout' => 200,
						'headers' => array(
							'Content-Type'  => 'application/json',
							'Authorization' => 'Bearer ' . $api_key,
						),
						'body'    => wp_json_encode(
							array(
								'model'                                           => $model,
								'messages'                                        => $code_fixer_messages,
								'temperature'                                     => $o1_flag ? 1 : 0,
								$o1_flag ? 'max_completion_tokens' : 'max_tokens' => $o1_flag ? 16384 : 4096,
								'top_p'                                           => 1,
								'frequency_penalty'                               => 0,
								'presence_penalty'                                => 0,
							)
						),
					);

					$code_fixer_response = wp_remote_post( $url, $code_fixer_args );

					if ( is_wp_error( $code_fixer_response ) ) {
						update_post_meta( $post_id, '_generation', array( false, 'error-openai-unable-to-connect', '' ) );
						return;
					} else {
						$code_fixer_body = wp_remote_retrieve_body( $code_fixer_response );
						$code_fixer_data = json_decode( $code_fixer_body, true );
		
						if ( isset( $code_fixer_data['error'] ) ) {
							update_post_meta( $post_id, '_generation', array( false, 'error-openai', $code_fixer_data['error']['message'] ) );
							return;
						} else {
							$fixed_code         = $code_fixer_data['choices'][0]['message']['content'];
							$escaped_fixed_code = str_replace( '\\', '\\\\', $fixed_code );

							update_post_meta( $post_id, '_php_output', "<?php\n" . $this->get_aiko_developer_extract_code( $escaped_fixed_code, '<?php', '```' ) );
							update_post_meta( $post_id, '_js_output', $this->get_aiko_developer_extract_code( $escaped_fixed_code, '```js', '```' ) );
							update_post_meta( $post_id, '_css_output', $this->get_aiko_developer_extract_code( $escaped_fixed_code, '```css', '```' ) );
							update_post_meta( $post_id, '_generation', array( true, 'success-generate', '' ) );
							update_post_meta( $post_id, '_can_generate', 0 );
							update_post_meta( $post_id, '_code_not_generated', false );
						}
					}
				}
			}
		} else {
			update_post_meta( $post_id, '_generation', array( true, 'no_prompt', '' ) );
			return;
		}
	}

	public function get_aiko_developer_save_generate( $post_id ) {
		$this->aiko_developer_save_generate( $post_id );
	}
}
