/**
 * TPB Category Station Modal Handler (v2.4)
 * - Integrates with TPB_QV modal-state (open/close, body flags, z-index lift)
 * - Idempotent trigger wiring
 * - Robust stepper loader (one-time script load, reused across opens)
 * - Product image: preload cache -> AJAX endpoint -> CONFIG fallback
 * - Scoped selectors to this overlay to avoid collisions
 */

(function ($, win, doc) {
  'use strict';
  if (!win || !doc || !$) return;

  const LOG_PREFIX = '📦 [TPB Category]';
  const CFG   = win.TPB_QV_CONFIG || {};
  const DEBUG = !!CFG.debug;

  const OVERLAY_ID = 'tpb-qv-category-modal';
  const MODAL_TYPE = 'category-station';

  // Defaults from CONFIG (no hard-coded IDs/URLs)
  const DEFAULT_PRODUCT_ID = (CFG.product_ids && CFG.product_ids[MODAL_TYPE]) ? parseInt(CFG.product_ids[MODAL_TYPE], 10) : 0;
  const FALLBACK_URL       = (CFG.fallback_images && CFG.fallback_images[MODAL_TYPE]) ? CFG.fallback_images[MODAL_TYPE] : '';

  // Prefer both id and class patterns for stepper container (supports legacy markup)
  const SELECTORS = {
    overlay:               '#tpb-qv-category-modal',
    dialogInOverlay:       '.tpb-qv-modal',               // holds data-modal-type
    stepperContainerInId:  '#tpb-qv-stepper-container',   // legacy id
    stepperContainerInCl:  '.tpb-qv-stepper-container',   // current class
    imageContainerIn:      '.tpb-qv-left-panel #tpb-qv-product-image',
    closeBtn:              '.tpb-qv-close',
    triggers: '[data-tpb-modal="category-station"], [data-tpb-quickview="category-station"], a[href*="category-station-configure-now"], a[href*="category-station"], a[href*="/product/category-station"], button[data-category="station"]'
  };

  // Guard against double-wiring
  if (win.__TPB_Category_Wired__) {
    if (DEBUG) console.debug(LOG_PREFIX, 'already wired; skipping re-init');
    return;
  }
  win.__TPB_Category_Wired__ = true;

  // Public namespace (debugging)
  win.TPBCategoryModal = win.TPBCategoryModal || {};

  // ---------------------------------------------------------------------------
  // Utilities
  // ---------------------------------------------------------------------------
  function log()   { if (DEBUG) console.log.apply(console, [LOG_PREFIX].concat([].slice.call(arguments))); }
  function warn()  { console.warn.apply(console, [LOG_PREFIX].concat([].slice.call(arguments))); }
  function error() { console.error.apply(console, [LOG_PREFIX].concat([].slice.call(arguments))); }

  function $overlay() { return $(SELECTORS.overlay); }

  function getScoped(elOr$) {
    const $root = elOr$ ? ($(elOr$).length ? $(elOr$) : $overlay()) : $overlay();
    // prefer id container, then class
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

  // Singleton loader for the category stepper script
  function loadCategoryStepperScript() {
    if (win.TPBCategoryStepper && typeof win.TPBCategoryStepper.build === 'function') {
      return Promise.resolve(win.TPBCategoryStepper);
    }
    if (win.__TPBCategoryStepperLoading) return win.__TPBCategoryStepperLoading;

    const base = (CFG.plugin_url || '/wp-content/plugins/tpb-quickview-modal/').replace(/\/+$/,'/');
    const src  = base + 'assets/js/category-stepper-modal.js' + (CFG.debug ? ('?v=' + Date.now()) : '');

    win.__TPBCategoryStepperLoading = new Promise(function (resolve, reject) {
      const EXISTING_ID = 'tpb-category-stepper-js';
      if (doc.getElementById(EXISTING_ID)) {
        const poll = setInterval(() => {
          if (win.TPBCategoryStepper && typeof win.TPBCategoryStepper.build === 'function') {
            clearInterval(poll); resolve(win.TPBCategoryStepper);
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
        if (win.TPBCategoryStepper && typeof win.TPBCategoryStepper.build === 'function') {
          resolve(win.TPBCategoryStepper);
        } else {
          reject(new Error('TPBCategoryStepper loaded but no build()'));
        }
      };
      s.onerror = function () { reject(new Error('Failed to load ' + src)); };
      doc.head.appendChild(s);
    });

    return win.__TPBCategoryStepperLoading;
  }

  // Product image helpers (preload cache → AJAX → CONFIG fallback)
  function setImageInto($container, url, alt) {
    if (!$container || !$container.length || !url) return;
    const $img = $('<img>').attr('src', url).attr('alt', alt || 'Category Station');
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
        setImageInto($image, pre.url, pre.alt || 'Category Station');
        return;
      }
    }

    // 2) AJAX endpoint
    if (productId) {
      return fetchImageViaAjax(productId)
        .then(({ url, alt }) => setImageInto($image, url, alt))
        .catch((e) => {
          warn('AJAX image fetch failed:', e && e.message);
          // 3) Fallback (from config)
          if (FALLBACK_URL) {
            setImageInto($image, FALLBACK_URL, 'Category Station');
          }
        });
    }

    // 3) Fallback (no product id)
    if (FALLBACK_URL) {
      setImageInto($image, FALLBACK_URL, 'Category Station');
    }
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

    return loadCategoryStepperScript()
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
        win.TPB_QV.emit('category:loaded', { productId: pid });
      }
    });

    // Open via modal-state (handles classes, focus-trap, scroll lock, z-index lift)
    if (win.TPB_QV && typeof win.TPB_QV.openModal === 'function') {
      win.TPB_QV.openModal(OVERLAY_ID);
      if (typeof win.TPB_QV.emit === 'function') {
        win.TPB_QV.emit('category:open', { productId: pid });
      }
    } else {
      // Fallback (unlikely with our architecture)
      $root.css('display', 'block').addClass('tpb-qv-visible is-open');
      $('html,body').addClass('tpb-modal-open tpb-qv-open');
    }
  }

  function closeModal() {
    const { $root } = getScoped();
    if (win.TPB_QV && typeof win.TPB_QV.closeModal === 'function') {
      win.TPB_QV.closeModal();
      if (typeof win.TPB_QV.emit === 'function') {
        win.TPB_QV.emit('category:close', {});
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
    const { $stepper } = getScoped($root);
    if ($stepper && $stepper.length) $stepper.empty();
  }

  // ---------------------------------------------------------------------------
  // Wiring (idempotent; modal-state handles backdrop/ESC globally)
  // ---------------------------------------------------------------------------
  function wireTriggers() {
    $(doc).on('click', SELECTORS.triggers, function (e) {
      const $t = $(this);
      const href = $t.attr('href') || $t.data('url') || '';
      // Only intercept when clearly targeting category station
      if (!href ||
          href.indexOf('category-station') !== -1 ||
          $t.data('tpbModal') === 'category-station' ||
          $t.data('tpbQuickview') === 'category-station' ||
          $t.is('button[data-category="station"]')) {
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
    log('Handler loaded. MODAL_TYPE=', MODAL_TYPE, 'DEFAULT_PRODUCT_ID=', DEFAULT_PRODUCT_ID, 'FALLBACK_URL set=', !!FALLBACK_URL);
    wireTriggers();
    wireCloseButton();
    wirePriceUpdates();

    if (DEBUG) console.log(LOG_PREFIX, 'Ready. Using plugin_url:', CFG.plugin_url || '(none)');
  });

  // Expose minimal API for debugging
  win.TPBCategoryModal.open  = function () { openModalForTrigger($(SELECTORS.triggers).first()); };
  win.TPBCategoryModal.close = closeModal;

})(window.jQuery, window, document);
