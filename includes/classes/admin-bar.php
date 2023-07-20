<?php
// no direct access allowed
if ( !defined( 'ABSPATH' ) ) {
    exit;
}
require_once ABSPATH . WPINC . '/class-wp-admin-bar.php';
class ShahDash_Admin_Bar extends WP_Admin_Bar {

	public function render() {
		$root = $this->_bind();

		if ( empty( $root ) ) {
			return;
		}

		$class = 'nojq nojs';
		if ( wp_is_mobile() ) {
			$class .= ' mobile';
		}

		?>
		<div id="wpadminbar" class="<?php echo esc_attr( $class ); ?>">
			<?php if ( ! is_admin() && ! did_action( 'wp_body_open' ) ) { ?>
				<a class="screen-reader-shortcut" href="#wp-toolbar" tabindex="1"><?php esc_html_e( 'Skip to toolbar', 'madmin' ); ?></a>
			<?php } ?>
			<div class="quicklinks navbar" id="wp-toolbar" role="navigation" aria-label="<?php esc_attr_e( 'Toolbar', 'madmin' ); ?>">
				<?php
				foreach ( $root->children as $group ) {
					if ( $group->id !== 'top-secondary' ) {
						$this->_render_group( $group );
					}
				}

				?>
				 
					<?php
					if ( is_admin() ) {
						do_action( 'madmin/before/secondary_menu' );}
					?>
					 
				<?php

				foreach ( $root->children as $group ) {
					if ( $group->id === 'top-secondary' ) {
						$this->_render_group( $group );
					}
				}
				?>
			</div>
			<?php if ( is_user_logged_in() ) : ?>
			<a class="screen-reader-shortcut" href="<?php echo esc_url( wp_logout_url() ); ?>"><?php esc_html_e( 'Log Out', 'madmin' ); ?></a>
			<?php endif; ?>
		</div>

		<?php
	}

}

class AdminBar 
{
    public  $post_types ;
    public function __construct()
    {

        // Disable the default admin bar
        add_filter('show_admin_bar', '__return_false');
        // Add admin-bar support if not already activated
        add_theme_support( 'admin-bar', [
            'callback' => '__return_false',
        ] );
        // Check Madmin Setup Wizard Page
        if ( !empty($_GET['page']) && 'wp-madmin-setup-wizard' == $_GET['page'] ) {
            return;
        }
        add_action( 'plugins_loaded', [ $this, 'initialize' ] );
        add_action( 'wp_admin_bar_class', [ $this, 'load_wp_admin_bar_class' ] );
        add_action( 'madmin/before/secondary_menu', [ $this, 'before_secondary_menu' ] );
    }
    
    public function initialize()
    {
        $admin_bar_position = 'top';
        
        if ( is_admin() ) {
            if ( $admin_bar_position == 'top' || $admin_bar_position == 'bottom' ) {
                add_action( 'admin_init', [ $this, 'ma_admin_add_admin_bar' ] );
            }
            // Remove Unnecessary Menus from Admin bar
            // add_action('wp_before_admin_bar_render', [$this, 'jltma_madmin_remove_admin_bar_menus'], 0);
            add_filter( 'admin_body_class', [ $this, 'admin_bar_body_class' ] );
            add_action( 'wp_ajax_madmin_all_search', [ $this, 'madmin_all_search' ] );
            add_action( 'wp_ajax_wp_madmin_color_mode', [ $this, 'wp_madmin_color_mode' ] );
            add_action( 'admin_enqueue_scripts', [ $this, 'ma_admin_admin_scripts' ], 100 );
            // Screen Option and Help Tab
            add_action(
                'admin_head',
                [ $this, 'ma_admin_remove_screen_options' ],
                10,
                3
            );
            add_action( 'admin_head', [ $this, 'ma_admin_remove_help_tab' ] );
        } else {
            // Admin bar Frontend settings
            $frontend_admin = ( !empty($this->options['admin_bar_hide_frontend']) ? $this->options['admin_bar_hide_frontend'] : 'show' );
            $this->ma_admin_front_bar();
            add_action( 'init', [ $this, 'admin_bar_front_style_init' ] );
            if ( $frontend_admin == 'hide' ) {
                add_filter( 'show_admin_bar', '__return_false' );
            }
        }
    
    }
    
