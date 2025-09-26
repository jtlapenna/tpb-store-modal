<?php
// ABSOLUTELY MINIMAL FUNCTIONS.PHP - NO WORDPRESS DEPENDENCIES
echo '<!-- TPB MINIMAL: functions.php is executing -->';

// Test if WordPress is loaded
if (function_exists('add_action')) {
    echo '<!-- TPB MINIMAL: WordPress functions available -->';
    
    // Add a simple hook that will fire
    add_action('wp_head', function() {
        echo '<!-- TPB HOOK TEST: wp_head hook fired -->';
        echo '<script>console.log("🧪 TPB HOOK TEST: wp_head hook fired");</script>';
    });
    
    add_action('wp_footer', function() {
        echo '<!-- TPB HOOK TEST: wp_footer hook fired -->';
        echo '<script>console.log("🧪 TPB HOOK TEST: wp_footer hook fired");</script>';
    });
} else {
    echo '<!-- TPB MINIMAL: WordPress functions NOT available -->';
}
?>