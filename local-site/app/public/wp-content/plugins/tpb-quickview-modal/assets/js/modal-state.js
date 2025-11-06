/* TPB Modal State v2.4.1
 * - Context-aware stacking + scroll lock
 * - Robust focus trap (Elementor-cart aware)
 * - Backdrop/ESC close with runtime toggles (+ per-overlay data-no-esc-close)
 * - Dynamic overlay registry + late-mounted observers
 * - Flexible openModal(id | element | selector)
 * - Lifecycle events: modal:after-open / modal:after-close
 */
(function ($, win, doc) {
  'use strict';
  if (!win || !doc) return;
  if (!$) { console.warn('[TPB_QV] jQuery not found'); return; }
  if (win.TPB_QV && win.TPB_QV._initted_modal_state) {
    console.debug('[TPB_QV] modal-state already initialized; skipping.');
    return;
  }

  const CFG        = win.TPB_QV_CONFIG || {};
  const LOG_PREFIX = '[TPB_QV] modal-state v2.4.1';

  // -------- Event bus --------
  const BUS = (typeof EventTarget !== 'undefined') ? new EventTarget() : doc;

  // Public namespace
  win.TPB_QV = win.TPB_QV || {};
  win.TPB_QV.version = '2.4.1';
  win.TPB_QV.bus  = BUS;
  win.TPB_QV.emit = (name, detail = {}) => BUS.dispatchEvent(new CustomEvent(name, { detail }));
  win.TPB_QV.on   = (name, cb) => BUS.addEventListener(name, cb);

  // -------- Options (runtime-togglable) --------
  const options = {
    backdropClose: true, // can be disabled globally or per-overlay via data-no-backdrop-close
    escClose: true
  };
  win.TPB_QV.setOptions = (opts = {}) => Object.assign(options, opts);

  // -------- Internal state --------
  const state = {
    openModalId: null,
    previouslyFocused: null,
    _overlayAttrObserver: null,
    _domObserver: null
  };
  win.TPB_QV._state = state; // diagnostics

  // -------- Helpers --------
  function isElem(el) {
    return el && (el.nodeType === 1 || el === win || el === doc);
  }

  function resolveOverlayInput(input) {
    // Accept id string, selector string, element, or jQuery object
    if (!input) return null;
    if (typeof input === 'string') {
      // If it's an id without '#', treat as id
      if (!input.trim().startsWith('#') && !input.includes(' ')) {
        return doc.getElementById(input) || null;
      }
      try { return doc.querySelector(input); } catch { return null; }
    }
    if (win.jQuery && input instanceof win.jQuery) {
      return input.length ? input[0] : null;
    }
    return isElem(input) ? input : null;
  }

  function $overlays() { return $('.tpb-qv-overlay'); }

  function visibleOverlayEl() {
    // Prefer explicit open class
    const el = doc.querySelector('.tpb-qv-overlay.tpb-qv-visible');
    if (el) return el;
    let candidate = null;
    $overlays().each(function () {
      const $el = $(this);
      const shown = $el.is(':visible') && $el.css('opacity') !== '0';
      if (shown) { candidate = this; return false; }
    });
    return candidate;
  }

  function anyOverlayVisible() { return !!visibleOverlayEl(); }

  function applyGlobalFlags() {
    const open = anyOverlayVisible();
    doc.documentElement.classList.toggle('tpb-modal-open', open);
    doc.body.classList.toggle('tpb-modal-open', open);
    doc.body.classList.toggle('tpb-qv-open', open); // legacy/compat lift flag
  }

  // Expose a couple debug helpers
  win.TPB_QV.visibleOverlayEl = visibleOverlayEl;
  win.TPB_QV.applyGlobalFlags = applyGlobalFlags;

  function getOverlay(idOrEl) {
    if (idOrEl) {
      const el = resolveOverlayInput(idOrEl);
      if (el) return el;
      // If a raw id string that didn't resolve, try as id explicitly
      if (typeof idOrEl === 'string') return doc.getElementById(idOrEl);
    }
    return visibleOverlayEl();
  }

  function ensureModalA11y(overlayEl) {
    if (!overlayEl) return;
    const dialog = overlayEl.querySelector('.tpb-qv-modal') || overlayEl;
    dialog.setAttribute('role', dialog.getAttribute('role') || 'dialog');
    dialog.setAttribute('aria-modal', dialog.getAttribute('aria-modal') || 'true');
    if (!dialog.hasAttribute('tabindex')) dialog.setAttribute('tabindex', '-1');
  }

  function focusableWithin(container) {
    if (!container) return [];
    const SEL = [
      'a[href]',
      'button:not([disabled]):not([tabindex="-1"])',
      'input:not([disabled]):not([type="hidden"]):not([tabindex="-1"])',
      'select:not([disabled]):not([tabindex="-1"])',
      'textarea:not([disabled]):not([tabindex="-1"])',
      '[tabindex]:not([tabindex="-1"])',
      '[contenteditable="true"]'
    ].join(',');
    const all = Array.from(container.querySelectorAll(SEL))
      .filter(el => el.offsetWidth + el.offsetHeight > 0 && getComputedStyle(el).visibility !== 'hidden');
    return all;
  }

  function trapFocus(overlayEl) {
    if (!overlayEl) return;
    const dialog = overlayEl.querySelector('.tpb-qv-modal') || overlayEl;
    const onKeydown = function (e) {
      if (e.key !== 'Tab') return;

      // If Elementor cart is shown, we don't trap focus
      if (doc.body.classList.contains('elementor-menu-cart--shown')) return;

      const focusables = focusableWithin(dialog);
      if (!focusables.length) {
        dialog.focus();
        e.preventDefault();
        return;
      }
      const first = focusables[0];
      const last  = focusables[focusables.length - 1];
      const active = doc.activeElement;

      if (e.shiftKey) {
        if (active === first || !dialog.contains(active)) {
          last.focus();
          e.preventDefault();
        }
      } else {
        if (active === last || !dialog.contains(active)) {
          first.focus();
          e.preventDefault();
        }
      }
    };
    overlayEl._tpbTrapHandler = onKeydown;
    doc.addEventListener('keydown', onKeydown, true);
  }

  function untrapFocus(overlayEl) {
    const handler = overlayEl && overlayEl._tpbTrapHandler;
    if (handler) {
      doc.removeEventListener('keydown', handler, true);
      delete overlayEl._tpbTrapHandler;
    }
  }

  function rememberFocus() {
    try { state.previouslyFocused = doc.activeElement || null; } catch { state.previouslyFocused = null; }
  }

  function restoreFocus() {
    const el = state.previouslyFocused;
    state.previouslyFocused = null;
    if (!el) return;
    try {
      if (doc.contains(el) && typeof el.focus === 'function') el.focus();
    } catch { /* ignore */ }
  }

  // Read the longer of transition/animation durations (ms) to time display:none
  function getTransitionMs(el) {
    if (!el) return 300;
    const cs = getComputedStyle(el);
    const td = (cs.transitionDuration || '0s').split(',').map(s => parseFloat(s) || 0);
    const ad = (cs.animationDuration || '0s').split(',').map(s => parseFloat(s) || 0);
    const tmax = Math.max(0, ...td) * 1000;
    const amax = Math.max(0, ...ad) * 1000;
    const total = Math.max(tmax, amax);
    return Number.isFinite(total) && total > 0 ? total : 300;
  }

  function showOverlay(idOrEl) {
    const el = getOverlay(idOrEl);
    if (!el) return;

    ensureModalA11y(el);

    // Show before animation
    el.style.display = 'block';
    el.removeAttribute('aria-hidden');
    el.classList.add('tpb-qv-visible'); // canonical open class
    el.classList.add('is-open');        // legacy alias

    applyGlobalFlags();

    rememberFocus();

    const dialog = el.querySelector('.tpb-qv-modal') || el;
    const focusables = focusableWithin(dialog);
    setTimeout(() => (focusables[0] || dialog).focus({ preventScroll: true }), 0);

    trapFocus(el);

    // after-open (next frame)
    setTimeout(() => {
      const detail = { id: el.id || null, el };
      doc.dispatchEvent(new CustomEvent('tpb:modal:after-open', { detail }));
      BUS.dispatchEvent(new CustomEvent('modal:after-open', { detail }));
    }, 0);
  }

  function hideOverlay(idOrEl) {
    const el = getOverlay(idOrEl);
    if (!el) return;

    untrapFocus(el);
    el.classList.remove('tpb-qv-visible', 'is-open');
    el.setAttribute('aria-hidden', 'true');

    // Let CSS transitions play, then display:none to avoid flicker / snap
    const delay = getTransitionMs(el);
    setTimeout(() => {
      el.style.display = 'none';
      applyGlobalFlags();
      restoreFocus();

      // after-close
      const detail = { id: el.id || null, el };
      doc.dispatchEvent(new CustomEvent('tpb:modal:after-close', { detail }));
      BUS.dispatchEvent(new CustomEvent('modal:after-close', { detail }));
    }, delay);
  }

  function openModal(idOrEl) {
    const target = getOverlay(idOrEl) || getOverlay(null);
    const targetId = target ? (target.id || null) : null;
    if (!target) return;

    if (state.openModalId && state.openModalId !== targetId) {
      hideOverlay(state.openModalId);
    }

    state.openModalId = targetId;
    showOverlay(target);

    const detail = { id: state.openModalId, el: target };
    doc.dispatchEvent(new CustomEvent('tpb:modal:open', { detail }));
    BUS.dispatchEvent(new CustomEvent('modal:open', { detail }));
  }

  function closeModal() {
    const current = state.openModalId || (getOverlay(null)?.id || null);
    if (!current) return;

    hideOverlay(current);
    const closedId = current;
    state.openModalId = null;

    const detail = { id: closedId, el: getOverlay(closedId) || null };
    doc.dispatchEvent(new CustomEvent('tpb:modal:close', { detail }));
    BUS.dispatchEvent(new CustomEvent('modal:close', { detail }));
  }

  // -------- Backdrop click-to-close (capture phase) --------
  function wireBackdropClose() {
    doc.addEventListener('click', function (e) {
      const el = visibleOverlayEl();
      if (!el) return;

      // Respect global toggle
      if (!options.backdropClose) return;

      // If overlay declares no-backdrop-close, honor it
      if (el.hasAttribute('data-no-backdrop-close')) return;

      // If Elementor cart is open, TPB overlay pointer-events are none; still, be safe:
      if (doc.body.classList.contains('elementor-menu-cart--shown')) return;

      const dialog = el.querySelector('.tpb-qv-modal');
      if (!dialog) return;
      const clickedInsideDialog = dialog.contains(e.target);
      if (!clickedInsideDialog) {
        e.preventDefault();
        closeModal();
      }
    }, true);
  }

  // -------- Close interactions (buttons + ESC) --------
  function wireCloseButtonsAndEsc() {
    doc.addEventListener('click', function (e) {
      const btn = e.target.closest('.tpb-qv-close');
      if (btn) { e.preventDefault(); closeModal(); }
    });

    doc.addEventListener('keydown', function (e) {
      if (e.key !== 'Escape') return;
      const el = visibleOverlayEl();
      if (!el) return;

      // Respect global toggle
      if (!options.escClose) return;

      // Per-overlay opt-out
      if (el.hasAttribute('data-no-esc-close')) return;

      // Let Elementor cart win if shown
      if (doc.body.classList.contains('elementor-menu-cart--shown')) return;

      closeModal();
    });
  }

  // -------- Observers --------
  function observeOverlay(el) {
    if (!state._overlayAttrObserver) {
      state._overlayAttrObserver = new MutationObserver(applyGlobalFlags);
    }
    state._overlayAttrObserver.observe(el, { attributes: true, attributeFilter: ['style', 'class', 'hidden', 'aria-hidden'] });
  }

  function attachObservers() {
    // Observe attributes on all current overlays
    $overlays().each(function () { observeOverlay(this); });

    // Also observe DOM for late-mounted overlays and auto-register them
    if (!state._domObserver) {
      state._domObserver = new MutationObserver((mutations) => {
        for (const m of mutations) {
          if (m.type === 'childList' && m.addedNodes && m.addedNodes.length) {
            m.addedNodes.forEach(node => {
              if (node.nodeType === 1) {
                if (node.matches && node.matches('.tpb-qv-overlay')) {
                  ensureModalA11y(node);
                  observeOverlay(node);
                }
                // If a subtree contains overlays
                const found = node.querySelectorAll ? node.querySelectorAll('.tpb-qv-overlay') : [];
                found.forEach(ov => {
                  ensureModalA11y(ov);
                  observeOverlay(ov);
                });
              }
            });
          }
        }
      });
      state._domObserver.observe(doc.body, { childList: true, subtree: true });
    }
  }

  // Public overlay registrar (manual)
  win.TPB_QV.registerOverlay = function (elOrSelector) {
    const el = getOverlay(elOrSelector);
    if (!el) return;
    ensureModalA11y(el);
    observeOverlay(el);
  };

  // Public API
  win.TPB_QV.openModal         = openModal;
  win.TPB_QV.closeModal        = closeModal;
  win.TPB_QV.isOpen            = () => anyOverlayVisible();
  win.TPB_QV.getCurrentOverlay = () => getOverlay(state.openModalId);

  // -------- Init --------
  $(function init() {
    if (win.TPB_QV._initted_modal_state) return;
    win.TPB_QV._initted_modal_state = true;

    console.log(LOG_PREFIX, CFG);
    wireCloseButtonsAndEsc();
    wireBackdropClose();
    attachObservers();
    applyGlobalFlags();

    // If an overlay is already visible on load, set up focus management
    const el = visibleOverlayEl();
    if (el) {
      ensureModalA11y(el);
      trapFocus(el);
      const dialog = el.querySelector('.tpb-qv-modal') || el;
      setTimeout(() => (focusableWithin(dialog)[0] || dialog).focus({ preventScroll: true }), 0);
    }
  });

})(window.jQuery, window, document);
