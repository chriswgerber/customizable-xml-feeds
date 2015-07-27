<?php
/**
 * Customizable XML Feeds Bootstrap File
 *
 * @wordpress-plugin
 *
 * @link              http://www.chriswgerber.com/custom-xml-feeds
 * @package           Custom-XML-Feeds
 *
 * Plugin Name:       Customizable XML Feeds
 * Description:       Make customizable XML Feeds for MailChimp, Exact Target, and more
 * Author:            Chris W. Gerber
 * Plugin URI:        https://www.github.com/ThatGerber/customizable-xml-feeds
 * Author URI:        http://www.chriswgerber.com/
 * License:           GPL2
 * Github URI:        https://www.github.com/ThatGerber/customizable-xml-feeds
 * Github Branch:     stable
 * Version:           1.0.0
 */
namespace CustomXMLFeeds;
/* Files */
include 'includes/Form.php';
include 'includes/CustomXML.php';
include 'includes/Feed.php';
include 'includes/Metabox.php';
include 'includes/admin/Page.php';
include 'includes/admin/Form.php';

/* Fires up the instance */
$custom_xml_feeds                = new CustomXML;
$custom_xml_feeds->template_dir  = dirname( __FILE__ );
$custom_xml_feeds->options_str   = 'custom_xml_feeds';
$custom_xml_feeds->taxonomy_slug = 'email-tags';
$custom_xml_feeds->taxonomy_name = 'Email Tags';
$custom_xml_feeds->defaults      = array(
	'post_count' => 10,
	'word_count' => 25,
	'image_size' => '125x125'
);
/* Register the Email Tag Taxonomy */
add_action( 'init', array( $custom_xml_feeds, 'register_taxonomy' ), 0 );
/* Adds image sizes */
add_action( 'wp_loaded', array( $custom_xml_feeds, 'add_image_sizes' ) );
/* Metabox */
$custom_xml_feeds->metabox = new Metabox;
/* Settings */
$custom_xml_feeds->metabox->id         = 'custom_xml_feed';
$custom_xml_feeds->metabox->title      = 'Except for Enewsletter';
$custom_xml_feeds->metabox->post_types = $custom_xml_feeds->post_types();
$custom_xml_feeds->metabox->register_metaboxes();
$custom_xml_feeds->metabox->register_save_data();

/* Adds XML feed */
$custom_xml_feeds->feed = new Feed( $custom_xml_feeds );
add_action( 'do_feed_customxml', array( $custom_xml_feeds->feed, 'get_feed' ) );
/* Taxonomy Create/Update/Delete Hooks */
// Create
add_action(
	"created_{$custom_xml_feeds->taxonomy_slug}",
	array( $custom_xml_feeds, 'update_option' ),
	10,
	2
);
// Delete
add_action(
	"deleted_{$custom_xml_feeds->taxonomy_slug}",
	array( $custom_xml_feeds, 'delete_option' ),
	10,
	2
);
/* Admin */
if ( is_admin() ) {
	/* Setup form */
	$custom_xml_admin              = new Admin_Page();
	$custom_xml_admin->form        = new Admin\Form();
	$custom_xml_admin->xml         = $custom_xml_feeds;
	$custom_xml_admin->page_title  = 'Custom XML Feeds';
	$custom_xml_admin->menu_title  = 'Custom XML Feeds';
	$custom_xml_admin->user_cap    = 'manage_options';
	$custom_xml_admin->plugin_slug = 'customizable-xml-feeds';
	$custom_xml_admin->set_options_string( $custom_xml_feeds->options_str );
	/* Check for custom form submit value. If it exists, update the data that is there. */
	if ( filter_input( INPUT_POST, 'submit' ) == 'Update Tags' ) {
		$custom_xml_admin->update_tags();
	}
	/* Admin Scripts */
	add_action( 'admin_enqueue_scripts', array( $custom_xml_admin, 'scripts_and_styles' ) );
	/* General Section */
	add_filter( $custom_xml_admin->options_str . '_sections', ( function ( $sections ) {
		$sections['import_data'] = array(
			'id'    => 'manage_feeds',
			'title' => 'Manage Feeds'
		);

		return $sections;
	} ) );
	/* Setting */
	add_filter( $custom_xml_admin->options_str . '_fields', ( function ( $fields ) {
		$fields['intro'] = array(
			'id'          => 'intro',
			'field'       => 'paragraph',
			'callback'    => 'paragraph',
			'title'       => 'Using the email tag manager',
			'section'     => 'manage_feeds',
			'description' => 'Edit the settings for your tag here.'
		);

		return $fields;
	} ) );
	/* Tag Settings Page */
	add_action( 'admin_menu', array( $custom_xml_admin, 'register_menu_page' ) );
	add_action( 'admin_init', array( $custom_xml_admin, 'menu_page_init' ) );
}