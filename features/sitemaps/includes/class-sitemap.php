<?php

namespace SEOPlugin\Feature\Sitemaps;

/**
 * Sitemap
 */
class Sitemap {
	/**
	 * Return sitemap URL
	 *
	 * @param  string $type
	 * @return string
	 */
	static function url( $type = '' ) {
		$name = ( $type ? 'sitemap-' . $type : 'sitemap' ) . '.xml';
		$url  = trailingslashit( home_url() );

		if ( empty( get_option( 'permalink_structure' ) ) ) {
			$url .= 'index.php/';
		}

		return $url . $name;
	}

	/**
	 * Retrieve sitemap
	 *
	 * @param  string $type Sitemap type
	 * @return string
	 */
	static function get( $type = '' ) {
		$name = 'sitemap';
		if ( $type ) {
			$name .= '-' . $type;
		}
		$sitemap = trim( \SEOPlugin\Core\Settings::get( 'sitemaps-' . $name . '.xml', $sitemap ) );
		if ( ! $sitemap ) {
			self::generate( $type );
			$sitemap = trim( \SEOPlugin\Core\Settings::get( 'sitemaps-' . $name . '.xml', $sitemap ) );
		}
		return $sitemap;
	}

	/**
	 * Display sitemap
	 *
	 * @wp-hook template_redirect
	 */
	static function show() {
		global $wp;
		if ( ! $wp->request && empty( get_option( 'permalink_structure' ) ) && substr( $_SERVER['REQUEST_URI'], 0, 11 ) == '/index.php/' ) {
			list($request) = explode( '?', substr( $_SERVER['REQUEST_URI'], 11 ) );
		} else {
			$request = $wp->request;
		}

		// redirect sitemap.xml to sitemap-index.xml
		if ( $request == 'sitemap.xml' ) {
			wp_safe_redirect( self::url( 'index' ) );
			exit;
		}

		// print sitemap if available
		if ( preg_match( '#^(sitemap|sitemap-index|sitemap-authors|sitemap-(taxonomy|content).+?)\.xml$#', $request ) ) {
			$slug    = $request == 'sitemap-index.xml' ? '' : substr( $request, 8, -4 );
			$sitemap = trim( self::get( $slug ) );
			if ( $sitemap ) {
				http_response_code( 200 );
				header( 'Content-type: text/xml' );
				header( 'Content-length: ' . strlen( $sitemap ) );
				echo $sitemap;
				exit;
			}
		}
	}

	/**
	 * Generates sitemap and saves it
	 *
	 * @param  string $type Sitemap type
	 */
	static function generate( $type = '' ) {
		global $wpdb;
		if ( $type ) {
			// non-index sitemaps
			$sitemap = '<?xml version="1.0" encoding="UTF-8"?><urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';
			// split type and slug
			list($x, $slug) = explode( '-', $type, 2 );
			switch ( $x ) {
				case 'taxonomy':
					// taxonomy sitemaps
					foreach ( get_terms(
						array(
							'taxonomy'   => $slug,
							'hide_empty' => false,
						)
					) as $term ) {
						$sitemap .= '<url>';
						// $sitemap    .= '<priority>1</priority><changefreq>daily</changefreq>';
						$sitemap    .= '<loc>' . get_term_link( $term->term_id, $slug ) . '</loc>';
						$latest_post = get_posts(
							array(
								'tax_query' => array(
									'taxonomy' => $slug,
									'field'    => 'slug',
									'terms'    => $term->slug,
								),
							)
						);
						if ( $latest_post ) {
							$sitemap .= '<lastmod>' . str_replace( ' ', 'T', $latest_post[0]->post_modified_gmt ) . '+00:00' . '</lastmod>';
						}
						$sitemap .= '</url>';
					}
					break;
				case 'authors':
					foreach ( $wpdb->get_results( 'select post_author, max(post_modified_gmt) as post_modified_gmt from ' . $wpdb->posts . ' where post_status="publish" group by post_author' ) as $author ) {
						$sitemap .= '<url>';
						// $sitemap .= '<priority>1</priority><changefreq>daily</changefreq>';
						$sitemap .= '<loc>' . get_author_posts_url( $author->post_author ) . '</loc>';
						$sitemap .= '<lastmod>' . str_replace( ' ', 'T', $author->post_modified_gmt ) . '+00:00' . '</lastmod>';
						$sitemap .= '</url>';
					}
					break;
				default:
					// content sitemaps
					$first = true;
					foreach ( get_posts( array( 'post_type' => $slug ) ) as $post ) {
						if ( $first ) {
							$first    = false;
							$sitemap .= '<url>';
							// $sitemap .= '<priority>1</priority><changefreq>daily</changefreq>';
							$sitemap .= '<loc>' . home_url( '/' ) . '</loc>';
							$sitemap .= '<lastmod>' . str_replace( ' ', 'T', $post->post_modified_gmt ) . '+00:00' . '</lastmod>';
							$sitemap .= '</url>';
						}
						$sitemap .= '<url>';
						// $sitemap .= '<priority>1</priority><changefreq>daily</changefreq>';
						$sitemap .= '<loc>' . get_permalink( $post->ID ) . '</loc>';
						$sitemap .= '<lastmod>' . str_replace( ' ', 'T', $post->post_modified_gmt ) . '+00:00' . '</lastmod>';
						$sitemap .= '</url>';
					}
			}
			$sitemap .= '</urlset>';
			\SEOPlugin\Core\Settings::update( 'sitemaps-sitemap-' . $type . '.xml', $sitemap );
		} else {
			// index sitemap
			$sitemap = '<?xml version="1.0" encoding="UTF-8"?><sitemapindex xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">';

			// taxonomies
			foreach ( \SEOPlugin\Core\Settings::get( 'sitemaps-enabled-taxonomies', array() ) as $tax ) {
				$sitemap .= '<sitemap><loc>' . self::url( 'taxonomy-' . $tax ) . '</loc></sitemap>';
			}

			// content
			foreach ( \SEOPlugin\Core\Settings::get( 'sitemaps-enabled-post-types', array() ) as $cpt ) {
				$sitemap .= '<sitemap><loc>' . self::url( 'content-' . $cpt ) . '</loc></sitemap>';
			}

			// author
			if ( \SEOPlugin\Core\Settings::get( 'sitemaps-enable-authors', false ) ) {
				$sitemap .= '<sitemap><loc>' . self::url( 'authors' ) . '</loc></sitemap>';
			}

			$sitemap .= '</sitemapindex>';
			\SEOPlugin\Core\Settings::update( 'sitemaps-sitemap.xml', $sitemap );
		}
	}

	/**
	 * Resets sitemap data
	 */
	static function reset() {
		static $reset = false;

		// reset only once per session
		if ( $reset ) {
			return;
		}

		$reset = true;

		// Reset taxonomy sitemaps
		foreach ( \SEOPlugin\Core\Settings::get( 'sitemaps-enabled-taxonomies', array() ) as $tax ) {
			\SEOPlugin\Core\Settings::delete( 'sitemaps-sitemap-taxonomy-' . $tax . '.xml' );
		}

		// Reset content sitemaps
		foreach ( \SEOPlugin\Core\Settings::get( 'sitemaps-enabled-post-types', array() ) as $cpt ) {
			\SEOPlugin\Core\Settings::delete( 'sitemaps-sitemap-content-' . $cpt . '.xml' );
		}

		// Reset sitemap index
		\SEOPlugin\Core\Settings::delete( 'sitemaps-sitemap.xml' );
	}
}

