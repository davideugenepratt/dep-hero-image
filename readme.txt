=== Hero Image ===
Contributors: davideugenepratt
Tags:
Requires at least: 3.7
Tested up to: 4.7
Stable tag: 0.1.0
License: GPLv2 or later
License URI: http://www.gnu.org/licenses/gpl-2.0.html

This plugin is an easy way to add hero images to your WordPress posts and pages.

== Description ==

This plugin is an easy way to add hero images to your WordPress posts and pages.

**Note:** You must have a template that supports these hero images. This plugin alone will not add hero images to your page,
 it only provides the admin interface to do so.

== Installation ==

1. Upload plugin files to the `/wp-content/plugins/dep-hero-image/` directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== Using Hero Image In Your Theme ==

The image url and the html content are stored in two seperate meta values.

To access the image url use:

`<?php echo get_post_custom( $post->ID )[ 'dep_hero_image_url' ]; ?>`

To access the html content use:

`<?php echo get_post_custom( $post->ID )[ 'dep_hero_image_content' ]; ?>`
