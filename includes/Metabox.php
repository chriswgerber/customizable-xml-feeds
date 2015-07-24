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

	public $id;

	public $title;

	public $context = 'advanced';

	public $priority = 'default';

	public $callback = 'email_lede';

	public $meta_key = 'custom_xml_description';

	public $post_types;

	protected $current_post_type;

	public function register_metaboxes() {
		foreach ( $this->post_types as $screen ) {
			$this->current_post_type = $screen;
			add_action( "add_meta_boxes_${screen}", array( $this, 'add_metabox' ) );
		}
	}

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

	public function email_lede() {
		global $post;
		wp_nonce_field( $this->id . '_meta_box', $this->id . '_meta_box_nonce' );
		/*
		 * Use get_post_meta() to retrieve an existing value
		 * from the database and use the value for the form.
		 */
		$value = get_post_meta( $post->ID, $this->meta_key, true );
		echo '<textarea rows="1" columns="40" class="custom_xml_desc" id="' . $this->meta_key . '" name="' . $this->meta_key . '">' . esc_textarea( $value ) . '</textarea>';
		echo '<p>This excerpt will appear in XML/RSS feed. HTML Tags are not allowed.</p>';
	}

	public function register_save_data() {
		foreach ( $this->post_types as $screen ) {
			add_action( 'save_' . $screen, array( $this, 'save_data' ) );
		}
	}

	public function save_data( $post_id ) {
		/*
		 * We need to verify this came from our screen and with proper authorization,
		 * because the save_post action can be triggered at other times.
		 */

		// Check if our nonce is set.
		if ( ! isset( $_POST[ $this->id . '_meta_box_nonce' ] ) ) {
			return;
		}

		// Verify that the nonce is valid.
		if ( ! wp_verify_nonce( $_POST[ $this->id . '_meta_box_nonce' ], $this->id . '_meta_box' ) ) {
			return;
		}

		// If this is an autosave, our form has not been submitted, so we don't want to do anything.
		if ( defined( 'DOING_AUTOSAVE' ) && DOING_AUTOSAVE ) {
			return;
		}

		/* OK, it's safe for us to save the data now. */

		// Make sure that it is set.
		if ( ! isset( $_POST[ $this->meta_key ] ) ) {
			return;
		}

		// Sanitize user input.
		$my_data = sanitize_text_field( $_POST[ $this->meta_key ] );

		// Update the meta field in the database.
		update_post_meta( $post_id, $this->meta_key, $my_data );
	}

}