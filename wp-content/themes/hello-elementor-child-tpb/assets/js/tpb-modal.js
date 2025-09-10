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
