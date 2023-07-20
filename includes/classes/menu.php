<?php
// no direct access allowed
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

class ShahDash_Menu {

	protected $options;

public function __construct() {

	add_filter( 'admin_body_class', array( $this, 'admin_menu_body_class' ) );
	add_filter( 'parent_file', array( $this, 'render_admin_menu' ), 999 );
	add_action( 'adminmenu', array( $this, 'render_output_madmin_admin_menu' ) );
}

	// Body Class
public function admin_menu_body_class( $classes ) {
	$classes .= ' ma-admin_admin_menu ';
	return $classes ;
}

	/**
	 * Render Admin Menu
	 *
	 * @package WP madmin
	 * @return void
	 */
public function render_admin_menu( $parent_file ) {
	global $menu, $pagenow, $shah_menu;
	$current_user        = wp_get_current_user();
	$this->original_menu = $menu;


	// Disable Default Menu
	$menu = array();
	ob_start();
	?>
		<aside id="layout-menu" class="layout-menu menu-vertical menu bg-menu-theme" data-bg-class="bg-menu-theme">
			<div class="app-brand demo">
				<?php echo get_custom_logo(); ?>
			</div>
			<ul class="menu-inner py-1 ps ps--active-y">
				<?php $this->render_top_level_menu_items( $this->original_menu ); ?>
			</ul>

		</aside>


	<?php
	$shah_menu = ob_get_clean();

	return $parent_file;
}


	/**
	 * Render Top Level Menu Items
	 */

public function render_top_level_menu_items( $the_menu ) {
	global $submenu;
	$this->original_submenu = $submenu;
	$menu_options     = '';
	$x=0;
	foreach ( $the_menu as $menu_item ) {
		$menu_name = $menu_item[0];
		$menu_link = $menu_item[2];
		$divider   = false;

		$disabled_for = array();
		if ( is_array( $menu_options ) ) {
			if ( isset( $menu_options[ $menu_item[2] ] ) ) {
				$optiongroup = $menu_options[ $menu_item[2] ];
				if ( isset( $optiongroup['hidden_for'] ) ) {
					$disabled_for = $optiongroup['hidden_for'];
				}
			}
		}
		$custom_menu = isset( $menu_item[5] ) ? $menu_item[5] : '';
		if ( strpos( $custom_menu, 'admin-custom-menu-' ) !== false ) {
			if ( isset( $menu_options[ $menu_item[5] ]['hidden_for'] ) ) {
				$disabled_for = $menu_options[ $menu_item[5] ]['hidden_for'];
			}
			$disabled_for = array_flip( $disabled_for );
			$disabled_for = array_change_key_case( $disabled_for, CASE_LOWER );
			$disabled_for = array_flip( $disabled_for );
		}

		if ( strpos( $menu_link, 'separator' ) !== false ) {
			$divider = true;
			// $this->render_divider($menu_item);
			continue;
		}

		if ( ! $menu_name ) {
			continue;
		}

		$link = $this->get_menu_link( $menu_item );

		$link_class = '';
		if ( isset( $submenu[ $menu_link ] ) ) {
			$link_class = 'menu-toggle';
			$sub_menu_items = $submenu[ $menu_link ];
			// $link = "#";
		} else {
			$sub_menu_items = false;
		}


		if ( 'edit.php?post_type=elementor_library' === $link ) {
			$link .= '&tabs_group=library';
		}
		$classes = $this->get_menu_clases( $menu_item, $submenu );
        $classes .= ' menu-item';

		?>
			<?php if($x!=0 && $x%4==1){ ?>
				<li class="menu-header small text-uppercase"><span class="menu-header-text">Menu Header</span></li>
			<?php } ?>

			<li class="<?php echo $classes; ?>" id="<?php echo esc_attr( $menu_item[5] ); ?>">
				<a class="menu-link <?php echo $link_class; ?>" href="<?php echo $link; ?>">
				<?php $this->get_icon( $menu_item ); ?>
				<div><?php echo wp_kses_post( $menu_name ); ?></div>
				</a>

			<?php
			if ( is_array( $sub_menu_items ) ) {
				$this->render_sub_level_menu_items( $sub_menu_items, $menu_item, $menu_options );
			}
			?>

			</li>

			<?php

			if ( isset( $menu_item['separator'] ) && $menu_item['separator'] == 1 ) {
				$this->render_divider( $menu_item );
			}
		++$x;
	}
}


