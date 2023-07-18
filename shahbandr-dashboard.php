<?php

/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              https://shahbandr.com
 * @since             1.0.0
 * @package           Shahbandr_Dashboard
 *
 * @wordpress-plugin
 * Plugin Name:       Shahbandr Dashboard
 * Plugin URI:        https://shahbandr.com/dashboard
 * Description:       Official Shahbandr.com shop manger dashboard
 * Version:           1.0.0
 * Author:            Shahbandr Team
 * Author URI:        https://shahbandr.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       shahbandr-dashboard
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Currently plugin version.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'SHAHBANDR_DASHBOARD_VERSION', '1.0.0' );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/class-shahbandr-dashboard-activator.php
 */
function activate_shahbandr_dashboard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-shahbandr-dashboard-activator.php';
	Shahbandr_Dashboard_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/class-shahbandr-dashboard-deactivator.php
 */
function deactivate_shahbandr_dashboard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/class-shahbandr-dashboard-deactivator.php';
	Shahbandr_Dashboard_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_shahbandr_dashboard' );
register_deactivation_hook( __FILE__, 'deactivate_shahbandr_dashboard' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/class-shahbandr-dashboard.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_shahbandr_dashboard() {

	$plugin = new Shahbandr_Dashboard();
	$plugin->run();

}
run_shahbandr_dashboard();
