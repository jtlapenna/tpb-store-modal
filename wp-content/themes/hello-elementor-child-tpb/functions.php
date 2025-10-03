<?php
// Purpose: Clean production hooks for TPB QuickView modal
if (!defined('ABSPATH')) { exit; }

// Helper: detect iframe mode via query param
function tpb_qv_is_iframe() {
    // Treat any of these params as iframe mode
    $p = $_GET;
    if ((isset($p['tpb_qv_iframe']) && $p['tpb_qv_iframe'] === '1')
        || (isset($p['tpb_qv']) && $p['tpb_qv'] === '1')
        || (isset($p['tpb_qv_staging']) && $p['tpb_qv_staging'] === '1')) {
        return true;
    }
    return false;
}

// Note: Modal assets are enqueued by the custom plugin (tpb-quickview-modal).
// The child theme intentionally does not enqueue modal CSS/JS to avoid conflicts.

// Add body class for iframe mode
function tpb_qv_body_class($classes) {
    if (tpb_qv_is_iframe()) { $classes[] = 'tpb-qv-iframe'; }
    return $classes;
}
add_filter('body_class', 'tpb_qv_body_class');

// Minimal chrome adjustments in iframe mode
function tpb_qv_iframe_chrome() {
    if (!tpb_qv_is_iframe()) { return; }
    echo '<style>
        header,footer,.site-header,.site-footer{display:none!important;}
        body{padding:0!important;margin:0!important;background:#fff!important;}
    </style>';
}
add_action('wp_head', 'tpb_qv_iframe_chrome', 99);

// Add lightweight iframe behavior: no pre-select + progressive disclosure (theme-side)
function tpb_qv_iframe_progressive() {
    if (!tpb_qv_is_iframe()) { return; }
    echo '<script>(function(){"use strict";
      function q(sel,root){return (root||document).querySelector(sel);} 
      function qa(sel,root){return Array.prototype.slice.call((root||document).querySelectorAll(sel));}
      function hide(el){ if(!el) return; el.classList.add("tpb-hidden"); el.style.display="none"; }
      function show(el){ if(!el) return; el.classList.remove("tpb-hidden"); el.style.display=""; }
      function firstSelectIn(el){ return el && el.querySelector("select"); }

      function ensurePlaceholder(select, text){
        if(!select) return;
        Array.from(select.options).forEach(function(o){ o.selected=false; o.removeAttribute("selected"); });
        var ph = select.querySelector("option[value=\"\"]");
        if(!ph){ ph = document.createElement("option"); ph.value=""; ph.textContent=text||"Select an option…"; ph.disabled=true; ph.selected=true; select.insertBefore(ph, select.firstChild); }
        select.selectedIndex = 0; select.value = ""; ["input","change"].forEach(function(e){ select.dispatchEvent(new Event(e,{bubbles:true})); });
      }

      function init(){
        var components = qa(".af_cp_all_components_content .single_component");
        if(!components.length){ components = qa(".variations, .woocommerce-variation, form.cart .variations"); }
        if(!components.length){ return; }

        // step 1: only first visible, rest hidden
        components.forEach(function(c,i){ i===0 ? show(c) : hide(c); });

        // clear and set placeholder on first select
        var first = components[0]; var sel = firstSelectIn(first); ensurePlaceholder(sel, "Select SKU count…");

        // on change in any component, reveal next if a non-empty value is chosen
        document.addEventListener("change", function(ev){
          var el = ev.target; var comp = el.closest(".single_component, .variations, .woocommerce-variation");
          if(!comp) return;
          var idx = components.indexOf(comp); if(idx===-1) return;
          var val = (el.value||"").trim(); if(val==="") return;
          var next = components[idx+1]; if(next){ show(next); }
        }, true);
      }

      if(document.readyState==="loading"){ document.addEventListener("DOMContentLoaded", init); } else { init(); }
    })();</script>';
}
add_action('wp_head', 'tpb_qv_iframe_progressive', 100);
?>