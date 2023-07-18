<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://shahbandr.com
 * @since      1.0.0
 *
 * @package    Shahbandr_Dashboard
 * @subpackage Shahbandr_Dashboard/includes
 */

/**
 * Define the internationalization functionality.
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @since      1.0.0
 * @package    Shahbandr_Dashboard
 * @subpackage Shahbandr_Dashboard/includes
 * @author     Shahbandr Team <info@shahbandr.com>
 */
class Shahbandr_Dashboard_i18n {


	/**
	 * Load the plugin text domain for translation.
	 *
	 * @since    1.0.0
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			'shahbandr-dashboard',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}



}
