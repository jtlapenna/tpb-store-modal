<?php
/**
 * Menu Boards Modal Template (v2.4)
 * Stepper-less; uses the shared base scaffold.
 */

if (!defined('ABSPATH')) exit;

$modal_type  = 'menu-board'; // matches handler + CSS variant hook
$title       = 'Menu Boards';
$description = 'Beautiful digital menu boards connected to your live inventory.';
$stepper     = false;        // no stepper for menu boards
$show_price  = false;        // set true if you want the base-price block visible

// Optional CTA/footer content (leave empty to use base default)
// $extra_footer_html = '<a class="button tpb-consultation-btn" href="/contact">Request a Menu Boards consult</a>';

// Optional fine print example (only if your base supports it)
// $fine_print = 'Installation & mounting hardware sold separately.';

include plugin_dir_path(__FILE__) . 'parts/modal-base.php';
?>
