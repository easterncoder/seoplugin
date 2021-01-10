<?php

namespace SEOPlugin\Core;

class Features {
	/**
	 * Load enabled plugin features
	 *
	 * @wp-hook plugins_loaded
	 */
	static function load_features() {
		$features = self::get_features();
		foreach ( self::get_enabled_features() as $feature ) {
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
	static function get_features() {
		$features = array();
		foreach ( glob( \SEOPlugin\PATH . '/features/*/config.php' ) as $feature ) {
			$name = $id = '';
			require $feature;
			if ( empty( $name ) || empty( $id ) ) {
				continue;
			}
			$path            = dirname( $feature );
			$features[ $id ] = compact( 'name', 'id', 'path' );
		}
		$features = apply_filters( 'seoplugin-features', $features );
		return $features;
	}

  /**
   * Retrieves list of enabled features
   * @return array array of enabled features
   */
	static function get_enabled_features() {
		return (array) get_option( 'seoplugin-enabled-features', array('sample-feature') );
	}
}
