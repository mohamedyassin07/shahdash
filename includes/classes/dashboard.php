<?php

/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       https://shahbandr.com
 *
 * @package    ShahDash
 * @subpackage ShahDash/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @package    ShahDash
 * @subpackage ShahDash/includes
 * @author     Shahbandr Team <info@shahbandr.com>
 */
class ShahDash {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @access   protected
	 * @var      ShahDash_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 */
	public function __construct() {

		$this->version = defined( 'SHAH_VER' ) ? SHAH_VER : '1.0.0';
		$this->plugin_name = SHAH_NAME;

		$this->set_loader();
		$this->set_locale();
		$this->set_modern_admin();

		require_once SHAH_DIR . 'includes/helpers.php';

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ShahDash_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @access   private
	 */
	private function set_loader() {

		/**
		 * The class responsible for orchestrating the actions and filters of the
		 * core plugin.
		 */
		require_once SHAH_DIR . 'includes/classes/loader.php';
		$this->loader = new ShahDash_Loader();

	}

	/**
	 * Define the locale for this plugin for internationalization.
	 *
	 * Uses the ShahDash_i18n class in order to set the domain and to register the hook
	 * with WordPress.
	 *
	 * @access   private
	 */
	private function set_locale() {

		/**
		 * The class responsible for defining internationalization functionality
		 * of the plugin.
		 */
		require_once SHAH_DIR . 'includes/classes/i18n.php';

		$plugin_i18n = new ShahDash_i18n();

		$this->loader->add_action( 'plugins_loaded', $plugin_i18n, 'load_plugin_textdomain' );

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ShahDash_Loader. Orchestrates the hooks of the plugin.
	 * - ShahDash_i18n. Defines internationalization functionality.
	 * - ShahDash_Admin. Defines all hooks for the admin area.
	 * - ShahDash_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @access   private
	 */
	private function set_modern_admin() {

		/**
		 * The class responsible for set the general admin page
		 * of the plugin.
		 */
		require_once SHAH_DIR . 'includes/classes/modern_admin.php';
		new ShahDash_Modern_Admin( $this->version , $this->plugin_name );
	}


	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @return    ShahDash_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}
