<?php
/**
 * Abstract class for building forms.
 *
 * Class abstraction for simple form functions.
 *
 * @since   1.0.0
 *
 * @package Custom-XML-Feeds
 */
namespace CustomXMLFeeds;

Abstract Class Form {

	/**
	 * Page Title
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $title;

	/**
	 * String to call settings fields
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $settings_fields;

	/**
	 * String to call settings sections
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $settings_sections;

	/**
	 * @since  1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $values;

	/**
	 * String identifier for the options
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $options_str;

	/**
	 * Coagulates the functions into a form on the front-end.
	 *
	 * Abstract
	 *
	 * @since  1.0.0
	 * @access public
	 */
	abstract public function render_form();

	/**
	 * Adds the title to the page.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $title string Form Title
	 */
	public function form_title( $title = null ) {
		// Takes specialized title, or uses default if empty.
		$title = ( $title === null ? $this->title : $title );
		// Echos title
		echo "<h2>$title</h2>";
	}

	/**
	 * Creates block of text
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $args array
	 */
	public function paragraph( $args ) {
		$description = $args[0]['description'];
		?><p><?php _e( $description, 'custom_xml' ); ?></p>
		<?php
	}

	/**
	 * Creates input
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $args array
	 */
	public function text_field( $args ) {
		$id    = $this->options_str . '[' . $args['id'] . '][' . $args['name'] . ']';
		$field = $args['field'];
		$title = $args['title'];
		$value = ( ! isset( $this->values[ $args['id'] ][ $args['name'] ] ) ? '' : $this->values[ $args['id'] ][ $args['name'] ] );
		?>
		<td>
			<label for="<?php _e( $id, 'custom_xml' ); ?>">
				<?php _e( $title, 'custom_xml' ); ?>
			</label>
		</td>
		<td>
			<input type="<?php _e( $field, 'custom_xml' ); ?>"
			       id="<?php _e( $id, 'custom_xml' ); ?>"
			       name="<?php _e( $id, 'custom_xml' ); ?>"
			       value="<?php _e( $value, 'custom_xml' ); ?>"/>
		</td>
		<?php
	}

	/**
	 * Button Function
	 *
	 * Creates an HTML button.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $value   string Value of the submit button
	 * @param $primary bool Mark a button as a primary button
	 */
	public function button( $value, $primary = false ) {
		$value = wp_strip_all_tags( $value, true );
		$button_type = ( $primary === false ? 'button-secondary' : 'button-primary' )
		?>
		<input type="submit" name="submit" id="submit" class="button <?php echo $button_type; ?>"
		       value="<?php echo $value; ?>">
		<?php
	}

	/**
	 * Simple method for accessing a submit button
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function submit_button() {
		$this->button( 'Save Changes', true );
	}
}