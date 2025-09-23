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
        
        // Set up basic listeners immediately
        setupCPBListeners();
        setupAutoResize();
        setupSKUSwapping();
        
        // Simple component setup with minimal delay
        setTimeout(() => {
            const components = getAllComponents();
            if (components.length > 0) {
                console.log('✅ Components found, setting up flow...');
                establishInitialFlowState();
            } else {
                console.log('⚠️ No components found, will retry...');
                // Simple retry after 1 second
                setTimeout(() => {
                    const retryComponents = getAllComponents();
                    if (retryComponents.length > 0) {
                        establishInitialFlowState();
                    }
                }, 1000);
            }
        }, 100);
        
        console.log('[TPB-QV] CPB initialization complete');
    }
    
    // Helpers to find CPB components in a resilient way
    function getAllComponents() {
        // Simple, fast component detection
        const comps = document.querySelectorAll('.af_cp_all_components_content .single_component');
        return Array.from(comps);
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

    // Removed complex MutationObserver - using simple timeouts instead

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

                // Force clear all selections and add placeholder
                const select = comp.querySelector('select');
                if (select) {
                    // Clear all selections first
                    select.selectedIndex = -1;
                    select.value = '';
                    
                    // Remove any existing placeholder
                    const existingPlaceholder = select.querySelector('option[value=""]');
                    if (existingPlaceholder) {
                        existingPlaceholder.remove();
                    }
                    
                    // Add new placeholder as first option
                    const placeholder = document.createElement('option');
                    placeholder.value = '';
                    placeholder.textContent = 'Select SKU count…';
                    placeholder.disabled = true;
                    placeholder.selected = true;
                    select.insertBefore(placeholder, select.firstChild);
                    
                    // Force selection to placeholder
                    select.selectedIndex = 0;
                    select.value = '';
                }

                // Clear all radios/checkboxes
                comp.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(el => { 
                    el.checked = false; 
                });
                
                // Also clear any Addify selected product blocks
                comp.querySelectorAll('.af-cp-selected-product').forEach(el => el.remove());
                
                // Retry clearing after a short delay in case Addify re-renders
                setTimeout(() => {
                    const retrySelect = comp.querySelector('select');
                    if (retrySelect && retrySelect.selectedIndex !== 0) {
                        console.log('🔄 Retrying SKU dropdown clear...');
                        retrySelect.selectedIndex = 0;
                        retrySelect.value = '';
                    }
                }, 500);
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
        // Simple, fast event delegation
        document.addEventListener('change', function(event) {
            if (event.target.closest('.af_cp_all_components_content')) {
                handleCPBSelection(event.target);
            }
        });
        
        document.addEventListener('click', function(event) {
            if (event.target.closest('.af_cp_all_components_content')) {
                handleCPBSelection(event.target);
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
                // Collapse SKU section after valid choice to guide the flow
                hideComponent(components[0]);
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
    
    // Simple initialization
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initializeCPB);
    } else {
        initializeCPB();
    }
    
})(window, document);
