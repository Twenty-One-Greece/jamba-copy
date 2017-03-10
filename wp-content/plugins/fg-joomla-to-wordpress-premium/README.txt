=== FG Joomla to WordPress Premium ===
Contributors: Frédéric GILLES
Plugin Uri: https://www.fredericgilles.net/fg-joomla-to-wordpress/
Tags: joomla, mambo, elxis, wordpress, importer, convert joomla to wordpress, migrate joomla to wordpress, joomla to wordpress migration, migrator, converter, import, k2, jcomments, joomlacomments, jomcomment, flexicontent, postviews, joomlatags, sh404sef, attachments, rokbox, kunena, phocagallery, phoca, joomsef, opensef, easyblog, zoo, zooitems, joomfish, joom!fish, wpml, joomgallery, jevents, contact directory, docman, virtuemart, woocommerce, jreviews, mosets tree, wpml, simple image gallery, rsgallery, community builder
Requires at least: 4.4
Tested up to: 4.7
Stable tag: 3.21.0
License: GPL-2.0+
License URI: http://www.gnu.org/licenses/gpl-2.0.html

A plugin to migrate categories, posts, tags, images, medias, menus and users from Joomla to WordPress

== Description ==

This plugin migrates sections, categories, posts, images, medias, tags, menus and users from Joomla to Wordpress.

This is the Premium version of the plugin FG Joomla to WordPress.
It has been tested with **Joomla versions 1.0 through 3.6**, **Mambo 4.5 and 4.6**, **Elxis** and **Wordpress 4.7** on huge databases (72 000+ posts). It is compatible with multisite installations.

Major features include:

* migrates Joomla sections as categories
* migrates categories as sub-categories
* migrates Joomla posts (published, unpublished and archived)
* migrates Joomla web links
* uploads all the posts media in WP uploads directories (as an option)
* uploads external media (as an option)
* modifies the post content to keep the media links
* resizes images according to the sizes defined in WP
* defines the featured image to be the first post image
* keeps the alt image attribute
* migrates the authors
* migrates the authors aliases
* modifies the internal links
* migrates meta keywords as tags
* migrates page breaks
* SEO: redirects Joomla URLs to the new WordPress URLs
* SEO: option to keep the Joomla IDs
* SEO: migrates the meta description and the meta keywords to WordPress SEO by Yoast
* can import Joomla posts as posts or pages
* migrates Joomla 1.0 static articles as pages
* migrates all the users except authors as subscribers
* migrates users passwords
* migrates Joomla 2.5+ featured images
* migrates Joomla 3.1+ tags
* migrates navigation menus
* partial imports : ability to skip some parts of the import: categories, articles, web links, users or menus
* migrates Mambo data
* migrates Elxis data (Joomla 1.0 fork)

No need to subscribe to an external web site.

= Add-ons =

The Premium version allows the use of add-ons that enhance functionality:

* K2
* EasyBlog
* Flexicontent
* Zoo
* Kunena forum
* sh404sef
* JoomSEF
* OpenSEF
* WP-PostViews (keep Joomla hits)
* JComments
* JomComment
* Joomlatags
* Attachments
* Rokbox
* JoomGallery
* Phocagallery
* Joom!Fish translations to WPML
* JEvents events
* Contact Manager
* Docman
* Virtuemart
* JReviews
* Mosets Tree
* User Groups
* WPML
* Simple Image Gallery & Simple Image Gallery Pro
* RSGallery
* Community Builder

These modules can be purchased on: https://www.fredericgilles.net/fg-joomla-to-wordpress/add-ons/

== Installation ==

1.  Install the plugin in the Admin => Plugins menu => Add New => Upload => Select the zip file => Install Now
2.  Activate Plugin in the Admin => Plugins Menu
3.  Run the importer in Tools > Import > Joomla (FG)
4.  Configure the plugin settings. You can find the Joomla database parameters in the Joomla file configuration.php<br />
    Hostname = $host<br />
    Port     = 3306 (standard MySQL port)<br />
    Database = $db<br />
    Username = $user<br />
    Password = $password<br />
    Joomla Table Prefix = $dbprefix

== Frequently Asked Questions ==

The FAQ is available on https://www.fredericgilles.net/support/kb/faq.php?cid=1

You can let a comment or report a bug on the Support Center: https://www.fredericgilles.net/support/

== Screenshots ==

1. Parameters screen

