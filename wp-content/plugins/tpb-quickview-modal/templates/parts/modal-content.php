<?php
/**
 * Shared Modal Content Partial
 * Used by all modal templates for consistent structure
 */
?>
<div class="tpb-qv-left-panel">
    <div id="tpb-qv-product-image">
        <!-- Product image will be loaded here -->
    </div>
</div>
<div class="tpb-qv-right-panel">
    <?php if (!empty($is_simplified)): ?>
        <!-- Simplified modal: centered title only -->
            <h2 class="tpb-qv-title tpb-qv-title-simplified" 
                data-cache-bust="<?php echo time() . '.' . rand(1000, 9999); ?>" 
                style="padding: 50px 30px; text-align: center; font-size: 48px; font-weight: 600; color: #2c3e50;">
                <?php echo esc_html($modal_title); ?>
            </h2>
    <?php else: ?>
        <!-- Full modal: title, description, iframe -->
            <h2 class="tpb-qv-title" 
                data-cache-bust="<?php echo time() . '.' . rand(1000, 9999); ?>" 
                style="padding: 50px 30px 10px 55px;">
                <?php echo esc_html($modal_title); ?>
            </h2>
        
        <?php if ($show_base_price && !empty($modal_description)): ?>
            <div id="tpb-qv-base-price" 
                 class="tpb-qv-base-price" 
                 data-cache-bust="<?php echo time() . '.' . rand(1000, 9999); ?>" 
                 style="margin-bottom: 20px !important;">
                <?php echo $modal_description; ?>
            </div>
        <?php endif; ?>
        
        <?php if ($show_iframe): ?>
            <iframe id="tpb-qv-iframe" class="tpb-qv-iframe" src="about:blank"></iframe>
        <?php endif; ?>
    <?php endif; ?>
</div>
