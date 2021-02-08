<?php
/**
 * @package SEOPlugin
 */

namespace SEOPlugin\Core;

/**
 * Features class
 * Handles retrieving, listing, enabling, disabling and loading of features
 */
class Features {
	/**
	 * Features initialization
	 */
	static function initialize() {
		self::load();
	}
	/**
	 * Load enabled features
	 *
	 * @wp-hook plugins_loaded
	 *
	 * @param string|array $features Optional. feature/s load. If not provided, will load all enabled features.
	 */
	static function load( $features = null ) {
		$features = $features ? (array) $features : self::get();
		foreach ( self::get_enabled() as $feature ) {
			$feature = trim( $feature );
			if ( ! $feature || empty( $features[ $feature ] ) ) {
				continue;
			}
			$feature = $features[ $feature ]['path'] ?: '';
			if ( $feature && file_exists( $feature . '/main.php' ) ) {
				require_once $feature . '/main.php';
			}
		}
	}

	/**
	 * Get list of all features
	 * Results can be filtered with the `seoplugin-features` filter
	 *
	 * @return array associative array of all valid features in the features
	 */
	static function get() {
		static $features = array();
		if( !empty( $features ) ) {
			return $features;
		}
		foreach ( glob( \SEOPlugin\PLUGIN_DIR . '/features/*/config.php' ) as $feature ) {
			$name = $id = $description = '';
			require $feature;
			if ( empty( $name ) || empty( $id ) ) {
				continue;
			}
			$path            = dirname( $feature );
			$features[ $id ] = compact( 'name', 'id', 'path', 'description' );
		}
		/**
		 * Filters the features array
		 *
		 * @param array $features Associative array of feature data
		 */
		$features = apply_filters( 'seoplugin-features', $features );
		return $features;
	}

	/**
	 * Retrieves list of enabled features
	 *
	 * @return array array of enabled features
	 */
	static function get_enabled() {
		return (array) Settings::get( 'enabled-features', array( 'sample-feature' ) );
	}

	/**
	 * Features toggle form handler
	 *
	 * @wp-hook admin_action_seoplugin-toggle-features
	 */
	static function toggle_features() {
		$enabled_features   = self::get_enabled();
		$requested_features = (array) $_POST['enabled_features'] ?? array();

		// enable requested features that are not yet enabled
		foreach ( $requested_features as $feature ) {
			if ( ! in_array( $feature, $enabled_features ) ) {
				self::toggle( $feature, true );
			}
		}

		// disable enabled features that were not requested
		foreach ( $enabled_features as $feature ) {
			if ( ! in_array( $feature, $requested_features ) ) {
				self::toggle( $feature, false );
			}
		}

		Util::set_notice( __( 'Settings saved.', 'seo-plugin' ), 'success' );
		Util::redirect();

	}

	/**
	 * Toggles a feature's state
	 *
	 * @param  string  $feature Feature ID
	 * @param  boolean $state   Feature's enabled state
	 */
	static function toggle( $feature, $state ) {
		$enabled_features = self::get_enabled();
		if ( $state ) {
			$enabled_features[] = $feature;
		} else {
			$enabled_features = array_diff( $enabled_features, array( $feature ) );
		}
		Settings::update( 'enabled-features', array_values( array_unique( $enabled_features ) ) );
		if ( $state ) {
			self::load( $feature );
			/**
			 * Fires once a feature is enabled
			 */
			do_action( 'seoplugin-enable-feature-' . $feature );

			/**
			 * Fires once a feature is enabled
			 *
			 * @param string $feature Feature ID
			 */
			do_action( 'seoplugin-enable-feature', $feature );
		} else {
			/**
			 * Fires once a feature is disabled
			 */
			do_action( 'seoplugin-disable-feature-' . $feature );

			/**
			 * Fires once a feature is disabled
			 *
			 * @param string $feature Feature ID
			 */
			do_action( 'seoplugin-disable-feature', $feature );
		}
	}

	/**
	 * Loads all necessary data then loads the features admin UI
	 */
	static function controller() {
		$features         = self::get();
		$enabled_features = self::get_enabled();
		require \SEOPlugin\PLUGIN_DIR . '/admin/features.php';
	}
}
