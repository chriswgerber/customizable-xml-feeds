<?php
/**
 * Class for creating feed objects.
 *
 * @since   1.0.0
 *
 * @package Custom-XML-Feeds
 */
namespace CustomXMLFeeds;

use CustomXMLFeeds\CustomXML;

class Feed {

	/**
	 * @access public
	 * @since  1.0.0
	 *
	 * @var CustomXML
	 */
	public $xml;

	/**
	 * @access public
	 * @since  1.0.0
	 *
	 * @var
	 */
	public $desc_meta_key;

	/**
	 * PHP5 Constructor
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @param CustomXML $xml
	 */
	public function __construct( CustomXML $xml ) {
		$this->xml = $xml;
		$this->desc_meta_key = $xml->metabox->meta_key;
		// Let's get rid of stupid smart quotes, please
		remove_filter( 'the_content', 'wptexturize' );
		remove_filter( 'the_excerpt', 'wptexturize' );
		remove_filter( 'comment_text', 'wptexturize' );
		remove_filter( 'the_title_rss', 'wptexturize' );
	}

	/**
	 * Instantiates the feed.
	 *
	 * @access public
	 * @since  1.0.0
	 */
	public function get_feed() {
		/**
		 * @param \WP_Query
		 */
		global $wp_query;
		// Declare an XML feed
		include( $this->xml->template_dir . '/templates/feed-header.php' );
		/*
		 * Two Parts:
		 *
		 * First it will attempt to query the custom taxonomy for posts. If it
		 * finds posts in the custom taxonomy, it will use those.
		 *
		 * Otherwise, the fallback is to target $wp_query and attempt to find
		 * posts there. This is so that it works on non-"email tag" posts in
		 * case someone wants to just use a category.
		 *
		 * There aren't limits on image sizes when it can't find custom tagged
		 * posts, so it will default to the thumbnail (125x125) size.
		 *
		 */
		if ( ( $query = $this->posts() ) && $query->have_posts() ) {
			while ( $query->have_posts() ) {
				$query->the_post();
				include( $this->xml->template_dir . '/templates/item-template.php' );
			}
		} elseif ( have_posts() ) {
			while ( have_posts() ) {
				the_post();
				include( $this->xml->template_dir . '/templates/item-template.php' );
			}

		}
		wp_reset_postdata();
		// Close up the feed
		include( $this->xml->template_dir . '/templates/feed-footer.php' );
	}


	/**
	 * Echos out the link to the feed image
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 */
	public function feed_image() {
		if ( has_post_thumbnail() ) {
			echo $this->get_feed_image( $size );
		}
	}

	/**
	 * Gets the feed image and returns it as a variable.
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @return mixed
	 */
	public function get_feed_image() {
		/**
		 * @param \WP_POST $post
		 */
		global $post;
		$image_sizes = $this->xml->get_image_sizes();
		$terms       = wp_get_post_terms( $post->ID, $this->xml->taxonomy_slug );
		$size        = $this->xml->taxonomy_slug . '/' . $terms[0]->slug . '/thumb';
		// Check if the requested size exists, otherwise return the default 125x125
		if ( array_key_exists( $size, $image_sizes ) ) {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $size );
		} else {
			$image = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), $this->xml->taxonomy_slug . '/default/thumb' );
		}

		return $image[0];
	}

	/**
	 * Echos out the description for the feed.
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 */
	public function the_description() {
		/**
		 * @param \WP_POST $post
		 */
		global $post;
		$desc = get_post_meta( $post->ID, $this->desc_meta_key, true );
		if ( is_string( $desc ) && strlen( $desc ) > 0 ) {
			echo $this->limit_text( __( $desc ) );
		} else {
			echo $this->limit_text( get_the_excerpt() );
		}
	}

	/**
	 * Gets custom posts call
	 *
	 * @access protected
	 * @since  1.0.0
	 *
	 * @return \WP_Query
	 */
	protected function posts() {
		$term_settings = $this->get_term_settings();
		$args          = array(
			'pagination'     => false,
			'cache_results'  => false,
			'post_status'    => 'publish',
			'posts_per_page' => $term_settings['post_count'],
			'orderby'        => 'date',
			'tax_query'      => array(
				array(
					'taxonomy' => $this->xml->taxonomy_slug,
					'field'    => 'slug',
					'terms'    => get_query_var( $this->xml->taxonomy_slug ),
				),
			),
		);

		return new \WP_Query( $args );
	}

	/**
	 * Limit the length of the text for the description.
	 *
	 * @access protected
	 * @since  1.0.0
	 *
	 * @param $text string Text to limit
	 *
	 * @return string Limited text
	 */
	protected function limit_text( $text ) {
		$limit = $this->get_word_limit();
		if ( str_word_count( $text ) > $limit ) {
			$text = implode( " ", array_slice( explode( " ", $text ), 0, $limit ) );
			if ( substr( $text, - 1 ) == '.' ) {

				return $text . '..';
			} elseif ( substr( $text, - 1 ) == '?' ) {

				return $text;
			} else {

				return $text . '...';
			}
		} else {

			return $text;
		}
	}

	/**
	 * Get the limit for words for a given tag.
	 *
	 * @access protected
	 * @since  1.0.0
	 *
	 * @return int
	 */
	protected function get_word_limit() {
		$term_settings = $this->get_term_settings();
		if ( isset( $term_settings['word_count'] ) ) {

			return $term_settings['word_count'];
		} else {

			return 25;
		}
	}

	/**
	 * Return an array of settings for a given term.
	 *
	 * @access protected
	 * @since  1.0.0
	 *
	 * @return null|array
	 */
	protected function get_term_settings() {
		$values = $this->xml->get_options();
		$term   = get_term_by( 'slug', get_query_var( $this->xml->taxonomy_slug ), $this->xml->taxonomy_slug );
		if ( array_key_exists( $term->term_taxonomy_id, $values ) ) {

			return $values[ $term->term_taxonomy_id ];
		}

		return null;
	}

}