== Translations ==
* English (default)
* French (fr_FR)
* Spanish (es_ES)
* Italian (it_IT)
* German (de_DE)
* Russian (ru_RU)
* Polish (pl_PL)
* Bulgarian (bg_BG)
* Brazilian (pt_BR)
* other can be translated

== Changelog ==

= 3.21.0 =
* New: Add an option to remove the accents from the medias (useful on Windows)
* Tested with WordPress 4.7

= 3.20.6 =
* Fixed: Existing images attached to imported posts were removed when deleting the imported data
* Fixed: Typo in Italian translation

= 3.20.5 =
* Fixed: Images not imported on HTTPS sites: Warning: fsockopen(): unable to connect to https::80 (php_network_getaddresses: getaddrinfo failed: nodename nor servname provided, or not known)

= 3.20.4 =
* Fixed: Wrong progress bar color

= 3.20.3 =
* Fixed: The progress bar didn't move during the first import
* Fixed: The log window was empty during the first import

= 3.20.2 =
* New: Check if the Community Builder module is required
* Fixed: The "IMPORT COMPLETE" message was still displayed when the import was run again

= 3.20.1 =
* Fixed: The images protected by a user agent protection were not imported

= 3.20.0 =
* Tweak: Code refactoring

= 3.19.1 =
* Fixed: Database passwords containing "<" were not accepted

= 3.19.0 =
* New: Modify the tags links in the post content

= 3.18.0 =
* New: Authorize the connections to Web sites that use invalid SSL certificates
* Tweak: If the import is blocked, stop sending AJAX requests

= 3.17.2 =
* Fixed: Review link broken
* Fixed: Imported tags were not removed when removing imported data only

= 3.17.1 =
* Fixed: Missing link between the post and its featured image
* Fixed: Wrong number of comments displayed
* Tested with WordPress 4.6.1

= 3.17.0 =
* New: Allow the redirects of the EasyBlog categories

= 3.16.0 =
* New: Display the number of data found in the Joomla database before importing
* New: Display the needed modules as warnings before importing
* Tested with WordPress 4.6

= 3.15.3 =
* Tweak: Code optimization

= 3.15.2 =
* Fixed: the "Modify internal links" function could break some links

= 3.15.1 =
* Fixed: Internal links like catid=XXX&id=YYY were not modified
* Tweak: Speed up and reduce the memory consumed by the modification of the internal links

= 3.15.0 =
* New translation: Italian

= 3.14.0 =
* New: Option to create submenus for the categories menus
* New: Compatible with Joomla 3.6

= 3.13.4 =
* Fixed: Regression from 3.13.2: some URLs didn't redirect
* New: Redirect the URLs translated with WPML

= 3.13.3 =
* Fixed: Display an error message when the process hangs
* Tweak: Increase the speed of counting the terms

= 3.13.2 =
* Fixed: SQL injection security breach
* New: Ability to stop the import during the import of users
* Change: Remove the Paypal Donate button
* Tested with WordPress 4.5.3

= 3.13.1 =
* Fixed: Don't import the introtext in the post content if it is marked as hidden on Joomla

= 3.13.0 =
* New: Compatibility between the Joom!Fish and Docman add-ons

= 3.12.1 =
* Fixed: Wrong redirect when an attachment has the same name as a post

= 3.12.0 =
* Fixed: Rewrite the function to delete only the imported data
* Fixed: Categories import can hang if the import counter was resetted and the imported categories were not deleted

= 3.11.0 =
* New: Option to import the featured images only

= 3.10.2 =
* Fixed: The message "[ERROR] The import process is still running. Please wait before running it again." sometimes appears after the process has crashed, and it prevents the import process to resume

= 3.10.1 =
* Fixed: Alias menus were not imported correctly
* Fixed: The imported menus were not removed when doing "Remove only new imported posts"

= 3.10.0 =
* New: Add some hooks
* Tweak: Code optimization

= 3.9.1 =
* Fixed: Images with line breaks inside the tag were not imported
* Tested with WordPress 4.5.2

= 3.9.0 =
* New: Allow image filenames starting with //

= 3.8.0 =
* Tweak: Add functions useful for add-ons
* Tested with WordPress 4.5.1

= 3.7.0 =
* New: Ability to stop the log window auto-refresh

= 3.6.1 =
* Tested with WordPress 4.5

= 3.6.0 =
* New: Compatible with Joomla 3.5
* Fixed: Images without slashes in their path were not imported

