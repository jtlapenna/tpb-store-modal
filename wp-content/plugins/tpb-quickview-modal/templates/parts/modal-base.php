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
    
    <!-- What's Included Information Section -->
    <div class="tpb-components-info" style="background: #f8f9fa; border: 1px solid #e3e6ea; border-radius: 8px; padding: 20px; margin: 0 55px 30px 55px; max-width: 600px;">
        <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #2c3e50; font-weight: 600;">What You're Purchasing</h3>
        
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 15px; margin-bottom: 12px;">
            <div style="display: flex; align-items: start;">
                <span style="color: #4fb089; font-size: 18px; margin-right: 8px;">✓</span>
                <div>
                    <strong style="display: block; font-size: 14px; color: #2c3e50;">Electronics</strong>
                    <span style="font-size: 13px; color: #6c757d;">Touch screens, PCs, readers</span>
                </div>
            </div>
            
            <div style="display: flex; align-items: start;">
                <span style="color: #4fb089; font-size: 18px; margin-right: 8px;">✓</span>
                <div>
                    <strong style="display: block; font-size: 14px; color: #2c3e50;">Software</strong>
                    <span style="font-size: 13px; color: #6c757d;">Custom TPB platform</span>
                </div>
            </div>
        </div>
        
        <div style="display: flex; align-items: start; padding-top: 12px; border-top: 1px solid #dee2e6;">
            <span style="color: #ffc107; font-size: 18px; margin-right: 8px;">ℹ</span>
            <div style="font-size: 13px; color: #6c757d;">
                <strong style="color: #2c3e50;">Fixtures sold separately.</strong> You'll need to purchase furniture, mounting brackets, and enclosures from your preferred retail supplier.
            </div>
        </div>
    </div>
    
    <!-- Stepper container for direct DOM integration -->
    <div id="tpb-qv-stepper-container" 
         class="tpb-qv-stepper-container" 
         data-modal-type="<?php echo esc_attr($modal_type); ?>">
        <!-- Stepper content will be loaded here directly -->
    </div>
</div>
