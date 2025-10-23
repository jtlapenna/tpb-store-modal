(function(){
  'use strict';
  
  function $(s,c){ return (c||document).querySelector(s); }
  function el(t,o){ var n=document.createElement(t); if(o){ if(o.className) n.className=o.className; if(o.text) n.textContent=o.text; if(o.html) n.innerHTML=o.html; } return n; }
  function clear(n){ while(n&&n.firstChild) n.removeChild(n.firstChild); }
  function hideLegacy(){ ['.af_cp_all_components_content','.afcpb-wrapper','.af_cp_content'].forEach(function(sel){ var c=$(sel); if(c){ c.style.display='none'; } }); }
  function resize(){ try{ var h=document.body.scrollHeight; if(window.parent&&window.parent!==window){ window.parent.postMessage({ type:'TPB_QV', action:'resize', height:h }, '*'); } }catch(e){} }
  function mount(){ console.log('Category Stations stepper - mount() function called - FORCE CSS UPDATE'); var r=document.getElementById('tpb-qv-native'); if(r) return r; r=el('div',{className:'tpb-qv-native'}); r.id='tpb-qv-native'; r.setAttribute('role','region'); r.setAttribute('aria-label','Configurator'); var host=$('.woocommerce .product')||$('.type-product')||$('main')||document.body; host.insertBefore(r, host.firstChild||null); 
  
  // Force CSS update with aggressive cache-busting
  var css=el('style'); 
  css.id='tpb-category-css-' + Date.now() + '-' + Math.random().toString(36).substr(2, 9);
  css.textContent='.tpb-qv-native{font-family:inherit;padding:20px;max-width:90%;box-sizing:border-box;overflow-x:hidden;width:100%;margin-top:-20px} .tpb-step{margin:0;padding:40px 0 0px 40px;max-width:100%;box-sizing:border-box;border:none!important;border-bottom:none!important;border-top:none!important;border-left:none!important;border-right:none!important} .tpb-grid{gap:20px!important;margin-top:16px!important;max-width:100%!important;box-sizing:border-box!important} .tpb-card{border:1px solid #e3e6ea;border-radius:12px;padding:16px;background:#fff;transition:box-shadow 0.2s,transform 0.2s;text-align:left;max-width:100%;box-sizing:border-box} .tpb-card:hover{box-shadow:0 4px 12px rgba(0,0,0,0.1);transform:translateY(-2px)} .tpb-card-horizontal{display:flex!important;gap:24px!important;align-items:flex-start!important;padding:24px!important;min-height:200px!important;width:100%!important;max-width:100%!important;overflow:visible!important} .tpb-card-image{position:relative;padding-bottom:75%;overflow:hidden;border-radius:8px;margin-bottom:12px} .tpb-card-horizontal .tpb-card-image{flex:0 0 200px!important;width:200px!important;height:200px!important;position:relative!important;overflow:hidden!important;border-radius:8px!important;margin-bottom:0!important;padding-bottom:0!important} .tpb-card-horizontal .tpb-card-content{flex:1!important;display:flex!important;flex-direction:column!important;justify-content:space-between!important;min-height:180px!important;padding-left:8px!important;min-width:0!important;width:100%!important} .tpb-card-horizontal .tpb-actions{display:flex!important;gap:16px!important;justify-content:flex-start!important;flex-wrap:nowrap!important;margin-top:8px!important;width:100%!important;min-width:0!important} .tpb-actions{display:flex;gap:8px;margin-top:12px;align-items:center;justify-content:flex-start;flex-wrap:wrap} .tpb-hint{color:#6b7280;font-size:15px;margin:4px 0 8px;text-align:left} .tpb-title{font-weight:700;font-size:18px;margin:8px 0;text-align:left} .tpb-card-title{font-weight:600;font-size:16px;margin-bottom:8px;min-height:40px;line-height:1.3;text-align:center} .tpb-card-horizontal .tpb-card-title{font-size:22px!important;text-align:left!important} .tpb-card-title .parenthetical{font-size:14px;font-weight:400;display:block;margin-top:2px} .tpb-card-price{color:rgb(79 176 137);font-weight:600;font-size:16px;padding-top:10px;padding-bottom:10px;margin-bottom:12px;text-align:center} .tpb-select,.tpb-radio{margin-top:6px;text-align:left;border:none!important;border-bottom:none!important;border-top:none!important;border-left:none!important;border-right:none!important;outline:none!important;box-shadow:none!important;background:none!important;appearance:none!important;-webkit-appearance:none!important;-moz-appearance:none!important} .tpb-toast{position:fixed;right:12px;bottom:12px;background:#111827;color:#fff;padding:10px 14px;border-radius:8px;opacity:.95;z-index:99999} .sr-live{position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden} .tpb-step[hidden]{display:none!important} .tpb-step:not([hidden]){display:block!important} .tpb-step{opacity:0;transform:translateY(20px);transition:opacity 0.4s ease-out,transform 0.4s ease-out} .tpb-step:not([hidden]){opacity:1;transform:translateY(0)} .tpb-step.entering{opacity:0;transform:translateY(20px);animation:stepEnter 0.4s ease-out forwards} @keyframes stepEnter{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}} .tpb-hint.entering{opacity:0;transform:translateY(15px);animation:textEnter 0.3s ease-out forwards} @keyframes textEnter{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}} .tpb-base-price{transition:all 0.3s ease-in-out} .tpb-base-price.updating{opacity:0.7;transform:scale(0.98)} .tpb-card:not(.tpb-card-horizontal){opacity:0;transform:translateY(15px);animation:cardReveal 0.5s ease-out forwards} .tpb-card:not(.tpb-card-horizontal):nth-child(1){animation-delay:0.1s} .tpb-card:not(.tpb-card-horizontal):nth-child(2){animation-delay:0.2s} .tpb-card:not(.tpb-card-horizontal):nth-child(3){animation-delay:0.3s} .tpb-card:not(.tpb-card-horizontal):nth-child(4){animation-delay:0.4s} .tpb-card:not(.tpb-card-horizontal):nth-child(5){animation-delay:0.5s} .tpb-card:not(.tpb-card-horizontal):nth-child(6){animation-delay:0.6s} @keyframes cardReveal{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}} .tpb-card-horizontal{opacity:1;transform:none;animation:none} .tpb-form-factor-buttons{display:flex!important;gap:40px!important;margin-top:20px!important;flex-wrap:wrap!important;justify-content:flex-start!important;opacity:1!important;transform:none!important;visibility:visible!important;position:relative!important;z-index:999!important} .tpb-form-factor-btn{flex:0 0 auto!important;padding:10px 16px!important;border:2px solid #e3e6ea!important;border-radius:25px!important;background:#fff!important;color:#374151!important;font-size:13px!important;font-weight:600!important;cursor:pointer!important;transition:all 0.3s ease!important;text-align:center!important;box-shadow:0 1px 3px rgba(0,0,0,0.1)!important;white-space:nowrap!important;opacity:1!important;visibility:visible!important;display:flex!important;position:relative!important;z-index:999!important} .tpb-form-factor-btn:hover{border-color:rgb(79,176,137)!important;background:#f0fdf4!important;color:rgb(79,176,137)!important;transform:translateY(-2px)!important;box-shadow:0 4px 12px rgba(79,176,137,0.15)!important} .tpb-form-factor-btn.active{border-color:rgb(79,176,137)!important;background:rgb(79,176,137)!important;color:#fff!important;transform:translateY(-1px)!important;box-shadow:0 6px 16px rgba(79,176,137,0.25)!important} .tpb-form-factor-buttons.entering{opacity:1!important;transform:none!important;animation:none!important} .tpb-form-factor-buttons.exiting{opacity:1!important;transform:none!important;transition:none!important} .tpb-form-factor-btn.entering{opacity:1!important;transform:none!important;animation:none!important} .tpb-form-factor-btn.exiting{opacity:1!important;transform:none!important;transition:none!important}'; 
  
  // Remove any existing CSS with the same ID pattern
  var existingCss = document.querySelectorAll('style[id^="tpb-category-css-"]');
  for(var i = 0; i < existingCss.length; i++) {
    existingCss[i].remove();
  }
  
  document.head.appendChild(css); 
  
  // Ensure form factor buttons are visible
  setTimeout(function() {
    var buttons = document.querySelectorAll('.tpb-form-factor-btn');
    for(var i = 0; i < buttons.length; i++) {
      buttons[i].style.opacity = '1';
      buttons[i].style.visibility = 'visible';
      buttons[i].style.display = '';
    }
  }, 100);
  
  return r; }
  function toast(m){ var t=el('div',{className:'tpb-toast',text:m}); t.setAttribute('role','status'); t.setAttribute('aria-live','polite'); document.body.appendChild(t); setTimeout(function(){ if(t&&t.remove) t.remove(); }, 1800); }
  function fetchProducts(p){
    // Use admin-ajax endpoint directly (WooCommerce API has 401 issues)
    var ajaxParams = new URLSearchParams();
    if (Array.isArray(p.tags) && p.tags.length) ajaxParams.set('tags', p.tags.join(','));
    if (Array.isArray(p.categories) && p.categories.length) ajaxParams.set('categories', p.categories.join(','));
    if (Array.isArray(p.exclude_categories) && p.exclude_categories.length) ajaxParams.set('exclude_categories', p.exclude_categories.join(','));
    if (p.limit) ajaxParams.set('limit', String(p.limit));
    if (p.sort) ajaxParams.set('sort', p.sort);
    
    return fetch('/wp-admin/admin-ajax.php?action=tpb_qv_products&'+ajaxParams.toString(), { credentials:'same-origin' })
      .then(function(r){ 
        if(!r.ok) throw new Error('AJAX '+r.status); 
        return r.json();
      });
  }
  function addCart(id,q,btn){ if(btn){ btn.disabled=true; } return fetch('/?add-to-cart='+encodeURIComponent(id)+(q?('&quantity='+encodeURIComponent(q)):'') ,{credentials:'same-origin'}).then(function(){ toast('Added to cart'); }).catch(function(){ toast('Add to cart failed'); }).finally(function(){ if(btn){ btn.disabled=false; } }); }
  function addQuote(id,btn){ if(btn){ btn.disabled=true; } return fetch('/?add-to-quote='+encodeURIComponent(id),{credentials:'same-origin'}).then(function(){ toast('Added to quote'); }).catch(function(){ toast('Add to quote failed'); }).finally(function(){ if(btn){ btn.disabled=false; } }); }
  function getApiUrl(params) {
    const baseUrl = '/wp-json/wc/v3/products';
    const queryParams = new URLSearchParams();
    
    if (params.tags) {
      queryParams.append('tags', params.tags.join(','));
    }
    if (params.categories) {
      queryParams.append('categories', params.categories.join(','));
    }
    if (params.exclude_categories) {
      queryParams.append('exclude_categories', params.exclude_categories.join(','));
    }
    if (params.limit) {
      queryParams.append('per_page', params.limit);
    }
    if (params.sort) {
      queryParams.append('orderby', params.sort);
    }
    
    return baseUrl + '?' + queryParams.toString();
  }
  function build(){ 
    console.log('Category Stations stepper - build() function called');
    var params=new URLSearchParams(location.search); 
    if(!(params.get('tpb_qv_iframe')==='1' || params.get('tpb_qv')==='1')) {
      console.log('Category Stations stepper - not in iframe mode, exiting');
      return;
    }
    console.log('Category Stations stepper - in iframe mode, proceeding with initialization');
    
    console.log('Category Stations stepper loading...'); 
    
    hideLegacy(); 
    var root=mount(); 
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
    var formFactorContainer=el('div',{className:'tpb-form-factor-buttons'}); 
    ['counter-top','free-standing','wall-mounted'].forEach(function(factor){ 
      var btn=el('button',{className:'tpb-form-factor-btn'}); 
      btn.textContent=factor.split('-').map(function(w){ return w.charAt(0).toUpperCase()+w.slice(1); }).join(' '); 
      btn.dataset.factor=factor; 
      formFactorContainer.appendChild(btn); 
    }); 
    s2.appendChild(formFactorContainer);
    
    // Force apply CSS to form-factor buttons immediately after creation
    setTimeout(function() {
      var buttons = formFactorContainer.querySelectorAll('.tpb-form-factor-btn');
      console.log('Category Stations - Applying form-factor button styling to', buttons.length, 'buttons');
      buttons.forEach(function(btn) {
        btn.style.setProperty('display', 'flex', 'important');
        btn.style.setProperty('flex', '0 0 auto', 'important');
        btn.style.setProperty('white-space', 'nowrap', 'important');
        btn.style.setProperty('padding', '10px 16px', 'important');
        btn.style.setProperty('border', '2px solid #e3e6ea', 'important');
        btn.style.setProperty('border-radius', '25px', 'important');
        btn.style.setProperty('background', '#fff', 'important');
        btn.style.setProperty('color', '#374151', 'important');
        btn.style.setProperty('font-size', '13px', 'important');
        btn.style.setProperty('font-weight', '600', 'important');
        btn.style.setProperty('cursor', 'pointer', 'important');
        btn.style.setProperty('transition', 'all 0.3s ease', 'important');
        btn.style.setProperty('text-align', 'center', 'important');
        btn.style.setProperty('box-shadow', '0 1px 3px rgba(0,0,0,0.1)', 'important');
        btn.style.setProperty('opacity', '1', 'important');
        btn.style.setProperty('visibility', 'visible', 'important');
        btn.style.setProperty('position', 'relative', 'important');
        btn.style.setProperty('z-index', '1', 'important');
      });
      
      formFactorContainer.style.setProperty('display', 'flex', 'important');
      formFactorContainer.style.setProperty('gap', '40px', 'important');
      formFactorContainer.style.setProperty('margin-top', '20px', 'important');
      formFactorContainer.style.setProperty('flex-wrap', 'wrap', 'important');
      formFactorContainer.style.setProperty('justify-content', 'flex-start', 'important');
      
      // NO ANIMATION - Keep buttons always visible
      console.log('Category Stations - Form factor buttons created and should be visible');
      
      // Continuous monitoring to ensure buttons stay visible
      var visibilityMonitor = setInterval(function() {
        var currentButtons = formFactorContainer.querySelectorAll('.tpb-form-factor-btn');
        if (currentButtons.length > 0) {
          currentButtons.forEach(function(btn) {
            // Force visibility with multiple approaches
            btn.style.setProperty('opacity', '1', 'important');
            btn.style.setProperty('visibility', 'visible', 'important');
            btn.style.setProperty('display', 'flex', 'important');
            btn.style.setProperty('position', 'relative', 'important');
            btn.style.setProperty('z-index', '999', 'important');
            
            // Remove any problematic classes
            btn.classList.remove('entering', 'exiting');
          });
          
          // Also ensure container is visible
          formFactorContainer.style.setProperty('opacity', '1', 'important');
          formFactorContainer.style.setProperty('visibility', 'visible', 'important');
          formFactorContainer.style.setProperty('display', 'flex', 'important');
        }
      }, 50); // Check every 50ms for more aggressive monitoring
      
      // Use MutationObserver to watch for any changes to the buttons
      if (window.MutationObserver) {
        var observer = new MutationObserver(function(mutations) {
          mutations.forEach(function(mutation) {
            if (mutation.type === 'attributes' && mutation.attributeName === 'style') {
              var target = mutation.target;
              if (target.classList.contains('tpb-form-factor-btn')) {
                // Immediately fix any style changes that might hide the button
                target.style.setProperty('opacity', '1', 'important');
                target.style.setProperty('visibility', 'visible', 'important');
                target.style.setProperty('display', 'flex', 'important');
                console.log('Category Stations - Fixed button visibility via MutationObserver');
              }
            }
          });
        });
        
        // Observe all form factor buttons
        currentButtons.forEach(function(btn) {
          observer.observe(btn, { attributes: true, attributeFilter: ['style', 'class'] });
        });
      }
      
      // Add hover and active state handlers
      buttons.forEach(function(btn) {
        btn.addEventListener('mouseenter', function() {
          this.style.setProperty('border-color', 'rgb(79,176,137)', 'important');
          this.style.setProperty('background', '#f0fdf4', 'important');
          this.style.setProperty('color', 'rgb(79,176,137)', 'important');
          this.style.setProperty('transform', 'translateY(-2px)', 'important');
          this.style.setProperty('box-shadow', '0 4px 12px rgba(79,176,137,0.15)', 'important');
        });
        
        btn.addEventListener('mouseleave', function() {
          if (!this.classList.contains('active')) {
            this.style.setProperty('border-color', '#e3e6ea', 'important');
            this.style.setProperty('background', '#fff', 'important');
            this.style.setProperty('color', '#374151', 'important');
            this.style.setProperty('transform', 'translateY(0)', 'important');
            this.style.setProperty('box-shadow', '0 1px 3px rgba(0,0,0,0.1)', 'important');
          }
        });
        
        btn.addEventListener('click', function() {
          // Remove active class from all buttons
          buttons.forEach(function(b) {
            b.classList.remove('active');
            b.style.setProperty('border-color', '#e3e6ea', 'important');
            b.style.setProperty('background', '#fff', 'important');
            b.style.setProperty('color', '#374151', 'important');
            b.style.setProperty('transform', 'translateY(0)', 'important');
            b.style.setProperty('box-shadow', '0 1px 3px rgba(0,0,0,0.1)', 'important');
          });
          
          // Add active class to clicked button
          this.classList.add('active');
          this.style.setProperty('border-color', 'rgb(79,176,137)', 'important');
          this.style.setProperty('background', 'rgb(79,176,137)', 'important');
          this.style.setProperty('color', '#fff', 'important');
          this.style.setProperty('transform', 'translateY(-1px)', 'important');
          this.style.setProperty('box-shadow', '0 6px 16px rgba(79,176,137,0.25)', 'important');
        });
      });
    }, 50);
    
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
      // Check for any existing content in the grid that needs to be animated out
      var existingCards = grid.querySelectorAll('.tpb-card');
      var existingEmptyState = grid.querySelector('.tpb-empty-state');
      
      console.log('update() called - existingCards:', existingCards.length, 'existingEmptyState:', !!existingEmptyState);
      
      if(existingCards.length > 0 || existingEmptyState) {
        console.log('Animating out existing content...');
        
        // Force a reflow to ensure the elements are ready for animation
        void grid.offsetWidth;
        
        // Add exit animation to existing cards
        if(existingCards.length > 0) {
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
        if(existingEmptyState) {
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
          grid.innerHTML=''; 
          root.classList.remove('has-results');
          // Continue with product fetching
          fetchProductsAndRender();
        }, 650); // Slightly longer than animation duration
        return; // Exit early, fetchProductsAndRender will handle the rest
      }
      
      console.log('No existing content, proceeding directly...');
      // No existing content, proceed directly
      grid.innerHTML=''; 
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
      var newTitle = state.strategy === 'custom' ? 'Here\'s Your Hardware-Only Kit' : 'Choose Your Category Station';
      if(resH.textContent !== newTitle) {
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
      
      if(!state.strategy){ resize(); return; } 
      
      var tags = [];
      var cats = [];
      var excludeCats = [];
      
      if(state.strategy === 'custom') {
        // Show 27" hardware-only kit for Category Stations
        // Search for all products and filter by name/slug since categories might not match
        console.log('Category Stations - Custom Build: Fetching all products to find hardware kit');
        cats = [];  // Don't filter by categories initially
        tags = [];  // Don't filter by tags initially
        
        // Add custom build description
        var existingCustomDesc = s3.querySelector('.tpb-hint');
        if (!existingCustomDesc || !existingCustomDesc.textContent.includes('custom build project')) {
          var desc = el('div',{className:'tpb-hint'});
          desc.innerHTML = 'For a custom build project, you only need to purchase the electronics hardware, and TPB will offer consultation for your display design, furniture, and all other components of the category station.';
          desc.style.cssText = 'width:100%;max-width:100%;padding:0;margin-bottom:16px;color:#6b7280;font-size:15px;line-height:1.5';
          
          // Insert description after the heading but before the grid
          s3.insertBefore(desc, grid);
          
          // Trigger description entrance animation only when first created
          desc.classList.add('entering');
          setTimeout(function() {
            desc.classList.remove('entering');
          }, 300);
        }
        
        // Set grid for horizontal layout
        grid.style.cssText = 'display:block!important;width:100%!important;max-width:none!important;box-sizing:border-box!important;margin-top:16px!important';
        
      } else if(state.strategy === 'predesigned' && state.formFactor) {
        // Show products for selected form-factor
        resH.textContent = 'Choose Your Category Station';
        
        // Map form-factor to three sub-categories for Category Stations
        // Based on user's exact categories for each form-factor:
        // Counter-top: All Products, All-category Stations (Pick & Place), Pre-designed, Counter-top
        // Free-standing: All Products, All-category Stations (Pick & Place), Pre-designed, Free-standing  
        // Wall-mounted: All Products, All-category Stations (Pick & Place), Pre-designed, Wall-mounted
        console.log('Category Stations - Pre-designed: Using tag-based form-factor filtering');
        // Use tags for form-factor filtering instead of categories
        // Tags: pd-counter-top, pd-free-standing, pd-wall-mounted
        cats = ['pick-place']; // Keep the main category filter
        if(state.formFactor === 'counter-top') {
          tags = ['pd-counter-top'];
        } else if(state.formFactor === 'free-standing') {
          tags = ['pd-free-standing'];
        } else if(state.formFactor === 'wall-mounted') {
          tags = ['pd-wall-mounted'];
        } else {
          tags = []; // Show all if no form-factor selected
        }
        
        // Set grid for vertical 2-column layout
        grid.style.cssText = 'display:grid!important;grid-template-columns:repeat(2,1fr)!important;gap:20px!important;margin-top:16px!important;max-width:100%!important;box-sizing:border-box!important';
      }
      
      // Clear any existing empty state message with exit animation
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
        limit: state.strategy === 'custom' ? 1 : 24
      });
      console.log('Category Stations - Strategy:', state.strategy, 'Form Factor:', state.formFactor);
      console.log('Category Stations - API URL:', getApiUrl({tags: tags, categories: cats, exclude_categories: excludeCats, limit: state.strategy === 'custom' ? 1 : 24}));
      
      fetchProducts({
        tags: tags,
        categories: cats,
        exclude_categories: excludeCats,
        limit: state.strategy === 'custom' ? 50 : 24  // Get more products to see what's available
      }).then(function(d){
        console.log('Category Stations - API Response:', d);
        console.log('Category Stations - API Response Type:', typeof d);
        console.log('Category Stations - API Response Keys:', d ? Object.keys(d) : 'null');
        if(d && d.results) {
          console.log('Category Stations - Results Count:', d.results.length);
          console.log('Category Stations - First Result:', d.results[0]);
          if(d.results.length > 0) {
            console.log('Category Stations - First Product Categories:', d.results[0].categories);
            console.log('Category Stations - First Product Tags:', d.results[0].tags);
            console.log('Category Stations - First Product Name:', d.results[0].name);
            console.log('Category Stations - First Product ID:', d.results[0].id);
            console.log('Category Stations - First Product Slug:', d.results[0].slug);
            console.log('Category Stations - First Product Full Object Keys:', Object.keys(d.results[0]));
            
            // Log detailed category information
            if(d.results[0].categories && Array.isArray(d.results[0].categories)) {
              console.log('Category Stations - Category Details:');
              d.results[0].categories.forEach(function(cat, index) {
                console.log('  Category ' + index + ':', cat);
              });
            }
          }
        }
        var items=Array.isArray(d)?d:(d&&Array.isArray(d.results)?d.results:[]);
        console.log('Category Stations - Items found before filtering:', items.length);
        
        // Filter out Flower Station products - only show Category Station products
        items = items.filter(function(product) {
          // Exclude products with "Flower Station" in the name
          if(product.name && product.name.toLowerCase().includes('flower station')) {
            console.log('Category Stations - Filtering out Flower Station product:', product.name);
            return false;
          }
          
          // Exclude products with "Configure Now" in the name (these are modal triggers, not actual products)
          if(product.name && product.name.toLowerCase().includes('configure now')) {
            console.log('Category Stations - Filtering out Configure Now product:', product.name);
            return false;
          }
          
        // For Custom Build, only show the specific hardware-only kit
        if(state.strategy === 'custom') {
          var name = product.name ? product.name.toLowerCase() : '';
          var slug = product.slug ? product.slug.toLowerCase() : '';
          
          // Only include the exact 27" hardware kit by slug
          if(slug === 'nh-e-27-pick-place-24-sku') {
            console.log('Category Stations - Including exact hardware-only kit:', product.name);
            return true;
          } else {
            console.log('Category Stations - Filtering out non-hardware product:', product.name, '(slug:', product.slug, ')');
            return false;
          }
        }
          
          // For Pre-designed, include Category Station products
          var name = product.name ? product.name.toLowerCase() : '';
          var slug = product.slug ? product.slug.toLowerCase() : '';
          
          if(name.includes('category') || name.includes('station') || 
             slug.includes('category') || slug.includes('station')) {
            console.log('Category Stations - Including Category Station product:', product.name);
            return true;
          }
          
          // If no clear indicators, exclude it
          console.log('Category Stations - Filtering out unclear product:', product.name);
          return false;
        });
        
        console.log('Category Stations - Items found after filtering:', items.length);
        
        // Debug: Show all product names and categories to help identify what's available
        console.log('Category Stations - All products in database with categories:');
        var allItems = Array.isArray(d) ? d : (d && Array.isArray(d.results) ? d.results : []);
        allItems.forEach(function(product, index) {
          console.log('  ' + (index + 1) + '. ' + product.name + ' (ID: ' + product.id + ', Slug: ' + product.slug + ')');
          if(product.categories && Array.isArray(product.categories)) {
            console.log('     Categories:', product.categories.map(function(cat) { return cat.slug; }).join(', '));
          }
        });
        
        if(items.length === 0) {
          console.log('Category Stations - No Category Station products found after filtering.');
        }
        
        if(items.length){
          root.classList.add('has-results');
          console.log('Category Stations - Rendering', items.length, 'products');
          
          // Render products
          items.forEach(function(p){ 
          var card=el('div',{className:'tpb-card'}); 
          
          if(state.strategy === 'custom') {
            // For Custom Build, use horizontal layout with separate class
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
            addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px';
            addToCartBtn.addEventListener('click',function(){ addCart(p.id,1,this); });
            
            var addToQuoteBtn = el('button',{text:'Add to quote'});
            addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px';
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
            addToCartBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#007cba;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px';
            addToCartBtn.addEventListener('click',function(){ addCart(p.id,1,this); });
            
            var addToQuoteBtn = el('button',{text:'Add to quote'});
            addToQuoteBtn.style.cssText = 'flex:1;min-width:120px;padding:8px 12px;background:#6c757d;color:#fff;border:none;border-radius:4px;cursor:pointer;font-size:14px';
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
            
            // Trigger entrance animation after a brief delay
            setTimeout(function() {
              card.style.opacity = '1';
              card.style.transform = 'translateY(0)';
            }, 50);
          }
        });
        } else {
          // No products found - show empty state
          console.log('Category Stations - No products found, showing empty state');
          var emptyState = el('div', {className: 'tpb-empty-state'});
          emptyState.innerHTML = 'There are no category station options with this configuration. Please try a different selection.';
          emptyState.style.cssText = 'text-align:center;padding:40px 20px;color:#6b7280;font-size:16px;line-height:1.5;background:#f8f9fa;border-radius:8px;margin:20px 0;border:1px solid #e3e6ea;opacity:0;transform:translateY(15px);transition:opacity 0.4s ease-out,transform 0.4s ease-out';
          grid.appendChild(emptyState);
          
          // Trigger entrance animation
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
    r.addEventListener('change',function(e){ 
      if(e.target&&e.target.name==='tpb-strategy'){ 
        var hadStrategy = !!state.strategy;
        var hasStrategy = !!e.target.value;
        
        if(!hadStrategy && hasStrategy) {
          // First time selecting strategy
          state.strategy = e.target.value;
          
          if(state.strategy === 'custom') {
            // Skip form-factor step, go straight to product
            s3.hidden = false;
            s3.classList.add('entering');
            setTimeout(function() {
              s3.classList.remove('entering');
            }, 400);
            update();
            
            // Fetch hardware price for base price display
            fetchProducts({
              tags: ['27-inch', 'hardware-only', 'category-station'],
              categories: ['category-station-kits'],
              sort: 'price_asc',
              limit: 1
            }).then(function(d){
              var items = Array.isArray(d) ? d : (d && Array.isArray(d.results) ? d.results : []);
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
              // Animate in step 2 title and hint
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
              
              // Trigger entrance animation for form-factor buttons
              formFactorContainer.classList.add('entering');
              setTimeout(function() {
                formFactorContainer.classList.remove('entering');
              }, 400);
            }, 200);
            
            // Hide Step 3 until form-factor is selected
            s3.hidden = true;
          }
          
          resize();
          return; // Exit early to prevent duplicate update
        } else if(hasStrategy) {
          // Switching strategies - animate out existing content
          s3.hidden = false;
          
          // Animate out all existing content in Step 3
          var existingCards = grid.querySelectorAll('.tpb-card');
          var existingDesc = s3.querySelector('.tpb-hint');
          var existingEmptyState = grid.querySelector('.tpb-empty-state');
          
          // Animate out section title
          resH.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
          resH.style.opacity = '0';
          resH.style.transform = 'translateY(-15px)';
          
          // Animate out existing cards
          if(existingCards.length > 0) {
            existingCards.forEach(function(card) {
              card.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
              card.style.opacity = '0';
              card.style.transform = 'translateY(-15px)';
            });
          }
          
          // Animate out existing description
          if(existingDesc) {
            existingDesc.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            existingDesc.style.opacity = '0';
            existingDesc.style.transform = 'translateY(-15px)';
          }
          
          // Animate out existing empty state message
          if(existingEmptyState) {
            existingEmptyState.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
            existingEmptyState.style.opacity = '0';
            existingEmptyState.style.transform = 'translateY(-15px)';
          }
          
          // Animate out form-factor buttons and step 2 content for ALL strategy switches
          console.log('Category Stations - Starting exit animation for form-factor buttons');
          
          // Animate out form-factor buttons with explicit inline styles
          formFactorContainer.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
          formFactorContainer.style.opacity = '0';
          formFactorContainer.style.transform = 'translateY(-15px)';
          
          // Animate out step 2 title and hint
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
            
            // Reset any inline styles that might interfere with animations
            formFactorContainer.style.opacity = '';
            formFactorContainer.style.transform = '';
            formFactorContainer.style.transition = '';
            
            // Reset step 2 content styles
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
          
          // Wait for exit animations to complete before updating
          setTimeout(function() {
            state.strategy = e.target.value;
            
            // Show/hide appropriate steps
            if(state.strategy === 'custom') {
              s2.hidden = true;
              s3.hidden = false;
              
              // Animate out form-factor buttons and step 2 content when switching to custom
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
              
              // Fetch hardware price for base price display
              fetchProducts({
                tags: ['27-inch', 'hardware-only', 'category-station'],
                categories: ['category-station-kits'],
                sort: 'price_asc',
                limit: 1
              }).then(function(d){
                var items = Array.isArray(d) ? d : (d && Array.isArray(d.results) ? d.results : []);
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
            } else {
              s2.hidden = false;
              s3.hidden = true; // Keep s3 hidden until form-factor is selected
              
              // Trigger entrance animation for step 2 content
              setTimeout(function() {
                // Animate in step 2 title and hint
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
                
                // Trigger entrance animation for form-factor buttons
                formFactorContainer.classList.add('entering');
                setTimeout(function() {
                  formFactorContainer.classList.remove('entering');
                }, 400);
              }, 50);
            }
            
            update(); 
            resize();
          }, 300);
          return; // Exit early to prevent immediate update
        }
        
        // Update state
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
    
    root.appendChild(s1);
    // Trigger step 1 entrance animation on initial load
    setTimeout(function() {
      s1.hidden = false; // Show the element first
      s1.classList.add('entering');
      setTimeout(function() {
        s1.classList.remove('entering');
      }, 400);
    }, 50);
    root.appendChild(s2); 
    root.appendChild(s3); 
    resize(); 
    window.addEventListener('resize', function(){ resize(); }); 
  }
  if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', build);} else { build(); }
})();
