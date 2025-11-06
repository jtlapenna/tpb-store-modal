(function(){
  'use strict';

  // Simple in-memory cache of preloaded URLs
  window.TPB_QV_IMAGE_CACHE = window.TPB_QV_IMAGE_CACHE || {};
  
  // Global cache of preloaded image URLs by product ID
  // Format: { productId: { url: '...', alt: '...', loaded: true } }
  window.TPB_QV_PRELOADED_IMAGES = window.TPB_QV_PRELOADED_IMAGES || {};
  
  // Track which products are currently being fetched to prevent duplicates
  window.TPB_QV_FETCHING = window.TPB_QV_FETCHING || {};

  function preload(src) {
    if (!src || window.TPB_QV_IMAGE_CACHE[src]) return;
    var img = new Image();
    img.onload = function(){ 
      window.TPB_QV_IMAGE_CACHE[src] = true;
      console.log('🖼️ [PRELOAD] Image loaded into cache:', src);
    };
    img.onerror = function() {
      console.warn('🖼️ [PRELOAD] Failed to load image:', src);
    };
    img.src = src;
  }

  function fetchFeaturedImage(productId) {
    if (!productId || !window.TPB_QV_PRELOAD || !window.TPB_QV_PRELOAD.baseUrl) {
      console.warn('🖼️ [PRELOAD] Missing requirements - productId:', productId, 'TPB_QV_PRELOAD:', !!window.TPB_QV_PRELOAD);
      return Promise.resolve(null);
    }
    
    // Check if already cached or currently fetching
    var pidStr = String(productId);
    var pidNum = Number(productId);
    if (window.TPB_QV_PRELOADED_IMAGES[pidStr] || window.TPB_QV_PRELOADED_IMAGES[pidNum]) {
      // Already cached, return cached URL
      var cached = window.TPB_QV_PRELOADED_IMAGES[pidStr] || window.TPB_QV_PRELOADED_IMAGES[pidNum];
      return Promise.resolve(cached.url);
    }
    if (window.TPB_QV_FETCHING[pidStr] || window.TPB_QV_FETCHING[pidNum]) {
      // Currently fetching, return existing promise
      return window.TPB_QV_FETCHING[pidStr] || window.TPB_QV_FETCHING[pidNum];
    }
    
    var base = window.TPB_QV_PRELOAD.baseUrl.replace(/\/$/, '');
    console.log('🖼️ [PRELOAD] Fetching featured image for product ID:', productId, '(type:', typeof productId + ')');
    
    // Mark as fetching
    var fetchPromise = null;

    // 1) Get product to read featured_media
    fetchPromise = fetch(base + '/wp-json/wp/v2/product/' + productId + '?_=' + Date.now(), { credentials: 'same-origin' })
      .then(function(r){ if(!r.ok) throw new Error('Product ' + r.status); return r.json(); })
      .then(function(product){
        if (!product || !product.featured_media) throw new Error('No featured_media');
        return fetch(base + '/wp-json/wp/v2/media/' + product.featured_media + '?_=' + Date.now(), { credentials: 'same-origin' });
      })
      .then(function(r){ if(!r.ok) throw new Error('Media ' + r.status); return r.json(); })
      .then(function(media){
        var url = (media && (media.source_url || (media.media_details && media.media_details.sizes && media.media_details.sizes.full && media.media_details.sizes.full.source_url))) || null;
        if (url) {
          // Store in global cache by product ID (store as both string and number for compatibility)
          var pidStr = String(productId);
          var pidNum = Number(productId);
          window.TPB_QV_PRELOADED_IMAGES[pidStr] = {
            url: url,
            alt: (media && media.alt_text) || '',
            loaded: false
          };
          // Also store as number key for compatibility
          if (!isNaN(pidNum)) {
            window.TPB_QV_PRELOADED_IMAGES[pidNum] = window.TPB_QV_PRELOADED_IMAGES[pidStr];
          }
          console.log('🖼️ [PRELOAD] Stored image in cache for product', productId, 'URL:', url);
          
          // Inject <link rel="preload" as="image"> to hint browser
          try {
            var link = document.createElement('link');
            link.rel = 'preload';
            link.as = 'image';
            link.href = url;
            document.head.appendChild(link);
            console.log('🖼️ [PRELOAD] Added <link rel="preload"> for product', productId, ':', url);
          } catch(e) {
            console.warn('🖼️ [PRELOAD] Failed to add preload link:', e);
          }
          
          // Preload the image into browser cache
          preload(url);
          
          // Mark as loaded when image actually loads
          var img = new Image();
          img.onload = function() {
            var pidStr = String(productId);
            var pidNum = Number(productId);
            if (window.TPB_QV_PRELOADED_IMAGES[pidStr]) {
              window.TPB_QV_PRELOADED_IMAGES[pidStr].loaded = true;
              if (!isNaN(pidNum) && window.TPB_QV_PRELOADED_IMAGES[pidNum]) {
                window.TPB_QV_PRELOADED_IMAGES[pidNum].loaded = true;
              }
              console.log('🖼️ [PRELOAD] ✅ Image fully loaded for product', productId, ':', url);
            }
          };
          img.onerror = function() {
            console.error('🖼️ [PRELOAD] ❌ Failed to load image for product', productId, ':', url);
          };
          img.src = url;
        }
        
        // Clear fetching flag
        delete window.TPB_QV_FETCHING[pidStr];
        if (!isNaN(pidNum)) delete window.TPB_QV_FETCHING[pidNum];
        
        return url;
      })
      .catch(function(err){
        // Clear fetching flag on error
        delete window.TPB_QV_FETCHING[pidStr];
        if (!isNaN(pidNum)) delete window.TPB_QV_FETCHING[pidNum];
        console.error('🖼️ [PRELOAD] Error fetching image for product', productId, ':', err);
        return null;
      });
    
    // Store promise to prevent duplicate fetches
    window.TPB_QV_FETCHING[pidStr] = fetchPromise;
    if (!isNaN(pidNum)) window.TPB_QV_FETCHING[pidNum] = fetchPromise;
    
    return fetchPromise;
  }

  function preloadFromHref(href) {
    if (!href || !window.TPB_QV_PRELOAD || !window.TPB_QV_PRELOAD.baseUrl) return;
    try {
      var url = new URL(href, window.location.origin);
      var path = url.pathname || '';
      var parts = path.split('/').filter(Boolean);
      var slugIndex = parts.indexOf('product');
      var slug = slugIndex >= 0 && parts[slugIndex + 1] ? parts[slugIndex + 1] : parts[parts.length - 1];
      if (!slug) return;
      var base = window.TPB_QV_PRELOAD.baseUrl.replace(/\/$/, '');
      fetch(base + '/wp-json/wp/v2/product?slug=' + encodeURIComponent(slug) + '&_=' + Date.now(), { credentials: 'same-origin' })
        .then(function(r){ if(!r.ok) throw new Error('ProductBySlug ' + r.status); return r.json(); })
        .then(function(arr){ if(!Array.isArray(arr) || !arr.length) throw new Error('No product for slug'); return arr[0]; })
        .then(function(product){
          if (!product || !product.featured_media) throw new Error('No featured_media');
          return fetch(base + '/wp-json/wp/v2/media/' + product.featured_media + '?_=' + Date.now(), { credentials: 'same-origin' });
        })
        .then(function(r){ if(!r.ok) throw new Error('Media ' + r.status); return r.json(); })
        .then(function(media){
          var url = (media && (media.source_url || (media.media_details && media.media_details.sizes && media.media_details.sizes.full && media.media_details.sizes.full.source_url))) || null;
          if (url) preload(url);
        })
        .catch(function(){});
    } catch(e) {}
  }

  function init() {
    console.log('🖼️ [DIAG] Preloader init - TPB_QV_PRELOAD exists:', !!window.TPB_QV_PRELOAD);
    if (!window.TPB_QV_PRELOAD || !window.TPB_QV_PRELOAD.products) {
      console.warn('🖼️ [DIAG] TPB_QV_PRELOAD missing or no products');
      return;
    }

    var products = window.TPB_QV_PRELOAD.products;
    console.log('🖼️ [PRELOAD] Initializing preloader for products:', products);
    
    // Preload all images immediately on page load
    var preloadPromises = [];
    
    if (products['flower-station']) {
      console.log('🖼️ [PRELOAD] Starting preload for Flower Station (ID:', products['flower-station'] + ')');
      preloadPromises.push(fetchFeaturedImage(products['flower-station']).then(function(url) {
        if (url) {
          console.log('🖼️ [PRELOAD] ✅ Flower Station image ready:', url);
        } else {
          console.warn('🖼️ [PRELOAD] ❌ Flower Station image failed to load');
        }
      }).catch(function(err) {
        console.error('🖼️ [PRELOAD] ❌ Flower Station preload error:', err);
      }));
    }
    
    if (products['category-station']) {
      console.log('🖼️ [PRELOAD] Starting preload for Category Station (ID:', products['category-station'] + ')');
      preloadPromises.push(fetchFeaturedImage(products['category-station']).then(function(url) {
        if (url) {
          console.log('🖼️ [PRELOAD] ✅ Category Station image ready:', url);
        } else {
          console.warn('🖼️ [PRELOAD] ❌ Category Station image failed to load');
        }
      }).catch(function(err) {
        console.error('🖼️ [PRELOAD] ❌ Category Station preload error:', err);
      }));
    }
    
    if (products['quickcheckout']) {
      console.log('🖼️ [PRELOAD] Starting preload for Quick Checkout (ID:', products['quickcheckout'] + ')');
      preloadPromises.push(fetchFeaturedImage(products['quickcheckout']).then(function(url) {
        if (url) {
          console.log('🖼️ [PRELOAD] ✅ Quick Checkout image ready:', url);
        } else {
          console.warn('🖼️ [PRELOAD] ❌ Quick Checkout image failed to load');
        }
      }).catch(function(err) {
        console.error('🖼️ [PRELOAD] ❌ Quick Checkout preload error:', err);
      }));
    }
    
    // Log when all preloads complete
    Promise.allSettled(preloadPromises).then(function(results) {
      var successCount = results.filter(function(r) { return r.status === 'fulfilled'; }).length;
      console.log('🖼️ [PRELOAD] All preloads complete:', successCount + '/' + results.length, 'succeeded');
      console.log('🖼️ [PRELOAD] Preloaded images cache:', window.TPB_QV_PRELOADED_IMAGES);
    });

    var triggerSelectors = [
      // Flower Station
      '[data-tpb-modal="flower-station"]',
      '[data-tpb-quickview="flower-station"]',
      'a[href*="flower-station-configure-now"]',
      'a[href*="/product/flower-station"]',
      // Category Station
      '[data-tpb-modal="category-station"]',
      '[data-tpb-quickview="category-station"]',
      'a[href*="category-station-configure-now"]',
      'a[href*="/product/category-station"]',
      // Quick Checkout
      '[data-tpb-modal="quickcheckout"]',
      '[data-tpb-quickview="quickcheckout"]',
      'a[href*="quickcheckout-station"]',
      'a[href*="/product/quickcheckout"]',
      // Menu Boards / Branded Stations
      'a[href*="menu-board"]', 'a[href*="menu-boards"]',
      'a[href*="branded-station"]', 'a[href*="branded-stations"]'
    ];

    document.addEventListener('mouseover', function(e){
      var el = e.target.closest(triggerSelectors.join(','));
      if (!el) return;
      if (products['flower-station']) fetchFeaturedImage(products['flower-station']);
      if (products['category-station']) fetchFeaturedImage(products['category-station']);
      if (products['quickcheckout']) fetchFeaturedImage(products['quickcheckout']);
      var href = el.getAttribute('href');
      if (href) preloadFromHref(href);
    }, { passive: true });
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', init);
  } else {
    init();
  }
})();