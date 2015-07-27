<?php
/**
 * Creates a metabox
 *
 * Metabox should be tied to whatever post types the taxonomy is tied to.
 *
 * @since   1.0.0
 *
 * @package Custom-XML-Feeds
 */
namespace CustomXMLFeeds;

class Metabox {

	/**
	 * Metabox ID. Used for referencing metaboxes
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $id;

	/**
	 * Title to appear above the metabox
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $title;

	/**
	 * Where the box should appear
	 *
	 * Describes what type of box it is. This will
	 * help determine where it appears on the page.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $context = 'advanced';

	/**
	 * Where it should appear
	 *
	 * By default, it'll appear lower in the page.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $priority = 'default';

	/**
	 * method or function to run for the callback to display
	 * the field
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $callback = 'email_lede';

	/**
	 * Meta key
	 *
	 * Name that the metadata is stored in the database under.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $meta_key = 'custom_xml_description';

	/**
	 * Array of post types that the metabox is queued with.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $post_types;

	/**
	 * Current screen
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	protected $current_post_type;

	/**
	 * Registers metaboxes for each of the supplied post types
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_metaboxes() {
		foreach ( $this->post_types as $screen ) {
			$this->current_post_type = $screen;
			add_action( "add_meta_boxes_${screen}", array( $this, 'add_metabox' ) );
		}
	}

	/**
	 * Adds metabox to current post type
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function add_metabox() {
		add_meta_box(
			$this->id,
			$this->title,
			array( $this, $this->callback ),
			$this->current_post_type,
			$this->context,
			$this->priority
		);
	}

	/**
	 *
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function email_lede() {
		global $post;
		wp_nonce_field( $this->id . '_meta_box', $this->id . '_meta_box_nonce' );
		$value = get_post_meta( $post->ID, $this->meta_key, true );
		echo '<textarea rows="1" columns="40" class="custom_xml_desc" id="' . $this->meta_key . '" name="' . $this->meta_key . '">' . esc_textarea( $value ) . '</textarea>';
		echo '<p>This excerpt will appear in XML/RSS feed. HTML Tags are not allowed.</p>';
	}

	/**
	 * Registers save function with each of the post types this is enqueued on
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_save_data() {
		foreach ( $this->post_types as $screen ) {
			add_action( 'save_' . $screen, array( $this, 'save_data' ) );
		}
	}

	/**
	 * Saves the metabox data
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param \WP_POST $post_id
	 *
	 * @return void|mixed
	 */
	public function save_data( $post_id ) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */
		if (
			! isset( $_POST[ $this->id . '_meta_box_nonce' ] ) ||
			! wp_verify_nonce( filter_input( INPUT_POST, $this->id . '_meta_box_nonce' ), $this->id . '_meta_box' ) ||
			defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ||
			! isset( $_POST[ $this->meta_key ] )
		) {
			return;
		}
		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST[ $this->meta_key ] );
		// Update the meta field in the database.
		update_post_meta( $post_id, $this->meta_key, $my_data );
	}

}