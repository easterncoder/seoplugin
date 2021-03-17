<?php

namespace SEOPlugin\Feature\Sitemaps;

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

		// display sitemap
		add_action( 'template_redirect', array( __NAMESPACE__ . '\Sitemap', 'show' ), 1 );

		// reset sitemap
		add_action( 'wp_insert_post', array( __NAMESPACE__ . '\Sitemap', 'reset' ), 1 );
		add_action(
			'seoplugin-settings-change',
			function( $option ) {
				if ( substr( $option, 0, 15 ) == 'sitemaps-enable' ) {
					Sitemap::reset();
				}
			}
		);

		// disable WordPress sitemap
		add_filter( 'wp_sitemaps_enabled', '__return_false' );
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
		// get post types enabled for sitemaps
		$sitemaps_enabled_post_types = \SEOPlugin\Core\Settings::get( 'sitemaps-enabled-post-types', array() );
		// get all public post types
		$custom_post_types = get_post_types( array( 'public' => true ), 'objects' );

		// get taxonomies enabled for sitemaps
		$sitemaps_enabled_taxonomies = \SEOPlugin\Core\Settings::get( 'sitemaps-enabled-taxonomies', array() );
		// get all public taxonomies
		$taxonomies = get_taxonomies( array( 'public' => true ), 'objects' );

		// get if authors are enabled for sitemaps
		$sitemaps_enable_authors = \SEOPlugin\Core\Settings::get( 'sitemaps-enable-authors', false );

		// get sitemap priority logic settings
		$sitemaps_priority_logic = \SEOPlugin\Core\Settings::get( 'sitemaps-priority-logic', 'site-architecture' );

		// config data
		$config = $this->config;

		// sitemap url
		$sitemap_url = Sitemap::url();

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
