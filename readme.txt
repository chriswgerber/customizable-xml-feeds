=== Customizable XML Feeds ===

Contributors: chriswgerber
Tags: feed, xml, exact target, custom xml feed
Requires at least: 3.0.0
Tested up to: 4.2.2
Stable tag: 1.0.0
License: GPLv2  
License URI: http://www.gnu.org/licenses/gpl-2.0.html

Custom XML feeds with adjustable post counts, word counts, and image sizes.

== Description ==

CustomXML uses a custom taxonomy to create rss feeds that can be utilized by Exact Target or
other services.

This plugin creates a custom xml feed, formatted to be usable by Exact Target or other any
other service that relies on information from RSS/XML feeds.

Activating the plugin adds a custom taxonomy called "Email Tags" to posts. This custom
taxonomy is utilized by the plugin to generate custom endpoints for rss feeds. "Email Tags"
are custom tags that appear like any other archive in WordPress. However, custom layout
settings can be applied on the plugin settings page to customize the length of the feed,
the size of the images in the feed, and the length of the content in the feed.

*Note about images:*

* Because of the nature of image sizes, I will not add functionality to go back and resize
all images for the correct categories. If you need to change it and go back to change image
sizes, try the [Regenerate Thumbnails plugin](https://wordpress.org/plugins/regenerate-thumbnails/)

Right now, this is limited to just "posts". However, to add it to additional post types,
create a new filter on 'CustomXML\tag_post_types' to and add the custom post types to the
array.

The plugin also creates a custom excerpt that can be used to override the default grabbed
by the feed. Rather than using a portion of the content to generate the description, it
will use the custom excerpt, if it exists. Otherwise, it'll pull an excerpt from the except,
and finally from the post.

The format of the feed is:

`
<item> 
    <title>Title of the Post</title>
    <link>Permalink to the Post</link>
    <image>Associated Image URL</image>
    <description>Story Description</description>
</item> 
`

Feeds can be located at

1. Base: `example.com/?feed=xtxml`
2. Custom Email Tag: `example.com/email-tag/email-tag-slug/?feed=xtxml`
3. Other taxonomy: `example.com/category/category_name/?feed=xtxml`

**Upcoming**

* Create setting to choose which post types the taxonomy should be registered with. Accessible
currently only by using a filter ('CustomXML\tag_post_types').

== Installation ==

1. Upload `customizable-xml-feeds` to the `/wp-content/plugins/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Create email tags to be used in stories.
4. Go to the settings page and configure layout for the tags.
5. Tag stories to appear in those feeds.

== Changelog ==

= Version 1.0.0 =

* Initial Release

== Screenshots ==

= Full View of the Feed =
[Feed View](http://www.chriswgerber.com/assets/uploads/2015/07/xtxml-feed-view.png)

= View of Single Post: Except and Email Tags taxonomy visible =
[Single Post](http://www.chriswgerber.com/assets/uploads/2015/07/xtxml-feed-post-view.png)

= Feed Settings Page =
[Settings Page](http://www.chriswgerber.com/assets/uploads/2015/07/xtxml-feed-settings-page.png)

= List of Tags =
[Tags Page](http://www.chriswgerber.com/assets/uploads/2015/07/xtxml-feed-tags-page.png)