<?php

namespace SEOPlugin\Feature\Schema_Markup;

class Schema {
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
	}

	/**
	 * Create and return schema data for the website
	 *
	 * @return array
	 */
	public function schema_website() {
		$schema = array(
			'@type'           => 'WebSite',
			'@id'             => home_url( '#website' ),
			'url'             => home_url(),
			'name'            => wp_title( '&raquo;', false ),
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
	 * Create and return schema data for categories and tags
	 *
	 * @return array
	 */
	public function schema_collection_page() {
		$obj = get_queried_object();
		if ( ! empty( $obj->term_id ) ) {
			$permalink = get_category_link( $obj->term_id );
			$schema    = array(
				'@type'           => 'CollectionPage',
				'@id'             => $permalink . '#webpage',
				'url'             => $permalink,
				'name'            => wp_title( '&raquo;', false ),
				'isPartOf'        =>
				array(
					'@id' => home_url( '#website' ),
				),
				'inLanguage'      => 'en-US',
				'potentialAction' => array(
					array(
						'@type'  => 'ReadAction',
						'target' => array( $permalink ),
					),
				),
			);
		} elseif ( $obj->ID ) {
			$permalink = get_permalink();
			$schema = array(
				'@type'           => 'CollectionPage',
				'@id'             => $permalink . '#webpage',
				'url'             => home_url(),
				'name'            => wp_title( '&raquo;', false ),
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
			);

		}
		return $schema;
	}

	/**
	 * Create and return schema data for all post types except attachment pages
	 *
	 * @param  boolean $include_author True to include author property
	 * @return array
	 */
	public function schema_webpage( $include_author = false ) {
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
	 * Create and return schema data for attachment pages
	 *
	 * @return array
	 */
	public function schema_attachment() {
		global $post;

		$attachment_url = wp_get_attachment_url();
		$schema         = array(
			'@type'           => 'WebPage',
			'@id'             => $attachment_url . '#webpage',
			'url'             => $attachment_url,
			'name'            => wp_title( '&raquo;', false ),
			'isPartOf'        =>
			array(
				'@id' => home_url( '#website' ),
			),
			'datePublished'   => get_the_date( \DateTime::ATOM ),
			'dateModified'    => get_the_modified_date( \DateTime::ATOM ),
			'inLanguage'      => get_locale(),
			'potentialAction' =>
			array(
				array(
					'@type'  => 'ReadAction',
					'target' =>
					array(
						$attachment_url,
					),
				),
			),
		);
		return $schema;
	}

	/**
	 * Create and return schema data for @type = Person
	 *
	 * @param  string $person (Optional) Current author if not specified
	 * @return array
	 */
	public function schema_person( $person = null ) {
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
	 * Create and return schema data for the user's profile page
	 *
	 * @return array
	 */
	public function schema_profile_page() {
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
	 * Generates a @graph schema collection with the passed $data
	 *
	 * @param  array $schema,... Schema type objects
	 * @return array
	 */
	public function graph( ...$schema ) {
		return array(
			'@context' => 'https://schema.org',
			'@graph'   => $schema,
		);
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
	 * Return object instance
	 *
	 * @return object
	 */
	static function instance() {
		if ( empty( self::$instance ) ) {
			self::$instance = new self();
		}
		return self::$instance;
	}

}
