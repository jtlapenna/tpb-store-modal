<?php
/**
 * MU-plugin loader for TPB Quickview utilities.
 */
require_once __DIR__ . '/tpb-deploy-flush.php';
require_once __DIR__ . '/api-qv.php';


// Parent-page styles (modal chrome) – runs regardless of theme
add_action('wp_enqueue_scripts', function () {
    // Style the close button as a square with rounded corners
    $css = '.tpb-qv-modal .tpb-qv-close, .tpb-qv-close, .tpb-qv-modal button.tpb-qv-close{'
         . 'width:36px!important;height:36px!important;min-width:36px!important;min-height:36px!important;'
         . 'border-radius:8px!important;padding:0!important;background:#f0f2f5!important;color:#2c3e50!important;'
         . 'border:1px solid #dce1e6!important;display:flex!important;align-items:center!important;justify-content:center!important;'
         . 'font-size:22px!important;line-height:1!important;box-shadow:none!important;appearance:none!important}';
    wp_register_style('tpb-qv-chrome-style', false);
    wp_enqueue_style('tpb-qv-chrome-style');
    wp_add_inline_style('tpb-qv-chrome-style', $css);

    // JS guard to normalize close button styles when modal opens
    wp_register_script('tpb-qv-chrome-js', '', [], null, true);
    wp_enqueue_script('tpb-qv-chrome-js');
    $jsguard = "(function(){\n\tfunction styleClose(){\n\t\tvar btn = document.querySelector('.tpb-qv-modal .tpb-qv-close, .tpb-qv-close');\n\t\tif(btn){ btn.style.setProperty('width','36px','important'); btn.style.setProperty('height','36px','important'); btn.style.setProperty('min-width','36px','important'); btn.style.setProperty('min-height','36px','important'); btn.style.setProperty('border-radius','8px','important'); btn.style.setProperty('padding','0','important'); }\n\t}\n\tfunction observe(){\n\t\tvar overlay = document.querySelector('#tpb-qv-overlay'); if(!overlay){ setTimeout(observe,300); return; }\n\t\tvar mo = new MutationObserver(styleClose); mo.observe(overlay,{ attributes:true, attributeFilter:['class']}); styleClose();\n\t}\n\tif(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', observe);} else { observe(); }\n})();";
    wp_add_inline_script('tpb-qv-chrome-js', $jsguard, 'after');

    // Parent-side resize handler for iframe messages
    $resizeParent = "(function(){\n\tfunction onMsg(e){ try{ var d=e&&e.data; if(!d||d.type!=='TPB_QV') return; if(d.action==='resize'){ var ifr=document.getElementById('tpb-qv-iframe'); if(ifr){ var h=parseInt(d.height||0,10); if(!isNaN(h)){ ifr.style.height=Math.max(400,h)+'px'; } } } }catch(err){} }\n\twindow.addEventListener('message', onMsg);\n})();";
    wp_add_inline_script('tpb-qv-chrome-js', $resizeParent, 'after');
}, 12);


