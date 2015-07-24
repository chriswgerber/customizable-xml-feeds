<?php
/**
 * Single RSS Item
 */
global $post;
global $custom_xml_feeds;
?>
<item>
	<title><?php the_title_rss(); ?></title>
	<link><?php the_permalink_rss(); ?></link>
	<image><?php $custom_xml_feeds->feed->feed_image( ); ?></image>
	<description>
		<![CDATA[ <?php $custom_xml_feeds->feed->the_description(); ?> ]]>
	</description>
	<?php do_action('rss2_item'); ?>
</item>