/**
 * TPB Quick Checkout Station Modal Handler (v2.4)
 * - Integrates with TPB_QV modal-state v2.2 (open/close, focus, scroll-lock)
 * - Idempotent trigger wiring
 * - Robust stepper loader (one-time script load, reused across opens)
 * - Product image: preload cache -> AJAX endpoint -> CONFIG fallback (or plugin asset)
 * - Scoped selectors to this overlay to avoid collisions
 */

(function ($, win, doc) {
  'use strict';
  if (!win || !doc || !$) return;

  const LOG_PREFIX = '⚡ [TPB QuickCheckout]';
  const CFG   = win.TPB_QV_CONFIG || {};
  const DEBUG = !!CFG.debug;

  const OVERLAY_ID = 'tpb-qv-quickcheckout-modal';
  const MODAL_TYPE = 'quickcheckout-station'; // ✅ FIXED: matches CONFIG keys and data-modal-type

  // Defaults from CONFIG (no hard-coded IDs/URLs)
  const DEFAULT_PRODUCT_ID = (CFG.product_ids && CFG.product_ids[MODAL_TYPE]) ? parseInt(CFG.product_ids[MODAL_TYPE], 10) : 0;
  const FALLBACK_URL_CFG   = (CFG.fallback_images && CFG.fallback_images[MODAL_TYPE]) ? CFG.fallback_images[MODAL_TYPE] : '';

  // Safe plugin asset fallback (used only if no CONFIG fallback was given)
  const PLUGIN_BASE = ((CFG.plugin_url || '/wp-content/plugins/tpb-quickview-modal/').replace(/\/+$/,'/'));
  const FALLBACK_URL_PLUGIN = PLUGIN_BASE + 'assets/img/quick-checkout-hero.jpg';

  const SELECTORS = {
    overlay:               '#tpb-qv-quickcheckout-modal',
    dialogInOverlay:       '.tpb-qv-modal',                 // holds data-modal-type
    // support both legacy id and current class for stepper container
    stepperContainerInId:  '#tpb-qv-stepper-container',
    stepperContainerInCl:  '.tpb-qv-stepper-container',
    imageContainerIn:      '.tpb-qv-left-panel #tpb-qv-product-image',
    closeBtn:              '.tpb-qv-close',
    // Narrow triggers to quick-checkout only (covering common slugs/links)
    triggers: [
      '[data-tpb-modal="quickcheckout-station"]',
      '[data-tpb-quickview="quickcheckout-station"]',
      '[data-tpb-modal="quick-checkout"]',
      '[data-tpb-quickview="quick-checkout"]',
      'a[href*="quickcheckout-station-configure-now"]',
      'a[href*="quick-checkout-station-configure-now"]',
      'a[href*="/product/quickcheckout-station"]',
      'a[href*="/product/quick-checkout-station"]',
      'a[href*="/quickcheckout-station"]',
      'a[href*="/quick-checkout-station"]',
      'a[href*="/quick-checkout"]',
      'a[href*="/quickcheckout"]'
    ].join(', ')
  };

  // Guard against double-wiring
  if (win.__TPB_Quick_Wired__) {
    if (DEBUG) console.debug(LOG_PREFIX, 'already wired; skipping re-init');
    return;
  }
  win.__TPB_Quick_Wired__ = true;

  // Public namespace (debugging)
  win.TPBQuickCheckoutModal = win.TPBQuickCheckoutModal || {};

  // ---------------------------------------------------------------------------
  // Utilities
  // ---------------------------------------------------------------------------
  const log   = (...args) => { if (DEBUG) console.log(LOG_PREFIX, ...args); };
  const warn  = (...args) => console.warn(LOG_PREFIX, ...args);
  const error = (...args) => console.error(LOG_PREFIX, ...args);

  const $overlay = () => $(SELECTORS.overlay);

  function getScoped(elOr$) {
    const $root = elOr$ ? ($(elOr$).length ? $(elOr$) : $overlay()) : $overlay();
    let $stepper = $root.find(SELECTORS.stepperContainerInId).first();
    if (!$stepper.length) $stepper = $root.find(SELECTORS.stepperContainerInCl).first();
    return {
      $root,
      $dialog: $root.find(SELECTORS.dialogInOverlay).first(),
      $stepper,
      $image: $root.find(SELECTORS.imageContainerIn).first(),
      $close: $root.find(SELECTORS.closeBtn).first()
    };
  }

  // Try to derive a product id from a trigger element or its URL
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

  // Singleton loader for the quick checkout stepper script
  function loadQuickStepperScript() {
    if (win.TPBQuickCheckoutStepper && typeof win.TPBQuickCheckoutStepper.build === 'function') {
      return Promise.resolve(win.TPBQuickCheckoutStepper);
    }
    if (win.__TPBQuickStepperLoading) return win.__TPBQuickStepperLoading;

    const src  = PLUGIN_BASE + 'assets/js/quickcheckout-stepper-modal.js' + (CFG.debug ? ('?v=' + Date.now()) : '');

    win.__TPBQuickStepperLoading = new Promise(function (resolve, reject) {
      const EXISTING_ID = 'tpb-quickcheckout-stepper-js';
      if (doc.getElementById(EXISTING_ID)) {
        const poll = setInterval(() => {
          if (win.TPBQuickCheckoutStepper && typeof win.TPBQuickCheckoutStepper.build === 'function') {
            clearInterval(poll); resolve(win.TPBQuickCheckoutStepper);
          }
        }, 50);
        setTimeout(() => { clearInterval(poll); reject(new Error('Stepper build not found after existing script')); }, 8000);
        return;
      }

      const s = doc.createElement('script');
      s.id = EXISTING_ID;
      s.src = src;
      s.async = true;
      s.onload = function () {
        if (win.TPBQuickCheckoutStepper && typeof win.TPBQuickCheckoutStepper.build === 'function') {
          resolve(win.TPBQuickCheckoutStepper);
        } else {
          reject(new Error('TPBQuickCheckoutStepper loaded but no build()'));
        }
      };
      s.onerror = function () { reject(new Error('Failed to load ' + src)); };
      doc.head.appendChild(s);
    });

    return win.__TPBQuickStepperLoading;
  }

  // Product image helpers (preload cache → AJAX → CONFIG/Plugin fallback)
  function setImageInto($container, url, alt) {
    if (!$container || !$container.length || !url) return;
    const $img = $('<img>').attr('src', url).attr('alt', alt || 'Quick Checkout Station');
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
          resolve({ url: res.data.image_url, alt: res.data.image_alt || 'Product image' });
        } else {
          reject(new Error('No image in response'));
        }
      }).fail((xhr) => reject(new Error('AJAX failed: ' + (xhr && xhr.status))));
    });
  }

  function chooseAndRenderImage($scope, productId) {
    const { $image } = getScoped($scope);
    if (!$image.length) return;

    // 1) Preloaded cache
    const cache = win.TPB_QV_PRELOADED_IMAGES || null;
    if (cache && productId) {
      const pre = cache[productId] || cache[String(productId)] || cache[Number(productId)];
      if (pre && pre.url) {
        log('Using PRELOADED image for', productId, pre.url);
        setImageInto($image, pre.url, pre.alt || 'Quick Checkout Station');
        return;
      }
    }

    // 2) AJAX endpoint
    if (productId) {
      return fetchImageViaAjax(productId)
        .then(({ url, alt }) => setImageInto($image, url, alt))
        .catch((e) => {
          warn('AJAX image fetch failed:', e && e.message);
          // 3) Fallback (from config or plugin asset)
          const fallback = FALLBACK_URL_CFG || FALLBACK_URL_PLUGIN;
          setImageInto($image, fallback, 'Quick Checkout Station');
        });
    }

    // 3) Fallback (no product id)
    const fallback = FALLBACK_URL_CFG || FALLBACK_URL_PLUGIN;
    setImageInto($image, fallback, 'Quick Checkout Station');
  }

  // Stepper mount (idempotent per open)
  function mountStepper($scope) {
    let { $stepper } = getScoped($scope);
    if (!$stepper.length) {
      warn('No stepper container found; creating one inside right panel.');
      const $right = $overlay().find('.tpb-qv-right-panel').first();
      const $host  = $right.length ? $right : $overlay();
      $host.append('<div class="tpb-qv-stepper-container"></div>');
      $stepper = getScoped($scope).$stepper;
    }

    $stepper.empty().append('<div class="tpb-qv-native" id="tpb-qv-native"></div>');
    const hostEl = $stepper.find('#tpb-qv-native')[0];

    return loadQuickStepperScript()
      .then((Stepper) => {
        if (!hostEl) throw new Error('Missing stepper host');
        if (!hostEl.__tpb_built__) {
          hostEl.__tpb_built__ = true;
        } else {
          log('Stepper host previously built; reusing.');
        }
        if (typeof Stepper.build === 'function') {
          Stepper.build(hostEl, { modalType: MODAL_TYPE });
          log('Stepper built.');
        } else {
          throw new Error('Stepper.build not available');
        }
      })
      .catch((e) => error('Stepper failed to load/mount:', e && e.message));
  }

  // Price updates from stepper (postMessage API)
  function wirePriceUpdates() {
    win.addEventListener('message', function (e) {
      if (!e || !e.data || e.data.type !== 'TPB_QV' || e.data.action !== 'update_base_price') return;
      const priceEl = doc.getElementById('tpb-qv-base-price');
      if (!priceEl) return;
      if (e.data.price) {
        priceEl.innerHTML = e.data.price;
        priceEl.classList.add('updating', 'has-price');
        setTimeout(() => priceEl.classList.remove('updating'), 300);
      }
    });
  }

  // Open / Close flows (delegate to modal-state API)
  function openModalForTrigger($trigger) {
    // Prefer product id from trigger; fallback to CONFIG default
    const derived = deriveProductId($trigger);
    const pid     = derived || DEFAULT_PRODUCT_ID || 0;
    log('Opening modal. productId=', pid, 'derived=', derived, 'default=', DEFAULT_PRODUCT_ID);

    const { $root, $dialog } = getScoped();
    if (!$root.length) { error('Overlay not found:', SELECTORS.overlay); return; }

    // Stamp modal type for variant hooks on the DIALOG element (not the overlay)
    if ($dialog && $dialog.length) {
      $dialog.attr('data-modal-type', MODAL_TYPE);
    }

    // Image + Stepper
    chooseAndRenderImage($root, pid);
    mountStepper($root).then(() => {
      if (win.TPB_QV && typeof win.TPB_QV.emit === 'function') {
        // Emit both spellings for compatibility
        win.TPB_QV.emit('quickcheckout:loaded', { productId: pid });
        win.TPB_QV.emit('quick-checkout:loaded', { productId: pid });
      }
    });

    // Open via modal-state (handles classes, focus-trap, scroll lock, z-index lift)
    if (win.TPB_QV && typeof win.TPB_QV.openModal === 'function') {
      win.TPB_QV.openModal(OVERLAY_ID);
      if (typeof win.TPB_QV.emit === 'function') {
        win.TPB_QV.emit('quickcheckout:open', { productId: pid });
        win.TPB_QV.emit('quick-checkout:open', { productId: pid });
      }
    } else {
      // Fallback (unlikely with our architecture)
      $root.css('display', 'block').addClass('tpb-qv-visible is-open');
      $('html,body').addClass('tpb-modal-open tpb-qv-open');
    }

    // Cache initial base-price HTML once (for reset on close)
    try {
      const priceEl = doc.getElementById('tpb-qv-base-price');
      if (priceEl && !priceEl.getAttribute('data-initial-html')) {
        priceEl.setAttribute('data-initial-html', priceEl.innerHTML);
      }
    } catch (_) {}
  }

  function closeModal() {
    const { $root, $stepper } = getScoped();
    if (win.TPB_QV && typeof win.TPB_QV.closeModal === 'function') {
      win.TPB_QV.closeModal();
      if (typeof win.TPB_QV.emit === 'function') {
        win.TPB_QV.emit('quickcheckout:close', {});
        win.TPB_QV.emit('quick-checkout:close', {});
      }
    } else {
      $root.removeClass('tpb-qv-visible is-open').css('display', 'none');
      $('html,body').removeClass('tpb-modal-open tpb-qv-open');
    }

    // Reset base price text (if present)
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

    // Cleanup host container only (keep the stepper script cached)
    if ($stepper && $stepper.length) $stepper.empty();
  }

  // ---------------------------------------------------------------------------
  // Wiring (idempotent; modal-state handles backdrop/ESC globally)
  // ---------------------------------------------------------------------------
  function wireTriggers() {
    $(doc).on('click', SELECTORS.triggers, function (e) {
      const $t = $(this);
      const href = $t.attr('href') || $t.data('url') || '';
      // Only intercept when clearly targeting quick checkout
      if (!href ||
          href.indexOf('quickcheckout-station') !== -1 ||
          href.indexOf('quick-checkout-station') !== -1 ||
          href.indexOf('/quick-checkout') !== -1 ||
          href.indexOf('/quickcheckout') !== -1 ||
          $t.data('tpbModal') === 'quickcheckout-station' ||
          $t.data('tpbModal') === 'quick-checkout' ||
          $t.data('tpbQuickview') === 'quickcheckout-station' ||
          $t.data('tpbQuickview') === 'quick-checkout') {
        e.preventDefault();
        e.stopPropagation();
        openModalForTrigger($t);
      }
    });
  }

  function wireCloseButton() {
    // Keep close button capture (modal-state also wires .tpb-qv-close)
    $(doc).on('click', SELECTORS.overlay + ' ' + SELECTORS.closeBtn, function (e) {
      e.preventDefault();
      closeModal();
    });
  }

  // ---------------------------------------------------------------------------
  // Init
  // ---------------------------------------------------------------------------
  $(function init() {
    log('Handler loaded. MODAL_TYPE=', MODAL_TYPE, 'DEFAULT_PRODUCT_ID=', DEFAULT_PRODUCT_ID, 'CFG fallback set=', !!FALLBACK_URL_CFG);
    wireTriggers();
    wireCloseButton();
    wirePriceUpdates();

    if (DEBUG) console.log(LOG_PREFIX, 'Ready. Using plugin_url:', CFG.plugin_url || '(none)');
  });

  // Expose minimal API for debugging
  win.TPBQuickCheckoutModal.open  = function () { openModalForTrigger($(SELECTORS.triggers).first()); };
  win.TPBQuickCheckoutModal.close = closeModal;

})(window.jQuery, window, document);