// Ensure CPB visibility and attach logic runs even when parent theme is active
add_action('wp_enqueue_scripts', function () {
    // Support either param variant used by the modal
    if (!isset($_GET['tpb_qv']) && !isset($_GET['tpb_qv_iframe'])) return;

    // Inject minimal CSS to enforce class-based visibility
    $css = ' .tpb-cpb-visible{display:block!important;visibility:visible!important}'
         . ' .single_component.tpb-hidden{display:none!important}'
         . ' .single_component.tpb-visible{display:block!important;visibility:visible!important}'
        . ' body.tpb-qv-iframe form.cart .quantity,'
        . ' body.tpb-qv-iframe .yith-ywraq-add-to-quote,'
        . ' body.tpb-qv-iframe [class*="add-to-quote"]{display:none!important}'
        . ' html{margin-top:0!important;margin-bottom:0!important;padding-top:0!important;padding-bottom:0!important}'
        . ' body.tpb-qv-iframe{margin:0!important;padding:0!important;margin-top:0!important;margin-bottom:0!important;padding-top:0!important;padding-bottom:0!important;padding-left:0!important;padding-right:0!important}';
    wp_register_style('tpb-qv-inline-visibility', false);
    wp_enqueue_style('tpb-qv-inline-visibility');
    wp_add_inline_style('tpb-qv-inline-visibility', $css);

    // Inject JS to wait for CPB and show first step without cloning/mutation of body
    $js = <<<'JS'
(function(){
	if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded', boot)} else {boot()}
	function boot(){
		var tries=0; var max=60;
		var timer=setInterval(function(){
			tries++;
			var cpb=document.querySelector('.afcpb-wrapper, .af_cp_all_components_content, .af_cp_content');
			if(cpb){
				// clear any inline display:none then enforce classes
				if(cpb.style&&cpb.style.removeProperty){cpb.style.removeProperty('display');}
				cpb.classList.add('tpb-cpb-visible');
				var steps=cpb.querySelectorAll('.single_component');
				if(steps.length){
					steps.forEach(function(s,i){ if(s.style&&s.style.removeProperty){s.style.removeProperty('display');} s.classList.toggle('tpb-hidden', i!==0); s.classList.toggle('tpb-visible', i===0); });
				}
				clearInterval(timer);
			}
			if(tries>max){clearInterval(timer);}
		},200);
	}
})();
JS;
    // Use our own handle so this prints even when jQuery is not queued
    wp_register_script('tpb-qv-inline-js', '', [], null, true);
    wp_enqueue_script('tpb-qv-inline-js');
    wp_add_inline_script('tpb-qv-inline-js', $js, 'after');

    // Neutralize aggressive hiding injected by parent modal.js after iframe load
    $parentPatch = <<<'JS'
(function(){
	function ensureVisible(doc){
		var cpb = doc.querySelector('.afcpb-wrapper, .af_cp_all_components_content, .af_cp_content');
		if(!cpb) return false;
		if(cpb.style&&cpb.style.removeProperty){cpb.style.removeProperty('display');}
		cpb.classList.add('tpb-cpb-visible');
		var anc = cpb.parentElement; var guard=0;
		while(anc && guard<5){ if(anc.style&&anc.style.display==='none'){ anc.style.removeProperty('display'); } anc=anc.parentElement; guard++; }
		var first = cpb.querySelector('.single_component'); if(first){ if(first.style&&first.style.display==='none'){ first.style.removeProperty('display'); } first.classList.remove('tpb-hidden'); first.classList.add('tpb-visible'); }
		return true;
	}
	function patch(){
		if(!window.TPBModal || typeof window.TPBModal.open!=='function'){ setTimeout(patch,300); return;}
		var originalOpen = window.TPBModal.open.bind(window.TPBModal);
		window.TPBModal.open = function(url){
			var result = originalOpen(url);
			setTimeout(function(){
				var iframe = document.getElementById('tpb-qv-iframe'); if(!iframe) return;
				iframe.addEventListener('load', function(){
					try{
						var doc = iframe.contentDocument || iframe.contentWindow.document; if(!doc) return;
						var hideStyle = doc.getElementById('tpb-qv-hide-css'); if(hideStyle) hideStyle.remove();
						var win = iframe.contentWindow; if(win){ win.initializeModalContent=function(){}; win.initializeProgressiveDisclosure=function(){return true;}; win.hideUnwantedElements=function(){}; }
						// guard loop to counter any late hides (runs for ~6s)
						var loops=0; var visTimer = setInterval(function(){
							loops++; ensureVisible(doc); if(loops>60){ clearInterval(visTimer);}
						}, 100);
					}catch(e){}
				}, { once:true });
			}, 0);
			return result;
		};
	}
	if(document.readyState==='loading'){ document.addEventListener('DOMContentLoaded', patch);} else { patch(); }
})();
JS;
    wp_add_inline_script('tpb-qv-inline-js', $parentPatch, 'after');

    // UI tweaks inside iframe (label/order cleanup)
    $uiFixes = <<<'JS'
(function(){
	function ready(fn){ if(document.readyState==='loading'){document.addEventListener('DOMContentLoaded',fn)} else {fn()} }
	function textMatch(el,needle){ try{ return (el.textContent||'').trim().toLowerCase().indexOf(needle)>=0;}catch(e){return false;} }
	function apply(){
		var root = document;
		// Remove helper text
		Array.from(root.querySelectorAll('p,div,h3,h4')).forEach(function(n){ if(textMatch(n,'choose skus, mount, and finish.')){ n.remove(); } });
		// Remove instock and view product
		Array.from(root.querySelectorAll('.af_cp_green, .stock_msg, .view_product_comp_compi.desc, .view_product_comp_compi')).forEach(function(n){ n.remove(); });
		// Move Add to Quote to end with cart
		var atq = root.querySelector('.yith-ywraq-add-to-quote, [class*="add-to-quote"]');
		var cart = root.querySelector('form.cart');
		if(atq && cart){ cart.appendChild(atq); }
		// Move quantity to end of cart
		if(cart){ var qty = cart.querySelector('.quantity'); if(qty){ cart.appendChild(qty); } }
	}
	ready(function(){ apply(); var i=0; var t=setInterval(function(){ i++; apply(); if(i>10) clearInterval(t); }, 300); });
})();
JS;
    wp_add_inline_script('tpb-qv-inline-js', $uiFixes, 'after');
    
    // Product type detection and stepper selection
    $productTypeDetection = <<<'JS'
(function(){
    var productUrl = new URL(window.location.href);
    var productType = 'flower'; // default
    
    // Detect product type from URL patterns
    if(productUrl.pathname.includes('quick-checkout-station-configure-now') || 
       productUrl.pathname.includes('quick-checkout-configure-now') || 
       productUrl.pathname.includes('quick-checkout') || 
       productUrl.searchParams.get('product_type') === 'quickcheckout') {
        productType = 'quickcheckout';
    } else if(productUrl.pathname.includes('category-station-configure-now') || 
              productUrl.pathname.includes('category-station') || 
              productUrl.searchParams.get('product_type') === 'category') {
        productType = 'category';
    }
    
    // Load appropriate stepper
    if(productType === 'quickcheckout') {
        // Quick Checkout stepper will initialize
        console.log('Loading Quick Checkout stepper');
    } else if(productType === 'category') {
        // Category stepper will initialize
        console.log('Loading Category Stations stepper');
    } else {
        // Flower stepper will initialize
        console.log('Loading Flower Stations stepper');
    }
})();
JS;
    wp_add_inline_script('tpb-qv-inline-js', $productTypeDetection, 'after');
    
    // Load appropriate stepper based on URL detection
    $current_url = $_SERVER['REQUEST_URI'];
    $is_category_station = strpos($current_url, 'category-station-configure-now') !== false || 
                          strpos($current_url, 'category-station') !== false;
    $is_quick_checkout = strpos($current_url, 'quick-checkout-station-configure-now') !== false || 
                        strpos($current_url, 'quick-checkout-configure-now') !== false ||
                        strpos($current_url, 'quick-checkout') !== false;
    
    if ($is_quick_checkout) {
        // Quick Checkout stepper - for Quick Checkout Stations modal
        $quickcheckout_src = plugin_dir_url(__FILE__) . 'assets/js/tpb-qv-native-quickcheckout.js';
        wp_enqueue_script('tpb-qv-native-quickcheckout-file', $quickcheckout_src, [], filemtime(plugin_dir_path(__FILE__) . 'assets/js/tpb-qv-native-quickcheckout.js') . time() . rand(1000, 9999) . microtime(true) . uniqid() . md5(uniqid()) . bin2hex(random_bytes(8)) . '_' . uniqid() . '_' . time() . '_' . uniqid() . '_' . microtime(true), true);
    } elseif ($is_category_station) {
        // Category stepper - for Category Stations modal
        $category_src = plugin_dir_url(__FILE__) . 'assets/js/tpb-qv-native-category.js';
        wp_enqueue_script('tpb-qv-native-category-file', $category_src, [], filemtime(plugin_dir_path(__FILE__) . 'assets/js/tpb-qv-native-category.js') . time() . rand(1000, 9999) . microtime(true) . uniqid() . md5(uniqid()) . bin2hex(random_bytes(8)) . '_' . uniqid() . '_' . time() . '_' . uniqid() . '_' . microtime(true), true);
    } else {
        // Flower stepper - default for Flower Stations modal
        $native_src = plugin_dir_url(__FILE__) . 'assets/js/tpb-qv-native.js';
        wp_enqueue_script('tpb-qv-native-file', $native_src, [], filemtime(plugin_dir_path(__FILE__) . 'assets/js/tpb-qv-native.js') . time() . rand(1000, 9999) . microtime(true) . uniqid() . md5(uniqid()) . bin2hex(random_bytes(8)), true);
    }
}, 20);


