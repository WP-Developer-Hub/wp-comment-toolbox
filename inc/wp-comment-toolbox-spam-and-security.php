<?php
if (!defined('ABSPATH')) {
    exit;
}

class WP_Comment_Toolbox_Span_And_Security {
    public function __construct() {
        add_action('init', [$this, 'toggle_make_clickable']);
        add_action('comment_form', [$this, 'add_wp_nonce_field']);
        add_action('check_comment_flood', [$this, 'check_referrer'], 5);
        add_filter('preprocess_comment', [$this, 'limit_comment_length']);
        add_action('pre_comment_on_post', [$this, 'verify_wp_nonce_field']);
        add_filter('comment_text', [$this, 'strip_bad_html_form_comment'], 9);
        add_filter('pre_comment_content', [$this, 'strip_bad_html_form_comment'], 9);
    }

    public function toggle_make_clickable() {
        if (get_option('wpct_disable_clickable_links', 0)) {
            remove_filter('comment_text', 'make_clickable', 9);
        }
    }

    public function add_wp_nonce_field() {
        if (get_option('wpct_enable_spam_protect', 0)) {
            wp_nonce_field('comment_nonce', 'wpct_comment_nonce');
        }
    }

    public function verify_wp_nonce_field() {
        if (get_option('wpct_enable_spam_protect', 0)) {
            if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == "") {
                if (!isset($_POST['wpct_comment_nonce']) || !wp_verify_nonce($_POST['wpct_comment_nonce'], 'comment_nonce')) {
                    // Check for cURL and wget in the User-Agent header
                    $is_curl_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false);
                    $is_wget_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Wget') !== false);

                    // Store the error message in a variable
                    $error_message = __('Error: Nonce verification failed.', 'wpct');

                    // Send response: For cURL/automated requests, output plain text
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
}

new WP_Comment_Toolbox_Span_And_Security();