= 3.5.1 =
* Fixed: Warning: array_keys() expects parameter 1 to be array, boolean given
* Fixed: Import stopped when a post has no title or no content
* Fixed: The first image was not removed from the content when used both in the intro text and in the full text

= 3.5.0 =
* New: Compatibility with Elxis (Joomla 1.0 fork)
* Fixed: Notice: Undefined variable: imported_tags
* Fixed: the progress bar was resetted when resuming the import
* Fixed: the fulltext image was not inserted in the post content when there was an intro image used as the featured image and when the "Remove first image" option was selected

= 3.4.0 =
* New: Modify the first image options
* New: Ability to import the intro image as the featured image
* Tweak: Code refactoring

= 3.3.1 =
* Fixed: Error :SQLSTATE[42S22]: Column not found: 1054 Unknown column 'c.extension' in 'where clause'

= 3.3.0 =
* New: Use the WordPress FTP API instead of the phpseclib library
* New: Better handle the progress bar
* New: Don't log the [COUNT] data in the log window
* Fixed: Browser tab crashed when too much data was displayed in the log window

= 3.2.0 =
* New: Modify the Joomla SEF links

= 3.1.0 =
* New: Brazilian translation added
* Fixed: When choosing "Import first image as featured only", the first image was not removed from content if it was surrounded by a hyperlink

= 3.0.8 =
* Fixed: Categories not redirected
* Fixed: The pages keeping the old URLs were using the post template instead of the page template

= 3.0.7 =
* Fixed: The URLs were redirected even when the Redirect checkbox was not selected
* Fixed: The home page was redirected to the first page found in the fg_redirect table

= 3.0.6 =
* Fixed: the Joomla 1.0 {mospagebreak} shortcodes were not imported if they contained an attribute

= 3.0.5 =
* New: Redirect the URLs prefixed by a directory
* Tweak: Restructure the redirect module and add unit tests

= 3.0.4 =
* New: Redirect the relative URLs

= 3.0.3 =
* Fixed: Articles got the unassigned category when the category is a duplicate

= 3.0.2 =
* Fixed: Infinite loop when some categories have duplicate names

= 3.0.1 =
* Fixed: After a resume, the posts were imported as uncategorized
* Tested with WordPress 4.4.2

= 3.0.0 =
* New: Run the import in AJAX
* New: Add a progress bar
* New: Add a logger frame to see the logs in real time
* New: Ability to stop the import
* New: Compatible with PHP 7

= 2.14.1 =
* Fixed: Medias with relative paths were not uploaded to the right folder when not using month- and year-based folders

= 2.14.0 =
* New: For the articles and categories whose alias is a date, the imported slug will be the title and not the alias

= 2.13.0 =
* New: Keep the Joomla media folder tree when the uploads are not organized into month- and year-based folders

= 2.12.0 =
* New: Import the Joomla 2.5+ intro image as a featured image but don't include it in the post content

= 2.11.1 =
* Tested with WordPress 4.4.1

= 2.11.0 =
* New: Redirect the /category/ URLs
* Fixed: Categories with null description were not imported

= 2.10.0 =
* Tweak: Use the WordPress 4.4 term metas: performance improved, nomore need to add a category prefix
* New: Redirect the categories and sections URLs containing view=category or view=section
* Tweak: Optimize code
* Fixed: The notices and errors were sometimes displayed before the header is sent
* Fixed: Categories with duplicated names were not imported
* Fixed: The cache for the taxonomies different from category was not cleaned

= 2.9.3 =
* Fixed: Phocagallery menu items were not imported

= 2.9.2 =
* Tested with WordPress 4.4

= 2.9.1 =
* New: Support the accented Greek characters

= 2.9.0 =
* New: Add SFTP protocol
* New: Import the {audio} tag
* New: Add a link to the FAQ in the connection error message

= 2.8.0 =
* New: Add an Import link on the plugins list page

= 2.7.4 =
* Tweak: Code refactoring for unit tests

= 2.7.3 =
* New: Add the hook 'fgj2wp_get_wp_post_from_joomla_url'
* Tweak: Code refactoring

= 2.7.2 =
* Fixed: Notice: Undefined index: usertype

= 2.7.1 =
* Fixed: Don't display the warning about WPML if JoomFish is used
* Tweak: Change the range of the get_wp_post_id_from_joomla_id() function to be available for add-ons

