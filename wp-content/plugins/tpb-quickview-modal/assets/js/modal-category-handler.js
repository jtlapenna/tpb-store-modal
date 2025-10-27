/**
 * TPB Category Station Modal Handler
 * Individual handler for Category Station modals only
 */

console.log('📦 TPB Category Station Modal Handler LOADED - Version:', Date.now());

(function($) {
    'use strict';
    
    // Category Station Modal Configuration
    const config = {
        modalType: 'category-station',
        overlaySelector: '#tpb-qv-category-modal',
        stepperContainerSelector: '#tpb-qv-stepper-container',
        closeSelector: '.tpb-qv-close',
        triggerSelector: '[data-tpb-modal="category-station"], [data-tpb-quickview="category-station"], a[href*="category-station-configure-now"], a[href*="category-station"], a[href*="/product/category-station"], button[data-category="station"]',
        debug: window.TPB_QV_CONFIG?.debug || false,
        cpbEnabled: window.TPB_QV_CONFIG?.cpb_enabled || false,
        ajaxUrl: window.TPB_QV_CONFIG?.ajax_url || '',
        nonce: window.TPB_QV_CONFIG?.nonce || ''
    };
    
    // Category Station Modal Object
    const CategoryModal = {
        
        // Initialize modal
        init: function() {
            console.log('📦 Initializing Category Station Modal Handler...');
            this.bindEvents();
            this.setupEventListeners();
            this.addDiagnosticLogging();
        },
        
        // Add diagnostic logging to identify button elements
        addDiagnosticLogging: function() {
            console.log('📦 Adding diagnostic click logging...');
            
            // Debug: Log all clicks on the page to identify the button
            $(document).on('click', 'a, button', function(e) {
                const $el = $(this);
                const text = $el.text().trim();
                if (text.toLowerCase().includes('configure') || text.toLowerCase().includes('category')) {
                    console.log('🔍 CLICK DEBUG:', {
                        text: text,
                        href: $el.attr('href'),
                        tag: this.tagName,
                        classes: this.className,
                        dataAttrs: this.dataset,
                        matchesSelector: $el.is(config.triggerSelector),
                        parentElement: this.parentElement?.tagName,
                        parentClasses: this.parentElement?.className
                    });
                    
                    // Fallback: If button contains "category" and "configure", try to open modal
                    if (text.toLowerCase().includes('category') && text.toLowerCase().includes('configure')) {
                        console.log('🔍 FALLBACK: Category configure button detected, attempting to open modal');
                        e.preventDefault();
                        e.stopPropagation();
                        CategoryModal.openModal($el.attr('href') || window.location.href);
                    }
                    
                    // Additional fallback: Check for exact href pattern
                    if ($el.attr('href') && $el.attr('href').includes('category-station-configure-now')) {
                        console.log('🔍 HREF FALLBACK: Category station configure now button detected');
                        e.preventDefault();
                        e.stopPropagation();
                        CategoryModal.openModal($el.attr('href'));
                    }
                }
            });
        },
        
        // Bind events
        bindEvents: function() {
            const self = this;
            
            // Trigger clicks for category station only
            $(document).on('click', config.triggerSelector, function(e) {
                // Always prevent default FIRST to avoid redirects
                e.preventDefault();
                e.stopPropagation();
                
                const url = $(this).attr('href') || $(this).data('url') || window.location.href;
                const element = this;
                
                console.log('📦 Category Station trigger clicked:', {
                    element: element,
                    url: url,
                    href: $(this).attr('href'),
                    dataUrl: $(this).data('url'),
                    classList: element.classList.toString(),
                    dataset: element.dataset,
                    target: e.target,
                    currentTarget: e.currentTarget
                });
                
                console.log('📦 Category Station trigger matched, opening modal for URL:', url);
                self.openModal(url);
            });
            
            // Additional specific handler for Category Station Configure Now buttons
            $(document).on('click', 'a[href*="category-station-configure-now"]', function(e) {
                console.log('📦 Category Station Configure Now button clicked (specific handler)');
                e.preventDefault();
                e.stopPropagation();
                
                const url = $(this).attr('href');
                console.log('📦 Opening Category Station modal for URL:', url);
                self.openModal(url);
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
            console.log('📦 Opening Category Station modal for URL:', url);
            console.log('📦 Modal opening step 1: Function called');
            
            const $overlay = $(config.overlaySelector);
            const $stepperContainer = $(config.stepperContainerSelector);
            const $leftPanel = $overlay.find('.tpb-qv-left-panel');
            
            console.log('📦 Modal opening step 2: Elements found:', {
                overlay: $overlay.length,
                stepperContainer: $stepperContainer.length,
                leftPanel: $leftPanel.length,
                overlayElement: $overlay[0],
                stepperElement: $stepperContainer[0]
            });
            
            if ($overlay.length === 0) {
                console.error('❌ Category Station modal overlay not found');
                console.error('❌ Available overlays:', $('.tpb-qv-overlay').length);
                console.error('❌ Available overlays IDs:', $('.tpb-qv-overlay').map(function() { return this.id; }).get());
                return;
            }
            
            console.log('📦 Modal opening step 3: Modal element found, proceeding');
            
            // Extract product image
            this.extractProductImage(url, $leftPanel);
            
            // Update modal attributes
            $overlay.attr('data-modal-type', config.modalType);
            
            // Load stepper content directly
            this.loadStepper($stepperContainer);
            
            // Show modal
            console.log('📦 Modal opening step 4: Showing Category Station modal');
            $overlay.removeAttr('style').addClass('is-open');
            $('body').addClass('tpb-modal-open');
            
            // Notify modal state that modal is opened
            if (window.TPBModalState) {
                window.TPBModalState.setOpened('category-station');
            }
            
            // Debug modal visibility
            console.log('📦 Modal opening step 5: Modal visibility after show:', {
                overlayDisplay: $overlay.css('display'),
                overlayVisibility: $overlay.css('visibility'),
                overlayOpacity: $overlay.css('opacity'),
                overlayClasses: $overlay.attr('class'),
                bodyClasses: $('body').attr('class')
            });
            
            console.log('📦 Modal opening step 6: Modal should now be visible');
            
            // Focus management
            $overlay.find(config.closeSelector).focus();
        },
        
        // Load Category Station stepper
        loadStepper: function($container) {
            console.log('📦 Modal opening step 7: Loading Category Station stepper');
            
            // Check if stepper content already exists
            const existingStepper = $container.find('.tpb-qv-native').length > 0;
            if (existingStepper) {
                console.log('📦 Category Station stepper already loaded, skipping');
                return;
            }
            
            // Clear container
            $container.empty();
            
            // Create stepper container
            const $stepperDiv = $('<div class="tpb-qv-native" id="tpb-qv-native"></div>');
            $container.append($stepperDiv);
            
            console.log('📦 Modal opening step 8: Stepper container created');
            
        // Load the Category Station stepper script
        const script = document.createElement('script');
        script.src = window.TPB_QV_CONFIG?.plugin_url + 'assets/js/category-stepper-modal.js?v=' + Date.now();
        script.onload = function() {
            console.log('📦 Modal opening step 9: Category Station stepper script loaded');
            
            // Check if the stepper content is being generated
            setTimeout(function() {
                const stepperContent = $container.find('.tpb-qv-native');
                console.log('📦 Modal opening step 10: Stepper content after load:', stepperContent.length, stepperContent.html());
                if (stepperContent.length === 0) {
                    console.error('❌ No stepper content generated');
                } else {
                    console.log('✅ Category Station modal fully loaded with stepper content');
                }
            }, 1000);
        };
        script.onerror = function() {
            console.error('❌ Failed to load Category Station stepper script');
        };
        document.head.appendChild(script);
        },
        
        // Extract product image
        extractProductImage: function(url, $leftPanel) {
            console.log('📦 Extracting Category Station product image from URL:', url);
            
            const $img = $leftPanel.find('#tpb-qv-product-image');
            if ($img.length) {
                // Clear any existing content first
                $img.empty();
                console.log('📦 Cleared existing image content');
                
                // Fetch image dynamically from Category Station product (ID: 4833)
                this.fetchProductImage(4833, $img);
            } else {
                console.error('❌ Category Station image container not found');
            }
        },
        
        // Fetch product image from WordPress REST API
        fetchProductImage: function(productId, $imgContainer) {
            console.log('📦 Fetching product image for ID:', productId);
            
            // Prevent multiple simultaneous loads
            if ($imgContainer.data('loading')) {
                console.log('📦 Image already loading, skipping');
                return;
            }
            $imgContainer.data('loading', true);
            
            // Use WordPress REST API to get product details
            fetch(`/wp-json/wc/v3/products/${productId}?consumer_key=ck_123&consumer_secret=cs_123`)
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`HTTP error! status: ${response.status}`);
                    }
                    return response.json();
                })
                .then(product => {
                    console.log('📦 Product data received:', product);
                    
                    // Get the featured image
                    if (product.images && product.images.length > 0) {
                        const imageUrl = product.images[0].src;
                        console.log('📦 Setting Category Station image:', imageUrl);
                        
                        // Create image element but don't insert it yet
                        const $img = $('<img>')
                            .attr('src', imageUrl)
                            .attr('alt', product.images[0].alt || 'Category Station');
                        
                        // Add load event handler before inserting
                        $img.on('load', function() {
                            console.log('📦 Category Station image loaded successfully');
                            $(this).addClass('loaded');
                            $imgContainer.data('loading', false);
                        });
                        
                        // Now insert the image
                        $imgContainer.html($img);
                    } else {
                        console.warn('📦 No images found for product, using fallback');
                        this.setFallbackImage($imgContainer);
                    }
                })
                .catch(error => {
                    console.error('❌ Error fetching product image:', error);
                    // Fallback to admin-ajax if REST API fails
                    this.fetchProductImageAjax(productId, $imgContainer);
                });
        },
        
        // Fallback method using admin-ajax
        fetchProductImageAjax: function(productId, $imgContainer) {
            console.log('📦 Using admin-ajax fallback for product image');
            
            const formData = new FormData();
            formData.append('action', 'tpb_qv_get_product_image');
            formData.append('product_id', productId);
            formData.append('nonce', config.nonce);
            
            fetch(config.ajaxUrl, {
                method: 'POST',
                body: formData,
                credentials: 'same-origin'
            })
            .then(response => response.json())
            .then(data => {
                if (data.success && data.data.image_url) {
                    console.log('📦 Setting Category Station image via AJAX:', data.data.image_url);
                    // Create image element but don't insert it yet
                    const $img = $('<img>')
                        .attr('src', data.data.image_url)
                        .attr('alt', 'Category Station');
                    
                    // Add load event handler before inserting
                    $img.on('load', function() {
                        console.log('📦 Category Station image loaded via AJAX');
                        $(this).addClass('loaded');
                        $imgContainer.data('loading', false);
                    });
                    
                    // Now insert the image
                    $imgContainer.html($img);
                } else {
                    console.warn('📦 AJAX fallback failed, using static image');
                    this.setFallbackImage($imgContainer);
                }
            })
            .catch(error => {
                console.error('❌ AJAX fallback error:', error);
                this.setFallbackImage($imgContainer);
            });
        },
        
        // Set fallback image
        setFallbackImage: function($imgContainer) {
            const fallbackUrl = '/wp-content/uploads/2024/01/category-station-hero.jpg';
            console.log('📦 Using fallback image:', fallbackUrl);
            // Create fallback image element but don't insert it yet
            const $img = $('<img>')
                .attr('src', fallbackUrl)
                .attr('alt', 'Category Station');
            
            // Add load event handler before inserting
            $img.on('load', function() {
                console.log('📦 Category Station fallback image loaded');
                $(this).addClass('loaded');
                $imgContainer.data('loading', false);
            });
            
            // Now insert the image
            $imgContainer.html($img);
        },
        
        // Close modal
        closeModal: function() {
            console.log('📦 Closing Category Station modal');
            
            const $overlay = $(config.overlaySelector);
            const $stepperContainer = $(config.stepperContainerSelector);
            
            // Clear stepper content
            $stepperContainer.empty();
            
            // Hide modal
            $overlay.removeClass('is-open').attr('style', 'display: none;');
            $('body').removeClass('tpb-modal-open');
            
            // Clean up any loaded scripts
            this.cleanupSteppers();
        },
        
        // Cleanup steppers
        cleanupSteppers: function() {
            // Remove stepper scripts that were dynamically loaded
            $('script[src*="category-stepper-modal.js"]').remove();
            
            // Clear any stepper containers
            $('.tpb-qv-native').remove();
        }
    };
    
    // Initialize when DOM is ready
    $(document).ready(function() {
        console.log('📦 DOM ready, initializing Category Station modal...');
        console.log('📦 TPB_QV_CONFIG:', window.TPB_QV_CONFIG);
        console.log('📦 jQuery version:', $.fn.jquery);
        
        // Test if modal HTML exists
        const $overlay = $(config.overlaySelector);
        console.log('📦 Modal overlay found:', $overlay.length > 0);
        console.log('📦 Overlay selector:', config.overlaySelector);
        console.log('📦 All overlays found:', $('.tpb-qv-overlay').length);
        console.log('📦 All overlays:', $('.tpb-qv-overlay').map(function() { return this.id; }).get());
        
        if ($overlay.length === 0) {
            console.error('❌ CRITICAL: Category Station modal overlay not found in DOM!');
            console.error('❌ Available overlays:', $('.tpb-qv-overlay').map(function() { return this.id; }).get());
        }
        
        // Test trigger selectors
        const $triggers = $(config.triggerSelector);
        console.log('📦 Trigger elements found:', $triggers.length);
        console.log('📦 Trigger selector:', config.triggerSelector);
        console.log('📦 Trigger elements:', $triggers.map(function() { 
            return {
                tag: this.tagName,
                href: this.href,
                class: this.className,
                id: this.id,
                dataset: this.dataset
            };
        }).get());
        
        CategoryModal.init();
        
        // Debug: Log available modal elements
        console.log('📦 DEBUG: Available modal elements on page:', {
            allOverlays: $('.tpb-qv-overlay').length,
            categoryOverlay: $('#tpb-qv-category-modal').length,
            flowerOverlay: $('#tpb-qv-flower-modal').length,
            allModals: $('.tpb-qv-modal').length,
            categoryModal: $('#tpb-qv-category-modal .tpb-qv-modal').length
        });
        
        // Debug functions removed - no longer needed
    });
    
    // Expose CategoryModal globally for debugging
    window.TPBCategoryModal = CategoryModal;
    
})(jQuery);