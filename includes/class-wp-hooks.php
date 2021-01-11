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
		add_action( 'admin_menu', array( __CLASS__, 'admin_menu' ) );
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
	 * Add our menu items
	 *
	 * @wp-hook admin_menu
	 */
	static function admin_menu() {
		/**
		 * Main plugin title
		 *
		 * @var [type]
		 */
		$main_title = \SEOPlugin\NAME;
		foreach ( array(
			array( 'features', __( 'Features', 'seo-plugin' ), ['\SEOPlugin\Core\Features', 'controller'] ),
			array( 'settings', __( 'Settings', 'seo-plugin' ), ['\SEOPlugin\Core\Settings', 'controller'] ),
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
}
