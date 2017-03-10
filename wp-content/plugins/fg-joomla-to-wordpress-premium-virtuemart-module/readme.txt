=== FG Joomla to WordPress Premium Virtuemart module ===
Contributors: Frédéric GILLES
Plugin Uri: https://www.fredericgilles.net/fg-joomla-to-wordpress/
Tags: joomla, wordpress, importer, migrator, converter, import, virtuemart, woocommerce
Requires at least: 4.4
Tested up to: 4.7
Stable tag: 2.10.1
License: GPLv2

A plugin to migrate Virtuemart content (Joomla) to WooCommerce (WordPress)
Needs the plugin «FG Joomla to WordPress Premium» to work

== Description ==

This is the Virtuemart module. It works only if the plugins FG Joomla to WordPress Premium and WooCommerce are already installed.
It has been tested with **Joomla versions 1.0, 1.5 and 2.5**, **Virtuemart 1.0, 1.1, 2.0, 2.6 and 3.0** and **Wordpress 4.7**. It is compatible with multisite installations.

Major features include:

* migrates the Virtuemart users informations (billing and shipping data)
* migrates the Virtuemart products
* migrates the products images
* migrates the featured products
* migrates the external products
* migrates the product categories
* migrates the product categories images
* migrates the Virtuemart custom fields as WooCommerce attributes
* migrates the price variations
* migrates the tax rates
* migrates the Virtuemart orders with their items, shipping, fees and taxes
* migrates the Virtuemart menus
* migrates the ratings & reviews
* migrates the coupons
* migrates the manufacturers
* redirects the products URLs with the pattern /ID/
* redirects the products URLs according to their slugs
* redirects the product categories URLs according to their slugs
* modifies the internal links containing product URLs
* option to keep the Virtuemart products IDs

== Installation ==

1.  Prerequesite: Buy and install the plugin «FG Joomla to WordPress Premium»
2.  Extract plugin zip file and load up to your wp-content/plugin directory
3.  Activate Plugin in the Admin => Plugins Menu
4.  Run the importer in Tools > Import > Joomla (FG)

== Translations ==
* English (default)
* French (fr_FR)
* German (de_DE)
* other can be translated

== Changelog ==

= 2.10.1 =
Fixed: Fatal error: Uncaught Error: Call to a member function query() on null

= 2.10.0 =
New: Compatibility with Virtuemart 2.0.20
Fixed: Notice: Undefined index: new_id
Tested with WordPress 4.7

= 2.9.1 =
Fixed: Fatal error: Cannot unset string offsets

= 2.9.0 =
New: Add an option to keep the Virtuemart products IDs

= 2.8.1 =
Fixed: Blank 404 page

= 2.8.0 =
New: Import external products
New: Check if WooCommerce is activated before running the import
Tested with WordPress 4.6

= 2.7.2 =
Fixed: Allow bad characters like "²" in the attribute names

= 2.7.1 =
Fixed: Some orders were not imported. WordPress database error: [Column 'post_excerpt' cannot be null]

= 2.7.0 =
New: Import the Virtuemart tax rates as WooCommerce tax classes

= 2.6.0 =
New: Compatible with WooCommerce 2.6.0

= 2.5.0 =
Fixed: Rewrite the function to delete only the imported data

= 2.4.0 =
New: Import the categories meta data (title, description, keywords) to Yoast SEO
Tested with WordPress 4.5

= 2.3.0 =
New: Redirect product links like /id-productname

= 2.2.0 =
New: Better handle the progress bar
New: Don't log the [COUNT] data in the log window

= 2.1.0 =
New: Ability to stop and resume the import
Fixed: Infinite loop while importing the products
Fixed: The total number of data to import was wrong in case of a partial import
Fixed: Set the default shop language to the site language

= 2.0.0 =
New: Run the import in AJAX
New: Compatible with PHP 7
Fixed: Virtuemart product categories menus were not imported since 1.23.0

= 1.24.1 =
Fixed: Notice: Undefined variable: date

= 1.24.0 =
New: Allow the translations of products and product categories (need the Joom!Fish 1.5+ module)
Fixed: Categories and manufacturers with null descriptions were not imported
Fixed: Compatibility with FG Joomla to WordPress Premium 2.11.0

= 1.23.0 =
Tweak: Use the WordPress 4.4 term metas: performance improved, nomore need to add a category prefix
Tested with WordPress 4.4

= 1.22.0 =
New: Support the attributes with Greek characters

= 1.21.1 =
Fixed: The default shopper group was sometimes wrong, so all prices were free

= 1.21.0 =
New: Compatibility with Virtuemart for Joomla 1.0

= 1.20.0 =
New: Import the product images with absolute paths

