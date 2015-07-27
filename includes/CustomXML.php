<?php
/**
 * Custom XML Feed Container
 *
 * Holds strings, methods, object instances, etc.
 *
 * @since   1.0.0
 *
 * @package Custom-XML-Feeds
 */
namespace CustomXMLFeeds;

class CustomXML {

	/**
	 * @since  1.0.0
	 * @access public
	 *
	 * @var Feed
	 */
	public $feed;

	/**
	 * @since  1.0.0
	 * @access public
	 *
	 * @var Metabox
	 */
	public $metabox;

	/**
	 * @since  1.0.0
	 * @access public
	 *
	 * @var mixed|void
	 */
	public $tags;

	/**
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $taxonomy_slug;

	/**
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $taxonomy_name;

	/**
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $options_str;

	/**
	 * @since  1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $defaults = array();

	/**
	 * @since  1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $values;

	/**
	 * Template directory
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $template_dir;

	/**
	 * Returns options for a string
	 *
	 * Takes a string and uses the WordPress get_option method to find the data.
	 * If the data has already been pulled once, it'll save the call and just
	 * return the value.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array|mixed|void
	 */
	public function get_options() {

		if ( isset( $this->values ) ) {

			return $this->values;
		} else {
			$this->values = get_option( $this->options_str );

			return $this->values;
		}
	}

	/**
	 * Adds the image sizes to be used with the plugin.
	 *
	 * Images are set up as {taxonomy}\{slug}\thumb and can be called back
	 * to be used by referencing that info.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function add_image_sizes() {
		$this->tags = $this->get_options();
		if (
			is_array( $this->tags ) &&
			count( $this->tags ) > 0
		) {
			foreach ( $this->tags as $email_term_id => $email_tag ) {
				// Get the full term to create the name
				$email_term = get_term_by( 'id', $email_term_id, $this->taxonomy_slug );
				// Explode image size, should be string (125x125)
				$image_size = explode( 'x', $email_tag['image_size'] );
				add_image_size(
					$email_term->taxonomy . '/' . $email_term->slug . '/thumb',
					$image_size[0],
					$image_size[1],
					true
				);
			}
		}
		add_image_size( $this->taxonomy_slug . '/default/thumb', 125, 125, true );
	}

	/**
	 * Register email tags taxonomy
	 *
	 * Ads the labels, grabs the post types, and registers the taxonomy.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_taxonomy() {
		// Register Custom Taxonomy
		$labels = array(
			'name'                       => 'Email Tags',
			'singular_name'              => 'Email Tag',
			'menu_name'                  => 'Email Tags',
			'all_items'                  => 'All Tags',
			'parent_item'                => 'Parent Tag',
			'parent_item_colon'          => 'Parent Tag:',
			'new_item_name'              => 'New Email Tag',
			'add_new_item'               => 'Add New Tag',
			'edit_item'                  => 'Edit Tag',
			'update_item'                => 'Update Tag',
			'view_item'                  => 'View Tag',
			'separate_items_with_commas' => 'Separate Email Tags with commas',
			'add_or_remove_items'        => 'Add or remove tags',
			'choose_from_most_used'      => 'Choose from the most used',
			'popular_items'              => 'Popular Tags',
			'search_items'               => 'Search Tags',
			'not_found'                  => 'Not Found',
		);
		$args   = array(
			'labels'            => $labels,
			'hierarchical'      => false,
			'public'            => true,
			'show_ui'           => true,
			'show_admin_column' => true,
			'show_in_nav_menus' => false,
			'show_tagcloud'     => false,
		);

		register_taxonomy( $this->taxonomy_slug, $this->post_types(), $args );
	}

	/**
	 * Returns all tags as objects
	 *
	 * If it fails to return as objects, it'll return nothing to avoid trying to
	 * send an error out to future objects.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return array|null
	 */
	public function get_all_tags() {
		$tags = get_terms( $this->taxonomy_slug, array(
			'orderby'      => 'name',
			'order'        => 'asc',
			'hide_empty'   => false,
			'exclude'      => array(),
			'exclude_tree' => array(),
			'include'      => array(),
			'fields'       => 'all'
		) );

		if ( is_wp_error( $tags ) ) {

			return null;
		}

		return $tags;
	}

	/**
	 * Returns the admin url for the email tags
	 *
	 * I didn't want to remake this link. So I included the function.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $link Text to include in the body of the anchor tag
	 *
	 * @return string
	 */
	public function tag_admin_url( $link ) {

		return '<a href="://' . get_admin_url( null, 'edit-tags.php?taxonomy=' ) . __( $this->taxonomy_slug, 'custom_xml' ) . '">' . __( $link ) . '</a>';
	}

	/**
	 * Add/update new option to database
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $term_id
	 * @param int $tt_id
	 *
	 * @return null
	 */
	public function update_option( $term_id, $tt_id ) {
		$options           = $this->get_options();
		$options[ $tt_id ] = $this->defaults;
		update_option( $this->options_str, $options );
	}

	/**
	 * Delete database option
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param int $term_id
	 * @param int $tt_id
	 *
	 * @return null
	 */
	public function delete_option( $term_id, $tt_id ) {
		$options           = $this->get_options();
		$options[ $tt_id ] = null;
		update_option( $this->options_str, $options );
	}

	/**
	 * Returns post types applied to CustomXML tag
	 *
	 * @return mixed|void
	 */
	public function post_types() {

		return apply_filters( 'CustomXML\tag_post_types', array( 'post' ) );
	}

	/**
	 * Return all queued image sizes.
	 *
	 * I use this while debugging. I know I'll find a use in the future
	 * when I need to create and associate image sizes.
	 *
	 * @access public
	 * @since  1.0.0
	 *
	 * @param string $size size to return. Returns all if null
	 *
	 * @return array|bool
	 */
	public function get_image_sizes( $size = '' ) {
		global $_wp_additional_image_sizes;
		$sizes                        = array();
		$get_intermediate_image_sizes = get_intermediate_image_sizes();
		// Create the full array with sizes and crop info
		foreach ( $get_intermediate_image_sizes as $_size ) {
			if ( in_array( $_size, array( 'thumbnail', 'medium', 'large' ) ) ) {
				$sizes[ $_size ]['width']  = get_option( $_size . '_size_w' );
				$sizes[ $_size ]['height'] = get_option( $_size . '_size_h' );
				$sizes[ $_size ]['crop']   = (bool) get_option( $_size . '_crop' );
			} elseif ( isset( $_wp_additional_image_sizes[ $_size ] ) ) {
				$sizes[ $_size ] = array(
					'width'  => $_wp_additional_image_sizes[ $_size ]['width'],
					'height' => $_wp_additional_image_sizes[ $_size ]['height'],
					'crop'   => $_wp_additional_image_sizes[ $_size ]['crop']
				);
			}
		}
		// Get only 1 size if found
		if ( $size ) {
			if ( isset( $sizes[ $size ] ) ) {
				return $sizes[ $size ];
			} else {
				return false;
			}
		}

		return $sizes;
	}

}