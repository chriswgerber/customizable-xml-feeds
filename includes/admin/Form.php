<?php
/**
 *
 *
 * @since 1.0.0
 *
 * @package Custom-XML-Feeds
 */
namespace CustomXMLFeeds\Admin;

use CustomXMLFeeds\Form as Abstract_Form;
use CustomXMLFeeds\CustomXML;

class Form extends Abstract_Form {

	/**
	 * @var array
	 */
	public $terms;

	/**
	 * @var \CustomXMLFeeds\CustomXML;
	 */
	public $xml;

	public function __construct( ) { }

	/**
	 * Options Page Callback
	 *
	 * Functionr renders the actual form.
	 *
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
	 * Re
	 *
	 */
	public function render_tags() {
		if ( count( $tags = $this->xml->get_all_tags() ) === 0 ) {
			printf( __('<p>There are no tags created yet. Create tags at the %1$s edit page.</p>', 'custom_xml'), $this->xml->tag_admin_url('Email Tags') );
		} else {
			foreach ( $tags as $tag ) {
				$this->display_tag( $tag );
			}
		}
	}

	/**
	 * Displays Single Tag.
	 *
	 * @since 1.0.0
	 * @access protected
	 *
	 * @param $tag \stdClass
	 */
	protected function display_tag( \stdCLass $tag ) {
		echo '<div class="email-tag">';
		echo '<h3>' . __( $tag->name ) . '</h3>';
		echo '<table class="form-table">';
		echo '<tr>';
		$this->text_field( array(
			'title' => 'Article Count',
			'field' => 'number',
			'id'    => $tag->term_taxonomy_id,
			'name'  => 'post_count'
		) );
		echo '</tr><tr>';
		$this->text_field( array(
			'title' => 'Word Count',
			'field' => 'number',
			'id'    => $tag->term_taxonomy_id,
			'name'  => 'word_count'
		) );
		echo '</tr><tr>';
		$this->text_field( array(
			'title' => 'Image Size',
			'field' => 'text',
			'id'    => $tag->term_taxonomy_id,
			'name'  => 'image_size'
		) );
		echo '</tr></table></div>';
	}
}