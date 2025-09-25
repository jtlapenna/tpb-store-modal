/**
 * TPB Quick View Iframe Handler - Phase 1 Optimized
 * Handles CPB interactions and communication with parent window
 * Fixed: Progressive disclosure, pre-selection issues, smooth interactions
 */
(function(window, document) {
    'use strict';
    
    let config = {};
    let currentSelections = {};
    let currentPath = 'custom'; // 'custom' or 'predesigned'
    let isInitialized = false;
    
    // Listen for configuration from parent
    window.addEventListener('message', function(event) {
        if (event.data && event.data.type === 'tpb-qv-config') {
            config = event.data.config;
            initializeCPB();
        }
    });
    
    function initializeCPB() {
        if (isInitialized) return;
        console.log('[TPB-QV] Starting CPB initialization...');
        
        // Set up basic listeners immediately
        setupCPBListeners();
        setupAutoResize();
        setupSKUSwapping();
        
        // Initialize with proper progressive disclosure
        setTimeout(() => {
            const components = getAllComponents();
            if (components.length > 0) {
                console.log('✅ Components found, setting up flow...');
                establishInitialFlowState();
                isInitialized = true;
            } else {
                console.log('⚠️ No components found, will retry...');
                // Simple retry after 1 second
                setTimeout(() => {
                    const retryComponents = getAllComponents();
                    if (retryComponents.length > 0) {
                        establishInitialFlowState();
                        isInitialized = true;
                    }
                }, 1000);
            }
        }, 100);
        
        console.log('[TPB-QV] CPB initialization complete');
    }
    
    // Helpers to find CPB components in a resilient way
    function getAllComponents() {
        // More flexible component detection
        let comps = document.querySelectorAll('.af_cp_all_components_content .single_component');
        
        // If no Addify components found, look for WooCommerce variations
        if (comps.length === 0) {
            comps = document.querySelectorAll('.woocommerce-variation, .variations select, form.cart .variations');
        }
        
        // If still nothing, look for any form elements that might be CPB
        if (comps.length === 0) {
            comps = document.querySelectorAll('form.cart, .woocommerce-variation, select[name*="attribute"]');
        }
        
        console.log('🔍 Found components:', comps.length, 'types:', Array.from(comps).map(c => c.className));
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
        console.log('📦 Hiding component:', comp.querySelector('.title')?.textContent || 'Unknown');
        comp.classList.add('tpb-hidden');
        comp.style.display = 'none';
    }

    function showComponent(comp) {
        if (!comp) return;
        console.log('📦 Showing component:', comp.querySelector('.title')?.textContent || 'Unknown');
        comp.classList.remove('tpb-hidden');
        comp.style.display = 'block';
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
                setupFirstComponent(comp);
            } else {
                console.log('📦 Collapsing component:', title);
                hideComponent(comp);
                setupCollapsedComponent(comp, index);
            }
        });
    }

    function setupFirstComponent(comp) {
        // Try multiple selectors for Addify dropdowns
        const selectors = [
            'select',
            'select[name*="variation"]',
            'select[name*="attribute"]',
            '.variations select',
            '.woocommerce-variation select',
            '.af_cp_all_components_content select'
        ];
        
        let select = null;
        for (const selector of selectors) {
            select = comp.querySelector(selector);
            if (select) {
                console.log('✅ Found select with selector:', selector);
                break;
            }
        }
        
        if (select) {
            setupSelectWithPlaceholder(select);
        } else {
            console.log('❌ No select found in component');
        }

        // Clear all radios/checkboxes
        comp.querySelectorAll('input[type="radio"], input[type="checkbox"]').forEach(el => { 
            el.checked = false; 
        });
        
        // Also clear any Addify selected product blocks
        comp.querySelectorAll('.af-cp-selected-product').forEach(el => el.remove());
    }

    function setupSelectWithPlaceholder(select) {
        console.log('🎯 Setting up select with placeholder:', select);
        
        // Clear all selections first (remove selected attrs too)
        Array.from(select.options).forEach(opt => { 
            opt.removeAttribute('selected'); 
            opt.selected = false; 
        });
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
        
        // Force selection to placeholder and dispatch events
        select.selectedIndex = 0;
        select.value = '';
        ['input','change'].forEach(evt => select.dispatchEvent(new Event(evt, { bubbles: true }))); 
        
        console.log('🎯 After reset - value:', select.value, 'selectedIndex:', select.selectedIndex);
        
        // Watch for Addify re-rendering and force placeholder
        const forcePlaceholder = () => {
            const currentSelect = comp.querySelector('select');
            if (currentSelect) {
                // Check if placeholder exists and is selected
                const placeholder = currentSelect.querySelector('option[value=""]');
                if (!placeholder || currentSelect.selectedIndex !== 0 || currentSelect.value !== '') {
                    console.log('🔄 Forcing SKU dropdown placeholder...');
                    
                    // Clear selection
                    Array.from(currentSelect.options).forEach(opt => { opt.removeAttribute('selected'); opt.selected = false; });
                    currentSelect.selectedIndex = -1;
                    currentSelect.value = '';
                    
                    // Remove existing placeholder
                    if (placeholder) placeholder.remove();
                    
                    // Add new placeholder
                    const newPlaceholder = document.createElement('option');
                    newPlaceholder.value = '';
                    newPlaceholder.textContent = 'Select SKU count…';
                    newPlaceholder.disabled = true;
                    newPlaceholder.selected = true;
                    currentSelect.insertBefore(newPlaceholder, currentSelect.firstChild);
                    
                    // Force selection
                    currentSelect.selectedIndex = 0;
                    currentSelect.value = '';
                    ['input','change'].forEach(evt => currentSelect.dispatchEvent(new Event(evt, { bubbles: true })));
                    const select2 = comp.querySelector('.select2-hidden-accessible');
                    if (select2) {
                        select2.value = '';
                        ['input','change'].forEach(evt => select2.dispatchEvent(new Event(evt, { bubbles: true })));
                    }
                }
            }
        };
        
        // Force immediately and then watch for changes
        forcePlaceholder();
        
        // Watch for DOM changes in this component
        const observer = new MutationObserver(() => {
            forcePlaceholder();
        });
        observer.observe(comp, { childList: true, subtree: true });
        
        // Also retry periodically
        const retryInterval = setInterval(forcePlaceholder, 1000);
        
        // Stop watching after 10 seconds
        setTimeout(() => {
            observer.disconnect();
            clearInterval(retryInterval);
        }, 10000);
    }

    function setupCollapsedComponent(comp, index) {
        // Make collapsed components clickable to expand
        comp.addEventListener('click', function(e) {
            // Only expand if clicking on the component itself, not on form elements
            if (e.target.tagName === 'INPUT' || e.target.tagName === 'SELECT' || e.target.tagName === 'BUTTON') {
                return;
            }
            
            if (comp.classList.contains('tpb-collapsed')) {
                console.log('🖱️ Clicked to expand component:', index);
                showComponent(comp);
                // Add smooth transition
                comp.style.opacity = '0';
                comp.style.transform = 'translateY(20px)';
                setTimeout(() => {
                    comp.style.opacity = '1';
                    comp.style.transform = 'translateY(0)';
                }, 50);
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
            const select = components[0].querySelector('select');
            const hasRealSelection = select && select.value && select.value !== '' && !select.querySelector('option[value=""]:checked');
            if (hasRealSelection && components[1]) {
                console.log('✅ SKU count selected, showing Build Strategy');
                showComponent(components[1]);
                // Add smooth transition
                components[1].style.opacity = '0';
                components[1].style.transform = 'translateY(20px)';
                setTimeout(() => {
                    components[1].style.opacity = '1';
                    components[1].style.transform = 'translateY(0)';
                }, 50);
                return;
            }
        }

        // If Build Strategy component has a selection, show third component (Bundle)
        if (components[1] && components[1].contains(changedComponent)) {
            const select = components[1].querySelector('select');
            const hasRealSelection = select && select.value && select.value !== '' && !select.querySelector('option[value=""]:checked');
            if (hasRealSelection && components[2]) {
                console.log('✅ Build Strategy selected, showing Bundle component');
                showComponent(components[2]);
                // Add smooth transition
                components[2].style.opacity = '0';
                components[2].style.transform = 'translateY(20px)';
                setTimeout(() => {
                    components[2].style.opacity = '1';
                    components[2].style.transform = 'translateY(0)';
                }, 50);
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
