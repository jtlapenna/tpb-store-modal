<?php
// ABSOLUTELY MINIMAL FUNCTIONS.PHP - NO WORDPRESS DEPENDENCIES
echo '<!-- TPB MINIMAL: functions.php is executing -->';

// Test if WordPress is loaded
if (function_exists('add_action')) {
    echo '<!-- TPB MINIMAL: WordPress functions available -->';
    
    // Add a VERY OBVIOUS visual test that will be impossible to miss
    add_action('wp_head', function() {
        echo '<style>
        body::before {
            content: "🚨 TPB DEPLOYMENT TEST - FUNCTIONS.PHP IS WORKING! 🚨";
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            background: #ff0000;
            color: #ffffff;
            font-size: 24px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            z-index: 999999;
            border: 5px solid #ffff00;
            box-shadow: 0 0 20px #ff0000;
        }
        </style>';
        echo '<script>console.log("🚨 TPB DEPLOYMENT TEST: functions.php is working!");</script>';
    });
    
    add_action('wp_footer', function() {
        echo '<div style="position: fixed; bottom: 0; left: 0; right: 0; background: #00ff00; color: #000000; font-size: 20px; font-weight: bold; text-align: center; padding: 15px; z-index: 999999; border: 3px solid #000000;">✅ TPB FOOTER TEST - DEPLOYMENT WORKING! ✅</div>';
        echo '<script>console.log("✅ TPB FOOTER TEST: wp_footer hook fired");</script>';
    });
} else {
    echo '<!-- TPB MINIMAL: WordPress functions NOT available -->';
}
?>