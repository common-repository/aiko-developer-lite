<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

if ( ! class_exists( 'Aiko_Developer_Ajax_Framework' ) ) {
	require_once plugin_dir_path( __DIR__ ) . '/framework/includes/class-aiko-developer-ajax-framework.php';
}

class Aiko_Developer_Ajax_Lite extends Aiko_Developer_Ajax_Framework {

}
