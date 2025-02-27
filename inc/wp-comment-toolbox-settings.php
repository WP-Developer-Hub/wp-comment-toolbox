<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WP_Comment_Toolbox_Settings_2_0 {
    public function __construct() {
        add_action('admin_menu', [$this, 'add_comment_settings_submenu']);
        add_action('admin_enqueue_scripts', [$this, 'enqueue_admin_styles']);
        require_once(WP_COMMENT_TOOLBOX_PLUGIN_DIR . 'inc/wp-comment-toolbox-helper.php');
    }

    public function enqueue_admin_styles($hook) {
        // Only enqueue on the comment settings page
        if ($hook === 'comments_page_wpct-comment-settings') {
            wp_enqueue_style('wpct-style', WP_COMMENT_TOOLBOX_PLUGIN_URL . 'css/wp-comment-toolbox-admin.css');
        }
    }

    public function add_comment_settings_submenu() {
        add_submenu_page(
                         'edit-comments.php',
                         'Comment Settings',
                         'Comment Settings',
                         'manage_options',
                         'wpct-comment-settings',
                         [$this, 'comment_settings_page']
                         );
    }

    public function comment_settings_page() {
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'spam_security';

        // Handle form submission
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['wpct_settings_nonce']) && wp_verify_nonce($_POST['wpct_settings_nonce'], 'wpct_save_settings')) {
            $this->save_settings();
        }

        echo '<div class="wrap">';
        echo '<h1>Comment Settings</h1>';

        // Start the form tag to allow submission
        echo '<form method="POST">';

        // Nonce field for security
        wp_nonce_field('wpct_save_settings', 'wpct_settings_nonce');

        // Display submit button before the settings
        WPCT_Helper::wpct_submit_button(0);

        // Tabs
        echo '<h2 class="nav-tab-wrapper">';
        $tabs = [
            'spam_security' => 'Spam & Security',
            'comment_list' => 'Comment List',
            'comment_form' => 'Comment Form',
            'extra' => 'Extra',
            'admin' => 'Admin',
        ];

        foreach ($tabs as $tab => $label) {
            echo '<a href="?page=wpct-comment-settings&tab=' . $tab . '" class="nav-tab ' . ($active_tab === $tab ? 'nav-tab-active' : '') . '">' . $label . '</a>';
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

        // Submit button after form fields
        WPCT_Helper::wpct_submit_button(1);

        echo '</form>'; // Close the form tag
        echo '</div>';
    }

    public function spam_security_settings() {
        $this->wpct_load_setting('spam_security');
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

    public function save_settings() {
        // Check nonce first for security
        if (!isset($_POST['wpct_settings_nonce']) || !wp_verify_nonce($_POST['wpct_settings_nonce'], 'wpct_save_settings')) {
            die('Permission denied');
        }

        // Get the active tab to determine which settings to save
        $active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'spam_security';

        // Save settings for each tab separately
        switch ($active_tab) {
            case 'spam_security':
                $this->save_spam_security_settings();
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
        update_option('wpct_comment_message_limit', $_POST['wpct_comment_message_limit']);
        update_option('wpct_disable_clickable_links', $_POST['wpct_disable_clickable_links']);
        update_option('wpct_enable_wp_kses_post', $_POST['wpct_enable_wp_kses_post']);
        update_option('wpct_enable_spam_protect', $_POST['wpct_enable_spam_protect']);
        update_option('wpct_submit_button_name', $_POST['wpct_submit_button_name']);
    }

    // Save settings specific to Comment List tab
    private function save_comment_list_settings() {
        update_option('wpct_author_link_visibility', $_POST['wpct_author_link_visibility']);
        update_option('wpct_author_link_type', $_POST['wpct_author_link_type']);
        update_option('wpct_format_comment_text', $_POST['wpct_format_comment_text']);
        update_option('wpct_twitter_mentions_linking', $_POST['wpct_twitter_mentions_linking']);
    }

    // Save settings specific to Comment Form tab
    private function save_comment_form_settings() {
        update_option('wpct_enabled_html5_validation', $_POST['wpct_enabled_html5_validation']);
        update_option('wpct_author_placeholder', $_POST['wpct_author_placeholder']);
        update_option('wpct_comment_textarea_placeholder', $_POST['wpct_comment_textarea_placeholder']);
        update_option('wpct_comment_textarea_height', $_POST['wpct_comment_textarea_height']);
        update_option('wpct_comment_notes_before', $_POST['wpct_comment_notes_before']);
        update_option('wpct_comment_notes_after', $_POST['wpct_comment_notes_after']);
        update_option('wpct_comment_form_layout', $_POST['wpct_comment_form_layout']);
        update_option('wpct_comment_form_cookies_msg', $_POST['wpct_comment_form_cookies_msg']);
    }

    // Save settings specific to Extra tab
    private function save_extra_settings() {
        update_option('wpct_toolbar_enabled', $_POST['wpct_toolbar_enabled']);
        update_option('wpct_toolbar_mode', $_POST['wpct_toolbar_mode']);
        update_option('wpct_character_count_enabled', $_POST['wpct_character_count_enabled']);
    }

    // Save settings specific to Admin tab
    private function save_admin_settings() {
        update_option('wpct_scam_filter_enabled', $_POST['wpct_scam_filter_enabled']);
        update_option('wpct_disable_comment_formatting', $_POST['wpct_disable_comment_formatting']);
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
            echo 'Settings file not found: ' . $file;
        }
    }
}

new WP_Comment_Toolbox_Settings_2_0();
