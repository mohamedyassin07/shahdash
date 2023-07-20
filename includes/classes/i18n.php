<?php

/**
 * Define the internationalization functionality
 *
 * Loads and defines the internationalization files for this plugin
 * so that it is ready for translation.
 *
 * @link       https://shahbandr.com
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
 * @package    Shahbandr_Dashboard
 * @subpackage Shahbandr_Dashboard/includes
 * @author     Shahbandr Team <info@shahbandr.com>
 */
class ShahDash_i18n {

	/**
	 * Load the plugin text domain for translation.
	 *
	 */
	public function load_plugin_textdomain() {

		load_plugin_textdomain(
			SHAH_NAME,
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);

	}

}