    public function before_secondary_menu()
    {
        ?>
			
		<?php 

    }
    
    public function load_wp_admin_bar_class()
    {
        return 'ShahDash_Admin_Bar';
    }
    
    public function ma_admin_front_bar()
    {
        add_action( 'admin_init', [ $this, 'ma_admin_add_admin_bar' ] );
        add_filter( 'body_class', [ $this, 'admin_bar_body_class' ] );
    }
    
    public function admin_bar_front_style_init()
    {
        add_filter( 'wp_enqueue_scripts', [ $this, 'admin_bar_front_style' ] );
    }
    
    // Frontend Admin bar style
    public function admin_bar_front_style()
    {
        $admin_bar_css = '';
        $admin_bar_css .= '.admin-bar-position-bottom #wpadminbar{
            top:auto;
            bottom: 0;
        }
        .admin-bar-position-bottom  #wpadminbar .menupop .ab-sub-wrapper{
            bottom: 32px;
        }

        @media all and (max-width:600px){
            body.logged-in.admin-bar-position-bottom {
                position: relative;
            }
        }';
        wp_add_inline_style( 'admin-bar', $admin_bar_css );
    }
    
    // Remove Screen Options
    public function ma_admin_remove_screen_options()
    {
        $enable_screen_tab = Utils::get_user_preference( 'screen_options_tab' );
        if ( $enable_screen_tab ) {
            add_filter( 'screen_options_show_screen', '__return_false' );
        }
    }
    
    // Contextual Help Tab Remove
    public function ma_admin_remove_help_tab()
    {
        $enable_screen_tab = Utils::get_user_preference( 'madmin_help_tab' );
        
        if ( $enable_screen_tab ) {
            $screen = get_current_screen();
            $screen->remove_help_tabs();
        }
    
    }
    
    // Get All registered WP Admin Menus
    public static function get_wp_admin_menus( $thismenu, $thissubmenu )
    {
        $options = [];
        if ( !empty($thismenu) && is_array( $thismenu ) ) {
            foreach ( $thismenu as $item ) {
                if ( !empty($item[0]) ) {
                    // the preg_replace removes "Comments" & "Plugins" menu spans.
                    $options[$item[2]] = preg_replace( '/\\<span.*?>.*?\\<\\/span><\\/span>/s', '', $item[0] );
                }
            }
        }
        if ( !empty($thissubmenu) && is_array( $thissubmenu ) ) {
            foreach ( $thissubmenu as $items ) {
                foreach ( $items as $item ) {
                    if ( !empty($item[0]) ) {
                        $options[$item[1]] = preg_replace( '/\\<span.*?>.*?\\<\\/span><\\/span>/s', '', $item[0] );
                    }
                }
            }
        }
        return $options;
    }
    
    public function ma_admin_admin_scripts()
    {
        global  $pagenow ;
        if ( !is_user_logged_in() && $pagenow != 'wp-login.php' ) {
            return;
        }
        if ( is_admin_bar_showing() ) {
        }
        wp_enqueue_style( 'wp-madmin-admin-bar' );
        wp_localize_script( 'wp-madmin-admin', 'WPMadmin', $this->madmin_create_admin_bar_js_object() );
        $this->admin_topbar_loader_css();
        // wp_enqueue_style('wp-madmin-admin-bar', WP_MADMIN_ASSETS . 'css/admin-bar.css', false, WP_MADMIN_VER);
        // wp_enqueue_script('wp-madmin-admin', WP_MADMIN_ASSETS . 'js/wp-madmin.js',  ['jquery'], WP_MADMIN_VER, true);
        // wp_localize_script('wp-madmin-admin', 'WPMadmin', $this->madmin_create_admin_bar_js_object());
    }
    
    public function madmin_create_admin_bar_js_object()
    {
        return [
            'ajax_url'       => admin_url( 'admin-ajax.php' ),
            'security_nonce' => wp_create_nonce( 'madmin-admin-bar-security-nonce' ),
            'notice_nonce'   => wp_create_nonce( 'madmin-notice-nonce' ),
        ];
    }
    
    /**
     * Preloader
     *
     * @return void
     */
    public function admin_topbar_loader_css()
    {
        // $output_css = '';
        // $topbar_wireframe_img = WP_MADMIN_ASSETS_IMAGE . 'topbar-wireframe.svg';
        // $output_css .= '.js .ma_admin-topbar-loader{background: url(' . esc_url( $topbar_wireframe_img ) . '); }';
        // echo  '<style>' . wp_strip_all_tags( $output_css ) . '</style>' ;
    }
    
    /**
     * Save Color Mode by Ajax
     *
     * @return void
     */
    public function wp_madmin_color_mode()
    {
        
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX && check_ajax_referer( 'madmin-admin-bar-security-nonce', 'security' ) > 0 ) {
            $admin_bar_mode = AdminSettings::get_instance()->get();
            
            if ( !empty($_POST['key']) ) {
                $key = sanitize_key( $_POST['key'] );
                $value = Utils::clean_ajax_input( wp_unslash( $_POST['value'] ) );
                
                if ( $key == '' ) {
                    $message = __( 'No Color Mode supplied to save', 'madmin' );
                    echo  Utils::ajax_error_message( $message ) ;
                    die;
                }
            
            }
            
            // Light/Dark Mode
            
            if ( $key === 'color_mode' ) {
                $admin_bar_mode['admin_bar_mode'] = $value;
                $admin_bar_mode['enable_schedule_dark_mode'] = false;
                update_option( '_wpmadmin', $admin_bar_mode );
                die;
            }
            
            // Screen Options, Help Tabs and WP Hide Links
            
            if ( $key === 'screen_options_tab' || $key === 'hide_wp_links' || $key === 'madmin_help_tab' ) {
                $userid = get_current_user_id();
                $current = get_user_meta( $userid, '_wpmadmin_preferences', true );
                
                if ( is_array( $current ) ) {
                    $current[$key] = $value;
                } else {
                    $current = [];
                    $current[$key] = $value;
                }
                
                $state = update_user_meta( $userid, '_wpmadmin_preferences', $current );
                
                if ( $state ) {
                    $returndata = [];
                    $returndata['success'] = true;
                    $returndata['message'] = __( 'Preferences saved', 'madmin' );
                    echo  json_encode( $returndata ) ;
                } else {
                    $message = __( 'Unable to save user preferences', 'madmin' );
                    echo  Utils::ajax_error_message( $message ) ;
                    die;
                }
            
            }
        
        }
    
    }
    
    /**
     * Search Everything
     *
     * @return void
     */
    public function madmin_all_search()
    {
        
        if ( defined( 'DOING_AJAX' ) && DOING_AJAX && check_ajax_referer( 'madmin-admin-bar-security-nonce', 'security' ) > 0 ) {
            if ( !empty($_POST['search']) ) {
                $term = sanitize_text_field( wp_unslash( $_POST['search'] ) );
            }
            // Search Arguments
            $args = [
                'numberposts' => -1,
                's'           => $term,
                'post_status' => [
                'publish',
                'pending',
                'draft',
                'future',
                'private',
                'inherit'
            ],
            ];
            // All Post Types
            $post_types = $this->get_post_types();
            foreach ( $post_types as $type ) {
                $args['post_type'][] = $type->name;
            }
            // All Categories/Taxonomies
            $all_taxonomies = get_taxonomies();
            // Get Comments
            $all_comments = get_comments();
            // Get All Users
            $all_users = get_users();
            // All Users
            // $blogusers = get_users();
            // foreach ($blogusers as $type) {
            // $name = $type->user_login;
            // $id = $type->ID;
            // $args['author__in'][] = $type->ID;
            // }
            // // All Menus
            // $all_admin_menus = self::get_wp_admin_menus();
            // All Plugins
            if ( !function_exists( 'get_plugins' ) ) {
                require_once ABSPATH . 'wp-admin/includes/plugin.php';
            }
            $all_plugins = get_plugins();
            $foundposts = get_posts( $args );
            // $count_items = '';
            // if (count($foundposts) > 0) {
            // $count_items .= count($foundposts);
            // } elseif (count($all_plugins)) {
            // $count_items .= count($all_plugins);
            // }
            ob_start();
            ?>

			<p><span class="count"></span><?php 
            echo  count( $foundposts ) . wp_kses_post( ' item<span>s</span> found' ) ;
            ?></p>

			<table class="top-header-result-table" style="height:500px;">
				<thead>
					<tr class="has-text-left">
						<th><?php 
            esc_html_e( 'Title', 'madmin' );
            ?></th>
						<th><?php 
            esc_html_e( 'Type', 'madmin' );
            ?></th>
						<th><?php 
            esc_html_e( 'User', 'madmin' );
            ?></th>
						<th><?php 
            esc_html_e( 'Date', 'madmin' );
            ?></th>
					</tr>
				</thead>
				<tbody>

				<?php 
            foreach ( $foundposts as $item ) {
                $author_id = $item->post_author;
                $editurl = get_edit_post_link( $item );
                $public = get_permalink( $item );
                ?>
						<tr>
							<td><span class="table-title"><a href="<?php 
                echo  esc_url( $editurl ) ;
                ?>"><?php 
                echo  wp_kses_post( get_the_title( $item ) ) ;
                ?></a></span></td>
							<td><span class="type"><?php 
                echo  wp_kses_post( get_post_type( $item ) ) ;
                ?></span></td>
							<td><span class="user"><?php 
                echo  wp_kses_post( the_author_meta( 'user_login', $author_id ) ) ;
                ?></span></td>
							<td><span class="date"><?php 
                echo  wp_kses_post( get_the_date( get_option( 'date_format' ), $item ) ) ;
                ?></span></td>
						</tr>
					<?php 
            }
            ?>

					<?php 
            foreach ( $all_taxonomies as $tax_name ) {
                $terms = get_terms( [
                    'taxonomy'   => $tax_name,
                    'hide_empty' => 1,
                ] );
                foreach ( $terms as $cat ) {
                    if ( strpos( strtolower( $cat->name ), strtolower( $term ) ) === false ) {
                        continue;
                    }
                    // $user = get_userdata($cat->term_id);
                    ?>
							<tr>
								<td>
									<span class="table-title">
										<a href="<?php 
                    echo  esc_url( get_term_link( $cat->slug, $cat->taxonomy ) ) ;
                    ?>">
											<?php 
                    echo  esc_html( $cat->name ) ;
                    ?>
										</a>
									</span>
								</td>
								<td>
									<span class="type"><?php 
                    echo  esc_html( $cat->taxonomy ) ;
                    ?></span>
								</td>
								<td>
									<span class="user">
										<?php 
                    esc_html_e( 'N/A', 'madmin' );
                    ?>
									</span>
								</td>
								<td>
									<span class="date">
										<?php 
                    esc_html_e( 'N/A', 'madmin' );
                    ?>
									</span>
								</td>
								<td>
								</td>
							</tr>
							<?php 
                }
            }
            ?>


					<!-- Get Comments  -->
					<?php 
            foreach ( $all_comments as $comment ) {
                if ( strpos( strtolower( $comment->comment_content ), strtolower( $term ) ) === false ) {
                    continue;
                }
                ?>
						<tr>
							<td>
								<span class="table-title">
									<a href="<?php 
                echo  esc_url_raw( admin_url( 'comment.php?action=editcomment&c=' . esc_attr( $comment->comment_ID ) ) ) ;
                ?>">
										<?php 
                echo  wp_kses_post( $comment->comment_content ) ;
                ?>
									</a>
								</span>
							</td>
							<td>
								<span class="type">
									<?php 
                echo  wp_kses_post( ucwords( $comment->comment_type ) ) ;
                ?>
								</span>
							</td>
							<td>
								<span class="user">
									<?php 
                echo  wp_kses_post( the_author_meta( 'display_name', $comment->user_id ) ) ;
                ?>
								</span>
							</td>
							<td>
								<span class="date">
									<?php 
                echo  esc_html( get_the_date( get_option( 'date_format' ), $comment->comment_date ) ) ;
                ?>
								</span>
							</td>
						</tr>
					<?php 
            }
            ?>



					<!-- Get Users  -->
						<?php 
            foreach ( $all_users as $user ) {
                if ( strpos( strtolower( $user->user_login ), strtolower( $term ) ) === false ) {
                    continue;
                }
                ?>
						<tr>
							<td>
								<span class="table-title">
									<a href="<?php 
                echo  esc_url( admin_url( 'user-edit.php?user_id=' . $user->ID ) ) ;
                ?>">
										<?php 
                echo  esc_html( $user->display_name ) ;
                ?>
									</a>
								</span>
							</td>
							<td>
								<span class="type">
									<?php 
                esc_html_e( 'User', 'madmin' );
                ?>
								</span>
							</td>
							<td>
								<span class="user">
									<?php 
                echo  wp_kses_post( the_author_meta( 'display_name', $user->ID ) ) ;
                ?>
								</span>
							</td>
							<td>
								<span class="date">
									<?php 
                echo  wp_kses_post( get_the_date( get_option( 'date_format' ), $user->user_registered ) ) ;
                ?>
								</span>
							</td>
						</tr>
						<?php 
            }
            ?>



					<!-- Get Plugins  -->
						<?php 
            foreach ( $all_plugins as $plugin ) {
                if ( strpos( strtolower( $plugin['Name'] ), strtolower( $term ) ) === false ) {
                    continue;
                }
                ?>
						<tr>
							<td>
								<span class="table-title">
									<a href="<?php 
                echo  esc_url( admin_url( 'plugins.php' ) ) ;
                ?>">
										<?php 
                echo  esc_html( $plugin['Name'] ) ;
                ?>
									</a>
								</span>
							</td>
							<td>
								<span class="type">
									<?php 
                esc_html_e( 'Plugin', 'madmin' );
                ?>
								</span>
							</td>
							<td>
								<span class="user">
									<?php 
                echo  esc_html( $plugin['AuthorName'] ) ;
                ?>
								</span>
							</td>
							<td>
								<span class="date">
									<?php 
                esc_html_e( 'N/A', 'madmin' );
                ?>
								</span>
							</td>
						</tr>
						<?php 
            }
            ?>


				</tbody>
			</table>


				<?php 
            $output_data = ob_get_clean();
            echo  json_encode( $output_data ) ;
        }
        
        die;
    }
      
    // Admin Bar Body Class
    public function admin_bar_body_class( $classes )
    {
        
        if ( is_admin() ) {
            $classes .= ' ma_admin-admin-bar';
            $admin_bar_position = ( !empty($this->options['admin_bar_position']) ? $this->options['admin_bar_position'] : 'top' );
            
            if ( $admin_bar_position === 'top' ) {
                $classes .= ' position-top';
            } elseif ( $admin_bar_position === 'bottom' ) {
                $classes .= ' position-bottom';
            }
            
            if ( !empty($this->options['enable_admin_bar']) ) {
                $classes .= ' topbar-disabled';
            }
        } else {
            global  $pagenow ;
            if ( !is_user_logged_in() && $pagenow != 'wp-login.php' ) {
                return $classes;
            }
            $classes[] = 'ma_admin-admin-bar';
            $admin_bar_position = ( !empty($this->options['admin_bar_position']) ? $this->options['admin_bar_position'] : 'top' );
            
            if ( $admin_bar_position === 'top' ) {
                $classes[] = 'admin-bar-position-top';
            } elseif ( $admin_bar_position === 'bottom' ) {
                $classes[] = 'admin-bar-position-bottom';
            }
            
            if ( !empty($this->options['enable_admin_bar']) ) {
                $classes[] = 'topbar-disabled';
            }
            $classes[] = 'layout-menu-fixed';
        }
        
        return $classes;
    }
    
    public function get_post_types()
    {
        
        if ( is_array( $this->post_types ) ) {
            return $this->post_types;
        } else {
            $args = [
                'public' => true,
            ];
            $output = 'objects';
            $post_types = get_post_types( $args, $output );
            $this->post_types = $post_types;
            return $post_types;
        }
    
    }
    
    public function ma_admin_add_admin_bar()
    {
        // For Testing Admin Bar on Setup Wizard
        // add_action('admin_head', [$this, 'ma_admin_render_admin_bar']);
        add_action( 'in_admin_header', [ $this, 'ma_admin_render_admin_bar' ], -999999999 );
        // on frontend area
        add_action( 'wp_head', [ $this, 'ma_admin_render_admin_bar' ] );
    }
    
    public function ma_admin_render_admin_bar()
    {
        global  $pagenow ;
        if ( !is_user_logged_in() && $pagenow != 'wp-login.php' ) {
            return;
        }
        if ( !is_admin_bar_showing() ) {
            return false;
        }
        global  $wp_admin_bar ;
        if ( empty($wp_admin_bar) ) {
            return false;
        }
        $admin_bar_mode = 'light';
        $admin_bar_mode = ( empty($admin_bar_mode['admin_bar_mode']) ? 'light' : $admin_bar_mode['admin_bar_mode'] );
        // Light/Dark Mode
        $enable_dark_mode = $admin_bar_mode != 'light';
        // Screen Option && Hide WP Links
        $enable_screen_tab = Utils::get_user_preference( 'screen_options_tab' );
        $enable_help_tab = Utils::get_user_preference( 'madmin_help_tab' );
        // $enable_hide_wp_links = Utils::get_user_preference( 'hide_wp_links' );
        // Admin Bar Position
        
        if ( !empty($this->options['admin_bar_position']) === 'top' ) {
            $admin_bar_position = 'top_bar';
        } elseif ( !empty($this->options['admin_bar_position']) === 'bottom' ) {
            $admin_bar_position = 'bottom_bar is-fixed-bottom';
        } else {
            $admin_bar_position = 'top_bar';
        }
        
        $current_user = wp_get_current_user();
        ob_start();
        ?>
        <div id="mo-admin" class="mo-admin">
            <nav class="layout-navbar container-xxl navbar navbar-expand-xl navbar-detached align-items-center bg-navbar-theme">
            <div class="navbar-nav align-items-center">
                <div class="nav-item d-flex align-items-center">
                  <i class="bx bx-search fs-4 lh-0"></i>
                  <input type="text" class="form-control border-0 shadow-none" placeholder="Search..." aria-label="Search...">
                </div>
              </div>
            <!-- Sidebar Toggle (Topbar) -->
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <div class="admin-render">
                <?php echo wp_admin_bar_render(); ?>
            </div>

            <ul class="navbar-nav flex-row align-items-center ms-auto">
                <!-- Place this tag where you want the button to render. -->
                <li class="nav-item lh-1 me-3">
                  <span></span>
                </li>

                <!-- User -->
                <li class="nav-item navbar-dropdown dropdown-user dropdown">
                  <a class="nav-link dropdown-toggle hide-arrow" href="javascript:void(0);" data-bs-toggle="dropdown" aria-expanded="false">
                    <div class="avatar avatar-online">
                    <?php 
                        echo  get_avatar(
                            $current_user->user_email,
                            45,
                            '',
                            '',
                            [
                            'class' => 'w-px-40 h-auto rounded-circle',
                        ]
                        ) ;
                    ?>
                
                    </div>
                  </a>
                  <ul class="dropdown-menu dropdown-menu-end">
                    <li>
                      <a class="dropdown-item" href="#">
                        <div class="d-flex">
                          <div class="flex-shrink-0 me-3">
                            <div class="avatar avatar-online">
                            <?php 
                                echo  get_avatar(
                                    $current_user->user_email,
                                    45,
                                    '',
                                    '',
                                    [
                                    'class' => 'w-px-40 h-auto rounded-circle',
                                ]
                                ) ;
                                ?>
                            </div>
                          </div>
                          <div class="flex-grow-1">
                            <span class="fw-semibold d-block"><?php echo $current_user->display_name; ?></span>
                            <small class="text-muted"><?php echo $current_user->display_name; ?></small>
                          </div>
                        </div>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="<?php echo get_edit_profile_url( $current_user->ID);?>">
                        <i class="bx bx-user me-2"></i>
                        <span class="align-middle">My Profile</span>
                      </a>
                    </li>
                    <li>
                      <a class="dropdown-item" href="<?php echo admin_url( 'options-general.php' ); ?>">
                        <i class="bx bx-cog me-2"></i>
                        <span class="align-middle">Settings</span>
                      </a>
                    </li>
                    <li>
                      <div class="dropdown-divider"></div>
                    </li>
                    <li>
                      <a class="dropdown-item" href="<?php echo wp_logout_url(); ?>">
                        <i class="bx bx-power-off me-2"></i>
                        <span class="align-middle">Log Out</span>
                      </a>
                    </li>
                  </ul>
                </li>
                <!--/ User -->
              </ul>

            </nav>
        </div>
        
		<?php 
        if ( $pagenow === 'index.php' ) {
            include 'dashborad.php';
        }
        
        $output_wp_admin_bar = ob_get_clean();
        echo  Utils::wp_kses_custom( $output_wp_admin_bar ) ;
    }
    
    public function ma_admin_logo()
    {
        global  $wp_admin_bar ;
        $adminurl = get_admin_url();
        $homeurl = $adminurl;
        ?>
        <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?php echo  esc_url( $homeurl ) ;?>">
                <div class="sidebar-brand-icon">
                    <i class="fas fa-fw fa-cog"></i>
                </div>
            <div class="sidebar-brand-text mx-3"><?php echo get_bloginfo(); ?></div>
        </a>
        <?php
    }
    
    /* Remove from the administration bar */
    public function jltma_madmin_remove_admin_bar_menus()
    {
        global  $wp_admin_bar ;
        $restricted_user = ( !empty($this->options['admin_bar_new_button_user_roles']) ? $this->options['admin_bar_new_button_user_roles'] : '' );
        if ( $restricted_user ) {
            
            if ( Utils::restrict_for( $this->options['admin_bar_new_button_user_roles'] ) ) {
                $wp_admin_bar->remove_menu( 'new-content' );
                return;
            }
        
        }
        $wp_admin_bar->remove_menu( 'wp-logo' );
        $wp_admin_bar->remove_menu( 'site-name' );
        $wp_admin_bar->remove_menu( 'updates' );
        $wp_admin_bar->remove_menu( 'menu-toggle' );
    }

}

new AdminBar();


/**
 * Add a new dashboard widget.
 */
function wpdocs_add_dashboard_widgets() {
	wp_add_dashboard_widget( 
        'dashboard_widget', 
        'Example Dashboard Widget', 
        'dashboard_widget_function',
        'column4',  // $context: 'advanced', 'normal', 'side', 'column3', 'column4'
		'core',     // $priority: 'high', 'core', 'default', 'low'
    );
}
add_action( 'wp_dashboard_setup', 'wpdocs_add_dashboard_widgets' );

/**
 * Output the contents of the dashboard widget
 */
function dashboard_widget_function( $post, $callback_args ) {
	esc_html_e( "Hello World, this is my first Dashboard Widget!", "textdomain" );
}