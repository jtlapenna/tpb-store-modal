<?php
/**
 * Flower Station Modal Template
 * Uses the base template with flower station specific configuration
 */

$modal_type = 'flower-station';
$title = 'Configure Your Flower Stations (Hardware Only)';
$description = 'Please choose a SKU-count to see the base-price for electronics hardware. Furniture-inclusive pricing will appear below according to your selections.';

include plugin_dir_path(__FILE__) . 'parts/modal-base.php';
?>