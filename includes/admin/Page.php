<?php
/**
 * WP Admin setup
 *
 * @since   1.0.0
 *
 * @package Custom-XML-Feeds
 */
namespace CustomXMLFeeds;

use \CustomXMLFeeds\Admin\Form as Admin_Form;

class Admin_Page {

	/**
	 * Object which stores data about the XML instance
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var CustomXML;
	 */
	public $xml;

	/**
	 * Form object. Displays the form of choice
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var Admin\Form
	 */
	public $form;

	/**
	 * Title of the Admin Page
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $page_title;

	/**
	 * Menu name for the admin page
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $menu_title;

	/**
	 * Capability to access the page
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $user_cap;

	/**
	 * Slug for the plugin page
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $plugin_slug;

	/**
	 * String for accessing stored options
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $options_str;

	/**
	 * Group name for options
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $options_grp;

	/**
	 * String for fields
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var string
	 */
	public $fields_str;

	/**
	 * Array of options values
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @var array
	 */
	private $options = array();

	/**
	 * Sections to appear in the form
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @var array
	 */
	protected $sections = array(
		array(
			'id'    => 'basic_settings',
			'title' => 'Tags',
		)
	);

	/**
	 * Fields to be displayed in marked section
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @var array
	 */
	public $fields = array();

	/**
	 * Created by page registration. Hook for the page that was created
	 *
	 * @since  1.0.0
	 * @access private
	 *
	 * @var string
	 */
	private $hook_suffix = '';

	/**
	 * Admin Styles
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function scripts_and_styles() {
		wp_enqueue_style( 'custom-xml-admin', plugins_url( '../../assets/css/all-admin.min.css', __FILE__ ) );
	}

	/**
	 * Register the Menu Page.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return mixed
	 */
	public function register_menu_page() {
		$this->sections    = apply_filters( $this->options_str . '_sections', array() );
		$this->fields      = apply_filters( $this->options_str . '_fields', array() );
		$this->values      = get_option( $this->options_str );
		$this->hook_suffix = add_options_page(
			$this->page_title,     // Page Title
			$this->menu_title,     // Menu Title
			$this->user_cap,       // Capability
			$this->plugin_slug,    // Menu Slug
			array( $this, 'form' ) // Function
		);
	}

	/*
	 * Initialization function for the settings page.
	 *
	 * Sets up the settings and calls the view.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function menu_page_init() {
		// Register settings
		$this->register_settings();
		// Add sections to settings page.
		$this->add_sections();
		// Errors
		add_action( 'admin_notices', array( $this, 'add_errors' ) );
	}

	/**
	 * Register the plugin settings
	 *
	 * Returns an options string/options loop that is used by this
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function register_settings() {
		// register our settings
		register_setting(
			$this->options_grp,
			$this->options_str,
			array( $this, 'options_validate' )
		);
	}

	public function set_options_string( $string ) {
		$this->options_str = $string;
		$this->options_grp = $string . '-group';
		$this->fields_str  = $string . '_fields';
	}

	/**
	 * Create form for plugin settings.
	 *
	 * @since  1.0.0
	 * @access public
	 */
	public function add_sections() {
		if ( $this->sections ) {
			foreach ( $this->sections as $section ) {
				$this->create_settings_section( $section );
			}
		}
		if ( $this->fields ) {
			foreach ( $this->fields as $setting ) {
				$this->create_settings_field( $setting );
			}
		}
	}

	/**
	 * Filters and updates the options in the database.
	 *
	 * @return bool
	 */
	public function update_tags() {
		$options = filter_input( INPUT_POST, $this->xml->options_str, FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );

		return update_option( $this->xml->options_str, $options );
	}

