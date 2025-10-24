/**
 * TPB Quick Checkout Stepper Modal
 * Handles the Quick Checkout modal configuration with vertical product cards
 */

(function() {
  'use strict';
  
  // Utility functions
  function el(tag, attrs) {
    var e = document.createElement(tag);
    if (attrs) {
      Object.keys(attrs).forEach(function(k) {
        if (k === 'className') e.className = attrs[k];
        else if (k === 'textContent') e.textContent = attrs[k];
        else if (k === 'innerHTML') e.innerHTML = attrs[k];
        else e.setAttribute(k, attrs[k]);
      });
    }
    return e;
  }
  
  function clear(el) {
    while (el.firstChild) el.removeChild(el.firstChild);
  }
  
  function toast(msg, type) {
    var t = el('div', {className: 'tpb-toast ' + (type || 'success'), textContent: msg});
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
  
  // Product fetching functions
  function fetchProducts(p) {
    console.log('Quick Checkout - fetchProducts() called with params:', p);
    var ajaxParams = new URLSearchParams();
    if (Array.isArray(p.tags) && p.tags.length) ajaxParams.set('tags', p.tags.join(','));
    if (Array.isArray(p.categories) && p.categories.length) ajaxParams.set('categories', p.categories.join(','));
    if (Array.isArray(p.exclude_categories) && p.exclude_categories.length) ajaxParams.set('exclude_categories', p.exclude_categories.join(','));
    if (p.limit) ajaxParams.set('limit', String(p.limit));
    if (p.sort) ajaxParams.set('sort', p.sort);
    
    var url = '/wp-admin/admin-ajax.php?action=tpb_qv_products&' + ajaxParams.toString();
    console.log('Quick Checkout - fetchProducts() URL:', url);
    
    return fetch(url, { credentials: 'same-origin' })
      .then(function(r) {
        console.log('Quick Checkout - fetchProducts() response status:', r.status);
        if (!r.ok) throw new Error('AJAX ' + r.status);
        return r.json();
      });
  }
  
  function addCart(id, q, btn) {
    if (btn) { 
      btn.disabled = true; 
      btn.textContent = 'Adding...';
    }
    
    // Use original working method
    return fetch('/?add-to-cart=' + encodeURIComponent(id) + (q ? ('&quantity=' + encodeURIComponent(q)) : ''), { credentials: 'same-origin' })
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
    // Try to update cart count in various cart widgets
    const cartCounts = document.querySelectorAll('.cart-count, .cart-counter, .woocommerce-cart-count');
    cartCounts.forEach(function(count) {
      // Trigger a refresh of the cart widget
      if (typeof jQuery !== 'undefined') {
        jQuery(count).trigger('wc_fragment_refresh');
      }
    });
    
    // Also try to refresh the entire cart widget
    const cartWidgets = document.querySelectorAll('.woocommerce-cart-widget, .cart-widget');
    cartWidgets.forEach(function(widget) {
      if (typeof jQuery !== 'undefined') {
        jQuery(widget).trigger('wc_fragment_refresh');
      }
    });
  }
  
  function addQuote(id, btn) {
    if (btn) btn.disabled = true;
    return fetch('/?add-to-quote=' + encodeURIComponent(id), { credentials: 'same-origin' })
      .then(function() { toast('Added to quote', 'success'); })
      .catch(function() { toast('Add to quote failed', 'error'); })
      .finally(function() { if (btn) btn.disabled = false; });
  }
  
  // Main stepper implementation
  function build() {
    console.log('Quick Checkout stepper script loaded');
    
    // Check if we're in the Quick Checkout modal specifically
    var quickcheckoutModal = document.querySelector('#tpb-qv-quickcheckout-modal');
    if (!quickcheckoutModal || quickcheckoutModal.style.display === 'none') {
      console.log('Quick Checkout stepper - not in modal or modal is hidden, skipping initialization');
      return;
    }
    console.log('Quick Checkout stepper - in modal/iframe mode, proceeding with initialization');
    
    // Look for the stepper container in the Quick Checkout modal
    var stepperContainer = document.querySelector('#tpb-qv-quickcheckout-modal #tpb-qv-stepper-container');
    if (!stepperContainer) {
      console.error('❌ Quick Checkout stepper container not found');
      return;
    }
    
    console.log('📦 Found stepper container:', stepperContainer);
    
    // Clear any existing content
    stepperContainer.innerHTML = '';
    
    // Create the native stepper div
    var root = el('div', { className: 'tpb-qv-native' });
    root.id = 'tpb-qv-native';
    root.setAttribute('role', 'region');
    root.setAttribute('aria-label', 'Quick Checkout Configurator');
    
    // Insert into the stepper container
    stepperContainer.appendChild(root);
    console.log('📦 Stepper div created and appended to container');
    
    clear(root);
    var live = el('div', { className: 'sr-live' });
    live.setAttribute('aria-live', 'polite');
    root.appendChild(live);
    
    // Inject CSS with cache busting
    var css = el('style');
    css.id = 'tpb-quickcheckout-css-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    css.textContent = `
      .tpb-qv-stepper-container .tpb-qv-native {
        font-family: inherit;
        padding: 20px;
        max-width: 90%;
        box-sizing: border-box;
        overflow-x: hidden;
        width: 100%;
        color: #333 !important;
      }
      
      .tpb-qv-native {
        font-family: inherit;
        padding: 20px;
        max-width: 90%;
        box-sizing: border-box;
        overflow-x: hidden;
        width: 100%;
        color: #333 !important;
      }
      
      .tpb-step {
        margin: 0;
        padding: 40px 0 0px 40px;
        max-width: 100%;
        box-sizing: border-box;
        border: none !important;
        border-bottom: none !important;
        border-top: none !important;
        border-left: none !important;
        border-right: none !important;
        color: #333 !important;
      }
      
      .tpb-grid {
        display: grid !important;
        grid-template-columns: repeat(2, 1fr) !important;
        margin-top: 35px !important;
        max-width: 100% !important;
        box-sizing: border-box !important;
        gap: 20px !important;
      }
      
      .tpb-qv-modal .tpb-card {
        border: 1px solid #e3e6ea;
        border-radius: 12px;
        padding: 16px;
        background: #fff;
        transition: box-shadow 0.2s, transform 0.2s;
        text-align: left;
        max-width: 100% !important;
        box-sizing: border-box;
        color: #333 !important;
        transform: scale(0.85) !important;
        transform-origin: top left !important;
      }
      
      .tpb-qv-modal .tpb-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        transform: scale(0.85) translateY(-2px) !important;
      }
      
      .tpb-qv-modal .tpb-card-image {
        position: relative;
        padding-bottom: 75%;
        overflow: hidden;
        border-radius: 8px;
        margin-bottom: 12px;
      }
      
      .tpb-qv-modal .tpb-card-image img {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        object-fit: cover;
      }
      
      .tpb-qv-modal .tpb-card-title {
        font-weight: 600;
        font-size: 16px;
        margin-bottom: 8px;
        min-height: 40px;
        line-height: 1.3;
        text-align: center;
        color: #333 !important;
      }
      
      .tpb-qv-modal .tpb-card-price {
        color: rgb(79 176 137) !important;
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
        color: #333 !important;
        width: 100%;
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
        min-width: 100px;
        color: white !important;
        text-align: center;
        display: inline-block;
        text-decoration: none;
        line-height: 1.2;
        box-sizing: border-box;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
      }
      
      .tpb-btn-primary {
        background: rgb(79, 176, 137) !important;
        color: white !important;
      }
      
      .tpb-btn-primary:hover {
        background: rgb(60, 140, 110) !important;
        color: white !important;
      }
      
      .tpb-btn-secondary {
        background: rgb(79, 176, 137) !important;
        color: white !important;
      }
      
      .tpb-btn-secondary:hover {
        background: rgb(60, 140, 110) !important;
        color: white !important;
      }
      
      .tpb-hint {
        color: #6b7280 !important;
        font-size: 15px;
        margin: 4px 0 8px;
        text-align: left;
      }
      
      .tpb-title {
        font-weight: 700;
        font-size: 18px;
        margin: 8px 0;
        text-align: left;
        color: #333 !important;
      }
      
      .tpb-empty-state {
        text-align: center;
        padding: 40px 20px;
        color: #6b7280;
        font-size: 16px;
        line-height: 1.5;
        background: #f8f9fa;
        border-radius: 8px;
        margin: 20px 0;
        border: 1px solid #e3e6ea;
      }
      
      .sr-live {
        position: absolute;
        left: -9999px;
        top: auto;
        width: 1px;
        height: 1px;
        overflow: hidden;
      }
      
      .tpb-toast {
        position: fixed;
        right: 12px;
        bottom: 12px;
        background: #111827;
        color: #fff;
        padding: 10px 14px;
        border-radius: 8px;
        opacity: .95;
        z-index: 99999;
      }
    `;
    
    // Remove any existing CSS with the same ID pattern
    var existingCss = document.querySelectorAll('style[id^="tpb-quickcheckout-css-"]');
    for (var i = 0; i < existingCss.length; i++) {
      existingCss[i].remove();
    }
    
    document.head.appendChild(css);
    
    // Build the stepper interface
    buildStepper();
    
    function buildStepper() {
      console.log('Quick Checkout - Building stepper interface');
      
      // Create the main step
      var step = el('div', { className: 'tpb-step' });
      step.innerHTML = `
        <div class="tpb-grid" id="tpb-products-grid">
          <!-- Products will be loaded here -->
        </div>
      `;
      
      root.appendChild(step);
      
      // Load products
      loadProducts();
    }
    
    function loadProducts() {
      console.log('Quick Checkout - Loading products');
      
      // Load the two specific Quick Checkout products
      var products = [
        {
          id: 2190,
          name: '27"Quick Checkout Station',
          price_html: '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>1,040.00</bdi></span>',
          image_url: 'http://the-peak-beyond-modal.local/wp-content/uploads/2025/04/27-qco-hk-300x300.jpg',
          description: 'Electronics components for a 27" Quick Checkout Station. Mounting type subject to customer\'s desired use case and purchased separately.'
        },
        {
          id: 2189,
          name: '22"Quick Checkout Station',
          price_html: '<span class="woocommerce-Price-amount amount"><bdi><span class="woocommerce-Price-currencySymbol">&#36;</span>820.00</bdi></span>',
          image_url: 'http://the-peak-beyond-modal.local/wp-content/uploads/2025/04/22-qco-hk-300x300.jpg',
          description: 'Electronics components for a 22" Quick Checkout Station. Mounting type subject to customer\'s desired use case and purchased separately.'
        }
      ];
      
      var grid = document.getElementById('tpb-products-grid');
      if (!grid) {
        console.error('Quick Checkout - Products grid not found');
        return;
      }
      
      // Clear existing content
      grid.innerHTML = '';
      
      // Render products
      products.forEach(function(product) {
        var card = el('div', { className: 'tpb-card' });
        
        // Image
        var imgContainer = el('div', { className: 'tpb-card-image' });
        var img = el('img');
        img.alt = product.name || '';
        img.src = product.image_url || '';
        img.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover';
        imgContainer.appendChild(img);
        card.appendChild(imgContainer);
        
        // Title
        var title = el('div', { className: 'tpb-card-title' });
        title.textContent = product.name || '';
        card.appendChild(title);
        
        // Price
        var priceDiv = el('div', { className: 'tpb-card-price' });
        priceDiv.innerHTML = product.price_html || '';
        card.appendChild(priceDiv);
        
        // Actions
        var actions = el('div', { className: 'tpb-actions' });
        
        var addToCartBtn = el('button', { className: 'tpb-btn tpb-btn-primary' });
        addToCartBtn.textContent = 'Add to cart';
        addToCartBtn.addEventListener('click', function() { addCart(product.id, 1, this); });
        
        var addToQuoteBtn = el('button', { className: 'tpb-btn tpb-btn-secondary' });
        addToQuoteBtn.textContent = 'Add to quote';
        addToQuoteBtn.addEventListener('click', function() { addQuote(product.id, this); });
        
        actions.appendChild(addToCartBtn);
        actions.appendChild(addToQuoteBtn);
        card.appendChild(actions);
        
        grid.appendChild(card);
      });
      
      console.log('Quick Checkout - Products loaded:', products.length);
    }
  }
  
  // Initialize when DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', build);
  } else {
    build();
  }
})();