= 2.7.0 =
* New: Add the FTP settings (used for Simple Image Gallery add-on)

= 2.6.1 =
* New: Redirect URLs like aaa (without .html) and aaa/99 (without the article name in the URL)

= 2.6.0 =
* New: Make the platform more accessible to more languages
* Update all the translations

= 2.5.2 =
* New: Check if we need the WPML module
* Fixed: Remove the message that says to get the Premium version if we already have got the Premium version

= 2.5.1 =
* Tested with WordPress 4.3.1

= 2.5.0 =
* New: Add the hook "fgj2wp_get_menus_add_extra_cols" for menus translations
* Fixed some translations

= 2.4.2 =
* Fixed: Warning: stripos() expects parameter 1 to be string, array given (regression from 2.4.1)

= 2.4.1 =
* Tweak: Optimize the SQL queries to get the user roles

= 2.4.0 =
* New: Add an anti-duplicate test if the user runs another import process again while one is still running
* New: Reset the wp_users autoincrement
* Fixed: Prevent the change password email to be sent when the users log in for the first time
* Fixed: Solve conflicts between FG plugins by limiting the Javascript scope

= 2.3.3 =
* Fixed: Some medias with accents were not imported
* Fixed: Avoid a double slash in the URLs
* Tweak: Add the fgj2wpp_post_get_authors that is required for the authors in Zoo
* Tested with WordPress 4.3

= 2.3.2 =
* Tested with WordPress 4.2.4

= 2.3.1 =
* Tested with WordPress 4.2.3

= 2.3.0 =
* New: Change the video links {"video"} to WordPress video tags

= 2.2.2 =
* Fixed: Fatal error: Call to a member function fetch() on a non-object

= 2.2.1 =
* Fixed: Regression bug (since 2.2.0) in the users import function that prevents the import of customer infos

= 2.2.0 =
* Tweak: Improve the method of importing users. It can now import more than 100000 users.
* Tested with WordPress 4.2.2

= 2.1.2 =
* Mosets Tree listings from Joomla 1.0 were imported as pages instead of listings
* Tested with WordPress 4.2.1

= 2.1.1 =
* Tested with WordPress 4.2

= 2.1.0 =
* Tweak: Restructure and optimize the images import functions
* Tweak: Move the suspend cache functions into the dispatch method

= 2.0.0 =
* Restructure the whole code using the BoilerPlate foundation

= 1.46.2 =
* Tweak: Add the hook fgj2wpp_post_add_user for the UserGroups module
* Update FAQ

= 1.46.1 =
* Fixed: Remove duplicate hook in weblinks.php
* New add-on: User Groups

= 1.46.0 =
* New: Compatible with Joomla 3.4 (ignore weblinks)

= 1.45.0 =
* New: Log the messages to wp-content/debug.log
* Tweak: Code optimization

= 1.44.3 =
* Fixed: Import images even when there are linefeeds in the img tags

= 1.44.2 =
* Fixed: Don't import the posts as duplicates if the categories are duplicated on Joomla
* Tested with WordPress 4.1.1

= 1.44.0 =
* New: Add Mosets Tree module
* Fixed: the joomla_query() function was returning only one row
* Update the German translation (thanks to Tobias C.)
* Update the Spanish translation (thanks to Jacob R.)

= 1.43.4 =
* Fixed: Multisite: Links that contain ":" were corrupted
* FAQ updated

= 1.43.3 =
* Tweak: Add the fgj2wpp_pre_import_users action hook
* FAQ updated

= 1.43.2 =
* Tweak: Add hooks in the modify_links functions

= 1.43.1 =
* Fixed: Remove empty lines in Joomla 1.0/Mambo mosimage that result in broken links
* Fixed: The images path was not replaced in the posts content on Joomla 1.0/Mambo
* Fixed: Fatal error: Call to undefined function password_verify()

= 1.43.0 =
* Fixed: Notice: Undefined variable: imported_menus
* FAQ updated
* Tested with WordPress 4.1

= 1.42.1 =
* Tweak: Don't display the timeout field if the medias are skipped
* Tweak: Create the class fgj2wpp_tools

= 1.42.0 =
* New: Redirect the menu links
* New: Import the Joomla 1.5 alias menus (menulink type)

= 1.41.1 =
* Fixed: Attach the menus to their first nearest known ancestor

