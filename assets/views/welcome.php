<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$api_key = get_option( 'aiko_developer_api_key', '' );
?>
<div class="aiko-developer-welcome-wrapper aiko-developer-lite-welcome-wrapper">
	<div class="aiko-developer-welcome">
		<p class="aiko-developer-welcome-supertitle"><?php echo esc_html__( 'Welcome to', 'aiko-developer-lite' ); ?></p>
		<h1>
			<span><?php echo esc_html__( 'AIKO', 'aiko-developer-lite' ); ?></span>
		</h1>
		<p class="aiko-developer-welcome-subtitle"><?php echo esc_html__( 'AI Developer Lite (Pro version avaliable)', 'aiko-developer-lite' ); ?></p>
		<p class="aiko-developer-welcome-description"><?php echo esc_html__( 'Designed for easy use, AIKO uses AI to create instant custom plugins in just a few minutes.', 'aiko-developer-lite' ); ?></p>
		<p class="aiko-developer-welcome-tools">
			<?php if ( ! empty( $api_key ) ) { ?>
			<a class="button button-primary button-large" href="post-new.php?post_type=aiko_developer"><?php echo esc_html__( 'Add New Plugin', 'aiko-developer-lite' ); ?></a>
			<a class="button button-primary button-large" href="https://codecanyon.net/item/aiko-instant-plugins-ai-developer/54220020" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Get Pro version', 'aiko-developer-lite' ); ?></a>
			<a class="button button-secondary button-large" href="admin.php?page=aiko-developer-settings"><?php echo esc_html__( 'Settings', 'aiko-developer-lite' ); ?></a>
			<?php } ?>
			<a class="button button-secondary button-large" href="https://aiko-developer.bold-themes.com" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Official Website', 'aiko-developer-lite' ); ?></a>
			<a class="button button-secondary button-large" href="https://documentation.bold-themes.com/aiko-developer" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Documentation', 'aiko-developer-lite' ); ?></a>
			<a class="button button-secondary button-large" href="https://boldthemes.ticksy.com/" target="_blank" rel="noopener noreferrer"><?php echo esc_html__( 'Support', 'aiko-developer-lite' ); ?></a>
		</p>
	</div>
</div>