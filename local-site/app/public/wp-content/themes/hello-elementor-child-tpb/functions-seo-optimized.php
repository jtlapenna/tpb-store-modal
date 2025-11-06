<?php
/**
 * TPB Quick View Modal Functions - SEO Optimized
 * Child theme functions for the TPB Store Modal with comprehensive SEO enhancements
 */

// Prevent direct access
if (!defined('ABSPATH')) {
    exit;
}

/**
 * SEO Meta Tags and Head Optimization
 */
function tpb_seo_head_optimization() {
    // Only add to frontend
    if (is_admin()) return;
    
    // Get current page data
    $title = tpb_get_seo_title();
    $description = tpb_get_seo_description();
    $keywords = tpb_get_seo_keywords();
    $canonical = tpb_get_canonical_url();
    $og_image = tpb_get_og_image();
    
    echo "\n<!-- TPB SEO Optimization -->\n";
    
    // Basic meta tags
    echo '<meta name="description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta name="keywords" content="' . esc_attr($keywords) . '">' . "\n";
    echo '<meta name="author" content="Cannabis Kiosks">' . "\n";
    echo '<meta name="robots" content="index, follow, max-snippet:-1, max-image-preview:large, max-video-preview:-1">' . "\n";
    
    // Canonical URL
    if ($canonical) {
        echo '<link rel="canonical" href="' . esc_url($canonical) . '">' . "\n";
    }
    
    // Open Graph tags
    echo '<meta property="og:type" content="website">' . "\n";
    echo '<meta property="og:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta property="og:description" content="' . esc_attr($description) . '">' . "\n";
    echo '<meta property="og:url" content="' . esc_url($canonical) . '">' . "\n";
    echo '<meta property="og:site_name" content="Cannabis Kiosks">' . "\n";
    if ($og_image) {
        echo '<meta property="og:image" content="' . esc_url($og_image) . '">' . "\n";
        echo '<meta property="og:image:width" content="1200">' . "\n";
        echo '<meta property="og:image:height" content="630">' . "\n";
    }
    
    // Twitter Card tags
    echo '<meta name="twitter:card" content="summary_large_image">' . "\n";
    echo '<meta name="twitter:title" content="' . esc_attr($title) . '">' . "\n";
    echo '<meta name="twitter:description" content="' . esc_attr($description) . '">' . "\n";
    if ($og_image) {
        echo '<meta name="twitter:image" content="' . esc_url($og_image) . '">' . "\n";
    }
    
    // Additional SEO meta tags
    echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">' . "\n";
    echo '<meta name="theme-color" content="#5ac59a">' . "\n";
    echo '<meta name="msapplication-TileColor" content="#5ac59a">' . "\n";
    
    // Preconnect to external domains for performance
    echo '<link rel="preconnect" href="https://fonts.googleapis.com">' . "\n";
    echo '<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>' . "\n";
    echo '<link rel="preconnect" href="https://www.google-analytics.com">' . "\n";
    
    echo "<!-- End TPB SEO Optimization -->\n\n";
}
add_action('wp_head', 'tpb_seo_head_optimization', 1);

/**
 * Get SEO-optimized title
 */
function tpb_get_seo_title() {
    if (is_home() || is_front_page()) {
        return 'Cannabis Kiosks - Advanced Retail Technology Solutions | POS Systems & Compliance Software';
    } elseif (is_page('about')) {
        return 'About Cannabis Kiosks - Cannabis Retail Technology Experts';
    } elseif (is_page('products') || is_shop()) {
        return 'Cannabis POS Systems & Retail Technology Products';
    } elseif (is_page('contact')) {
        return 'Contact Cannabis Kiosks - Get Expert Cannabis Technology Support';
    } elseif (is_single() && get_post_type() === 'post') {
        return get_the_title() . ' | Cannabis Kiosks Blog';
    } elseif (is_single() && get_post_type() === 'product') {
        return get_the_title() . ' | Cannabis Kiosks Products';
    } else {
        return get_the_title() . ' | Cannabis Kiosks';
    }
}

/**
 * Get SEO-optimized description
 */
