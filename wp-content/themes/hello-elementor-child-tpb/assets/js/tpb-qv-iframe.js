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
        // Establish initial state: only first component active; clear defaults elsewhere
        establishInitialFlowState();
        
        // Set up CPB component listeners
        setupCPBListeners();
        
        // Set up auto-resize
        setupAutoResize();
        
        // Set up SKU swapping logic
        setupSKUSwapping();
        
        console.log('[TPB-QV] CPB initialized with config:', config);
    }
    
    // Helpers to find CPB components in a resilient way
    function getAllComponents() {
        // Prefer Addify CPB structure if present
        let comps = Array.from(document.querySelectorAll('.af_cp_all_components_content .single_component'));
        if (comps.length) return comps;
        // Addify toggle/vertical templates
        comps = Array.from(document.querySelectorAll('.af_cp_vertical_template .single_component, .af_cp_toggle_template .single_component'));
        if (comps.length) return comps;
        // Generic/legacy fallback (rare)
        comps = Array.from(document.querySelectorAll('.cpb-component'));
        if (comps.length) return comps;
        return Array.from(document.querySelectorAll('[data-component], .component'));
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
        comp.style.display = 'none';
    }

    function showComponent(comp) {
        if (!comp) return;
        comp.style.display = '';
    }

    function establishInitialFlowState() {
        const components = getAllComponents();
        if (!components.length) return;

        // Assume first component is SKU count (base). Keep it visible; others collapsed.
        components.forEach((comp, index) => {
            if (index === 0) {
                showComponent(comp);
            } else {
                clearSelections(comp);
                hideComponent(comp);
            }
        });

        // Attempt to find Build Strategy and ensure it has no default selected
        const buildComp = matchComponentByText(components, '(build\s*strategy|pre-?designed|custom build)');
        clearSelections(buildComp);
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
        
        // Progressive disclosure: reveal next components as prerequisites are met
        progressiveReveal(component);

        // Handle path changes
        if (componentName === 'build-strategy') {
            currentPath = value.toLowerCase().includes('custom') ? 'custom' : 'predesigned';
            handlePathChange();
        }
        
        // Handle SKU swapping
        handleSKUSwapping();
    }

    function progressiveReveal(changedComponent) {
        const components = getAllComponents();
        if (!components.length) return;

        // Heuristic ordering based on titles within components
        const buildComp = matchComponentByText(components, '(build\s*strategy|pre-?designed|custom build)');
        const bundleComp = matchComponentByText(components, '(choose\s*your\s*complete\s*bundle|finish\s*material|finish/material|bundle)');

        // If first component (SKU count) got a value, show Build Strategy
        if (components[0] && (components[0].contains(changedComponent) || currentSelections['sku-count'])) {
            showComponent(buildComp);
        }

        // If Build Strategy is selected
        if (buildComp) {
            const hasSelection = !!(buildComp.querySelector('input[type="radio"]:checked, select option:checked, .af-cp-selected-product'));
            if (hasSelection) {
                if (currentPath === 'predesigned') {
                    showComponent(bundleComp);
                } else {
                    hideComponent(bundleComp);
                    clearSelections(bundleComp);
                }
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
    
    // Initialize when DOM is ready
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCPB);
    } else {
        initializeCPB();
    }
    
})(window, document);
