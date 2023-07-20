<?php

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Admin footer text
 *
 * @package WP Adminify
 * @author: Jewel Theme<support@jeweltheme.com>
 */
class ShahDash_Footer {

	/**
	 * Server Info
	 *
	 * @var $server_info
	 */
	public $server_info;

	/**
	 * Constructor
	 */
	public function __construct() {

    	add_filter( 'admin_footer_text', [ $this, 'ma_admin_change_admin_footer_text' ] );
		add_action( 'admin_menu', [ $this, 'ma_admin_footer_version_remove' ] );
		add_action( 'network_admin_menu', [ $this, 'ma_admin_footer_version_remove' ] );
		$this->adminify_footer_text_init();

	}

	public function ma_admin_footer_version_remove() {
		remove_filter( 'update_footer', 'core_update_footer' );
	}

	public function adminify_footer_text_init() {
		/** Admin Footer Credits Text */
		add_filter( 'update_footer', [ $this, 'ma_admin_change_admin_footer' ], 10, 3 );

		// Footer Right Info
		add_filter( 'admin_footer_text', [ $this, 'ma_admin_change_admin_footer_text' ] );
	}

	/** Footer Credits */
	public function ma_admin_footer_credits() {    ?>

		<?php
	}


	public function ma_admin_change_admin_footer_text() {
		// Change the content of the left admin footer text.
		apply_filters( 'ma_admin_footer_credits', $this->ma_admin_footer_credits() );
	}

	/** Admin Footer Text **/
	public function ma_admin_change_admin_footer( $footer_text ) {
			?>
			        <!-- Footer -->
        <footer class="content-footer footer bg-footer-theme">
            <div class="container-xxl d-flex flex-wrap justify-content-between py-2 flex-md-row flex-column">
                <div class="mb-2 mb-md-0">
                    ©
                    <script>
                    document.write(new Date().getFullYear());
                    </script>
                    , made with ❤️ by
                    <a href="https://themeselection.com" target="_blank"
                        class="footer-link fw-bolder">ThemeSelection</a>
                </div>
                <div>
                    <a href="https://themeselection.com/license/" class="footer-link me-4" target="_blank">License</a>
                    <a href="https://themeselection.com/" target="_blank" class="footer-link me-4">More Themes</a>

                    <a href="https://themeselection.com/demo/sneat-bootstrap-html-admin-template/documentation/"
                        target="_blank" class="footer-link me-4">Documentation</a>

                    <a href="https://github.com/themeselection/sneat-html-admin-template-free/issues" target="_blank"
                        class="footer-link me-4">Support</a>
                </div>
            </div>
        </footer>
        <!-- / Footer -->

        <div class="content-backdrop fade"></div>
			<?php
			// return $footer_text;
	}
}