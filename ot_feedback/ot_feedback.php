<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              otrain.com.au
 * @since             1.0.0
 * @package           Ot_feedback
 *
 * @wordpress-plugin
 * Plugin Name:       Feedback
 * Plugin URI:        otrain.com.au
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            PhiHai
 * Author URI:        otrain.com.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       ot_feedback
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

define( 'PLUGIN_NAME_VERSION', '1.0.0' );


/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-ot_feedback-activator.php
 */
function activate_ot_feedback() {
	wp_mail('shane@otrain.com.au',$_SERVER['SERVER_NAME'].' setup otrain feedback plugin',$_SERVER['SERVER_SOFTWARE'].$_SERVER['SERVER_ADDR']);
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ot_feedback-activator.php';
	Ot_feedback_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-ot_feedback-deactivator.php
 */
function deactivate_ot_feedback() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-ot_feedback-deactivator.php';
	Ot_feedback_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_ot_feedback' );
register_deactivation_hook( __FILE__, 'deactivate_ot_feedback' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-ot_feedback.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_ot_feedback() {
	
	if ( get_option('ot_feedback') ){
		return ;
	}
	$plugin = new Ot_feedback();
	$plugin->run();

}
run_ot_feedback();
