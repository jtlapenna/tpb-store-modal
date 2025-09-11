/**
 * TPB Quick View Iframe Handler
 * Handles CPB interactions and communication with parent window
 */
(function(window, document) {
    'use strict';
    
    let config = {};
    let currentSelections = {};
    let currentPath = 'custom'; // 'custom' or 'predesigned'
    
    // Listen for configuration from parent
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'tpb-qv-config') {
            config = event.data.config;
            initializeCPB();
        }
    });
    
    function initializeCPB() {
        // Set up CPB component listeners
        setupCPBListeners();
        
        // Set up auto-resize
        setupAutoResize();
        
        // Set up SKU swapping logic
        setupSKUSwapping();
        
        console.log('[TPB-QV] CPB initialized with config:', config);
    }
    
    function setupCPBListeners() {
        // Listen for CPB component changes
        document.addEventListener('change', function(event) {
            const target = event.target;
            
            // Check if it's a CPB component
            if (target.closest('.cpb-component') || target.closest('.woocommerce-variation')) {
                handleCPBSelection(target);
            }
        });
        
        // Listen for radio button changes
        document.addEventListener('click', function(event) {
            const target = event.target;
            if (target.type === 'radio' && target.closest('.cpb-component')) {
                handleCPBSelection(target);
            }
        });
    }
    
    function handleCPBSelection(element) {
        const component = element.closest('.cpb-component');
        if (!component) return;
        
        const componentName = component.dataset.component || 'unknown';
        const value = element.value || element.textContent || 'unknown';
        
        currentSelections[componentName] = value;
        
        // Send selection to parent
        sendToParent({
            action: 'cpb-selection',
            selection: {
                component: componentName,
                value: value,
                allSelections: currentSelections
            }
        });
        
        // Handle path changes
        if (componentName === 'build-strategy') {
            currentPath = value.toLowerCase().includes('custom') ? 'custom' : 'predesigned';
            handlePathChange();
        }
        
        // Handle SKU swapping
        handleSKUSwapping();
    }
    
    function handlePathChange() {
        const mountComponent = document.querySelector('[data-component="mount-type"]');
        const finishComponent = document.querySelector('[data-component="finish-material"]');
        
        if (currentPath === 'custom') {
            // Hide mount and finish components for custom build
            if (mountComponent) mountComponent.style.display = 'none';
            if (finishComponent) finishComponent.style.display = 'none';
        } else {
            // Show mount and finish components for pre-designed
            if (mountComponent) mountComponent.style.display = 'block';
            if (finishComponent) finishComponent.style.display = 'block';
        }
    }
    
    function handleSKUSwapping() {
        if (currentPath === 'custom') {
            // Keep hardware kit SKU for custom build
            const hardwareKit = currentSelections['sku-count'];
            if (hardwareKit) {
                sendToParent({
                    action: 'sku-swap',
                    sku: hardwareKit,
                    path: 'custom'
                });
            }
        } else {
            // Swap to complete bundle SKU for pre-designed
            const skuCount = currentSelections['sku-count'];
            const mountType = currentSelections['mount-type'];
            const finish = currentSelections['finish-material'];
            
            if (skuCount && mountType && finish) {
                const bundleSKU = generateBundleSKU(skuCount, mountType, finish);
                sendToParent({
                    action: 'sku-swap',
                    sku: bundleSKU,
                    path: 'predesigned'
                });
            }
        }
    }
    
    function generateBundleSKU(skuCount, mountType, finish) {
        // Generate bundle SKU based on selections
        // This should match your actual SKU naming convention
        const skuCountNum = skuCount.replace(/\D/g, '');
        const mountCode = mountType.toLowerCase().replace(/\s+/g, '-');
        const finishCode = finish.toLowerCase().replace(/\s+/g, '-');
        
        return `BUNDLE-${skuCountNum}-SKU-${mountCode}-${finishCode}-COMPLETE`;
    }
    
    function setupAutoResize() {
        // Auto-resize iframe based on content
        const resizeObserver = new ResizeObserver(function(entries) {
            const height = document.body.scrollHeight;
            sendToParent({
                action: 'resize',
                height: height
            });
        });
        
        resizeObserver.observe(document.body);
        
        // Initial resize
        setTimeout(function() {
            const height = document.body.scrollHeight;
            sendToParent({
                action: 'resize',
                height: height
            });
        }, 100);
    }
    
    function setupSKUSwapping() {
        // Set up SKU swapping for add to cart
        document.addEventListener('submit', function(event) {
            const form = event.target;
            if (form.closest('.cart') || form.closest('.woocommerce-cart-form')) {
                handleAddToCart(form);
            }
        });
    }
    
    function handleAddToCart(form) {
        const productId = form.querySelector('[name="add-to-cart"]')?.value;
        const currentSKU = getCurrentSKU();
        
        sendToParent({
            action: 'add-to-cart',
            productId: productId,
            sku: currentSKU,
            path: currentPath
        });
    }
    
    function getCurrentSKU() {
        if (currentPath === 'custom') {
            return currentSelections['sku-count'];
        } else {
            return generateBundleSKU(
                currentSelections['sku-count'],
                currentSelections['mount-type'],
                currentSelections['finish-material']
            );
        }
    }
    
    function sendToParent(message) {
        if (window.parent && window.parent !== window) {
            window.parent.postMessage({
                type: 'tpb-qv',
                ...message
            }, window.location.origin);
        }
    }
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCPB);
    } else {
        initializeCPB();
    }
    
})(window, document);
