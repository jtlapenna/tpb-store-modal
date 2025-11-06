<?php
if (!defined('ABSPATH')) exit;
/**
 * TPB Modal Base (v2.4)
 * Shared scaffold for all modals.
 *
 * Expected vars (set by including template):
 *   - $modal_type   (string)  e.g. 'quickcheckout-station', 'flower-station', 'menu-board', 'branded-station'
 *   - $title        (string)
 *   - $description  (string, optional)
 *   - $stepper      (bool, optional) default: true (add .tpb-qv-stepper-container)
 *   - $show_price   (bool, optional) default: true (render #tpb-qv-base-price)
 *   - $extra_footer_html (string, optional) safe HTML appended to footer
 */

// Defaults
$modal_type  = isset($modal_type) ? (string) $modal_type : 'station';
$title       = isset($title) ? (string) $title : 'Configure';
$description = isset($description) ? (string) $description : '';
$stepper     = array_key_exists('stepper', get_defined_vars()) ? (bool) $stepper : true;
$show_price  = array_key_exists('show_price', get_defined_vars()) ? (bool) $show_price : true;

$title_id = 'tpb-qv-title-' . sanitize_title_with_dashes($modal_type);
$desc_id  = 'tpb-qv-desc-'  . sanitize_title_with_dashes($modal_type);

// Initial copy used by JS to reset after close
$initial_price_html = 'Please choose a SKU-count to see the base-price for electronics hardware.<br>Furniture-inclusive pricing will appear below according to your selections.';
?>
<div class="tpb-qv-content tpb-qv-two-col" aria-labelledby="<?php echo esc_attr($title_id); ?>" <?php echo $description ? 'aria-describedby="' . esc_attr($desc_id) . '"' : ''; ?>>

  <!-- LEFT: hero image -->
  <aside class="tpb-qv-left-panel" aria-label="Product image">
    <div id="tpb-qv-product-image" class="tpb-qv-product-image">
      <!-- JS will render PRELOAD → AJAX → FALLBACK here -->
    </div>
  </aside>

  <!-- RIGHT: copy (+ optional price + stepper) -->
  <section class="tpb-qv-right-panel" aria-label="Configuration">
    <header class="tpb-qv-header">
      <h2 id="<?php echo esc_attr($title_id); ?>" class="tpb-qv-title">
        <?php echo esc_html($title); ?>
      </h2>

      <?php if ($description) : ?>
        <p id="<?php echo esc_attr($desc_id); ?>" class="tpb-qv-description">
          <?php echo esc_html($description); ?>
        </p>
      <?php endif; ?>

      <?php if ($show_price) : ?>
        <div id="tpb-qv-base-price"
             class="tpb-qv-base-price"
             aria-live="polite"
             data-initial-html="<?php echo esc_attr($initial_price_html); ?>">
          <?php echo wp_kses_post($initial_price_html); ?>
        </div>
      <?php endif; ?>
    </header>

    <?php if ($stepper) : ?>
      <!-- Stepper mount host (JS injects #tpb-qv-native) -->
      <div class="tpb-qv-stepper-container"></div>
    <?php endif; ?>

    <footer class="tpb-qv-footer">
      <?php
      if (!empty($extra_footer_html)) {
        echo wp_kses_post($extra_footer_html);
      } else {
        // sensible default fine print; override via $extra_footer_html if needed
        ?>
        <small class="tpb-qv-fineprint">
          Pricing shown may exclude furniture, mounts, and installation unless otherwise noted.
        </small>
        <?php
      }
      ?>
    </footer>
  </section>

</div>
