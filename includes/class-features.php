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
		$features = array();
		foreach ( glob( \SEOPlugin\PLUGIN_DIR . '/features/*/config.php' ) as $feature ) {
			$name = $id = '';
			require $feature;
			if ( empty( $name ) || empty( $id ) ) {
				continue;
			}
			$path            = dirname( $feature );
			$features[ $id ] = compact( 'name', 'id', 'path' );
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
		Settings::update( 'enabled-features', $enabled_features );
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
}
