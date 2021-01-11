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
			array( 'features', __( 'Features', 'seo-plugin' ) ),
			array( 'settings', __( 'Settings', 'seo-plugin' ) ),
		) as $menu ) {

			// get menu slug and title
			list($menu, $title) = $menu;

			// first menu item is also the top level menu
			if ( empty( $top_menu ) ) {
				$top_menu = 'seo-plugin/' . $menu;
				add_menu_page(
					$main_title . ' | ' . $title,
					$main_title,
					'manage_options',
					$top_menu,
					array( __CLASS__, 'admin_screen' ),
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
				array( __CLASS__, 'admin_screen' )
			);

		}

	}

	/**
	 * Load admin view for requested menu item
	 * Loaded via `add_menu_page()` and `add_submenu_page()`
	 */
	static function admin_screen() {
		// generate admin view's file path
		$page = \SEOPlugin\PLUGIN_DIR . '/admin/' . preg_replace( array( '#^seo-plugin/#', '#\.#' ), '', $_GET['page'] ?: '' ) . '.php';

		if ( file_exists( $page ) && is_file( $page ) ) {
			// get base part of view
			$view = preg_replace( '#\.php$#', '', basename( $page ) );

			// view wrapper div
			echo '<div class="wrap">';

			/**
			 * Fires before the admin view is loaded
			 */
			do_action( 'seoplugin-before-admin-' . $view );

			/**
			 * Fires before the admin view is loaded
			 *
			 * @param string $view
			 */
			do_action( 'seoplugin-before-admin', $view );

			// load the view
			require $page;

			/**
			 * Fires after the admin view is loaded
			 */
			do_action( 'seoplugin-after-admin-' . $view );

			/**
			 * Fires after the admin view is loaded
			 *
			 * @param string $view
			 */
			do_action( 'seoplugin-after-admin', $view );

			// close view wrapper div
			echo '</div>';
		}

	}

}
