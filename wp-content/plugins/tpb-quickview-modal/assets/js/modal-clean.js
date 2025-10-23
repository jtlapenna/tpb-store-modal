/**
 * TPB QuickView Modal JavaScript
 * Clean, reliable modal functionality
 */

console.log('TPB Modal Clean JS LOADED - Version:', Date.now(), '- CACHE BUSTED');

// Check for cache issues
if (window.TPB_MODAL_LOADED) {
    console.warn('CACHE ISSUE: modal-clean.js loaded multiple times or from cache');
}
window.TPB_MODAL_LOADED = Date.now();
console.log('Modal JS timestamp:', window.TPB_MODAL_LOADED);

// Check for cache issues
if (window.TPB_MODAL_LOADED) {
    console.warn('CACHE ISSUE: modal-clean.js loaded multiple times or from cache');
}
window.TPB_MODAL_LOADED = Date.now();
console.log('Modal JS timestamp:', window.TPB_MODAL_LOADED);

// Add error boundary logging
window.addEventListener('error', function(e) {
    console.error('Global error caught:', e.message, e.filename, e.lineno);
});

(function($) {
    'use strict';
    
    console.log('🚀 TPB QuickView Modal Plugin Loading...');
    
    // Configuration
    const config = {
        overlaySelector: '#tpb-qv-overlay',
        iframeSelector: '#tpb-qv-iframe',
        closeSelector: '.tpb-qv-close',
        triggerSelector: '[data-tpb-modal="true"], [data-tpb-quickview], a[href*="/product/"], a[href*="/configure-menu-boards"], a[href*="/configure-branded"], .afc_add_to_cart_button, .single_add_to_cart_button',
        qvParam: 'tpb_qv_iframe',
        debug: window.TPB_QV_CONFIG?.debug || false,
        cpbEnabled: window.TPB_QV_CONFIG?.cpb_enabled || false,
        ajaxUrl: window.TPB_QV_CONFIG?.ajax_url || '',
        nonce: window.TPB_QV_CONFIG?.nonce || ''
    };
    
    // Modal object
    const Modal = {
        
        // Initialize modal
        init: function() {
            console.log('🔧 Initializing TPB QuickView Modal...');
            
            this.bindEvents();
            this.detectButtons();
            this.setupModal();
            
            console.log('✅ TPB QuickView Modal initialized');
        },
        
        // Bind event listeners
        bindEvents: function() {
            const self = this;
            
            // Close button
            $(document).on('click', config.closeSelector, function(e) {
                e.preventDefault();
                e.stopPropagation();
                console.log('🔴 Close button clicked');
                self.close();
            });
            
            // Overlay click to close
            $(document).on('click', config.overlaySelector, function(e) {
                if (e.target === this) {
                    console.log('🔴 Overlay clicked to close');
                    self.close();
                }
            });
            
            // ESC key to close
            $(document).on('keydown', function(e) {
                if (e.key === 'Escape' && self.isOpen()) {
                    console.log('🔴 ESC key pressed to close');
                    self.close();
                }
            });
            
            // Modal trigger clicks
            $(document).on('click', config.triggerSelector, function(e) {
                const href = $(this).attr('href') || $(this).data('product-url');
                if (href && (href.includes('/product/') || href.includes('/configure-menu-boards') || href.includes('/configure-branded'))) {
                    e.preventDefault();
                    console.log('🔴 Modal trigger clicked:', href);
                    self.open(href);
                }
            });
            
            // Listen for messages from iframe (CPB form submissions)
            window.addEventListener('message', function(event) {
                if (event.origin !== window.location.origin) return;
                
                if (event.data.type === 'tpb_qv_cpb_add_to_cart') {
                    console.log('📦 CPB add to cart message received:', event.data);
                    self.handleCpbAddToCart(event.data);
                }
                
                if (event.data.type === 'tpb_qv_close_modal') {
                    console.log('❌ Close modal message received');
                    self.close();
                }
            });
            
            console.log('✅ Event listeners bound');
        },
        
        // Detect Configure Now buttons
        detectButtons: function() {
            console.log('🔍 Detecting Configure Now buttons...');
            
            // CPB specific buttons
            const cpbButtons = $('.afc_add_to_cart_button, .single_add_to_cart_button, .afc_configure_button');
            console.log(`🎯 Found ${cpbButtons.length} CPB buttons`);
            
            cpbButtons.each(function() {
                const $this = $(this);
                const href = $this.attr('href') || window.location.href;
                
                if (href.includes('/product/')) {
                    console.log(`✅ Wiring up CPB button: ${$this.text().trim()}`);
                    $this.attr('data-tpb-modal', 'true');
                    $this.attr('data-product-url', href);
                }
            });
            
            // Generic configure/customize buttons
            const genericButtons = $('a, button').filter(function() {
                const text = $(this).text().toLowerCase().trim();
                return text.includes('configure') || text.includes('customize') || text.includes('build');
            });
            
            console.log(`🎯 Found ${genericButtons.length} generic buttons`);
            
            genericButtons.each(function() {
                const $this = $(this);
                const href = $this.attr('href');
                
                if (href && href.includes('/product/')) {
                    console.log(`✅ Wiring up generic button: ${$this.text().trim()}`);
                    $this.attr('data-tpb-modal', 'true');
                    $this.attr('data-product-url', href);
                }
            });
        },
        
        // Setup modal HTML
        setupModal: function() {
            // Ensure modal exists
            if ($(config.overlaySelector).length === 0) {
                console.log('⚠️ Modal HTML not found, creating...');
                this.createModal();
            }
            
            // Apply close button styling
            this.styleCloseButton();
            
            // Listen for price updates from iframe
            window.addEventListener('message', function(e) {
                if (e.data && e.data.type === 'TPB_QV' && e.data.action === 'update_base_price') {
                    var priceEl = document.getElementById('tpb-qv-base-price');
                    if (priceEl) {
                        // Add updating animation
                        priceEl.classList.add('updating');
                        
                        // Force reflow to ensure transition triggers
                        void priceEl.offsetWidth;
                        
                        // Delay content update to ensure fade-out completes (15% slower)
                        setTimeout(function() {
                            if (e.data.price) {
                                priceEl.innerHTML = 'Base Price: ' + e.data.price;
                                priceEl.classList.add('has-price');
                            } else {
                                priceEl.innerHTML = 'Please choose a SKU-count to see the base-price for electronics hardware.<br>Furniture-inclusive pricing will appear below according to your selections.';
                                priceEl.classList.remove('has-price');
                            }
                            
                            // Remove updating animation after content update
                            setTimeout(function() {
                                priceEl.classList.remove('updating');
                            }, 115);
                        }, 173);
                    }
                }
            });
        },
        
        // Style close button
        styleCloseButton: function() {
            const $closeButton = $(config.closeSelector);
            if ($closeButton.length) {
                // Use attr to set style with !important
                $closeButton.attr('style', 
                    'background: rgb(79, 176, 137) !important; ' +
                    'color: #fff !important; ' +
                    'position: absolute !important; ' +
                    'top: 20px !important; ' +
                    'right: 20px !important; ' +
                    'width: 30px !important; ' +
                    'height: 30px !important; ' +
                    'border-radius: 50% !important; ' +
                    'font-size: 18px !important; ' +
                    'border: none !important; ' +
                    'box-shadow: none !important; ' +
                    'appearance: none !important; ' +
                    'min-width: 30px !important; ' +
                    'min-height: 30px !important; ' +
                    'padding: 0px !important;'
                );
                console.log('🎨 Applied close button styling');
            }
        },
        
        // Create modal HTML (fallback)
        createModal: function() {
            const modalHTML = `
                <div id="tpb-qv-overlay" class="tpb-qv-overlay" style="display: none;">
                    <div class="tpb-qv-modal">
                        <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
                        <div class="tpb-qv-left-panel">
                            <div id="tpb-qv-product-image">
                                <!-- Product image will be loaded here -->
                            </div>
                        </div>
                        <div class="tpb-qv-right-panel">
                            <h2 class="tpb-qv-title">Configure Your Flower Station</h2>
                            <div id="tpb-qv-base-price" class="tpb-qv-base-price">
                                Please choose a SKU-count to see the base-price for electronics hardware.<br>
                                Furniture-inclusive pricing will appear below according to your selections.
                            </div>
                        <iframe id="tpb-qv-iframe" class="tpb-qv-iframe" src="about:blank"></iframe>
                        </div>
                    </div>
                </div>
            `;
            $('body').append(modalHTML);
        },
        
        // Open modal
        open: function(url) {
            console.log('✅ Opening modal for:', url);
            
            const $overlay = $(config.overlaySelector);
            const $iframe = $(config.iframeSelector);
            const $leftPanel = $overlay.find('.tpb-qv-left-panel');
            const $rightPanel = $overlay.find('.tpb-qv-right-panel');
            
            if ($overlay.length === 0) {
                console.error('❌ Modal overlay not found');
                return;
            }
            
            // Build iframe URL
            const iframeUrl = this.buildIframeUrl(url);
            
            // === MODAL OPENING DEBUG ===
            console.log('=== MODAL OPENING DEBUG ===');
            console.log('URL passed to modal:', url);
            console.log('Built iframe URL:', iframeUrl);
            console.log('URL includes quick-checkout:', url.includes('quick-checkout-station-configure-now'));
            console.log('Iframe URL includes quick-checkout:', iframeUrl.includes('quick-checkout-station-configure-now'));
            console.log('URL type:', typeof url);
            console.log('URL length:', url.length);
            console.log('Searching for:', 'quick-checkout-station-configure-now');
            console.log('URL indexOf result:', url.indexOf('quick-checkout-station-configure-now'));
            console.log('=== END DEBUG ===');
            
            console.log('🔗 Iframe URL:', iframeUrl);
            
            // Extract product image from the page with cache busting
            this.extractProductImage(url, $leftPanel);
            
            // Force refresh image for Quick Checkout modal
            const isQuickCheckout = url.includes('quick-checkout-station-configure-now') || 
                                  iframeUrl.includes('quick-checkout-station-configure-now') ||
                                  url.indexOf('quick-checkout-station-configure-now') !== -1 ||
                                  iframeUrl.indexOf('quick-checkout-station-configure-now') !== -1;
            
            console.log('Quick Checkout detection result:', isQuickCheckout);
            
            if (isQuickCheckout) {
                console.log('🔄 Force refreshing Quick Checkout image immediately...');
                this.fetchProductImageFromPage('http://the-peak-beyond-modal.local/product/quick-checkout-station-configure-now/?v=' + Date.now(), $leftPanel);
            }
            
            // Set iframe source for right panel (only for product modals)
            if (!url.includes('menu-boards') && !url.includes('configure-menu-boards') && 
                !url.includes('branded') && !url.includes('configure-branded')) {
            $iframe.attr('src', iframeUrl);
            } else {
                // Hide iframe for simplified modals
                $iframe.hide();
            }
            
            // Inject CSS into iframe after it loads
            $iframe.on('load', function() {
                // IMPORTANT: When loading the dedicated QV iframe page, DO NOT inject hide CSS/JS
                try {
                    var srcUrl = this.src || '';
                    if (srcUrl.indexOf('tpb_qv_iframe=1') !== -1) {
                        console.log('⏭️ Skipping iframe hide/injection in quickview mode (modal-clean)');
                        return;
                    }
                } catch (e) {}
                try {
                    const iframeDoc = this.contentDocument || this.contentWindow.document;
                    const $iframeContent = $(iframeDoc);
                    
                    // Inject CSS to hide non-CPB elements and improve layout
                    const hideCSS = `
                        <style id="tpb-qv-hide-css">
                            /* Hide everything before CPB */
                            body.tpb-qv-iframe .product_title,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description > h1,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description > .price,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description > p:first-of-type,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description > a,
                            body.tpb-qv-iframe .summary > div:first-child,
                            body.tpb-qv-iframe .price,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description > p .price,
                            body.tpb-qv-iframe a[href="#"] {
                                display: none !important;
                            }
                            
                            /* Hide specific elements */
                            body.tpb-qv-iframe .instock,
                            body.tpb-qv-iframe a[href*="/product/"],
                            body.tpb-qv-iframe p:contains("instock"),
                            body.tpb-qv-iframe a[href*="/product/"],
                            body.tpb-qv-iframe .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity {
                                display: none !important;
                            }
                            
                            /* Remove WordPress admin bar margin and any other spacing */
                            html {
                                margin-top: 0 !important;
                                margin-bottom: 0 !important;
                                padding-top: 0 !important;
                                padding-bottom: 0 !important;
                            }
                            
                            /* Override any admin bar related margins */
                            html.admin-bar,
                            body.admin-bar,
                            html.logged-in,
                            body.logged-in {
                                margin-top: 0 !important;
                                padding-top: 0 !important;
                            }
                            
                            /* Improve layout and prevent overflow */
                            body.tpb-qv-iframe {
                                overflow-x: hidden !important;
                                max-width: 100% !important;
                                padding: 0 !important;
                                margin: 0 !important;
                                margin-top: 0 !important;
                                margin-bottom: 0 !important;
                                padding-top: 0 !important;
                                padding-bottom: 0 !important;
                                padding-left: 0 !important;
                                padding-right: 0 !important;
                            }
                            
                            /* Override any theme or plugin padding */
                            body.tpb-qv-iframe * {
                                box-sizing: border-box !important;
                            }
                            
                            body.tpb-qv-iframe .woocommerce-product-details__short-description {
                                overflow-x: hidden !important;
                                max-width: 100% !important;
                                padding: 80px 20px 20px 20px !important;
                                margin: 0 !important;
                            }
                            
                            /* Style build strategy as options, not products */
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product {
                                border: 2px solid #e0e0e0 !important;
                                border-radius: 8px !important;
                                padding: 15px !important;
                                margin: 10px 0 !important;
                                background: #f9f9f9 !important;
                                cursor: pointer !important;
                                transition: all 0.3s ease !important;
                            }
                            
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product:hover {
                                border-color: #007cba !important;
                                background: #f0f8ff !important;
                            }
                            
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product input[type="radio"] {
                                margin-right: 10px !important;
                            }
                            
                            /* Style bundle options as product cards */
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product:last-child {
                                display: grid !important;
                                grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)) !important;
                                gap: 15px !important;
                                margin-top: 20px !important;
                            }
                            
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product:last-child > div {
                                border: 2px solid #e0e0e0 !important;
                                border-radius: 12px !important;
                                padding: 20px !important;
                                background: white !important;
                                box-shadow: 0 2px 8px rgba(0,0,0,0.1) !important;
                                cursor: pointer !important;
                                transition: all 0.3s ease !important;
                                text-align: center !important;
                            }
                            
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product:last-child > div:hover {
                                border-color: #007cba !important;
                                box-shadow: 0 4px 16px rgba(0,0,0,0.15) !important;
                                transform: translateY(-2px) !important;
                            }
                            
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product:last-child > div input[type="radio"] {
                                margin-bottom: 10px !important;
                            }
                            
                            /* Hide price displays */
                            body.tpb-qv-iframe .price,
                            body.tpb-qv-iframe .woocommerce-Price-amount,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .price,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-Price-amount {
                                display: none !important;
                            }
                            
                            /* Hide specific remaining elements */
                            body.tpb-qv-iframe p:contains("instock"),
                            body.tpb-qv-iframe a[href*="/product/"],
                            body.tpb-qv-iframe .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product p:contains("instock"),
                            body.tpb-qv-iframe .afc-composite-product a[href*="/product/"],
                            body.tpb-qv-iframe .afc-composite-product .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity {
                                display: none !important;
                            }
                            
                            /* Add product title and instructions at top */
                            body.tpb-qv-iframe .woocommerce-product-details__short-description::before {
                                content: "Flower Station – Configure Now" !important;
                                display: block !important;
                                font-size: 22px !important;
                                font-weight: 700 !important;
                                margin-bottom: 15px !important;
                                color: #2c3e50 !important;
                                text-align: center !important;
                                padding: 12px 20px !important;
                                border-bottom: 2px solid #e9ecef !important;
                                background: linear-gradient(135deg, #f8f9fa 0%, #e9ecef 100%) !important;
                                border-radius: 8px 8px 0 0 !important;
                                position: fixed !important;
                                top: 0 !important;
                                left: 0 !important;
                                right: 0 !important;
                                z-index: 1000 !important;
                                width: 100% !important;
                                box-sizing: border-box !important;
                            }
                            
                            body.tpb-qv-iframe .woocommerce-product-details__short-description p:first-of-type::after {
                                content: "Configure your Flower Station hardware to match your store's needs. Adjust SKUs, mounting, and finishes. Choose your preferred flower station configuration with hardware and furniture included." !important;
                                display: block !important;
                                font-size: 16px !important;
                                color: #6c757d !important;
                                margin-bottom: 25px !important;
                                font-style: normal !important;
                                line-height: 1.6 !important;
                                text-align: center !important;
                                padding: 0 20px !important;
                            }
                            
                            /* Hide more specific elements */
                            body.tpb-qv-iframe .woocommerce-product-details__short-description p,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description a,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description div:not(.afc-composite-product),
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description p,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description a,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description div:not(.afc-composite-product),
                            body.tpb-qv-iframe p:contains("instock"),
                            body.tpb-qv-iframe a[href*="/product/"],
                            body.tpb-qv-iframe .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity {
                                display: none !important;
                            }
                            
                            /* Hide specific elements with more targeted selectors */
                            body.tpb-qv-iframe p:contains("instock"),
                            body.tpb-qv-iframe a[href*="/product/"],
                            body.tpb-qv-iframe .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product p:contains("instock"),
                            body.tpb-qv-iframe .afc-composite-product a[href*="/product/"],
                            body.tpb-qv-iframe .afc-composite-product .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product p:contains("instock"),
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product a[href*="/product/"],
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity {
                                display: none !important;
                            }
                            
                            /* Hide specific elements with more targeted selectors */
                            body.tpb-qv-iframe p:contains("instock"),
                            body.tpb-qv-iframe a[href*="/product/"],
                            body.tpb-qv-iframe .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product p:contains("instock"),
                            body.tpb-qv-iframe .afc-composite-product a[href*="/product/"],
                            body.tpb-qv-iframe .afc-composite-product .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product p:contains("instock"),
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product a[href*="/product/"],
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity {
                                display: none !important;
                            }
                            
                            /* Hide specific elements with valid CSS selectors */
                            body.tpb-qv-iframe a[href*="/product/"],
                            body.tpb-qv-iframe .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product a[href*="/product/"],
                            body.tpb-qv-iframe .afc-composite-product .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product a[href*="/product/"],
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .woocommerce-product-details__short-description .quantity {
                                display: none !important;
                            }
                            
                            /* Hide specific text elements that contain unwanted content - using more specific selectors */
                            body.tpb-qv-iframe .afc-composite-product p,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product p,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product p,
                            body.tpb-qv-iframe a[href*="/product/"],
                            body.tpb-qv-iframe .quantity {
                                display: none !important;
                            }
                            
                            /* Show only the essential CPB content */
                            body.tpb-qv-iframe .afc-composite-product h4,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product h4,
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product h4,
                            body.tpb-qv-iframe .afc-composite-product input[type="radio"],
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product input[type="radio"],
                            body.tpb-qv-iframe .afc-composite-product .afc-composite-product .afc-composite-product input[type="radio"] {
                                display: block !important;
                            }
                            
                            /* Ensure no options are pre-selected */
                            body.tpb-qv-iframe input[type="radio"]:checked {
                                background-color: transparent !important;
                            }
                            
                            body.tpb-qv-iframe input[type="radio"] {
                                appearance: none !important;
                                -webkit-appearance: none !important;
                                -moz-appearance: none !important;
                                width: 20px !important;
                                height: 20px !important;
                                border: 2px solid #ddd !important;
                                border-radius: 50% !important;
                                background-color: white !important;
                                cursor: pointer !important;
                                position: relative !important;
                                margin-right: 10px !important;
                                vertical-align: middle !important;
                            }
                            
                            body.tpb-qv-iframe input[type="radio"]:checked::after {
                                content: '' !important;
                                position: absolute !important;
                                top: 50% !important;
                                left: 50% !important;
                                transform: translate(-50%, -50%) !important;
                                width: 10px !important;
                                height: 10px !important;
                                border-radius: 50% !important;
                                background-color: #007cba !important;
                            }
                            
                            body.tpb-qv-iframe input[type="radio"]:checked {
                                border-color: #007cba !important;
                            }
                            
                            /* Ensure CPB content is visible */
                            body.tpb-qv-iframe .afc-composite-product,
                            body.tpb-qv-iframe .afc-composite-product * {
                                display: block !important;
                            }
                        </style>
                        <script>
                        // Function to uncheck all form elements and hide unwanted content
                        function initializeModalContent() {
                            console.log('🔧 Initializing modal content...');
                            
                            // Uncheck all radio buttons
                            var radioButtons = document.querySelectorAll('input[type="radio"]');
                            radioButtons.forEach(function(radio) {
                                radio.checked = false;
                            });
                            console.log('🔘 Unchecked', radioButtons.length, 'radio buttons');
                            
                            // Uncheck all checkboxes
                            var checkboxes = document.querySelectorAll('input[type="checkbox"]');
                            checkboxes.forEach(function(checkbox) {
                                checkbox.checked = false;
                            });
                            console.log('☑️ Unchecked', checkboxes.length, 'checkboxes');
                            
                            // Reset all select elements to first option (unselected state)
                            var selectElements = document.querySelectorAll('select');
                            selectElements.forEach(function(select) {
                                if (select.options.length > 0) {
                                    select.selectedIndex = -1; // No selection
                                    // If that doesn't work, select the first option but mark it as placeholder
                                    if (select.selectedIndex === 0) {
                                        select.options[0].text = 'Please select...';
                                        select.options[0].value = '';
                                    }
                                }
                            });
                            console.log('📋 Reset', selectElements.length, 'select elements');
                            
                            // Hide unwanted elements
                            hideUnwantedElements();
                            
                            // Initialize progressive disclosure with retry logic
                            var progressiveSuccess = initializeProgressiveDisclosure();
                            if (!progressiveSuccess) {
                                console.log('🔄 Progressive disclosure failed, will retry in 2 seconds...');
                                setTimeout(function() {
                                    initializeProgressiveDisclosure();
                                }, 2000);
                            }
                        }
                        
                        // Function to implement progressive disclosure
                        function initializeProgressiveDisclosure() {
                            console.log('🎯 Initializing progressive disclosure...');
                            
                            // Find all CPB sections by looking for divs with h4 headings
                            var cpbSections = [];
                            var allDivs = document.querySelectorAll('div');
                            
                            allDivs.forEach(function(div) {
                                var h4 = div.querySelector('h4');
                                if (h4 && (h4.textContent.includes('Number of SKUs') || 
                                          h4.textContent.includes('Build Strategy') || 
                                          h4.textContent.includes('Choose Your Complete Bundle'))) {
                                    cpbSections.push(div);
                                }
                            });
                            
                            console.log('📋 Found', cpbSections.length, 'CPB sections');
                            
                            // If we don't have enough sections, try again later
                            if (cpbSections.length < 3) {
                                console.log('⚠️ Not enough CPB sections found, will retry...');
                                return false;
                            }
                            
                            // Hide all sections except the first one
                            for (var i = 1; i < cpbSections.length; i++) {
                                cpbSections[i].style.display = 'none';
                                console.log('🙈 Hidden section', i + 1);
                            }
                            
                            // Show only the first section
                            cpbSections[0].style.display = 'block';
                            console.log('👁️ Showing section 1');
                            
                            // Add event listeners to radio buttons for progressive disclosure
                            addProgressiveListeners();
                            return true;
                        }
                        
                        // Function to add event listeners for progressive disclosure
                        function addProgressiveListeners() {
                            console.log('🎧 Adding progressive disclosure listeners...');
                            
                            // Listen for changes on all radio buttons
                            var radioButtons = document.querySelectorAll('input[type="radio"]');
                            radioButtons.forEach(function(radio, index) {
                                radio.addEventListener('change', function() {
                                    if (this.checked) {
                                        console.log('✅ Radio button', index, 'selected:', this.value);
                                        showNextStep(this);
                                    }
                                });
                            });
                            
                            // Listen for changes on select elements
                            var selectElements = document.querySelectorAll('select');
                            selectElements.forEach(function(select, index) {
                                select.addEventListener('change', function() {
                                    if (this.value && this.value !== '') {
                                        console.log('✅ Select', index, 'changed to:', this.value);
                                        showNextStep(this);
                                    }
                                });
                            });
                        }
                        
                        // Function to show the next step
                        function showNextStep(selectedElement) {
                            console.log('➡️ Showing next step after selection...');
                            
                            // Find the parent section by looking for the closest div with an h4 heading
                            var currentSection = selectedElement.closest('div');
                            while (currentSection && !currentSection.querySelector('h4')) {
                                currentSection = currentSection.parentElement;
                            }
                            
                            if (!currentSection) return;
                            
                            // Find all CPB sections
                            var cpbSections = [];
                            var allDivs = document.querySelectorAll('div');
                            
                            allDivs.forEach(function(div) {
                                var h4 = div.querySelector('h4');
                                if (h4 && (h4.textContent.includes('Number of SKUs') || 
                                          h4.textContent.includes('Build Strategy') || 
                                          h4.textContent.includes('Choose Your Complete Bundle'))) {
                                    cpbSections.push(div);
                                }
                            });
                            
                            var currentIndex = cpbSections.indexOf(currentSection);
                            console.log('📍 Current section index:', currentIndex);
                            
                            // Show the next section if it exists
                            if (currentIndex < cpbSections.length - 1) {
                                var nextSection = cpbSections[currentIndex + 1];
                                nextSection.style.display = 'block';
                                console.log('👁️ Showing section', currentIndex + 2);
                                
                                // Scroll to the next section
                                nextSection.scrollIntoView({ behavior: 'smooth', block: 'start' });
                            } else {
                                console.log('🏁 All steps completed!');
                            }
                        }
                        
                        // Function to hide unwanted elements
                        function hideUnwantedElements() {
                            // Hide "instock" text
                            var instockElements = document.querySelectorAll('p');
                            var instockCount = 0;
                            instockElements.forEach(function(p) {
                                if (p.textContent && p.textContent.toLowerCase().includes('instock')) {
                                    p.style.display = 'none';
                                    instockCount++;
                                }
                            });
                            console.log('📝 Hidden', instockCount, 'instock elements');
                            
                            // Hide "View product" links
                            var productLinks = document.querySelectorAll('a[href*="/product/"]');
                            productLinks.forEach(function(link) {
                                link.style.display = 'none';
                            });
                            console.log('🔗 Hidden', productLinks.length, 'product links');
                            
                            // Hide quantity selectors
                            var quantityElements = document.querySelectorAll('.quantity');
                            quantityElements.forEach(function(qty) {
                                qty.style.display = 'none';
                            });
                            console.log('🔢 Hidden', quantityElements.length, 'quantity selectors');
                            
                            // Hide price displays
                            var priceElements = document.querySelectorAll('.price, .woocommerce-Price-amount');
                            priceElements.forEach(function(price) {
                                price.style.display = 'none';
                            });
                            console.log('💰 Hidden', priceElements.length, 'price displays');
                            
                            // Hide "not purchasable" notices
                            var notPurchasableElements = document.querySelectorAll('p');
                            var notPurchasableCount = 0;
                            notPurchasableElements.forEach(function(p) {
                                if (p.textContent && (p.textContent.includes('cannot be purchased') || p.textContent.includes('not purchasable'))) {
                                    p.style.display = 'none';
                                    notPurchasableCount++;
                                }
                            });
                            console.log('❌ Hidden', notPurchasableCount, 'not purchasable notices');
                            
                            // Hide all links that contain "product" in href
                            var linkCount = 0;
                            var allLinks = document.querySelectorAll('a');
                            allLinks.forEach(function(link) {
                                var href = link.getAttribute('href');
                                if (href && href.includes('product')) {
                                    link.style.display = 'none';
                                    linkCount++;
                                }
                            });
                            console.log('🔗 Hidden', linkCount, 'links with product in href');
                            
                            console.log('✅ Additional elements hidden via JavaScript in iframe');
                        }
                        
                        // Run immediately when script loads
                        if (document.readyState === 'loading') {
                            document.addEventListener('DOMContentLoaded', initializeModalContent);
                        } else {
                            initializeModalContent();
                        }
                        
                        // Also run after delays to catch dynamically loaded content
                        setTimeout(initializeModalContent, 1000);
                        setTimeout(initializeModalContent, 3000);
                        setTimeout(initializeModalContent, 5000); // Additional delay for iframe content
                        
                        // Listen for messages from parent window
                        window.addEventListener('message', function(event) {
                            if (event.data.type === 'tpb_hide_elements' && event.data.action === 'hide_unwanted_elements') {
                                console.log('🔍 Received hide elements message in iframe');
                                initializeModalContent();
                            }
                        });
                        </script>
                    `;
                    
                    $iframeContent.find('head').append(hideCSS);
                    console.log('✅ CSS and JavaScript injected into iframe');
                    
               // Execute JavaScript directly in iframe context using contentWindow
               try {
                   var iframeWindow = this.contentWindow;
                   if (iframeWindow) {
                       console.log('🔍 Starting JavaScript element hiding in iframe...');
                       
                       // Use iframe's document directly
                       var iframeDocument = iframeWindow.document;
                       
                       // Add a delay to ensure the iframe content is fully loaded
                       setTimeout(function() {
                           console.log('🔍 Starting delayed JavaScript element hiding in iframe...');
                           
                           // Check if iframe document is accessible
                           if (!iframeDocument || !iframeDocument.querySelectorAll) {
                               console.log('⚠️ Iframe document not accessible');
                               return;
                           }
                           
                           console.log('✅ Iframe document accessible, starting element hiding...');
                           
                           // Try to execute JavaScript in the iframe context using postMessage
                           try {
                               iframeWindow.postMessage({
                                   type: 'tpb_hide_elements',
                                   action: 'hide_unwanted_elements'
                               }, '*');
                               console.log('📨 Sent postMessage to iframe');
                           } catch (postMessageError) {
                               console.log('⚠️ Could not send postMessage to iframe:', postMessageError);
                           }
                       
                       // Set up a MutationObserver to watch for dynamically added elements
                       var observer = new MutationObserver(function(mutations) {
                           var hiddenCount = 0;
                           mutations.forEach(function(mutation) {
                               if (mutation.type === 'childList') {
                                   mutation.addedNodes.forEach(function(node) {
                                       if (node.nodeType === 1) { // Element node
                                           // Hide "instock" text
                                           if (node.textContent && node.textContent.includes('instock')) {
                                               node.style.display = 'none';
                                               hiddenCount++;
                                           }
                                           
                                           // Hide "View product" links
                                           if (node.tagName === 'A' && node.href && node.href.includes('/product/')) {
                                               node.style.display = 'none';
                                               hiddenCount++;
                                           }
                                           
                                           // Hide quantity selectors
                                           if (node.classList && node.classList.contains('quantity')) {
                                               node.style.display = 'none';
                                               hiddenCount++;
                                           }
                                           
                                           // Hide price displays
                                           if (node.classList && (node.classList.contains('price') || node.classList.contains('woocommerce-Price-amount'))) {
                                               node.style.display = 'none';
                                               hiddenCount++;
                                           }
                                           
                                           // Hide "not purchasable" notices
                                           if (node.textContent && (node.textContent.includes('cannot be purchased') || node.textContent.includes('not purchasable'))) {
                                               node.style.display = 'none';
                                               hiddenCount++;
                                           }
                                       }
                                   });
                               }
                           });
                           if (hiddenCount > 0) {
                               console.log('🔄 Hidden', hiddenCount, 'dynamically added elements');
                           }
                       });
                       
                       // Start observing
                       observer.observe(iframeDocument.body, {
                           childList: true,
                           subtree: true
                       });
                       
                       console.log('👀 MutationObserver set up to watch for dynamic elements');
                       }, 1000); // 1 second delay
                   }
               } catch (e) {
                   console.log('⚠️ Could not execute JavaScript in iframe:', e);
               }
                } catch (e) {
                    console.log('⚠️ Could not inject CSS into iframe:', e);
                }
            });
            
            // Show modal with proper positioning
            $overlay.css({
                'display': 'flex',
                'position': 'fixed',
                'top': '0',
                'left': '0',
                'width': '100vw',
                'height': '100vh',
                'z-index': '999999'
            });
            
            // Trigger animation after a brief delay to ensure proper rendering
            setTimeout(() => {
                $overlay.addClass('is-open');
            }, 10);
            
            $('body').addClass('tpb-qv-locked');
            
            // Ensure modal is properly positioned with two-panel layout
            const $modal = $overlay.find('.tpb-qv-modal');
            
            // Add data attribute for modal type-specific styling
            if (url.includes('flower-station-configure-now')) {
                $modal.attr('data-modal-type', 'flower-station');
            } else if (url.includes('category-station-configure-now')) {
                $modal.attr('data-modal-type', 'category-station');
            } else if (url.includes('quick-checkout-configure-now')) {
                $modal.attr('data-modal-type', 'quick-checkout');
            }
            
            $modal.css({
                'display': 'flex',
                'flex-direction': 'row',
                'width': '85%',
                'max-width': '1400px',
                'height': '75vh',
                'max-height': '90vh',
                'margin': 'auto'
            });
            
            // Debug info
            if (config.debug) {
                this.showDebugInfo('Modal opened', {
                    url: url,
                    iframeUrl: iframeUrl,
                    overlayVisible: $overlay.is(':visible'),
                    modalPosition: $modal.offset()
                });
            }
            
            console.log('🎯 Modal should now be visible with two-panel layout');
            
            // Apply close button styling with a delay to ensure it runs after all other styles
            setTimeout(() => {
                this.styleCloseButton();
            }, 100);
        },
        
        // Close modal
        close: function() {
            console.log('❌ Closing modal...');
            
            const $overlay = $(config.overlaySelector);
            const $iframe = $(config.iframeSelector);
            
            if ($overlay.length === 0) {
                console.error('❌ Modal overlay not found for closing');
                return;
            }
            
            // Add close animation
            $overlay.addClass('closing');
            
            setTimeout(function() {
                $overlay.removeClass('is-open closing');
            $overlay.hide();
            $iframe.attr('src', 'about:blank');
            $('body').removeClass('tpb-qv-locked');
                
                // Clear the left panel image to prevent flashing on next open
                const $leftPanel = $overlay.find('.tpb-qv-left-panel');
                if ($leftPanel.length) {
                    $leftPanel.html('<div class="tpb-qv-product-image-container"></div>');
                }
            }, 300);
            
            console.log('✅ Modal closed');
        },
        
        // Check if modal is open
        isOpen: function() {
            return $(config.overlaySelector).hasClass('is-open');
        },
        
        // Build iframe URL with query parameter
        buildIframeUrl: function(url) {
            try {
                // Handle relative URLs
                if (url.startsWith('/')) {
                    url = window.location.origin + url;
                } else if (!url.startsWith('http')) {
                    url = window.location.origin + '/' + url;
                }
                
                const urlObj = new URL(url);
                urlObj.searchParams.set(config.qvParam, '1');
                return urlObj.toString();
            } catch (e) {
                console.error('❌ Error building iframe URL:', e, 'URL:', url);
                // Fallback to current page with query parameter
                const fallbackUrl = window.location.origin + window.location.pathname + '?' + config.qvParam + '=1';
                console.log('🔄 Using fallback URL:', fallbackUrl);
                return fallbackUrl;
            }
        },
        
        // Fetch Category Station product image from API
        fetchCategoryStationImage: function($leftPanel) {
            console.log('🔍 Fetching Category Station product image from API...');
            
            // Fetch the 27" Category Station product (ID 476)
            fetch('/wp-json/tpb/v1/qv/products', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({
                    limit: 50
                })
            })
            .then(response => response.json())
            .then(data => {
                console.log('📦 API Response:', data);
                
                if (data.results && data.results.length > 0) {
                    // Find the 27" Category Station product
                    const categoryStationProduct = data.results.find(product => 
                        product.name && product.name.includes('27" Category Station')
                    );
                    
                    if (categoryStationProduct && categoryStationProduct.image_url) {
                        console.log('✅ Found Category Station product:', categoryStationProduct.name);
                        console.log('🖼️ Using image URL:', categoryStationProduct.image_url);
                        
                        $leftPanel.html(`
                            <div class="tpb-qv-product-image-container">
                                <img src="${categoryStationProduct.image_url}" alt="${categoryStationProduct.name}" class="tpb-qv-product-image">
                            </div>
                        `);
                    } else {
                        console.log('❌ Category Station product not found or no image URL');
                        this.showDefaultImage($leftPanel);
                    }
                } else {
                    console.log('❌ No products found in API response');
                    this.showDefaultImage($leftPanel);
                }
            })
            .catch(error => {
                console.error('❌ Error fetching Category Station product:', error);
                this.showDefaultImage($leftPanel);
            });
        },
        
        // Show default image if API fails
        showDefaultImage: function($leftPanel) {
            console.log('🔄 Using fallback Category Station image');
            const fallbackImage = 'http://the-peak-beyond-modal.local/wp-content/uploads/2025/03/24-product-category-hk.jpg';
            $leftPanel.html(`
                <div class="tpb-qv-product-image-container">
                    <img src="${fallbackImage}" alt="Category Station" class="tpb-qv-product-image">
                </div>
            `);
        },
        
        // Fetch product image from the actual product page
        fetchProductImageFromPage: function(url, $leftPanel) {
            console.log('🔍 fetchProductImageFromPage called with URL:', url);
            console.log('Left panel exists:', !!$leftPanel);
            console.log('Left panel element:', $leftPanel);
            
            // Add cache busting headers
            const fetchOptions = {
                cache: 'no-cache',
                headers: {
                    'Cache-Control': 'no-cache, no-store, must-revalidate',
                    'Pragma': 'no-cache',
                    'Expires': '0'
                }
            };
            
            // Fetch the product page HTML
            console.log('🚀 Starting fetch request to:', url);
            fetch(url, fetchOptions)
                .then(response => {
                    console.log('📡 Fetch response received:', response.status, response.statusText);
                    return response.text();
                })
                .then(html => {
                    console.log('📄 Product page HTML fetched, length:', html.length);
                    
                    // Create a temporary DOM element to parse the HTML
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Look for the product image in the gallery
                    let productImage = doc.querySelector('.woocommerce-product-gallery__image img[data-large_image]');
                    
                    if (!productImage) {
                        productImage = doc.querySelector('.woocommerce-product-gallery__image img[data-src]');
                    }
                    
                    if (!productImage) {
                        productImage = doc.querySelector('.woocommerce-product-gallery__image img');
                    }
                    
                    if (!productImage) {
                        productImage = doc.querySelector('.wp-post-image');
                    }
                    
                    if (productImage) {
                        let imageSrc = productImage.getAttribute('data-large_image') || 
                                      productImage.getAttribute('data-src') || 
                                      productImage.getAttribute('src');
                        
                        if (imageSrc) {
                            // Remove size parameters to get full size image
                            imageSrc = imageSrc.replace(/-\d+x\d+\.(jpg|jpeg|png|gif|webp)/i, '.$1');
                            // Add cache busting to image URL
                            imageSrc += '?v=' + Date.now();
                            console.log('✅ Found product image:', imageSrc);
                            
                            $leftPanel.html(`
                                <div class="tpb-qv-product-image-container">
                                    <img src="${imageSrc}" alt="Product Image" style="width: 100%; height: 100%; object-fit: contain; max-width: none; max-height: none;">
                                </div>
                            `);
                        } else {
                            console.log('❌ No image source found');
                            this.showDefaultImage($leftPanel);
                        }
                    } else {
                        console.log('❌ No product image found in page');
                        this.showDefaultImage($leftPanel);
                    }
                })
                .catch(error => {
                    console.error('❌ Error fetching product page:', error);
                    console.error('Error details:', error.message, error.stack);
                    this.showDefaultImage($leftPanel);
                });
        },
        
        // Extract product image from the product URL
        extractProductImage: function(url, $leftPanel) {
            console.log('🖼️ Extracting product image from URL:', url);
            
            // Also check iframe URL for Quick Checkout detection
            const iframe = document.querySelector('#tpb-qv-iframe');
            const iframeUrl = iframe ? iframe.src : '';
            console.log('🖼️ Iframe URL for detection:', iframeUrl);
            
            // Clear any existing image first to prevent flashing
            $leftPanel.html('<div class="tpb-qv-product-image-container"><div style="width: 100%; height: 100%; background: #f5f5f5; display: flex; align-items: center; justify-content: center; color: #666;">Loading...</div></div>');
            
            // For Category Station modal, fetch the actual product image from the product page
            if (url.includes('category-station-configure-now')) {
                console.log('🎯 Category Station modal - fetching product image from product page');
                this.fetchProductImageFromPage(url, $leftPanel);
                return;
            }
            
            // For Flower Station modal, fetch the actual product image from the product page
            if (url.includes('flower-station-configure-now')) {
                console.log('🎯 Flower Station modal - fetching product image from product page');
                this.fetchProductImageFromPage(url, $leftPanel);
                return;
            }
            
        // For Quick Checkout modal, fetch the actual product image from the product page
        const isQuickCheckout = url.includes('quick-checkout-station-configure-now') || 
                              iframeUrl.includes('quick-checkout-station-configure-now') ||
                              url.indexOf('quick-checkout-station-configure-now') !== -1 ||
                              iframeUrl.indexOf('quick-checkout-station-configure-now') !== -1;
        
        console.log('extractProductImage - Quick Checkout detection:', isQuickCheckout);
        
        if (isQuickCheckout) {
            console.log('🎯 Quick Checkout modal - fetching product image from product page');
            this.fetchProductImageFromPage('http://the-peak-beyond-modal.local/product/quick-checkout-station-configure-now/?v=' + Date.now(), $leftPanel);
            return;
        }
            
            // For other modals, try to find product image on current page
            let $productImage = $('.woocommerce-product-gallery__image img[data-large_image], .woocommerce-product-gallery__image img[data-src]').first();
            
            if ($productImage.length === 0) {
                $productImage = $('.woocommerce-product-gallery__image img, .wp-post-image, .product-image img, .single-product-image img').first();
            }
            
            if ($productImage.length > 0) {
                let imageSrc = $productImage.attr('data-large_image') || 
                              $productImage.attr('data-src') || 
                              $productImage.attr('src');
                
                if (imageSrc) {
                    // Remove size parameters to get full size image
                    imageSrc = imageSrc.replace(/-\d+x\d+\.(jpg|jpeg|png|gif|webp)/i, '.$1');
                        // Add cache busting to image URL
                        imageSrc += '?v=' + Date.now();
                    console.log('✅ Found product image:', imageSrc);
                    $leftPanel.html(`
                        <div class="tpb-qv-product-image-container">
                            <img src="${imageSrc}" alt="Product Image" style="width: 100%; height: 100%; object-fit: contain; max-width: none; max-height: none;">
                        </div>
                    `);
                    return;
                }
            }
            
            // Try to get image from the product cards on the current page
            const productCards = $('.woocommerce-loop-product__link img, .product img, .wp-post-image');
            if (productCards.length > 0) {
                let imageSrc = productCards.first().attr('src') || productCards.first().attr('data-src');
                if (imageSrc) {
                    // Remove size parameters to get full size image
                    imageSrc = imageSrc.replace(/-\d+x\d+\.(jpg|jpeg|png|gif|webp)/i, '.$1');
                    console.log('✅ Found product image from cards:', imageSrc);
                    $leftPanel.html(`
                        <div class="tpb-qv-product-image-container">
                            <img src="${imageSrc}" alt="Product Image" style="width: 100%; height: 100%; object-fit: contain; max-width: none; max-height: none;">
                        </div>
                    `);
                    return;
                }
            }
            
            // Fallback: try to load image from the product URL
            this.loadProductImageFromUrl(url, $leftPanel);
        },
        
        // Load product image from URL
        loadProductImageFromUrl: function(url, $leftPanel) {
            console.log('🔄 Loading product image from URL...');
            
            // Create a temporary iframe to extract the image
            const tempIframe = $('<iframe>').attr({
                'src': url,
                'style': 'display: none; width: 1px; height: 1px;'
            });
            
            $('body').append(tempIframe);
            
            tempIframe.on('load', function() {
                try {
                    const iframeDoc = this.contentDocument || this.contentWindow.document;
                    const $iframeContent = $(iframeDoc);
                    
                    const $productImage = $iframeContent.find('.woocommerce-product-gallery__image img, .wp-post-image, .product-image img, .single-product-image img').first();
                    
                    if ($productImage.length > 0) {
                        const imageSrc = $productImage.attr('src') || $productImage.attr('data-src');
                        if (imageSrc) {
                            console.log('✅ Loaded product image from URL:', imageSrc);
                            $leftPanel.html(`
                                <div class="tpb-qv-product-image-container">
                                    <img src="${imageSrc}" alt="Product Image" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                                </div>
                            `);
                        }
                    } else {
                        console.log('⚠️ No product image found, using placeholder');
                        $leftPanel.html(`
                            <div class="tpb-qv-product-image-container">
                                <div style="text-align: center; color: #666; padding: 40px;">
                                    <div style="font-size: 48px; margin-bottom: 20px;">🖼️</div>
                                    <div>Product Image</div>
                                </div>
                            </div>
                        `);
                    }
                } catch (e) {
                    console.log('⚠️ Could not access iframe content, using placeholder');
                    $leftPanel.html(`
                        <div class="tpb-qv-product-image-container">
                            <div style="text-align: center; color: #666; padding: 40px;">
                                <div style="font-size: 48px; margin-bottom: 20px;">🖼️</div>
                                <div>Product Image</div>
                            </div>
                        </div>
                    `);
                }
                
                // Clean up temporary iframe
                tempIframe.remove();
            });
        },
        
        // Handle CPB add to cart from iframe
        handleCpbAddToCart: function(data) {
            console.log('🛒 Processing CPB add to cart:', data);
            
            if (!config.ajaxUrl || !config.nonce) {
                console.error('❌ Missing AJAX configuration');
                return;
            }
            
            $.ajax({
                url: config.ajaxUrl,
                type: 'POST',
                data: {
                    action: 'tpb_qv_cpb_add_to_cart',
                    nonce: config.nonce,
                    product_id: data.product_id,
                    quantity: data.quantity || 1,
                    variation_id: data.variation_id || 0,
                    cpb_data: data.cpb_data || {}
                },
                success: function(response) {
                    console.log('✅ CPB add to cart successful:', response);
                    
                    if (response.success) {
                        // Show success message
                        self.showNotification('Product added to cart!', 'success');
                        
                        // Close modal after short delay
                        setTimeout(() => {
                            self.close();
                        }, 1500);
                        
                        // Trigger cart update event
                        $(document.body).trigger('added_to_cart', [response.data.cart_item_key, 1, data.variation_id, $]);
                    } else {
                        self.showNotification('Failed to add product to cart', 'error');
                    }
                },
                error: function(xhr, status, error) {
                    console.error('❌ CPB add to cart failed:', error);
                    self.showNotification('Error adding product to cart', 'error');
                }
            });
        },
        
        // Show notification
        showNotification: function(message, type = 'info') {
            const notification = $(`
                <div class="tpb-qv-notification tpb-qv-notification-${type}">
                    ${message}
                </div>
            `);
            
            $('.tpb-qv-notification').remove();
            $('body').append(notification);
            
            // Show notification
            setTimeout(() => {
                notification.addClass('show');
            }, 100);
            
            // Hide after 3 seconds
            setTimeout(() => {
                notification.removeClass('show');
                setTimeout(() => {
                    notification.remove();
                }, 300);
            }, 3000);
        },
        
        // Show debug information
        showDebugInfo: function(message, data) {
            if (!config.debug) return;
            
            const debugInfo = `
                <div class="tpb-qv-debug">
                    <strong>${message}</strong><br>
                    ${JSON.stringify(data, null, 2)}
                </div>
            `;
            
            $('.tpb-qv-debug').remove();
            $('body').append(debugInfo);
            
            // Remove after 3 seconds
            setTimeout(() => {
                $('.tpb-qv-debug').fadeOut();
            }, 3000);
        }
    };
    
    // Initialize when document is ready
    $(document).ready(function() {
        Modal.init();
    });
    
    // Also try after a delay for dynamic content
    setTimeout(function() {
        Modal.detectButtons();
    }, 2000);
    
    // Expose Modal to global scope for debugging
    window.TPBModal = Modal;
    
    // Global debug function for testing
    window.TPB_DEBUG_IMAGE = function() {
        console.log('Manual image test triggered');
        const leftPanel = document.querySelector('.tpb-qv-left-panel');
        if (leftPanel && window.TPBModal) {
            window.TPBModal.fetchProductImageFromPage(
                'http://the-peak-beyond-modal.local/product/quick-checkout-station-configure-now/?v=' + Date.now(),
                $(leftPanel)
            );
        } else {
            console.error('Left panel or modal object not found');
            console.log('Left panel exists:', !!leftPanel);
            console.log('Modal object exists:', !!window.TPBModal);
        }
    };
    
    console.log('🎉 TPB QuickView Modal Plugin loaded');
    
})(jQuery);