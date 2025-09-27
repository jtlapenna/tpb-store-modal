<?php
// ULTRA SIMPLE FUNCTIONS.PHP TEST
echo '<!-- TPB FUNCTIONS.PHP LOADED -->';

// Test if WordPress is loaded
if (function_exists('add_action')) {
    echo '<!-- TPB: WordPress functions available -->';
    
    // Add visual test
    add_action('wp_head', function() {
        echo '<style>body::before{content:"🚨 TPB FUNCTIONS.PHP WORKING! 🚨";position:fixed;top:0;left:0;right:0;background:#ff0000;color:#fff;font-size:20px;font-weight:bold;text-align:center;padding:10px;z-index:999999;}</style>';
        echo '<script>console.log("🚨 TPB: functions.php working!");</script>';
    });
    
    add_action('wp_footer', function() {
        echo '<div style="position:fixed;bottom:0;left:0;right:0;background:#00ff00;color:#000;font-size:16px;font-weight:bold;text-align:center;padding:10px;z-index:999999;">✅ TPB FOOTER WORKING ✅</div>';
        echo '<script>console.log("✅ TPB: wp_footer working!");</script>';
    });
} else {
    echo '<!-- TPB: WordPress functions NOT available -->';
}
?>