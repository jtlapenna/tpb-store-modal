/**
 * Flower Station Stepper for Individual Modal Architecture
 * Works directly in modal container, not in iframe
 */

(function($) {
    'use strict';
    
    console.log('🌸 Flower Station Modal Stepper Loading...');
    
    // Helper functions
    function $(s, c) { return (c || document).querySelector(s); }
    function el(t, o) { 
        var n = document.createElement(t); 
        if (o) { 
            if (o.className) n.className = o.className; 
            if (o.text) n.textContent = o.text; 
            if (o.html) n.innerHTML = o.html; 
        } 
        return n; 
    }
    function clear(n) { while (n && n.firstChild) n.removeChild(n.firstChild); }
    
    function toast(m, type) { 
        var t = el('div', {className: 'tpb-toast ' + (type || 'success'), text: m}); 
        t.setAttribute('role', 'status'); 
        t.setAttribute('aria-live', 'polite'); 
        document.body.appendChild(t); 
        
        // Add fade out animation before removing
        setTimeout(function() { 
            if (t) {
                t.classList.add('fade-out');
                setTimeout(function() {
                    if (t && t.remove) t.remove(); 
                }, 300);
            }
        }, 1500); 
    }
    
    function fetchProducts(p) {
        // Try REST first; fallback to admin-ajax if REST unavailable
        return fetch('/wp-json/tpb/v1/qv/products', {
            method: 'POST', 
            headers: { 'Content-Type': 'application/json' }, 
            credentials: 'same-origin', 
            body: JSON.stringify(p)
        }).then(function(r) { 
            if (!r.ok) throw new Error('REST ' + r.status); 
            return r.json(); 
        }).catch(function() {
            var params = new URLSearchParams();
            if (Array.isArray(p.tags) && p.tags.length) params.set('tags', p.tags.join(','));
            if (Array.isArray(p.categories) && p.categories.length) params.set('categories', p.categories.join(','));
            if (Array.isArray(p.exclude_categories) && p.exclude_categories.length) params.set('exclude_categories', p.exclude_categories.join(','));
            if (p.limit) params.set('limit', String(p.limit));
            return fetch('/wp-admin/admin-ajax.php?action=tpb_qv_products&' + params.toString(), { 
                credentials: 'same-origin' 
            }).then(function(r) { return r.json(); });
        });
    }
    
    function addCart(id, q, btn) { 
        if (btn) { 
            btn.disabled = true; 
            btn.textContent = 'Adding...';
        } 
        
        // Use original working method
        return fetch('/?add-to-cart=' + encodeURIComponent(id) + (q ? ('&quantity=' + encodeURIComponent(q)) : ''), {
            credentials: 'same-origin'
        })
        .then(function() { 
            toast('Added to cart!', 'success');
            // Update cart count if cart widget is present
            if (typeof window.tpbUpdateCart === 'function') {
                window.tpbUpdateCart();
            } else {
                updateCartCount();
            }
        })
        .catch(function() { 
            toast('Add to cart failed', 'error'); 
        })
        .finally(function() { 
            if (btn) { 
                btn.disabled = false; 
                btn.textContent = 'Add to cart';
            } 
        }); 
    }
    
    // Update cart count in floating cart
    function updateCartCount() {
        // Trigger WooCommerce cart fragments update
        if (typeof jQuery !== 'undefined' && jQuery(document.body).trigger) {
            // Trigger the standard WooCommerce cart update event
            jQuery(document.body).trigger('wc_fragment_refresh');
            jQuery(document.body).trigger('updated_wc_div');
            jQuery(document.body).trigger('added_to_cart');
            
            // Also try to update specific cart elements
            const cartElements = document.querySelectorAll('[class*="cart"], [class*="woocommerce-cart"], [class*="elementor-cart"]');
            cartElements.forEach(function(element) {
                jQuery(element).trigger('wc_fragment_refresh');
            });
        }
        
        // Force a page refresh if that's what the cart widget expects
        // This is a fallback for stubborn cart widgets
        setTimeout(function() {
            // Re-fetch the cart fragment from WooCommerce
            if (typeof jQuery !== 'undefined') {
                jQuery.get('/cart/', function(response) {
                    var $response = jQuery(response);
                    var $newCart = $response.find('[class*="cart"], [class*="woocommerce"]');
                    if ($newCart.length > 0) {
                        jQuery('[class*="cart"], [class*="woocommerce"]').replaceWith($newCart);
                    }
                });
            }
        }, 500);
    }
    
    function addQuote(id, btn) { 
        if (btn) { btn.disabled = true; } 
        return fetch('/?add-to-quote=' + encodeURIComponent(id), {
            credentials: 'same-origin'
        }).then(function() { 
            toast('Added to quote', 'success'); 
        }).catch(function() { 
            toast('Add to quote failed', 'error'); 
        }).finally(function() { 
            if (btn) { btn.disabled = false; } 
        }); 
    }
    
    // Main Flower Station Stepper
    function buildFlowerStepper(container) {
        console.log('🌸 Building Flower Station stepper in modal');
        
        var root = container;
        clear(root);
        
        // Add CSS styles - exact copy from original stepper with cache busting
        console.log('🎨 Injecting CSS for .tpb-qv-native');
        var css = el('style');
        css.id = 'tpb-flower-stepper-css-' + Date.now();
        css.textContent = '.tpb-qv-stepper-container .tpb-qv-native{font-family:inherit;padding:20px;max-width:90%;box-sizing:border-box;overflow-x:hidden;width:100%;color:#333!important} .tpb-qv-native{font-family:inherit;padding:20px;max-width:90%;box-sizing:border-box;overflow-x:hidden;width:100%;color:#333!important} .tpb-step{margin:0;padding:40px 0 0px 40px;max-width:100%;box-sizing:border-box;border:none!important;border-bottom:none!important;border-top:none!important;border-left:none!important;border-right:none!important;color:#333!important} .tpb-grid{display:grid!important;grid-template-columns:repeat(2,1fr)!important;margin-top:35px!important;max-width:100%!important;box-sizing:border-box!important} .tpb-qv-modal .tpb-card{border:1px solid #e3e6ea;border-radius:12px;padding:16px;background:#fff;transition:box-shadow 0.2s,transform 0.2s;text-align:left;max-width:100%!important;box-sizing:border-box;color:#333!important;transform:scale(0.85)!important;transform-origin:top left!important} .tpb-qv-modal .tpb-card:hover{box-shadow:0 4px 12px rgba(0,0,0,0.1);transform:scale(0.85) translateY(-2px)!important} .tpb-qv-modal .tpb-card-horizontal{display:flex!important;gap:24px!important;align-items:flex-start!important;padding:24px!important;min-height:200px!important;width:100%!important;max-width:100%!important;overflow:visible!important;color:#333!important} .tpb-qv-modal .tpb-card-image{position:relative;padding-bottom:75%;overflow:hidden;border-radius:8px;margin-bottom:12px} .tpb-qv-modal .tpb-card-horizontal .tpb-card-image{flex:0 0 200px!important;width:200px!important;height:200px!important;position:relative!important;overflow:hidden!important;border-radius:8px!important;margin-bottom:0!important;padding-bottom:0!important} .tpb-qv-modal .tpb-card-horizontal .tpb-card-content{flex:1!important;display:flex!important;flex-direction:column!important;justify-content:space-between!important;min-height:180px!important;padding-left:8px!important;min-width:0!important;width:100%!important;color:#333!important} .tpb-qv-modal .tpb-card-horizontal .tpb-actions{display:flex!important;gap:16px!important;justify-content:flex-start!important;flex-wrap:nowrap!important;margin-top:8px!important;width:100%!important;min-width:0!important} .tpb-actions{display:flex;gap:8px;margin-top:12px;align-items:center;justify-content:flex-start;flex-wrap:wrap;color:#333!important} .tpb-hint{color:#6b7280!important;font-size:15px;margin:4px 0 8px;text-align:left} .tpb-title{font-weight:700;font-size:18px;margin:8px 0;text-align:left;color:#333!important} .tpb-qv-modal .tpb-card-title{font-weight:600;font-size:16px;margin-bottom:8px;min-height:40px;line-height:1.3;text-align:center;color:#333!important} .tpb-qv-modal .tpb-card-horizontal .tpb-card-title{font-size:22px!important;text-align:left!important;color:#333!important} .tpb-qv-modal .tpb-card-title .parenthetical{font-size:16px;font-weight:400;display:block;margin-top:2px;color:#666!important} .tpb-qv-modal .tpb-card-price{color:rgb(79 176 137)!important;font-weight:600;font-size:16px;padding-top:10px;padding-bottom:10px;margin-bottom:12px;text-align:center} .tpb-select{margin-top:6px;text-align:left;border:2px solid #e3e6ea!important;border-radius:8px!important;padding:12px 16px!important;background:#fff!important;color:#333!important;font-size:16px!important;cursor:pointer!important;transition:border-color 0.2s ease!important;appearance:none!important;-webkit-appearance:none!important;-moz-appearance:none!important;background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23666\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6,9 12,15 18,9\'%3e%3c/polyline%3e%3c/svg%3e")!important;background-repeat:no-repeat!important;background-position:right 12px center!important;background-size:16px!important;padding-right:40px!important} .tpb-select:hover{border-color:#4fb08f!important} .tpb-select:focus{outline:none!important;border-color:#4fb08f!important;box-shadow:0 0 0 3px rgba(79,176,143,0.1)!important} .tpb-radio{margin-top:6px;text-align:left;border:none!important;border-bottom:none!important;border-top:none!important;border-left:none!important;border-right:none!important;outline:none!important;box-shadow:none!important;background:none!important;appearance:none!important;-webkit-appearance:none!important;-moz-appearance:none!important;color:#333!important} .tpb-toast{position:fixed;right:12px;bottom:12px;background:#111827;color:#fff;padding:10px 14px;border-radius:8px;opacity:.95;z-index:99999} .sr-live{position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden} .tpb-step[hidden]{display:none!important} .tpb-step:not([hidden]){display:block!important} .tpb-step:not(.visible){opacity:0;transform:translateY(20px);transition:opacity 0.4s ease-out,transform 0.4s ease-out} .tpb-step.visible{opacity:1;transform:translateY(0)} .tpb-step.entering{opacity:0;transform:translateY(20px);animation:stepEnter 0.4s ease-out forwards} @keyframes stepEnter{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}} .tpb-hint.entering{opacity:0;transform:translateY(15px);animation:textEnter 0.3s ease-out forwards} @keyframes textEnter{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}} .tpb-base-price{transition:all 0.3s ease-in-out;color:#333!important;margin-bottom:0!important} .tpb-base-price.updating{opacity:0.7;transform:scale(0.98)} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal){opacity:0;transform:translateY(15px);animation:cardReveal 0.5s ease-out forwards} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(1){animation-delay:0.1s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(2){animation-delay:0.2s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(3){animation-delay:0.3s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(4){animation-delay:0.4s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(5){animation-delay:0.5s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(6){animation-delay:0.6s} @keyframes cardReveal{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}} .tpb-qv-modal .tpb-card-horizontal{opacity:1;transform:none;animation:none} .tpb-qty{color:#333!important} .tpb-qty span{color:#333!important} .tpb-qty input{color:#333!important;background:#fff!important;border:1px solid #ddd!important} .tpb-bottom-section{color:#333!important} .tpb-qv-stepper-container{padding-top:0!important}';
        document.head.appendChild(css);
        
        
        var live = el('div', {className: 'sr-live'});
        live.setAttribute('aria-live', 'polite');
        root.appendChild(live);
        
        var state = {sku: null, strategy: null, hardwarePrice: null, priceUpdateTimeout: null, shouldScroll: true};
        
        // Step 1: SKU Selection
        var s1 = el('div', {className: 'tpb-step'});
        s1.appendChild(el('div', {className: 'tpb-title', text: 'How many SKUs?'}));
        s1.appendChild(el('div', {className: 'tpb-hint', text: 'Select how many strains you will display at this station.'}));
        var sel = el('select', {className: 'tpb-select'});
        ['', '8', '10', '16', '20'].forEach(function(v) {
            var o = el('option');
            o.value = v;
            o.textContent = v ? (v + ' SKU') : 'Select';
            sel.appendChild(o);
        });
        s1.appendChild(sel);
        
        // Step 2: Build Strategy
        var s2 = el('div', {className: 'tpb-step'});
        s2.appendChild(el('div', {className: 'tpb-title', text: 'Build Strategy'}));
        s2.appendChild(el('div', {className: 'tpb-hint', text: 'Custom Build includes hardware only; Pre-designed includes furniture.'}));
        var r = el('div', {className: 'tpb-radio'});
        r.innerHTML = '<label><input type="radio" name="tpb-strategy" value="custom"> Custom Build</label> <label style="margin-left:16px"><input type="radio" name="tpb-strategy" value="predesigned"> Pre-designed</label>';
        s2.appendChild(r);
        
        // Step 3: Results
        var s3 = el('div', {className: 'tpb-step'});
        var resH = el('div', {className: 'tpb-title', text: 'Choose Your Bundle'});
        resH.id = 'tpb-results-heading';
        resH.tabIndex = -1;
        s3.appendChild(resH);
        var grid = el('div', {className: 'tpb-grid'});
        s3.appendChild(grid);
        
        // Initially hide all steps
        s1.hidden = true;
        s2.hidden = true;
        s3.hidden = true;
        
        function focusResults() { 
            try { 
                resH.focus(); 
            } catch (e) {} 
        }
        
        // Update only the product grid without rebuilding Step 1
        function updateProductsOnly() {
            console.log('updateProductsOnly() called - strategy:', state.strategy, 'sku:', state.sku);
            
            // Save scroll position from the right panel
            var modalContent = document.querySelector('.tpb-qv-right-panel');
            var scrollPosition = modalContent ? modalContent.scrollTop : 0;
            window.tpbSavedScrollPosition = scrollPosition;
            console.log('Saved scroll position:', scrollPosition);
            
            // Skip exit animation to preserve scroll position
            // Clear the grid content immediately
            var existingCards = grid.querySelectorAll('.tpb-card, .tpb-empty-state');
            if (existingCards.length > 0) {
                console.log('Clearing existing content without animation...');
                while (grid.firstChild) {
                    grid.removeChild(grid.firstChild);
                }
            }
            
            // Restore scroll position immediately after clearing
            if (modalContent) {
                modalContent.scrollTop = scrollPosition;
                console.log('Restored scroll position to:', scrollPosition);
            }
            
            // Proceed with update
            proceedWithUpdate();
        }
        
        function proceedWithUpdate() {
            // Update Step 3 heading based on strategy
            var newTitle = state.strategy === 'custom' ? 'Here\'s Your Hardware-Only Kit' : 'Choose Your Bundle';
            if (resH.textContent !== newTitle) {
                resH.textContent = newTitle;
            }
            
            // Handle custom build description
            var existingDesc = s3.querySelector('.tpb-hint');
            if (state.strategy === 'custom') {
                // Add custom build description if it doesn't exist
                if (!existingDesc || !existingDesc.textContent.includes('custom build project')) {
                    var desc = el('div', {className: 'tpb-hint'});
                    desc.innerHTML = 'For a custom build project, you only need to purchase the electronics hardware, and TPB will offer consultation for your furniture design, jars, coasters, and all other components of the project.';
                    desc.style.cssText = 'width:100%;max-width:100%;padding:0;margin-bottom:16px;color:#6b7280;font-size:15px;line-height:1.5';
                    s3.insertBefore(desc, grid);
                }
            } else {
                // Remove custom build description for pre-designed
                if (existingDesc && existingDesc.textContent.includes('custom build project')) {
                    existingDesc.remove();
                }
            }
            
            // Update grid styles based on strategy
            if (state.strategy === 'predesigned') {
                // Reset grid styles for pre-designed - force 2-column layout
                // Clear all existing styles first
                grid.removeAttribute('style');
                
                // Apply pre-designed grid styles
                grid.style.cssText = 'display:grid!important;grid-template-columns:repeat(2,1fr)!important;gap:20px!important;margin-top:16px!important;max-width:100%!important;box-sizing:border-box!important';
                
                // Force override with setProperty as backup
                grid.style.setProperty('display', 'grid', 'important');
                grid.style.setProperty('grid-template-columns', 'repeat(2, 1fr)', 'important');
                
                // Force grid to recalculate
                void grid.offsetWidth;
            } else if (state.strategy === 'custom') {
                // Override grid styles for custom build to allow full width
                grid.style.cssText = 'display:block!important;width:100%!important;max-width:none!important;box-sizing:border-box!important;margin-top:16px!important';
            }
            
            // Clear existing content without using innerHTML
            while (grid.firstChild) {
                grid.removeChild(grid.firstChild);
            }
            
            // Fetch and display products
            if (!state.sku || !state.strategy) { 
                return; 
            }
            
            var skuTag = state.sku ? (state.sku + '-sku') : null;
            var tags = skuTag ? [skuTag] : [];
            var cats = [];
            var excludeCats = [];
            var excludeTags = [];

            // Set API parameters based on strategy using proper WordPress taxonomy
            if (state.strategy === 'custom') {
                // For custom build: just use SKU tag to get all products, then filter by name pattern
                cats = [];
                tags = [state.sku + '-SKU']; // e.g., '8-SKU', '16-SKU' (uppercase)
            } else if (state.strategy === 'predesigned') {
                // For pre-designed: use SKU tag + exclude problematic tags, rely on client-side filtering
                cats = [];
                tags = [state.sku + '-SKU']; // e.g., '8-SKU', '16-SKU' (uppercase)
                excludeTags = ['sales-tool', 'hardware-only-kits']; // Exclude sales-tool and hardware-only-kits tags
            }
            
            console.log('🌸 Fetching products with tags:', tags, 'categories:', cats, 'exclude cats:', excludeCats, 'exclude tags:', excludeTags);
            
            var params = {
                tags: tags,
                categories: cats,
                exclude_categories: excludeCats,
                exclude_tags: excludeTags,
                limit: 50
            };
            
            fetchProducts(params).then(function(products) {
                console.log('Products fetched (using taxonomy filtering):', products.length);
                console.log('Strategy:', state.strategy, 'SKU:', state.sku);
                console.log('Products before client-side filtering:', products.map(p => ({id: p.id, name: p.name, price: p.price_html, tags: p.tags})));
                console.log('Detailed product names before filtering:', products.map(p => p.name));
                console.log('Product tags before filtering:', products.map(p => ({name: p.name, tags: (p.tags || []).map(function(tag) { return tag.name || tag; })})));
                
                // Client-side filtering to distinguish between hardware-only and pre-designed
                if (state.strategy === 'custom') {
                    // For custom build: show only 22" hardware-only kit (not 27")
                    products = products.filter(function(p) {
                        var name = (p.name || '');
                        // Match ONLY 22" hardware-only pattern: "22" Flower Station - X SKU"
                        return /^22"?\s+Flower Station\s+-\s+\d+\s+SKU$/i.test(name);
                    });
                } else if (state.strategy === 'predesigned') {
                    // For pre-designed: show bundles (Tech Bundle, TPB-, etc.) but exclude hardware-only and sales-tool
                    products = products.filter(function(p) {
                        var name = (p.name || '');
                        var tags = (p.tags || []).map(function(tag) { return tag.name || tag; });
                        
                        // Exclude products with sales-tool tag
                        if (tags.some(function(tag) { return tag.toLowerCase() === 'sales-tool'; })) {
                            console.log('Excluding product with sales-tool tag:', p.name, 'tags:', tags);
                            return false;
                        }
                        
                        // Exclude simple hardware-only pattern, keep everything else (bundles)
                        var isHardwareOnly = /^\d{2}"?\s+Flower Station\s+-\s+\d+\s+SKU$/i.test(name);
                        if (isHardwareOnly) {
                            console.log('Excluding hardware-only product:', p.name);
                            return false;
                        }
                        
                        return true;
                    });
                }
                
                console.log('Products after client-side filtering:', products.length);
                console.log('Filtered products:', products.map(p => ({id: p.id, name: p.name, price: p.price_html})));
                
                if (products.length === 0) {
                    var emptyState = el('div', {className: 'tpb-empty-state'});
                    emptyState.innerHTML = '<p>No products found for the selected strategy.</p>';
                    grid.appendChild(emptyState);
                } else {
                    // Use the same rendering logic as the original fetchProductsAndRender
                    products.forEach(function(p, index) {
                        var card = el('div', {className: 'tpb-card'});
                        
                        if (state.strategy === 'custom') {
                            // For Custom Build, use horizontal layout with separate class
                            card.className = 'tpb-card tpb-card-horizontal';
                            card.style.cssText = 'display:flex!important;gap:24px!important;align-items:flex-start!important;padding:24px!important;border:1px solid #e3e6ea!important;border-radius:12px!important;background:#fff!important;transition:box-shadow 0.2s,transform 0.2s!important;text-align:left!important;width:100%!important;max-width:none!important;box-sizing:border-box!important;min-height:200px!important;overflow:visible!important;flex-shrink:0!important';
                            
                            // Image container - left side
                            var imgContainer = el('div', {className: 'tpb-card-image'});
                            imgContainer.style.cssText = 'flex:0 0 200px;width:200px;height:200px;position:relative;overflow:hidden;border-radius:8px';
                            var img = el('img');
                            img.alt = p.name || '';
                            img.src = p.image_url || '';
                            img.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover';
                            imgContainer.appendChild(img);
                            card.appendChild(imgContainer);
                            
                            // Content container - right side
                            var contentContainer = el('div', {className: 'tpb-card-content'});
                            contentContainer.style.cssText = 'flex:1;display:flex;flex-direction:column;justify-content:space-between;min-height:180px;padding-left:8px;min-width:0;width:100%';
                            
                            // Product title with parenthetical text handling
                            var title = el('div', {className: 'tpb-card-title'});
                            var productName = p.name || '';
                            
                            // Check for parenthetical text and split it
                            var parenMatch = productName.match(/^(.+?)\s*\(([^)]+)\)$/);
                            if (parenMatch) {
                                var mainTitle = parenMatch[1].trim();
                                var parenthetical = parenMatch[2].trim();
                                title.innerHTML = mainTitle + '<span class="parenthetical">(' + parenthetical + ')</span>';
                            } else {
                                title.textContent = productName;
                            }
                            
                            title.style.cssText = 'font-weight:600;font-size:22px;margin-bottom:8px;line-height:1.3;text-align:left';
                            contentContainer.appendChild(title);
                            
                            // Price
                            var priceDiv = el('div', {className: 'tpb-card-price'});
                            priceDiv.innerHTML = p.price_html || '';
                            priceDiv.style.cssText = 'color:rgb(79 176 137);font-weight:600;font-size:20px;padding:8px 0;margin-bottom:16px;text-align:left';
                            contentContainer.appendChild(priceDiv);
                            
                            // Bottom section with quantity and actions
                            var bottomSection = el('div', {className: 'tpb-bottom-section'});
                            bottomSection.style.cssText = 'display:flex;flex-direction:column;gap:12px;width:100%;min-width:0';
                            
                            // Quantity selector
                            var qtyDiv = el('div', {className: 'tpb-qty'});
                            qtyDiv.style.cssText = 'display:flex;align-items:center;gap:8px';
                            qtyDiv.innerHTML = '<span>Qty:</span><input type="number" value="1" min="1" style="width:60px;padding:4px;border:1px solid #ddd;border-radius:4px">';
                            bottomSection.appendChild(qtyDiv);
                            
                            // Action buttons
                            var actions = el('div', {className: 'tpb-actions'});
                            actions.style.cssText = 'display:flex;gap:16px;justify-content:flex-start;flex-wrap:nowrap;margin-top:8px;width:100%;min-width:0';
                            
                            var addToCartBtn = el('button', {text: 'Add to cart'});
                            addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                            addToCartBtn.addEventListener('click', function() { addCart(p.id, 1, this); });
                            
                            var addToQuoteBtn = el('button', {text: 'Add to quote'});
                            addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                            addToQuoteBtn.addEventListener('click', function() { addQuote(p.id, this); });
                            
                            actions.appendChild(addToCartBtn);
                            actions.appendChild(addToQuoteBtn);
                            bottomSection.appendChild(actions);
                            contentContainer.appendChild(bottomSection);
                            card.appendChild(contentContainer);
                        } else {
                            // For Pre-designed, use vertical layout
                            card.className = 'tpb-card';
                            
                            // Image container with aspect ratio
                            var imgContainer = el('div', {className: 'tpb-card-image'});
                            imgContainer.style.cssText = 'position:relative;padding-bottom:75%;overflow:hidden;border-radius:8px;margin-bottom:12px';
                            var img = el('img');
                            img.alt = p.name || '';
                            img.src = p.image_url || '';
                            img.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover';
                            imgContainer.appendChild(img);
                            card.appendChild(imgContainer);
                            
                            // Product title with parenthetical text handling
                            var title = el('div', {className: 'tpb-card-title'});
                            var productName = p.name || '';
                            
                            // Check for parenthetical text and split it
                            var parenMatch = productName.match(/^(.+?)\s*\(([^)]+)\)$/);
                            if (parenMatch) {
                                var mainTitle = parenMatch[1].trim();
                                var parenthetical = parenMatch[2].trim();
                                title.innerHTML = mainTitle + '<span class="parenthetical">(' + parenthetical + ')</span>';
                            } else {
                                title.textContent = productName;
                            }
                            
                            title.style.cssText = 'font-weight:600;font-size:16px;margin-bottom:8px;min-height:40px;line-height:1.3;text-align:center';
                            card.appendChild(title);
                            
                            // Price
                            var priceDiv = el('div', {className: 'tpb-card-price'});
                            priceDiv.innerHTML = p.price_html || '';
                            priceDiv.style.cssText = 'color:rgb(79 176 137);font-weight:600;font-size:16px;padding-top:10px;padding-bottom:10px;margin-bottom:12px;text-align:center';
                            card.appendChild(priceDiv);
                            
                            // Quantity selector
                            var qtyDiv = el('div', {className: 'tpb-qty'});
                            qtyDiv.style.cssText = 'display:flex;align-items:center;gap:8px;justify-content:center';
                            qtyDiv.innerHTML = '<span>Qty:</span><input type="number" value="1" min="1" style="width:60px;padding:4px;border:1px solid #ddd;border-radius:4px">';
                            card.appendChild(qtyDiv);
                            
                            // Action buttons
                            var actions = el('div', {className: 'tpb-actions'});
                            actions.style.cssText = 'display:flex;gap:8px;margin-top:12px;align-items:center;justify-content:flex-start;flex-wrap:wrap';
                            
                            var addToCartBtn = el('button', {text: 'Add to cart'});
                            addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                            addToCartBtn.addEventListener('click', function() { addCart(p.id, 1, this); });
                            
                            var addToQuoteBtn = el('button', {text: 'Add to quote'});
                            addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                            addToQuoteBtn.addEventListener('click', function() { addQuote(p.id, this); });
                            
                            actions.appendChild(addToCartBtn);
                            actions.appendChild(addToQuoteBtn);
                            card.appendChild(actions);
                        }
                        
                        // Set initial state for entry animation
                        card.style.setProperty('opacity', '0', 'important');
                        card.style.setProperty('transform', 'translateY(15px)', 'important');
                        grid.appendChild(card);
                        
                        // Trigger entry animation with staggered delay
                        setTimeout(function() {
                            card.style.setProperty('transition', 'opacity 0.4s ease-out, transform 0.4s ease-out', 'important');
                            card.style.setProperty('opacity', '1', 'important');
                            card.style.setProperty('transform', 'translateY(0)', 'important');
                        }, 50 + (index * 50)); // Stagger by 50ms per card
                    });
                }
                
                root.classList.add('has-results');
                
            }).catch(function(error) {
                console.error('Error fetching products for products only:', error);
                var errorState = el('div', {className: 'tpb-empty-state'});
                errorState.innerHTML = '<p>Error loading products. Please try again.</p>';
                grid.appendChild(errorState);
            });
        }
        
        function update() {
            // Check for any existing content in the grid that needs to be animated out
            var existingCards = grid.querySelectorAll('.tpb-card');
            var existingEmptyState = grid.querySelector('.tpb-empty-state');
            
            console.log('update() called - existingCards:', existingCards.length, 'existingEmptyState:', !!existingEmptyState);
            
            if (existingCards.length > 0 || existingEmptyState) {
                console.log('Animating out existing content...');
                
                // Force a reflow to ensure the elements are ready for animation
                void grid.offsetWidth;
                
                // Add exit animation to existing cards
                if (existingCards.length > 0) {
                    existingCards.forEach(function(card, index) {
                        console.log('Animating out card:', index, card);
                        // Use requestAnimationFrame to ensure the animation is applied
                        requestAnimationFrame(function() {
                            card.style.setProperty('transition', 'opacity 0.6s ease-out, transform 0.6s ease-out', 'important');
                            card.style.setProperty('opacity', '0', 'important');
                            card.style.setProperty('transform', 'translateY(-15px)', 'important');
                        });
                    });
                }
                
                // Add exit animation to existing empty state
                if (existingEmptyState) {
                    console.log('Animating out empty state:', existingEmptyState);
                    requestAnimationFrame(function() {
                        existingEmptyState.style.setProperty('transition', 'opacity 0.6s ease-out, transform 0.6s ease-out', 'important');
                        existingEmptyState.style.setProperty('opacity', '0', 'important');
                        existingEmptyState.style.setProperty('transform', 'translateY(-15px)', 'important');
                    });
                }
                
                // Wait for exit animation to complete before clearing
                setTimeout(function() {
                    console.log('Clearing grid after animation...');
                    grid.innerHTML = '';
                    root.classList.remove('has-results');
                    // Continue with product fetching
                    fetchProductsAndRender();
                }, 650); // Slightly longer than animation duration
                return; // Exit early, fetchProductsAndRender will handle the rest
            }
            
            console.log('No existing content, proceeding directly...');
            // No existing content, proceed directly
            grid.innerHTML = '';
            root.classList.remove('has-results');
            fetchProductsAndRender();
        }
        
        function fetchProductsAndRender() {
            // Remove any existing custom build description only when switching away from Custom Build
            var existingDesc = s3.querySelector('.tpb-hint');
            if (existingDesc && existingDesc.textContent.includes('custom build project') && state.strategy !== 'custom') {
                existingDesc.remove();
            }
            
            // Update Step 3 heading based on strategy
            var newTitle = state.strategy === 'custom' ? 'Here\'s Your Hardware-Only Kit' : 'Choose Your Bundle';
            if (resH.textContent !== newTitle) {
                // Title is changing - add entrance animation (slide up from below)
                resH.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                resH.style.opacity = '0';
                resH.style.transform = 'translateY(10px)';
                
                setTimeout(function() {
                    resH.textContent = newTitle;
                    resH.style.opacity = '1';
                    resH.style.transform = 'translateY(0)';
                }, 150);
            } else {
                resH.textContent = newTitle;
            }
            
            if (!state.sku || !state.strategy) { 
                return; 
            }
            
            var skuTag = state.sku ? (state.sku + '-sku') : null;
            var tags = skuTag ? [skuTag] : [];
            var cats = [];
            var excludeCats = [];
            
            if (state.strategy === 'predesigned') {
                tags.push('flower-stations');
                excludeCats = ['hardware-only-kits']; // Exclude hardware kits
                
                // Reset grid styles for pre-designed - force 2-column layout
                // Clear all existing styles first
                grid.removeAttribute('style');
                
                // Apply pre-designed grid styles
                grid.style.cssText = 'display:grid!important;grid-template-columns:repeat(2,1fr)!important;gap:20px!important;margin-top:16px!important;max-width:100%!important;box-sizing:border-box!important';
                
                // Force override with setProperty as backup
                grid.style.setProperty('display', 'grid', 'important');
                grid.style.setProperty('grid-template-columns', 'repeat(2, 1fr)', 'important');
                
                // Force grid to recalculate
                void grid.offsetWidth;
            } else if (state.strategy === 'custom') {
                // For custom build, use the SKU-specific tag to find hardware-only kits
                // The hardware-only kits are the cheapest options for each SKU count
                
                // Only add custom build description if it doesn't already exist
                var existingCustomDesc = s3.querySelector('.tpb-hint');
                if (!existingCustomDesc || !existingCustomDesc.textContent.includes('custom build project')) {
                    // Add custom build description
                    var desc = el('div', {className: 'tpb-hint'});
                    desc.innerHTML = 'For a custom build project, you only need to purchase the electronics hardware, and TPB will offer consultation for your furniture design, jars, coasters, and all other components of the project.';
                    desc.style.cssText = 'width:100%;max-width:100%;padding:0;margin-bottom:16px;color:#6b7280;font-size:15px;line-height:1.5';
                    
                    // Insert description after the heading but before the grid
                    s3.insertBefore(desc, grid);
                    
                    // Trigger description entrance animation only when first created
                    desc.classList.add('entering');
                    setTimeout(function() {
                        desc.classList.remove('entering');
                    }, 300);
                }
                
                // Override grid styles for custom build to allow full width
                grid.style.cssText = 'display:block!important;width:100%!important;max-width:none!important;box-sizing:border-box!important;margin-top:16px!important';
            }
            
            // Clear any existing empty state message with exit animation
            var existingEmptyState = grid.querySelector('.tpb-empty-state');
            if (existingEmptyState) {
                existingEmptyState.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                existingEmptyState.style.opacity = '0';
                existingEmptyState.style.transform = 'translateY(-15px)';
                setTimeout(function() {
                    existingEmptyState.remove();
                }, 300);
            }
            
            // Set up tags and categories for product filtering
            var skuTag = state.sku ? (state.sku + '-sku') : null;
            var tags = skuTag ? [skuTag] : [];
            var cats = [];
            var excludeCats = [];
            var excludeTags = [];

            // Set API parameters based on strategy using proper WordPress taxonomy
            if (state.strategy === 'custom') {
                // For custom build: just use SKU tag to get all products, then filter by name pattern
                cats = [];
                tags = [state.sku + '-SKU']; // e.g., '8-SKU', '16-SKU' (uppercase)
            } else if (state.strategy === 'predesigned') {
                // For pre-designed: use SKU tag + exclude problematic tags, rely on client-side filtering
                cats = [];
                tags = [state.sku + '-SKU']; // e.g., '8-SKU', '16-SKU' (uppercase)
                excludeTags = ['sales-tool', 'hardware-only-kits']; // Exclude sales-tool and hardware-only-kits tags
            }
            
            
            console.log('🌸 Fetching products with tags:', tags, 'categories:', cats, 'exclude cats:', excludeCats, 'exclude tags:', excludeTags);
            
            fetchProducts({
                tags: tags,
                categories: cats,
                exclude_categories: excludeCats,
                exclude_tags: excludeTags,
                limit: state.strategy === 'custom' ? 1 : 24
            }).then(function(d) {
                var items = Array.isArray(d) ? d : (d && Array.isArray(d.data) ? d.data : (d && Array.isArray(d.results) ? d.results : []));
                
                console.log('🌸 API returned', items.length, 'products for', state.strategy, 'strategy');
                console.log('Strategy:', state.strategy, 'SKU:', state.sku);
                console.log('Products:', items.map(p => ({id: p.id, name: p.name, price: p.price_html})));
                return items;
            }).then(function(items) {
                console.log('Products fetched (using taxonomy filtering):', items.length);
                console.log('Strategy:', state.strategy, 'SKU:', state.sku);
                console.log('Products before client-side filtering:', items.map(p => ({id: p.id, name: p.name, price: p.price_html, tags: p.tags})));
                console.log('Product tags before filtering:', items.map(p => ({name: p.name, tags: (p.tags || []).map(function(tag) { return tag.name || tag; })})));
                
                // Client-side filtering to distinguish between hardware-only and pre-designed
                if (state.strategy === 'custom') {
                    // For custom build: show only 22" hardware-only kit (not 27")
                    items = items.filter(function(p) {
                        var name = (p.name || '');
                        // Match ONLY 22" hardware-only pattern: "22" Flower Station - X SKU"
                        return /^22"?\s+Flower Station\s+-\s+\d+\s+SKU$/i.test(name);
                    });
                } else if (state.strategy === 'predesigned') {
                    // For pre-designed: show bundles (Tech Bundle, TPB-, etc.) but exclude hardware-only and sales-tool
                    items = items.filter(function(p) {
                        var name = (p.name || '');
                        var tags = (p.tags || []).map(function(tag) { return tag.name || tag; });
                        
                        // Exclude products with sales-tool tag
                        if (tags.some(function(tag) { return tag.toLowerCase() === 'sales-tool'; })) {
                            console.log('Excluding product with sales-tool tag:', p.name, 'tags:', tags);
                            return false;
                        }
                        
                        // Exclude simple hardware-only pattern, keep everything else (bundles)
                        var isHardwareOnly = /^\d{2}"?\s+Flower Station\s+-\s+\d+\s+SKU$/i.test(name);
                        if (isHardwareOnly) {
                            console.log('Excluding hardware-only product:', p.name);
                            return false;
                        }
                        
                        return true;
                    });
                }
                
                console.log('Products after client-side filtering:', items.length);
                console.log('Filtered products:', items.map(p => ({id: p.id, name: p.name, price: p.price_html})));
                
                if (items.length) {
                    root.classList.add('has-results');
                } else if (state.strategy === 'predesigned') {
                    // Show empty state message for pre-designed when no products found
                    var emptyState = el('div', {className: 'tpb-empty-state'});
                    emptyState.innerHTML = 'There are no pre-designed options with this SKU-count. Please choose another option.';
                    emptyState.style.cssText = 'text-align:center;padding:40px 20px;color:#6b7280;font-size:16px;line-height:1.5;background:#f8f9fa;border-radius:8px;margin:20px 0;border:1px solid #e3e6ea;opacity:0;transform:translateY(15px);transition:opacity 0.4s ease-out,transform 0.4s ease-out';
                    grid.appendChild(emptyState);
                    
                    // Trigger entrance animation
                    setTimeout(function() {
                        emptyState.style.opacity = '1';
                        emptyState.style.transform = 'translateY(0)';
                        // Auto-scroll to show empty state
                        scrollToShowContent(emptyState);
                    }, 50);
                }
                
                items.forEach(function(p) {
                    var card = el('div', {className: 'tpb-card'});
                    
                    if (state.strategy === 'custom') {
                        // For Custom Build, use horizontal layout with separate class
                        card.className = 'tpb-card tpb-card-horizontal';
                        card.style.cssText = 'display:flex!important;gap:24px!important;align-items:flex-start!important;padding:24px!important;border:1px solid #e3e6ea!important;border-radius:12px!important;background:#fff!important;transition:box-shadow 0.2s,transform 0.2s!important;text-align:left!important;width:100%!important;max-width:none!important;box-sizing:border-box!important;min-height:200px!important;overflow:visible!important;flex-shrink:0!important';
                        
                        // Image container - left side
                        var imgContainer = el('div', {className: 'tpb-card-image'});
                        imgContainer.style.cssText = 'flex:0 0 200px;width:200px;height:200px;position:relative;overflow:hidden;border-radius:8px';
                        var img = el('img');
                        img.alt = p.name || '';
                        img.src = p.image_url || '';
                        img.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover';
                        imgContainer.appendChild(img);
                        card.appendChild(imgContainer);
                        
                        // Content container - right side
                        var contentContainer = el('div', {className: 'tpb-card-content'});
                        contentContainer.style.cssText = 'flex:1;display:flex;flex-direction:column;justify-content:space-between;min-height:180px;padding-left:8px;min-width:0;width:100%';
                        
                        // Product title with parenthetical text handling
                        var title = el('div', {className: 'tpb-card-title'});
                        var productName = p.name || '';
                        
                        // Check for parenthetical text and split it
                        var parenMatch = productName.match(/^(.+?)\s*\(([^)]+)\)$/);
                        if (parenMatch) {
                            var mainTitle = parenMatch[1].trim();
                            var parenthetical = parenMatch[2].trim();
                            title.innerHTML = mainTitle + '<span class="parenthetical">(' + parenthetical + ')</span>';
                        } else {
                            title.textContent = productName;
                        }
                        
                        title.style.cssText = 'font-weight:600;font-size:22px;margin-bottom:8px;line-height:1.3;text-align:left';
                        contentContainer.appendChild(title);
                        
                        // Price
                        var priceDiv = el('div', {className: 'tpb-card-price'});
                        priceDiv.innerHTML = p.price_html || '';
                        priceDiv.style.cssText = 'color:rgb(79 176 137);font-weight:600;font-size:20px;padding:8px 0;margin-bottom:16px;text-align:left';
                        contentContainer.appendChild(priceDiv);
                        
                        // Bottom section with quantity and actions
                        var bottomSection = el('div', {className: 'tpb-bottom-section'});
                        bottomSection.style.cssText = 'display:flex;flex-direction:column;gap:12px;width:100%;min-width:0';
                        
                        // Quantity selector
                        var qtyDiv = el('div', {className: 'tpb-qty'});
                        qtyDiv.style.cssText = 'display:flex;align-items:center;gap:8px';
                        qtyDiv.innerHTML = '<span>Qty:</span><input type="number" value="1" min="1" style="width:60px;padding:4px;border:1px solid #ddd;border-radius:4px">';
                        bottomSection.appendChild(qtyDiv);
                        
                        // Action buttons
                        var actions = el('div', {className: 'tpb-actions'});
                        actions.style.cssText = 'display:flex;gap:16px;justify-content:flex-start;flex-wrap:nowrap;margin-top:8px;width:100%;min-width:0';
                        
                        var addToCartBtn = el('button', {text: 'Add to cart'});
                        addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                        addToCartBtn.addEventListener('click', function() { addCart(p.id, 1, this); });
                        
                        var addToQuoteBtn = el('button', {text: 'Add to quote'});
                        addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                        addToQuoteBtn.addEventListener('click', function() { addQuote(p.id, this); });
                        
                        actions.appendChild(addToCartBtn);
                        actions.appendChild(addToQuoteBtn);
                        bottomSection.appendChild(actions);
                        contentContainer.appendChild(bottomSection);
                        card.appendChild(contentContainer);
                    } else {
                        // For Pre-designed, use vertical layout
                        card.className = 'tpb-card';
                        
                        // Image container with aspect ratio
                        var imgContainer = el('div', {className: 'tpb-card-image'});
                        imgContainer.style.cssText = 'position:relative;padding-bottom:75%;overflow:hidden;border-radius:8px;margin-bottom:12px';
                        var img = el('img');
                        img.alt = p.name || '';
                        img.src = p.image_url || '';
                        img.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover';
                        imgContainer.appendChild(img);
                        card.appendChild(imgContainer);
                        
                        // Product title with parenthetical text handling
                        var title = el('div', {className: 'tpb-card-title'});
                        var productName = p.name || '';
                        
                        // Check for parenthetical text and split it
                        var parenMatch = productName.match(/^(.+?)\s*\(([^)]+)\)$/);
                        if (parenMatch) {
                            var mainTitle = parenMatch[1].trim();
                            var parenthetical = parenMatch[2].trim();
                            title.innerHTML = mainTitle + '<span class="parenthetical">(' + parenthetical + ')</span>';
                        } else {
                            title.textContent = productName;
                        }
                        
                        title.style.cssText = 'font-weight:600;font-size:16px;margin-bottom:8px;min-height:40px;line-height:1.3;text-align:center';
                        card.appendChild(title);
                        
                        // Price
                        var priceDiv = el('div', {className: 'tpb-card-price'});
                        priceDiv.innerHTML = p.price_html || '';
                        priceDiv.style.cssText = 'color:rgb(79 176 137);font-weight:600;font-size:16px;padding-top:10px;padding-bottom:10px;margin-bottom:12px;text-align:center';
                        card.appendChild(priceDiv);
                        
                        // Quantity selector
                        var qtyDiv = el('div', {className: 'tpb-qty'});
                        qtyDiv.style.cssText = 'display:flex;align-items:center;gap:8px;justify-content:center';
                        qtyDiv.innerHTML = '<span>Qty:</span><input type="number" value="1" min="1" style="width:60px;padding:4px;border:1px solid #ddd;border-radius:4px">';
                        card.appendChild(qtyDiv);
                        
                        // Action buttons
                        var actions = el('div', {className: 'tpb-actions'});
                        actions.style.cssText = 'display:flex;gap:8px;margin-top:12px;align-items:center;justify-content:flex-start;flex-wrap:wrap';
                        
                        var addToCartBtn = el('button', {text: 'Add to cart'});
                        addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                        addToCartBtn.addEventListener('click', function() { addCart(p.id, 1, this); });
                        
                        var addToQuoteBtn = el('button', {text: 'Add to quote'});
                        addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                        addToQuoteBtn.addEventListener('click', function() { addQuote(p.id, this); });
                        
                        actions.appendChild(addToCartBtn);
                        actions.appendChild(addToQuoteBtn);
                        card.appendChild(actions);
                    }
                    
                    grid.appendChild(card);
                    
                    // Add entrance animation for horizontal cards in Custom Build mode
                    if (state.strategy === 'custom') {
                        card.style.opacity = '0';
                        card.style.transform = 'translateY(15px)';
                        card.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
                        
                        // Trigger entrance animation after a brief delay
                        setTimeout(function() {
                            card.style.opacity = '1';
                            card.style.transform = 'translateY(0)';
                        }, 50);
                    }
                });
                focusResults();
                // Auto-scroll to show new content after cards are loaded (only if shouldScroll is true)
                if (state.shouldScroll) {
                    setTimeout(function() {
                        scrollToShowContent(s3);
                    }, 300);
                } else {
                    // Lock scroll position during all DOM updates
                    var modalContent = document.querySelector('.tpb-qv-right-panel');
                    if (modalContent && window.tpbSavedScrollPosition !== undefined) {
                        console.log('Locking scroll position at:', window.tpbSavedScrollPosition);
                        
                        // Create a scroll lock that maintains position
                        var lockScroll = function() {
                            modalContent.scrollTop = window.tpbSavedScrollPosition;
                        };
                        
                        // Lock scroll continuously for 500ms
                        var startTime = Date.now();
                        var lockInterval = setInterval(function() {
                            lockScroll();
                            if (Date.now() - startTime > 500) {
                                clearInterval(lockInterval);
                                console.log('Released scroll lock after 500ms');
                            }
                        }, 10); // Lock every 10ms
                        
                        // Also restore after delays
                        setTimeout(lockScroll, 100);
                        setTimeout(lockScroll, 200);
                        setTimeout(lockScroll, 400);
                    }
                }
            }).catch(function() { 
                console.log('🌸 API failed, using mock data for testing');
                var items = mockData[state.strategy] || [];
                if (items.length) {
                    root.classList.add('has-results');
                    items.forEach(function(p) {
                        // [Same product rendering code as above]
                        var card = el('div', {className: 'tpb-card'});
                        
                        if (state.strategy === 'custom') {
                            // For Custom Build, use horizontal layout with separate class
                            card.className = 'tpb-card tpb-card-horizontal';
                            card.style.cssText = 'display:flex!important;gap:24px!important;align-items:flex-start!important;padding:24px!important;border:1px solid #e3e6ea!important;border-radius:12px!important;background:#fff!important;transition:box-shadow 0.2s,transform 0.2s!important;text-align:left!important;width:100%!important;max-width:none!important;box-sizing:border-box!important;min-height:200px!important;overflow:visible!important;flex-shrink:0!important';
                            
                            // Image container - left side
                            var imgContainer = el('div', {className: 'tpb-card-image'});
                            imgContainer.style.cssText = 'flex:0 0 200px;width:200px;height:200px;position:relative;overflow:hidden;border-radius:8px';
                            var img = el('img');
                            img.alt = p.name || '';
                            img.src = p.image_url || '';
                            img.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover';
                            imgContainer.appendChild(img);
                            card.appendChild(imgContainer);
                            
                            // Content container - right side
                            var contentContainer = el('div', {className: 'tpb-card-content'});
                            contentContainer.style.cssText = 'flex:1;display:flex;flex-direction:column;justify-content:space-between;min-height:180px;padding-left:8px;min-width:0;width:100%';
                            
                            // Product title with parenthetical text handling
                            var title = el('div', {className: 'tpb-card-title'});
                            var productName = p.name || '';
                            
                            // Check for parenthetical text and split it
                            var parenMatch = productName.match(/^(.+?)\s*\(([^)]+)\)$/);
                            if (parenMatch) {
                                var mainTitle = parenMatch[1].trim();
                                var parenthetical = parenMatch[2].trim();
                                title.innerHTML = mainTitle + '<span class="parenthetical">(' + parenthetical + ')</span>';
                            } else {
                                title.textContent = productName;
                            }
                            
                            title.style.cssText = 'font-weight:600;font-size:22px;margin-bottom:8px;line-height:1.3;text-align:left';
                            contentContainer.appendChild(title);
                            
                            // Price
                            var priceDiv = el('div', {className: 'tpb-card-price'});
                            priceDiv.innerHTML = p.price_html || '';
                            priceDiv.style.cssText = 'color:rgb(79 176 137);font-weight:600;font-size:20px;padding:8px 0;margin-bottom:16px;text-align:left';
                            contentContainer.appendChild(priceDiv);
                            
                            // Bottom section with quantity and actions
                            var bottomSection = el('div', {className: 'tpb-bottom-section'});
                            bottomSection.style.cssText = 'display:flex;flex-direction:column;gap:12px;width:100%;min-width:0';
                            
                            // Quantity selector
                            var qtyDiv = el('div', {className: 'tpb-qty'});
                            qtyDiv.style.cssText = 'display:flex;align-items:center;gap:8px';
                            qtyDiv.innerHTML = '<span>Qty:</span><input type="number" value="1" min="1" style="width:60px;padding:4px;border:1px solid #ddd;border-radius:4px">';
                            bottomSection.appendChild(qtyDiv);
                            
                            // Action buttons
                            var actions = el('div', {className: 'tpb-actions'});
                            actions.style.cssText = 'display:flex;gap:16px;justify-content:flex-start;flex-wrap:nowrap;margin-top:8px;width:100%;min-width:0';
                            
                            var addToCartBtn = el('button', {text: 'Add to cart'});
                            addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                            addToCartBtn.addEventListener('click', function() { addCart(p.id, 1, this); });
                            
                            var addToQuoteBtn = el('button', {text: 'Add to quote'});
                            addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                            addToQuoteBtn.addEventListener('click', function() { addQuote(p.id, this); });
                            
                            actions.appendChild(addToCartBtn);
                            actions.appendChild(addToQuoteBtn);
                            bottomSection.appendChild(actions);
                            contentContainer.appendChild(bottomSection);
                            card.appendChild(contentContainer);
                        } else {
                            // For Pre-designed, use vertical layout
                            card.className = 'tpb-card';
                            
                            // Image container with aspect ratio
                            var imgContainer = el('div', {className: 'tpb-card-image'});
                            imgContainer.style.cssText = 'position:relative;padding-bottom:75%;overflow:hidden;border-radius:8px;margin-bottom:12px';
                            var img = el('img');
                            img.alt = p.name || '';
                            img.src = p.image_url || '';
                            img.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover';
                            imgContainer.appendChild(img);
                            card.appendChild(imgContainer);
                            
                            // Product title with parenthetical text handling
                            var title = el('div', {className: 'tpb-card-title'});
                            var productName = p.name || '';
                            
                            // Check for parenthetical text and split it
                            var parenMatch = productName.match(/^(.+?)\s*\(([^)]+)\)$/);
                            if (parenMatch) {
                                var mainTitle = parenMatch[1].trim();
                                var parenthetical = parenMatch[2].trim();
                                title.innerHTML = mainTitle + '<span class="parenthetical">(' + parenthetical + ')</span>';
                            } else {
                                title.textContent = productName;
                            }
                            
                            title.style.cssText = 'font-weight:600;font-size:16px;margin-bottom:8px;min-height:40px;line-height:1.3;text-align:center';
                            card.appendChild(title);
                            
                            // Price
                            var priceDiv = el('div', {className: 'tpb-card-price'});
                            priceDiv.innerHTML = p.price_html || '';
                            priceDiv.style.cssText = 'color:rgb(79 176 137);font-weight:600;font-size:16px;padding-top:10px;padding-bottom:10px;margin-bottom:12px;text-align:center';
                            card.appendChild(priceDiv);
                            
                            // Quantity selector
                            var qtyDiv = el('div', {className: 'tpb-qty'});
                            qtyDiv.style.cssText = 'display:flex;align-items:center;gap:8px;justify-content:center';
                            qtyDiv.innerHTML = '<span>Qty:</span><input type="number" value="1" min="1" style="width:60px;padding:4px;border:1px solid #ddd;border-radius:4px">';
                            card.appendChild(qtyDiv);
                            
                            // Action buttons
                            var actions = el('div', {className: 'tpb-actions'});
                            actions.style.cssText = 'display:flex;gap:8px;margin-top:12px;align-items:center;justify-content:flex-start;flex-wrap:wrap';
                            
                            var addToCartBtn = el('button', {text: 'Add to cart'});
                            addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                            addToCartBtn.addEventListener('click', function() { addCart(p.id, 1, this); });
                            
                            var addToQuoteBtn = el('button', {text: 'Add to quote'});
                            addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
                            addToQuoteBtn.addEventListener('click', function() { addQuote(p.id, this); });
                            
                            actions.appendChild(addToCartBtn);
                            actions.appendChild(addToQuoteBtn);
                            card.appendChild(actions);
                        }
                        
                        grid.appendChild(card);
                        
                        // Add entrance animation for horizontal cards in Custom Build mode
                        if (state.strategy === 'custom') {
                            card.style.opacity = '0';
                            card.style.transform = 'translateY(15px)';
                            card.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
                            
                            // Trigger entrance animation after a brief delay
                            setTimeout(function() {
                                card.style.opacity = '1';
                                card.style.transform = 'translateY(0)';
                            }, 50);
                        }
                    });
                    focusResults();
                    // Auto-scroll to show new content after cards are loaded
                    setTimeout(function() {
                        scrollToShowContent(s3);
                    }, 300);
                } else {
                    root.classList.remove('has-results');
                }
            });
        }
        
        // Single event listener for SKU selection
        sel.addEventListener('change', function() {
            // Only hide/show Step 2 when transitioning between no SKU and SKU
            var hadSku = !!state.sku;
            var hasSku = !!this.value;
            
            if (!hadSku && hasSku) {
                // Transitioning from no SKU to SKU - show Step 2 with animation
                s2.hidden = false;
                s2.classList.add('visible');
                s2.classList.add('entering');
                setTimeout(function() {
                    s2.classList.remove('entering');
                    // Auto-scroll to show new content
                    scrollToShowContent(s2);
                }, 400);
            } else if (hadSku && !hasSku) {
                // Transitioning from SKU to no SKU - hide Step 2
                s2.hidden = true;
                state.strategy = null;
                s3.hidden = true;
            } else if (hasSku) {
                // Already had SKU and still has SKU - keep Step 2 visible, no animation
                s2.hidden = false;
                s2.classList.add('visible');
            }
            
            // Update state
            state.sku = this.value || null;
            
            // Clear any pending price update
            if (state.priceUpdateTimeout) {
                clearTimeout(state.priceUpdateTimeout);
            }
            
            // Fetch hardware-only kit price for this SKU
            if (state.sku) {
                fetchProducts({
                    tags: [state.sku + '-sku'],
                    sort: 'price_asc',
                    limit: 1
                }).then(function(d) {
                    var items = Array.isArray(d) ? d : (d && Array.isArray(d.data) ? d.data : (d && Array.isArray(d.results) ? d.results : []));
                    if (items.length && items[0].price_html) {
                        state.hardwarePrice = items[0].price_html;
                        // Send to parent modal with slight delay to prevent double animation
                        state.priceUpdateTimeout = setTimeout(function() {
                            // Update the base price in the modal with fade transition
                            var priceEl = document.getElementById('tpb-qv-base-price');
                            if (priceEl) {
                                // Strip any color styling from price HTML to prevent color flashing
                                var cleanPriceHtml = items[0].price_html.replace(/style="[^"]*"/gi, '').replace(/<span[^>]*>/gi, '<span>');
                                var newContent = 'Base Price:  ' + cleanPriceHtml;
                                
                                // Fade out first
                                priceEl.style.transition = 'opacity ' + (560/2) + 'ms ease-in-out';
                                priceEl.style.opacity = '0';
                                
                                setTimeout(function() {
                                    // Change content AND class only when completely invisible
                                    priceEl.innerHTML = newContent;
                                    priceEl.classList.add('has-price');
                                    
                                    // Small delay to ensure content is set before fading in
                                    setTimeout(function() {
                                        priceEl.style.opacity = '1';
                                    }, 10);
                                }, 560/2);
                            }
                        }, 50);
                    }
                });
            } else {
                state.hardwarePrice = null;
                // Reset price in parent with slight delay
                state.priceUpdateTimeout = setTimeout(function() {
                    var priceEl = document.getElementById('tpb-qv-base-price');
                    if (priceEl) {
                        var newContent = 'Please choose a SKU-count to see the base-price for electronics hardware.<br>Furniture-inclusive pricing will appear below according to your selections.';
                        
                        // Fade out first
                        priceEl.style.transition = 'opacity ' + (560/2) + 'ms ease-in-out';
                        priceEl.style.opacity = '0';
                        
                        setTimeout(function() {
                            // Change content AND class only when completely invisible
                            priceEl.innerHTML = newContent;
                            priceEl.classList.remove('has-price');
                            
                            // Small delay to ensure content is set before fading in
                            setTimeout(function() {
                                priceEl.style.opacity = '1';
                            }, 10);
                        }, 560/2);
                    }
                }, 50);
            }
            
            update();
        });
        
        // Single event listener for strategy selection
        r.addEventListener('change', function(e) {
            if (e.target && e.target.name === 'tpb-strategy') {
                // Save current scroll position IMMEDIATELY before doing anything else
                var modalContent = document.querySelector('.tpb-qv-right-panel');
                var scrollPosition = modalContent ? modalContent.scrollTop : 0;
                window.tpbSavedScrollPosition = scrollPosition;
                console.log('Strategy change - saved scroll position:', scrollPosition);
                
                // Only hide/show Step 3 when transitioning between no strategy and strategy
                var hadStrategy = !!state.strategy;
                var hasStrategy = !!e.target.value;
                
                if (!hadStrategy && hasStrategy) {
                    // Transitioning from no strategy to strategy - show Step 3 with animation
                    s3.hidden = false;
                    s3.classList.add('visible');
                    s3.classList.add('entering');
                    setTimeout(function() {
                        s3.classList.remove('entering');
                        // Auto-scroll to show new content
                        scrollToShowContent(s3);
                    }, 400);
                    
                    // Update state and use updateProductsOnly() to prevent Step 1 reload
                    state.strategy = e.target.value;
                    state.shouldScroll = true; // Allow scroll when first selecting a strategy
                    updateProductsOnly();
                    return; // Exit early to prevent calling update()
                } else if (hadStrategy && !hasStrategy) {
                    // Transitioning from strategy to no strategy - hide Step 3
                    s3.hidden = true;
                    state.strategy = e.target.value;
                    return; // Exit early to prevent calling update()
                } else if (hasStrategy) {
                    // Already had strategy and still has strategy - just update products without rebuilding
                    s3.hidden = false;
                    s3.classList.add('visible');
                    
                    // Update state first
                    state.strategy = e.target.value;
                    state.shouldScroll = false; // Don't scroll when switching strategies
                    
                    // Update products directly without calling update()
                    updateProductsOnly();
                    return; // Exit early to prevent calling update()
                }
                
                // This should never be reached now, but keeping as fallback
                state.strategy = e.target.value;
                update();
            }
        });
        
        root.appendChild(s1);
        // Trigger step 1 entrance animation on initial load
        setTimeout(function() {
            s1.hidden = false; // Show the element first
            s1.classList.add('visible');
            // Force immediate visibility before animation
            s1.style.opacity = '1';
            s1.style.transform = 'translateY(0)';
            s1.classList.add('entering');
            setTimeout(function() {
                s1.classList.remove('entering');
                // Auto-scroll to show new content
                scrollToShowContent(s1);
            }.bind(this), 400);
        }.bind(this), 50);
        root.appendChild(s2);
        root.appendChild(s3);
        
    }
    
    // Fade transition function for smooth content changes
    function fadeTransition(element, newContent, duration = 420) { // 40% slower (300 * 1.4)
        // Clear any existing transitions
        element.style.transition = 'none';
        
        // Fade out
        element.style.transition = 'opacity ' + (duration/2) + 'ms ease-in-out';
        element.style.opacity = '0';
        
        setTimeout(function() {
            // Change content only when completely invisible (opacity = 0)
            element.innerHTML = newContent;
            
            // Small delay to ensure content is set before fading in
            setTimeout(function() {
                element.style.opacity = '1';
            }, 10);
        }, duration/2);
    }

    // Custom smooth scroll function for ultra-smooth scrolling
    function smoothScrollTo(container, targetTop, duration = 2000) {
        const startTop = container.scrollTop;
        const distance = targetTop - startTop;
        const startTime = performance.now();
        
        // Use a very gentle easing function for subtle, smooth feel
        function easeOutCubic(t) {
            return 1 - Math.pow(1 - t, 3);
        }
        
        function animateScroll(currentTime) {
            const elapsed = currentTime - startTime;
            const progress = Math.min(elapsed / duration, 1);
            const easedProgress = easeOutCubic(progress);
            
            container.scrollTop = startTop + (distance * easedProgress);
            
            if (progress < 1) {
                requestAnimationFrame(animateScroll);
            }
        }
        
        requestAnimationFrame(animateScroll);
    }

    // Auto-scroll function to show new content
    function scrollToShowContent(element) {
        // Immediate scroll for better responsiveness
        setTimeout(function() {
            // Find the modal container
            const modal = element.closest('.tpb-qv-modal');
            if (!modal) return;
            
            // Try multiple scroll containers in order of preference
            let scrollContainer = modal.querySelector('.tpb-qv-right-panel');
            if (!scrollContainer || scrollContainer.scrollHeight <= scrollContainer.clientHeight) {
                // Try the modal itself
                scrollContainer = modal;
            }
            
            if (!scrollContainer) return;
            
            // Calculate if element is visible in viewport
            const elementRect = element.getBoundingClientRect();
            const containerRect = scrollContainer.getBoundingClientRect();
            
            // Check if element is below the visible area
            if (elementRect.bottom > containerRect.bottom) {
                // Try native scrollIntoView first (most reliable) - just enough to show element
                try {
                    element.scrollIntoView({
                        behavior: 'smooth',
                        block: 'nearest',
                        inline: 'nearest'
                    });
                    return;
                } catch (e) {
                    // Fallback to manual scroll calculation
                }
                
                // Fallback to manual scroll calculation - just enough to show the element
                const elementTop = elementRect.top;
                const containerTop = containerRect.top;
                const scrollTop = scrollContainer.scrollTop + (elementTop - containerTop) - 20; // Just 20px padding
                
                // Use custom smooth scroll for ultra-smooth experience
                smoothScrollTo(scrollContainer, scrollTop, 2000);
            }
        }, 5); // Minimal delay for early scroll start
    }
    
    // Expose the function globally
    window.TPBFlowerStepper = {
        build: buildFlowerStepper
    };
    
    console.log('🌸 Flower Station Modal Stepper loaded');
    
})(jQuery);
