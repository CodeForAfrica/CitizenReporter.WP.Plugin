<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * Plugin Name: WP Citizen Reporter
 * Plugin URI: http://github.com/codeforafrica/WPCitizenReporter
 * Description: A Wordpress plugin with enhancements for use with the CitizenReporter application
 * Version: 1.0.0
 * Author: Nick Hargreaves
 * Author URI: http://nickhargreaves.com
 * License: GPL2
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-wpcitizenreporter-activator.php
 */
function activate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcitizenreporter-activator.php';
	WPCitizenReporter_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-wpcitizenreporter-deactivator.php
 */
function deactivate_plugin_name() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-wpcitizenreporter-deactivator.php';
	WPCitizenReporter_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_plugin_name' );
register_deactivation_hook( __FILE__, 'deactivate_plugin_name' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-wpcitizenreporter.php';
//require plugin_dir_path( __FILE__ ) . 'includes/dashboard.php';
require plugin_dir_path( __FILE__ ) . 'includes/feedback.php';
require plugin_dir_path( __FILE__ ) . 'includes/gcm_stuff.php';
require plugin_dir_path( __FILE__ ) . 'includes/lessons.php';
require plugin_dir_path( __FILE__ ) . 'includes/payment.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_plugin_name() {

	$plugin = new WPCitizenReporter();
	$plugin->run();

}
run_plugin_name();
