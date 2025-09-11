/* TPB QuickView (delegated) — v1.1
 * - Works even if the trigger class/attributes are on a wrapper.
 * - Does NOT require inline onclick handlers.
 * - Adds ?tpb_qv=1 to the provided product URL.
 * - Provides TPB_QV.open(urlOrProductId) for manual use.
 */
(function (w, d) {
  'use strict';
  const cfg = w.TPB_QV_CFG || {
    home: (location.origin + '/'),
    qv_param: 'tpb_qv'
  };

  const QV = w.TPB_QV || {};
  let overlay, iframe;

  function log() { try { console.log.apply(console, ['[TPB_QV]'].concat([].slice.call(arguments))); } catch(e){} }

  function ensureDOM() {
    if (overlay) return;
    overlay = d.createElement('div');
    overlay.id = 'tpb-qv-overlay';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML = [
      '<div class="tpb-qv-modal" role="dialog" aria-modal="true" aria-label="Product quick view">',
      '  <button class="tpb-qv-close" type="button" aria-label="Close">&times;</button>',
      '  <iframe class="tpb-qv-iframe" src="about:blank" loading="eager" referrerpolicy="no-referrer-when-downgrade"></iframe>',
      '</div>'
    ].join('');
    d.body.appendChild(overlay);

    iframe = overlay.querySelector('.tpb-qv-iframe');

    overlay.addEventListener('click', function (e) {
      if (e.target === overlay) close();
    });
    overlay.querySelector('.tpb-qv-close').addEventListener('click', close);
    d.addEventListener('keydown', function (e) { if (e.key === 'Escape') close(); });
  }

  function paramJoin(url, key, val) {
    const hasQ = url.indexOf('?') > -1;
    return url + (hasQ ? '&' : '?') + encodeURIComponent(key) + '=' + encodeURIComponent(val);
  }

  function normalizeUrl(urlOrId) {
    if (!urlOrId) return null;
    if (/^\d+$/.test(String(urlOrId))) {
      return cfg.home.replace(/\/+$/, '') + '/?p=' + String(urlOrId);
    }
    return String(urlOrId);
  }

  function open(urlOrId) {
    ensureDOM();
    const base = normalizeUrl(urlOrId);
    if (!base) return false;
    const url = paramJoin(base, cfg.qv_param, '1');
    iframe.src = url;
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');
    d.documentElement.classList.add('tpb-qv-locked');
    
    // Set up iframe communication for CPB
    setupIframeCommunication();
    
    return false;
  }

  function close() {
    if (!overlay) return;
    overlay.classList.remove('is-open');
    overlay.setAttribute('aria-hidden', 'true');
    d.documentElement.classList.remove('tpb-qv-locked');
    if (iframe) iframe.removeAttribute('src');
  }

  function onDocClick(e) {
    const trigger = e.target.closest('[data-tpb-quickview], [data-product-url], .tpb-qv-trigger');
    if (!trigger) return;

    const source = trigger.closest('[data-product-url], [data-tpb-quickview]') || trigger;
    const pid = source.getAttribute('data-tpb-quickview');
    const url = source.getAttribute('data-product-url') || source.getAttribute('href');

    const target = url || pid;
    if (!target) return;

    e.preventDefault();
    e.stopPropagation();
    open(target);
  }

  function setupIframeCommunication() {
    if (!iframe) return;
    
    // Listen for messages from iframe
    w.addEventListener('message', function(event) {
      // Verify origin for security
      if (event.origin !== w.location.origin) return;
      
      const data = event.data;
      if (!data || data.type !== 'tpb-qv') return;
      
      switch (data.action) {
        case 'resize':
          // Auto-resize iframe height
          if (data.height) {
            iframe.style.height = data.height + 'px';
          }
          break;
          
        case 'cpb-selection':
          // Handle CPB component selections
          log('CPB Selection:', data.selection);
          handleCPBSelection(data.selection);
          break;
          
        case 'sku-swap':
          // Handle SKU swapping for Pre-designed vs Custom
          log('SKU Swap:', data.sku);
          handleSKUSwap(data.sku, data.path);
          break;
          
        case 'add-to-cart':
          // Handle add to cart from iframe
          log('Add to Cart:', data.productId, data.sku);
          handleAddToCart(data.productId, data.sku);
          break;
      }
    });
    
    // Send configuration to iframe
    iframe.addEventListener('load', function() {
      if (iframe.contentWindow) {
        iframe.contentWindow.postMessage({
          type: 'tpb-qv-config',
          action: 'init',
          config: {
            home: cfg.home,
            qvParam: cfg.qv_param,
            enableSKUSwap: true,
            enableAnalytics: true
          }
        }, w.location.origin);
      }
    });
  }
  
  function handleCPBSelection(selection) {
    // Track CPB component selections for analytics
    if (w.gtag) {
      w.gtag('event', 'cpb_selection', {
        'event_category': 'CPB',
        'event_label': selection.component,
        'value': selection.value
      });
    }
  }
  
  function handleSKUSwap(sku, path) {
    // Handle SKU swapping logic
    log('Swapping to SKU:', sku, 'for path:', path);
    
    // Update iframe URL with new SKU if needed
    const currentUrl = iframe.src;
    const newUrl = currentUrl.replace(/[?&]sku=[^&]*/, '') + 
                   (currentUrl.includes('?') ? '&' : '?') + 'sku=' + encodeURIComponent(sku);
    
    if (newUrl !== currentUrl) {
      iframe.src = newUrl;
    }
  }
  
  function handleAddToCart(productId, sku) {
    // Handle add to cart from modal
    log('Adding to cart:', productId, sku);
    
    // You can implement custom add-to-cart logic here
    // or let the iframe handle it natively
  }

  function diag() {
    const ov = d.getElementById('tpb-qv-overlay');
    return {
      hasOverlay: !!ov,
      iframePresent: !!(ov && ov.querySelector('iframe')),
      iframeSrc: (ov && ov.querySelector('iframe')) ? ov.querySelector('iframe').getAttribute('src') : null
    };
  }

  function wire() {
    if (QV._wired) { log('already wired', cfg); return; }
    ensureDOM();
    d.addEventListener('click', onDocClick, true);
    QV._wired = true;
    log('wired', cfg);
  }

  QV.open = open;
  QV.close = close;
  QV.diag = diag;

  w.TPB_QV = QV;

  if (d.readyState === 'loading') {
    d.addEventListener('DOMContentLoaded', wire);
  } else {
    wire();
  }
})(window, document);
