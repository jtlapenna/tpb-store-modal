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
        // Establish initial state (after CPB renders) and observe for changes
        observeCPBAndInitialize();
        
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
        comp.classList.add('tpb-collapsed');
        comp.style.maxHeight = '60px';
        comp.style.overflow = 'hidden';
        comp.style.opacity = '0.6';
    }

    function showComponent(comp) {
        if (!comp) return;
        comp.classList.remove('tpb-collapsed');
        comp.style.maxHeight = 'none';
        comp.style.overflow = 'visible';
        comp.style.opacity = '1';
    }

    function observeCPBAndInitialize() {
        const tryInit = () => {
            const components = getAllComponents();
            console.log('🔍 Checking for components:', components.length);
            if (components.length) {
                console.log('✅ Components found, establishing initial flow state...');
                establishInitialFlowState();
                return true;
            }
            console.log('❌ No components found yet');
            return false;
        };

        // Attempt immediately and shortly after
        if (!tryInit()) {
            setTimeout(() => {
                console.log('🔄 Retry 1 (150ms)...');
                tryInit();
            }, 150);
            setTimeout(() => {
                console.log('🔄 Retry 2 (400ms)...');
                tryInit();
            }, 400);
        }

        // Observe Addify container for dynamic renders
        const container = document.querySelector('.af_cp_all_components_content') || document.querySelector('.af_cp_vertical_template') || document.body;
        console.log('👀 Observing container:', container);
        const observer = new MutationObserver(() => {
            console.log('🔄 Mutation detected, retrying init...');
            tryInit();
        });
        observer.observe(container, { childList: true, subtree: true });
    }

    function establishInitialFlowState() {
        const components = getAllComponents();
        if (!components.length) return;

        console.log('🎯 Establishing initial flow state with', components.length, 'components');
        
        // Assume first component is SKU count (base). Keep it visible; others collapsed.
        components.forEach((comp, index) => {
            const title = comp.querySelector('h4.title, h4, .title')?.textContent?.trim() || `Component ${index + 1}`;
            if (index === 0) {
                console.log('✅ Showing first component:', title);
                showComponent(comp);
            } else {
                console.log('📦 Collapsing component:', title);
                clearSelections(comp);
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

        // Attempt to find Build Strategy and ensure it has no default selected
        const buildComp = matchComponentByText(components, '(build\s*strategy|pre-?designed|custom build)');
        if (buildComp) {
            console.log('🔧 Found Build Strategy component, clearing selections');
            clearSelections(buildComp);
        } else {
            console.log('⚠️ Build Strategy component not found');
        }
    }

    function setupCPBListeners() {
        // Listen for CPB component changes
        document.addEventListener('change', function(event) {
            const target = event.target;
            
            // Check if it's a CPB component
            if (target.closest('.cpb-component') || target.closest('.woocommerce-variation') || target.closest('.af_cp_all_components_content')) {
                handleCPBSelection(target);
            }
        });
        
        // Listen for radio button changes
        document.addEventListener('click', function(event) {
            const target = event.target;
            if (target.type === 'radio' && (target.closest('.cpb-component') || target.closest('.af_cp_all_components_content'))) {
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
        if (!components.length) {
            console.log('❌ No components found for progressive reveal');
            return;
        }

        console.log('🔍 Found', components.length, 'components for progressive reveal');

        // Heuristic ordering based on titles within components
        const buildComp = matchComponentByText(components, '(build\s*strategy|pre-?designed|custom build)');
        const bundleComp = matchComponentByText(components, '(choose\s*your\s*complete\s*bundle|finish\s*material|finish/material|bundle)');

        console.log('🔍 Build Strategy component:', buildComp ? 'found' : 'not found');
        console.log('🔍 Bundle component:', bundleComp ? 'found' : 'not found');

        const hasValue = comp => !!(comp && (comp.querySelector('input[type="radio"]:checked, input[type="checkbox"]:checked, select option:checked, .af-cp-selected-product')));

        // If first component (SKU count) got a value, show Build Strategy
        if (components[0] && (components[0].contains(changedComponent) || hasValue(components[0]))) {
            console.log('✅ First component has value, showing Build Strategy');
            showComponent(buildComp);
        }

        // If Build Strategy is selected
        if (buildComp) {
            const bsSelected = buildComp.querySelector('input[type="radio"]:checked, select option:checked');
            if (bsSelected) {
                const txt = (bsSelected.textContent || bsSelected.value || '').toLowerCase();
                currentPath = txt.includes('custom') ? 'custom' : 'predesigned';
                console.log('🛤️ Build Strategy selected, path:', currentPath);
                if (currentPath === 'predesigned') {
                    console.log('✅ Showing bundle component for pre-designed path');
                    showComponent(bundleComp);
                } else {
                    console.log('❌ Hiding bundle component for custom path');
                    hideComponent(bundleComp);
                    clearSelections(bundleComp);
                }
            } else {
                console.log('⚠️ Build Strategy component found but no selection made');
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
