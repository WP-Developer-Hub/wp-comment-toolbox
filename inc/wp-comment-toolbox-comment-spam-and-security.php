<?php

class WP_Comment_Toolbox_Comment_Span_And_Security {
    public function __construct() {
        add_action('init', array($this, 'toggle_make_clickable'));
        add_filter('comment_text', array($this, 'strip_comment_links'));
        add_filter('preprocess_comment', array($this, 'limit_comment_length'));
        add_filter('pre_comment_content', array($this, 'strip_comment_links'));
    }

    public function toggle_make_clickable() {
        $disable_clickable = get_option('wpct_disable_clickable_links', '0');

        if ($disable_clickable === '1') {
            remove_filter('comment_text', 'make_clickable', 9);
        }
    }

    public function strip_comment_links($content) {
        $disable_clickable = get_option('wpct_disable_clickable_links', '1');
        $max_length = esc_html(get_option('wpct_comment_message_limit', 280));

        if ($disable_clickable === '1') {
            $content = preg_replace_callback('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', function($matches) {
                return filter_var($matches[2], FILTER_VALIDATE_URL) ? $matches[2] : $matches[1];
            }, $content);
        }
        return substr($content, 0, $max_length);
    }

    public function limit_comment_length($comment) {
        $max_length = esc_html(get_option('wpct_comment_message_limit', 280));
        if (strlen($comment['comment_content']) > $max_length) {
            wp_die('<strong>Warning:</strong> Please keep your comment under ' . $max_length . ' characters.', 'Comment Length Warning', array('response' => 500, 'back_link' => true));
        }
        return $comment;
    }
}

new WP_Comment_Toolbox_Comment_Span_And_Security();
