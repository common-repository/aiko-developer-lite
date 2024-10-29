<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Aiko_Developer_Render_Framework' ) ) {
require_once plugin_dir_path( __DIR__ ) . '/framework/includes/class-aiko-developer-render-framework.php';
}

class Aiko_Developer_Render_Lite extends Aiko_Developer_Render_Framework {
    private function aiko_developer_render_download_buttons() {
		?>
		<div id="aiko-developer-download">
			<div class="aiko-developer-button-container">
				<button class="button button-secondary button-large aiko-developer-test-start"><?php echo esc_html__( 'Test plugin', 'aiko-developer-lite' ); ?></button>
				<div class="aiko-developer-tooltip-container aiko-developer-tooltip-arrow-right">
					<i class="dashicons dashicons-info aiko-developer-download-info" aria-hidden="true"></i>
					<div class="aiko-developer-tooltip-text"><?php echo esc_html__( 'Plugin testing on the WordPress playground, an independent service, without the risk of crashing the site.', 'aiko-developer-lite' ); ?></div>
				</div>
			</div>	
			<div class="aiko-developer-button-container">		
				<button class="button button-secondary button-large aiko-developer-download-zip"><?php echo esc_html__( 'Download ZIP', 'aiko-developer-lite' ); ?></button>
				<div class="aiko-developer-tooltip-container aiko-developer-tooltip-arrow-right">
					<i class="dashicons dashicons-info aiko-developer-download-info" aria-hidden="true"></i>
					<div class="aiko-developer-tooltip-text"><?php echo esc_html__( 'Download the zip and manually install the plugin.', 'aiko-developer-lite' ); ?></div>
				</div>
			</div>
		</div>
		<?php
	}

	public function get_aiko_developer_render_download_buttons() {
		$this->aiko_developer_render_download_buttons();
	}

	private function aiko_developer_render_settings_page() {
		$api_key = get_option( 'aiko_developer_api_key', '' );
		?>
		<div class="wrap">
			<?php if ( empty( $api_key ) ) { ?>
			<div id="aiko-developer-api-not-present-wrapper" class="aiko-developer-notice aiko-developer-notice-show aiko-developer-notice-error">
				<div class="aiko-developer-notice-content">
					<p id="aiko-developer-api-not-present"><?php echo esc_html__( 'You must have ', 'aiko-developer-lite' ); ?><a href="https://platform.openai.com/api-keys" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'OpenAI API key', 'aiko-developer-lite' ); ?></a><?php echo esc_html__( ' if you want to use our plugin. ', 'aiko-developer-lite' ); ?></p>
				</div>
			</div>
			<?php } ?>
			<form method="post" action="options.php">
				<h1>AIKO Settings</h1>
				<div id="aiko-developer-buy-full-wrapper" class="aiko-developer-block aiko-developer-buy-full-settings">
					<h2 id="aiko-developer-buy-full-title"><?php echo esc_html__( 'There is a Pro version of this plugin!', 'aiko-developer-lite' ); ?></h2>
					<p id="aiko-developer-buy-full-description"><?php echo esc_html__( 'The Pro version of AIKO Developer Lite provides advanced features, such as: temperature settings for all models, easy extension of functional requirements, code review and improvement suggestions, automatic deployment, WordPress Playground testing options (default plugins and themes, import content) and many more. ', 'aiko-developer-lite' ); ?> </p><p id="aiko-developer-buy-full-call-to action"><a href="<?php echo esc_url( 'https://codecanyon.net/item/aiko-instant-plugins-ai-developer/54220020' ); ?>" target="_blank" rel="noopener noreferrer" class="button button-primary"><?php echo esc_html__( 'Buy full version', 'aiko-developer-lite' ); ?></a></p>
				</div>
				<?php
				settings_fields( 'aiko_developer_settings' );
				do_settings_sections( 'aiko_developer_settings' );
				submit_button();
				?>
			</form>
		</div>
		<?php
	}

	public function get_aiko_developer_render_settings_page() {
		$this->aiko_developer_render_settings_page();
	}
}