	/**
	 * Gets correct classes for top level menu item
	 *
	 * @since 1.4
	 */

public function get_menu_clases( $menu_item, $sub_menu ) {
	$menu_link = $menu_item[2];
	$classes   = $menu_item[4];

	if ( isset( $sub_menu[ $menu_link ] ) ) {
		$classes = ' submenu ';
		$classes .= $this->check_if_active( $menu_item, $sub_menu[ $menu_link ] );
	} else {
		$classes = $this->check_if_single_active( $menu_item );
	}

	return $classes;
}


	/**
	 * Checks if we are on an active link or sub link
	 *
	 * @since 1.4
	 */

public function check_if_active( $menu_item, $sub_menu ) {
	if ( ! is_array( $sub_menu ) ) {
		return '';
	}

	global $pagenow;

    if( isset( $_SERVER['QUERY_STRING'] ) ){
        $currentquery = sanitize_text_field(wp_unslash($_SERVER['QUERY_STRING']));
    }

	if ( $currentquery ) {
		$currentquery = '?' . $currentquery;
	}
	$wholestring = $pagenow . $currentquery;
	$visibility  = 'hidden';
	$open        = '';
	$files       = $this->files;

	foreach ( $sub_menu as $sub ) {
		if ( $sub[2] === 'edit-tags.php?taxonomy=elementor_library_category&amp;post_type=elementor_library' ) {
			$link = 'edit-tags.php?taxonomy=elementor_library_category&post_type=elementor_library';
		} elseif ( $sub[2] === 'edit.php?post_type=elementor_library' ) {
			$link = 'edit.php?post_type=elementor_library';
		} elseif ( ( $sub[2] === 'e-landing-page' ) || ( $sub[2] === 'popup_templates' ) ) {
			$link = 'edit.php?post_type=elementor_library&page=' . esc_attr( $sub[2] );
		} elseif ( $sub[2] === 'updraftplus' ) {
			$link = 'options-general.php?page=' . esc_attr( $sub[2] );
		} elseif ( strpos( $sub[2], '.php' ) !== false ) {
			$link = $sub[2];

			$querypieces = explode( '?', $link );
			$temp        = $querypieces[0];

			if ( ! in_array( $temp, $files ) ) {
				$link = 'admin.php?page=' . esc_attr( $sub[2] );
			}
		} else {
			$link = 'admin.php?page=' . esc_attr( $sub[2] );
		}
		$linkclass = '';
		if ( ( $wholestring == $link ) || ( ( strpos( $wholestring, 'edit.php?post_type=elementor_library' ) !== false ) && ( strpos( $link, 'edit.php?post_type=elementor_library' ) !== false ) ) ) {
			$linkclass  = 'menu-toggle';
			$open       = 'open wp-menu-open active wp-has-current-submenu';
			$visibility = '';
			break;
		}
	}

	return $open;
}



	/**
	 * Render Divider
	 *
	 * @package WP madmin
	 * @return void
	 */
public function render_divider( $divider ) {
	if ( isset( $divider['name'] ) ) {
		?>

			<li class="madmin-nav-header"><?php echo esc_html( $divider['name'] ); ?></li>
			<li class="madmin-nav-divider divider-placeholder"></li>

			<?php

	} else {
		?>

			<li class="madmin-nav-divider"></li>

		<?php
	}
}


	/**
	 * Render Sub Menu Items
	 *
	 * @return void
	 */
public function render_sub_level_menu_items( $sub_menu, $parent_menu, $menu_options ) {
	?>
		<ul class="menu-sub wp-submenu">

		<?php
		foreach ( $sub_menu as $sub_item ) {
			$sub_menu_name = $sub_item[0];
			$sub_menu_link = $sub_item[2];
			$link          = $this->get_menu_link( $sub_item );
			$class         = $this->check_if_single_active( $sub_item );

			$parent_menu_id = preg_replace( '/[^A-Za-z0-9 ]/', '', $sub_menu_link );

			$disabled_for   = array();
			$custom_submenu = isset( $sub_item['key'] ) ? $sub_item['key'] : '';
			if ( strpos( $parent_menu[5], 'admin-custom-menu-' ) !== false ) {
				if ( strpos( $custom_submenu, 'admin-custom-submenu-' ) !== false ) {
					$submenu_options = $menu_options[ $parent_menu[5] ]['submenu'][ $custom_submenu ];
					if ( ! empty( $submenu_options['hidden_for'] ) ) {
						$disabled_for = $submenu_options['hidden_for'];
					}
					$disabled_for = array_flip( $disabled_for );
					$disabled_for = array_change_key_case( $disabled_for, CASE_LOWER );
					$disabled_for = array_flip( $disabled_for );
				}
			}
				?>
				<li class="<?php echo esc_attr( $class ); ?> menu-item">
					<a class="menu-link" href="<?php echo esc_url( $link ); ?>">
					<?php echo wp_kses_post( $sub_menu_name ); ?>
					</a>
				</li>
				<?php
			
		}
		?>

		</ul>
		<?php
}