	/**
	 * Sanitize and validate input. Accepts an array, return a sanitized array.
	 *
	 * @since  1.0.0
	 *
	 * @param array $input
	 *
	 * @return array $new_input
	 */
	public function options_validate( $input ) {
		$new_input = array();
		foreach ( $input as $tt_id => $value ) {
			// $tt_id = Term Tax. ID
			// $value = array of values
			//          post_count
			//          word_count
			//          image_size
			$new_input[ $tt_id ]['post_count'] = $this->article_validate( $value['post_count'] );
			$new_input[ $tt_id ]['word_count'] = $this->wordcount_validate( $value['word_count'] );
			$new_input[ $tt_id ]['image_size'] = $this->imagesize_validate( $value['image_size'] );
		}

		return $new_input;
	}

	/**
	 * Validates value as int
	 *
	 * @since  1.0.0
	 *
	 * @param $value int
	 *
	 * @return int
	 */
	public function article_validate( $value ) {
		$new_value = intval( $value );

		return ( is_int( $new_value ) ? $new_value : 10 );
	}

	/**
	 * Validates value as int
	 *
	 * @since  1.0.0
	 *
	 * @param $value int
	 *
	 * @return int
	 */
	public function wordcount_validate( $value ) {
		$new_value = intval( $value );

		return ( is_int( $new_value ) ? $new_value : 10 );
	}

	/**
	 * Escapes string
	 *
	 * @since  1.0.0
	 *
	 * @param $value
	 *
	 * @return string
	 */
	public function imagesize_validate( $value ) {

		return $value;
	}

	/**
	 * Queue up the errors
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function add_errors() {
		settings_errors( $this->options_str );
	}

	/**
	 * Creates the settings sections
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param array $section
	 *
	 * @return void
	 */
	protected function create_settings_section( $section ) {
		add_settings_section(
			$section['id'],    // ID
			$section['title'], // Title
			array( $this, 'basic_section_callback' ), // Callback
			$this->plugin_slug // Page
		);
	}

	/**
	 * Creates settings fields
	 *
	 * @since  1.0.0
	 * @access protected
	 *
	 * @param array $settings
	 *              ID = input ID,
	 *              Title = Name of field,
	 *              Field = Type of field,
	 *              Callback = Callback function
	 *              Description = Description below field
	 *
	 * @return void
	 */
	protected function create_settings_field( $settings ) {
		add_settings_field(
			$settings['id'], // ID
			$settings['title'], // Title
			array( $this->form, $settings['callback'] ), // Callback
			$this->plugin_slug, // Page
			$settings['section'], // Section
			array( $settings ) // Args
		);
	}

	/**
	 * Renders Form Object
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @return void
	 */
	public function form() {
		$this->form->xml               = $this->xml;
		$this->form->values            = $this->xml->get_options();
		$this->form->options_str       = $this->options_str;
		$this->form->title             = $this->page_title;
		$this->form->settings_fields   = $this->options_grp;
		$this->form->settings_sections = $this->plugin_slug;
		$this->form->render_form();
	}

	/**
	 * Basic section callback. Creates the settings header.
	 *
	 * @since      1.0.0
	 * @access     public
	 *
	 * @deprecated Used as a placeholder
	 *
	 * @param $args array
	 *
	 * @return void
	 */
	public function basic_section_callback( $args ) {
	}

	/**
	 * To be replaced
	 *
	 * @since      1.0.0
	 * @access     public
	 *
	 * @deprecated Used as a placeholder
	 *
	 * @param $args array
	 *
	 * @return void
	 */
	public function basic_input_callback( $args ) {
	}

	/**
	 * Just double checks something is set before it's returned
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param $value
	 *
	 * @return string
	 */
	protected function input_field_value( $value ) {

		return ( isset( $value ) ? $value : '' );
	}

	/**
	 * Add Message to admin page.
	 *
	 * Will warn users of an issue or add a message saying it was successful.
	 *
	 * @since  1.0.0
	 * @access public
	 *
	 * @param string $message Message to send to user
	 * @param string $type    Type of Message: Error / Updated
	 *
	 * @return void
	 */
	public function new_error( $message, $type ) {
		add_settings_error(
			$this->options_str,
			'settings_updated',
			$message,
			$type
		);
	}
}