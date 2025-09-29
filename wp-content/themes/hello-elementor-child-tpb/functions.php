<?php
// Purpose: Clean production hooks for TPB QuickView modal
if (!defined('ABSPATH')) { exit; }

// Helper: detect iframe mode via query param
function tpb_qv_is_iframe() {
    return isset($_GET['tpb_qv_iframe']) && $_GET['tpb_qv_iframe'] === '1';
}

// Enqueue modal assets with cache-busting
function tpb_qv_enqueue_assets() {
    $theme_uri = get_stylesheet_directory_uri();
    $theme_dir = get_stylesheet_directory();

    $css_files = [
        'assets/css/tpb-qv.css',
        'assets/css/tpb-quickview.css',
    ];
    foreach ($css_files as $rel) {
        $path = $theme_dir . '/' . $rel;
        if (file_exists($path)) {
            wp_enqueue_style(
                'tpb-' . md5($rel),
                $theme_uri . '/' . $rel,
                [],
                filemtime($path)
            );
        }
    }

    $js_files = [
        'assets/js/tpb-modal.js',
        'assets/js/tpb-qv-iframe.js',
    ];
    foreach ($js_files as $rel) {
        $path = $theme_dir . '/' . $rel;
        if (file_exists($path)) {
            wp_enqueue_script(
                'tpb-' . md5($rel),
                $theme_uri . '/' . $rel,
                ['jquery'],
                filemtime($path),
                true
            );
        }
    }
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_assets', 20);

// Add body class for iframe mode
function tpb_qv_body_class($classes) {
    if (tpb_qv_is_iframe()) { $classes[] = 'tpb-qv-iframe'; }
    return $classes;
}
add_filter('body_class', 'tpb_qv_body_class');

// Minimal chrome adjustments in iframe mode
function tpb_qv_iframe_chrome() {
    if (!tpb_qv_is_iframe()) { return; }
    echo '<style>header,footer,.site-header,.site-footer{display:none!important;}body{padding:0!important;margin:0!important;}</style>';
}
add_action('wp_head', 'tpb_qv_iframe_chrome', 99);
?>