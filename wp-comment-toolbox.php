<?php
/**
 * Plugin Name: WP Comment Toolbox
 * Description: A comprehensive toolset for enhancing WordPress comment forms. It reorders comment fields, customizes user role visibility, adds a dark/light mode toggle for the comment toolbar, supports link management (including disabling clickable links).
 * Version: 9.4.0
 * Author: DJABHipHop
 * Author URI: https://github.com/WP-Developer-Hub/
 * Plugin URI: https://github.com/WP-Developer-Hub/wp-comment-toolbox
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
define('WP_COMMENT_TOOLBOX_PLUGIN_ORG', 'WP-Developer-Hub');
define('WP_COMMENT_TOOLBOX_PLUGIN_SLUG', 'wp-comment-toolbox');
define('WP_COMMENT_TOOLBOX_PLUGIN_DIR', plugin_dir_path(__FILE__));
define('WP_COMMENT_TOOLBOX_PLUGIN_URL', plugin_dir_url(__FILE__));
define('WP_COMMENT_TOOLBOX_PLUGIN_BASENAME', plugin_basename(__FILE__));
define('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON', (defined('WP_DEBUG') && WP_DEBUG));
if (!class_exists('WP_Comment_Toolbox')) {
    class WP_Comment_Toolbox {

        /**
         * Constructor to initialize the plugin.
         */
        public function __construct() {
            // Load plugin text domain for translations
            add_action('plugins_loaded', [$this, 'load_plugin_textdomain']);
            
            // Include required files
            require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-admin.php');
            require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-privacy.php');
            require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-settings.php');

            if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && !WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-updates.php');
            }

            require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-comment-list.php');
            require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-comment-form.php');
            require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-spam-and-security.php');

            add_action('wp_head', array($this, 'add_custom_css'));
            add_action('wp_footer', array($this, 'add_custom_toolbar_script'));

            // Add customizer link in plugin actions
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'add_settings_link']);
        }

        /**
         * Load the plugin's text domain for translations.
         */
        public function load_plugin_textdomain() {
            load_plugin_textdomain('wpct', false, WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'languages');
        }

        /**
         * Add a custom settings tab link to the plugin action links.
         */
        public function add_settings_link($links) {
            // Build the URL to your plugin's comment form tab
            $url = esc_url(admin_url('edit-comments.php?page=wpct-comment-settings'));

            // Construct the link HTML
            $custom_link = '<a href="' . $url . '">' . __('Settings', 'wpct') . '</a>';

            // Add the link to the existing array of links
            array_push($links, $custom_link);

            return $links;
        }

        public function add_custom_toolbar_script() {
            // Ensure that comments are open and the post is not password protected
            if (is_singular() && comments_open() && !post_password_required()) {
                // Check if specific features are enabled through options
                $toolbar_enabled = get_option('wpct_toolbar_enabled', 0);
                $math_captcha_enable = get_option('wpct_enable_math_captcha', 0);
                $spam_protect_enabled = get_option('wpct_enable_spam_protect', 0);
                $char_count_enabled = get_option('wpct_character_count_enabled', 0);
                $html5_validation_enabled = get_option('wpct_enabled_html5_validation', 0);

                // Enqueue the script and style if any relevant feature is enabled
                // This ensures the script is loaded for any necessary feature
                if ($toolbar_enabled || $char_count_enabled || ($html5_validation_enabled && $math_captcha_enable)) {
                    wp_enqueue_script('wpct-script', WP_COMMENT_TOOLBOX_PLUGIN_URL . 'js/wp-comment-toolbox.js', array('jquery', 'quicktags'), null, true);
                }

                // Enqueue styles if the toolbar or character count feature is enabled
                // This ensures the proper styles are applied to the toolbar or character count
                if ($toolbar_enabled || $char_count_enabled || $math_captcha_enable) {
                    wp_enqueue_style('wpct-style', WP_COMMENT_TOOLBOX_PLUGIN_URL . 'css/wp-comment-toolbox.css');
                }

                // Initialize Quicktags if the toolbar feature is enabled
                // Quicktags adds the editor buttons to the comment textarea
                if ($toolbar_enabled) {
                    wp_enqueue_script('quicktags');
                    wp_add_inline_script('wpct-script', '(function($){$(function(){$.fn.initializeQuicktags();});})(jQuery);');
                }

                // Initialize the character count status bar if enabled
                // Displays a real-time character count in the comment form
                if ($char_count_enabled) {
                    wp_add_inline_script('wpct-script', '(function($){$(function(){$.fn.initializeStatusBar();});})(jQuery);');
                }

                // Initialize CAPTCHA validation if HTML5 validation is enabled
                // Adds CAPTCHA validation to prevent spam submissions
                if ($html5_validation_enabled) {
                    wp_add_inline_script('wpct-script', '(function($){$(function(){$.fn.initializeCaptchaValidation();});})(jQuery);');
                    wp_localize_script('wpct-script', 'wpctCaptchaMessage', array(
                        'wpctCaptchaErrorMessage' => __('Please answer the CAPTCHA question.', 'wpct'),
                        'wpctCaptchaSuccessMessage' => __('Your CAPTCHA answer was incorrect. Please try again.', 'wpct')
                    ));
                }

                // Enable spam protection if the feature is enabled
                // This hides the submit button initially and displays it after a timeout to prevent spam bots
                if ($spam_protect_enabled) {
                    $submit_button_name = esc_attr(get_option('wpct_submit_button_name'));
                    wp_add_inline_style('', "#$submit_button_name { display: none; }");
                    wp_add_inline_script('wpct-script', '(function($){$(function(){setTimeout(function() { $("#' . esc_js($submit_button_name) . '").show(); }, 8000);});})(jQuery);');
                }
            }
        }

        public function add_custom_css() {
            if (comments_open() && get_option('wpct_toolbar_enabled', 'disable')) {
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
    new WP_Comment_Toolbox();
}
