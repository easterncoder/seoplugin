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
		foreach ( array(
			array( 'features', __( 'Features', 'seo-plugin' ), array( '\SEOPlugin\Core\Features', 'controller' ) ),
			array( 'settings', __( 'Settings', 'seo-plugin' ), array( '\SEOPlugin\Core\Settings', 'controller' ) ),
		) as $menu ) {

			// get menu slug and title
			list($menu, $title, $controller) = $menu;

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
				$controller,
			);
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
}
