<?php
/**
 * Plugin Name: TPB QuickView Modal
 * Plugin URI: https://cannabis-kiosks.com
 * Description: Custom modal for product configuration with iframe support
 * Version: 1.0.0
 * Author: TPB Team
 * License: GPL v2 or later
 * Text Domain: tpb-quickview-modal
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TPB_QV_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TPB_QV_PLUGIN_PATH', plugin_dir_path(__FILE__));
define('TPB_QV_VERSION', '1.0.0');

/**
 * Main plugin class
 */
class TPB_QuickView_Modal {
    
    /**
     * Plugin instance
     */
    private static $instance = null;
    
    /**
     * Get plugin instance
     */
    public static function get_instance() {
        if (null === self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }
    
    /**
     * Constructor
     */
    private function __construct() {
        add_action('init', array($this, 'init'));
        add_action('wp_enqueue_scripts', array($this, 'enqueue_scripts'));
        add_action('wp_footer', array($this, 'add_modal_html'));
        add_action('admin_menu', array($this, 'add_admin_menu'));
        add_action('template_redirect', array($this, 'handle_iframe_content'));
    }
    
    /**
     * Initialize plugin
     */
    public function init() {
        // Plugin initialization code
        load_plugin_textdomain('tpb-quickview-modal', false, dirname(plugin_basename(__FILE__)) . '/languages');
    }
    
    /**
     * Enqueue scripts and styles
     */
    public function enqueue_scripts() {
        // Only load on frontend
        if (is_admin()) {
            return;
        }
        
        // Enqueue CSS
        wp_enqueue_style(
            'tpb-qv-modal-css',
            TPB_QV_PLUGIN_URL . 'assets/css/modal.css',
            array(),
            TPB_QV_VERSION
        );
        
        // Enqueue blocking script first (runs before other scripts)
        wp_enqueue_script(
            'tpb-qv-blocker-js',
            TPB_QV_PLUGIN_URL . 'assets/js/modal-blocker.js',
            array(),
            time(),
            false // Load in header, not footer
        );
        
        // Enqueue JavaScript
        wp_enqueue_script(
            'tpb-qv-modal-js',
            TPB_QV_PLUGIN_URL . 'assets/js/modal.js',
            array('jquery'),
            time(), // Use timestamp for cache busting
            true
        );
        
        // Localize script for AJAX
        wp_localize_script('tpb-qv-modal-js', 'tpb_qv_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce' => wp_create_nonce('tpb_qv_nonce'),
            'home_url' => home_url(),
        ));
    }
    
    /**
     * Add modal HTML to footer
     */
    public function add_modal_html() {
        // Only add on frontend
        if (is_admin()) {
            return;
        }
        ?>
        <div id="tpb-qv-overlay" class="tpb-qv-overlay" style="display: none;">
            <div class="tpb-qv-modal">
                <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
                <iframe id="tpb-qv-iframe" class="tpb-qv-iframe" src="about:blank"></iframe>
            </div>
        </div>
        <?php
    }
    
    /**
     * Handle iframe content layout
     */
    public function handle_iframe_content() {
        // Check if this is an iframe request
        if (isset($_GET['tpb_qv_iframe']) && $_GET['tpb_qv_iframe'] === '1') {
            // Add iframe-specific styles and scripts
            add_action('wp_head', array($this, 'add_iframe_styles'), 99);
            add_action('wp_footer', array($this, 'add_iframe_scripts'), 99);
            add_filter('body_class', array($this, 'add_iframe_body_class'));
            
            // Debug: Add a visible indicator
            add_action('wp_head', array($this, 'add_iframe_debug'), 100);
        }
    }
    
    /**
     * Add iframe debug indicator
     */
    public function add_iframe_debug() {
        ?>
        <div style="position: fixed; top: 0; left: 0; background: red; color: white; padding: 10px; z-index: 999999;">
            IFRAME MODE ACTIVE - TPB QuickView
        </div>
        <?php
    }
    
