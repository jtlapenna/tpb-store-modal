<?php
/**
 * Branded Station Simplified Template
 * Uses the base template with customizations for branded stations
 */

$modal_type = 'branded-station';
$title = 'Branded Stations';
$description = 'Custom branded display stations for your store.';
$custom_classes = ['tpb-qv-title-simplified'];
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
            style="padding: 50px 30px; text-align: center; font-size: 48px; font-weight: 600; color: #2c3e50;">
            <?php echo esc_html($title); ?>
        </h2>
        
        <div style="padding: 20px 30px; text-align: center;">
            <p style="font-size: 18px; color: #666; margin-bottom: 40px;">
                <?php echo esc_html($description); ?>
            </p>
            <button class="tpb-consultation-btn" style="
                background: #007cba;
                color: white;
                border: none;
                padding: 15px 30px;
                font-size: 16px;
                border-radius: 5px;
                cursor: pointer;
            ">Request Consultation</button>
        </div>
    </div>
</div>
