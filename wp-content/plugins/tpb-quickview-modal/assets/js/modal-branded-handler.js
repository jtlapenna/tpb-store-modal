/**
 * TPB Branded Stations Modal Handler
 * Individual handler for Branded Stations modals only (simplified - no stepper)
 */

console.log('🏷️ TPB Branded Stations Modal Handler LOADED - Version:', Date.now());

(function($) {
    'use strict';
    
    // Branded Stations Modal Configuration
    const config = {
        modalType: 'branded-stations',
        overlaySelector: '#tpb-qv-branded-modal',  // Individual modal ID
        closeSelector: '.tpb-qv-close',
        triggerSelector: '[data-tpb-modal="true"], [data-tpb-quickview], a[href*="branded-stations-configure-now"], a[href*="/configure-branded"]',
        debug: window.TPB_QV_CONFIG?.debug || false,
        cpbEnabled: window.TPB_QV_CONFIG?.cpb_enabled || false,
        ajaxUrl: window.TPB_QV_CONFIG?.ajax_url || '',
        nonce: window.TPB_QV_CONFIG?.nonce || ''
    };
    
    // Branded Stations Modal Object
    const BrandedModal = {
        
        // Initialize modal
        init: function() {
            console.log('🏷️ Initializing Branded Stations Modal Handler...');
            this.bindEvents();
        },
        
        // Bind events
        bindEvents: function() {
            const self = this;
            
            // Trigger clicks for branded stations only
            $(document).on('click', config.triggerSelector, function(e) {
                const url = $(this).attr('href') || $(this).data('url') || window.location.href;
                
                // Only handle branded stations URLs
                if (url.includes('branded-stations-configure-now') || url.includes('branded-stations') || url.includes('/configure-branded')) {
                    e.preventDefault();
                    console.log('🏷️ Branded Stations trigger clicked, URL:', url);
                    self.openModal(url);
                }
            });
            
            // Close button
            $(document).on('click', config.closeSelector, function(e) {
                e.preventDefault();
                self.closeModal();
            });
            
            // Overlay click to close
            $(document).on('click', config.overlaySelector, function(e) {
                if (e.target === this) {
                    self.closeModal();
                }
            });
            
            // ESC key to close
            $(document).on('keydown', function(e) {
                if (e.keyCode === 27) { // ESC
                    self.closeModal();
                }
            });
            
            // Consultation button click
            $(document).on('click', '.tpb-consultation-btn', function(e) {
                e.preventDefault();
                console.log('🏷️ Consultation button clicked');
                // Add consultation logic here
                alert('Thank you for your interest! We will contact you soon about Branded Stations consultation.');
            });
        },
        
        // Open modal
        openModal: function(url) {
            console.log('🏷️ Opening Branded Stations modal for URL:', url);
            
            const $overlay = $(config.overlaySelector);
            const $leftPanel = $overlay.find('.tpb-qv-left-panel');
            
            console.log('🏷️ Modal elements found:', {
                overlay: $overlay.length,
                leftPanel: $leftPanel.length
            });
            
            if ($overlay.length === 0) {
                console.error('❌ Branded Stations modal overlay not found');
                return;
            }
            
            // Extract product image
            this.extractProductImage(url, $leftPanel);
            
            // Update modal attributes
            $overlay.attr('data-modal-type', config.modalType);
            
            // Show modal
            console.log('🏷️ Showing Branded Stations modal');
            $overlay.css('display', 'flex');
            setTimeout(() => {
                $overlay.addClass('is-open');
            }, 10);
            $('body').addClass('tpb-modal-open');
            
            // Focus management
            $overlay.find(config.closeSelector).focus();
        },
        
        // Extract product image
        extractProductImage: function(url, $leftPanel) {
            console.log('🏷️ Extracting Branded Stations product image from URL:', url);
            
            const $img = $leftPanel.find('#tpb-qv-product-image');
            if ($img.length) {
                // Use correct Branded Stations image URL
                const imageUrl = 'http://the-peak-beyond-modal.local/wp-content/uploads/2025/03/24-product-category-hk.jpg';
                
                $img.html(`<img src="${imageUrl}" alt="Branded Stations Product" style="width: 100%; height: auto; border-radius: 8px; max-width: 100%;">`);
                console.log('✅ Branded Stations product image loaded:', imageUrl);
            }
        },
        
        // Close modal
        closeModal: function() {
            console.log('🏷️ Closing Branded Stations modal');
            
            const $overlay = $(config.overlaySelector);
            
            // Hide modal
            $overlay.removeClass('is-open');
            setTimeout(() => {
                $overlay.css('display', 'none');
            }, 300);
            $('body').removeClass('tpb-modal-open');
        }
    };
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        console.log('🏷️ DOM ready, initializing Branded Stations modal...');
        
        // Test if modal HTML exists
        const $overlay = $(config.overlaySelector);
        console.log('🏷️ Modal overlay found:', $overlay.length > 0);
        
        if ($overlay.length === 0) {
            console.error('❌ CRITICAL: Branded Stations modal overlay not found in DOM!');
        }
        
        BrandedModal.init();
    });
    
    // Expose BrandedModal globally for debugging
    window.TPBBrandedModal = BrandedModal;
    
})(jQuery);