// Native Configurator AJAX fallback (admin-ajax) – works even if REST is blocked
add_action('wp_ajax_nopriv_tpb_qv_products', 'tpb_qv_products_ajax');
add_action('wp_ajax_tpb_qv_products', 'tpb_qv_products_ajax');

// Register custom REST endpoint
add_action('rest_api_init', function() {
    register_rest_route('tpb/v1', '/qv/products', array(
        'methods' => 'POST',
        'callback' => 'tpb_qv_products_ajax',
        'permission_callback' => '__return_true'
    ));
});
function tpb_qv_products_ajax($request = null){
	// Handle both REST API and AJAX requests
	if ($request && is_a($request, 'WP_REST_Request')) {
		// REST API request
		$params = $request->get_json_params();
		$tags = isset($params['tags']) ? array_filter(array_map('sanitize_title', $params['tags'])) : [];
		$cats = isset($params['categories']) ? array_filter(array_map('sanitize_title', $params['categories'])) : [];
		$exclude_cats = isset($params['exclude_categories']) ? array_filter(array_map('sanitize_title', $params['exclude_categories'])) : [];
		$limit = isset($params['limit']) ? max(1, min(48, intval($params['limit']))) : 24;
	} else {
		// AJAX request
		$tags = isset($_REQUEST['tags']) ? array_filter(array_map('sanitize_title', explode(',', (string)$_REQUEST['tags']))) : [];
		$cats = isset($_REQUEST['categories']) ? array_filter(array_map('sanitize_title', explode(',', (string)$_REQUEST['categories']))) : [];
		$exclude_cats = isset($_REQUEST['exclude_categories']) ? array_filter(array_map('sanitize_title', explode(',', (string)$_REQUEST['exclude_categories']))) : [];
		$limit = isset($_REQUEST['limit']) ? max(1, min(48, intval($_REQUEST['limit']))) : 24;
	}

	$tax = [ 'relation' => 'AND' ];
	if (!empty($tags)){
		$tax[] = [ 'taxonomy'=>'product_tag', 'field'=>'slug', 'terms'=>$tags, 'operator'=>'AND' ];
	}
	if (!empty($cats)){
		$tax[] = [ 'taxonomy'=>'product_cat', 'field'=>'slug', 'terms'=>$cats, 'operator'=>'AND' ];
	}
	if (!empty($exclude_cats)){
		$tax[] = [ 'taxonomy'=>'product_cat', 'field'=>'slug', 'terms'=>$exclude_cats, 'operator'=>'NOT IN' ];
	}

	$args = [
		'post_type' => 'product',
		'post_status' => 'publish',
		'posts_per_page' => $limit,
		'tax_query' => $tax,
		'meta_key' => '_price',
		'orderby' => 'meta_value_num',
		'order' => 'ASC',
	];
	$q = new WP_Query($args);
	$items = [];
	if ($q->have_posts()){
		foreach($q->posts as $p){
			$product = wc_get_product($p);
			if(!$product) continue;
			$img_id = $product->get_image_id();
			$img = $img_id ? wp_get_attachment_image_url($img_id,'large') : wc_placeholder_img_src('large');
			// Get product categories and tags
			$categories = [];
			$tags = [];
			
			$product_cats = wp_get_post_terms($product->get_id(), 'product_cat');
			if (!is_wp_error($product_cats)) {
				foreach ($product_cats as $cat) {
					$categories[] = [
						'id' => $cat->term_id,
						'name' => $cat->name,
						'slug' => $cat->slug
					];
				}
			}
			
			$product_tags = wp_get_post_terms($product->get_id(), 'product_tag');
			if (!is_wp_error($product_tags)) {
				foreach ($product_tags as $tag) {
					$tags[] = [
						'id' => $tag->term_id,
						'name' => $tag->name,
						'slug' => $tag->slug
					];
				}
			}
			
			$items[] = [
				'id' => $product->get_id(),
				'name' => html_entity_decode($product->get_name()),
				'slug' => $product->get_slug(),
				'price_html' => $product->get_price_html(),
				'image_url' => $img,
				'permalink' => get_permalink($product->get_id()),
				'is_purchasable' => $product->is_purchasable(),
				'supports_qty' => !$product->is_sold_individually(),
				'categories' => $categories,
				'tags' => $tags,
			];
		}
	}
	wp_reset_postdata();

	wp_send_json([ 'results' => $items ]);
}