    /**
     * Add iframe-specific styles
     */
    public function add_iframe_styles() {
        ?>
        <style>
            /* Hide header and footer in iframe */
            header, footer, .site-header, .site-footer {
                display: none !important;
            }
            
            /* Set body styles for iframe */
            body {
                padding: 0 !important;
                margin: 0 !important;
                background-color: #fff !important;
            }
            
            /* Two-panel layout for iframe mode */
            body.tpb-qv-iframe .product {
                display: grid;
                grid-template-columns: 1fr 1fr;
                gap: 20px;
                height: 100vh;
                background-color: #fff;
                padding: 20px;
                box-sizing: border-box;
            }
            
            /* Gallery takes left column */
            body.tpb-qv-iframe .woocommerce-product-gallery {
                grid-column: 1;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            
            /* Summary takes right column */
            body.tpb-qv-iframe .summary.entry-summary {
                grid-column: 2;
                display: flex;
                flex-direction: column;
                justify-content: center;
            }
            
            /* Ensure images are responsive */
            body.tpb-qv-iframe .woocommerce-product-gallery img {
                max-width: 100%;
                height: auto;
            }
        </style>
        <?php
    }
    
    /**
     * Add iframe-specific scripts
     */
    public function add_iframe_scripts() {
        ?>
        <script>
            console.log('🔧 TPB QuickView iframe layout script loading...');
            
            // Add iframe body class
            document.body.classList.add('tpb-qv-iframe');
            
            // Ensure proper layout
            document.addEventListener('DOMContentLoaded', function() {
                const product = document.querySelector('.product');
                if (product) {
                    console.log('✅ Product container found, applying iframe layout');
                    
                    // Ensure gallery and summary are direct children
                    const gallery = product.querySelector('.woocommerce-product-gallery');
                    const summary = product.querySelector('.summary.entry-summary');
                    
                    if (gallery && summary) {
                        console.log('✅ Gallery and summary found, layout should be applied');
                    } else {
                        console.log('⚠️ Gallery or summary not found');
                    }
                } else {
                    console.log('❌ Product container not found');
                }
                
                // Progressive disclosure: no pre-select + step-by-step reveal
                console.log('🎯 Setting up progressive disclosure...');
                
                // Debug: Check if CPB plugin is active
                console.log('🔍 CPB Plugin Debug:', {
                    addifyScript: !!document.querySelector('script[src*="af-comp-product"]'),
                    addifyContainer: !!document.querySelector('.af_cp_all_components_content'),
                    woocommerce: !!document.querySelector('.woocommerce'),
                    product: !!document.querySelector('.product'),
                    bodyClasses: document.body.className,
                    allScripts: Array.from(document.querySelectorAll('script[src]')).map(s => s.src).filter(s => s.includes('af') || s.includes('cpb') || s.includes('addify'))
                });
                
                function q(sel, root) { return (root || document).querySelector(sel); }
                function qa(sel, root) { return Array.from((root || document).querySelectorAll(sel)); }
                function hide(el) { if (!el) return; el.classList.add('tpb-hidden'); el.style.display = 'none'; }
                function show(el) { if (!el) return; el.classList.remove('tpb-hidden'); el.style.display = ''; }
                function firstSelectIn(el) { return el && el.querySelector('select'); }

                function ensurePlaceholder(select, text) {
                    if (!select) return;
                    // Clear all selections
                    Array.from(select.options).forEach(function(o) { 
                        o.selected = false; 
                        o.removeAttribute('selected'); 
                    });
                    // Remove existing placeholder
                    const existing = select.querySelector('option[value=""]');
                    if (existing) existing.remove();
                    // Add new placeholder
                    const ph = document.createElement('option');
                    ph.value = '';
                    ph.textContent = text || 'Select an option…';
                    ph.disabled = true;
                    ph.selected = true;
                    select.insertBefore(ph, select.firstChild);
                    select.selectedIndex = 0;
                    select.value = '';
                    ['input', 'change'].forEach(function(e) { 
                        select.dispatchEvent(new Event(e, {bubbles: true})); 
                    });
                }

                function initProgressive() {
                    // Find CPB components - try multiple selectors
                    let components = qa('.af_cp_all_components_content .single_component');
                    console.log('🔍 Addify CPB components found:', components.length);
                    
                    if (!components.length) { 
                        components = qa('.variations, .woocommerce-variation, form.cart .variations'); 
                        console.log('🔍 WooCommerce variations found:', components.length);
                    }
                    
                    if (!components.length) { 
                        components = qa('form.cart, .woocommerce-variation, select[name*="attribute"]'); 
                        console.log('🔍 Form elements found:', components.length);
                    }
                    
                    if (!components.length) { 
                        components = qa('select:not([name="quantity"])'); 
                        console.log('🔍 Select elements found:', components.length);
                    }
                    
                    if (!components.length) { 
                        console.log('⚠️ No components found for progressive disclosure');
                        console.log('🔍 Available elements:', {
                            addify: qa('.af_cp_all_components_content').length,
                            variations: qa('.variations').length,
                            forms: qa('form.cart').length,
                            selects: qa('select').length,
                            allDivs: qa('div').length
                        });
                        return; 
                    }

                    console.log('✅ Found', components.length, 'components for progressive disclosure');

                    // Step 1: Only first visible, rest hidden
                    components.forEach(function(c, i) { 
                        if (i === 0) {
                            show(c);
                            console.log('✅ Showing first component');
                        } else {
                            hide(c);
                            console.log('📦 Hiding component', i + 1);
                        }
                    });

                    // Clear and set placeholder on first select
                    const first = components[0];
                    const sel = firstSelectIn(first);
                    if (sel) {
                        ensurePlaceholder(sel, 'Select SKU count…');
                        console.log('✅ Set placeholder on first select');
                    }

                    // On change in any component, reveal next if a non-empty value is chosen
                    document.addEventListener('change', function(ev) {
                        const el = ev.target;
                        const comp = el.closest('.single_component, .variations, .woocommerce-variation');
                        if (!comp) return;
                        const idx = components.indexOf(comp);
                        if (idx === -1) return;
                        const val = (el.value || '').trim();
                        if (val === '') return;
                        
                        const next = components[idx + 1];
                        if (next) { 
                            show(next);
                            console.log('✅ Revealed next component after selection');
                        }
                    }, true);
                }

                // Initialize progressive disclosure with retries for dynamic content
                initProgressive();
                
                // Retry after delays to catch dynamically loaded content
                setTimeout(function() {
                    console.log('🔄 Retrying component detection after 1s...');
                    initProgressive();
                }, 1000);
                
                setTimeout(function() {
                    console.log('🔄 Retrying component detection after 3s...');
                    initProgressive();
                }, 3000);
                
                setTimeout(function() {
                    console.log('🔄 Final retry after 5s...');
                    initProgressive();
                }, 5000);
            });
        </script>
        <?php
    }
    
    /**
     * Add iframe body class
     */
    public function add_iframe_body_class($classes) {
        if (isset($_GET['tpb_qv_iframe']) && $_GET['tpb_qv_iframe'] === '1') {
            $classes[] = 'tpb-qv-iframe';
        }
        return $classes;
    }
    
    /**
     * Add admin menu
     */
    public function add_admin_menu() {
        add_options_page(
            'TPB QuickView Modal Settings',
            'TPB QuickView Modal',
            'manage_options',
            'tpb-qv-settings',
            array($this, 'admin_page')
        );
    }
    
    /**
     * Admin page
     */
    public function admin_page() {
        ?>
        <div class="wrap">
            <h1>TPB QuickView Modal Settings</h1>
            <p>Plugin is active and working. Modal will automatically detect "Configure Now" buttons.</p>
            
            <h2>Shortcode Usage</h2>
            <p>Use this shortcode to manually add a modal button:</p>
            <code>[tpb_modal_button url="https://example.com/product/configure" text="Configure Now"]</code>
            
            <h2>Debug Information</h2>
            <p>Plugin Version: <?php echo TPB_QV_VERSION; ?></p>
            <p>Plugin Path: <?php echo TPB_QV_PLUGIN_PATH; ?></p>
            <p>Plugin URL: <?php echo TPB_QV_PLUGIN_URL; ?></p>
        </div>
        <?php
    }
}

// Initialize the plugin
TPB_QuickView_Modal::get_instance();

/**
 * Shortcode for manual modal button
 */
function tpb_modal_button_shortcode($atts) {
    $atts = shortcode_atts(array(
        'url' => '',
        'text' => 'Configure Now',
        'class' => 'tpb-modal-trigger'
    ), $atts);
    
    if (empty($atts['url'])) {
        return '<p>Error: URL is required for modal button.</p>';
    }
    
    return sprintf(
        '<a href="%s" class="%s" data-tpb-modal="true">%s</a>',
        esc_url($atts['url']),
        esc_attr($atts['class']),
        esc_html($atts['text'])
    );
}
add_shortcode('tpb_modal_button', 'tpb_modal_button_shortcode');
