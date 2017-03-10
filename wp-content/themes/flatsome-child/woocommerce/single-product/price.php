<?php
/**
 * Single Product Price, including microdata for SEO
 *
 * This template can be overridden by copying it to yourtheme/woocommerce/single-product/price.php.
 *
 * HOWEVER, on occasion WooCommerce will need to update template files and you
 * (the theme developer) will need to copy the new files to your theme to
 * maintain compatibility. We try to do this as little as possible, but it does
 * happen. When this occurs the version of the template file will be bumped and
 * the readme will list any important changes.
 *
 * @see     https://docs.woocommerce.com/document/template-structure/
 * @author  WooThemes
 * @package WooCommerce/Templates
 * @version 2.4.9
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

global $product;

?>
<div itemprop="offers" itemscope itemtype="http://schema.org/Offer">

<?php
    $simple_product_price = $product->get_price_html(); // price without VAT
    $simple_product_no_vat = $product->get_price_excluding_tax();  // price with included VAT
    $simple_product_vat = $simple_product_total - $simple_product_price; // VAT price amount
?>
    <p class="price"><?php echo $simple_product_price; ?></p>
    
    <meta itemprop="price" content="<?php echo esc_attr( $product->get_price() ); ?>" />
    <meta itemprop="priceCurrency" content="<?php echo esc_attr( get_woocommerce_currency() ); ?>" />
    <link itemprop="availability" href="http://schema.org/<?php echo $product->is_in_stock() ? 'InStock' : 'OutOfStock'; ?>" />

</div>

