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
		$option       = $arguments[0];
		$arguments[0] = self::prefix . $arguments[0];

		if ( $function_name != 'get' && $function_name != 'get_site' ) {
			// run action when settings have been changed
			do_action( 'seoplugin-settings-change', $option );			
			
			// json_encode non-string and non-numeric data
			if( !is_string( $arguments[1]) && !is_numeric( $arguments[1] ) ) {
				$arguments[1] = json_encode( $arguments[1] );
			}
		}

		$result = call_user_func_array( $function_name . '_option', $arguments );
		
		// attempt to json_decode result from get_ or and get_site_ calls
		if( $function_name == 'get' || $function_name == 'get_site' ) {
			// return non-string results as-is
			if( !is_string( $result ) ) {
				return $result;
			}
			
			// attempt to json_decode result
			switch( $result ) {
				case 'null':
					$result = null;
					break;
				case 'true':
					$result = true;
					break;
				case 'false':
					$result = false;
					break;
				default:
					$result = json_decode( $result, true ) ?: $result;
			}
		}
		
		return $result;
	}

	/**
	 * Settings form handler
	 *
	 * @wp-hook admin_action_seoplugin-save-settings
	 */
	static function save_settings() {
		foreach ( $_POST['settings'] ?? array() as $setting => $value ) {
			self::update( $setting, $value );
		}
		Util::set_notice( __( 'Settings saved.', 'seo-plugin' ), 'success' );
		Util::redirect();
	}

	/**
	 * Loads all necessary data then loads the settings admin UI
	 */
	static function controller() {
		require \SEOPlugin\PLUGIN_DIR . '/admin/settings.php';
	}

}
