<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Aiko_Developer_Post_Type_Framework' ) ) {
	require_once plugin_dir_path( __DIR__ ) . '/framework/includes/class-aiko-developer-post-type-framework.php';
}
require_once plugin_dir_path( __FILE__ ) . 'class-aiko-developer-render.php';

class Aiko_Developer_Post_Type_Lite extends Aiko_Developer_Post_Type_Framework {
	public function __construct() {
		$this->render = new Aiko_Developer_Render_Lite();
	}

	private function aiko_developer_add_menu_pages() {
		add_menu_page(
			esc_html__( 'AIKO Developer Lite', 'aiko-developer-lite' ),
			esc_html__( 'AIKO Developer Lite', 'aiko-developer-lite' ),
			'manage_options',
			'aiko-developer-home',
			array( $this->render, 'get_aiko_developer_render_home_page' ),
			'dashicons-media-code',
			3
		);

		add_submenu_page(
			'aiko-developer-home',
			esc_html__( 'AIKO Lite Home', 'aiko-developer-lite' ),
			esc_html__( 'Home', 'aiko-developer-lite' ),
			'manage_options',
			'aiko-developer-home',
			array( $this->render, 'get_aiko_developer_render_home_page' )
		);

		add_submenu_page(
			'aiko-developer-home',
			esc_html__( 'Instant Plugins', 'aiko-developer-lite' ),
			esc_html__( 'Instant Plugins', 'aiko-developer-lite' ),
			'manage_options',
			'edit.php?post_type=aiko_developer'
		);

		add_submenu_page(
			'aiko-developer-home',
			esc_html__( 'Add New Plugin', 'aiko-developer-lite' ),
			esc_html__( 'Add New Plugin', 'aiko-developer-lite' ),
			'manage_options',
			'post-new.php?post_type=aiko_developer'
		);

		add_submenu_page(
			'aiko-developer-home',
			esc_html__( 'AIKO Lite Settings', 'aiko-developer-lite' ),
			esc_html__( 'Settings', 'aiko-developer-lite' ),
			'manage_options',
			'aiko-developer-settings',
			array( $this->render, 'get_aiko_developer_render_settings_page' )
		);
	}

	public function get_aiko_developer_add_menu_pages() {
		$this->aiko_developer_add_menu_pages();
	}
}
