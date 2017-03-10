=== Img Title Removal ===
Contributors: iglauser
Donate link: http://glauserconsulting.com/
Plugin URL: http://glauserconsulting.com/en/other/
Tags: img, image, images, plugin, picture, pictures, thumbnail, thumbnails, tag, attribute, title, hovering, mouse, remove, hide
Requires at least: 2.5
Tested up to: 3.2
Stable tag: 1.1


Plugin that hide all title attribute-tags from images in posts.

== Description ==

Normally the title attribute of an image show up when hovering the mouse over an image. This behavior can easily be avoided by removing the title attribute-tag from the image. However if the site is already crowded with hundreds of images, removing each and every title attribute-tag can be a time consuming job. This plugin simply filters out all title attribute-tags before they are sent out to the browser. Install into the theme plugin directory, activate, and you're done! No more titles show up when hovering the mouse over images on your site!


== Installation ==

1. Upload 'img-title-removal.php' to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress


== How it Works ==

All the plugin do is adding a hook to 'the_content' and to 'post_thumbnail_html' and removes all title-tags before they are sent to the browser. The plugin will only work for what is contained in 'the_content' and the 'post_thumbnail_html', i.e. it will not remove title attribute-tags from other parts of the site.


== Frequently Asked Questions ==

= Will the plugin remove all original img-titles that once were entered in the posts? =

No, the plugin will not remove any title tag from any post. It will only filter them out before they are sent to the browser.


== Changelog ==

= 1.1 =
* Fixed reported compatibility issues with other plugins (Portfolio Slideshow).

= 1.0 =
* Added support for also removing titel tags from thumbnails. Many thanks to Luisa Ambros Costa for giving me the tip to fix this!

= 0.2.1 =
* First version.


== Licence ==

This plugin is free for everyone! Since it's released under the GPL, you can use it free of charge on your personal or commercial blog. :)
