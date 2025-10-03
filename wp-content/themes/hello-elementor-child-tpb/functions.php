<?php
// Purpose: Clean production hooks for TPB QuickView modal
if (!defined('ABSPATH')) { exit; }

// Helper: detect iframe mode via query param
function tpb_qv_is_iframe() {
    // Treat any of these params as iframe mode
    $p = $_GET;
    if ((isset($p['tpb_qv_iframe']) && $p['tpb_qv_iframe'] === '1')
        || (isset($p['tpb_qv']) && $p['tpb_qv'] === '1')
        || (isset($p['tpb_qv_staging']) && $p['tpb_qv_staging'] === '1')) {
        return true;
    }
    return false;
}

// Note: Modal assets are enqueued by the custom plugin (tpb-quickview-modal).
// The child theme intentionally does not enqueue modal CSS/JS to avoid conflicts.

// Add body class for iframe mode
function tpb_qv_body_class($classes) {
    if (tpb_qv_is_iframe()) { $classes[] = 'tpb-qv-iframe'; }
    return $classes;
}
add_filter('body_class', 'tpb_qv_body_class');

// Minimal chrome adjustments in iframe mode
function tpb_qv_iframe_chrome() {
    if (!tpb_qv_is_iframe()) { return; }
    echo '<style>
        header,footer,.site-header,.site-footer{display:none!important;}
        body{padding:0!important;margin:0!important;background:#fff!important;}
    </style>';
}
add_action('wp_head', 'tpb_qv_iframe_chrome', 99);
?>