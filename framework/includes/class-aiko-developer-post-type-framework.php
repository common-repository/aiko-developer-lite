<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once plugin_dir_path( dirname( __DIR__ ) ) . 'includes/class-aiko-developer-render.php';

class Aiko_Developer_Post_Type_Framework {
	public function __construct() {
		if ( class_exists( 'Aiko_Developer_Render_Lite' ) ) {
			$this->render = new Aiko_Developer_Render_Lite();
		} elseif ( class_exists( 'Aiko_Developer_Render' ) ) {
			$this->render = new Aiko_Developer_Render();
		}
	}

	private function aiko_developer_register_post_type() {
		$args = array(
			'labels'        => array(
				'name'               => esc_html__( 'Instant Plugins', 'aiko-developer-lite' ),
				'singular_name'      => esc_html__( 'Instant Plugin', 'aiko-developer-lite' ),
				'add_new_item'       => esc_html__( 'New Instant Plugin', 'aiko-developer-lite' ),
				'add_new'            => esc_html__( 'Add New Plugin', 'aiko-developer-lite' ),
				'edit_item'          => esc_html__( 'Instant Plugin', 'aiko-developer-lite' ),
				'menu_name'          => esc_html__( 'AIKO Developer', 'aiko-developer-lite' ),
				'search_items'       => esc_html__( 'Search Instant Plugins', 'aiko-developer-lite' ),
				'not_found'          => esc_html__( 'No Instant Plugins found.', 'aiko-developer-lite' ),
				'not_found_in_trash' => esc_html__( 'No Instant Plugins found in Trash.', 'aiko-developer-lite' ),
				'view_item'          => esc_html__( 'View Instant Plugin', 'aiko-developer-lite' ),
			),
			'public'        => false,
			'show_ui'       => true,
			'show_in_menu'  => false,
			'menu_position' => 3,
			'menu_icon'     => 'dashicons-media-code',
			'supports'      => array( 'title', 'revisions' ),
		);

		register_post_type( 'aiko_developer', $args );
	}

	public function get_aiko_developer_register_post_type() {
		$this->aiko_developer_register_post_type();
	}

	private function aiko_developer_hide_posts_for_non_admins( $query ) {
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
		if ( 'aiko_developer' === $query->get( 'post_type' ) && $query->is_main_query() && ! $is_user_admin ) {
			wp_die( '<div class="wp-die-message"><h1>Unauthorized access!</h1><p>Only Administrators are allowed to use AIKO Developer plugin. If you think this is an error, please contact your Administrator.</p></div>' );
		}
	}

	public function get_aiko_developer_hide_posts_for_non_admins( $query ) {
		$this->aiko_developer_hide_posts_for_non_admins( $query );
	}

	private function aiko_developer_restrict_post_access() {
		global $pagenow;
		if ( 'post.php' === $pagenow ) {
			$screen    = get_current_screen();
			$post_type = $screen->post_type;
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
			if ( 'aiko_developer' === $post_type && ! $is_user_admin ) {
				wp_die( '<div class="wp-die-message"><h1>Unauthorized access!</h1><p>Only Administrators are allowed to use AIKO Developer plugin. If you think this is an error, please contact your Administrator.</p></div>' );
			}
		}
	}

	public function get_aiko_developer_restrict_post_access() {
		$this->aiko_developer_restrict_post_access();
	}

	private function aiko_developer_add_output_meta_boxes() {
		add_meta_box(
			'aiko-developer-php-output-meta-box',
			esc_html__( 'PHP', 'aiko-developer-lite' ),
			array( $this->render, 'get_aiko_developer_render_php' ),
			'aiko_developer',
			'normal',
			'default'
		);
		add_meta_box(
			'aiko-developer-js-output-meta-box',
			esc_html__( 'JavaScript', 'aiko-developer-lite' ),
			array( $this->render, 'get_aiko_developer_render_js' ),
			'aiko_developer',
			'normal',
			'default'
		);
		add_meta_box(
			'aiko-developer-css-output-meta-box',
			esc_html__( 'CSS', 'aiko-developer-lite' ),
			array( $this->render, 'get_aiko_developer_render_css' ),
			'aiko_developer',
			'normal',
			'default'
		);
		add_meta_box(
			'aiko-developer-download-meta-box',
			esc_html__( 'Download', 'aiko-developer-lite' ),
			array( $this->render, 'get_aiko_developer_render_download_buttons' ),
			'aiko_developer',
			'side',
			'default'
		);
	}

	public function get_aiko_developer_add_output_meta_boxes() {
		$this->aiko_developer_add_output_meta_boxes();
	}

	private function aiko_developer_add_menu_pages() {
		add_menu_page(
			esc_html__( 'AIKO Developer', 'aiko-developer-lite' ),
			esc_html__( 'AIKO Developer', 'aiko-developer-lite' ),
			'manage_options',
			'aiko-developer-home',
			array( $this->render, 'get_aiko_developer_render_home_page' ),
			'dashicons-media-code',
			3
		);

		add_submenu_page(
			'aiko-developer-home',
			esc_html__( 'AIKO Home', 'aiko-developer-lite' ),
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
			esc_html__( 'AIKO Settings', 'aiko-developer-lite' ),
			esc_html__( 'Settings', 'aiko-developer-lite' ),
			'manage_options',
			'aiko-developer-settings',
			array( $this->render, 'get_aiko_developer_render_settings_page' )
		);
	}

