<?php
/**
 * Portfolio examples: WooCommerce product search enhancements.
 *
 * The FiboSearch filters below require the FiboSearch plugin.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/**
 * Restrict the main front-end search query to a matching product brand.
 */
function portfolio_filter_search_by_brand( $query ) {
    if ( is_admin() || ! $query->is_main_query() || ! $query->is_search() ) {
        return;
    }

    $search = $query->get( 's' );

    if ( ! $search ) {
        return;
    }

    $normalize = function ( $value ) {
        $value = strtolower( $value );
        $value = str_replace( array( '+', '&' ), array( 'plus', 'and' ), $value );
        return preg_replace( '/[^a-z0-9]+/', '', $value );
    };

    $normalized_search = $normalize( $search );
    $brands = get_terms(
        array(
            'taxonomy'   => 'product_brand',
            'hide_empty' => true,
        )
    );

    if ( is_wp_error( $brands ) || empty( $brands ) ) {
        return;
    }

    foreach ( $brands as $brand ) {
        $normalized_brand = $normalize( $brand->name );

        // Avoid matching very short or generic brand names.
        if ( strlen( $normalized_brand ) < 5 ) {
            continue;
        }

        if ( false !== strpos( $normalized_search, $normalized_brand ) ) {
            $tax_query   = (array) $query->get( 'tax_query' );
            $tax_query[] = array(
                'taxonomy' => 'product_brand',
                'field'    => 'term_id',
                'terms'    => (int) $brand->term_id,
            );

            $query->set( 'tax_query', $tax_query );
            break;
        }
    }
}
add_action( 'pre_get_posts', 'portfolio_filter_search_by_brand' );

/**
 * Optional FiboSearch integration: include brand names in indexed product data.
 */
function portfolio_fibosearch_add_brand_data( $data, $product_id, $product ) {
    $terms = get_the_terms( $product_id, 'product_brand' );

    if ( $terms && ! is_wp_error( $terms ) ) {
        $data['meta']['brands'] = strtolower( implode( '|', wp_list_pluck( $terms, 'name' ) ) );
    }

    return $data;
}
add_filter( 'dgwt/wcas/tnt/indexer/readable/product/data', 'portfolio_fibosearch_add_brand_data', 10, 3 );
