/**
 * Category Station Stepper Modal
 * Complete stepper implementation for Category Station modals
 * Based on commit history and requirements
 */

(function(){
  'use strict';
  
  // Utility functions
  function $(s,c){ return (c||document).querySelector(s); }
  function el(t,o){ 
    var n=document.createElement(t); 
    if(o){ 
      if(o.className) n.className=o.className; 
      if(o.text) n.textContent=o.text; 
      if(o.html) n.innerHTML=o.html; 
    } 
    return n; 
  }
  function clear(n){ while(n&&n.firstChild) n.removeChild(n.firstChild); }
  function hideLegacy(){ 
    ['.af_cp_all_components_content','.afcpb-wrapper','.af_cp_content'].forEach(function(sel){ 
      var c=$(sel); 
      if(c){ c.style.display='none'; } 
    }); 
  }
  function resize(){ 
    try{ 
      var h=document.body.scrollHeight; 
      if(window.parent&&window.parent!==window){ 
        window.parent.postMessage({ type:'TPB_QV', action:'resize', height:h }, '*'); 
      } 
    }catch(e){} 
  }
  function toast(m, type){ 
    var t=el('div',{className:'tpb-toast ' + (type || 'success'),text:m}); 
    t.setAttribute('role','status'); 
    t.setAttribute('aria-live','polite'); 
    document.body.appendChild(t); 
    
    // Add fade out animation before removing
    setTimeout(function(){ 
      if(t) {
        t.classList.add('fade-out');
        setTimeout(function() {
          if(t&&t.remove) t.remove(); 
        }, 300);
      }
    }, 1500); 
  }
  
  // Product fetching functions
  function fetchProducts(p){
    console.log('Category Stations - fetchProducts() called with params:', p);
    var ajaxParams = new URLSearchParams();
    if (Array.isArray(p.tags) && p.tags.length) ajaxParams.set('tags', p.tags.join(','));
    if (Array.isArray(p.categories) && p.categories.length) ajaxParams.set('categories', p.categories.join(','));
    if (Array.isArray(p.exclude_categories) && p.exclude_categories.length) ajaxParams.set('exclude_categories', p.exclude_categories.join(','));
    if (p.limit) ajaxParams.set('limit', String(p.limit));
    if (p.sort) ajaxParams.set('sort', p.sort);
    
    var url = '/wp-admin/admin-ajax.php?action=tpb_qv_products&'+ajaxParams.toString();
    console.log('Category Stations - fetchProducts() URL:', url);
    
    return fetch(url, { credentials:'same-origin' })
      .then(function(r){ 
        console.log('Category Stations - fetchProducts() response status:', r.status);
        if(!r.ok) throw new Error('AJAX '+r.status); 
        return r.json();
      });
  }
  
  function addCart(id,q,btn){ 
    if(btn){ 
      btn.disabled=true; 
      btn.textContent='Adding...';
    } 
    
    // Use original working method
    return fetch('/?add-to-cart='+encodeURIComponent(id)+(q?('&quantity='+encodeURIComponent(q)):'') ,{credentials:'same-origin'})
      .then(function(){ 
        toast('Added to cart!', 'success');
        // Update cart count if cart widget is present
        if (typeof window.tpbUpdateCart === 'function') {
          window.tpbUpdateCart();
        } else {
          updateCartCount();
        }
      })
      .catch(function(){ 
        toast('Add to cart failed', 'error'); 
      })
      .finally(function(){ 
        if(btn){ 
          btn.disabled=false; 
          btn.textContent='Add to cart';
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
  }
  
  function addQuote(id,btn){ 
    if(btn){ btn.disabled=true; } 
    return fetch('/?add-to-quote='+encodeURIComponent(id),{credentials:'same-origin'})
      .then(function(){ toast('Added to quote', 'success'); })
      .catch(function(){ toast('Add to quote failed', 'error'); })
      .finally(function(){ if(btn){ btn.disabled=false; } }); 
  }
  
  // Auto-scroll functions
  function smoothScrollTo(container, targetTop, duration = 2000) {
    const startTop = container.scrollTop;
    const distance = targetTop - startTop;
    const startTime = performance.now();
    
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
  
  function scrollToShowContent(element) {
    setTimeout(function() {
      const modal = element.closest('.tpb-qv-modal');
      if (!modal) return;
      
      let scrollContainer = modal.querySelector('.tpb-qv-right-panel');
      if (!scrollContainer || scrollContainer.scrollHeight <= scrollContainer.clientHeight) {
        scrollContainer = modal;
      }
      
      if (!scrollContainer) return;
      
      const elementRect = element.getBoundingClientRect();
      const containerRect = scrollContainer.getBoundingClientRect();
      
      if (elementRect.bottom > containerRect.bottom) {
        try {
          element.scrollIntoView({
            behavior: 'smooth',
            block: 'nearest',
            inline: 'nearest'
          });
          return;
        } catch (e) {
          // Fallback to manual scroll
        }
        
        const elementTop = elementRect.top;
        const containerTop = containerRect.top;
        const scrollTop = scrollContainer.scrollTop + (elementTop - containerTop) - 20;
        
        smoothScrollTo(scrollContainer, scrollTop, 2000);
      }
    }, 5);
  }
  
  // Fade transition function
  function fadeTransition(element, newContent, newClass, duration = 560) {
    // Clear any existing transitions
    element.style.transition = 'none';
    
    // Fade out
    element.style.transition = 'opacity ' + (duration/2) + 'ms ease-in-out';
    element.style.opacity = '0';
    
    setTimeout(function() {
      // Change content and class when completely invisible
      element.innerHTML = newContent;
      if (newClass) {
        element.classList.add(newClass);
      }
      
      // Small delay to ensure content is set before fading in
      setTimeout(function() {
        element.style.opacity = '1';
      }, 10);
    }, duration/2);
  }
  
  // Mount function
  function mount(){ 
    console.log('Category Stations stepper - mount() function called');
    console.log('Category Stations - About to call build()');
    
    // Look for the stepper container in the Category Station modal
    var stepperContainer = document.querySelector('#tpb-qv-category-modal #tpb-qv-stepper-container');
    if (!stepperContainer) {
      console.error('❌ Category Station stepper container not found');
      return null;
    }
    
    console.log('📦 Found stepper container:', stepperContainer);
    
    // Clear any existing content
    stepperContainer.innerHTML = '';
    
    // Create the native stepper div
    var r=el('div',{className:'tpb-qv-native'}); 
    r.id='tpb-qv-native'; 
    r.setAttribute('role','region'); 
    r.setAttribute('aria-label','Configurator'); 
    
    // Insert into the stepper container
    stepperContainer.appendChild(r);
    console.log('📦 Stepper div created and appended to container'); 
    
    // Call build function
    console.log('📦 About to call build() function');
    build();
    console.log('📦 build() function called');
    
    // Inject CSS with cache busting
    var css=el('style'); 
    css.id='tpb-category-css-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
    css.textContent = '.tpb-qv-stepper-container .tpb-qv-native{font-family:inherit;padding:20px;max-width:90%;box-sizing:border-box;overflow-x:hidden;width:100%;color:#333!important} .tpb-qv-native{font-family:inherit;padding:20px;max-width:90%;box-sizing:border-box;overflow-x:hidden;width:100%;color:#333!important} .tpb-step{margin:0;padding:40px 0 0px 40px;max-width:100%;box-sizing:border-box;border:none!important;border-bottom:none!important;border-top:none!important;border-left:none!important;border-right:none!important;color:#333!important} .tpb-grid{display:grid!important;grid-template-columns:repeat(2,1fr)!important;margin-top:35px!important;max-width:100%!important;box-sizing:border-box!important} .tpb-qv-modal .tpb-card{border:1px solid #e3e6ea;border-radius:12px;padding:16px;background:#fff;transition:box-shadow 0.2s,transform 0.2s;text-align:left;max-width:100%!important;box-sizing:border-box;color:#333!important;transform:scale(0.85)!important;transform-origin:top left!important} .tpb-qv-modal .tpb-card:hover{box-shadow:0 4px 12px rgba(0,0,0,0.1);transform:scale(0.85) translateY(-2px)!important} .tpb-qv-modal .tpb-card-horizontal{display:flex!important;gap:24px!important;align-items:flex-start!important;padding:24px!important;min-height:200px!important;width:100%!important;max-width:100%!important;overflow:visible!important;color:#333!important} .tpb-qv-modal .tpb-card-image{position:relative;padding-bottom:75%;overflow:hidden;border-radius:8px;margin-bottom:12px} .tpb-qv-modal .tpb-card-horizontal .tpb-card-image{flex:0 0 200px!important;width:200px!important;height:200px!important;position:relative!important;overflow:hidden!important;border-radius:8px!important;margin-bottom:0!important;padding-bottom:0!important} .tpb-qv-modal .tpb-card-horizontal .tpb-card-content{flex:1!important;display:flex!important;flex-direction:column!important;justify-content:space-between!important;min-height:180px!important;padding-left:8px!important;min-width:0!important;width:100%!important;color:#333!important} .tpb-qv-modal .tpb-card-horizontal .tpb-actions{display:flex!important;gap:16px!important;justify-content:flex-start!important;flex-wrap:nowrap!important;margin-top:8px!important;width:100%!important;min-width:0!important} .tpb-actions{display:flex;gap:8px;margin-top:12px;align-items:center;justify-content:flex-start;flex-wrap:wrap;color:#333!important} .tpb-hint{color:#6b7280!important;font-size:15px;margin:4px 0 8px;text-align:left} .tpb-title{font-weight:700;font-size:18px;margin:8px 0;text-align:left;color:#333!important} .tpb-qv-modal .tpb-card-title{font-weight:600;font-size:16px;margin-bottom:8px;min-height:40px;line-height:1.3;text-align:center;color:#333!important} .tpb-qv-modal .tpb-card-horizontal .tpb-card-title{font-size:22px!important;text-align:left!important;color:#333!important} .tpb-qv-modal .tpb-card-title .parenthetical{font-size:14px;font-weight:400;display:block;margin-top:2px;color:#666!important} .tpb-qv-modal .tpb-card-price{color:rgb(79 176 137)!important;font-weight:600;font-size:16px;padding-top:10px;padding-bottom:10px;margin-bottom:12px;text-align:center} .tpb-select{margin-top:6px;text-align:left;border:2px solid #e3e6ea!important;border-radius:8px!important;padding:12px 16px!important;background:#fff!important;color:#333!important;font-size:16px!important;cursor:pointer!important;transition:border-color 0.2s ease!important;appearance:none!important;-webkit-appearance:none!important;-moz-appearance:none!important;background-image:url("data:image/svg+xml;charset=UTF-8,%3csvg xmlns=\'http://www.w3.org/2000/svg\' viewBox=\'0 0 24 24\' fill=\'none\' stroke=\'%23666\' stroke-width=\'2\' stroke-linecap=\'round\' stroke-linejoin=\'round\'%3e%3cpolyline points=\'6,9 12,15 18,9\'%3e%3c/polyline%3e%3c/svg%3e")!important;background-repeat:no-repeat!important;background-position:right 12px center!important;background-size:16px!important;padding-right:40px!important} .tpb-select:hover{border-color:#4fb08f!important} .tpb-select:focus{outline:none!important;border-color:#4fb08f!important;box-shadow:0 0 0 3px rgba(79,176,143,0.1)!important} .tpb-radio{margin-top:6px;text-align:left;border:none!important;border-bottom:none!important;border-top:none!important;border-left:none!important;border-right:none!important;outline:none!important;box-shadow:none!important;background:none!important;appearance:none!important;-webkit-appearance:none!important;-moz-appearance:none!important;color:#333!important} .tpb-toast{position:fixed;right:12px;bottom:12px;background:#111827;color:#fff;padding:10px 14px;border-radius:8px;opacity:.95;z-index:99999} .sr-live{position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden} .tpb-step[hidden]{display:none!important} .tpb-step:not([hidden]){display:block!important} .tpb-step{opacity:0;transform:translateY(20px);transition:opacity 0.4s ease-out,transform 0.4s ease-out} .tpb-step:not([hidden]){opacity:1;transform:translateY(0)} .tpb-step.entering{opacity:0;transform:translateY(20px);animation:stepEnter 0.4s ease-out forwards} @keyframes stepEnter{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}} .tpb-hint.entering{opacity:0;transform:translateY(15px);animation:textEnter 0.3s ease-out forwards} @keyframes textEnter{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}} .tpb-base-price{transition:all 0.3s ease-in-out;color:#333!important;margin-bottom:0!important} .tpb-base-price.updating{opacity:0.7;transform:scale(0.98)} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal){opacity:0;transform:translateY(15px);animation:cardReveal 0.5s ease-out forwards} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(1){animation-delay:0.1s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(2){animation-delay:0.2s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(3){animation-delay:0.3s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(4){animation-delay:0.4s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(5){animation-delay:0.5s} .tpb-qv-modal .tpb-card:not(.tpb-card-horizontal):nth-child(6){animation-delay:0.6s} @keyframes cardReveal{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}} .tpb-qv-modal .tpb-card-horizontal{opacity:1;transform:none;animation:none} .tpb-qty{color:#333!important} .tpb-qty span{color:#333!important} .tpb-qty input{color:#333!important;background:#fff!important;border:1px solid #ddd!important} .tpb-bottom-section{color:#333!important} .tpb-qv-stepper-container{padding-top:0!important} .tpb-form-factor-buttons{display:flex!important;gap:40px!important;margin-top:20px!important;flex-wrap:wrap!important;justify-content:flex-start!important;opacity:1!important;transform:none!important;visibility:visible!important;position:relative!important;z-index:999!important} .tpb-form-factor-btn{flex:0 0 auto!important;padding:10px 16px!important;border:2px solid #e3e6ea!important;border-radius:25px!important;background:#fff!important;color:#374151!important;font-size:13px!important;font-weight:600!important;cursor:pointer!important;transition:all 0.3s ease!important;text-align:center!important;box-shadow:0 1px 3px rgba(0,0,0,0.1)!important;white-space:nowrap!important;opacity:1!important;visibility:visible!important;display:flex!important;position:relative!important;z-index:999!important} .tpb-form-factor-btn:hover{border-color:rgb(79,176,137)!important;background:#f0fdf4!important;color:rgb(79,176,137)!important;transform:translateY(-2px)!important;box-shadow:0 4px 12px rgba(79,176,137,0.15)!important} .tpb-form-factor-btn.active{border-color:rgb(79,176,137)!important;background:rgb(79,176,137)!important;color:#fff!important;transform:translateY(-1px)!important;box-shadow:0 6px 16px rgba(79,176,137,0.25)!important}';
    
    // Remove any existing CSS with the same ID pattern
    var existingCss = document.querySelectorAll('style[id^="tpb-category-css-"]');
    for(var i = 0; i < existingCss.length; i++) {
      existingCss[i].remove();
    }
    
    document.head.appendChild(css); 
    return r; 
  }
  
  // Main build function
// Allow re-initialization for modal reopening
console.log('📦 Category stepper script loaded');

function build(){ 
  console.log('=== CATEGORY STEPPER DEBUG ===');
  console.log('1. Stepper script loaded');
  console.log('2. Checking environment...');
    console.log('3. Starting build process');
    console.log('   - URL:', window.location.href);
    console.log('   - Category modal exists:', !!document.querySelector('#tpb-qv-category-modal'));
    console.log('   - Category modal visible:', document.querySelector('#tpb-qv-category-modal')?.style.display);
    console.log('   - All modals:', document.querySelectorAll('.tpb-qv-modal').length);
    
    var params=new URLSearchParams(location.search); 
    
    // Check if we're in the Category Station modal specifically
    var categoryModal = document.querySelector('#tpb-qv-category-modal');
    var isInModal = categoryModal !== null && categoryModal.classList.contains('is-open');
    var isInIframe = params.get('tpb_qv_iframe')==='1' || params.get('tpb_qv')==='1';
    
    console.log('Category Stations stepper - Modal detection:', {
      isInModal: isInModal,
      isInIframe: isInIframe,
      categoryModal: categoryModal,
      categoryModalClasses: categoryModal?.classList.toString(),
      allModals: document.querySelectorAll('.tpb-qv-modal').length
    });
    
    if(!isInModal && !isInIframe) {
      console.log('Category Stations stepper - not in modal or iframe mode, exiting');
      return;
    }
    console.log('Category Stations stepper - in modal/iframe mode, proceeding with initialization');
    
    console.log('Category Stations stepper loading...'); 
    
    hideLegacy(); 
    
    // Look for the stepper container in the Category Station modal
    var stepperContainer = document.querySelector('#tpb-qv-category-modal #tpb-qv-stepper-container');
    if (!stepperContainer) {
      console.error('❌ Category Station stepper container not found');
      return;
    }
    
    console.log('📦 Found stepper container:', stepperContainer);
    
    // Clear any existing content
    stepperContainer.innerHTML = '';
    
    // Create the native stepper div
    var root=el('div',{className:'tpb-qv-native'}); 
    root.id='tpb-qv-native'; 
    root.setAttribute('role','region'); 
    root.setAttribute('aria-label','Configurator'); 
    
    // Insert into the stepper container
    stepperContainer.appendChild(root);
    console.log('📦 Stepper div created and appended to container');
    clear(root); 
    var live=el('div',{className:'sr-live'}); 
    live.setAttribute('aria-live','polite'); 
    root.appendChild(live); 
    
    var state={strategy:null,formFactor:null,hardwarePrice:null,priceUpdateTimeout:null}; 
    
    // Step 1: Build Strategy
    var s1=el('div',{className:'tpb-step'}); 
    s1.appendChild(el('div',{className:'tpb-title',text:'Build Strategy'})); 
    s1.appendChild(el('div',{className:'tpb-hint',text:'Custom Build includes hardware only; Pre-designed includes furniture and display components.'})); 
    var r=el('div',{className:'tpb-radio'}); 
    r.innerHTML='<label><input type="radio" name="tpb-strategy" value="custom"> Custom Build</label> <label style="margin-left:16px"><input type="radio" name="tpb-strategy" value="predesigned"> Pre-designed</label>'; 
    s1.appendChild(r);
    
    // Step 2: Form-Factor Selection
    var s2=el('div',{className:'tpb-step'}); 
    s2.appendChild(el('div',{className:'tpb-title',text:'Display Configuration'})); 
    s2.appendChild(el('div',{className:'tpb-hint',text:'Choose your preferred display setup for the category station.'})); 
    
    // Create form factor container with inline styles
    var formFactorContainer=el('div'); 
    formFactorContainer.style.cssText = 'display: flex !important; gap: 40px !important; margin-top: 20px !important; flex-wrap: wrap !important; justify-content: flex-start !important; opacity: 1 !important; visibility: visible !important; position: relative !important; z-index: 999 !important;';
    
    ['counter-top','free-standing','wall-mounted'].forEach(function(factor){ 
      var btn=el('button', {className: 'tpb-form-factor-btn'}); 
      btn.textContent=factor.split('-').map(function(w){ return w.charAt(0).toUpperCase()+w.slice(1); }).join(' '); 
      btn.dataset.factor=factor; 
      
      // Apply all styles inline
      btn.style.cssText = 'flex: 0 0 auto !important; padding: 10px 16px !important; border: 2px solid #e3e6ea !important; border-radius: 25px !important; background: #fff !important; color: #374151 !important; font-size: 13px !important; font-weight: 600 !important; cursor: pointer !important; transition: all 0.3s ease !important; text-align: center !important; box-shadow: 0 1px 3px rgba(0,0,0,0.1) !important; white-space: nowrap !important; opacity: 1 !important; visibility: visible !important; display: flex !important; position: relative !important; z-index: 999 !important; margin: 0 !important; outline: none !important;';
      
      formFactorContainer.appendChild(btn); 
    }); 
    s2.appendChild(formFactorContainer);
    
    // Step 3: Results
    var s3=el('div',{className:'tpb-step'}); 
    var resH=el('div',{className:'tpb-title',text:'Choose Your Category Station'}); 
    resH.id='tpb-results-heading'; 
    resH.tabIndex=-1; 
    s3.appendChild(resH); 
    var grid=el('div',{className:'tpb-grid'}); 
    s3.appendChild(grid);
    
    // Initially hide all steps
    s1.hidden = true;
    s2.hidden = true; 
    s3.hidden = true;
    
    function focusResults(){ try{ resH.focus(); }catch(e){} } 
    
    function update(){ 
      console.log('Category Stations - update() called with strategy:', state.strategy);
      // Check for any existing content in the grid that needs to be animated out
      var existingCards = grid.querySelectorAll('.tpb-card');
      var existingEmptyState = grid.querySelector('.tpb-empty-state');
      
      console.log('update() called - existingCards:', existingCards.length, 'existingEmptyState:', !!existingEmptyState);
      
      if(existingCards.length > 0 || existingEmptyState) {
        console.log('Animating out existing content...');
        
        void grid.offsetWidth;
        
        if(existingCards.length > 0) {
          existingCards.forEach(function(card, index) {
            console.log('Animating out card:', index, card);
            requestAnimationFrame(function() {
              card.style.setProperty('transition', 'opacity 0.6s ease-out, transform 0.6s ease-out', 'important');
              card.style.setProperty('opacity', '0', 'important');
              card.style.setProperty('transform', 'translateY(-15px)', 'important');
            });
          });
        }
        
        if(existingEmptyState) {
          console.log('Animating out empty state:', existingEmptyState);
          requestAnimationFrame(function() {
            existingEmptyState.style.setProperty('transition', 'opacity 0.6s ease-out, transform 0.6s ease-out', 'important');
            existingEmptyState.style.setProperty('opacity', '0', 'important');
            existingEmptyState.style.setProperty('transform', 'translateY(-15px)', 'important');
          });
        }
        
        setTimeout(function() {
          console.log('Clearing grid after animation...');
          grid.innerHTML=''; 
          root.classList.remove('has-results');
          fetchProductsAndRender();
        }, 650);
        return;
      }
      
      console.log('No existing content, proceeding directly...');
      grid.innerHTML=''; 
      root.classList.remove('has-results');
      fetchProductsAndRender();
    }
    
    function fetchProductsAndRender() {
      console.log('Category Stations - fetchProductsAndRender() called');
      console.log('Category Stations - Current state:', state);
      
      // Remove any existing custom build description only when switching away from Custom Build
      var existingDesc = s3.querySelector('.tpb-hint');
      if (existingDesc && existingDesc.textContent.includes('custom build project') && state.strategy !== 'custom') {
        existingDesc.remove();
      }
      
      // Update Step 3 heading based on strategy
      var newTitle = state.strategy === 'custom' ? 'Here\'s Your Hardware-Only Kit' : 'Choose Your Category Station';
      if(resH.textContent !== newTitle) {
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
      
      if(!state.strategy){ resize(); return; } 
      
      var tags = [];
      var cats = [];
      var excludeCats = [];
      
      if(state.strategy === 'custom') {
        console.log('Category Stations - Custom Build: Using client-side filtering for 27" hardware-only kit');
        console.log('Category Stations - Custom Build: Strategy state is:', state.strategy);
        // Don't use server-side filtering since products don't have tags/categories
        cats = [];
        tags = [];
        
        // Add custom build description
        var existingCustomDesc = s3.querySelector('.tpb-hint');
        if (!existingCustomDesc || !existingCustomDesc.textContent.includes('custom build project')) {
          var desc = el('div',{className:'tpb-hint'});
          desc.innerHTML = 'For a custom build project, you only need to purchase the electronics hardware, and TPB will offer consultation for your display design, furniture, and all other components of the category station.';
          desc.style.cssText = 'width:100%;max-width:100%;padding:0;margin-bottom:16px;color:#6b7280;font-size:15px;line-height:1.5';
          
          s3.insertBefore(desc, grid);
          
          desc.classList.add('entering');
          setTimeout(function() {
            desc.classList.remove('entering');
          }, 300);
        }
        
        grid.style.cssText = 'display:block!important;width:100%!important;max-width:none!important;box-sizing:border-box!important;margin-top:16px!important';
        
      } else if(state.strategy === 'predesigned' && state.formFactor) {
        resH.textContent = 'Choose Your Category Station';
        
        console.log('Category Stations - Pre-designed: Using tag-based form-factor filtering');
        cats = ['pick-place'];
        if(state.formFactor === 'counter-top') {
          tags = ['pd-counter-top'];
        } else if(state.formFactor === 'free-standing') {
          tags = ['pd-free-standing'];
        } else if(state.formFactor === 'wall-mounted') {
          tags = ['pd-wall-mounted'];
        } else {
          tags = [];
        }
        
        grid.style.cssText = 'display:grid!important;grid-template-columns:repeat(2,1fr)!important;gap:20px!important;margin-top:16px!important;max-width:100%!important;box-sizing:border-box!important';
      }
      
      // Clear any existing empty state message
      var existingEmptyState = grid.querySelector('.tpb-empty-state');
      if(existingEmptyState) {
        existingEmptyState.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        existingEmptyState.style.opacity = '0';
        existingEmptyState.style.transform = 'translateY(-15px)';
        setTimeout(function() {
          existingEmptyState.remove();
        }, 300);
      }
      
      console.log('Category Stations - Fetching with params:', {
        tags: tags,
        categories: cats,
        exclude_categories: excludeCats,
        limit: state.strategy === 'custom' ? 50 : 24
      });
      
      fetchProducts({
        tags: tags,
        categories: cats,
        exclude_categories: excludeCats,
        limit: state.strategy === 'custom' ? 50 : 24
      }).then(function(d){
        console.log('Category Stations - API Response:', d);
        console.log('Category Stations - API Response type:', typeof d);
        console.log('Category Stations - API Response isArray:', Array.isArray(d));
        console.log('Category Stations - API Response has data:', d && d.data);
        console.log('Category Stations - API Response data isArray:', d && Array.isArray(d.data));
        console.log('Category Stations - API Response has results:', d && d.results);
        console.log('Category Stations - API Response results isArray:', d && Array.isArray(d.results));
        
        var items=Array.isArray(d)?d:(d&&Array.isArray(d.data)?d.data:(d&&Array.isArray(d.results)?d.results:[]));
        console.log('Category Stations - Items found before filtering:', items.length);
        console.log('Category Stations - Raw items:', items.map(function(item) { 
          return { name: item.name, slug: item.slug, id: item.id }; 
        }));
        console.log('Category Stations - Current strategy:', state.strategy);
        
        // Client-side filtering based on strategy
        console.log('Category Stations - Starting client-side filtering for strategy:', state.strategy);
        console.log('Category Stations - Items before filtering:', items.length);
        
        if(state.strategy === 'custom') {
          // For Custom Build, only show the 27" hardware-only kit
          console.log('Category Stations - Custom Build: Applying filter for 27" hardware-only kit');
          items = items.filter(function(product) {
            var name = product.name.toLowerCase();
            console.log('Category Stations - Custom Build: Checking product:', product.name, '->', name);
            console.log('Category Stations - Custom Build: Contains "27":', name.includes('27'));
            console.log('Category Stations - Custom Build: Contains "category station":', name.includes('category station'));
            console.log('Category Stations - Custom Build: Contains "24":', name.includes('24'));
            
            if(name.includes('27') && name.includes('category station') && name.includes('24')) {
              console.log('Category Stations - Custom Build: ✅ INCLUDING 27" hardware-only kit:', product.name);
              return true;
            } else {
              console.log('Category Stations - Custom Build: ❌ Filtering out non-hardware product:', product.name);
              return false;
            }
          });
        } else {
          // For Pre-designed, filter out Flower Station and Configure Now
          items = items.filter(function(product) {
            if(product.name && product.name.toLowerCase().includes('flower station')) {
              console.log('Category Stations - Filtering out Flower Station product:', product.name);
              return false;
            }
            
            if(product.name && product.name.toLowerCase().includes('configure now')) {
              console.log('Category Stations - Filtering out Configure Now product:', product.name);
              return false;
            }
            
            console.log('Category Stations - Including product:', product.name);
            return true;
          });
        }
        
        console.log('Category Stations - Items found after filtering:', items.length);
        console.log('Category Stations - Filtered items:', items.map(function(item) { 
          return { name: item.name, id: item.id }; 
        }));
        
        if(items.length === 0) {
          console.log('Category Stations - No Category Station products found after filtering.');
          console.log('Category Stations - This will show empty state instead of product card');
        }
        
        if(items.length){
          root.classList.add('has-results');
          console.log('Category Stations - Rendering', items.length, 'products');
          
          // Update base price in modal
          if(items[0].price_html) {
            var priceEl = document.getElementById('tpb-qv-base-price');
            if (priceEl) {
              var cleanPriceHtml = items[0].price_html.replace(/style="[^"]*"/gi, '').replace(/<span[^>]*>/gi, '<span>');
              var newContent = 'Base Price:  ' + cleanPriceHtml;
              
              fadeTransition(priceEl, newContent, 'has-price', 560);
            }
          }
          
          // Render products
          items.forEach(function(p){ 
            var card=el('div',{className:'tpb-card'}); 
            
            if(state.strategy === 'custom') {
              // For Custom Build, use horizontal layout
              card.className = 'tpb-card tpb-card-horizontal';
              card.style.cssText = 'display:flex!important;gap:24px!important;align-items:flex-start!important;padding:24px!important;border:1px solid #e3e6ea!important;border-radius:12px!important;background:#fff!important;transition:box-shadow 0.2s,transform 0.2s!important;text-align:left!important;width:100%!important;max-width:none!important;box-sizing:border-box!important;min-height:200px!important;overflow:visible!important;flex-shrink:0!important';
              
              // Image container - left side
              var imgContainer = el('div',{className:'tpb-card-image'});
              imgContainer.style.cssText = 'flex:0 0 200px;width:200px;height:200px;position:relative;overflow:hidden;border-radius:8px';
              var img = el('img');
              img.alt = p.name || '';
              img.src = p.image_url || '';
              img.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover';
              imgContainer.appendChild(img);
              card.appendChild(imgContainer);
              
              // Content container - right side
              var contentContainer = el('div',{className:'tpb-card-content'});
              contentContainer.style.cssText = 'flex:1;display:flex;flex-direction:column;justify-content:space-between;min-height:180px;padding-left:8px;min-width:0;width:100%';
              
              // Product title with parenthetical text handling
              var title = el('div',{className:'tpb-card-title'});
              var productName = p.name || '';
              
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
              var priceDiv = el('div',{className:'tpb-card-price'});
              priceDiv.innerHTML = p.price_html || '';
              priceDiv.style.cssText = 'color:rgb(79 176 137);font-weight:600;font-size:20px;padding:8px 0;margin-bottom:16px;text-align:left';
              contentContainer.appendChild(priceDiv);
              
              // Bottom section with quantity and actions
              var bottomSection = el('div',{className:'tpb-bottom-section'});
              bottomSection.style.cssText = 'display:flex;flex-direction:column;gap:12px;width:100%;min-width:0';
              
              // Quantity selector
              var qtyDiv = el('div',{className:'tpb-qty'});
              qtyDiv.style.cssText = 'display:flex;align-items:center;gap:8px';
              qtyDiv.innerHTML = '<span>Qty:</span><input type="number" value="1" min="1" style="width:60px;padding:4px;border:1px solid #ddd;border-radius:4px">';
              bottomSection.appendChild(qtyDiv);
              
              // Action buttons
              var actions = el('div',{className:'tpb-actions'});
              actions.style.cssText = 'display:flex;gap:16px;justify-content:flex-start;flex-wrap:nowrap;margin-top:8px;width:100%;min-width:0';
              
              var addToCartBtn = el('button',{text:'Add to cart'});
              addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
              addToCartBtn.addEventListener('click',function(){ addCart(p.id,1,this); });
              
              var addToQuoteBtn = el('button',{text:'Add to quote'});
              addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
              addToQuoteBtn.addEventListener('click',function(){ addQuote(p.id,this); });
              
              actions.appendChild(addToCartBtn);
              actions.appendChild(addToQuoteBtn);
              bottomSection.appendChild(actions);
              contentContainer.appendChild(bottomSection);
              card.appendChild(contentContainer);
            } else {
              // For Pre-designed, use vertical layout
              card.className = 'tpb-card';
              
              // Image container with aspect ratio
              var imgContainer = el('div',{className:'tpb-card-image'});
              imgContainer.style.cssText = 'position:relative;padding-bottom:75%;overflow:hidden;border-radius:8px;margin-bottom:12px';
              var img = el('img');
              img.alt = p.name || '';
              img.src = p.image_url || '';
              img.style.cssText = 'position:absolute;top:0;left:0;width:100%;height:100%;object-fit:cover';
              imgContainer.appendChild(img);
              card.appendChild(imgContainer);
              
              // Product title with parenthetical text handling
              var title = el('div',{className:'tpb-card-title'});
              var productName = p.name || '';
              
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
              var priceDiv = el('div',{className:'tpb-card-price'});
              priceDiv.innerHTML = p.price_html || '';
              priceDiv.style.cssText = 'color:rgb(79 176 137);font-weight:600;font-size:16px;padding-top:10px;padding-bottom:10px;margin-bottom:12px;text-align:center';
              card.appendChild(priceDiv);
              
              // Quantity selector
              var qtyDiv = el('div',{className:'tpb-qty'});
              qtyDiv.style.cssText = 'display:flex;align-items:center;gap:8px;justify-content:center';
              qtyDiv.innerHTML = '<span>Qty:</span><input type="number" value="1" min="1" style="width:60px;padding:4px;border:1px solid #ddd;border-radius:4px">';
              card.appendChild(qtyDiv);
              
              // Action buttons
              var actions = el('div',{className:'tpb-actions'});
              actions.style.cssText = 'display:flex;gap:8px;margin-top:12px;align-items:center;justify-content:flex-start;flex-wrap:wrap';
              
              var addToCartBtn = el('button',{text:'Add to cart'});
              addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
              addToCartBtn.addEventListener('click',function(){ addCart(p.id,1,this); });
              
              var addToQuoteBtn = el('button',{text:'Add to quote'});
              addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:16px';
              addToQuoteBtn.addEventListener('click',function(){ addQuote(p.id,this); });
              
              actions.appendChild(addToCartBtn);
              actions.appendChild(addToQuoteBtn);
              card.appendChild(actions);
            }
            
            grid.appendChild(card); 
            
            // Add entrance animation for horizontal cards in Custom Build mode
            if(state.strategy === 'custom') {
              card.style.opacity = '0';
              card.style.transform = 'translateY(15px)';
              card.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
              
              setTimeout(function() {
                card.style.opacity = '1';
                card.style.transform = 'translateY(0)';
              }, 50);
            }
          });
          
          // Auto-scroll to show new content
          scrollToShowContent(grid);
        } else {
          // No products found - show empty state
          console.log('Category Stations - No products found, showing empty state');
          var emptyState = el('div', {className: 'tpb-empty-state'});
          emptyState.innerHTML = 'There are no category station options with this configuration. Please try a different selection.';
          emptyState.style.cssText = 'text-align:center;padding:40px 20px;color:#6b7280;font-size:16px;line-height:1.5;background:#f8f9fa;border-radius:8px;margin:20px 0;border:1px solid #e3e6ea;opacity:0;transform:translateY(15px);transition:opacity 0.4s ease-out,transform 0.4s ease-out';
          grid.appendChild(emptyState);
          
          setTimeout(function() {
            emptyState.style.opacity = '1';
            emptyState.style.transform = 'translateY(0)';
          }, 50);
        }
        focusResults(); 
        resize(); 
      }).catch(function(){ toast('Failed to load results'); resize(); root.classList.remove('has-results'); } ); 
    } 
    
    // Strategy selection handler
    console.log('Category Stations - Adding radio button event listener');
    r.addEventListener('change',function(e){ 
      if(e.target&&e.target.name==='tpb-strategy'){ 
        var hadStrategy = !!state.strategy;
        var hasStrategy = !!e.target.value;
        
        if(!hadStrategy && hasStrategy) {
          // First time selecting strategy
          state.strategy = e.target.value;
          console.log('Category Stations - Radio button clicked, strategy set to:', state.strategy);
          console.log('Category Stations - State object:', state);
          
          if(state.strategy === 'custom') {
            // Skip form-factor step, go straight to product
            s3.hidden = false;
            s3.classList.add('entering');
            setTimeout(function() {
              s3.classList.remove('entering');
            }, 400);
            update();
            
            // Fetch hardware price for base price display - use server-side filtering
            fetchProducts({
              tags: ['27-inch', 'hardware-only', 'category-station'],
              categories: ['category-station-kits'],
              sort: 'price_asc',
              limit: 1
            }).then(function(d){
              var items = Array.isArray(d) ? d : (d && Array.isArray(d.results) ? d.results : []);
              
              console.log('Category Stations - Hardware price fetch items:', items.length);
              
              if(items.length && items[0].price_html) {
                // Send to parent modal
                if(window.parent && window.parent !== window) {
                  window.parent.postMessage({
                    type: 'TPB_QV',
                    action: 'update_base_price',
                    price: items[0].price_html
                  }, '*');
                }
              }
            });
          } else if(state.strategy === 'predesigned') {
            // Show form-factor step
            s2.hidden = false;
            s2.classList.add('entering');
            setTimeout(function() {
              s2.classList.remove('entering');
            }, 400);
            
            // Trigger entrance animation for step 2 content and form-factor buttons
            setTimeout(function() {
              var step2Title = s2.querySelector('.tpb-title');
              var step2Hint = s2.querySelector('.tpb-hint');
              
              if(step2Title) {
                step2Title.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
                step2Title.style.opacity = '0';
                step2Title.style.transform = 'translateY(15px)';
                
                setTimeout(function() {
                  step2Title.style.opacity = '1';
                  step2Title.style.transform = 'translateY(0)';
                }, 50);
              }
              
              if(step2Hint) {
                step2Hint.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
                step2Hint.style.opacity = '0';
                step2Hint.style.transform = 'translateY(15px)';
                
                setTimeout(function() {
                  step2Hint.style.opacity = '1';
                  step2Hint.style.transform = 'translateY(0)';
                }, 100);
              }
              
              formFactorContainer.classList.add('entering');
              setTimeout(function() {
                formFactorContainer.classList.remove('entering');
              }, 400);
            }, 200);
            
            s3.hidden = true;
          }
          
          resize();
          return;
        } else if(hasStrategy) {
          // Switching strategies - animate out existing content
          s3.hidden = false;
          
          var existingCards = grid.querySelectorAll('.tpb-card');
          var existingDesc = s3.querySelector('.tpb-hint');
          var existingEmptyState = grid.querySelector('.tpb-empty-state');
          
          resH.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
          resH.style.opacity = '0';
          resH.style.transform = 'translateY(-15px)';
          
          if(existingCards.length > 0) {
            existingCards.forEach(function(card) {
              card.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
              card.style.opacity = '0';
              card.style.transform = 'translateY(-15px)';
            });
          }
          
          if(existingDesc) {
            existingDesc.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            existingDesc.style.opacity = '0';
            existingDesc.style.transform = 'translateY(-15px)';
          }
          
          if(existingEmptyState) {
            existingEmptyState.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            existingEmptyState.style.opacity = '0';
            existingEmptyState.style.transform = 'translateY(-15px)';
          }
          
          console.log('Category Stations - Starting exit animation for form-factor buttons');
          
          formFactorContainer.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
          formFactorContainer.style.opacity = '0';
          formFactorContainer.style.transform = 'translateY(-15px)';
          
          var step2Title = s2.querySelector('.tpb-title');
          var step2Hint = s2.querySelector('.tpb-hint');
          
          if(step2Title) {
            step2Title.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            step2Title.style.opacity = '0';
            step2Title.style.transform = 'translateY(-15px)';
          }
          
          if(step2Hint) {
            step2Hint.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            step2Hint.style.opacity = '0';
            step2Hint.style.transform = 'translateY(-15px)';
          }
          
          setTimeout(function() {
            console.log('Category Stations - Exit animation completed, resetting styles');
            
            formFactorContainer.style.opacity = '';
            formFactorContainer.style.transform = '';
            formFactorContainer.style.transition = '';
            
            if(step2Title) {
              step2Title.style.opacity = '';
              step2Title.style.transform = '';
              step2Title.style.transition = '';
            }
            
            if(step2Hint) {
              step2Hint.style.opacity = '';
              step2Hint.style.transform = '';
              step2Hint.style.transition = '';
            }
          }, 300);
          
          setTimeout(function() {
            state.strategy = e.target.value;
            console.log('Category Stations - Strategy updated in timeout, strategy set to:', state.strategy);
            
            if(state.strategy === 'custom') {
              s2.hidden = true;
              s3.hidden = false;
              
              console.log('Category Stations - Animating out form-factor buttons (custom switch)');
              
              formFactorContainer.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
              formFactorContainer.style.opacity = '0';
              formFactorContainer.style.transform = 'translateY(-15px)';
              
              var step2Title = s2.querySelector('.tpb-title');
              var step2Hint = s2.querySelector('.tpb-hint');
              
              if(step2Title) {
                step2Title.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                step2Title.style.opacity = '0';
                step2Title.style.transform = 'translateY(-15px)';
              }
              
              if(step2Hint) {
                step2Hint.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
                step2Hint.style.opacity = '0';
                step2Hint.style.transform = 'translateY(-15px)';
              }
              
              setTimeout(function() {
                console.log('Category Stations - Reset form-factor button styles (custom switch)');
                formFactorContainer.style.opacity = '';
                formFactorContainer.style.transform = '';
                formFactorContainer.style.transition = '';
                
                if(step2Title) {
                  step2Title.style.opacity = '';
                  step2Title.style.transform = '';
                  step2Title.style.transition = '';
                }
                
                if(step2Hint) {
                  step2Hint.style.opacity = '';
                  step2Hint.style.transform = '';
                  step2Hint.style.transition = '';
                }
              }, 300);
              
              // Update products will be handled by the update() function
              update();
            } else {
              s2.hidden = false;
              s3.hidden = true;
              
              setTimeout(function() {
                var step2Title = s2.querySelector('.tpb-title');
                var step2Hint = s2.querySelector('.tpb-hint');
                
                if(step2Title) {
                  step2Title.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
                  step2Title.style.opacity = '0';
                  step2Title.style.transform = 'translateY(15px)';
                  
                  setTimeout(function() {
                    step2Title.style.opacity = '1';
                    step2Title.style.transform = 'translateY(0)';
                  }, 50);
                }
                
                if(step2Hint) {
                  step2Hint.style.transition = 'opacity 0.4s ease-out, transform 0.4s ease-out';
                  step2Hint.style.opacity = '0';
                  step2Hint.style.transform = 'translateY(15px)';
                  
                  setTimeout(function() {
                    step2Hint.style.opacity = '1';
                    step2Hint.style.transform = 'translateY(0)';
                  }, 100);
                }
                
                formFactorContainer.classList.add('entering');
                setTimeout(function() {
                  formFactorContainer.classList.remove('entering');
                }, 400);
              }, 50);
            }
            
            update(); 
            resize();
          }, 300);
          return;
        }
        
        state.strategy = e.target.value;
        update(); 
        resize();
      } 
    });
    
    // Form-factor button handler
    formFactorContainer.addEventListener('click', function(e){
      if(e.target.classList.contains('tpb-form-factor-btn')) {
        // Remove active class from all buttons and reset styling
        formFactorContainer.querySelectorAll('.tpb-form-factor-btn').forEach(function(btn){
          btn.classList.remove('active');
          btn.style.setProperty('border-color', '#e3e6ea', 'important');
          btn.style.setProperty('background', '#fff', 'important');
          btn.style.setProperty('color', '#374151', 'important');
          btn.style.setProperty('transform', 'translateY(0)', 'important');
          btn.style.setProperty('box-shadow', '0 1px 3px rgba(0,0,0,0.1)', 'important');
        });
        
        // Add active class to clicked button and apply active styling
        e.target.classList.add('active');
        e.target.style.setProperty('border-color', 'rgb(79,176,137)', 'important');
        e.target.style.setProperty('background', 'rgb(79,176,137)', 'important');
        e.target.style.setProperty('color', '#fff', 'important');
        e.target.style.setProperty('transform', 'translateY(-1px)', 'important');
        e.target.style.setProperty('box-shadow', '0 6px 16px rgba(79,176,137,0.25)', 'important');
        
        // Update state
        var oldFormFactor = state.formFactor;
        state.formFactor = e.target.dataset.factor;
        
        // Show Step 3 if first selection
        if(!oldFormFactor) {
          s3.hidden = false;
          s3.classList.add('entering');
          setTimeout(function() {
            s3.classList.remove('entering');
          }, 400);
        }
        
        // Update products
        update();
        resize();
      }
    });
    
    console.log('📦 Appending steps to root element');
    root.appendChild(s1);
    root.appendChild(s2); 
    root.appendChild(s3);
    console.log('📦 Steps appended, root children count:', root.children.length);
    
    // Show Step 1 immediately with force visibility
    console.log('📦 Showing Step 1 - before:', s1.hidden, s1.style.display);
    s1.hidden = false;
    s1.classList.add('visible'); // Add visible class to prevent CSS transitions
    s1.style.display = 'block !important';
    s1.style.visibility = 'visible !important';
    s1.style.opacity = '1 !important';
    s1.classList.add('entering');
    console.log('📦 Showing Step 1 - after:', s1.hidden, s1.style.display, s1.classList.toString());
    setTimeout(function() {
      s1.classList.remove('entering');
      console.log('📦 Step 1 animation complete');
    }, 400); 
    resize(); 
    window.addEventListener('resize', function(){ resize(); }); 
  }
  
  // Make mount function globally available
  window.mount = mount;
  
  if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', build);} else { build(); }
})();
