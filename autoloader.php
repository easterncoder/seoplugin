<?php
/**
 * @package seo-plugin
 */

defined( 'ABSPATH' ) || die();

/**
 * Auto load classes in:
 * - includes/
 * - features/feature-name/includes/
 *
 * Expects class file to be class-*.php
 * Underscores in namespaces are replaced with hyphens when mapping to the file path
 */
spl_autoload_register(
	function( $class ) {
		// explode namespaces
		$parts = explode( '\\', $class );

		// last entry in array is the class name
		$class = array_pop( $parts );

		// abort if first entry in array is not SEOPlugin
		if ( array_shift( $parts ) != 'SEOPlugin' ) {
			return;
		}

		// check which part of our code needs to be loaded
		switch ( array_shift( $parts ) ) {
			// Features...
			// called as `new SEOPlugin\Feature\[Feature-Name]\[Optional-Path]\ClassName`
			// mapped to `features/feature-name/includes/optional-path/class-classname.php`
			case 'Feature':
				$base_path = 'features/' . array_shift( $parts ) . '/includes/' . ( $parts ? implode( '/', $parts ) . '/' : '' );
				break;

			// Core...
			// called as `new SEOPlugin\Core\[Optional-Path]\ClassName`
			// mapped to `includes/optional-path/class-classname.php`
			case 'Core':
				$base_path = 'includes/' . ( $parts ? implode( '/', $parts ) . '/' : '' );
				break;
		}

		// abort if no $base_path defined
		if ( empty( $base_path ) ) {
			return;
		}

		// generate full path to class-*.php and replace _ with -
		$path = str_replace( '_', '-', strtolower( $base_path . 'class-' . $class . '.php' ) );

		// require the path
		require_once $path;
	}
);


