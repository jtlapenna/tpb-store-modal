<?php
/**
 * Reset WordPress Admin Password
 * This script will reset the admin password to 'admin123'
 */

// Load WordPress
require_once('wp-config.php');
require_once('wp-includes/wp-db.php');

// Initialize WordPress
$wpdb = new wpdb(DB_USER, DB_PASSWORD, DB_NAME, DB_HOST);

echo "<h2>WordPress User Reset Tool</h2>";

// Get all users
$users = $wpdb->get_results("SELECT ID, user_login, user_email, user_status FROM wp_users ORDER BY ID");

echo "<h3>Current Users in Database:</h3>";
echo "<table border='1' cellpadding='5'>";
echo "<tr><th>ID</th><th>Username</th><th>Email</th><th>Status</th></tr>";

foreach($users as $user) {
    echo "<tr>";
    echo "<td>" . $user->ID . "</td>";
    echo "<td>" . $user->user_login . "</td>";
    echo "<td>" . $user->user_email . "</td>";
    echo "<td>" . $user->user_status . "</td>";
    echo "</tr>";
}
echo "</table>";

// Reset password for first admin user
if(!empty($users)) {
    $admin_user = $users[0]; // Get first user (usually admin)
    
    // Generate new password
    $new_password = 'admin123';
    $hashed_password = wp_hash_password($new_password);
    
    // Update password
    $result = $wpdb->update(
        'wp_users',
        array('user_pass' => $hashed_password),
        array('ID' => $admin_user->ID)
    );
    
    if($result !== false) {
        echo "<h3>✅ Password Reset Successful!</h3>";
        echo "<p><strong>Username:</strong> " . $admin_user->user_login . "</p>";
        echo "<p><strong>New Password:</strong> admin123</p>";
        echo "<p><strong>Login URL:</strong> <a href='wp-admin/'>wp-admin/</a></p>";
    } else {
        echo "<h3>❌ Password Reset Failed</h3>";
        echo "<p>Error: " . $wpdb->last_error . "</p>";
    }
} else {
    echo "<h3>❌ No users found in database</h3>";
}

echo "<hr>";
echo "<p><strong>Note:</strong> Delete this file after use for security!</p>";
?>







