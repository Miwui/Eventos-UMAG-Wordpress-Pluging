<?php

/**
 * Plugin Name: Plugin Eventos para wordpress
 * Description: Plugin base para administrar eventos de la Universidad de Magallanes.
 * Version: 1.0
 * Author: Antonio Bravo Saavedra.
 * Text Domain: umag-eventos
 */

if (! defined('ABSPATH')) {
	exit;
}

define('UMAG_EVENTOS_VERSION', '1.0');
define('UMAG_EVENTOS_PATH', plugin_dir_path(__FILE__));
define('UMAG_EVENTOS_URL', plugin_dir_url(__FILE__));

require_once UMAG_EVENTOS_PATH . 'includes/class-umag-eventos-plugin.php';

register_activation_hook(__FILE__, array('UMAG_Eventos_Plugin', 'activate'));
register_deactivation_hook(__FILE__, array('UMAG_Eventos_Plugin', 'deactivate'));

UMAG_Eventos_Plugin::init();
