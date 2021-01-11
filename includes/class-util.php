<?php
/**
 * @package SEOPlugin
 */

namespace SEOPlugin\Core;

/**
 * Util class
 */
class Util {
	/**
	 * Redirect method
	 *
	 * @param  string $page Optional page to redirect to. Default $_POST['redirect'] ?? 'seo-plugin/features'
	 * @param  array  $parameters Optional associative array of query parameters
	 */
	static function redirect( $page = '', $parameters = array() ) {
		if ( empty( $page ) ) {
			$page = $_POST['redirect'] ?? 'seo-plugin/features';
		}
		if ( empty( $page ) ) {
			$page = '';
		}

		if ( ! is_array( $parameters ) ) {
			$parameters = array();
		}
		$parameters['page'] = $page;

		if ( wp_redirect( add_query_arg( $parameters, admin_url( 'admin.php' ) ) ) ) {
			exit;
		}
	}

	/**
	 * Sets notices to be displayed by Core\Features::admin_notices()
	 *
	 * @param string  $message     Message to display
	 * @param string  $type        Any of 'success', 'error', 'warning', 'info'. Default 'success'
	 * @param boolean $dismissible Whether to add a dismiss button to the message
	 */
	static function set_notice( $message, $type = 'success', $dismissible = false ) {
		$index                                      = $dismissible ? 'dismissible-notices' : 'notices';
		$_SESSION['seoplugin'][ $index ][ $type ][] = $message;
	}
}