// Disable CPB plugin entirely in quickview mode
add_action('plugins_loaded', function () {
    if (isset($_GET['tpb_qv']) || isset($_GET['tpb_qv_iframe'])) {
        // Disable the CPB plugin
        remove_action('wp_enqueue_scripts', 'addify_composite_products_scripts');
        remove_action('wp_enqueue_scripts', 'addify_composite_products_styles');
        remove_action('wp_head', 'addify_composite_products_head');
        remove_action('wp_footer', 'addify_composite_products_footer');
        remove_action('woocommerce_single_product_summary', 'addify_composite_products_display', 25);
        remove_action('woocommerce_before_single_product_summary', 'addify_composite_products_display', 25);
        remove_action('woocommerce_after_single_product_summary', 'addify_composite_products_display', 25);
        
        // Also disable any other CPB hooks
        global $wp_filter;
        if (isset($wp_filter['wp_enqueue_scripts'])) {
            foreach ($wp_filter['wp_enqueue_scripts']->callbacks as $priority => $callbacks) {
                foreach ($callbacks as $callback_id => $callback) {
                    if (is_array($callback['function']) && is_object($callback['function'][0])) {
                        $class_name = get_class($callback['function'][0]);
                        if (strpos($class_name, 'Addify') !== false || strpos($class_name, 'Composite') !== false) {
                            remove_action('wp_enqueue_scripts', $callback['function'], $priority);
                        }
                    }
                }
            }
        }
    }
}, 1);

