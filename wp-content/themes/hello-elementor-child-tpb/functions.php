<?php
// MINIMAL FUNCTIONS.PHP - NO WORDPRESS DEPENDENCIES
echo '<!-- TPB MINIMAL: functions.php is executing -->';
echo '<script>console.log("🧪 TPB MINIMAL: functions.php loaded successfully");</script>';

// Test if WordPress is loaded
if (function_exists('add_action')) {
    echo '<!-- TPB MINIMAL: WordPress functions available -->';
    echo '<script>console.log("🧪 TPB MINIMAL: WordPress functions available");</script>';
} else {
    echo '<!-- TPB MINIMAL: WordPress functions NOT available -->';
    echo '<script>console.log("🧪 TPB MINIMAL: WordPress functions NOT available");</script>';
}
?>