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
	 * Constructor
	 */
	public function __construct() {
		require __DIR__ . '/../config.php';
		$this->config = compact( 'name', 'id', 'description' );

		// add schema markup
		add_action( 'wp_head', array( $this, 'add_schema' ), 1 );

	}

	/**
	 * Create and return schema data for @type = WebSite
	 *
	 * @return array
	 */
	private function schema_website() {
		$schema = array(
			'@type'           => 'WebSite',
			'@id'             => home_url( '#website' ),
			'url'             => home_url(),
			'name'            => get_bloginfo( 'name' ),
			'description'     => get_bloginfo( 'description' ),
			'potentialAction' =>
			array(
				array(
					'@type'       => 'SearchAction',
					'target'      => home_url( '?s={search_term_string}' ),
					'query-input' => 'required name=search_term_string',
				),
			),
			'inLanguage'      => get_locale(),
		);
		return $schema;
	}

	/**
	 * Create and return schema data for @type = WebPage
	 *
	 * @return array
	 */

	/**
	 * Create and return schema data for @type = WebPage
	 *
	 * @param  boolean $include_author True to include author property
	 * @return array
	 */
	private function schema_webpage( $include_author = false ) {
		$permalink = get_permalink();
		$schema    = array(
			'@type'           => 'WebPage',
			'@id'             => $permalink . '#webpage',
			'url'             => $permalink,
			'name'            => wp_title( '&raquo;', false ),
			'isPartOf'        =>
			array(
				'@id' => home_url( '#website' ),
			),
			'datePublished'   => get_the_date( \DateTime::ATOM ),
			'dateModified'    => get_the_modified_date( \DateTime::ATOM ),
			'inLanguage'      => get_locale(),
			'author'          => array(
				'@id' => $this->get_person_id( get_the_author() ),
			),
			'potentialAction' =>
			array(
				array(
					'@type'  => 'ReadAction',
					'target' =>
					array(
						$permalink,
					),
				),
			),
		);
		if ( ! $include_author ) {
			unset( $schema['author'] );
		}
		return $schema;
	}

	/**
	 * Create and return schema data for @type = Person
	 *
	 * @param  string $person (Optional) Current author if not specified
	 * @return array
	 */
	private function schema_person( $person = null ) {
		static $gravatars = array();
		if ( empty( $person ) ) {
			$person = get_the_author();
		}
		if ( empty( $person ) ) {
			return array();
		}
		if ( empty( $gravatars[ $person ] ) ) {
			$gravatars[ $person ] = sprintf( '//0.gravatar.com/avatar/%s?s=96&d=mm&r=g', md5( strtolower( trim( get_the_author_meta( 'user_email' ) ) ) ) );
		}
		$name   = $this->get_person_name();
		$schema = array(
			'@type'  => 'Person',
			'@id'    => $this->get_person_id( $person ),
			'name'   => $name,
			'image'  =>
			array(
				'@type'      => 'ImageObject',
				'@id'        => home_url( '#personlogo' ),
				'inLanguage' => get_locale(),
				'url'        => $gravatars[ $person ],
				'caption'    => $name,
			),
			'sameAs' =>
			array(
				home_url(),
			),
		);
		return $schema;
	}

	/**
	 * Create and return the @id to be used for @type = Person
	 *
	 * @param  string $person (Optional) Current author if not specified
	 * @return string
	 */
	private function get_person_id( $person = null ) {
		if ( empty( $person ) ) {
			$person = get_the_author();
		}
		if ( empty( $person ) ) {
			return '';
		}
		return home_url( '#/schema/person/' . md5( $person ) );
	}

	/**
	 * Create and return the name of a person
	 *
	 * @param  boolean $user_id (Optional) Current author if not specified
	 * @return string
	 */
	private function get_person_name( $user_id = false ) {
		return get_the_author_meta( 'display_name', $user_id ) ?: get_the_author_meta( 'first_name', $user_id ) . ' ' . get_the_author_meta( 'last_name', $user_id );
	}

	/**
	 * Create and return schema data for @type = ProfilePage
	 *
	 * @return array
	 */
	private function schema_profile_page() {
		$id     = get_the_author_meta( 'ID' );
		$url    = get_author_posts_url( $id );
		$schema = array(
			'@type'           => 'ProfilePage',
			'@id'             => $url . '#webpage',
			'url'             => $url,
			'name'            => $this->get_person_name( $id ) . ', Author at ' . get_bloginfo( 'name' ),
			'isPartOf'        =>
			array(
				'@id' => home_url( '#website' ),
			),
			'inLanguage'      => get_locale(),
			'potentialAction' =>
			array(
				array(
					'@type'  => 'ReadAction',
					'target' =>
					array(
						$url,
					),
				),
			),
		);
		return $schema;
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
				$schema = array(
					'@context' => 'https://schema.org',
					'@graph'   =>
					array(
						$this->schema_website(),
						array(
							'@type'           => 'CollectionPage',
							'@id'             => 'http://seoplugin.local/#webpage',
							'url'             => home_url(),
							'name'            => 'SEO Plugin - Just another WordPress site',
							'isPartOf'        =>
							array(
								'@id' => home_url( '#website' ),
							),
							'description'     => get_bloginfo( 'description' ),
							'inLanguage'      => get_locale(),
							'potentialAction' =>
							array(
								array(
									'@type'  => 'ReadAction',
									'target' =>
									array(
										home_url(),
									),
								),
							),
						),
					),
				);

				break;
			case is_page():
				$schema = array(
					'@context' => 'https://schema.org',
					'@graph'   =>
					array(
						$this->schema_website(),
						$this->schema_webpage(),
					),
				);
				break;
			case is_single():
				$schema = array(
					'@context' => 'https://schema.org',
					'@graph'   =>
					array(
						$this->schema_website(),
						$this->schema_webpage(),
						$this->schema_person(),
					),
				);
				break;
			case is_attachment():
				$schema = array(
					'@context' => 'https://schema.org',
					'@graph'   =>
					array(
						$this->schema_website(),
						array(
							'@type'           => 'WebPage',
							'@id'             => 'http://seoplugin.local/sample-page/#webpage',
							'url'             => 'http://seoplugin.local/sample-page/',
							'name'            => 'Sample Page - SEO Plugin',
							'isPartOf'        =>
							array(
								'@id' => home_url( '#website' ),
							),
							'datePublished'   => '2021-01-10T14:09:27+00:00',
							'dateModified'    => '2021-01-21T05:06:08+00:00',
							'inLanguage'      => get_locale(),
							'potentialAction' =>
							array(
								array(
									'@type'  => 'ReadAction',
									'target' =>
									array(
										'http://seoplugin.local/sample-page/',
									),
								),
							),
						),
					),
				);
				break;
			case is_category():
				$schema = array(
					'@context' => 'https://schema.org',
					'@graph'   =>
					array(
						$this->schema_website(),
						array(
							'@type'           => 'WebPage',
							'@id'             => 'http://seoplugin.local/sample-page/#webpage',
							'url'             => 'http://seoplugin.local/sample-page/',
							'name'            => 'Sample Page - SEO Plugin',
							'isPartOf'        =>
							array(
								'@id' => home_url( '#website' ),
							),
							'datePublished'   => '2021-01-10T14:09:27+00:00',
							'dateModified'    => '2021-01-21T05:06:08+00:00',
							'inLanguage'      => get_locale(),
							'potentialAction' =>
							array(
								array(
									'@type'  => 'ReadAction',
									'target' =>
									array(
										'http://seoplugin.local/sample-page/',
									),
								),
							),
						),
					),
				);
				break;
			case is_tag():
				$schema = array(
					'@context' => 'https://schema.org',
					'@graph'   =>
					array(
						$this->schema_website(),
						array(
							'@type'           => 'CollectionPage',
							'@id'             => 'http://seoplugin.local/tag/lopez/#webpage',
							'url'             => 'http://seoplugin.local/tag/lopez/',
							'name'            => 'lopez Archives - SEO Plugin',
							'isPartOf'        =>
							array(
								'@id' => home_url( '#website' ),
							),
							'inLanguage'      => get_locale(),
							'potentialAction' =>
							array(
								array(
									'@type'  => 'ReadAction',
									'target' =>
									array(
										'http://seoplugin.local/tag/lopez/',
									),
								),
							),
						),
					),
				);
				break;
			case is_author():
				$schema = array(
					'@context' => 'https://schema.org',
					'@graph'   =>
					array(
						$this->schema_website(),
						$x = $this->schema_profile_page(),
						$this->schema_person() + array(
							'mainEntityOfPage' =>
							array(
								'@id' => $x['@id'],
							),
						),
					),
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
	}

}