function tpb_get_seo_description() {
    if (is_home() || is_front_page()) {
        return 'Leading cannabis retail technology solutions including POS systems, compliance software, and dispensary management tools. Streamline your cannabis business operations with our advanced technology.';
    } elseif (is_page('about')) {
        return 'Learn about Cannabis Kiosks\' expertise in cannabis retail technology, POS systems, and compliance solutions for dispensaries and cannabis businesses.';
    } elseif (is_page('products') || is_shop()) {
        return 'Discover our comprehensive cannabis POS systems, compliance software, and retail technology solutions designed specifically for cannabis dispensaries and retailers.';
    } elseif (is_page('contact')) {
        return 'Contact Cannabis Kiosks for expert support with cannabis retail technology, POS systems, and compliance solutions. Get personalized assistance for your dispensary.';
    } elseif (is_single() && get_post_type() === 'post') {
        $excerpt = get_the_excerpt();
        return $excerpt ? wp_trim_words($excerpt, 25) : 'Read our latest insights on cannabis retail technology, POS systems, and dispensary management solutions.';
    } elseif (is_single() && get_post_type() === 'product') {
        $excerpt = get_the_excerpt();
        return $excerpt ? wp_trim_words($excerpt, 25) : 'Explore this cannabis retail technology product from Cannabis Kiosks - your trusted partner for dispensary solutions.';
    } else {
        return 'Cannabis Kiosks provides advanced retail technology solutions for cannabis dispensaries. POS systems, compliance software, and more.';
    }
}

/**
 * Get SEO keywords
 */
function tpb_get_seo_keywords() {
    $base_keywords = 'cannabis kiosks, cannabis retail technology, cannabis POS systems, cannabis dispensary software, cannabis compliance software, dispensary management, cannabis point of sale, cannabis retail solutions';
    
    if (is_home() || is_front_page()) {
        return $base_keywords . ', cannabis technology, retail automation, dispensary POS';
    } elseif (is_page('about')) {
        return $base_keywords . ', cannabis technology experts, dispensary consultants, retail technology specialists';
    } elseif (is_page('products') || is_shop()) {
        return $base_keywords . ', cannabis products, retail technology products, POS hardware, compliance tools';
    } elseif (is_single() && get_post_type() === 'product') {
        $product_keywords = get_post_meta(get_the_ID(), '_seo_keywords', true);
        return $product_keywords ? $product_keywords . ', ' . $base_keywords : $base_keywords;
    } else {
        return $base_keywords;
    }
}

/**
 * Get canonical URL
 */
function tpb_get_canonical_url() {
    if (is_home() || is_front_page()) {
        return home_url('/');
    } elseif (is_single() || is_page()) {
        return get_permalink();
    } elseif (is_shop()) {
        return get_permalink(wc_get_page_id('shop'));
    } else {
        return home_url($_SERVER['REQUEST_URI']);
    }
}

/**
 * Get Open Graph image
 */
function tpb_get_og_image() {
    if (is_single() && has_post_thumbnail()) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
        return $image ? $image[0] : false;
    } elseif (is_page() && has_post_thumbnail()) {
        $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
        return $image ? $image[0] : false;
    } else {
        // Default OG image
        return get_template_directory_uri() . '/assets/images/og-default.jpg';
    }
}

/**
 * Add structured data (Schema.org)
 */
function tpb_add_structured_data() {
    if (is_admin()) return;
    
    $schema = [];
    
    // Organization schema for all pages
    $schema['@context'] = 'https://schema.org';
    $schema['@type'] = 'Organization';
    $schema['name'] = 'Cannabis Kiosks';
    $schema['url'] = home_url('/');
    $schema['logo'] = home_url('/wp-content/uploads/logo.png');
    $schema['description'] = 'Leading cannabis retail technology solutions including POS systems, compliance software, and dispensary management tools.';
    
    // Contact information
    $schema['contactPoint'] = [
        '@type' => 'ContactPoint',
        'telephone' => '+1-XXX-XXX-XXXX', // Update with real phone
        'contactType' => 'customer service',
        'areaServed' => 'US',
        'availableLanguage' => 'English'
    ];
    
    // Address (update with real address)
    $schema['address'] = [
        '@type' => 'PostalAddress',
        'addressCountry' => 'US',
        'addressLocality' => 'Your City',
        'addressRegion' => 'Your State',
        'postalCode' => '12345',
        'streetAddress' => 'Your Street Address'
    ];
    
    // Services
    $schema['makesOffer'] = [
        [
            '@type' => 'Offer',
            'itemOffered' => [
                '@type' => 'Service',
                'name' => 'Cannabis POS Systems',
                'description' => 'Point of sale systems designed specifically for cannabis dispensaries'
            ]
        ],
        [
            '@type' => 'Offer',
            'itemOffered' => [
                '@type' => 'Service',
                'name' => 'Cannabis Compliance Software',
                'description' => 'Software solutions to help cannabis businesses maintain compliance'
            ]
        ]
    ];
    
    // Add product schema for product pages
    if (is_single() && get_post_type() === 'product') {
        $product = wc_get_product(get_the_ID());
        if ($product) {
            $schema['@type'] = 'Product';
            $schema['name'] = get_the_title();
            $schema['description'] = get_the_excerpt();
            $schema['sku'] = $product->get_sku();
            $schema['price'] = $product->get_price();
            $schema['priceCurrency'] = 'USD';
            $schema['availability'] = $product->is_in_stock() ? 'https://schema.org/InStock' : 'https://schema.org/OutOfStock';
            
            if (has_post_thumbnail()) {
                $image = wp_get_attachment_image_src(get_post_thumbnail_id(), 'large');
                $schema['image'] = $image[0];
            }
        }
    }
    
    // Add article schema for blog posts
    if (is_single() && get_post_type() === 'post') {
        $schema['@type'] = 'Article';
        $schema['headline'] = get_the_title();
        $schema['description'] = get_the_excerpt();
        $schema['datePublished'] = get_the_date('c');
        $schema['dateModified'] = get_the_modified_date('c');
        $schema['author'] = [
            '@type' => 'Person',
            'name' => get_the_author()
        ];
        $schema['publisher'] = [
            '@type' => 'Organization',
            'name' => 'Cannabis Kiosks',
            'logo' => [
                '@type' => 'ImageObject',
                'url' => home_url('/wp-content/uploads/logo.png')
            ]
        ];
    }
    
    echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
}
add_action('wp_head', 'tpb_add_structured_data', 5);

