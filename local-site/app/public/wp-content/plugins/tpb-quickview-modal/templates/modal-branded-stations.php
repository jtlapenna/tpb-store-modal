<?php
/**
 * Branded Stations Modal Template (v2.4)
 * Stepper-less; uses shared base scaffold.
 */

if (!defined('ABSPATH')) exit;

$modal_type  = 'branded-station';
$title       = 'Branded Stations';
$description = 'Showcase a single brand with a focused, high-impact experience. Great for shop-in-shop, pop-ups, and campaigns.';
$stepper     = false; // no stepper for branded
$show_price  = false; // hide base price block by default

// Optional extra footer content (or omit to use base fine print)
// $extra_footer_html = '<a class="button tpb-consultation-btn" href="/contact">Request a branded station consult</a>';

include plugin_dir_path(__FILE__) . 'parts/modal-base.php';
?>