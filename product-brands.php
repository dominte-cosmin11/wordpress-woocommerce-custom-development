<?php
/**
 * Portfolio example: custom WooCommerce product brand taxonomy.
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function portfolio_register_product_brand_taxonomy() {
    $labels = array(
        'name'          => __( 'Brands', 'portfolio' ),
        'singular_name' => __( 'Brand', 'portfolio' ),
    );

    register_taxonomy(
        'product_brand',
        array( 'product' ),
        array(
            'labels'       => $labels,
            'public'       => true,
            'hierarchical' => true,
            'show_ui'      => true,
            'show_in_rest' => true,
            'rewrite'      => array(
                'slug'       => 'brand',
                'with_front' => false,
            ),
        )
    );
}
add_action( 'init', 'portfolio_register_product_brand_taxonomy' );

/**
 * Usage: [brand_list]
 * Uses the standard WordPress term thumbnail_id metadata convention.
 */
function portfolio_brand_list_shortcode() {
    $terms = get_terms(
        array(
            'taxonomy'   => 'product_brand',
            'hide_empty' => true,
        )
    );

    if ( is_wp_error( $terms ) || empty( $terms ) ) {
        return '';
    }

    $output = '<div class="brand-grid">';

    foreach ( $terms as $term ) {
        $term_link = get_term_link( $term );

        if ( is_wp_error( $term_link ) ) {
            continue;
        }

        $thumbnail_id = get_term_meta( $term->term_id, 'thumbnail_id', true );
        $image        = $thumbnail_id
            ? wp_get_attachment_image( $thumbnail_id, 'medium' )
            : '';

        $output .= '<a class="brand-card" href="' . esc_url( $term_link ) . '">';
        $output .= '<div class="brand-image">' . $image . '</div>';
        $output .= '<div class="brand-name">' . esc_html( $term->name ) . '</div>';
        $output .= '</a>';
    }

    $output .= '</div>';

    return $output;
}
add_shortcode( 'brand_list', 'portfolio_brand_list_shortcode' );
