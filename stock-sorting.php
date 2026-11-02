<?php
/**
 * Portfolio example: show in-stock WooCommerce products first.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function portfolio_add_stock_sorting_option( $sortby ) {
    $sortby['portfolio_stock_first'] = __( 'In stock first', 'portfolio' );
    return $sortby;
}
add_filter( 'woocommerce_catalog_orderby', 'portfolio_add_stock_sorting_option' );

function portfolio_apply_stock_sorting( $args ) {
    if ( is_search() ) {
        return $args;
    }

    $orderby = isset( $_GET['orderby'] )
        ? sanitize_text_field( wp_unslash( $_GET['orderby'] ) )
        : '';

    if ( '' === $orderby || 'portfolio_stock_first' === $orderby ) {
        $args['orderby']  = 'meta_value';
        $args['order']    = 'ASC';
        $args['meta_key'] = '_stock_status';
    }

    return $args;
}
add_filter( 'woocommerce_get_catalog_ordering_args', 'portfolio_apply_stock_sorting' );

function portfolio_default_stock_sorting( $default ) {
    return is_search() ? $default : 'portfolio_stock_first';
}
add_filter( 'woocommerce_default_catalog_orderby', 'portfolio_default_stock_sorting' );