// In quickview iframe, hard-block Addify/CPB assets so the modal is not influenced
add_action('wp_enqueue_scripts', function () {
    if (!isset($_GET['tpb_qv']) && !isset($_GET['tpb_qv_iframe'])) return;
    
    // Debug: Log that we're blocking CPB assets
    error_log('TPB: Blocking CPB assets in quickview mode');
    
    $needles = [ 'addify', 'afcpb', 'composite', 'configurable-product', 'cpb' ];
    $blocked_scripts = [];
    $blocked_styles = [];
    
    // Scripts
    global $wp_scripts; if ($wp_scripts && is_object($wp_scripts)) {
        foreach ((array) $wp_scripts->queue as $h) {
            if (!isset($wp_scripts->registered[$h])) continue;
            $src = (string) $wp_scripts->registered[$h]->src;
            foreach ($needles as $n) { 
                if ($src && stripos($src, $n) !== false) { 
                    wp_dequeue_script($h); 
                    wp_deregister_script($h); 
                    $blocked_scripts[] = $h . ' (' . $src . ')';
                    break; 
                } 
            }
        }
    }
    // Styles
    global $wp_styles; if ($wp_styles && is_object($wp_styles)) {
        foreach ((array) $wp_styles->queue as $h) {
            if (!isset($wp_styles->registered[$h])) continue;
            $src = (string) $wp_styles->registered[$h]->src;
            foreach ($needles as $n) { 
                if ($src && stripos($src, $n) !== false) { 
                    wp_dequeue_style($h); 
                    wp_deregister_style($h); 
                    $blocked_styles[] = $h . ' (' . $src . ')';
                    break; 
                } 
            }
        }
    }
    
    // Debug: Log what was blocked
    if (!empty($blocked_scripts)) {
        error_log('TPB: Blocked scripts: ' . implode(', ', $blocked_scripts));
    }
    if (!empty($blocked_styles)) {
        error_log('TPB: Blocked styles: ' . implode(', ', $blocked_styles));
    }
}, 1000);


// Hard-load native stepper early in <head> for iframe pages (theme-agnostic)
add_action('wp_head', function(){
    if (!isset($_GET['tpb_qv']) && !isset($_GET['tpb_qv_iframe'])) return;
    
    $native_src = plugin_dir_url(__FILE__) . 'assets/js/tpb-qv-native.js';
    $category_src = plugin_dir_url(__FILE__) . 'assets/js/tpb-qv-native-category.js';
    
    $script = <<<'JS'
(function(){
    try {
        if(!document.getElementById("tpb-qv-native")) {
            var productUrl = new URL(window.location.href);
            var productType = 'flower'; // default
            
            // Detect product type from URL patterns
            if(productUrl.pathname.includes('category-station-configure-now') || 
               productUrl.pathname.includes('category-station') || 
               productUrl.searchParams.get('product_type') === 'category') {
                productType = 'category';
            }
            
            var s = document.createElement("script");
            s.src = productType === 'category' ? 'CATEGORY_SRC' : 'NATIVE_SRC';
            s.defer = true;
            document.head.appendChild(s);
        }
    } catch(e) {}
})();
JS;
    
    $script = str_replace('NATIVE_SRC', json_encode($native_src . '?v=' . time() . rand(1000, 9999) . '&cb=' . uniqid() . '&t=' . microtime(true)), $script);
    $script = str_replace('CATEGORY_SRC', json_encode($category_src . '?v=' . time() . rand(1000, 9999) . '&cb=' . uniqid() . '&t=' . microtime(true)), $script);
    
    echo '<script>' . $script . '</script>';
}, 5);


