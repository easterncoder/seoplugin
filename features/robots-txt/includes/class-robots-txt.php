<?php

namespace SEOPlugin\Feature\Robots_Txt;

/**
 * Robots.txt
 */
class Robots_Txt {
	/**
	 * Return default robots.txt data
	 * @return array
	 */
	static function get_default() {
		return array(
			array(
				'user-agent' => '*',
				'disallow' => '/wp-admin/',
				'allow' => '/wp-admin/admin-ajax.php',
				'crawl-delay' => '0',
				'include-sitemap-index' => 'true',
			),
		);
	}

	/**
	 * Return robots.txt settings
	 * @return array
	 */
	static function get() {
		return \SEOPlugin\Core\Settings::get( 'robots-txt', self::get_default() );		
	}
	
	/**
	 * Reset robots.txt to default
	 */
	static function reset() {
		\SEOPlugin\Core\Settings::delete( 'robots-txt' );
	}
}

