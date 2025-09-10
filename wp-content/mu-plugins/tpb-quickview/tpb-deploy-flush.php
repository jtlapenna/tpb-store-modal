<?php
/**
 * Simple deploy cache flush endpoint: /?tpb_deploy_flush=YOUR_TOKEN
 */
add_action('init', function () {
    if (empty($_GET['tpb_deploy_flush'])) return;
    $token = $_GET['tpb_deploy_flush'];
    $expected = getenv('TPB_DEPLOY_TOKEN');
    if (!$expected && defined('TPB_DEPLOY_TOKEN')) $expected = TPB_DEPLOY_TOKEN;

    if (!$expected || hash_equals((string)$expected, (string)$token) === false) {
        status_header(403);
        exit('Forbidden');
    }

    // Elementor cache
    if (defined('ELEMENTOR_VERSION') && class_exists('Elementor\\Plugin')) {
        try { \Elementor\Plugin::$instance->files_manager->clear_cache(); } catch (\Throwable $e) {}
    }
    // Autoptimize cache
    if (class_exists('autoptimizeCache')) {
        try { \autoptimizeCache::clearall(); } catch (\Throwable $e) {}
    }

    header('Content-Type: text/plain');
    echo 'OK';
    exit;
});


