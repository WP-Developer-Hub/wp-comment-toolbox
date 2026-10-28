<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WP_Comment_Toolbox_Settings')) {
    class WP_Comment_Toolbox_Settings {
        public function __construct() {
            add_action('admin_menu', [$this, 'add_comment_settings_submenu']);
            add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        }

        public function enqueue_admin_styles($hook) {
            // Only enqueue on the comment settings page
            if ($hook === 'comments_page_wpct-comment-settings') {
                wp_enqueue_style('wpct-style', WP_COMMENT_TOOLBOX_PLUGIN_URL . 'css/wp-comment-toolbox-admin.css', [], '9.6.2');
            }
        }

        public function add_comment_settings_submenu() {
            add_comments_page(
                'Comment Settings',
                'Comment Settings',
                'manage_options',
                'wpct-comment-settings',
                [$this, 'comment_settings_page']
           );
        }

        public function comment_settings_page() {
            $active_tab = isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'spam_security';

            $is_not_last_tab = ($active_tab != 'test_commend');

            // Handle form submission
            if (isset($_SERVER['REQUEST_METHOD']) && sanitize_text_field(wp_unslash($_SERVER['REQUEST_METHOD'])) === 'POST' && isset($_POST['wpct_settings_nonce']) && wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpct_settings_nonce'])), 'wpct_save_settings')) {
                $this->save_settings();
            }


            echo '<div class="wrap">';
            echo '<h1>Comment Settings</h1>';
            
            // Start the form tag to allow submission
            echo '<form method="POST">';
            
            // Nonce field for security
            wp_nonce_field('wpct_save_settings', 'wpct_settings_nonce');

            if ($is_not_last_tab) {
                // Display submit button before the settings
                WPCT_Helper::wpct_submit_button(0);
            }

            // Tabs
            echo '<h2 class="nav-tab-wrapper">';
            $tabs = [
            'spam_security' => 'Spam & Security',
            'privacy' => 'Privacy',
            'comment_list' => 'Comment List',
            'comment_form' => 'Comment Form',
            'extra' => 'Extras',
            'admin' => 'Admin',
            'test_commend' => 'Test Commend',
            ];

            foreach ($tabs as $tab => $label) {
                echo '<a href="?page=wpct-comment-settings&tab=' . esc_attr($tab) . '" class="nav-tab ' . esc_attr($active_tab === $tab ? 'nav-tab-active' : '') . '">' . esc_html($label) . '</a>';
            }

            echo '</h2>';
            echo '<table class="form-table">';
            echo '<tbody>';

            // Tab content
            $method = $active_tab . '_settings';
            if (method_exists($this, $method)) {
                $this->$method();
            } else {
                $this->extra_settings(); // Fallback to extra settings if no method found
            }

            echo '</tbody>';
            echo '</table>';

            if ($is_not_last_tab) {
                // Display submit button before the settings
                WPCT_Helper::wpct_submit_button(0);
            }

            echo '</form>'; // Close the form tag
            echo '</div>';
        }

        public function spam_security_settings() {
            $this->wpct_load_setting('spam_security');
        }

        public function privacy_settings() {
            $this->wpct_load_setting('privacy');
        }

        public function comment_list_settings() {
            $this->wpct_load_setting('comment_list');
        }

        public function comment_form_settings() {
            $this->wpct_load_setting('comment_form');
        }

        public function extra_settings() {
            $this->wpct_load_setting('extra');
        }

        public function admin_settings() {
            $this->wpct_load_setting('admin');
        }

        public function test_commend_settings() {
            $this->wpct_load_setting('test_commend');
        }

        public function save_settings() {
            // Check nonce first for security
            if (!isset($_POST['wpct_settings_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpct_settings_nonce'])), 'wpct_save_settings')) {
                $this->wpct_setting_nonce_message();
            }

            // Get the active tab to determine which settings to save
            $active_tab = (isset($_GET['tab']) ? sanitize_text_field(wp_unslash($_GET['tab'])) : 'spam_security');

            // Save settings for each tab separately
            switch ($active_tab) {
                case 'spam_security':
                    $this->save_spam_security_settings();
                    break;
                case 'privacy':
                    $this->save_privacy_settings();
                    break;
                case 'comment_list':
                    $this->save_comment_list_settings();
                    break;
                case 'comment_form':
                    $this->save_comment_form_settings();
                    break;
                case 'extra':
                    $this->save_extra_settings();
                    break;
                case 'admin':
                    $this->save_admin_settings();
                    break;
                default:
                    $this->save_spam_security_settings(); // Fallback to 'spam_security'
                    break;
            }

            // Optionally, you can display a success message
            echo '<div class="updated"><p>Settings saved successfully.</p></div>';
        }

        // Save settings specific to Spam & Security tab
        private function save_spam_security_settings() {
            if (!isset($_POST['wpct_settings_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpct_settings_nonce'])), 'wpct_save_settings')) {
                $this->wpct_setting_nonce_message();
            }

            if (isset($_POST['wpct_comment_message_limit'])) {
                update_option('wpct_comment_message_limit', intval(wp_unslash($_POST['wpct_comment_message_limit'])));
            }

            if (isset($_POST['wpct_disable_clickable_links'])) {
                update_option('wpct_disable_clickable_links', sanitize_text_field(wp_unslash($_POST['wpct_disable_clickable_links'])));
            }

            if (isset($_POST['wpct_enable_wp_kses_post'])) {
                update_option('wpct_enable_wp_kses_post', sanitize_text_field(wp_unslash($_POST['wpct_enable_wp_kses_post'])));
            }

            if (get_option('show_comments_cookies_opt_in', true) && isset($_POST['wpct_comment_flood_delay'])) {
                update_option('wpct_comment_flood_delay', intval(wp_unslash($_POST['wpct_comment_flood_delay'])));
            }

            if (isset($_POST['wpct_enable_spam_protect'])) {
                update_option('wpct_enable_spam_protect', sanitize_text_field(wp_unslash($_POST['wpct_enable_spam_protect'])));
            }

            if (isset($_POST['wpct_submit_button_name'])) {
                update_option('wpct_submit_button_name', sanitize_text_field(wp_unslash($_POST['wpct_submit_button_name'])));
            }

            if (isset($_POST['wpct_enable_math_captcha'])) {
                update_option('wpct_enable_math_captcha', sanitize_text_field(wp_unslash($_POST['wpct_enable_math_captcha'])));
            }

            if (isset($_POST['wpct_math_captcha_level'])) {
                update_option('wpct_math_captcha_level', sanitize_text_field(wp_unslash($_POST['wpct_math_captcha_level'])));
            }
        }

        private function save_privacy_settings() {
            if (!isset($_POST['wpct_settings_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpct_settings_nonce'])), 'wpct_save_settings')) {
                $this->wpct_setting_nonce_message();
            }

            if (isset($_POST['wpct_comment_cookie_lifetime'])) {
                update_option('wpct_comment_cookie_lifetime', intval(wp_unslash($_POST['wpct_comment_cookie_lifetime'])));
            }
        }

        // Save settings specific to Comment List tab
        private function save_comment_list_settings() {
            if (!isset($_POST['wpct_settings_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpct_settings_nonce'])), 'wpct_save_settings')) {
                $this->wpct_setting_nonce_message();
            }

            if (isset($_POST['wpct_author_link_visibility'])) {
                update_option('wpct_author_link_visibility', sanitize_text_field(wp_unslash($_POST['wpct_author_link_visibility'])));
            }

            if (isset($_POST['wpct_author_link_type'])) {
                update_option('wpct_author_link_type', sanitize_text_field(wp_unslash($_POST['wpct_author_link_type'])));
            }

            if (isset($_POST['wpct_format_comment_text'])) {
                update_option('wpct_format_comment_text', sanitize_text_field(wp_unslash($_POST['wpct_format_comment_text'])));
            }

            if (isset($_POST['wpct_twitter_mentions_linking'])) {
                update_option('wpct_twitter_mentions_linking', sanitize_text_field(wp_unslash($_POST['wpct_twitter_mentions_linking'])));
            }
        }

        // Save settings specific to Comment Form tab
        private function save_comment_form_settings() {
            if (!isset($_POST['wpct_settings_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpct_settings_nonce'])), 'wpct_save_settings')) {
                $this->wpct_setting_nonce_message();
            }

            if (isset($_POST['wpct_enabled_html5_validation'])) {
                update_option('wpct_enabled_html5_validation', sanitize_text_field(wp_unslash($_POST['wpct_enabled_html5_validation'])));
            }

            if (isset($_POST['wpct_author_placeholder'])) {
                update_option('wpct_author_placeholder', sanitize_textarea_field(wp_unslash($_POST['wpct_author_placeholder'])));
            }

            if (isset($_POST['wpct_comment_textarea_placeholder'])) {
                update_option('wpct_comment_textarea_placeholder', sanitize_textarea_field(wp_unslash($_POST['wpct_comment_textarea_placeholder'])));
            }

            if (isset($_POST['wpct_comment_textarea_height'])) {
                update_option('wpct_comment_textarea_height', intval(wp_unslash($_POST['wpct_comment_textarea_height'])));
            }

            if (isset($_POST['wpct_comment_notes_before'])) {
                update_option('wpct_comment_notes_before', sanitize_textarea_field(wp_unslash($_POST['wpct_comment_notes_before'])));
            }

            if (isset($_POST['wpct_comment_notes_after'])) {
                update_option('wpct_comment_notes_after', sanitize_textarea_field(wp_unslash($_POST['wpct_comment_notes_after'])));
            }

            if (isset($_POST['wpct_comment_form_layout'])) {
                update_option('wpct_comment_form_layout', sanitize_textarea_field(wp_unslash($_POST['wpct_comment_form_layout'])));
            }

            if (isset($_POST['wpct_comment_form_cookies_msg'])) {
                update_option('wpct_comment_form_cookies_msg', sanitize_textarea_field(wp_unslash($_POST['wpct_comment_form_cookies_msg'])));
            }
        }

        // Save settings specific to Extra tab
        private function save_extra_settings() {
            if (!isset($_POST['wpct_settings_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpct_settings_nonce'])), 'wpct_save_settings')) {
                $this->wpct_setting_nonce_message();
            }

            if (isset($_POST['wpct_toolbar_enabled'])) {
                update_option('wpct_toolbar_enabled', sanitize_text_field(wp_unslash($_POST['wpct_toolbar_enabled'])));
            }

            if (isset($_POST['wpct_toolbar_mode'])) {
                update_option('wpct_toolbar_mode', sanitize_text_field(wp_unslash($_POST['wpct_toolbar_mode'])));
            }

            if (isset($_POST['wpct_character_count_enabled'])) {
                update_option('wpct_character_count_enabled', sanitize_text_field(wp_unslash($_POST['wpct_character_count_enabled'])));
            }

            if (isset($_POST['wpct_comment_count_format_enabled'])) {
                update_option('wpct_comment_count_format_enabled', sanitize_text_field(wp_unslash($_POST['wpct_comment_count_format_enabled'])));
            }
        }

        // Save settings specific to Admin tab
        private function save_admin_settings() {
            if (!isset($_POST['wpct_settings_nonce']) || !wp_verify_nonce(sanitize_text_field(wp_unslash($_POST['wpct_settings_nonce'])), 'wpct_save_settings')) {
                $this->wpct_setting_nonce_message();
            }

            if (isset($_POST['wpct_show_block_ip_action'])) {
                update_option('wpct_show_block_ip_action', sanitize_text_field(wp_unslash($_POST['wpct_show_block_ip_action'])));
            }

            if (isset($_POST['wpct_enable_nuke_all_sus_comment_button'])) {
                update_option('wpct_enable_nuke_all_sus_comment_button', sanitize_text_field(wp_unslash($_POST['wpct_enable_nuke_all_sus_comment_button'])));
            }

            // Special handling for the array option
            if (isset($_POST['wpct_comment_list_enhancement']) && is_array($_POST['wpct_comment_list_enhancement'])) {
                $data = array_map('sanitize_text_field', wp_unslash($_POST['wpct_comment_list_enhancement']));
                update_option('wpct_comment_list_enhancement', $data);
            }
        }

        private function wpct_load_setting($name) {
            // Sanitize the input to ensure it's safe to use in the filename
            $name = sanitize_text_field($name);

            // Construct the filename dynamically
            $file = WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/settings/wpct_' . $name . '_settings.php';
            
            // Check if the file exists before including it
            if (file_exists($file)) {
                require_once($file); // Include the settings file dynamically
            } else {
                // Handle the case when the file doesn't exist
                // Optionally, show an error or log the issue
                echo 'Settings file not found: ' . esc_attr($file);
            }
        }

        private function wpct_setting_nonce_message() {
            wp_die(esc_html__('Invaled nonce', 'wp-comment-toolbox'), esc_html__('Access Denied', 'wp-comment-toolbox' ), ['response' => 403, 'back_link' => true]);
        }
    }
    new WP_Comment_Toolbox_Settings();
}