/**
 * Performance optimizations
 */
function tpb_performance_optimizations() {
    // Remove unnecessary WordPress features
    remove_action('wp_head', 'wp_generator');
    remove_action('wp_head', 'wlwmanifest_link');
    remove_action('wp_head', 'rsd_link');
    remove_action('wp_head', 'wp_shortlink_wp_head');
    remove_action('wp_head', 'adjacent_posts_rel_link_wp_head');
    
    // Remove emoji scripts
    remove_action('wp_head', 'print_emoji_detection_script', 7);
    remove_action('wp_print_styles', 'print_emoji_styles');
    remove_action('admin_print_scripts', 'print_emoji_detection_script');
    remove_action('admin_print_styles', 'print_emoji_styles');
    
    // Remove unnecessary scripts
    wp_dequeue_script('wp-embed');
}
add_action('init', 'tpb_performance_optimizations');

/**
 * Enqueue optimized assets with performance enhancements
 */
function tpb_qv_enqueue_assets() {
    // Enqueue CSS with critical CSS inlining
    wp_enqueue_style(
        'tpb-qv-css',
        get_stylesheet_directory_uri() . '/assets/css/tpb-qv.css',
        [],
        filemtime(get_stylesheet_directory() . '/assets/css/tpb-qv.css')
    );
    
    // Add preload for critical CSS
    add_action('wp_head', function() {
        echo '<link rel="preload" href="' . get_stylesheet_directory_uri() . '/assets/css/tpb-qv.css" as="style" onload="this.onload=null;this.rel=\'stylesheet\'">' . "\n";
    }, 1);
    
    // Enqueue parent window JS with defer
    wp_enqueue_script(
        'tpb-modal-js',
        get_stylesheet_directory_uri() . '/assets/js/tpb-modal.js',
        ['jquery'],
        filemtime(get_stylesheet_directory() . '/assets/js/tpb-modal.js'),
        true
    );
    
    // Add defer attribute to scripts
    add_filter('script_loader_tag', function($tag, $handle) {
        if (in_array($handle, ['tpb-modal-js', 'tpb-qv-iframe-js'])) {
            return str_replace('<script ', '<script defer ', $tag);
        }
        return $tag;
    }, 10, 2);
    
    // Enqueue iframe JS
    wp_enqueue_script(
        'tpb-qv-iframe-js',
        get_stylesheet_directory_uri() . '/assets/js/tpb-qv-iframe.js?v=' . time() . '&r=' . rand(1000, 9999),
        [],
        time(),
        true
    );
    
    // Localize script with configuration
    wp_localize_script('tpb-modal-js', 'TPB_QV_CFG', [
        'home_url' => home_url(),
        'qv_param' => 'tpb_qv'
    ]);
}
add_action('wp_enqueue_scripts', 'tpb_qv_enqueue_assets');

/**
 * Add body class for quick view mode
 */
