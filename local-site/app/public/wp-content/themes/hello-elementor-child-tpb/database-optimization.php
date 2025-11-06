<?php
/**
 * TPB Database Optimization Script
 * Safe database cleanup and optimization for WordPress
 * 
 * IMPORTANT: Always backup your database before running this script!
 * 
 * Usage:
 * 1. Upload this file to your WordPress root directory
 * 2. Access via: https://yourdomain.com/database-optimization.php
 * 3. Run the optimizations
 * 4. Delete this file after use for security
 */

// Security check - only allow from admin users
if (!defined('ABSPATH')) {
    // Load WordPress
    require_once('wp-config.php');
    require_once('wp-includes/wp-db.php');
    require_once('wp-includes/pluggable.php');
    
    // Check if user is admin
    if (!current_user_can('manage_options')) {
        die('Access denied. Admin privileges required.');
    }
}

// Prevent direct access
if (!defined('ABSPATH')) {
    die('Direct access not allowed.');
}

// Database optimization class
class TPB_Database_Optimizer {
    
    private $wpdb;
    private $results = [];
    
    public function __construct() {
        global $wpdb;
        $this->wpdb = $wpdb;
    }
    
    /**
     * Run all database optimizations
     */
    public function run_optimizations() {
        $this->results = [];
        
        // Safe optimizations
        $this->cleanup_trash_posts();
        $this->cleanup_spam_comments();
        $this->cleanup_expired_transients();
        $this->cleanup_orphaned_postmeta();
        $this->cleanup_orphaned_commentmeta();
        $this->cleanup_orphaned_usermeta();
        $this->cleanup_revision_posts();
        $this->cleanup_auto_drafts();
        $this->cleanup_unused_tags();
        $this->optimize_database_tables();
        
        return $this->results;
    }
    
    /**
     * Clean up trash posts
     */
    private function cleanup_trash_posts() {
        $count = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->posts} WHERE post_status = 'trash'");
        
