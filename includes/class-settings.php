<?php
/**
 * @package SEOPlugin
 */

namespace SEOPlugin\Core;

/**
 * Setting class
 * Provides WordPress Options API wrapper methods that
 * prepends the value of self::prefix to the option name
 *
 * See https://codex.wordpress.org/Options_API for documentation
 */
class Settings {
	/**
	 * Prefix for all our options
   *
	 * @var string
	 */
	const prefix = 'seo-plugin-';

	/**
	 * Wrapper function for WP's add_option() function
	 */
	static function add() {
		return self::_call_wp( __FUNCTION__, func_get_args() );
	}
	
  /**
	 * Wrapper function for WP's add_site_option() function
	 */
	static function add_site() {
		return self::_call_wp( __FUNCTION__, func_get_args() );
	}
	
  /**
	 * Wrapper function for WP's delete_option() function
	 */
	static function delete() {
		return self::_call_wp( __FUNCTION__, func_get_args() );
	}
	
  /**
	 * Wrapper function for WP's delete_site_option() function
	 */
	static function delete_site() {
		return self::_call_wp( __FUNCTION__, func_get_args() );
	}
	
  /**
	 * Wrapper function for WP's get_option() function
	 */
	static function get() {
		return self::_call_wp( __FUNCTION__, func_get_args() );
	}
	
  /**
	 * Wrapper function for WP's get_site_option() function
	 */
	static function get_site() {
		return self::_call_wp( __FUNCTION__, func_get_args() );
	}
	
  /**
	 * Wrapper function for WP's update_option() function
	 */
	static function update() {
		return self::_call_wp( __FUNCTION__, func_get_args() );
	}
	
  /**
	 * Wrapper function for WP's update_site_option() function
	 */
	static function update_site() {
		return self::_call_wp( __FUNCTION__, func_get_args() );
	}

  /**
	 * Calls the relevant WP Options API function
	 * - Prepends self::prefix to the option name ($arguments[0]);
	 * - Calls {$function_name}_option()
	 *
	 * @param  string $function_name  The name of the calling function
	 * @param  array  $arguments      function Arguments
	 * @return mixed                  Whatever is returned by the called WP Options API function
	 */
	protected static function _call_wp( $function_name, $arguments ) {
		$arguments[0] = self::prefix . $arguments[0];
		return call_user_func_array( $function_name . '_option', $arguments );
	}

}