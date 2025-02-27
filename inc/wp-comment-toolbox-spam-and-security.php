<?php
if (!defined('ABSPATH')) {
    exit;
}

class WP_Comment_Toolbox_Span_And_Security {
    public function __construct() {
        add_action('init', [$this, 'toggle_make_clickable']);
        add_action('pre_comment_approved', [$this, 'check_honeypot']);
        add_action('check_comment_flood', [$this, 'check_referrer'], 5);
        add_filter('preprocess_comment', [$this, 'limit_comment_length']);
        add_action('pre_comment_on_post', [$this, 'verify_wp_nonce_field']);
        add_filter('comment_text', [$this, 'strip_bad_html_form_comment'], 9);
        add_action('comment_form', [$this, 'add_wp_nonce_and_huonoy_pot_field']);
        add_filter('pre_comment_content', [$this, 'strip_bad_html_form_comment'], 9);
    }

    public function toggle_make_clickable() {
        if (get_option('wpct_disable_clickable_links', 0)) {
            remove_filter('comment_text', 'make_clickable', 9);
        }
    }

    public function add_wp_nonce_and_huonoy_pot_field() {
        if (get_option('wpct_enable_spam_protect', 0)) {
            $textarea_name = uniqid('wpct_comment_honeypot_');

            wp_nonce_field('comment_nonce', 'wpct_comment_nonce');

            // Set a cookie for the textarea name (expires in 1 hour)
            setcookie('wpct_comment_honeypot_name', $textarea_name, time() + 3600, COOKIEPATH, COOKIE_DOMAIN);

            // Output the hidden textarea field
            echo '<p style="display:none">';
            echo '<textarea name="' . esc_attr($textarea_name) . '" cols="100%" rows="10"></textarea>';
            echo '<label for="' . esc_attr($textarea_name) . '">' . esc_html__('If you are a human, do not fill in this field.', 'wpct') . '</label>';
            echo '</p>';
        }
    }

    public function verify_wp_nonce_field() {
        if (get_option('wpct_enable_spam_protect', 0)) {
            // Check if the HTTP_REFERER header is set and non-empty
            if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == "") {
                // Handle the absence of referer header
                $is_curl_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false);
                $is_wget_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Wget') !== false);

                // Store the error message for invalid referer
                $error_message = __('Error: No referer header or invalid referer.', 'wpct');

                // Handle cURL/Wget requests differently
                if ($is_curl_request || $is_wget_request) {
                    header('HTTP/1.1 403 Forbidden');
                    header('Content-Type: text/plain; charset=UTF-8');
                    die($error_message);
                } else {
                    wp_die($error_message, 'Security Check', array('response' => 403));
                }
            }

            // After checking referer, check for nonce
            if (!isset($_POST['wpct_comment_nonce']) || !wp_verify_nonce($_POST['wpct_comment_nonce'], 'comment_nonce')) {
                // Handle nonce verification failure for cURL/Wget requests
                $is_curl_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false);
                $is_wget_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Wget') !== false);

                // Store the error message for nonce failure
                $error_message = __('Error: Nonce verification failed.', 'wpct');

                // Handle cURL/Wget requests differently
                if ($is_curl_request || $is_wget_request) {
                    header('HTTP/1.1 403 Forbidden');
                    header('Content-Type: text/plain; charset=UTF-8');
                    die($error_message);
                } else {
                    wp_die($error_message, 'Security Check', array('response' => 403));
                }
            }
        }
    }

    public function strip_bad_html_form_comment($content) {
        if (get_option('wpct_disable_clickable_links', 0)) {
            $content = preg_replace_callback('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', function($matches) {
                return filter_var($matches[1], FILTER_VALIDATE_URL) ? $matches[1] : $matches[2];
            }, $content);
        }

        if(get_option('wpct_enable_wp_kses_post', 0)){
            $content = wp_kses_post($content);
        }

        return $content;
    }

    public function limit_comment_length($comment) {
        $max_length = esc_html(get_option('wpct_comment_message_limit', 280));
        if (strlen($comment['comment_content']) > $max_length) {
            wp_die('<strong>Warning:</strong> Please keep your comment under ' . $max_length . ' characters.', 'Comment Length Warning', array('response' => 500, 'back_link' => true));
        }
        return $comment;
    }

    public function check_referrer() {
        if (get_option('wpct_enable_spam_protect', 0)) {
            if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == "") {
                // Check for cURL and wget in the User-Agent header
                $is_curl_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false);
                $is_wget_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Wget') !== false);

                // Store the error message in a variable for easier customization
                $error_message = __('Please enable referrers in your browser, or, if you\'re a spammer, bugger off!', 'wpct');

                // Check if it's a cURL or wget request
                if ($is_curl_request || $is_wget_request) {
                    header('HTTP/1.1 403 Forbidden');
                    header('Content-Type: text/plain; charset=UTF-8');
                    die($error_message);
                } else {
                    wp_die($error_message, 'Security Check', array('response' => 403));
                }
            }
        }
    }

    public function check_honeypot($approved) {
        // Retrieve the textarea name from the cookie
        if (isset($_COOKIE['wpct_comment_honeypot_name'])) {
            // Sanitize the cookie value to prevent potential issues
            $textarea_name = sanitize_text_field($_COOKIE['wpct_comment_honeypot_name']); // Get the value of textarea name from the cookie
            
            // Get the submit button name from settings
            $submit_name = sanitize_text_field(get_option('wpct_submit_button_name'));
            
            // Check if the hidden textarea field is filled out
            $textarea_filled_out = !empty($_POST[$textarea_name]);
            
            // Check if the submit button name is missing
            $submit_name_missing = !empty($submit_name) && empty($_POST[$submit_name]);
            
            if ($textarea_filled_out || $submit_name_missing) {
                // Mark as spam if the honeypot field is filled or the submit button is missing
                $approved = 'spam';
            }

            // Expire the cookie after the check is done
            setcookie('wpct_comment_honeypot_name', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN); // Expire the cookie by setting a past time
        }
        return $approved;
    }
}
new WP_Comment_Toolbox_Span_And_Security();
