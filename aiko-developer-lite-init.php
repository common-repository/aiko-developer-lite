<?php
/**
 * Plugin Name: AIKO - AI Developer Lite
 * Description: AI powered plugin that creates plugins for users without experience (Lite version)
 * Version: 1.0.3
 * Author: BoldThemes
 * Author URI: https://www.bold-themes.com
 * License: GPLv2 or later
 * License URI: https://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 * Text Domain: aiko-developer-lite
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

register_activation_hook( __FILE__, 'aiko_developer_check_for_full_version' );

function aiko_developer_check_for_full_version() {
    if ( class_exists( 'Aiko_Developer' ) ) {
        deactivate_plugins( plugin_basename( __FILE__ ) );
        wp_die( '<p style="font-family: -apple-system,BlinkMacSystemFont,Roboto,Oxygen-Sans,Ubuntu,Cantarell,sans-serif; font-size: 16px; line-height: 1.5; font-weight: 600;">Could not activate AIKO Developer Lite, please deactivate AIKO Developer.</p>' );	
    } else {
        update_option( 'aiko_developer_lite_path', plugin_basename( __FILE__ ) );
    }
}

function aiko_developer_add_custom_text_to_plugin_action_links_lite( $links ) {
    $aiko_links = array();

    $aiko_links['aikopro']  = sprintf(
        '<a href="%1$s" aria-label="%2$s" target="_blank" rel="noopener noreferrer" 
            style="color: #00a32a; font-weight: 700;" 
            onmouseover="this.style.color=\'#008a20\';" 
            onmouseout="this.style.color=\'#00a32a\';"
            >%3$s</a>',
        esc_url( 'https://codecanyon.net/item/aiko-instant-plugins-ai-developer/54220020' ),
        esc_attr__( 'Upgrade to AIKO Developer Pro', 'aiko-developer-lite' ),
        esc_html__( 'Get AIKO Developer Pro', 'aiko-developer-lite' )
    );
    $aiko_links['instantplugins'] = sprintf(
        '<a href="edit.php?post_type=aiko_developer">%s</a>',
        esc_html__( 'Instant Plugins', 'aiko-developer-lite' )
    );

    return array_merge( $aiko_links, $links );
}

add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), 'aiko_developer_add_custom_text_to_plugin_action_links_lite', 10, 2 );

require_once plugin_dir_path( __FILE__ ) . 'includes/class-aiko-developer.php';

$aiko_developer_lite = new Aiko_Developer_Lite();
