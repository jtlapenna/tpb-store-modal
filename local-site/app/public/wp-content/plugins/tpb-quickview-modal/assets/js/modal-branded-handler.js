/**
 * TPB Branded Station Modal Handler (v2.4, stepper-less)
 * - Integrates with TPB_QV modal-state v2.2 (open/close, focus, scroll-lock)
 * - Idempotent trigger wiring, scoped to the branded overlay
 * - Hero image pipeline: preload cache → admin-ajax → config fallback → plugin asset fallback
 * - Stamps data-modal-type on INNER .tpb-qv-modal for CSS variant hooks
 */

(function ($, win, doc) {
  'use strict';
  if (!win || !doc || !$) return;

  const LOG_PREFIX = '🏷️ [TPB Branded]';
  const CFG   = win.TPB_QV_CONFIG || {};
  const DEBUG = !!CFG.debug;

  // ✅ FIXED: matches CONFIG keys and data-modal-type (singular)
  const MODAL_TYPE = 'branded-station';
  const OVERLAY_ID = 'tpb-qv-branded-modal';

  // Resolve defaults from CONFIG
  const PRODUCT_IDS = (CFG.product_ids || {});
  const FALLBACKS   = (CFG.fallback_images || {});
  const DEFAULT_PRODUCT_ID = parseInt(PRODUCT_IDS[MODAL_TYPE], 10) || 0;

  // Base/plugin path + fallback image
  const PLUGIN_BASE = ((CFG.plugin_url || '/wp-content/plugins/tpb-quickview-modal/').replace(/\/+$/,'/'));
  const FALLBACK_URL_CFG = FALLBACKS[MODAL_TYPE] || '';
  const FALLBACK_URL_PLUGIN = PLUGIN_BASE + 'assets/img/branded-station-hero.jpg';

  const SELECTORS = {
    overlay:         '#tpb-qv-branded-modal',
    dialogInOverlay: '.tpb-qv-modal',                          // holds data-modal-type
    imageContainer:  '.tpb-qv-left-panel #tpb-qv-product-image',
    closeBtn:        '.tpb-qv-close',
    // Branded-only triggers (cover both singular/plural slugs)
    triggers: [
      '[data-tpb-modal="branded-station"]',
      '[data-tpb-quickview="branded-station"]',
      '[data-tpb-modal="branded-stations"]',
      '[data-tpb-quickview="branded-stations"]',
      'a[href*="branded-station-configure-now"]',
      'a[href*="branded-stations-configure-now"]',
      'a[href*="/branded-station"]',
      'a[href*="/branded-stations"]',
      'a[href*="/configure-branded"]'
    ].join(', ')
  };

  // Guard against double-wiring
  if (win.__TPB_Branded_Wired__) {
    if (DEBUG) console.debug(LOG_PREFIX, 'already wired; skipping re-init');
    return;
  }
  win.__TPB_Branded_Wired__ = true;

  // ---------------------------------------------------------------------------
  // Utilities
  // ---------------------------------------------------------------------------
  const log   = (...a) => { if (DEBUG) console.log(LOG_PREFIX, ...a); };
  const warn  = (...a) => console.warn(LOG_PREFIX, ...a);
  const error = (...a) => console.error(LOG_PREFIX, ...a);

  const $overlay = () => $(SELECTORS.overlay);

  function getScoped() {
    const $root   = $overlay();
    return {
      $root,
      $dialog: $root.find(SELECTORS.dialogInOverlay).first(),
      $image:  $root.find(SELECTORS.imageContainer).first(),
      $close:  $root.find(SELECTORS.closeBtn).first()
    };
  }

  // Derive product id from trigger attributes/URL if available
  function deriveProductId($trigger) {
    const dataPid = $trigger.data('productId') || $trigger.attr('data-product-id');
    if (dataPid) return parseInt(dataPid, 10) || null;

    const href = $trigger.attr('href') || $trigger.data('url') || '';
    if (!href) return null;

    const addToCart = href.match(/[?&]add-to-cart=(\d+)/i);
    if (addToCart && addToCart[1]) return parseInt(addToCart[1], 10);

    const postParam = href.match(/[?&](post|product_id)=(\d+)/i);
    if (postParam && postParam[2]) return parseInt(postParam[2], 10);

    const idInPath = href.match(/\/product\/(\d+)(?:\/|$)/i);
    if (idInPath && idInPath[1]) return parseInt(idInPath[1], 10);

    return null;
  }

  // Image helpers
  function setImageInto($container, url, alt) {
    if (!$container || !$container.length || !url) return;
    const $img = $('<img>').attr('src', url).attr('alt', alt || 'Branded Station');
    $img.on('load', function () { $(this).addClass('loaded'); });
    $container.html($img);
  }

  function fetchImageViaAjax(productId) {
    return new Promise((resolve, reject) => {
      if (!CFG.ajax_url || !CFG.nonce || !productId) return reject(new Error('Missing ajax config or productId'));
      $.ajax({
        method: 'POST',
        url: CFG.ajax_url,
        data: { action: 'tpb_qv_get_product_image', nonce: CFG.nonce, product_id: productId },
        dataType: 'json'
      }).done((res) => {
        if (res && res.success && res.data && res.data.image_url) {
          resolve({ url: res.data.image_url, alt: res.data.image_alt || 'Branded Station' });
        } else {
          reject(new Error('No image in response'));
        }
      }).fail((xhr) => reject(new Error('AJAX failed: ' + (xhr && xhr.status))));
    });
  }

  function chooseAndRenderImage(productId) {
    const { $image } = getScoped();
    if (!$image.length) return;

    // 1) Preloaded cache
    const cache = win.TPB_QV_PRELOADED_IMAGES || null;
    if (cache && (productId || productId === 0)) {
      const pre = cache[productId] || cache[String(productId)] || cache[Number(productId)];
      if (pre && pre.url) {
        log('Using PRELOADED image for', productId, pre.url);
        setImageInto($image, pre.url, pre.alt || 'Branded Station');
        return;
      }
    }

    // 2) AJAX endpoint
    if (productId) {
      fetchImageViaAjax(productId)
        .then(({ url, alt }) => setImageInto($image, url, alt))
        .catch((e) => {
          warn('AJAX image fetch failed:', e && e.message);
          // 3) Fallback (config or plugin asset)
          const fallback = FALLBACK_URL_CFG || FALLBACK_URL_PLUGIN;
          setImageInto($image, fallback, 'Branded Station');
        });
      return;
    }

    // 3) Fallback (no product id)
    const fallback = FALLBACK_URL_CFG || FALLBACK_URL_PLUGIN;
    setImageInto($image, fallback, 'Branded Station');
  }

  // Price updates (postMessage from any embedded UI)
  function wirePriceUpdates() {
    win.addEventListener('message', function (e) {
      if (!e || !e.data || e.data.type !== 'TPB_QV' || e.data.action !== 'update_base_price') return;
      const el = doc.getElementById('tpb-qv-base-price');
      if (el && e.data.price) {
        el.innerHTML = e.data.price;
        el.classList.add('updating', 'has-price');
        setTimeout(() => el.classList.remove('updating'), 300);
      }
    });
  }

  // Open / Close
  function openModalForTrigger($trigger) {
    const derived = deriveProductId($trigger);
    const productId = derived || DEFAULT_PRODUCT_ID || 0;
    log('Opening modal. productId=', productId, 'derived=', derived, 'default=', DEFAULT_PRODUCT_ID);

    const { $root, $dialog, $close } = getScoped();
    if (!$root.length) { error('Overlay not found:', SELECTORS.overlay); return; }

    // Stamp variant on INNER modal (CSS hooks)
    if ($dialog && $dialog.length) {
      $dialog.attr('data-modal-type', MODAL_TYPE);
    }

    // Hero image
    chooseAndRenderImage(productId);

    // Open via modal-state
    if (win.TPB_QV && typeof win.TPB_QV.openModal === 'function') {
      win.TPB_QV.openModal(OVERLAY_ID);
      if (typeof win.TPB_QV.emit === 'function') {
        // Emit both for compatibility
        win.TPB_QV.emit('branded-stations:open', { productId });
        win.TPB_QV.emit('branded:open', { productId });
      }
    } else {
      // Fallback visibility
      $root.css('display', 'flex').addClass('is-open tpb-qv-visible');
      $('html,body').addClass('tpb-modal-open tpb-qv-open');
    }

    // a11y focus
    if ($close && $close.length) $close.trigger('focus');
  }

  function closeModal() {
    const { $root } = getScoped();

    // Reset base price (if present)
    try {
      const priceEl = doc.getElementById('tpb-qv-base-price');
      if (priceEl) {
        const initialHtml =
          priceEl.getAttribute('data-initial-html') ||
          'Please choose a SKU-count to see the base-price for electronics hardware.<br>Furniture-inclusive pricing will appear below according to your selections.';
        priceEl.innerHTML = initialHtml;
        priceEl.classList.remove('has-price', 'updating');
        priceEl.style.opacity = '1';
      }
    } catch (_) {}

    if (win.TPB_QV && typeof win.TPB_QV.closeModal === 'function') {
      win.TPB_QV.closeModal();
      if (typeof win.TPB_QV.emit === 'function') {
        win.TPB_QV.emit('branded-stations:close', {});
        win.TPB_QV.emit('branded:close', {});
      }
    } else {
      $root.removeClass('is-open tpb-qv-visible').css('display', 'none');
      $('html,body').removeClass('tpb-modal-open tpb-qv-open');
    }
  }

  // Wiring
  function wireTriggers() {
    $(doc).on('click', SELECTORS.triggers, function (e) {
      const $t = $(this);
      const href = $t.attr('href') || $t.data('url') || '';
      // Only intercept when clearly a branded target (supports singular/plural)
      if (!href ||
          href.indexOf('branded-station') !== -1 ||
          href.indexOf('branded-stations') !== -1 ||
          $t.data('tpbModal') === 'branded-station' ||
          $t.data('tpbModal') === 'branded-stations' ||
          $t.data('tpbQuickview') === 'branded-station' ||
          $t.data('tpbQuickview') === 'branded-stations') {
        e.preventDefault();
        e.stopPropagation();
        openModalForTrigger($t);
      }
    });
  }

  function wireCloseButton() {
    $(doc).on('click', SELECTORS.overlay + ' ' + SELECTORS.closeBtn, function (e) {
      e.preventDefault();
      closeModal();
    });
  }

  // Init
  $(function init() {
    log('Handler loaded. MODAL_TYPE=', MODAL_TYPE, 'DEFAULT_PRODUCT_ID=', DEFAULT_PRODUCT_ID, 'CFG fallback set=', !!FALLBACK_URL_CFG);
    wireTriggers();
    wireCloseButton();
    wirePriceUpdates();

    // Expose minimal API for debugging
    win.TPBBrandedModal = win.TPBBrandedModal || {};
    win.TPBBrandedModal.open  = function () { openModalForTrigger($(SELECTORS.triggers).first()); };
    win.TPBBrandedModal.close = closeModal;
  });

})(window.jQuery, window, document);