	/**
	 * Checks if we are on an active link or sub link
	 *
	 * @since 1.4
	 */

public function check_if_single_active( $sub_menu_item ) {
		global $pagenow;

		$currentquery = sanitize_text_field(wp_unslash($_SERVER['QUERY_STRING']));

		if ( $currentquery ) {
			$currentquery = '?' . $currentquery;
		}
		$wholestring = $pagenow . $currentquery;
		$visibility  = 'hidden';
		$open        = '';
		$files       = $this->files;

		if ( $sub_menu_item[ 2 ] === 'edit-tags.php?taxonomy=elementor_library_category&amp;post_type=elementor_library' ) {
			$link = 'edit-tags.php?taxonomy=elementor_library_category&post_type=elementor_library';
		} elseif ( $sub_menu_item[ 2 ] === 'popup_templates' ) {
			$link = 'edit.php?post_type=elementor_library&page=' . esc_attr( $sub_menu_item[ 2 ] );
		} elseif ( $sub_menu_item[ 2 ] === 'updraftplus' ) {
			$link = 'options-general.php?page=' . esc_attr( $sub_menu_item[ 2 ] );
		} elseif ( strpos( $sub_menu_item[ 2 ], '.php' ) !== false ) {
			$link = $sub_menu_item[ 2 ];

			$querypieces = explode( '?', $link );
			$temp        = $querypieces[ 0 ];

			if ( ! in_array( $temp, $files ) ) {
				$link = 'admin.php?page=' . esc_attr( $sub_menu_item[ 2 ] );
			}
		} else {
			$link = 'admin.php?page=' . esc_attr( $sub_menu_item[ 2 ] );
		}

		$linkclass = '';
		if ( $wholestring == $link ) {
			$linkclass = 'active';
		}

		return $linkclass;
	}



	/**
	 * Scans admin directory for menu links
	 *
	 * @since 1.4
	 */
	public function get_admin_files() {
		$absolutepath = ABSPATH . '/wp-admin' . '/';
		$files        = array_diff( scandir( $absolutepath ), array( '.', '..' ) );

		if ( is_multisite() ) {
			$pathtonetwork = ABSPATH . '/wp-admin' . '/network/';
			$networkfiles  = array_diff( scandir( $pathtonetwork ), array( '.', '..' ) );
			$files         = array_merge( $files, $networkfiles );
		}

		return $files;
	}

	/**
	 * Gets correct link for menu item
	 *
	 * @since 1.4
	 */

	public function get_menu_link( $menu_item ) {
		$menu_link   = $menu_item[ 2 ];
		$files       = $this->get_admin_files();
		$this->files = $files;

		if ( $menu_link == 'woocommerce' ) {
			$menu_link = 'wc-admin';
		}

		if ( ( $menu_link === 'e-landing-page' ) || ( $menu_link === 'popup_templates' ) ) {
			$link = 'edit.php?post_type=elementor_library&page=' . esc_attr( $menu_link );
		} elseif ( $menu_link === 'updraftplus' ) {
			$link = 'options-general.php?page=' . esc_attr( $menu_link );
		} elseif ( strpos( $menu_link, 'admin.php' ) !== false ) {
			$link = $menu_link;
		} elseif ( strpos( $menu_link, '.php' ) !== false ) {
			$link = $menu_link;
			if ( strpos( $menu_link, '/' ) !== false ) {
				$pieces = explode( '/', $menu_link );
				if ( strpos( $pieces[ 0 ], '.php' ) !== true || ! file_exists( get_admin_url() . esc_attr( $menu_link ) ) ) {
					$link = 'admin.php?page=' . esc_attr( $menu_link );
				}
			}

			$querypieces = explode( '?', $link );
			$temp        = $querypieces[ 0 ];

			if ( ! in_array( $temp, $files ) ) {
				$link = 'admin.php?page=' . esc_attr( $menu_link );
			}
		} else {
			$link = 'admin.php?page=' . esc_attr( $menu_link );
		}

		if ( strpos( $menu_link, '/wp-content/' ) !== false ) {
			$link = 'admin.php?page=' . esc_attr( $menu_link );
		}

		// CHECK IF INTERNAL URL
		if ( strpos( $menu_link, get_site_url() ) !== false ) {
			$link = $menu_link;
		}

		// CHECK IF EXTERNAL LINK
		if ( strpos( $menu_link, 'https://' ) !== false || strpos( $menu_link, 'http://' ) !== false ) {
			$link = $menu_link;
		}

		return $link;
	}



