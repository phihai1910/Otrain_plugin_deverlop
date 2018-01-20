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
 * @package           Coupon_form
 *
 * @wordpress-plugin
 * Plugin Name:       Coupon Form
 * Plugin URI:        otrain.com.au
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Hai
 * Author URI:        otrain.com.au
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       coupon_form
 * Domain Path:       /languages
 */
if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently pligin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'PLUGIN_NAME_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-coupon_form-activator.php
 */
function activate_coupon_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coupon_form-activator.php';
	Coupon_form_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-coupon_form-deactivator.php
 */
function deactivate_coupon_form() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-coupon_form-deactivator.php';
	Coupon_form_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_coupon_form' );
register_deactivation_hook( __FILE__, 'deactivate_coupon_form' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-coupon_form.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_coupon_form() {

	$plugin = new Coupon_form();
	$plugin->run();

}
run_coupon_form();
}