/**
 * TPB Quick Checkout Native Stepper
 * Handles the Quick Checkout modal configuration
 */

(function($) {
    'use strict';

    // Quick Checkout Stepper Class
    class TPBQuickCheckoutStepper {
        constructor() {
            this.currentStep = 1;
            this.selectedConfiguration = null;
            this.selectedProducts = [];
            this.basePrice = 0;
            
            this.init();
        }

        init() {
            console.log('🚀 Quick Checkout Stepper initializing...');
            
            // Check if we're in iframe mode
            if (window.location.search.indexOf('tpb_qv_iframe=1') !== -1) {
                console.log('Quick Checkout Stepper - in iframe mode, proceeding with initialization');
                this.build();
            } else {
                console.log('Quick Checkout Stepper - not in iframe mode, skipping initialization');
            }
        }

        build() {
            console.log('Quick Checkout Stepper - build() function called');
            
            // Wait for DOM to be ready
            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', () => this.mount());
            } else {
                this.mount();
            }
        }

        mount() {
            console.log('Quick Checkout Stepper - mount() function called - FORCE CSS UPDATE');
            
            // Force CSS update
            this.injectCSS();
            
            // Build the stepper interface
            this.buildStepper();
            
            // Bind events
            this.bindEvents();
            
            console.log('Quick Checkout Stepper loading...');
        }

        injectCSS() {
            const css = `
                .tpb-qv-native-quickcheckout {
                    padding: 20px;
                    max-width: 100%;
                    margin: 0 auto;
                }
                
                .tpb-qv-native-quickcheckout .tpb-qv-native {
                    margin-top: -60px !important;
                }
                
                .tpb-qv-native {
                    margin-top: -60px !important;
                }
                
                .tpb-step {
                    margin-bottom: 30px;
                    opacity: 0;
                    transform: translateY(20px);
                    transition: opacity 0.4s ease-out, transform 0.4s ease-out;
                }
                
                .tpb-step.active {
                    opacity: 1;
                    transform: translateY(0);
                }
                
                .tpb-step.completed {
                    opacity: 0.7;
                }
                
                .tpb-title {
                    font-size: 24px;
                    font-weight: 600;
                    color: #2c3e50;
                    margin-bottom: 10px;
                }
                
                .tpb-hint {
                    font-size: 16px;
                    color: #6b7280;
                    margin-bottom: 20px;
                    line-height: 1.5;
                }
                
                .tpb-configuration-options {
                    display: grid;
                    grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
                    gap: 20px;
                    margin-bottom: 20px;
                }
                
                .tpb-configuration-card {
                    border: 2px solid #e5e7eb;
                    border-radius: 12px;
                    padding: 20px;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    background: #fff;
                }
                
                .tpb-configuration-card:hover {
                    border-color: #4faf50;
                    transform: translateY(-2px);
                    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
                }
                
                .tpb-configuration-card.selected {
                    border-color: #4faf50;
                    background: #f0f9f0;
                }
                
                .tpb-configuration-title {
                    font-size: 18px;
                    font-weight: 600;
                    color: #2c3e50;
                    margin-bottom: 10px;
                }
                
                .tpb-configuration-description {
                    font-size: 14px;
                    color: #6b7280;
                    margin-bottom: 15px;
                    line-height: 1.4;
                }
                
                .tpb-configuration-price {
                    font-size: 16px;
                    font-weight: 600;
                    color: #4faf50;
                }
                
                .tpb-products-grid {
                    display: grid;
                    grid-template-columns: repeat(2, 1fr);
                    gap: 20px;
                    margin-top: 20px;
                    max-width: 100%;
                }
                
                @media (max-width: 768px) {
                    .tpb-products-grid {
                        grid-template-columns: 1fr;
                    }
                }
                
                .tpb-card {
                    border: 1px solid #e3e6ea;
                    border-radius: 12px;
                    padding: 16px;
                    background: #fff;
                    transition: box-shadow 0.2s, transform 0.2s;
                    text-align: left;
                    max-width: 100%;
                    box-sizing: border-box;
                }
                
                .tpb-card:hover {
                    box-shadow: 0 4px 12px rgba(0,0,0,0.1);
                    transform: translateY(-2px);
                }
                
                .tpb-card.selected {
                    border-color: #4faf50;
                    background: #f0f9f0;
                }
                
                .tpb-card-image {
                    position: relative;
                    padding-bottom: 75%;
                    overflow: hidden;
                    border-radius: 8px;
                    margin-bottom: 12px;
                }
                
                .tpb-card-image img {
                    position: absolute;
                    top: 0;
                    left: 0;
                    width: 100%;
                    height: 100%;
                    object-fit: cover;
                }
                
                .tpb-card-title {
                    font-weight: 600;
                    font-size: 16px;
                    margin-bottom: 8px;
                    min-height: 40px;
                    line-height: 1.3;
                    text-align: center;
                }
                
                .tpb-card-description {
                    font-size: 14px;
                    color: #6b7280;
                    margin-bottom: 10px;
                    line-height: 1.4;
                    text-align: center;
                }
                
                .tpb-card-price {
                    color: rgb(79 176 137);
                    font-weight: 600;
                    font-size: 16px;
                    padding-top: 10px;
                    padding-bottom: 10px;
                    margin-bottom: 12px;
                    text-align: center;
                }
                
                .tpb-actions {
                    display: flex;
                    gap: 8px;
                    margin-top: 12px;
                    align-items: center;
                    justify-content: center;
                    flex-wrap: wrap;
                }
                
                .tpb-btn {
                    padding: 8px 16px;
                    border: none;
                    border-radius: 6px;
                    font-size: 14px;
                    font-weight: 500;
                    cursor: pointer;
                    transition: all 0.3s ease;
                    flex: 1;
                }
                
                .tpb-btn-primary {
                    background: #4faf50;
                    color: white;
                }
                
                .tpb-btn-primary:hover {
                    background: #45a049;
                }
                
                .tpb-btn-secondary {
                    background: #6b7280;
                    color: white;
                }
                
                .tpb-btn-secondary:hover {
                    background: #5a6268;
                }
                
                .tpb-empty-state {
                    text-align: center;
                    padding: 40px 20px;
                    color: #6b7280;
                }
                
                .tpb-empty-state h3 {
                    font-size: 18px;
                    margin-bottom: 10px;
                }
                
                .tpb-empty-state p {
                    font-size: 14px;
                }
            `;
            
            const style = document.createElement('style');
            style.textContent = css;
            document.head.appendChild(style);
        }

        buildStepper() {
            const container = document.querySelector('.tpb-qv-native');
            if (!container) {
                console.error('Quick Checkout Stepper - Container not found');
                return;
            }

            container.innerHTML = `
                <div class="tpb-qv-native-quickcheckout">
                    <!-- Simple Product Selection -->
                    <div class="tpb-step active">
                        <div class="tpb-products-grid" id="tpb-products-grid">
                            <!-- Products will be loaded here -->
                        </div>
                    </div>
                </div>
            `;
            
            // Load products immediately
            this.loadProducts();
            
            // Apply Quick Checkout specific styling
            this.applyQuickCheckoutStyling();
        }

        bindEvents() {
            // Product selection
            $(document).on('click', '.tpb-card', (e) => {
                const $card = $(e.currentTarget);
                const productId = $card.data('product-id');
                
                if ($card.hasClass('selected')) {
                    $card.removeClass('selected');
                    this.selectedProducts = this.selectedProducts.filter(id => id !== productId);
                } else {
                    $card.addClass('selected');
                    this.selectedProducts.push(productId);
                }
            });
            
            // Add to cart/quote buttons
            $(document).on('click', '.tpb-btn-primary', (e) => {
                e.preventDefault();
                this.addToCart();
            });
            
            $(document).on('click', '.tpb-btn-secondary', (e) => {
                e.preventDefault();
                this.addToQuote();
            });
        }

        nextStep() {
            if (this.currentStep < 3) {
                // Hide current step
                $(`.tpb-step[data-step="${this.currentStep}"]`).removeClass('active').addClass('completed');
                
                // Show next step
                this.currentStep++;
                $(`.tpb-step[data-step="${this.currentStep}"]`).addClass('active');
                
                // Load products if moving to step 2
                if (this.currentStep === 2) {
                    this.loadProducts();
                }
                
                // Update summary if moving to step 3
                if (this.currentStep === 3) {
                    this.updateSummary();
                }
            }
        }

        updateBasePrice() {
            const prices = {
                'basic': 1200,
                'premium': 2500,
                'enterprise': 4000
            };
            
            this.basePrice = prices[this.selectedConfiguration] || 0;
            
            // Update base price display
            const $basePrice = $('#tpb-qv-base-price');
            if ($basePrice.length) {
                $basePrice.html(`Base Price: $${this.basePrice.toLocaleString()}<br>Additional products and customizations will be added to this base price.`);
            }
        }

        loadProducts() {
            // Load the two specific Quick Checkout products
            const products = [
                {
                    id: '27quick-checkout-station',
                    title: '27″Quick Checkout Station',
                    price: 1040,
                    image: 'http://the-peak-beyond-modal.local/wp-content/uploads/2025/04/27-qco-hk.jpg',
                    url: 'http://the-peak-beyond-modal.local/product/27quick-checkout-station/'
                },
                {
                    id: '22quick-checkout-station',
                    title: '22″Quick Checkout Station',
                    price: 820,
                    image: 'http://the-peak-beyond-modal.local/wp-content/uploads/2025/04/22-qco-hk.jpg',
                    url: 'http://the-peak-beyond-modal.local/product/22quick-checkout-station/'
                }
            ];
            
            // Fetch product descriptions from WordPress
            this.fetchProductDescriptions(products).then(productsWithDescriptions => {
                const $grid = $('#tpb-products-grid');
                $grid.html(productsWithDescriptions.map(product => `
                    <div class="tpb-card" data-product-id="${product.id}">
                        <div class="tpb-card-image">
                            <img src="${product.image}" alt="${product.title}" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB3aWR0aD0iMjAwIiBoZWlnaHQ9IjE1MCIgdmlld0JveD0iMCAwIDIwMCAxNTAiIGZpbGw9Im5vbmUiIHhtbG5zPSJodHRwOi8vd3d3LnczLm9yZy8yMDAwL3N2ZyI+CjxyZWN0IHdpZHRoPSIyMDAiIGhlaWdodD0iMTUwIiBmaWxsPSIjRjNGNEY2Ii8+Cjx0ZXh0IHg9IjEwMCIgeT0iNzUiIGZvbnQtZmFtaWx5PSJBcmlhbCIgZm9udC1zaXplPSIxNCIgZmlsbD0iIzZCNzI4MCIgdGV4dC1hbmNob3I9Im1pZGRsZSI+UHJvZHVjdCBJbWFnZTwvdGV4dD4KPC9zdmc+'">
                        </div>
                        <h3 class="tpb-card-title">${product.title}</h3>
                        <p class="tpb-card-description">${product.description}</p>
                        <div class="tpb-card-price">$${product.price.toLocaleString()}</div>
                        <div class="tpb-actions">
                            <button class="tpb-btn tpb-btn-primary" onclick="addToCart('${product.id}')">Add to Cart</button>
                            <button class="tpb-btn tpb-btn-secondary" onclick="addToQuote('${product.id}')">Add to Quote</button>
                        </div>
                    </div>
                `).join(''));
            });
        }
        
        applyQuickCheckoutStyling() {
            // Ensure Quick Checkout specific styling is applied
            const nativeContainer = document.querySelector('.tpb-qv-native');
            if (nativeContainer) {
                console.log('Applying Quick Checkout specific styling');
                nativeContainer.style.marginTop = '-60px';
            }
        }
        
        async fetchProductDescriptions(products) {
            const productsWithDescriptions = [];
            
            for (const product of products) {
                try {
                    const response = await fetch(product.url);
                    const html = await response.text();
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    
                    // Try to find product description
                    let description = 'Quick checkout station for your dispensary needs.';
                    
                    // Look for product description in various places
                    const descriptionElement = doc.querySelector('.woocommerce-product-details__short-description p, .product-short-description p, .woocommerce-Tabs-panel--description p, .product-description p');
                    if (descriptionElement) {
                        description = descriptionElement.textContent.trim();
                    } else {
                        // Fallback to meta description or title
                        const metaDescription = doc.querySelector('meta[name="description"]');
                        if (metaDescription) {
                            description = metaDescription.getAttribute('content');
                        }
                    }
                    
                    productsWithDescriptions.push({
                        ...product,
                        description: description
                    });
                } catch (error) {
                    console.error('Error fetching product description for', product.id, error);
                    productsWithDescriptions.push({
                        ...product,
                        description: 'Quick checkout station for your dispensary needs.'
                    });
                }
            }
            
            return productsWithDescriptions;
        }

        updateSummary() {
            const $summary = $('#tpb-summary-content');
            if (!$summary.length) return;
            
            let total = this.basePrice;
            let summaryHtml = `
                <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="margin: 0 0 15px 0; color: #2c3e50;">Configuration: ${this.selectedConfiguration.charAt(0).toUpperCase() + this.selectedConfiguration.slice(1)} Checkout</h3>
                    <p style="margin: 0; color: #6b7280;">Base price: $${this.basePrice.toLocaleString()}</p>
                </div>
            `;
            
            if (this.selectedProducts.length > 0) {
                summaryHtml += `
                    <div style="background: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                        <h3 style="margin: 0 0 15px 0; color: #2c3e50;">Selected Products</h3>
                        <ul style="margin: 0; padding-left: 20px;">
                `;
                
                // Mock product details - in real implementation, fetch from API
                const productDetails = {
                    'nfc-reader': { title: 'NFC Reader', price: 63 },
                    'receipt-printer': { title: 'Receipt Printer', price: 510 },
                    'mini-pc': { title: 'Mini PC', price: 285 }
                };
                
                this.selectedProducts.forEach(productId => {
                    const product = productDetails[productId];
                    if (product) {
                        total += product.price;
                        summaryHtml += `<li style="margin-bottom: 5px;">${product.title} - $${product.price}</li>`;
                    }
                });
                
                summaryHtml += `
                        </ul>
                    </div>
                `;
            }
            
            summaryHtml += `
                <div style="background: #e8f5e8; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
                    <h3 style="margin: 0 0 10px 0; color: #2c3e50;">Total Price: $${total.toLocaleString()}</h3>
                    <p style="margin: 0; color: #6b7280;">Includes hardware, software, and setup</p>
                </div>
                
                <div class="tpb-card-actions">
                    <button class="tpb-btn tpb-btn-primary">Add to Cart</button>
                    <button class="tpb-btn tpb-btn-secondary">Add to Quote</button>
                </div>
            `;
            
            $summary.html(summaryHtml);
        }

        addToCart() {
            console.log('Adding to cart:', {
                configuration: this.selectedConfiguration,
                products: this.selectedProducts,
                basePrice: this.basePrice
            });
            
            // In real implementation, this would make an API call
            alert('Added to cart! (This is a demo)');
        }

        addToQuote() {
            console.log('Adding to quote:', {
                configuration: this.selectedConfiguration,
                products: this.selectedProducts,
                basePrice: this.basePrice
            });
            
            // In real implementation, this would make an API call
            alert('Quote request sent! (This is a demo)');
        }
    }

    // Build function for iframe mode
    function build() {
        var params = new URLSearchParams(location.search);
        if (!(params.get('tpb_qv_iframe') === '1' || params.get('tpb_qv') === '1')) {
            return;
        }
        
        console.log('Loading Quick Checkout stepper');
        new TPBQuickCheckoutStepper();
    }

    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', build);
    } else {
        build();
    }

})(jQuery);
