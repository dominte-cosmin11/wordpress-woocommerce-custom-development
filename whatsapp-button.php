<?php
/**
 * Plugin Name: Portfolio - WhatsApp Product Button
 * Description: Adds a WhatsApp inquiry button to WooCommerce product pages.
 * Version: 1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Usage: [bw_whatsapp_button]
 */
function portfolio_whatsapp_button_shortcode() {
    if ( ! function_exists( 'is_product' ) || ! is_product() ) {
        return '';
    }

    global $product;

    if ( ! $product instanceof WC_Product ) {
        return '';
    }

    // Replace with the business WhatsApp number in international format, without "+".
    $phone = '40700000000';

    $product_name = $product->get_name();
    $sku          = $product->get_sku();
    $product_url  = get_permalink( $product->get_id() );

    $message = sprintf(
        "Hello, I am interested in %s. Product code: %s. Product link: %s",
        $product_name,
        $sku ? $sku : 'N/A',
        $product_url
    );

    $whatsapp_url = 'https://wa.me/' . rawurlencode( $phone ) . '?text=' . rawurlencode( $message );

    return sprintf(
        '<a class="portfolio-whatsapp-button" href="%1$s" target="_blank" rel="noopener noreferrer">
            <span aria-hidden="true">WhatsApp</span>
            <span>Contact us about this product</span>
        </a>',
        esc_url( $whatsapp_url )
    );
}
add_shortcode( 'bw_whatsapp_button', 'portfolio_whatsapp_button_shortcode' );