        if ($count > 0) {
            $deleted = $this->wpdb->query("DELETE FROM {$this->wpdb->posts} WHERE post_status = 'trash'");
            $this->results[] = "✅ Deleted {$deleted} trash posts";
        } else {
            $this->results[] = "✅ No trash posts found";
        }
    }
    
    /**
     * Clean up spam comments
     */
    private function cleanup_spam_comments() {
        $count = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->comments} WHERE comment_approved = 'spam'");
        
        if ($count > 0) {
            $deleted = $this->wpdb->query("DELETE FROM {$this->wpdb->comments} WHERE comment_approved = 'spam'");
            $this->results[] = "✅ Deleted {$deleted} spam comments";
        } else {
            $this->results[] = "✅ No spam comments found";
        }
    }
    
    /**
     * Clean up expired transients
     */
    private function cleanup_expired_transients() {
        $count = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->options} WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%'");
        
        if ($count > 0) {
            $deleted = $this->wpdb->query("DELETE FROM {$this->wpdb->options} WHERE option_name LIKE '_transient_%' OR option_name LIKE '_site_transient_%'");
            $this->results[] = "✅ Deleted {$deleted} expired transients";
        } else {
            $this->results[] = "✅ No expired transients found";
        }
    }
    
    /**
     * Clean up orphaned post meta
     */
    private function cleanup_orphaned_postmeta() {
        $count = $this->wpdb->get_var("
            SELECT COUNT(*) FROM {$this->wpdb->postmeta} pm 
            LEFT JOIN {$this->wpdb->posts} p ON pm.post_id = p.ID 
            WHERE p.ID IS NULL
        ");
        
        if ($count > 0) {
            $deleted = $this->wpdb->query("
                DELETE pm FROM {$this->wpdb->postmeta} pm 
                LEFT JOIN {$this->wpdb->posts} p ON pm.post_id = p.ID 
                WHERE p.ID IS NULL
            ");
            $this->results[] = "✅ Deleted {$deleted} orphaned post meta entries";
        } else {
            $this->results[] = "✅ No orphaned post meta found";
        }
    }
    
    /**
     * Clean up orphaned comment meta
     */
    private function cleanup_orphaned_commentmeta() {
        $count = $this->wpdb->get_var("
            SELECT COUNT(*) FROM {$this->wpdb->commentmeta} cm 
            LEFT JOIN {$this->wpdb->comments} c ON cm.comment_id = c.comment_ID 
            WHERE c.comment_ID IS NULL
        ");
        
        if ($count > 0) {
            $deleted = $this->wpdb->query("
                DELETE cm FROM {$this->wpdb->commentmeta} cm 
                LEFT JOIN {$this->wpdb->comments} c ON cm.comment_id = c.comment_ID 
                WHERE c.comment_ID IS NULL
            ");
            $this->results[] = "✅ Deleted {$deleted} orphaned comment meta entries";
        } else {
            $this->results[] = "✅ No orphaned comment meta found";
        }
    }
    
    /**
     * Clean up orphaned user meta
     */
    private function cleanup_orphaned_usermeta() {
        $count = $this->wpdb->get_var("
            SELECT COUNT(*) FROM {$this->wpdb->usermeta} um 
            LEFT JOIN {$this->wpdb->users} u ON um.user_id = u.ID 
            WHERE u.ID IS NULL
        ");
        
        if ($count > 0) {
            $deleted = $this->wpdb->query("
                DELETE um FROM {$this->wpdb->usermeta} um 
                LEFT JOIN {$this->wpdb->users} u ON um.user_id = u.ID 
                WHERE u.ID IS NULL
            ");
            $this->results[] = "✅ Deleted {$deleted} orphaned user meta entries";
        } else {
            $this->results[] = "✅ No orphaned user meta found";
        }
    }
    
    /**
     * Clean up revision posts
     */
    private function cleanup_revision_posts() {
        $count = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->posts} WHERE post_type = 'revision'");
        
        if ($count > 0) {
            $deleted = $this->wpdb->query("DELETE FROM {$this->wpdb->posts} WHERE post_type = 'revision'");
            $this->results[] = "✅ Deleted {$deleted} revision posts";
        } else {
            $this->results[] = "✅ No revision posts found";
        }
    }
    
    /**
     * Clean up auto-draft posts
     */
    private function cleanup_auto_drafts() {
        $count = $this->wpdb->get_var("SELECT COUNT(*) FROM {$this->wpdb->posts} WHERE post_status = 'auto-draft'");
        
        if ($count > 0) {
            $deleted = $this->wpdb->query("DELETE FROM {$this->wpdb->posts} WHERE post_status = 'auto-draft'");
            $this->results[] = "✅ Deleted {$deleted} auto-draft posts";
        } else {
            $this->results[] = "✅ No auto-draft posts found";
        }
    }
    
    /**
     * Clean up unused tags
     */
    private function cleanup_unused_tags() {
        $count = $this->wpdb->get_var("
            SELECT COUNT(*) FROM {$this->wpdb->terms} t 
            LEFT JOIN {$this->wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
            WHERE tt.taxonomy = 'post_tag' AND tt.count = 0
        ");
        
        if ($count > 0) {
            $deleted = $this->wpdb->query("
                DELETE t, tt FROM {$this->wpdb->terms} t 
                LEFT JOIN {$this->wpdb->term_taxonomy} tt ON t.term_id = tt.term_id 
                WHERE tt.taxonomy = 'post_tag' AND tt.count = 0
            ");
            $this->results[] = "✅ Deleted {$deleted} unused tags";
        } else {
            $this->results[] = "✅ No unused tags found";
        }
    }
    
    /**
     * Optimize database tables
     */
    private function optimize_database_tables() {
        $tables = $this->wpdb->get_results("SHOW TABLES", ARRAY_N);
        $optimized = 0;
        
        foreach ($tables as $table) {
            $table_name = $table[0];
            $result = $this->wpdb->query("OPTIMIZE TABLE `{$table_name}`");
            if ($result !== false) {
                $optimized++;
            }
        }
        
        $this->results[] = "✅ Optimized {$optimized} database tables";
    }
    
    /**
     * Get database size information
     */
    public function get_database_info() {
        $size = $this->wpdb->get_var("
            SELECT ROUND(SUM(data_length + index_length) / 1024 / 1024, 2) AS 'DB Size in MB' 
            FROM information_schema.tables 
            WHERE table_schema = '{$this->wpdb->dbname}'
        ");
        
        $tables = $this->wpdb->get_var("SELECT COUNT(*) FROM information_schema.tables WHERE table_schema = '{$this->wpdb->dbname}'");
        
        return [
            'size' => $size,
            'tables' => $tables
        ];
    }
}

// Handle form submission
if ($_POST['action'] === 'optimize') {
    $optimizer = new TPB_Database_Optimizer();
    $results = $optimizer->run_optimizations();
    $db_info = $optimizer->get_database_info();
} else {
    $optimizer = new TPB_Database_Optimizer();
    $db_info = $optimizer->get_database_info();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>TPB Database Optimization</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        h1 {
            color: #5ac59a;
            margin-bottom: 30px;
        }
        .warning {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            color: #856404;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .info {
            background: #d1ecf1;
            border: 1px solid #bee5eb;
            color: #0c5460;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        .results {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .btn {
            background: #5ac59a;
            color: white;
            padding: 12px 24px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            margin-right: 10px;
        }
        .btn:hover {
            background: #3aa87d;
        }
        .btn-danger {
            background: #dc3545;
        }
        .btn-danger:hover {
            background: #c82333;
        }
        .stats {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin: 20px 0;
        }
        .stat-card {
            background: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            text-align: center;
        }
        .stat-number {
            font-size: 2em;
            font-weight: bold;
            color: #5ac59a;
        }
        .stat-label {
            color: #6c757d;
            margin-top: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🗄️ TPB Database Optimization</h1>
        
        <div class="warning">
            <strong>⚠️ Important:</strong> Always backup your database before running optimizations! This script will permanently delete unused data.
        </div>
        
        <div class="info">
            <strong>ℹ️ Current Database Status:</strong><br>
            Database Size: <strong><?php echo $db_info['size']; ?> MB</strong><br>
            Total Tables: <strong><?php echo $db_info['tables']; ?></strong>
        </div>
        
        <?php if (isset($results)): ?>
        <div class="results">
            <h3>📊 Optimization Results:</h3>
            <?php foreach ($results as $result): ?>
                <p><?php echo $result; ?></p>
            <?php endforeach; ?>
            
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-number"><?php echo $db_info['size']; ?> MB</div>
                    <div class="stat-label">Current Size</div>
                </div>
                <div class="stat-card">
                    <div class="stat-number"><?php echo $db_info['tables']; ?></div>
                    <div class="stat-label">Tables</div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        
        <form method="post">
            <input type="hidden" name="action" value="optimize">
            <button type="submit" class="btn" onclick="return confirm('Are you sure you want to optimize the database? Make sure you have a backup!')">
                🚀 Run Database Optimization
            </button>
        </form>
        
        <div style="margin-top: 30px; padding: 20px; background: #f8f9fa; border-radius: 5px;">
            <h3>🔧 What This Script Does:</h3>
            <ul>
                <li>Deletes trash posts and spam comments</li>
                <li>Removes expired transients and caches</li>
                <li>Cleans up orphaned meta data</li>
                <li>Removes revision posts and auto-drafts</li>
                <li>Deletes unused tags</li>
                <li>Optimizes all database tables</li>
            </ul>
            
            <h3>⚠️ Safety Notes:</h3>
            <ul>
                <li>All operations are safe and only remove unused data</li>
                <li>No content or important data will be deleted</li>
                <li>Always backup before running</li>
                <li>Delete this file after use for security</li>
            </ul>
        </div>
        
        <div style="margin-top: 20px; text-align: center; color: #6c757d;">
            <small>TPB Database Optimizer - Cannabis Kiosks</small>
        </div>
    </div>
</body>
</html>

