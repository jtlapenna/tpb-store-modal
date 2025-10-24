<?php
/**
 * TPB QuickView - REST API endpoints for native configurator
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'tpb/v1', '/qv/products', [
        'methods'             => 'POST',
        'permission_callback' => '__return_true',
        'args'                => [
        'tags'              => [ 'type' => 'array', 'required' => false ],
        'categories'        => [ 'type' => 'array', 'required' => false ],
        'exclude_categories' => [ 'type' => 'array', 'required' => false ],
        'exclude_tags'      => [ 'type' => 'array', 'required' => false ],
        'limit'             => [ 'type' => 'integer', 'required' => false, 'default' => 24 ],
        'sort'              => [ 'type' => 'string', 'required' => false, 'default' => 'price_asc' ],
        ],
        'callback'            => function ( WP_REST_Request $req ) {
            $tags              = array_values( array_filter( (array) $req->get_param( 'tags' ) ) );
            $categories        = array_values( array_filter( (array) $req->get_param( 'categories' ) ) );
            $exclude_categories = array_values( array_filter( (array) $req->get_param( 'exclude_categories' ) ) );
            $exclude_tags      = array_values( array_filter( (array) $req->get_param( 'exclude_tags' ) ) );
            $limit             = max( 1, min( 48, intval( $req->get_param( 'limit' ) ) ) );
            $sort              = $req->get_param( 'sort' );

            $cache_key = 'tpb_qv_products_' . md5( wp_json_encode( [ $tags, $categories, $exclude_categories, $exclude_tags, $limit, $sort ] ) );
            $cached    = get_transient( $cache_key );
            if ( $cached ) {
                return rest_ensure_response( $cached );
            }

            $tax_query = [ 'relation' => 'AND' ];
            if ( ! empty( $tags ) ) {
                $tax_query[] = [
                    'taxonomy' => 'product_tag',
                    'field'    => 'slug',
                    'terms'    => array_map( 'sanitize_title', $tags ),
                    'operator' => 'AND',
                ];
            }
            if ( ! empty( $categories ) ) {
                $tax_query[] = [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => array_map( 'sanitize_title', $categories ),
                    'operator' => 'AND',
                ];
            }
            if ( ! empty( $exclude_categories ) ) {
                $tax_query[] = [
                    'taxonomy' => 'product_cat',
                    'field'    => 'slug',
                    'terms'    => array_map( 'sanitize_title', $exclude_categories ),
                    'operator' => 'NOT IN',
                ];
            }
            if ( ! empty( $exclude_tags ) ) {
                $tax_query[] = [
                    'taxonomy' => 'product_tag',
                    'field'    => 'slug',
                    'terms'    => array_map( 'sanitize_title', $exclude_tags ),
                    'operator' => 'NOT IN',
                ];
            }

            $orderby = 'meta_value_num';
            $order   = 'ASC';
            if ( $sort === 'price_desc' ) {
                $order = 'DESC';
            }

            $args = [
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'tax_query'      => $tax_query,
                'meta_query'     => [
                    [
                        'key'     => '_price',
                        'value'   => 0,
                        'compare' => '>',
                    ],
                ],
                'orderby'        => $orderby,
                'order'          => $order,
                'meta_key'       => '_price',
            ];

            $query = new WP_Query( $args );
            $products = [];

            if ( $query->have_posts() ) {
                while ( $query->have_posts() ) {
                    $query->the_post();
                    $product = wc_get_product( get_the_ID() );
                    if ( $product ) {
                        $products[] = [
                            'id'         => $product->get_id(),
                            'name'       => $product->get_name(),
                            'price_html' => $product->get_price_html(),
                            'image_url'  => wp_get_attachment_image_url( $product->get_image_id(), 'full' ),
                            'permalink'  => get_permalink(),
                            'tags'       => wp_get_post_terms( $product->get_id(), 'product_tag', [ 'fields' => 'all' ] ),
                        ];
                    }
                }
                wp_reset_postdata();
            }

            set_transient( $cache_key, $products, HOUR_IN_SECONDS );
            return rest_ensure_response( $products );
        },
    ] );
} );

// AJAX fallback for admin-ajax.php
add_action( 'wp_ajax_tpb_qv_products', function() {
    $tags = isset($_GET['tags']) ? explode(',', sanitize_text_field($_GET['tags'])) : [];
    $categories = isset($_GET['categories']) ? explode(',', sanitize_text_field($_GET['categories'])) : [];
    $exclude_categories = isset($_GET['exclude_categories']) ? explode(',', sanitize_text_field($_GET['exclude_categories'])) : [];
    $exclude_tags = isset($_GET['exclude_tags']) ? explode(',', sanitize_text_field($_GET['exclude_tags'])) : [];
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 24;
    $sort = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'price_asc';

    $tax_query = [ 'relation' => 'AND' ];
    if ( ! empty( $tags ) ) {
        $tax_query[] = [
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $tags ),
            'operator' => 'AND',
        ];
    }
    if ( ! empty( $categories ) ) {
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $categories ),
            'operator' => 'AND',
        ];
    }
    if ( ! empty( $exclude_categories ) ) {
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $exclude_categories ),
            'operator' => 'NOT IN',
        ];
    }
    if ( ! empty( $exclude_tags ) ) {
        $tax_query[] = [
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $exclude_tags ),
            'operator' => 'NOT IN',
        ];
    }

    $orderby = 'meta_value_num';
    $order   = 'ASC';
    if ( $sort === 'price_desc' ) {
        $order = 'DESC';
    }

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'tax_query'      => $tax_query,
        'meta_query'     => [
            [
                'key'     => '_price',
                'value'   => 0,
                'compare' => '>',
            ],
        ],
        'orderby'        => $orderby,
        'order'          => $order,
        'meta_key'       => '_price',
    ];

    $query = new WP_Query( $args );
    $products = [];

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $product = wc_get_product( get_the_ID() );
            if ( $product ) {
                $products[] = [
                    'id'         => $product->get_id(),
                    'name'       => $product->get_name(),
                    'price_html' => $product->get_price_html(),
                    'image_url'  => wp_get_attachment_image_url( $product->get_image_id(), 'medium' ),
                    'permalink'  => get_permalink(),
                    'tags'       => wp_get_post_terms( $product->get_id(), 'product_tag', [ 'fields' => 'all' ] ),
                ];
            }
        }
        wp_reset_postdata();
    }

    wp_send_json_success($products);
});
add_action( 'wp_ajax_nopriv_tpb_qv_products', function() {
    $tags = isset($_GET['tags']) ? explode(',', sanitize_text_field($_GET['tags'])) : [];
    $categories = isset($_GET['categories']) ? explode(',', sanitize_text_field($_GET['categories'])) : [];
    $exclude_categories = isset($_GET['exclude_categories']) ? explode(',', sanitize_text_field($_GET['exclude_categories'])) : [];
    $exclude_tags = isset($_GET['exclude_tags']) ? explode(',', sanitize_text_field($_GET['exclude_tags'])) : [];
    $limit = isset($_GET['limit']) ? intval($_GET['limit']) : 24;
    $sort = isset($_GET['sort']) ? sanitize_text_field($_GET['sort']) : 'price_asc';

    $tax_query = [ 'relation' => 'AND' ];
    if ( ! empty( $tags ) ) {
        $tax_query[] = [
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $tags ),
            'operator' => 'AND',
        ];
    }
    if ( ! empty( $categories ) ) {
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $categories ),
            'operator' => 'AND',
        ];
    }
    if ( ! empty( $exclude_categories ) ) {
        $tax_query[] = [
            'taxonomy' => 'product_cat',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $exclude_categories ),
            'operator' => 'NOT IN',
        ];
    }
    if ( ! empty( $exclude_tags ) ) {
        $tax_query[] = [
            'taxonomy' => 'product_tag',
            'field'    => 'slug',
            'terms'    => array_map( 'sanitize_title', $exclude_tags ),
            'operator' => 'NOT IN',
        ];
    }

    $orderby = 'meta_value_num';
    $order   = 'ASC';
    if ( $sort === 'price_desc' ) {
        $order = 'DESC';
    }

    $args = [
        'post_type'      => 'product',
        'post_status'    => 'publish',
        'posts_per_page' => $limit,
        'tax_query'      => $tax_query,
        'meta_query'     => [
            [
                'key'     => '_price',
                'value'   => 0,
                'compare' => '>',
            ],
        ],
        'orderby'        => $orderby,
        'order'          => $order,
        'meta_key'       => '_price',
    ];

    $query = new WP_Query( $args );
    $products = [];

    if ( $query->have_posts() ) {
        while ( $query->have_posts() ) {
            $query->the_post();
            $product = wc_get_product( get_the_ID() );
            if ( $product ) {
                $products[] = [
                    'id'         => $product->get_id(),
                    'name'       => $product->get_name(),
                    'price_html' => $product->get_price_html(),
                    'image_url'  => wp_get_attachment_image_url( $product->get_image_id(), 'medium' ),
                    'permalink'  => get_permalink(),
                    'tags'       => wp_get_post_terms( $product->get_id(), 'product_tag', [ 'fields' => 'all' ] ),
                ];
            }
        }
        wp_reset_postdata();
    }
    
    wp_send_json_success($products);
});