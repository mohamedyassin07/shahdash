<?php

/**
 * The admin-specific functionality of the plugin.
 *
 * @link       https://shahbandr.com
 *
 * @package    Shahbandr_Dashboard
 * @subpackage Shahbandr_Dashboard/admin
 */

/**
 * The admin-specific functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the admin-specific stylesheet and JavaScript.
 *
 * @package    Shahbandr_Dashboard
 * @subpackage Shahbandr_Dashboard/admin
 * @author     Shahbandr Team <info@shahbandr.com>
 */
class ShahDash_Modern_Admin {

	/**
	 * The ID of this plugin.
	 *
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @param      string    $plugin_name       The name of this plugin.
	 * @param      string    $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version = $version;

		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_backend_scripts_and_styles' ), 20 );
		add_action( 'wp_ajax_my_demo_ajax_call', array( $this, 'my_demo_ajax_call_callback' ), 20 );

		$this->load_dependencies();

	}

	
	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ShahDash_Loader. Orchestrates the hooks of the plugin.
	 * - ShahDash_i18n. Defines internationalization functionality.
	 * - ShahDash_Modern_Admin. Defines all hooks for the admin area.
	 * - ShahDash_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @access   public
	 */
	public function load_dependencies(){

		require_once SHAH_DIR . 'includes/classes/utils.php';

		require_once SHAH_DIR . 'includes/classes/admin-bar.php';
		new ShahDash_Admin_Bar();

		require_once SHAH_DIR . 'includes/classes/menu.php';
		new ShahDash_Menu();
		
		require_once SHAH_DIR . 'includes/classes/footer.php';
		new ShahDash_Footer();

	}



	/**
	 * Register the JavaScript for the admin area.
	 *
	 */
	public function enqueue_scripts() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ShahDash_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ShahDash_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		wp_enqueue_script( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'js/shahbandr-dashboard-admin.js', array( 'jquery' ), $this->version, false );

	}


	/**
	 * The callback function for my_demo_ajax_call
	 *
	 * @access	public
	 * @since	0.0.1
	 *
	 * @return	void
	 */
	public function my_demo_ajax_call_callback() {
		check_ajax_referer( 'your-nonce-name', 'ajax_nonce_parameter' );

		$demo_data = isset( $_REQUEST['demo_data'] ) ? sanitize_text_field( $_REQUEST['demo_data'] ) : '';
		$response = array( 'success' => false );

		if ( ! empty( $demo_data ) ) {
			$response['success'] = true;
			$response['msg'] = __( 'The value was successfully filled.', 'shahbandr-dashboard' );
		} else {
			$response['msg'] = __( 'The sent value was empty.', 'shahbandr-dashboard' );
		}

		if( $response['success'] ){
			wp_send_json_success( $response );
		} else {
			wp_send_json_error( $response );
		}

		die();
	}

	/**
	 * Enqueue the backend related scripts and styles for this plugin.
	 * All of the added scripts andstyles will be available on every page within the backend.
	 *
	 * @access	public
	 *
	 * @return	void
	 */
	public function enqueue_backend_scripts_and_styles() {

		// Vendor Stylesheets
		wp_enqueue_style( 'fullcalendar.bundle.css', SHAH_URL . 'assets/plugins/custom/fullcalendar/fullcalendar.bundle.css' , array(), SHAH_VER, 'all' );
		wp_enqueue_style( 'datatables.bundle.rtl.css', SHAH_URL . 'assets/plugins/custom/fullcalendar/datatables.bundle.rtl.css' , array(), SHAH_VER, 'all' );
		
		// Global Stylesheets Bundle(mandatory for all pages)
		wp_enqueue_style( 'plugins.bundle.rtl.css', SHAH_URL . 'assets/plugins/global/plugins.bundle.rtl.css' , array(), SHAH_VER, 'all' );
		wp_enqueue_style( 'style.bundle.rtl.css', SHAH_URL . 'assets/css/style.bundle.rtl.css' , array(), SHAH_VER, 'all' );




		// <script>var hostUrl = "assets/";</script>

		// Global Javascript Bundle(mandatory for all pages)
		wp_enqueue_script( 'global/plugins.bundle.js', SHAH_URL . 'assets/plugins/global/plugins.bundle.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'js/scripts.bundle.js', SHAH_URL . 'assets/js/scripts.bundle.js', array( 'jquery' ), SHAH_VER, true );


		// Vendors Javascript
		wp_enqueue_script( 'fullcalendar/fullcalendar.bundle.js', SHAH_URL . 'assets/plugins/custom/fullcalendar/fullcalendar.bundle.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/index.js', 'https://cdn.amcharts.com/lib/5/index.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/xy.js', 'https://cdn.amcharts.com/lib/5/xy.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/percent.js', 'https://cdn.amcharts.com/lib/5/percent.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/radar.js', 'https://cdn.amcharts.com/lib/5/radar.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/themes/Animated.js', 'https://cdn.amcharts.com/lib/5/themes/Animated.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/map.js', 'https://cdn.amcharts.com/lib/5/map.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/geodata/worldLow.js', 'https://cdn.amcharts.com/lib/5/geodata/worldLow.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/geodata/continentsLow.js', 'https://cdn.amcharts.com/lib/5/geodata/continentsLow.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/geodata/usaLow.js', 'https://cdn.amcharts.com/lib/5/geodata/usaLow.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js', 'https://cdn.amcharts.com/lib/5/geodata/worldTimeZonesLow.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js', 'https://cdn.amcharts.com/lib/5/geodata/worldTimeZoneAreasLow.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'assets/plugins/custom/datatables/datatables.bundle.js', 'assets/plugins/custom/datatables/datatables.bundle.js', array( 'jquery' ), SHAH_VER, true );

		
		// Custom Javascript
		wp_enqueue_script( 'widgets.bundle.js', SHAH_URL . 'assets/js/widgets.bundle.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'custom/widgets.js', SHAH_URL . 'assets/js/custom/widgets.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'apps/chat/chat.js', SHAH_URL . 'assets/js/custom/apps/chat/chat.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'utilities/modals/upgrade-plan.js', SHAH_URL . 'assets/js/custom/utilities/modals/upgrade-plan.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'utilities/modals/create-app.js', SHAH_URL . 'assets/js/custom/utilities/modals/create-app.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'utilities/modals/new-target.js', SHAH_URL . 'assets/js/custom/utilities/modals/new-target.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'utilities/modals/users-search.js', SHAH_URL . 'assets/js/custom/utilities/modals/users-search.js', array( 'jquery' ), SHAH_VER, true );
		wp_enqueue_script( 'shahdash-backend-scripts', SHAH_URL . 'core/includes/assets/js/backend-scripts.js', array( 'jquery' ), SHAH_VER, true );
		
		wp_localize_script( 'shahdash-backend-scripts', 'shahdash', array(
			'plugin_name'   	=> __( SHAH_NAME, 'shahbandr-dashboard' ),
			'ajaxurl' 			=> admin_url( 'admin-ajax.php' ),
			'security_nonce'	=> wp_create_nonce( "your-nonce-name" ),
		));
	}

}