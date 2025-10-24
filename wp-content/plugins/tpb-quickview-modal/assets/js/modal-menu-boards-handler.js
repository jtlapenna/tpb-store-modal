/**
 * TPB Menu Boards Modal Handler
 * Individual handler for Menu Boards modals only (simplified - no stepper)
 */

console.log('📋 TPB Menu Boards Modal Handler LOADED - Version:', Date.now());

(function($) {
    'use strict';
    
    // Menu Boards Modal Configuration
    const config = {
        modalType: 'menu-boards',
        overlaySelector: '#tpb-qv-menu-boards-modal',  // Individual modal ID
        closeSelector: '.tpb-qv-close',
        triggerSelector: '[data-tpb-modal="true"], [data-tpb-quickview], a[href*="menu-boards-configure-now"], a[href*="/configure-menu-boards"]',
        debug: window.TPB_QV_CONFIG?.debug || false,
        cpbEnabled: window.TPB_QV_CONFIG?.cpb_enabled || false,
        ajaxUrl: window.TPB_QV_CONFIG?.ajax_url || '',
        nonce: window.TPB_QV_CONFIG?.nonce || ''
    };
    
    // Menu Boards Modal Object
    const MenuBoardsModal = {
        
        // Initialize modal
        init: function() {
            console.log('📋 Initializing Menu Boards Modal Handler...');
            this.bindEvents();
        },
        
        // Bind events
        bindEvents: function() {
            const self = this;
            
            // Trigger clicks for menu boards only
            $(document).on('click', config.triggerSelector, function(e) {
                const url = $(this).attr('href') || $(this).data('url') || window.location.href;
                
                // Only handle menu boards URLs
                if (url.includes('menu-boards-configure-now') || url.includes('menu-boards') || url.includes('/configure-menu-boards')) {
                    e.preventDefault();
                    console.log('📋 Menu Boards trigger clicked, URL:', url);
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
                console.log('📋 Consultation button clicked');
                // Add consultation logic here
                alert('Thank you for your interest! We will contact you soon about Menu Boards consultation.');
            });
        },
        
        // Open modal
        openModal: function(url) {
            console.log('📋 Opening Menu Boards modal for URL:', url);
            
            const $overlay = $(config.overlaySelector);
            const $leftPanel = $overlay.find('.tpb-qv-left-panel');
            
            console.log('📋 Modal elements found:', {
                overlay: $overlay.length,
                leftPanel: $leftPanel.length
            });
            
            if ($overlay.length === 0) {
                console.error('❌ Menu Boards modal overlay not found');
                return;
            }
            
            // Extract product image
            this.extractProductImage(url, $leftPanel);
            
            // Update modal attributes
            $overlay.attr('data-modal-type', config.modalType);
            
            // Show modal
            console.log('📋 Showing Menu Boards modal');
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
            console.log('📋 Extracting Menu Boards product image from URL:', url);
            
            const $img = $leftPanel.find('#tpb-qv-product-image');
            if ($img.length) {
                // Use correct Menu Boards image URL
                const imageUrl = 'http://the-peak-beyond-modal.local/wp-content/uploads/2025/03/24-product-category-hk.jpg';
                
                $img.html(`<img src="${imageUrl}" alt="Menu Boards Product" style="width: 100%; height: auto; border-radius: 8px; max-width: 100%;">`);
                console.log('✅ Menu Boards product image loaded:', imageUrl);
            }
        },
        
        // Close modal
        closeModal: function() {
            console.log('📋 Closing Menu Boards modal');
            
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
        console.log('📋 DOM ready, initializing Menu Boards modal...');
        
        // Test if modal HTML exists
        const $overlay = $(config.overlaySelector);
        console.log('📋 Modal overlay found:', $overlay.length > 0);
        
        if ($overlay.length === 0) {
            console.error('❌ CRITICAL: Menu Boards modal overlay not found in DOM!');
        }
        
        MenuBoardsModal.init();
    });
    
    // Expose MenuBoardsModal globally for debugging
    window.TPBMenuBoardsModal = MenuBoardsModal;
    
})(jQuery);