function tpb_qv_body_class($classes) {
    if ((isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') || (isset($_GET['tpb_qv_staging']) && $_GET['tpb_qv_staging'] == '1')) {
        $classes[] = 'tpb-qv';
    }
    return $classes;
}
add_filter('body_class', 'tpb_qv_body_class');

/**
 * Hide header/footer in iframe mode with SEO-friendly approach
 */
add_action('wp_head', function() {
    if ((isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') || (isset($_GET['tpb_qv_staging']) && $_GET['tpb_qv_staging'] == '1')) {
        echo '<style>
            .tpb-qv .site-header,
            .tpb-qv .site-footer,
            .tpb-qv #wpadminbar,
            .tpb-qv .woocommerce-breadcrumb {
                display: none !important;
            }
            .tpb-qv body {
                display: flex !important;
                height: 100vh !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .tpb-qv .tpb-qv-left-panel {
                width: 45% !important;
                height: 100vh !important;
                flex-shrink: 0 !important;
                box-sizing: border-box !important;
            }
            .tpb-qv .tpb-qv-right-panel {
                width: 55% !important;
                flex: 1 !important;
                height: 100vh !important;
                overflow-y: auto !important;
                overflow-x: hidden !important;
                box-sizing: border-box !important;
                display: flex !important;
                flex-direction: column !important;
            }
            .tpb-qv .tpb-hidden { display: none !important; }
        </style>';
    }
});

/**
 * Simple iframe setup for quick view
 */
add_action('wp_footer', function() {
    if ((isset($_GET['tpb_qv']) && $_GET['tpb_qv'] == '1') || (isset($_GET['tpb_qv_staging']) && $_GET['tpb_qv_staging'] == '1')) {
        echo '<script>
        document.addEventListener("DOMContentLoaded", function() {
            const body = document.body;
            const product = document.querySelector(".woocommerce div.product");
            
            if (product) {
                // Create panels
                const leftPanel = document.createElement("div");
                leftPanel.className = "tpb-qv-left-panel";
                
                const rightPanel = document.createElement("div");
                rightPanel.className = "tpb-qv-right-panel";
                
                // Move gallery to left panel
                const gallery = product.querySelector(".woocommerce-product-gallery");
                if (gallery) {
                    leftPanel.appendChild(gallery.cloneNode(true));
                }
                
                // Move summary to right panel
                const summary = product.querySelector(".summary");
                if (summary) {
                    rightPanel.appendChild(summary.cloneNode(true));
                }
                
                // Also move any CPB components that might be outside summary
                const cpbComponents = product.querySelectorAll(".af_cp_all_components_content, .woocommerce-variation, form.cart");
                cpbComponents.forEach(comp => {
                    if (!rightPanel.contains(comp)) {
                        rightPanel.appendChild(comp.cloneNode(true));
                    }
                });
                
                // Clear body and add panels
                body.innerHTML = "";
                body.appendChild(leftPanel);
                body.appendChild(rightPanel);
            }
        });
        </script>';
    }
});

/**
 * Convenience shortcode for adding a Quick View trigger button anywhere
 */
add_shortcode('tpb_qv_button', function($atts = []) {
    $atts = shortcode_atts([
        'product' => '',
        'label'   => 'Configure Now',
        'class'   => '',
    ], $atts, 'tpb_qv_button');

    $product_id = absint($atts['product']);
    if (!$product_id) return '';

    $url = get_permalink($product_id);
    if (!$url) return '';

    $classes = trim('tpb-qv-trigger button ' . $atts['class']);
    $label   = esc_html($atts['label']);
    $url     = esc_url($url);

    return sprintf(
        '<a class="%1$s" href="%2$s" data-product-url="%2$s">%3$s</a>',
        esc_attr($classes),
        $url,
        $label
    );
});

/**
 * Add breadcrumb schema for better SEO
 */
function tpb_add_breadcrumb_schema() {
    if (is_admin() || is_home() || is_front_page()) return;
    
    $breadcrumbs = [];
    $breadcrumbs[] = [
        '@type' => 'ListItem',
        'position' => 1,
        'name' => 'Home',
        'item' => home_url('/')
    ];
    
    $position = 2;
    
    if (is_single() && get_post_type() === 'product') {
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Products',
            'item' => get_permalink(wc_get_page_id('shop'))
        ];
    }
    
    if (is_single()) {
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title(),
            'item' => get_permalink()
        ];
    } elseif (is_page()) {
        $breadcrumbs[] = [
            '@type' => 'ListItem',
            'position' => $position,
            'name' => get_the_title(),
            'item' => get_permalink()
        ];
    }
    
    if (count($breadcrumbs) > 1) {
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $breadcrumbs
        ];
        
        echo '<script type="application/ld+json">' . json_encode($schema, JSON_UNESCAPED_SLASHES) . '</script>' . "\n";
    }
}
add_action('wp_head', 'tpb_add_breadcrumb_schema', 6);