	/**
	 * Gets top level menu item icon
	 *
	 * @since 1.4
	 */

	public function get_icon( $menu_item ) {

		// LIST OF AVAILABLE MENU ICONS
		$icons = array(
			'dashicons-dashboard'        => 'fas fa-gear',
			'dashicons-admin-post'       => 'fas fa-thumbtack',
			'dashicons-database'         => 'fa-solid fa-database',
			'dashicons-admin-media'      => 'fas fa-icons',
			'dashicons-admin-page'       => 'far fa-copy',
			'dashicons-admin-comments'   => 'fa-regular fa-message',
			'dashicons-admin-appearance' => 'fa-solid fa-brush',
			'dashicons-admin-plugins'    => 'fa-solid fa-plug',
			'dashicons-admin-users'      => 'fa-regular fa-user',
			'dashicons-admin-tools'      => 'fa-solid fa-wrench',
			'dashicons-chart-bar'        => 'fa-solid fa-chart-bar',
			'dashicons-admin-settings'   => 'fa-solid fa-sliders',
			'dashicons-megaphone'        => 'fa-solid fa-bullhorn',
			'dashicons-archive'          => 'fa-solid fa-box-archive',
			'dashicons-admin-generic'    => 'fa-brands fa-elementor',
			'default-icon'               => 'fa-brands fa-wordpress-simple'
		);

		// SET MENU ICON
		$theicon = '';
		// $wpicon = $menu_item;
		$wpicon = ( isset( $menu_item ) ) ? $menu_item[ 6 ] : '';
   
		if ( isset( $icons[ $wpicon ] ) ) {
			?>
			<i class="menu-icon bx bx-layout <?php echo esc_attr( $icons[ $wpicon ] ); ?>"></i>
			<?php
			return;
		}
		if ( ! $theicon ) {
			if ( strpos( $wpicon, 'http' ) !== false || strpos( $wpicon, 'data:' ) !== false ) {
				// ICON IS IMAGE && BASE64
				$class = '';
				// ICON IS BASE64
				if ( strpos( $wpicon, 'data:' ) !== false ) {
					$deocded_bas64 = base64_decode( explode( ',', $wpicon )[1] );
				}
				// ICON IS IMAGE
				if ( strpos( $wpicon, 'http' ) !== false ) {
					$class  = 'svg-image-icon';
					$wpicon = ( strpos( $wpicon, ',' ) !== false ) ? explode( ',', $wpicon )[1] : $wpicon;
				}
				?>
				<span class="menu-icon bx-layout <?php echo esc_attr( $class ); ?>"
					<?php
					if ( strpos( $wpicon, 'data:' ) === false ) {
						?>
						style="background-image: url(<?php echo esc_url( $wpicon ); ?>);" <?php } ?>>
					<?php
					if ( strpos( $wpicon, 'data:' ) !== false ) {
						echo Utils::wp_kses_custom( $deocded_bas64 );
					}
					?>
				</span>
				<?php
			} else {

				// ICON IS ::BEFORE ELEMENT
				if ( empty( $wpicon ) ) {
					$wpicon = '';
					?>
						<i class="menu-icon bx bx-layout <?php echo esc_attr( $icons[ $wpicon ] ); ?>"></i>
					<?php
				} else {
					$wpicon = ( strpos( $wpicon, 'dashicons-' ) !== false ) ? 'dashicons-before ' . $wpicon : $wpicon;
					?>
					   <i class="menu-icon bx bx-layout <?php echo esc_attr( $wpicon  ); ?>"></i>
					<?php
				}
			}
		}
	}


	/**
	 * Render Output Admin Menu
	 *
	 * @return void
	 */
	public function render_output_madmin_admin_menu() {
		global $menu, $submenu, $shah_menu;
		echo Utils::wp_kses_custom( $shah_menu );
		$menu    = $this->original_menu;
		$submenu = $this->original_submenu;
	}
}