= 1.19.0 =
New: Modifies the internal links containing product URLs

= 1.18.0 =
New: Redirects the products URLs according to their slugs
New: Redirects the product categories URLs according to their slugs

= 1.17.0 =
New: Add the stock management option
New: Import Virtuemart 1.x prices with tax
New: Import the override price in the variations
New: purge the tax cache
Tested with WordPress 4.3.1

= 1.16.3 =
Tweak: Optimize some SQL queries

= 1.16.2 =
Fixed: Error:SQLSTATE[42000]: Syntax error or access violation

= 1.16.1 =
Fixed: Cache issue with the product categories

= 1.16.0 =
New: redirects the products URLs with the pattern /ID/
Tested with WordPress 4.3

= 1.15.1 =
Fixed: "product_sku" was imported as a custom value
Tested with WordPress 4.2.4

= 1.15.0 =
New: Import the secondary product images from Virtuemart 1.1

= 1.14.2 =
Fixed: Product images from Virtuemart 1.1 were not imported (bug from 1.14.1)

= 1.14.1 =
New: Import the product images with no mime type
New: Import the product images with relative path

= 1.14.0 =
New: Compatible with WooCommerce Brands Addon
Tested with WordPress 4.2.1

= 1.13.0 =
New: Restructure the product attributes import functions
Tweak: Optimize the SQL queries

= 1.12.2 =
Tweak: Restructure and optimize the images import functions

= 1.12.1 =
Fixed: Import only the images in the gallery

= 1.12.0 =
New: Import Virtuemart manufacturers - Needs the WooCommerce Brands plugin

= 1.11.1 =
Fixed: the joomla_query() function was returning only one row

= 1.11.0 =
New: Compatible with Virtuemart 3.0

= 1.10.2 =
Fixed: Notice: register_taxonomy was called incorrectly. Taxonomies cannot exceed 32 characters in length
Fixed: Products with quantity discounts were imported multiple times
Tested with WordPress 4.1

= 1.10.1 =
Fixed: With some databases, prices were not imported.
Tested with WordPress 4.0.1

= 1.10.0 =
Add the German translation (thanks to Tobias C.)

= 1.9.0 =
New: Add partial import options
Fixed: Set the products with a null quantity as "Out of stock"
Fixed: The product category medias were imported even when we choosed to skip the medias
Fixed: WordPress database error: [Duplicate entry 'xxx-yyy' for key 'PRIMARY']
Fixed: The reviews were duplicated when relaunching the import
Fixed: The coupons were duplicated when relaunching the import

= 1.8.0 =
New: Import the discounted prices

= 1.7.0 =
New: Display the number of Virtuemart products and orders when testing the database connection

= 1.6.1 =
Fixed: Check the Virtuemart version before importing
Fixed: The data were not imported if the Virtuemart language was not the same as the Joomla language

= 1.6.0 =
Fixed: Sometimes the default WooCommerce pages were not recreated.
Fixed: Remove the shop_order_status taxonomy according to WooCommerce 2.2
Tweak: Simplify the posts count function
Compatible with WooCommerce 2.2

= 1.5.0 =
New: Add an option to not include the first image into the product gallery
Tested with WordPress 4.0

= 1.4.1 =
New: Help screen
Fixed: Notice: Undefined property: stdClass::$publish

= 1.4.0 =
New: Import the Virtuemart menus
New: Import the ratings & reviews
New: Import the coupons
New: Manage the custom order statuses

= 1.3.2 =
Fixed: Guest email was not imported

= 1.3.1 =
New: Import the guest orders

= 1.3.0 =
New: Import the meta title, meta description and meta keywords of the products
New: Remove the prefixes from the product categories
New: Import the meta keywords as product tags
New: Compatible with sh404sef
New: Set the «Manage stock» option for a product to false if the stock quantity is 0
New: Ability to import product prices including tax or excluding tax

= 1.2.2 =
Fixed: Don't add the "customer" role to administrator users, otherwise they won't have access to the posts anymore

= 1.2.1 =
Fixed: The message displaying the number of imported images was incorrect

= 1.2.0 =
New: Compatible with Virtuemart 2.x (Joomla 2.5+)
New: Import the Virtuemart products
New: Import the Virtuemart custom fields
New: Import the Virtuemart orders
Fixed: Customers without state were not imported
Tested with WordPress 3.9.2

= 1.1.0 =
New: Put the state name instead of the state code in the billing and shipping addresses
New: Put the 2 letter country code in the billing and shipping addresses

= 1.0.2 =
Fixed: the user data were not imported if the WordPress prefix was different from the default one (wp_).

= 1.0.1 =
Fixed: administrator role was replaced by customer role

= 1.0.0 =
Migrates Virtuemart user informations (billing and shipping data)
