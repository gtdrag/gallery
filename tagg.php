<?php
/**
 * Plugin Name: TAGG
 * Plugin URI: https://example.com/tagg
 * Description: A simple logo gallery plugin for displaying partner logos
 * Version: 1.0.0
 * Author: Your Name
 * Author URI: https://example.com
 * Text Domain: tagg
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants
define('TAGG_VERSION', '1.0.0');
define('TAGG_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('TAGG_PLUGIN_URL', plugin_dir_url(__FILE__));
define('TAGG_PLUGIN_BASENAME', plugin_basename(__FILE__));

// Include required files
require_once TAGG_PLUGIN_DIR . 'includes/class-tagg.php';
require_once TAGG_PLUGIN_DIR . 'includes/class-tagg-admin.php';

// Initialize the plugin
function tagg_init() {
    $tagg = new TAGG();
    $tagg->init();
    
    // Initialize admin if in admin area
    if (is_admin()) {
        $tagg_admin = new TAGG_Admin();
        $tagg_admin->init();
    }
}
add_action('plugins_loaded', 'tagg_init');

// Register activation hook
register_activation_hook(__FILE__, 'tagg_activate');

function tagg_activate() {
    // Create necessary directories
    $css_dir = TAGG_PLUGIN_DIR . 'css';
    $js_dir = TAGG_PLUGIN_DIR . 'js';
    $includes_dir = TAGG_PLUGIN_DIR . 'includes';
    
    if (!file_exists($css_dir)) {
        wp_mkdir_p($css_dir);
    }
    
    if (!file_exists($js_dir)) {
        wp_mkdir_p($js_dir);
    }
    
    if (!file_exists($includes_dir)) {
        wp_mkdir_p($includes_dir);
    }
    
    // Flush rewrite rules
    flush_rewrite_rules();
    
    // Set default options
    $default_options = array(
        'default_columns' => 3,
        'default_link' => 'yes',
    );
    
    add_option('tagg_options', $default_options);
}

// Register deactivation hook
register_deactivation_hook(__FILE__, 'tagg_deactivate');

function tagg_deactivate() {
    // Flush rewrite rules
    flush_rewrite_rules();
}

// Add settings link to plugins page
function tagg_add_settings_link($links) {
    $settings_link = '<a href="edit.php?post_type=tagg_logo&page=tagg-settings">' . __('Settings', 'tagg') . '</a>';
    array_unshift($links, $settings_link);
    return $links;
}
add_filter('plugin_action_links_' . TAGG_PLUGIN_BASENAME, 'tagg_add_settings_link'); 