	public function get_aiko_developer_add_menu_pages() {
		$this->aiko_developer_add_menu_pages();
	}

	private function aiko_developer_highlight_submenu() {
		global $parent_file, $submenu_file, $post_type;

		$screen = get_current_screen();
		$page   = preg_replace( '/.*page_/', '', $screen->id );

		if ( 'aiko_developer' === $post_type ) {
			$parent_file  = 'aiko-developer-home';
			$submenu_file = 'edit.php?post_type=aiko_developer';
		} elseif ( 'aiko-developer-settings' === $page ) {
			$parent_file  = 'aiko-developer-home';
			$submenu_file = 'aiko-developer-settings';
		}
	}

	public function get_aiko_developer_highlight_submenu() {
		$this->aiko_developer_highlight_submenu();
	}

	private function aiko_developer_register_settings() {
		add_settings_section(
			'aiko_developer_section',
			esc_html__( 'OpenAI API Key', 'aiko-developer-lite' ),
			null,
			'aiko_developer_settings'
		);

		add_settings_field(
			'aiko_developer_api_key',
			esc_html__( 'API Key', 'aiko-developer-lite' ),
			array( $this->render, 'get_aiko_developer_render_api_key_field' ),
			'aiko_developer_settings',
			'aiko_developer_section'
		);

		add_settings_section(
			'aiko_developer_model_section',
			esc_html__( 'OpenAI Models', 'aiko-developer-lite' ),
			null,
			'aiko_developer_settings'
		);

		add_settings_field(
			'aiko_developer_model',
			esc_html__( 'Developer', 'aiko-developer-lite' ),
			array( $this->render, 'get_aiko_developer_render_model_field' ),
			'aiko_developer_settings',
			'aiko_developer_model_section'
		);

		add_settings_field(
			'aiko_developer_consultant_model',
			esc_html__( 'Consultant', 'aiko-developer-lite' ),
			array( $this->render, 'get_aiko_developer_render_consultant_model_field' ),
			'aiko_developer_settings',
			'aiko_developer_model_section'
		);

		register_setting( 'aiko_developer_settings', 'aiko_developer_api_key' );
		register_setting( 'aiko_developer_settings', 'aiko_developer_model' );
		register_setting( 'aiko_developer_settings', 'aiko_developer_consultant_model' );
	}

	public function get_aiko_developer_register_settings() {
		$this->aiko_developer_register_settings();
	}

	private function aiko_developer_enable_meta_revisions( $revisioned_keys ) {
		if ( ! in_array( '_post_slug', $revisioned_keys, true ) ) {
			$revisioned_keys[] = '_post_slug';
		}
		if ( ! in_array( '_improvements', $revisioned_keys, true ) ) {
			$revisioned_keys[] = '_improvements';
		}
		if ( ! in_array( '_php_output', $revisioned_keys, true ) ) {
			$revisioned_keys[] = '_php_output';
		}
		if ( ! in_array( '_js_output', $revisioned_keys, true ) ) {
			$revisioned_keys[] = '_js_output';
		}
		if ( ! in_array( '_css_output', $revisioned_keys, true ) ) {
			$revisioned_keys[] = '_css_output';
		}
		if ( ! in_array( '_functional_requirements', $revisioned_keys, true ) ) {
			$revisioned_keys[] = '_functional_requirements';
		}
		return $revisioned_keys;
	}

	public function get_aiko_developer_enable_meta_revisions( $revisioned_keys ) {
		return $this->aiko_developer_enable_meta_revisions( $revisioned_keys );
	}

	private function aiko_developer_enable_meta_revision_fields( $fields ) {
		$fields['_post_slug']                    = esc_html__( 'Post Slug', 'aiko-developer-lite' );
		$fields['_improvements']                 = esc_html__( 'Improvements', 'aiko-developer-lite' );
		$fields['_functional_requirements']      = esc_html__( 'Functional Requirements', 'aiko-developer-lite' );
		$fields['_php_output']                   = esc_html__( 'PHP Output', 'aiko-developer-lite' );
		$fields['_js_output']                    = esc_html__( 'JS Output', 'aiko-developer-lite' );
		$fields['_css_output']                   = esc_html__( 'CSS Output', 'aiko-developer-lite' );
		return $fields;
	}

	public function get_aiko_developer_enable_meta_revision_fields( $fields ) {
		return $this->aiko_developer_enable_meta_revision_fields( $fields );
	}

	private function aiko_developer_change_title_placeholder( $title, $post ) {
		if ( 'aiko_developer' === $post->post_type ) {
			$title = esc_html__( 'Add Plugin Title', 'aiko-developer-lite' );
		}
		return $title;
	}

	public function get_aiko_developer_change_title_placeholder( $title, $post ) {
		return $this->aiko_developer_change_title_placeholder( $title, $post );
	}
}
