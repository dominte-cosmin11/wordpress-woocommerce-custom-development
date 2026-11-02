<?php
/**
 * Portfolio example: conditional WooCommerce shipping rules.
 *
 * IMPORTANT: Update the shipping class slug and method instance ID for the
 * target store before enabling this example.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function portfolio_disable_free_shipping_for_special_class( $is_available, $package, $method ) {
    $special_class_slug = 'special-shipping-class';

    foreach ( $package['contents'] as $item ) {
        if (
            ! empty( $item['data'] ) &&
            $item['data'] instanceof WC_Product &&
            $item['data']->get_shipping_class() === $special_class_slug
        ) {
            return false;
        }
    }

    return $is_available;
}
add_filter(
    'woocommerce_shipping_free_shipping_is_available',
    'portfolio_disable_free_shipping_for_special_class',
    10,
    3
);

function portfolio_filter_shipping_methods_by_class( $rates, $package ) {
    $special_class_slug = 'special-shipping-class';
    $special_instance_id = 3; // Configure for the target store.

    $has_special_class = false;

    foreach ( $package['contents'] as $item ) {
        if (
            ! empty( $item['data'] ) &&
            $item['data'] instanceof WC_Product &&
            $item['data']->get_shipping_class() === $special_class_slug
        ) {
            $has_special_class = true;
            break;
        }
    }

    foreach ( $rates as $rate_id => $rate ) {
        $is_special_method = (int) $rate->instance_id === $special_instance_id;

        if ( $has_special_class && ! $is_special_method ) {
            unset( $rates[ $rate_id ] );
        } elseif ( ! $has_special_class && $is_special_method ) {
            unset( $rates[ $rate_id ] );
        }
    }

    return $rates;
}
add_filter( 'woocommerce_package_rates', 'portfolio_filter_shipping_methods_by_class', 100, 2 );
