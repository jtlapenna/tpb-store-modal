/**
 * TPB Flower Station Modal Handler
 * Individual handler for Flower Station modals only
 */

console.log('🌸 TPB Flower Station Modal Handler LOADED - Version:', Date.now());

(function($) {
    'use strict';
    
    // Flower Station Modal Configuration
    const config = {
        modalType: 'flower-station',
        overlaySelector: '#tpb-qv-flower-modal',  // Individual modal ID
        stepperContainerSelector: '#tpb-qv-stepper-container',
        closeSelector: '.tpb-qv-close',
        triggerSelector: '[data-tpb-modal="flower-station"], [data-tpb-quickview="flower-station"], a[href*="flower-station-configure-now"]',
        debug: window.TPB_QV_CONFIG?.debug || false,
        cpbEnabled: window.TPB_QV_CONFIG?.cpb_enabled || false,
        ajaxUrl: window.TPB_QV_CONFIG?.ajax_url || '',
        nonce: window.TPB_QV_CONFIG?.nonce || ''
    };
    
    // Flower Station Modal Object
    const FlowerModal = {
        
        // Initialize modal
        init: function() {
            console.log('🌸 Initializing Flower Station Modal Handler...');
            this.bindEvents();
            this.setupEventListeners();
        },
        
        // Bind events
        bindEvents: function() {
            const self = this;
            
            // Trigger clicks for flower station only
            $(document).on('click', config.triggerSelector, function(e) {
                const url = $(this).attr('href') || $(this).data('url') || window.location.href;
                
                // Only handle flower station URLs
                if (url.includes('flower-station-configure-now') || url.includes('flower-station')) {
                    e.preventDefault();
                    console.log('🌸 Flower Station trigger clicked, URL:', url);
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
            console.log('🌸 Opening Flower Station modal for URL:', url);
            
            const $overlay = $(config.overlaySelector);
            const $stepperContainer = $(config.stepperContainerSelector);
            const $leftPanel = $overlay.find('.tpb-qv-left-panel');
            
            console.log('🌸 Modal elements found:', {
                overlay: $overlay.length,
                stepperContainer: $stepperContainer.length,
                leftPanel: $leftPanel.length
            });
            
            if ($overlay.length === 0) {
                console.error('❌ Flower Station modal overlay not found');
                return;
            }
            
            // Extract product image
            this.extractProductImage(url, $leftPanel);
            
            // Update modal attributes
            $overlay.attr('data-modal-type', config.modalType);
            
            // Load stepper content directly
            this.loadStepper($stepperContainer);
            
            // Show modal
            console.log('🌸 Showing Flower Station modal');
            $overlay.css('display', 'flex');
            setTimeout(() => {
                $overlay.addClass('is-open');
            }, 10);
            $('body').addClass('tpb-modal-open');
            
            // Focus management
            $overlay.find(config.closeSelector).focus();
        },
        
        // Load Flower Station stepper
        loadStepper: function($container) {
            console.log('🌸 Loading Flower Station stepper');
            
            // Clear container
            $container.empty();
            
            // Create stepper container
            const $stepperDiv = $('<div class="tpb-qv-native" id="tpb-qv-native"></div>');
            $container.append($stepperDiv);
            
            
            // Load the Flower Station modal stepper script
            const script = document.createElement('script');
            script.src = '/wp-content/plugins/tpb-quickview-modal/assets/js/flower-stepper-modal.js?v=' + Date.now();
            script.onload = function() {
                console.log('✅ Flower Station modal stepper script loaded');
                // Initialize the stepper in the container
                if (window.TPBFlowerStepper && window.TPBFlowerStepper.build) {
                    window.TPBFlowerStepper.build($stepperDiv[0]);
                }
            };
            script.onerror = function() {
                console.error('❌ Failed to load Flower Station modal stepper script');
            };
            document.head.appendChild(script);
        },
        
        // Extract product image
        extractProductImage: function(url, $leftPanel) {
            console.log('🌸 Extracting Flower Station product image from URL:', url);
            
            const $img = $leftPanel.find('#tpb-qv-product-image');
            if ($img.length) {
                // Dynamically fetch featured image from product 4607
                this.fetchProductImage(4607, $img);
            }
        },
        
        // Fetch product featured image from WordPress API
        fetchProductImage: function(productId, $imgContainer) {
            console.log('🌸 Fetching featured image for product ID:', productId);
            
            // First get the product to find the featured media ID
            fetch(`http://the-peak-beyond-modal.local/wp-json/wp/v2/product/${productId}?_=${Date.now()}`)
                .then(response => response.json())
                .then(product => {
                    if (product.featured_media) {
                        // Get the featured media details
                        return fetch(`http://the-peak-beyond-modal.local/wp-json/wp/v2/media/${product.featured_media}?_=${Date.now()}`);
                    } else {
                        throw new Error('No featured media found for product');
                    }
                })
                .then(response => response.json())
                .then(media => {
                    // Use the full-size image URL with cache busting
                    const baseImageUrl = media.source_url || media.media_details.sizes.full.source_url;
                    const imageUrl = `${baseImageUrl}?v=${Date.now()}`;
                    const altText = media.alt_text || 'Flower Station Product';
                    
                    $imgContainer.html(`<img src="${imageUrl}" alt="${altText}" style="opacity: 0;">`);
                    console.log('✅ Flower Station product image loaded dynamically:', imageUrl);
                    
                    // Add fade-in effect
                    const $img = $imgContainer.find('img');
                    $img.on('load', function() {
                        $(this).addClass('loaded');
                    });
                })
                .catch(error => {
                    console.error('❌ Failed to fetch product image:', error);
                    // Fallback to a default image with cache busting
                    const fallbackUrl = `http://the-peak-beyond-modal.local/wp-content/uploads/2025/09/16-COUNT-JAR_Comp-fs-1-web-scaled.jpg?v=${Date.now()}`;
                    $imgContainer.html(`<img src="${fallbackUrl}" alt="Flower Station Product" style="opacity: 0;">`);
                    console.log('🔄 Using fallback image:', fallbackUrl);
                    
                    // Add fade-in effect for fallback
                    const $img = $imgContainer.find('img');
                    $img.on('load', function() {
                        $(this).addClass('loaded');
                    });
                });
        },
        
        // Close modal
        closeModal: function() {
            console.log('🌸 Closing Flower Station modal');
            
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
            $('script[src*="tpb-qv-native.js"]').remove();
            
            // Clear any stepper containers
            $('.tpb-qv-native').remove();
        }
    };
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        console.log('🌸 DOM ready, initializing Flower Station modal...');
        
        // Test if modal HTML exists
        const $overlay = $(config.overlaySelector);
        console.log('🌸 Modal overlay found:', $overlay.length > 0);
        
        if ($overlay.length === 0) {
            console.error('❌ CRITICAL: Flower Station modal overlay not found in DOM!');
        }
        
        FlowerModal.init();
    });
    
    // Expose FlowerModal globally for debugging
    window.TPBFlowerModal = FlowerModal;
    
})(jQuery);
