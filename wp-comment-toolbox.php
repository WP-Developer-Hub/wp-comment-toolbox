<?php
/**
 * Plugin Name: WP Comment Toolbox
 * Description: A comprehensive toolset for enhancing WordPress comment forms. It reorders comment fields, customizes user role visibility, adds a dark/light mode toggle for the comment toolbar, supports link management (including disabling clickable links), and integrates with popular WordPress plugins for user role display.
 * Version: 1.1.0
 * Author: DJABHipHop
 * Requires PHP: 7.2
 * Requires at least: 6.0
 * License: GPL-2.0-or-later
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 * Text Domain: wpct
 */

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

// Define plugin constants for easy reference
define('WP_COMMENT_TOOLBOX_PLUGIN_DIR', plugin_dir_path(__FILE__)); // Plugin directory path
define('WP_COMMENT_TOOLBOX_PLUGIN_URL', plugin_dir_url(__FILE__));  // Plugin directory URL

class WP_Comment_Toolbox {

    /**
     * Constructor to initialize the plugin.
     */
    public function __construct() {
        // Load plugin text domain for translations
        add_action('plugins_loaded', [$this, 'load_plugin_textdomain']);
        
        // Include required files
        require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-settings.php');
        require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-comment-list.php');
        require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-comment-form.php');
        require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-comment-author-roles.php');
        require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-comment-spam-and-security.php');

        add_action('wp_head', array($this, 'add_custom_css'));
        add_action('wp_footer', array($this, 'add_custom_toolbar_script'));

        // Add customizer link in plugin actions
        add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_customizer_link']);
    }

    /**
     * Load the plugin's text domain for translations.
     */
    public function load_plugin_textdomain() {
        load_plugin_textdomain('wpct', false, WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'languages');
    }

    /**
     * Add a Customizer link to the plugin action links.
     */
    public function add_customizer_link($links) {
        $customizer_link = '<a href="' . esc_url(admin_url('customize.php')) . '">' . __('Customizer', 'wpct') . '</a>';
        array_push($links, $customizer_link);
        return $links;
    }

    public function add_custom_toolbar_script() {
        if (get_option('wpct_toolbar_enabled', 'disable')) {
            wp_enqueue_script('quicktags');
            wp_enqueue_script('wp-comment-toolbox-script', WP_COMMENT_TOOLBOX_PLUGIN_URL . 'js/wp-comment-toolbox.js', array('jquery', 'quicktags'), null, true);
            wp_enqueue_style('wp-comment-toolbox-style', WP_COMMENT_TOOLBOX_PLUGIN_URL . 'css/wp-comment-toolbox.css');
        }
    }

    public function add_custom_css() {
        if (get_option('wpct_toolbar_enabled', 'disable')) {
            $toolbar_mode = get_option('wpct_toolbar_mode', 'light');
            $bg_color = ($toolbar_mode === 'light') ? '#e9e9e9' : '#111';
            $border_color = ($toolbar_mode === 'light') ? '#ccc' : '#000';

            echo "<style>
                :root {
                    --wpct-toolbar-bg-color: $bg_color;
                    --wpct-toolbar-border-color: $border_color;
                }
            </style>";
        }
    }
}

// Instantiate the plugin class
new WP_Comment_Toolbox();
?>
