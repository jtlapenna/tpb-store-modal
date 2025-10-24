/**
 * TPB Quick Checkout Station Modal Handler
 * Individual handler for Quick Checkout Station modals only
 */

console.log('⚡ TPB Quick Checkout Station Modal Handler LOADED - Version:', Date.now());

(function($) {
    'use strict';
    
    // Quick Checkout Station Modal Configuration
    const config = {
        modalType: 'quick-checkout',
        overlaySelector: '#tpb-qv-quickcheckout-modal',  // Individual modal ID
        stepperContainerSelector: '#tpb-qv-stepper-container',
        closeSelector: '.tpb-qv-close',
        triggerSelector: '[data-tpb-modal="true"], [data-tpb-quickview], a[href*="quickcheckout-station-configure-now"], a[href*="quick-checkout-station-configure-now"]',
        debug: window.TPB_QV_CONFIG?.debug || false,
        cpbEnabled: window.TPB_QV_CONFIG?.cpb_enabled || false,
        ajaxUrl: window.TPB_QV_CONFIG?.ajax_url || '',
        nonce: window.TPB_QV_CONFIG?.nonce || ''
    };
    
    // Quick Checkout Station Modal Object
    const QuickCheckoutModal = {
        
        // Initialize modal
        init: function() {
            console.log('⚡ Initializing Quick Checkout Station Modal Handler...');
            this.bindEvents();
            this.setupEventListeners();
        },
        
        // Bind events
        bindEvents: function() {
            const self = this;
            
            // Trigger clicks for quick checkout station only
            $(document).on('click', config.triggerSelector, function(e) {
                const url = $(this).attr('href') || $(this).data('url') || window.location.href;
                
                // Only handle quick checkout station URLs
                if (url.includes('quickcheckout-station-configure-now') || url.includes('quickcheckout-station') || url.includes('quick-checkout-station-configure-now') || url.includes('quick-checkout-station')) {
                    e.preventDefault();
                    console.log('⚡ Quick Checkout Station trigger clicked, URL:', url);
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
        },
        
        // Setup event listeners
        setupEventListeners: function() {
            // Listen for price updates from steppers
            window.addEventListener('message', function(e) {
                if (e.data && e.data.type === 'TPB_QV' && e.data.action === 'update_base_price') {
                    var priceEl = document.getElementById('tpb-qv-base-price');
                    if (priceEl && e.data.price) {
                        priceEl.innerHTML = e.data.price;
                        priceEl.classList.add('updating');
                        setTimeout(function() {
                            priceEl.classList.remove('updating');
                        }, 300);
                    }
                }
            });
        },
        
        // Open modal
        openModal: function(url) {
            console.log('⚡ Opening Quick Checkout Station modal for URL:', url);
            
            const $overlay = $(config.overlaySelector);
            const $stepperContainer = $(config.stepperContainerSelector);
            const $leftPanel = $overlay.find('.tpb-qv-left-panel');
            
            console.log('⚡ Modal elements found:', {
                overlay: $overlay.length,
                stepperContainer: $stepperContainer.length,
                leftPanel: $leftPanel.length
            });
            
            if ($overlay.length === 0) {
                console.error('❌ Quick Checkout Station modal overlay not found');
                return;
            }
            
            // Extract product image
            this.extractProductImage(url, $leftPanel);
            
            // Update modal attributes
            $overlay.attr('data-modal-type', config.modalType);
            
            // Load stepper content directly
            this.loadStepper($stepperContainer);
            
            // Show modal
            console.log('⚡ Showing Quick Checkout Station modal');
            $overlay.css('display', 'flex');
            setTimeout(() => {
                $overlay.addClass('is-open');
            }, 10);
            $('body').addClass('tpb-modal-open');
            
            // Focus management
            $overlay.find(config.closeSelector).focus();
        },
        
        // Load Quick Checkout Station stepper
        loadStepper: function($container) {
            console.log('⚡ Loading Quick Checkout Station stepper');
            
            // Clear container
            $container.empty();
            
            // Load the Quick Checkout Station stepper script
            const script = document.createElement('script');
            script.src = window.TPB_QV_CONFIG?.plugin_url + 'assets/js/quickcheckout-stepper-modal.js?v=' + Date.now();
            script.onload = function() {
                console.log('⚡ Quick Checkout Station stepper script loaded');
                
                // Check if the stepper content is being generated
                setTimeout(function() {
                    const stepperContent = $container.find('.tpb-qv-native');
                    console.log('⚡ Stepper content after load:', stepperContent.length, stepperContent.html());
                    if (stepperContent.length === 0) {
                        console.error('❌ No stepper content generated');
                    } else {
                        console.log('✅ Quick Checkout Station modal fully loaded with stepper content');
                    }
                }, 1000);
            };
            script.onerror = function() {
                console.error('❌ Failed to load Quick Checkout Station stepper script');
            };
            document.head.appendChild(script);
        },
        
        // Extract product image
        extractProductImage: function(url, $leftPanel) {
            console.log('⚡ Extracting Quick Checkout Station product image from URL:', url);
            
            const $img = $leftPanel.find('#tpb-qv-product-image');
            console.log('⚡ Image container found:', $img.length);
            
            if ($img.length) {
                console.log('⚡ Fetching Quick Checkout Station product image for ID 4842');
                
                // Fetch image dynamically from Quick Checkout Station product (ID: 4842)
                this.fetchProductImage(4842, $img);
            } else {
                console.error('❌ Quick Checkout Station image container not found');
            }
        },
        
        // Fetch product image from WordPress REST API (same as Flower Station)
        fetchProductImage: function(productId, $imgContainer) {
            console.log('⚡ Fetching product image for ID:', productId);
            
            // Use WordPress REST API (same as Flower Station)
            fetch(`http://the-peak-beyond-modal.local/wp-json/wp/v2/product/${productId}?_=${Date.now()}`)
                .then(response => response.json())
                .then(product => {
                    console.log('⚡ Product data received:', product);
                    
                    if (product.featured_media) {
                        // Get the featured media details
                        return fetch(`http://the-peak-beyond-modal.local/wp-json/wp/v2/media/${product.featured_media}?_=${Date.now()}`);
                    } else {
                        throw new Error('No featured media found for product');
                    }
                })
                .then(response => response.json())
                .then(media => {
                    console.log('⚡ Media data received:', media);
                    
                    if (media.source_url) {
                        const imageUrl = media.source_url;
                        console.log('⚡ Setting Quick Checkout Station image URL:', imageUrl);
                        
                        $imgContainer.html(`<img src="${imageUrl}" alt="Quick Checkout Station" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0; opacity: 0;">`);
                        
                        // Add error handling for image load
                        const $img = $imgContainer.find('img');
                        $img.on('load', function() {
                            console.log('✅ Quick Checkout Station product image loaded successfully');
                            $(this).addClass('loaded');
                        });
                        
                        $img.on('error', function() {
                            console.error('❌ Failed to load Quick Checkout Station product image');
                        });
                    } else {
                        throw new Error('No image URL found in media data');
                    }
                })
                .catch(error => {
                    console.error('❌ Error fetching product image:', error);
                    
                    // Fallback to admin-ajax
                    console.log('⚡ Using admin-ajax fallback for product image');
                    fetch(`/wp-admin/admin-ajax.php?action=tpb_qv_products&product_id=${productId}`)
                        .then(response => response.json())
                        .then(data => {
                            if (data.success && data.data && data.data.image_url) {
                                console.log('⚡ Setting Quick Checkout Station image via AJAX:', data.data.image_url);
                                $imgContainer.html(`<img src="${data.data.image_url}" alt="Quick Checkout Station" style="width: 100%; height: 100%; object-fit: cover; border-radius: 0; opacity: 0;">`);
                                
                                // Add fade-in effect for AJAX fallback
                                const $img = $imgContainer.find('img');
                                $img.on('load', function() {
                                    $(this).addClass('loaded');
                                });
                            } else {
                                console.error('❌ No image data in AJAX response');
                            }
                        })
                        .catch(ajaxError => {
                            console.error('❌ AJAX fallback also failed:', ajaxError);
                        });
                });
        },
        
        // Close modal
        closeModal: function() {
            console.log('⚡ Closing Quick Checkout Station modal');
            
            const $overlay = $(config.overlaySelector);
            const $stepperContainer = $(config.stepperContainerSelector);
            
            // Clear stepper content
            $stepperContainer.empty();
            
            // Hide modal
            $overlay.removeClass('is-open');
            setTimeout(() => {
                $overlay.css('display', 'none');
            }, 300);
            $('body').removeClass('tpb-modal-open');
            
            // Clean up any loaded scripts
            this.cleanupSteppers();
        },
        
        // Cleanup steppers
        cleanupSteppers: function() {
            // Remove stepper scripts that were dynamically loaded
            $('script[src*="quickcheckout-stepper-modal.js"]').remove();
            
            // Clear any stepper containers
            $('.tpb-qv-native').remove();
        }
    };
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        console.log('⚡ DOM ready, initializing Quick Checkout Station modal...');
        
        // Test if modal HTML exists
        const $overlay = $(config.overlaySelector);
        console.log('⚡ Modal overlay found:', $overlay.length > 0);
        
        if ($overlay.length === 0) {
            console.error('❌ CRITICAL: Quick Checkout Station modal overlay not found in DOM!');
        }
        
        QuickCheckoutModal.init();
    });
    
    // Expose QuickCheckoutModal globally for debugging
    window.TPBQuickCheckoutModal = QuickCheckoutModal;
    
})(jQuery);
