<?php
/**
 * Direct Modal Test - No Database Required
 * This tests the modal system directly
 */

// Check if files exist
$plugin_file = '/Users/jeff/Projects/tpb-store-modal/local-site/app/public/wp-content/plugins/tpb-quickview-modal/tpb-quickview-modal.php';
$js_file = '/Users/jeff/Projects/tpb-store-modal/local-site/app/public/wp-content/plugins/tpb-quickview-modal/assets/js/modal.js';
$css_file = '/Users/jeff/Projects/tpb-store-modal/local-site/app/public/wp-content/plugins/tpb-quickview-modal/assets/css/modal.css';

?>
<!DOCTYPE html>
<html>
<head>
    <title>TPB Modal System Test</title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .status { padding: 10px; margin: 10px 0; border-radius: 5px; }
        .success { background: #d4edda; color: #155724; border: 1px solid #c3e6cb; }
        .error { background: #f8d7da; color: #721c24; border: 1px solid #f5c6cb; }
        .info { background: #d1ecf1; color: #0c5460; border: 1px solid #bee5eb; }
        .test-button { 
            background: #007cba; 
            color: white; 
            padding: 10px 20px; 
            border: none; 
            border-radius: 5px; 
            cursor: pointer; 
            margin: 10px;
        }
        .test-button:hover { background: #005a87; }
    </style>
</head>
<body>
    <h1>🧪 TPB Modal System Direct Test</h1>
    
    <h2>File Status</h2>
    <div class="status <?php echo file_exists($plugin_file) ? 'success' : 'error'; ?>">
        <strong>Plugin File:</strong> <?php echo file_exists($plugin_file) ? '✅ Exists' : '❌ Missing'; ?>
        <br><small><?php echo $plugin_file; ?></small>
    </div>
    
    <div class="status <?php echo file_exists($js_file) ? 'success' : 'error'; ?>">
        <strong>JavaScript File:</strong> <?php echo file_exists($js_file) ? '✅ Exists' : '❌ Missing'; ?>
        <br><small><?php echo $js_file; ?></small>
    </div>
    
    <div class="status <?php echo file_exists($css_file) ? 'success' : 'error'; ?>">
        <strong>CSS File:</strong> <?php echo file_exists($css_file) ? '✅ Exists' : '❌ Missing'; ?>
        <br><small><?php echo $css_file; ?></small>
    </div>
    
    <h2>Test Modal Functionality</h2>
    <p>Click the button below to test the modal system:</p>
    
    <button class="test-button" onclick="testModal()">Test Modal (Product)</button>
    <button class="test-button" onclick="testModalHome()">Test Modal (Homepage)</button>
    <button class="test-button" onclick="testCpbButton()">Test CPB Button</button>
    
    <h2>Debug Information</h2>
    <div id="debug-info" class="status info">
        <strong>Console Messages:</strong> Open browser console (F12) to see debug messages
    </div>
    
    <!-- Include jQuery -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <!-- Include our modal CSS -->
    <link rel="stylesheet" href="/wp-content/plugins/tpb-quickview-modal/assets/css/modal.css">
    
    <!-- Include our modal JS -->
    <script src="/wp-content/plugins/tpb-quickview-modal/assets/js/modal.js"></script>
    
    <!-- Test Modal HTML -->
    <div id="tpb-qv-overlay" class="tpb-qv-overlay" style="display: none;">
        <div class="tpb-qv-modal">
            <button class="tpb-qv-close" aria-label="Close modal">&times;</button>
            <iframe id="tpb-qv-iframe" class="tpb-qv-iframe" src="about:blank"></iframe>
        </div>
    </div>
    
    <script>
        // Test modal functionality
        function testModal() {
            console.log('🧪 Testing modal functionality...');
            
            // Check if TPBModal exists
            if (typeof window.TPBModal !== 'undefined') {
                console.log('✅ TPBModal object found');
                
                // Test opening modal with a real product URL
                const testUrl = window.location.origin + '/product/test-product/';
                console.log('🔗 Opening modal with URL:', testUrl);
                window.TPBModal.open(testUrl);
            } else {
                console.error('❌ TPBModal object not found');
                alert('TPBModal object not found. Check console for errors.');
            }
        }
        
        // Test modal with homepage
        function testModalHome() {
            console.log('🧪 Testing modal with homepage...');
            
            if (typeof window.TPBModal !== 'undefined') {
                console.log('✅ TPBModal object found');
                
                // Test opening modal with homepage
                const testUrl = window.location.origin + '/';
                console.log('🔗 Opening modal with URL:', testUrl);
                window.TPBModal.open(testUrl);
            } else {
                console.error('❌ TPBModal object not found');
                alert('TPBModal object not found. Check console for errors.');
            }
        }
        
        // Test CPB button detection
        function testCpbButton() {
            console.log('🧪 Testing CPB button detection...');
            
            // Create a test CPB button
            const testButton = document.createElement('a');
            testButton.className = 'afc_add_to_cart_button';
            testButton.textContent = 'Configure Now';
            testButton.href = window.location.origin + '/product/test-product/';
            testButton.style.cssText = 'display: inline-block; margin: 10px; padding: 10px; background: #007cba; color: white; text-decoration: none; border-radius: 5px;';
            
            // Add click handler for testing
            testButton.addEventListener('click', function(e) {
                e.preventDefault();
                console.log('🔴 Test CPB button clicked!');
                if (typeof window.TPBModal !== 'undefined') {
                    window.TPBModal.open(this.href);
                }
            });
            
            document.body.appendChild(testButton);
            
            // Trigger button detection
            if (typeof window.TPBModal !== 'undefined' && window.TPBModal.detectButtons) {
                window.TPBModal.detectButtons();
                console.log('✅ Button detection triggered');
            } else {
                console.error('❌ Button detection not available');
            }
        }
        
        // Update debug info
        function updateDebugInfo() {
            const debugDiv = document.getElementById('debug-info');
            const info = [];
            
            if (typeof window.TPBModal !== 'undefined') {
                info.push('✅ TPBModal object loaded');
            } else {
                info.push('❌ TPBModal object not found');
            }
            
            if (typeof jQuery !== 'undefined') {
                info.push('✅ jQuery loaded');
            } else {
                info.push('❌ jQuery not found');
            }
            
            debugDiv.innerHTML = '<strong>Status:</strong><br>' + info.join('<br>');
        }
        
        // Run when page loads
        $(document).ready(function() {
            console.log('🚀 Test page loaded');
            updateDebugInfo();
            
            // Wait a bit for modal to initialize
            setTimeout(function() {
                updateDebugInfo();
            }, 2000);
        });
    </script>
</body>
</html>
