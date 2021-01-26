<?php

namespace SEOPlugin\Feature\Schema_Markup;

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
	 * Schema Object Instance
	 *
	 * @var \SEOPlugin\Feature\Schema_Markup\Schema
	 */
	var $schema;

	/**
	 * Constructor
	 */
	public function __construct() {
		require __DIR__ . '/../config.php';
		$this->config = compact( 'name', 'id', 'description' );

		// Schema object
		$this->schema = Schema::instance();

		// add schema markup
		add_action( 'wp_head', array( $this, 'add_schema' ), 1 );

	}

	/**
	 * Inserts schema to header
	 *
	 * @wp-hook wp_head
	 */
	public function add_schema() {
		$schema = array();
		switch ( true ) {
			case is_home():
				$schema = $this->schema->graph(
						$this->schema->schema_website(),
						$this->schema->schema_collection_page()
				);
				break;
			case is_front_page():
				$schema = $this->schema->graph(
						$this->schema->schema_website(),
						$this->schema->schema_webpage()
				);
				break;
			case is_attachment():
				$schema = $this->schema->graph(
						$this->schema->schema_website(),
						$this->schema->schema_attachment()
				);
				break;
			case is_page():
				$schema = $this->schema->graph(
						$this->schema->schema_website(),
						$this->schema->schema_webpage()
				);
				break;
			case is_single():
				$schema = $this->schema->graph(
						$this->schema->schema_website(),
						$this->schema->schema_webpage(),
						$this->schema->schema_person()
				);
				break;
			case is_category():
				$schema = $this->schema->graph(
					$this->schema->schema_website(),
					$this->schema->schema_collection_page()
				);
				break;
			case is_tag():
				$schema = $this->schema->graph(
					$this->schema->schema_website,
					$this->schema->schema_collection_page()
				);
				break;
			case is_author():
				$schema = $this->schema->graph(
					$this->schema->schema_website(),
					$x  = $this->schema->schema_profile_page(),
					$this->schema->schema_person() + array(
						'mainEntityOfPage' =>
						array(
							'@id' => $x['@id'],
						),
					)
				);
				break;
		}
		if ( $schema ) {
			printf( '<script type="application/ld+json" class="seoplugin-schema-graph">%s</script>', json_encode( $schema, JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT ) );
		}
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
		return self::$instance;
	}

}
