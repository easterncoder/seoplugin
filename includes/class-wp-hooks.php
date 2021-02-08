<?php
/**
 * @package SEOPlugin
 */

namespace SEOPlugin\Core;

/**
 * WP_Hooks class
 * Handles registration of WordPress hooks (actions and filters)
 */
class WP_Hooks {
	/**
	 * Register hooks
	 *
	 * @param  string $file Plugin file
	 */
	static function initialize() {
		register_activation_hook( \SEOPlugin\PLUGIN_FILE, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( \SEOPlugin\PLUGIN_FILE, array( __CLASS__, 'deactivate' ) );
		add_action( 'admin_init', array( __CLASS__, 'start_session' ) );
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
		add_action( 'admin_notices', array( __CLASS__, 'admin_notices' ) );
		add_action( 'admin_enqueue_scripts', array( __CLASS__, 'enqueue_scripts' ) );

		add_action( 'admin_action_seoplugin-toggle-features', array( '\SEOPlugin\Core\Features', 'toggle_features' ) );
		add_action( 'admin_action_seoplugin-save-settings', array( '\SEOPlugin\Core\Settings', 'save_settings' ) );
	}

	/**
	 * Plugin activation routine
	 *
	 * @wp-hook register_activation_hook
	 */
	static function activate() {
		Database::initialize();
	}

	/**
	 * Plugin deactivation routine
	 *
	 * @wp-hook register_deactivation_hook
	 */
	static function deactivate() {}

	/**
	 * Start a PHP session
	 *
	 * @wp-hook admin_init
	 */
	static function start_session() {
		if ( session_status() == PHP_SESSION_NONE ) {
			session_start();
		}
	}

	/**
	 * Add our menu items
	 *
	 * @wp-hook admin_menu
	 */
	static function admin_menu() {
		$main_title = \SEOPlugin\NAME;
		
		$menus = array(
			'features' => array( 'features', __( 'Features', 'seo-plugin' ), array( '\SEOPlugin\Core\Features', 'controller' ) ),
			'settings' => array( 'settings', __( 'Settings', 'seo-plugin' ), array( '\SEOPlugin\Core\Settings', 'controller' ) ),
		);
		// filter menus
		$menus = apply_filters( 'seoplugin-menu-items', $menus );

		while ( $menu = array_shift( $menus ) ) {
			// get menu slug and title
			list($menu, $title, $controller) = $menu;
			
			// filter submenus
			$children = apply_filters( 'seoplugin-submenu-items', array(), $menu );

			// first menu item is also the top level menu
			if ( empty( $top_menu ) ) {
				$top_menu = 'seo-plugin/' . $menu;
				add_menu_page(
					$main_title . ' | ' . $title,
					$main_title,
					'manage_options',
					$top_menu,
					$controller,
					$icon
				);
			}

			// add sub-menu
			add_submenu_page(
				$top_menu,
				$main_title . ' | ' . $title,
				$title,
				'manage_options',
				'seo-plugin/' . $menu,
				$controller
			);
			
			if( $children ) {
				$menus = array_merge( $children, $menus );
			}
		}
	}

	/**
	 * Displays notices added via Core\Util::set_notice()
	 *
	 * @wp-hook admin_notices
	 */
	static function admin_notices() {
		if ( is_array( $_SESSION['seoplugin'] ?? '' ) && is_array( $_SESSION['seoplugin']['notices'] ?? '' ) ) {
			foreach ( $_SESSION['seoplugin']['notices'] as $type => $messages ) {
				foreach ( (array) $messages as $message ) {
					printf( '<div class="notice notice-%s"><p>%s</p></div>', $type, $message );
				}
			}
		}
		$_SESSION['seoplugin']['notices'] = array();

		if ( is_array( $_SESSION['seoplugin'] ?? '' ) && is_array( $_SESSION['seoplugin']['dismissible-notices'] ?? '' ) ) {
			foreach ( $_SESSION['seoplugin']['dismissible-notices'] as $type => $messages ) {
				foreach ( (array) $messages as $message ) {
					printf( '<div class="notice notice-%s is-dismissible"><p>%s</p></div>', $type, $message );
				}
			}
		}
		$_SESSION['seoplugin']['dismissible-notices'] = array();
	}
	
	/**
	 * Enqueues admin scripts and styles
	 *
	 * @wp-hook admin_enqueue_scripts
	 * 
	 * @param  string $hook Current admin page
	 */
	static function enqueue_scripts( $hook ) {
		if( !preg_match( '#^(seo-plugin_page_seo-plugin/|toplevel_page_seo-plugin/)(.*)$#', $hook, $path ) ) {
			// only on our screens
			return;
		}
		// WP's thickbox
		add_thickbox();
		
		$path = explode( '/', $path[2] );
		
		// load feature specific css and js found in feature's assets folder
		if( $path[0] == 'features' && !empty( $path[1] ) ) {
			$features = \SEOPlugin\Core\Features::get();
			if( isset( $features[$path[1]] ) ) {
				// js
				foreach( glob( $features[$path[1]]['path'] . '/assets/*.js' ) as $js ) {
					wp_enqueue_script( str_replace( array( dirname( \SEOPlugin\PLUGIN_DIR ) . '/', '/', '.js' ), array( '', '-', '' ), $js ), plugins_url( basename( $js ), $js ), array(), \SEOPlugin\VERSION );
				}
				// css
				foreach( glob( $features[$path[1]]['path'] . '/assets/*.css' ) as $css ) {
					wp_enqueue_style( str_replace( array( dirname( \SEOPlugin\PLUGIN_DIR ) . '/', '/', '.css' ), array( '', '-', '' ), $css ), plugins_url( basename( $css ), $css ), array(), \SEOPlugin\VERSION );
				}
			}
		}
	}
}
