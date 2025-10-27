<?php
/**
 * Modal Base Template
 * Shared HTML structure for all modal types
 * 
 * @param string $modal_type - The type of modal (flower-station, category-station, etc.)
 * @param string $title - The modal title
 * @param string $description - The initial description text
 * @param array $custom_classes - Additional CSS classes for customization
 */

// Set defaults
$modal_type = $modal_type ?? 'default';
$title = $title ?? 'Configure Your Station';
$description = $description ?? 'Please make your selections to continue.';
$custom_classes = $custom_classes ?? [];
?>

<div class="tpb-qv-left-panel">
    <div id="tpb-qv-product-image">
        <!-- Product image will be loaded here dynamically -->
    </div>
</div>

<div class="tpb-qv-right-panel">
    <div class="tpb-qv-header-container">
        <h2 class="tpb-qv-title <?php echo implode(' ', $custom_classes); ?>" 
            data-cache-bust="<?php echo time() . '.' . rand(1000, 9999); ?>" 
            style="padding: 50px 30px 10px 55px;">
            <?php echo esc_html($title); ?>
        </h2>
        
        <div id="tpb-qv-base-price" 
             class="tpb-qv-base-price" 
             data-cache-bust="<?php echo time() . '.' . rand(1000, 9999); ?>" 
             style="margin-bottom: 20px !important;">
            <?php echo esc_html($description); ?>
        </div>
    </div>
    
    <!-- Stepper container for direct DOM integration -->
    <div id="tpb-qv-stepper-container" 
         class="tpb-qv-stepper-container" 
         data-modal-type="<?php echo esc_attr($modal_type); ?>">
        <!-- Stepper content will be loaded here directly -->
    </div>
</div>
