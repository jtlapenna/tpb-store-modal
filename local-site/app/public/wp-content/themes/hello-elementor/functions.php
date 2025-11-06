<?php
/**
 * Hello Elementor functions and definitions
 *
 * @package HelloElementor
 */

if ( ! defined( 'ABSPATH' ) ) {
    exit; // Exit if accessed directly.
}

define( 'HELLO_ELEMENTOR_VERSION', '2.8.1' );

if ( ! isset( $content_width ) ) {
    $content_width = 800; // Pixels.
}

function hello_elementor_setup() {
    add_theme_support( 'automatic-feed-links' );
    add_theme_support( 'post-thumbnails' );
    add_theme_support( 'title-tag' );
    add_theme_support(
        'html5',
        array(
            'search-form',
            'comment-form',
            'comment-list',
            'gallery',
            'caption',
            'script',
            'style',
        )
    );
    add_theme_support( 'woocommerce' );
    add_theme_support( 'customize-selective-refresh-widgets' );
    add_theme_support( 'wp-block-styles' );
    add_theme_support( 'align-wide' );
    add_theme_support( 'responsive-embeds' );
}
add_action( 'after_setup_theme', 'hello_elementor_setup' );

function hello_elementor_scripts_styles() {
    $min_suffix = defined( 'SCRIPT_DEBUG' ) && SCRIPT_DEBUG ? '' : '.min';

    wp_enqueue_style(
        'hello-elementor',
        get_template_directory_uri() . '/style' . $min_suffix . '.css',
        [],
        HELLO_ELEMENTOR_VERSION
    );

    if ( is_rtl() ) {
        wp_enqueue_style(
            'hello-elementor-rtl',
            get_template_directory_uri() . '/rtl' . $min_suffix . '.css',
            [],
            HELLO_ELEMENTOR_VERSION
        );
    }
}
add_action( 'wp_enqueue_scripts', 'hello_elementor_scripts_styles' );

// REMOVED THE PROBLEMATIC FUNCTION THAT CAUSED INFINITE RECURSION
// function hello_elementor_register_elementor_locations() {
//     if ( ! function_exists( 'elementor_theme_do_location' ) ) {
//         return;
//     }
//     elementor_theme_do_location( 'header' );
//     elementor_theme_do_location( 'footer' );
// }
// add_action( 'elementor/theme/before_do_header', 'hello_elementor_register_elementor_locations' );
// add_action( 'elementor/theme/before_do_footer', 'hello_elementor_register_elementor_locations' );
