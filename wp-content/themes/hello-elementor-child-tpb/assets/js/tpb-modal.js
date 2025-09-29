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
    
    // Check if this is a staging URL and use appropriate parameter
    const isStaging = base.includes('staging') || base.includes('tpb_qv_staging');
    const qvParam = isStaging ? 'tpb_qv_staging' : cfg.qv_param;
    const url = paramJoin(base, qvParam, '1');
    
    log('Opening modal with URL:', url);
    iframe.src = url;
    overlay.classList.add('is-open');
    overlay.setAttribute('aria-hidden', 'false');
    overlay.style.setProperty('display', 'block', 'important');
    overlay.style.display = 'block';
    overlay.classList.add('is-open');
    
    // Force visibility with multiple approaches
    setTimeout(() => {
        overlay.style.setProperty('display', 'block', 'important');
        overlay.style.display = 'block';
        overlay.classList.add('is-open');
    }, 100);
    
    // Additional force after iframe loads
    iframe.onload = () => {
        overlay.style.setProperty('display', 'block', 'important');
        overlay.style.display = 'block';
        overlay.classList.add('is-open');
        // Force again after a delay
        setTimeout(() => {
            overlay.style.setProperty('display', 'block', 'important');
            overlay.style.display = 'block';
            overlay.classList.add('is-open');
        }, 50);
    };
    
    // Multiple timeouts to force display
    [50, 100, 200, 500].forEach(delay => {
        setTimeout(() => {
            overlay.style.setProperty('display', 'block', 'important');
            overlay.style.display = 'block';
            overlay.classList.add('is-open');
            // Also add CSS rule to override any conflicting styles
            const style = document.createElement('style');
            style.textContent = `.tpb-qv-overlay { display: block !important; visibility: visible !important; opacity: 1 !important; }`;
            document.head.appendChild(style);
        }, delay);
    });
    d.documentElement.classList.add('tpb-qv-locked');
    
    // Diagnostic logging for two-tone issue
    log('Modal opened, checking z-index...');
    const computedStyle = window.getComputedStyle(overlay);
    log('Overlay z-index:', computedStyle.zIndex);
    log('Overlay position:', computedStyle.position);
    log('Overlay display:', computedStyle.display);
    
    // Check for elements with higher z-index
    const allElements = d.querySelectorAll('*');
    const highZIndexElements = Array.from(allElements).filter(el => {
      const zIndex = parseInt(window.getComputedStyle(el).zIndex);
      return zIndex > 999999;
    });
    if (highZIndexElements.length > 0) {
      log('⚠️ Found elements with higher z-index:', highZIndexElements.map(el => ({
        tag: el.tagName,
        zIndex: window.getComputedStyle(el).zIndex,
        className: el.className
      })));
    }
    
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

    // Validate and normalize before cancelling the native navigation
    const normalized = normalizeUrl(target);
    if (!normalized || normalized === '#' || /javascript:/i.test(normalized)) {
      return; // let the click behave normally if we don't have a valid URL
    }

    e.preventDefault();
    e.stopPropagation();
    open(normalized);
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
    // Auto-mark common "Configure" CTAs as triggers
    try {
      const markTriggers = () => {
        const candidates = d.querySelectorAll('a');
        candidates.forEach((el) => {
          if (el.classList.contains('tpb-qv-trigger') || el.hasAttribute('data-tpb-quickview') || el.hasAttribute('data-product-url')) return;
          const text = (el.textContent || '').trim().toLowerCase();
          const href = el.getAttribute('href') || '';
          const looksLikeProduct = /\/product\//.test(href) || /[?&]p=\d+/.test(href);
          const isValidHref = href && href !== '#' && !/^javascript:/i.test(href);
          if (!text) return;
          const isConfigureCta = text.includes('configure now') || text === 'configure' || text.includes('customize');
          if (isConfigureCta && isValidHref && looksLikeProduct) {
            el.classList.add('tpb-qv-trigger');
            el.setAttribute('data-product-url', href);
            if (!el.hasAttribute('data-tpb-bound')) {
              el.setAttribute('data-tpb-bound', '1');
              el.addEventListener('click', function(ev){
                const targetUrl = this.getAttribute('data-product-url') || this.getAttribute('href');
                const normalized = normalizeUrl(targetUrl);
                if (!normalized || normalized === '#' || /^javascript:/i.test(normalized)) return;
                ev.preventDefault();
                ev.stopPropagation();
                open(normalized);
              }, true);
            }
          }
        });
      };
      markTriggers();
      // Observe DOM for dynamically injected CTAs
      const mo = new MutationObserver(() => markTriggers());
      mo.observe(d.documentElement, { childList: true, subtree: true });
    } catch(_){}
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
