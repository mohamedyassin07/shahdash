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


// Plugin Name
define( 'SHAH_NAME', 'Shahbandr_Dashboard' );

// Plugin Slug
define( 'SHAH_SLUG', 'shah_dash' );

// Plugin version
define( 'SHAH_VER',	'1.0.0' );

// Plugin Root File
define( 'SHAH_FILE', __FILE__ );

// Plugin base
define( 'SHAH_BASE', ( SHAH_FILE ) );

// Plugin Folder Path
define( 'SHAH_DIR',	plugin_dir_path( SHAH_FILE ) );

// Plugin Folder URL
define( 'SHAH_URL',	plugin_dir_url( SHAH_FILE ) );

/**
 * The code that runs during plugin activation.
 * This action is documented in includes/classes/dashboard-activator.php
 */
function activate_shahbandr_dashboard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/classes/dashboard-activator.php';
	Shahbandr_Dashboard_Activator::activate();
}

/**
 * The code that runs during plugin deactivation.
 * This action is documented in includes/classes/dashboard-deactivator.php
 */
function deactivate_shahbandr_dashboard() {
	require_once plugin_dir_path( __FILE__ ) . 'includes/classes/dashboard-deactivator.php';
	Shahbandr_Dashboard_Deactivator::deactivate();
}

register_activation_hook( __FILE__, 'activate_shahbandr_dashboard' );
register_deactivation_hook( __FILE__, 'deactivate_shahbandr_dashboard' );

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require plugin_dir_path( __FILE__ ) . 'includes/classes/dashboard.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 */
function run_shahbandr_dashboard() {

	$plugin = new Shahbandr_Dashboard();
	$plugin->run();

}
run_shahbandr_dashboard();
