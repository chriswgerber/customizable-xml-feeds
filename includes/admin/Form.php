<?php
/**
 * Form for the Admin page
 *
 * @since   1.0.0
 *
 * @package Custom-XML-Feeds
 */
namespace CustomXMLFeeds\Admin;

use CustomXMLFeeds\Form as Abstract_Form,
	CustomXMLFeeds\CustomXML;

class Form extends Abstract_Form {

	/**
	 * List of terms to be added
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $terms;

	/**
	 * XML Object
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var \CustomXMLFeeds\CustomXML;
	 */
	public $xml;

	/**
	 * Options Page Callback
	 *
	 * Function renders the actual form.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function render_form() {
		?>
		<div class="wrap">
			<?php $this->form_title(); ?>
			<div class="postbox ">
				<div class="inside">
					<h2>Tags</h2>

					<form method="post" method="post" action="">
						<?php $this->render_tags(); ?>
						<div>
							<?php $this->button( 'Update Tags', true ); ?>
						</div>
					</form>
				</div>
			</div>
			<div class="postbox ">
				<div class="inside">
					<form method="post" action="options.php">
						<?php settings_fields( $this->settings_fields ); ?>
						<?php do_settings_sections( $this->settings_sections ); ?>
					</form>
				</div>
			</div>
		</div>
		<?php
	}

	/**
	 * Renders all of the tags.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function render_tags() {
		if ( count( $tags = $this->xml->get_all_tags() ) === 0 ) {
			printf( __( '<p>There are no tags created yet. Create tags at the %1$s edit page.</p>', 'custom_xml' ), $this->xml->tag_admin_url( 'Email Tags' ) );
		} else {
			foreach ( $tags as $tag ) {
				$this->display_tag( $tag );
			}
		}
	}

	/**
	 * Displays Single Tag.
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param $tag \stdClass
	 *
	 * @return void
	 */
	protected function display_tag( \stdCLass $tag ) {
		?>
		<div class="email-tag">
			<h3><?php _e( $tag->name ); ?></h3>
			<table class="form-table">
				<tr>
					<?php $this->text_field( array(
						'title' => 'Article Count',
						'field' => 'number',
						'id'    => $tag->term_taxonomy_id,
						'name'  => 'post_count'
					) ); ?>
				</tr>
				<tr>
					<?php $this->text_field( array(
						'title' => 'Word Count',
						'field' => 'number',
						'id'    => $tag->term_taxonomy_id,
						'name'  => 'word_count'
					) ); ?>
				</tr>
				<tr>
					<?php $this->text_field( array(
						'title' => 'Image Size',
						'field' => 'text',
						'id'    => $tag->term_taxonomy_id,
						'name'  => 'image_size'
					) ); ?>
				</tr>
			</table>
		</div>
		<?php
	}
}
