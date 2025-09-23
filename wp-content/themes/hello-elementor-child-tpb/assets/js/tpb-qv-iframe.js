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
        console.log('[TPB-QV] Starting CPB initialization...');
        
        // Set up CPB component listeners first
        setupCPBListeners();
        
        // Set up auto-resize
        setupAutoResize();
        
        // Set up SKU swapping logic
        setupSKUSwapping();
        
        // Establish initial state (after CPB renders) and observe for changes
        observeCPBAndInitialize();
        
        console.log('[TPB-QV] CPB initialization setup complete');
    }
    
    // Helpers to find CPB components in a resilient way
    function getAllComponents() {
        // Cache the result to avoid repeated DOM queries
        if (window._tpbCachedComponents) {
            return window._tpbCachedComponents;
        }
        
        // Prefer Addify CPB structure if present
        let comps = Array.from(document.querySelectorAll('.af_cp_all_components_content .single_component'));
        if (comps.length) {
            window._tpbCachedComponents = comps;
            return comps;
        }
        // Addify toggle/vertical templates
        comps = Array.from(document.querySelectorAll('.af_cp_vertical_template .single_component, .af_cp_toggle_template .single_component'));
        if (comps.length) {
            window._tpbCachedComponents = comps;
            return comps;
        }
        // Generic/legacy fallback (rare)
        comps = Array.from(document.querySelectorAll('.cpb-component'));
        if (comps.length) {
            window._tpbCachedComponents = comps;
            return comps;
        }
        comps = Array.from(document.querySelectorAll('[data-component], .component'));
        window._tpbCachedComponents = comps;
        return comps;
    }

    function matchComponentByText(components, pattern) {
        const regex = new RegExp(pattern, 'i');
        return components.find(c => regex.test(c.textContent || '')) || null;
    }

    function clearSelections(root) {
        if (!root) return;
        root.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(el => {
            el.checked = false;
        });
        root.querySelectorAll('select').forEach(sel => {
            sel.selectedIndex = -1;
            // also clear any value that plugins may have set
            sel.value = '';
        });
        // Addify: remove any pre-rendered selected product blocks
        root.querySelectorAll('.af-cp-selected-product').forEach(el => el.remove());
        // Addify: clear hidden selected inputs if present
        root.querySelectorAll('input[type="hidden"]').forEach(h => {
            if (/af_cp_selected|selected_product/i.test(h.name || '')) h.value = '';
        });
    }

    function hideComponent(comp) {
        if (!comp) return;
        comp.classList.add('tpb-collapsed');
        comp.style.maxHeight = '120px';
        comp.style.overflow = 'hidden';
        comp.style.opacity = '0.7';
    }

    function showComponent(comp) {
        if (!comp) return;
        comp.classList.remove('tpb-collapsed');
        comp.style.maxHeight = 'none';
        comp.style.overflow = 'visible';
        comp.style.opacity = '1';
    }

    function observeCPBAndInitialize() {
        let initAttempts = 0;
        const maxAttempts = 5;
        
        const tryInit = () => {
            initAttempts++;
            const components = getAllComponents();
            console.log('🔍 Checking for components (attempt', initAttempts, '):', components.length);
            
            if (components.length) {
                console.log('✅ Components found, establishing initial flow state...');
                establishInitialFlowState();
                return true;
            }
            
            if (initAttempts < maxAttempts) {
                console.log('❌ No components found yet, retrying...');
                return false;
            }
            
            console.log('⚠️ Max attempts reached, giving up');
            return false;
        };

        // Attempt immediately
        if (tryInit()) return;

        // Retry with increasing delays
        const retryDelays = [100, 300, 600, 1000];
        retryDelays.forEach((delay, index) => {
            setTimeout(() => {
                if (tryInit()) return;
            }, delay);
        });

        // Only observe specific Addify containers, not the entire body
        const container = document.querySelector('.af_cp_all_components_content') || document.querySelector('.af_cp_vertical_template');
        if (container) {
            console.log('👀 Observing Addify container:', container);
            const observer = new MutationObserver((mutations) => {
                // Only process if we haven't initialized yet
                if (initAttempts >= maxAttempts) return;
                
                const hasRelevantChanges = mutations.some(mutation => 
                    mutation.type === 'childList' && 
                    Array.from(mutation.addedNodes).some(node => 
                        node.nodeType === 1 && 
                        (node.classList?.contains('single_component') || 
                         node.querySelector?.('.single_component'))
                    )
                );
                
                if (hasRelevantChanges) {
                    console.log('🔄 Relevant mutation detected, retrying init...');
                    tryInit();
                }
            });
            observer.observe(container, { childList: true, subtree: true });
        }
    }

    function establishInitialFlowState() {
        const components = getAllComponents();
        if (!components.length) return;

        console.log('🎯 Establishing initial flow state with', components.length, 'components');
        
        // Clear ALL selections first - no pre-selected options
        components.forEach(comp => {
            clearSelections(comp);
        });
        
        // Only show first component (SKU count), collapse all others
        components.forEach((comp, index) => {
            const title = comp.querySelector('h4.title, h4, .title')?.textContent?.trim() || `Component ${index + 1}`;
            if (index === 0) {
                console.log('✅ Showing first component:', title);
                showComponent(comp);
            } else {
                console.log('📦 Collapsing component:', title);
                hideComponent(comp);
                // Make collapsed components clickable to expand
                comp.addEventListener('click', function() {
                    if (comp.classList.contains('tpb-collapsed')) {
                        console.log('🖱️ Clicked to expand:', title);
                        showComponent(comp);
                    }
                });
            }
        });
    }

    function setupCPBListeners() {
        // More efficient event delegation
        document.addEventListener('change', function(event) {
            const target = event.target;
            
            // Only handle form elements in CPB components
            if (target.matches('input[type="radio"], input[type="checkbox"], select') && 
                target.closest('.af_cp_all_components_content, .cpb-component')) {
                handleCPBSelection(target);
            }
        });
        
        // Handle clicks on product cards/selections
        document.addEventListener('click', function(event) {
            const target = event.target;
            if (target.closest('.af-cp-selected-product, .product-card, .variation') && 
                target.closest('.af_cp_all_components_content, .cpb-component')) {
                handleCPBSelection(target);
            }
        });
    }
    
    function handleCPBSelection(element) {
        console.log('🎯 CPB Selection detected:', element);
        
        // Support Addify markup
        const component = element.closest('.cpb-component') || element.closest('.single_component');
        if (!component) {
            console.log('❌ No component found for element');
            return;
        }
        
        const titleEl = component.querySelector('h4.title, h4, .title');
        const componentName = (component.dataset.component || (titleEl ? (titleEl.textContent || '').trim().toLowerCase() : 'unknown'));
        const value = element.value || element.textContent || (element.options && element.options[element.selectedIndex]?.text) || 'unknown';
        
        console.log('📝 Selection:', { componentName, value });
        
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
        
        // Progressive disclosure: reveal next components as prerequisites are met
        console.log('🔄 Triggering progressive reveal...');
        progressiveReveal(component);

        // Handle path changes
        if (/build\s*strategy/i.test(componentName)) {
            currentPath = (String(value).toLowerCase().includes('custom')) ? 'custom' : 'predesigned';
            console.log('🛤️ Path changed to:', currentPath);
            handlePathChange();
        }
        
        // Handle SKU swapping
        handleSKUSwapping();
    }

    function progressiveReveal(changedComponent) {
        console.log('🔄 Progressive reveal triggered for component:', changedComponent);
        
        const components = getAllComponents();
        if (!components.length) return;

        // Fast path: if first component (SKU count) has a selection, show second component (Build Strategy)
        if (components[0] && components[0].contains(changedComponent)) {
            const hasSelection = components[0].querySelector('input[type="radio"]:checked, select option:checked, .af-cp-selected-product');
            if (hasSelection && components[1]) {
                console.log('✅ SKU count selected, showing Build Strategy');
                showComponent(components[1]);
                return;
            }
        }

        // If Build Strategy component has a selection, show third component (Bundle)
        if (components[1] && components[1].contains(changedComponent)) {
            const hasSelection = components[1].querySelector('input[type="radio"]:checked, select option:checked, .af-cp-selected-product');
            if (hasSelection && components[2]) {
                console.log('✅ Build Strategy selected, showing Bundle component');
                showComponent(components[2]);
                return;
            }
        }
    }
    
    function handlePathChange() {
        // Backward-compatible show/hide for legacy markup if present
        const mountComponent = document.querySelector('[data-component="mount-type"]');
        const finishComponent = document.querySelector('[data-component="finish-material"]');
        if (currentPath === 'custom') {
            hideComponent(mountComponent);
            hideComponent(finishComponent);
        } else {
            showComponent(mountComponent);
            showComponent(finishComponent);
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
    
    // Initialize when DOM is ready with timeout
    const initWithTimeout = () => {
        initializeCPB();
        
        // Fallback timeout - if nothing happens in 10 seconds, give up
        setTimeout(() => {
            if (!window._tpbCachedComponents || window._tpbCachedComponents.length === 0) {
                console.log('⚠️ CPB initialization timeout - components not found after 10 seconds');
            }
        }, 10000);
    };
    
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initWithTimeout);
    } else {
        initWithTimeout();
    }
    
})(window, document);
