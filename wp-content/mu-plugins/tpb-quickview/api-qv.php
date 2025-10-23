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
        'limit'             => [ 'type' => 'integer', 'required' => false, 'default' => 24 ],
        'sort'              => [ 'type' => 'string', 'required' => false, 'default' => 'price_asc' ],
        ],
        'callback'            => function ( WP_REST_Request $req ) {
            $tags              = array_values( array_filter( (array) $req->get_param( 'tags' ) ) );
            $categories        = array_values( array_filter( (array) $req->get_param( 'categories' ) ) );
            $exclude_categories = array_values( array_filter( (array) $req->get_param( 'exclude_categories' ) ) );
            $limit             = max( 1, min( 48, intval( $req->get_param( 'limit' ) ) ) );
            $sort              = $req->get_param( 'sort' );

            $cache_key = 'tpb_qv_products_' . md5( wp_json_encode( [ $tags, $categories, $exclude_categories, $limit, $sort ] ) );
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

            $args = [
                'post_type'      => 'product',
                'post_status'    => 'publish',
                'posts_per_page' => $limit,
                'tax_query'      => $tax_query,
            ];

            // Sorting
            if ( $sort === 'price_asc' ) {
                $args['meta_key'] = '_price';
                $args['orderby']  = 'meta_value_num';
                $args['order']    = 'ASC';
            }

            $q = new WP_Query( $args );
            $items = [];

            if ( $q->have_posts() ) {
                foreach ( $q->posts as $post ) {
                    $product = wc_get_product( $post );
                    if ( ! $product ) { continue; }

                    $image_id  = $product->get_image_id();
                    $image_url = $image_id ? wp_get_attachment_image_url( $image_id, 'large' ) : wc_placeholder_img_src( 'large' );

                    // Build add to cart URL (AJAX can use product id + quantity)
                    $items[] = [
                        'id'              => $product->get_id(),
                        'name'            => html_entity_decode( $product->get_name() ),
                        'price_html'      => $product->get_price_html(),
                        'image_url'       => $image_url,
                        'permalink'       => get_permalink( $product->get_id() ),
                        'is_purchasable'  => $product->is_purchasable(),
                        'supports_qty'    => $product->is_sold_individually() ? false : true,
                    ];
                }
            }

            wp_reset_postdata();

            $response = [ 'results' => $items ];
            set_transient( $cache_key, $response, 10 * MINUTE_IN_SECONDS );
            return rest_ensure_response( $response );
        },
    ] );
} );


