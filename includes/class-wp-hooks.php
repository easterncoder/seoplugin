<?php
namespace SEOPlugin\Core;

class WP_Hooks {
	static function initialize( $file ) {
		register_activation_hook( $file, array( __CLASS__, 'activate' ) );
		register_deactivation_hook( $file, array( __CLASS__, 'deactivate' ) );

		add_action( 'plugins_loaded', array( __NAMESPACE__ . '\Features', 'load_features' ) );
	}

  /**
   * Plugin activation routine
   * @wp-hook register_activation_hook
   */
	static function activate() {
		SEOPlugin\Core\Database::initialize();
	}

  /**
   * Plugin deactivation routine
   * @wp-hook register_deactivation_hook
   */
	static function deactivate() {}

}