= 1.41.0 =
* New: Keep the anchor link when modifying the internal links
* New: Import the parent pages from the menu hierarchy when importing as pages
* Fixed: The post slug was imported from the menu name and not from the menu alias when using the option "Set the post slugs from menus instead of aliases"
* Tested with WordPress 4.0.1

= 1.40.0 =
* New: Import the alias menus
* New: Import the custom HTML modules
* Update the German translation (thanks to Tobias C.)

= 1.39.6 =
* Add filters to authorize the add-ons to change the partial import options

= 1.39.5 =
* Update the Spanish translation (thanks to Bradis García L.)

= 1.39.4 =
* Fixed: Allow backslashes in the articles content

= 1.39.3 =
* Fixed: Remove extra slashes in the media filenames

= 1.39.2 =
* Fixed: URLs were not redirected when using FastCGI (http://php.net/manual/fr/function.filter-input.php#77307)

= 1.39.1 =
* Tweak: Simplify the posts count function

= 1.39.0 =
* New: Add a timeout option

= 1.38.1 =
* New: Import the meta title from the menus
* New: Import the captions of Joomla 2.5 featured images
* Fixed: Some image captions were not imported

= 1.38.0 =
* Fixed: The media filename was empty on the attachment page
* Tested with WordPress 4.0

= 1.37.0 =
* New: Help screen
* New: Set the pages slugs from the menus instead of the aliases
* New: Set the meta data from the menus instead of the articles

= 1.36.2 =
* New: Enable the K2 advanced SEF URL redirect

= 1.36.1 =
* New: Improve the speed of the menus import
* New: Function to get the Joomla imported sections

= 1.36.0 =
* New: Functions to get the Joomla imported posts, categories and users
* Fixed: Users were not authenticated with their Joomla passwords for Joomla 2.5+
* New add-on: JReviews

= 1.35.0 =
* New: Function to get the Joomla installation language
* New add-on: Virtuemart to WooCommerce
* Tested with WordPress 3.9.2

= 1.34.2 =
* Fixed: Define the width and the height of the images only if it isn't defined yet

= 1.34.1 =
* New: Modify the internal links for both posts, pages and custom post types
* New: Import the menus with relative URLs

= 1.34.0 =
* New: Option to get the post slugs from menus instead of aliases
* New: Add option to automatically remove the WordPress content before each import

= 1.33.0 =
* New: Partial imports: options to skip the import of categories, articles, web links, users or menus

= 1.32.0 =
* New: Display the number of Joomla articles, categories, users and web links during the database connection test
* New: Compatibility with Joomla 3.3

= 1.31.5 =
* New: Remove the categories prefix for Docman categories

= 1.31.4 =
* New: Redirect the URLs with the parameter task=view
* Fixed: Warning: Creating default object from empty value

= 1.31.3 =
* Fixed: "Fatal error: Call to a member function fetch() on a non-object" for versions of MySQL < 5.0.3

= 1.31.2 =
* New add-on: Docman
* Tested with WordPress 3.9.1

= 1.31.1 =
* New: Add a parameter to force the external media import (for PhocaGallery)

= 1.31.0 =
* New: Import Web links
* New function get_component_categories() for add-ons

= 1.30.0 =
* New: Import the menu separators
* New: Refactor the menus import
* Tested with WordPress 3.9

= 1.29.4 =
* New: Change the visibility of some methods to use them in add-ons
* New: Add JEvents module
* Fixed: Notice: Undefined index: width
* Fixed: Notice: Undefined index: height

= 1.29.3 =
* Fixed: Was displaying the warning "Your version of Joomla (probably 1.0) is not supported by this plugin." when both the Premium and the free versions were activated
* Tested with WordPress 3.8.2

= 1.29.2 =
* New: Change the visibility of some methods to use them in add-ons

= 1.29.1 =
* Fixed: Fatal error: Call to a member function fetch() on a non-object

= 1.29.0 =
* New: The required modules are listed when testing the connection to Joomla

= 1.28.0 =
* New: Nomore need to choose the Joomla version ; it is guessed by the plugin.
* Fixed: the fgj2wp class was instantiated twice

= 1.27.0 =
* New: Import the users' registration date

= 1.26.1 =
* Fixed: The usernames with Cyrillic characters were not imported

= 1.26.0 =
* New: Import Joomla 3.1 tags

= 1.25.1 =
* Fixed: Error:SQLSTATE[42S22]: Column not found: 1054 Unknown column 'u.usertype' in 'field list' for Joomla versions > 1.5

= 1.25.0 =
* New: Import the Joomla administrators as WordPress administrators

= 1.24.4 =
* New: Add some hooks for WPML
* Fixed: Notice Undefined offset

= 1.24.3 =
* Fixed: Don't add the &lt;!--more--&gt; tag if the introtext is empty
* Tested with WordPress 3.8.1

= 1.24.2 =
* Fixed: The URLs were not redirected if the articles were imported as pages

= 1.24.1 =
* Fixed: Syntax error with parse_ini_string
* Fixed: Images containing "%20" were not imported into the post content

= 1.24.0 =
* New: Full refactoring of the URL redirect
* Fixed: Redirect URLs with articles beginning with a number
* New translation: Bulgarian (thanks to Hristo P.)

= 1.23.0 =
* New: Compatibility with Joomla 3.2
* Fixed: Redirect URLs with articles beginning with a number

= 1.22.6 =
* Fixed: The «Remove only new imported posts» option was not removing anything
* Fixed: Notice: Undefined variable: result in fgj2wp-users.php
* Tested with WordPress 3.8

= 1.22.5 =
* Fixed: Archived posts were always imported as drafts in Joomla 2.5
* Fixed: Rewrite rules not deactivated after plugin deactivation

= 1.22.4 =
* New: Display error message if PDO is not enabled

= 1.22.3 =
* New: Drastically improve the speed of the users deletion
* New: Display SQL errors in debug mode
* Fixed: Blank page when the users table or the menus table didn't exist

= 1.22.2 =
* New: Add Zoo support
* New: Check if the upload directory is writable
* Tested with WordPress 3.7.1

= 1.22.1 =
* Fixed: The navigation menus were not imported when the articles were imported as pages

= 1.22.0 =
* Fixed: Import the categories even when the articles are imported as pages
* Tested with WordPress 3.7

= 1.21.4 =
* Fixed: Meta descriptions and meta keywords were overwritten by empty sh404sef meta data

= 1.21.3 =
* Fixed: "Warning: sprintf(): Too few arguments" message for image captions with %

= 1.21.2 =
* New: Enable EasyBlog menus import

= 1.21.1 =
* Fixed: Nested category menus
* Fixed: Joomla 1.0 menus were not imported

= 1.21.0 =
* New translation: Spanish (thanks to Bradis García L.)
* Fixed: Joomla 2.5+ featured images not imported

= 1.20.1 =
* Fixed: Use the modified post date if the creation date is empty
* Fixed: Warning: array_key_exists() [function.array-key-exists]: The second argument should be either an array or an object

= 1.20.0 =
* New: Display the author aliases

= 1.19.4 =
* Fixed: The menus were not imported for Joomla versions > 1.5

= 1.19.3 =
* Fixed: Some spaces were removed (due to the extra newlines removal)
* Fixed: Better rule for the convert_post_attribs_to_array function
* Fixed: "WordPress database error Field 'post_content' doesn't have a default value"

= 1.19.2 =
* Fixed: Fatal error: Call to undefined function parse_ini_string() when PHP < 5.3.0

= 1.19.1 =
* New: Add import of meta title and canonical (used by JoomSEF add-on)

= 1.19.0 =
* New: Import the page breaks
* New: Option to import the Joomla introtext in the post and in the excerpt
* New: Use the show_intro article parameter to import the introtext in the content or not
* Tested with WordPress 3.6.1

= 1.18.0 =
* New: Import users passwords
* New: Compatibility with Joomla 3.1

= 1.17.1 =
* Fixed: Remove extra newlines

= 1.17.0 =
* New: Add automatically http:// at the beginning of the URL if it is missing
* New: Option for the first image import
* FAQ updated

= 1.16.0 =
* New: Option to import images with duplicate names
* New translation: Polish (Thanks to Łukasz Z.)
* FAQ updated

= 1.15.2 =
* Optimize the Joomla connection

= 1.15.1 =
* New: Option to not import archived posts or to import them as drafts or as published posts
* New: Compatibility with Kunena users

= 1.15.0 =
* New: Import archived posts as drafts
* Tested with WordPress 3.6

= 1.14.2 =
* Fixed: The HTML classes were lost in the a-href and img tags
* Unset by default the checkbox «Import the text above the "read more" to the excerpt»

= 1.14.1 =
* Fixed: The caption shortcode is imported twice if the image has a link a-href pointing to a different image

= 1.14.0 =
* New: Import images captions
* New: Migrates the meta description and the meta keywords to WordPress SEO by Yoast
* Improve speed of processing the image links
* Update the FAQ
* Fixed: Warning during activation

= 1.13.0 =
* New: Migrates the Joomla navigation menus
* New: SEO, keeps the Joomla article ID (as an option)
* New: Ability to deactivate the redirects
* Tested with WordPress 3.5.2

= 1.12.1 =
* Fixed: Replaces the publication date by the creation date as Joomla uses the creation date for sorting articles

= 1.12.0 =
* New: Add a button to remove the categories prefixes
* New: Option to not use the first post image as the featured image

= 1.11.0 =
* New: Import external media (as an option)

= 1.10.9 =
* Fixed: Broken links to author pages

= 1.10.8 =
* Fixed: The usernames with Greek characters were not imported

= 1.10.7 =
* New translation: Russian (Thanks to Julia N.)
* New: Compatibility with the dynamicImage mambot (Joomla 1.0 and Mambo)

= 1.10.6 =
* Fixed: Categories hierarchy lost when parent categories had an id greater than their children
* FAQ updated

= 1.10.5 =
* New: Redirect /view URLs
* Fixed: With a multisite installation, delete only the current blog users and not all the multisite users

= 1.10.4 =
* Fixed: Posts were not imported when the skip media option was off

= 1.10.3 =
* Fixed: Categories hierarchy lost when parent categories had an id greater than their children (Joomla 1.6+)
* New: Add hooks for extra images and after saving options

= 1.10.2 =
* Tested with WordPress 3.5.1
* New: Add hooks in the modify_links method

= 1.10.1 =
* New: Add a hook for extra options
* Fixed: Move the fgj2wp_post_empty_database hook
* FAQ updated

= 1.10.0 =
* New: Compatibility with Joomla 3.0
* New: Option to delete only new imported posts without deleting the whole database

= 1.9.2 =
* Fixed: URL redirect works with sticky posts

= 1.9.1 =
* Fixed: the internal links where not modified on pages

= 1.9.0 =
* Tested with WordPress 3.5
* New: Button to test the database connection
* New: Improve the user experience by displaying explanations on the parameters and error messages
* New: get_categories hook modified to be able to migrate non K2 databases even when the K2 module is activated

= 1.8.5 =
* New: Option to not import already imported medias

= 1.8.4 =
* Add a hook for Flexicontent module

= 1.8.3 =
* Fixed: Compatibility issue with WordPress < 3.3

= 1.8.2 =
* Fixed: Cache flushed after the migration

= 1.8.1 =
* New: Better compatibility for copying media: uses the WordPress HTTP API

= 1.8.0 =
* New: Enable modules
* New: Compatibility with PHP 5.1
* New: Compatibility with WordPress 3.0
* New: Better compatibility for copying media (uses cURL)

= 1.7.1 =
* Fixed: Bug in the URL rewriting module

= 1.7.0 =
* FAQ updated

= 1.6.0 =
* New: Compatibility with Joomla 2.5
* New: Migrates Joomla 2.5 featured images
* Code restructuring

= 1.5.0 =
* New: Migrates all the users

= 1.4.0 =
* New: Compatibility with Joomla 1.0 and Mambo 4.5 and 4.6

= 1.3.1 =
* Fixed: Clean the cache after emptying the database
* Fixed: The categories slugs were not imported if they had no alias

= 1.3.0 =
* New: Compatibility with Joomla 1.6 and 1.7

= 1.2.0 =
* New: Can import posts as pages
* Fixed: The keywords were not imported
* Translation: German

= 1.1.1 =
* Fixed: Don't import empty authors aliases
* Update the FAQ

= 1.1.0 =
* Migrates the authors aliases

= 1.0.4 =
* Tested with WordPress 3.4

= 1.0.3 =
* Fixed: Conflict between Joomla URLs and events URLs that begin with numbers

= 1.0.2 =
* Add "c" in the category slug to not be in conflict with the Joomla URLs

= 1.0.1 =
* New: SEO. The Joomla URLs are automatically redirected (301 redirection) to the new WordPress URLs using the permalink settings. So the old URLs don’t appear as « 404 not found » and all traffic is kept.

= 1.0.0 =
* Initial version
