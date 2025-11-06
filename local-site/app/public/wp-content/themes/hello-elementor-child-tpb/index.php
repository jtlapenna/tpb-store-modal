<?php
// TEST FILE - This should definitely be loaded by WordPress
echo '<!-- TPB INDEX TEST: index.php is executing -->';
echo '<script>console.log("🧪 TPB INDEX TEST: index.php loaded successfully");</script>';

// Include the parent theme's index.php
$parent_theme = get_template_directory();
if (file_exists($parent_theme . '/index.php')) {
    include $parent_theme . '/index.php';
} else {
    echo '<!-- TPB INDEX TEST: Parent theme index.php not found -->';
    echo '<script>console.log("🧪 TPB INDEX TEST: Parent theme index.php not found");</script>';
}
?>
