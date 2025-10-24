/**
 * TPB Store Modal - Cart Integration
 * Universal cart update functionality for all modals
 */

(function() {
    'use strict';
    
    // Ensure cart elements have proper z-index above modal overlay
    function ensureCartZIndex() {
        // Cart widgets
        const cartWidgets = document.querySelectorAll('.woocommerce-cart-widget, .elementor-widget-woocommerce-cart, .floating-cart, .cart-widget, .woocommerce-mini-cart, .elementor-cart-widget');
        cartWidgets.forEach(function(widget) {
            widget.style.zIndex = '1000000';
            widget.style.position = 'relative';
        });
        
        // Cart count badges
        const cartCounts = document.querySelectorAll('.cart-count, .cart-counter, .woocommerce-cart-count, .elementor-cart-count, .woocommerce-cart-count-badge');
        cartCounts.forEach(function(count) {
            count.style.zIndex = '1000001';
            count.style.position = 'relative';
        });
        
        // Toast notifications
        const toasts = document.querySelectorAll('.toast, .tpb-toast, .notification, .woocommerce-message, .woocommerce-info, .woocommerce-error, .woocommerce-notice');
        toasts.forEach(function(toast) {
            toast.style.zIndex = '1000002';
            toast.style.position = 'fixed';
        });
        
        // Cart overlays
        const cartOverlays = document.querySelectorAll('.cart-overlay, .cart-popup, .cart-drawer, .woocommerce-cart-overlay');
        cartOverlays.forEach(function(overlay) {
            overlay.style.zIndex = '1000003';
        });
    }
    
    // Global cart update function
    window.tpbUpdateCart = function() {
        console.log('🛒 Updating cart display...');
        
        // Ensure cart elements have proper z-index
        ensureCartZIndex();
        
        // Method 1: Trigger WooCommerce fragment refresh
        if (typeof jQuery !== 'undefined') {
            // Trigger the standard WooCommerce cart update event
            jQuery(document.body).trigger('wc_fragment_refresh');
            
            // Also trigger specific cart widget updates
            jQuery('.woocommerce-cart-widget, .cart-widget, .elementor-widget-woocommerce-cart').trigger('wc_fragment_refresh');
            
            // Update cart count displays
            jQuery('.cart-count, .cart-counter, .woocommerce-cart-count, .elementor-cart-count').trigger('wc_fragment_refresh');
        }
        
        // Method 2: Direct AJAX cart refresh
        if (typeof fetch !== 'undefined') {
            fetch('/wp-admin/admin-ajax.php?action=woocommerce_get_refreshed_fragments', {
                credentials: 'same-origin'
            })
            .then(function(response) {
                return response.json();
            })
            .then(function(data) {
                if (data && data.fragments) {
                    // Update cart fragments
                    Object.keys(data.fragments).forEach(function(key) {
                        const element = document.querySelector(key);
                        if (element) {
                            element.innerHTML = data.fragments[key];
                        }
                    });
                    
                    // Update cart hash
                    if (data.cart_hash) {
                        const cartHashInput = document.querySelector('input[name="woocommerce-cart-hash"]');
                        if (cartHashInput) {
                            cartHashInput.value = data.cart_hash;
                        }
                    }
                }
            })
            .catch(function(error) {
                console.warn('Cart fragment refresh failed:', error);
            });
        }
        
        // Method 3: Force page reload of cart widget (fallback)
        setTimeout(function() {
            const cartWidgets = document.querySelectorAll('.elementor-widget-woocommerce-cart, .woocommerce-cart-widget');
            cartWidgets.forEach(function(widget) {
                if (widget.querySelector('iframe')) {
                    // Reload iframe-based cart widgets
                    const iframe = widget.querySelector('iframe');
                    if (iframe) {
                        iframe.src = iframe.src;
                    }
                }
            });
        }, 500);
    };
    
    // Listen for cart updates from modals
    document.addEventListener('DOMContentLoaded', function() {
        // Ensure cart z-index on page load
        ensureCartZIndex();
        
        // Listen for custom cart update events
        document.addEventListener('tpb_cart_updated', function() {
            window.tpbUpdateCart();
        });
        
        // Listen for WooCommerce cart events
        if (typeof jQuery !== 'undefined') {
            jQuery(document.body).on('added_to_cart', function() {
                console.log('🛒 WooCommerce added_to_cart event detected');
                setTimeout(window.tpbUpdateCart, 100);
            });
            
            jQuery(document.body).on('removed_from_cart', function() {
                console.log('🛒 WooCommerce removed_from_cart event detected');
                setTimeout(window.tpbUpdateCart, 100);
            });
        }
    });
    
    console.log('🛒 TPB Cart Integration loaded');
})();
