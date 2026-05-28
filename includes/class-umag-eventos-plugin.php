<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

require_once UMAG_EVENTOS_PATH . 'includes/class-umag-eventos-post-type.php';
require_once UMAG_EVENTOS_PATH . 'includes/class-umag-eventos-admin.php';
require_once UMAG_EVENTOS_PATH . 'includes/class-umag-eventos-locations.php';
require_once UMAG_EVENTOS_PATH . 'includes/class-umag-eventos-meta-boxes.php';
require_once UMAG_EVENTOS_PATH . 'includes/class-umag-eventos-settings.php';
require_once UMAG_EVENTOS_PATH . 'includes/class-umag-eventos-shortcodes.php';
require_once UMAG_EVENTOS_PATH . 'includes/class-umag-eventos-frontend.php';

class UMAG_Eventos_Plugin {
	/**
	 * Inicializa el plugin.
	 *
	 * @return void
	 */
	public static function init() {
		add_action( 'init', array( 'UMAG_Eventos_Post_Type', 'register' ) );
		add_action( 'admin_init', array( 'UMAG_Eventos_Admin', 'init' ) );
		add_action( 'admin_init', array( 'UMAG_Eventos_Locations', 'init' ) );
		add_action( 'admin_init', array( 'UMAG_Eventos_Settings', 'register_settings' ) );
		add_action( 'admin_menu', array( 'UMAG_Eventos_Settings', 'register_menu' ) );
		add_action( 'add_meta_boxes', array( 'UMAG_Eventos_Meta_Boxes', 'register' ) );
		add_action( 'save_post_umag_evento', array( 'UMAG_Eventos_Meta_Boxes', 'save' ) );
		add_action( 'init', array( 'UMAG_Eventos_Shortcodes', 'register' ) );
		add_action( 'wp_enqueue_scripts', array( 'UMAG_Eventos_Frontend', 'register_assets' ) );
		add_action( 'elementor/frontend/after_enqueue_styles', array( 'UMAG_Eventos_Frontend', 'enqueue_elementor_assets' ) );
		add_action( 'elementor/preview/enqueue_styles', array( 'UMAG_Eventos_Frontend', 'enqueue_elementor_assets' ) );
		add_action( 'elementor/frontend/after_enqueue_scripts', array( 'UMAG_Eventos_Frontend', 'enqueue_elementor_assets' ) );
		add_action( 'pre_get_posts', array( 'UMAG_Eventos_Frontend', 'sort_archive_query' ) );
		add_filter( 'template_include', array( 'UMAG_Eventos_Frontend', 'load_archive_template' ) );
	}

	/**
	 * Ejecuta tareas al activar el plugin.
	 *
	 * @return void
	 */
	public static function activate() {
		UMAG_Eventos_Post_Type::register();
		flush_rewrite_rules();
	}

	/**
	 * Ejecuta tareas al desactivar el plugin.
	 *
	 * @return void
	 */
	public static function deactivate() {
		flush_rewrite_rules();
	}
}
