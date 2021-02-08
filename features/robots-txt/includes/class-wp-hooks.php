<?php

namespace SEOPlugin\Feature\Robots_Txt;

class WP_Hooks {
	/**
	 * Object instance
	 *
	 * @var object
	 */
	static $instance;

	/**
	 * Config
	 *
	 * @var array associative array of feature config
	 */
	var $config = array();

	/**
	 * Constructor
	 */
	public function __construct() {
		require __DIR__ . '/../config.php';
		$this->config = compact( 'name', 'id', 'description' );

		// add menu
		add_filter( 'seoplugin-submenu-items', array( $this, 'menu' ), 10, 2 );
	}

	/**
	 * Add sitemaps to menus
	 *
	 * @wp-hook seoplugin-menu-items
	 * @param  array $menus
	 * @return array
	 */
	public function menu( $menus, $id ) {
		if ( $id == 'features' ) {
			$menus[] = array( 'features/' . $this->config['id'], __( '- ' . $this->config['name'], 'seo-plugin' ), array( $this, 'controller' ), array() );
		}
		return $menus;
	}

	/**
	 * Menu controler
	 */
	public function controller() {
		// config data
		$config = $this->config;
		
		$settings = Robots_Txt::get();

		// load our settings view
		require_once __DIR__ . '/settings.php';
	}

	/**
	 * Return object instance
	 *
	 * @return object
	 */
	static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new WP_Hooks();
		}
	}

}
