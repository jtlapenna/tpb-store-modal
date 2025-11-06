(function(){
  'use strict';
  
  function $(s,c){ return (c||document).querySelector(s); }
  function el(t,o){ var n=document.createElement(t); if(o){ if(o.className) n.className=o.className; if(o.text) n.textContent=o.text; if(o.html) n.innerHTML=o.html; } return n; }
  function clear(n){ while(n&&n.firstChild) n.removeChild(n.firstChild); }
  function hideLegacy(){ ['.af_cp_all_components_content','.afcpb-wrapper','.af_cp_content'].forEach(function(sel){ var c=$(sel); if(c){ c.style.display='none'; } }); }
  function resize(){ try{ var h=document.body.scrollHeight; if(window.parent&&window.parent!==window){ window.parent.postMessage({ type:'TPB_QV', action:'resize', height:h }, '*'); } }catch(e){} }
  function mount(){ var r=document.getElementById('tpb-qv-native'); if(r) return r; r=el('div',{className:'tpb-qv-native'}); r.id='tpb-qv-native'; r.setAttribute('role','region'); r.setAttribute('aria-label','Configurator'); var host=$('.woocommerce .product')||$('.type-product')||$('main')||document.body; host.insertBefore(r, host.firstChild||null); var css=el('style'); css.textContent='.tpb-qv-native{font-family:inherit;padding:20px;max-width:90%;box-sizing:border-box;overflow-x:hidden;width:100%;margin-top:-20px} .tpb-step{margin:0;padding:40px 0 0px 40px;max-width:100%;box-sizing:border-box;border:none!important;border-bottom:none!important;border-top:none!important;border-left:none!important;border-right:none!important} .tpb-grid{gap:20px!important;margin-top:16px!important;max-width:100%!important;box-sizing:border-box!important} .tpb-card{border:1px solid #e3e6ea;border-radius:12px;padding:16px;background:#fff;transition:box-shadow 0.2s,transform 0.2s;text-align:left;max-width:100%;box-sizing:border-box} .tpb-card:hover{box-shadow:0 4px 12px rgba(0,0,0,0.1);transform:translateY(-2px)} .tpb-card-horizontal{display:flex!important;gap:24px!important;align-items:flex-start!important;padding:24px!important;min-height:200px!important;width:100%!important;max-width:100%!important;overflow:visible!important} .tpb-card-image{position:relative;padding-bottom:75%;overflow:hidden;border-radius:8px;margin-bottom:12px} .tpb-card-horizontal .tpb-card-image{flex:0 0 200px!important;width:200px!important;height:200px!important;position:relative!important;overflow:hidden!important;border-radius:8px!important;margin-bottom:0!important;padding-bottom:0!important} .tpb-card-horizontal .tpb-card-content{flex:1!important;display:flex!important;flex-direction:column!important;justify-content:space-between!important;min-height:180px!important;padding-left:8px!important;min-width:0!important;width:100%!important} .tpb-card-horizontal .tpb-actions{display:flex!important;gap:16px!important;justify-content:flex-start!important;flex-wrap:nowrap!important;margin-top:8px!important;width:100%!important;min-width:0!important} .tpb-actions{display:flex;gap:8px;margin-top:12px;align-items:center;justify-content:flex-start;flex-wrap:wrap} .tpb-hint{color:#6b7280;font-size:15px;margin:4px 0 8px;text-align:left} .tpb-title{font-weight:700;font-size:18px;margin:8px 0;text-align:left} .tpb-card-title{font-weight:600;font-size:16px;margin-bottom:8px;min-height:40px;line-height:1.3;text-align:center} .tpb-card-horizontal .tpb-card-title{font-size:22px!important;text-align:left!important} .tpb-card-title .parenthetical{font-size:14px;font-weight:400;display:block;margin-top:2px} .tpb-card-price{color:rgb(79 176 137);font-weight:600;font-size:16px;padding-top:10px;padding-bottom:10px;margin-bottom:12px;text-align:center} .tpb-select,.tpb-radio{margin-top:6px;text-align:left;border:none!important;border-bottom:none!important;border-top:none!important;border-left:none!important;border-right:none!important;outline:none!important;box-shadow:none!important;background:none!important;appearance:none!important;-webkit-appearance:none!important;-moz-appearance:none!important} .tpb-toast{position:fixed;right:12px;bottom:12px;background:#111827;color:#fff;padding:10px 14px;border-radius:8px;opacity:.95;z-index:99999} .sr-live{position:absolute;left:-9999px;top:auto;width:1px;height:1px;overflow:hidden} .tpb-step[hidden]{display:none!important} .tpb-step:not([hidden]){display:block!important} .tpb-step{opacity:0;transform:translateY(20px);transition:opacity 0.4s ease-out,transform 0.4s ease-out} .tpb-step:not([hidden]){opacity:1;transform:translateY(0)} .tpb-step.entering{opacity:0;transform:translateY(20px);animation:stepEnter 0.4s ease-out forwards} @keyframes stepEnter{from{opacity:0;transform:translateY(20px)}to{opacity:1;transform:translateY(0)}} .tpb-hint.entering{opacity:0;transform:translateY(15px);animation:textEnter 0.3s ease-out forwards} @keyframes textEnter{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}} .tpb-base-price{transition:all 0.3s ease-in-out} .tpb-base-price.updating{opacity:0.7;transform:scale(0.98)} .tpb-card:not(.tpb-card-horizontal){opacity:0;transform:translateY(15px);animation:cardReveal 0.5s ease-out forwards} .tpb-card:not(.tpb-card-horizontal):nth-child(1){animation-delay:0.1s} .tpb-card:not(.tpb-card-horizontal):nth-child(2){animation-delay:0.2s} .tpb-card:not(.tpb-card-horizontal):nth-child(3){animation-delay:0.3s} .tpb-card:not(.tpb-card-horizontal):nth-child(4){animation-delay:0.4s} .tpb-card:not(.tpb-card-horizontal):nth-child(5){animation-delay:0.5s} .tpb-card:not(.tpb-card-horizontal):nth-child(6){animation-delay:0.6s} @keyframes cardReveal{from{opacity:0;transform:translateY(15px)}to{opacity:1;transform:translateY(0)}} .tpb-card-horizontal{opacity:1;transform:none;animation:none}'; document.head.appendChild(css); return r; }
  function toast(m){ var t=el('div',{className:'tpb-toast',text:m}); t.setAttribute('role','status'); t.setAttribute('aria-live','polite'); document.body.appendChild(t); setTimeout(function(){ if(t&&t.remove) t.remove(); }, 1800); }
  function fetchProducts(p){
    // Try REST first; fallback to admin-ajax if REST unavailable
    return fetch('/wp-json/tpb/v1/qv/products',{
      method:'POST', headers:{ 'Content-Type':'application/json' }, credentials:'same-origin', body: JSON.stringify(p)
    }).then(function(r){ if(!r.ok) throw new Error('REST '+r.status); return r.json(); })
    .catch(function(){
      var params = new URLSearchParams();
      if (Array.isArray(p.tags) && p.tags.length) params.set('tags', p.tags.join(','));
      if (Array.isArray(p.categories) && p.categories.length) params.set('categories', p.categories.join(','));
      if (Array.isArray(p.exclude_categories) && p.exclude_categories.length) params.set('exclude_categories', p.exclude_categories.join(','));
      if (p.limit) params.set('limit', String(p.limit));
      return fetch('/wp-admin/admin-ajax.php?action=tpb_qv_products&'+params.toString(), { credentials:'same-origin' })
        .then(function(r){ return r.json(); });
    });
  }
  function addCart(id,q,btn){ if(btn){ btn.disabled=true; } return fetch('/?add-to-cart='+encodeURIComponent(id)+(q?('&quantity='+encodeURIComponent(q)):'') ,{credentials:'same-origin'}).then(function(){ toast('Added to cart'); }).catch(function(){ toast('Add to cart failed'); }).finally(function(){ if(btn){ btn.disabled=false; } }); }
  function addQuote(id,btn){ if(btn){ btn.disabled=true; } return fetch('/?add-to-quote='+encodeURIComponent(id),{credentials:'same-origin'}).then(function(){ toast('Added to quote'); }).catch(function(){ toast('Add to quote failed'); }).finally(function(){ if(btn){ btn.disabled=false; } }); }
  function build(){ 
    var params=new URLSearchParams(location.search); 
    if(!(params.get('tpb_qv_iframe')==='1' || params.get('tpb_qv')==='1')) {
      return;
    } 
    
    // Load Flower Stations stepper (this file contains the Flower Station stepper)
    console.log('Loading Flower Stations stepper');
    loadFlowerStepper();
  }
  
  
  function loadFlowerStepper() {
    hideLegacy(); 
    var root=mount(); 
    clear(root); 
    var live=el('div',{className:'sr-live'}); 
    live.setAttribute('aria-live','polite'); 
    root.appendChild(live); 
    
    var state={sku:null,strategy:null,hardwarePrice:null,priceUpdateTimeout:null}; 
    
    // Step 1: SKU Selection
    var s1=el('div',{className:'tpb-step'}); 
    s1.appendChild(el('div',{className:'tpb-title',text:'How many SKUs?'})); 
    s1.appendChild(el('div',{className:'tpb-hint',text:'Select how many strains you will display at this station.'})); 
    var sel=el('select',{className:'tpb-select'}); 
    ['','8','10','16','20'].forEach(function(v){ 
      var o=el('option'); 
      o.value=v; 
      o.textContent=v?(v+' SKU'):'Select'; 
      sel.appendChild(o); 
    }); 
    s1.appendChild(sel); 
    
    // Step 2: Build Strategy
    var s2=el('div',{className:'tpb-step'}); 
    s2.appendChild(el('div',{className:'tpb-title',text:'Build Strategy'})); 
    s2.appendChild(el('div',{className:'tpb-hint',text:'Custom Build includes hardware only; Pre-designed includes furniture.'})); 
    var r=el('div',{className:'tpb-radio'}); 
    r.innerHTML='<label><input type="radio" name="tpb-strategy" value="custom"> Custom Build</label> <label style="margin-left:16px"><input type="radio" name="tpb-strategy" value="predesigned"> Pre-designed</label>'; 
    s2.appendChild(r);
    
    // Step 3: Results
    var s3=el('div',{className:'tpb-step'}); 
    var resH=el('div',{className:'tpb-title',text:'Choose Your Station'}); 
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
      var newTitle = state.strategy === 'custom' ? 'Here\'s Your Hardware-Only Kit' : 'Choose Your Station';
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
      
      if(!state.sku||!state.strategy){ resize(); return; } 
      var skuTag = state.sku ? (state.sku+'-sku') : null; 
      var tags = skuTag ? [skuTag] : []; 
      var cats = [];
      var excludeCats = [];
      
      if(state.strategy==='predesigned'){ 
        tags.push('flower-stations'); 
        excludeCats = ['hardware-only-kits'];  // Exclude hardware kits
        
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
      } else if(state.strategy==='custom'){ 
        // For custom build, use the SKU-specific tag to find hardware-only kits
        // The hardware-only kits are the cheapest options for each SKU count 
        
        // Only add custom build description if it doesn't already exist
        var existingCustomDesc = s3.querySelector('.tpb-hint');
        if (!existingCustomDesc || !existingCustomDesc.textContent.includes('custom build project')) {
          // Add custom build description
          var desc = el('div',{className:'tpb-hint'});
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
      if(existingEmptyState) {
        existingEmptyState.style.transition = 'opacity 0.3s ease-out, transform 0.3s ease-out';
        existingEmptyState.style.opacity = '0';
        existingEmptyState.style.transform = 'translateY(-15px)';
        setTimeout(function() {
          existingEmptyState.remove();
        }, 300);
      }
      
      fetchProducts({
        tags: state.strategy === 'custom' ? [state.sku+'-sku'] : tags,
        categories:cats,
        exclude_categories:excludeCats,
        limit: state.strategy === 'custom' ? 1 : 24
      }).then(function(d){
        var items=Array.isArray(d)?d:(d&&Array.isArray(d.results)?d.results:[]);
        if(items.length){
          root.classList.add('has-results');
        } else if(state.strategy === 'predesigned') {
          // Show empty state message for pre-designed when no products found
          var emptyState = el('div', {className: 'tpb-empty-state'});
          emptyState.innerHTML = 'There are no pre-designed options with this SKU-count. Please choose another option.';
          emptyState.style.cssText = 'text-align:center;padding:40px 20px;color:#6b7280;font-size:16px;line-height:1.5;background:#f8f9fa;border-radius:8px;margin:20px 0;border:1px solid #e3e6ea;opacity:0;transform:translateY(15px);transition:opacity 0.4s ease-out,transform 0.4s ease-out';
          grid.appendChild(emptyState);
          
          // Trigger entrance animation
          setTimeout(function() {
            emptyState.style.opacity = '1';
            emptyState.style.transform = 'translateY(0)';
          }, 50);
        }
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
        focusResults(); 
        resize(); 
      }).catch(function(){ toast('Failed to load results'); resize(); root.classList.remove('has-results'); } ); 
    } 
    
    // Single event listener for SKU selection
    sel.addEventListener('change',function(){ 
      // Only hide/show Step 2 when transitioning between no SKU and SKU
      var hadSku = !!state.sku;
      var hasSku = !!this.value;
      
      if(!hadSku && hasSku) {
        // Transitioning from no SKU to SKU - show Step 2 with animation
        s2.hidden = false;
        s2.classList.add('entering');
        setTimeout(function() {
          s2.classList.remove('entering');
        }, 400);
      } else if(hadSku && !hasSku) {
        // Transitioning from SKU to no SKU - hide Step 2
        s2.hidden = true;
        state.strategy = null;
        s3.hidden = true;
      } else if(hasSku) {
        // Already had SKU and still has SKU - keep Step 2 visible, no animation
        s2.hidden = false;
      }
      
      // Update state
      state.sku = this.value || null;
      
      // Clear any pending price update
      if(state.priceUpdateTimeout) {
        clearTimeout(state.priceUpdateTimeout);
      }
      
      // Fetch hardware-only kit price for this SKU
      if(state.sku) {
        fetchProducts({
          tags:[state.sku+'-sku'],
          sort:'price_asc',
          limit:1
        }).then(function(d){
          var items=Array.isArray(d)?d:(d&&Array.isArray(d.results)?d.results:[]);
          if(items.length && items[0].price_html) {
            state.hardwarePrice = items[0].price_html;
            // Send to parent modal with slight delay to prevent double animation
            state.priceUpdateTimeout = setTimeout(function() {
              if(window.parent && window.parent !== window) {
                window.parent.postMessage({
                  type:'TPB_QV',
                  action:'update_base_price',
                  price: items[0].price_html
                }, '*');
              }
            }, 50);
          }
        });
      } else {
        state.hardwarePrice = null;
        // Reset price in parent with slight delay
        state.priceUpdateTimeout = setTimeout(function() {
          if(window.parent && window.parent !== window) {
            window.parent.postMessage({
              type:'TPB_QV',
              action:'update_base_price',
              price: null
            }, '*');
          }
        }, 50);
      }
      
      update(); 
      resize(); 
    }); 
    
    // Single event listener for strategy selection
    r.addEventListener('change',function(e){ 
      if(e.target&&e.target.name==='tpb-strategy'){ 
        // Only hide/show Step 3 when transitioning between no strategy and strategy
        var hadStrategy = !!state.strategy;
        var hasStrategy = !!e.target.value;
        
        if(!hadStrategy && hasStrategy) {
          // Transitioning from no strategy to strategy - show Step 3 with animation
          s3.hidden = false;
          s3.classList.add('entering');
          setTimeout(function() {
            s3.classList.remove('entering');
          }, 400);
        } else if(hadStrategy && !hasStrategy) {
          // Transitioning from strategy to no strategy - hide Step 3
          s3.hidden = true;
        } else if(hasStrategy) {
          // Already had strategy and still has strategy - animate out existing content, then update
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
          
          // Wait for exit animations to complete before updating
          setTimeout(function() {
            // Update state
            state.strategy = e.target.value;
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