=== Payment Gateway Based Fees and Discounts for WooCommerce ===
Contributors: algoritmika,anbinder
Tags: woocommerce,payment,gateway,fee,discount
Requires at least: 4.4
Tested up to: 4.6
Stable tag: 2.1.1
License: GNU General Public License v3.0
License URI: http://www.gnu.org/licenses/gpl-3.0.html

Set fees or discounts for WooCommerce payment gateways.

== Description ==

**Payment Gateway Based Fees and Discounts for WooCommerce** plugin extends WooCommerce by adding options to set **fees or discounts based on customer selected payment gateway**.

Payment gateway based fees and discounts can be added to **all payment gateways** including:

* standard WooCommerce payment gateways (Direct Bank Transfer (BACS), Cheque Payment, Cash on Delivery and PayPal),
* custom payment gateways added with any other plugin.

Fees and discounts can be set:

* globally for all products, or
* on per product basis.

Plugin requires **minimum setup**: after enabling the fee/discount for selected gateway (in WooCommerce > Settings > Payment Gateway Based Fees and Discounts), you can set:

* fee/discount value,
* fee/discount type: fixed or percent,
* additional fee/discount,
* minimum and maximum fee/discount values,
* minimum and/or maximum cart amount for adding the fee/discount,
* rounding options,
* taxation options,
* shipping options,
* product categories,
* customer countries.

= Feedback =
* We are open to your suggestions and feedback. Thank you for using or trying out one of our plugins!
* Drop us a line at http://www.algoritmika.com

= More =
* Visit the *Payment Gateway Based Fees and Discounts for WooCommerce* plugin page at http://coder.fm/item/payment-gateway-based-fees-and-discounts-for-woocommerce-plugin/

== Installation ==

1. Upload the entire 'checkout-fees-for-woocommerce' folder to the '/wp-content/plugins/' directory
2. Activate the plugin through the 'Plugins' menu in WordPress
3. Go to WooCommerce > Settings > Payment Gateway Based Fees and Discounts.

== Changelog ==

= 2.1.1 - 04/10/2016 =
* Fix - Bug when local & global fees have same title, fixed.
* Dev - "Max Range Options" options section added to "General" settings.
* Dev - "Override Global Fee" (main and additional) option added to per product settings.
* Dev - "Minimum Fee Value", "Maximum Fee Value", "Minimum Additional Fee Value" and "Maximum Additional Fee Value" options added to both local and global fees.
* Fix - Categories restrictions wrongly applied to per product fees, fixed.
* Tweak - "Minimum Cart Amount" and "Maximum Cart Amount" options title modified.

= 2.1.0 - 20/08/2016 =
* Fix - Tax bug fixed when "Categories to include - Calculation type" is equal to "Only for selected products".
* Fix - Tax bug fixed in info.
* Fix - "Categories to include - Calculation type" with value "Only for selected products" fixed when displaying info.
* Dev - "Categories to exclude - Calculation type." option added.
* Dev - "Categories to include (additional fee)." and "Categories to exclude (additional fee)." options added.
* Dev - Version system added.
* Tweak - Author changed.
* Tweak - Plugin renamed.

= 2.0.2 - 06/08/2016 =
* Dev - Multisite support added.
* Dev - "Variable Products Info" option added.
* Dev - "Categories to include - Calculation type" option added to global fees.
* Fix - `sanitize_title()` added to `add_gateway_fees_settings_hook()` and removed from `output_sections()`.
* Dev - Language (POT) file added.
* Tweak - Author changed.

= 2.0.1 - 10/03/2016 =
* Fix - Additional checks in `add_gateway_fees_settings_hook()`.

= 2.0.0 - 01/03/2016 =
* Dev - `%product_title%`, `%product_variation_atts%` added.
* Fix - Checked tab in admin per product fees is marked now.
* Fix - Info on Single Product bugs fixed: for variable products; for percent fees.
* Dev - Info on Single Product - `[alg_show_checkout_fees_full_info]` and `[alg_show_checkout_fees_lowest_price_info]` shortcodes added.
* Dev - Info on Single Product - Lowest Price Info on Single Product Page added.
* Dev - Info on Single Product - `%gateway_fee_title%` and `%gateway_fee_value%` removed from info.
* Dev - "Add Product Title to Fee/Discount Title" option added to "General > Fees/Discounts per Product" settings.
* Dev - "Hide Gateways Fees and Discounts on Cart Page" option added to "General" settings.
* Dev - "Exclude Shipping" option added for both global and per product fees.
* Dev - "Title" option added for optional "Additional fee" (per product and global).
* Dev - "Customer Countries" (include / exclude) options added to global fees.
* Dev - "Product Categories" (include / exclude) options added to global fees.
* Dev - Compatibility with "Aelia Currency Switcher for WooCommerce" plugin added (for fixed fees; for percent fees compatibility was already there).
* Dev - "Fee Calculation (for Fixed Fees)" options (once / by product quantity) added to per product fees.
* Dev - "Fee Calculation (for Percent Fees)" options (for all cart / by product) added to per product fees.
* Fix - "General" section in admin settings menu is marked bold by default.

= 1.3.0 - 27/10/2015 =
* Dev - Second optional fee added.

= 1.2.0 - 30/09/2015 =
* Dev - Checkout fees/discounts info on single product frontend page added.

= 1.1.0 - 04/09/2015 =
* Dev - Checkout fees/discounts on per product basis added.

= 1.0.0 - 29/08/2015 =
* Initial Release.

== Upgrade Notice ==

= 1.0.0 =
This is the first release of the plugin.