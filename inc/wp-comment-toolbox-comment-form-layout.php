<?php

class WP_Comment_Toolbox_Comment_Form_Layout {
    public function __construct() {
        add_action('init', array($this, 'toggle_make_clickable'));
        add_filter('comment_text', array($this, 'strip_comment_links'));
        add_filter('preprocess_comment', array($this, 'limit_comment_length'));
        add_filter('pre_comment_content', array($this, 'strip_comment_links'));
        add_filter('comment_form_fields', array($this, 'reorder_comment_form_fields'));
        add_filter('get_comment_author_link', array($this, 'remove_comment_author_link_other'), 10, 3);
    }

    public function toggle_make_clickable() {
        $disable_clickable = get_option('wpct_disable_clickable_links', '0');

        if ($disable_clickable === '1') {
            remove_filter('comment_text', 'make_clickable', 9);
        }
    }

    public function strip_comment_links($content) {
        $disable_clickable = get_option('wpct_disable_clickable_links', '1');

        if ($disable_clickable === '1') {
            $content = preg_replace_callback('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', function($matches) {
                return filter_var($matches[2], FILTER_VALIDATE_URL) ? $matches[2] : $matches[1];
            }, $content);
        }

        return $content;
    }

    public function remove_comment_author_link_other($return, $author, $comment_id ) {
        $comment_form_layout = get_option('wpct_comment_form_layout', '[author] [email] [url] [comment] [cookies]');

        $comment = get_comment($comment_id);

        // Get the user associated with the comment
        $user = get_user_by('id', $comment->user_id);

        if (!str_contains($comment_form_layout, '[url]')) {
            // Check if the comment author has the capability to edit the post
            if (!$user && !user_can($user->ID, 'edit_others_posts')) {
                $return = $author;
            }
        }
        return $return;
    }

    public function reorder_comment_form_fields($fields) {
        $name_or_username = get_option('wpct_author_placeholder', 'full_name');
        $comment_form_layout = get_option('wpct_comment_form_layout', '[author] [email] [url] [comment] [cookies]');
        $custom_cookies_msg = esc_html(get_option('wpct_comment_form_cookies_msg'), '');
        $privacy_policy_link = '<a href="' . esc_url(get_privacy_policy_url()) . '" target="_blank">' . __('Privacy Policy', 'wpct') . '</a>';

        preg_match_all('/\[(.*?)\]/', $comment_form_layout, $matches);
        $keys = $matches[1] ?? [];

        $ordered_fields = [];
        foreach ($keys as $key) {
            if (isset($fields[$key])) {
                $ordered_fields[$key] = $fields[$key];
            }
        }

        foreach ($fields as $key => $value) {
            if (!in_array($key, $keys)) {
                unset($fields[$key]);
            }
        }

        if (isset($ordered_fields['author'])) {
            $placeholders = [
                'full_name' => __('Please enter your full name', 'wpct'),
                'username' => __('Please enter your username', 'wpct'),
                'both' => __('Please enter your full name or username', 'wpct')
            ];

            // Set placeholder based on the selected option
            $placeholder = $placeholders[$name_or_username] ?? $placeholders['full_name']; // Default translation

            // Update the placeholder in the form field
            $ordered_fields['author'] = preg_replace('/\binput/', 'input placeholder="' . esc_attr($placeholder) . '"', $ordered_fields['author'], 1);
        }

        if (isset($ordered_fields['email'])) {
            $ordered_fields['email'] = preg_replace('/\binput/', 'input placeholder="username@emailprovider.com"', $ordered_fields['email'], 1);
        }

        if (isset($ordered_fields['url'])) {
            $ordered_fields['url'] = preg_replace('/\binput/', 'input placeholder="' . esc_attr(get_bloginfo("url") . '/') . '"', $ordered_fields['url'], 1);
        }

        if (isset($ordered_fields['cookies'])) {
            $commenter = wp_get_current_commenter();
            $consent = empty($commenter['comment_author_email']) ? '' : ' checked="checked"';

            $cookies_msg = '';

            $fields_set = [
                'comment_author' => isset($ordered_fields['author']),
                'comment_author_email' => isset($ordered_fields['email']),
                'comment_author_url' => isset($ordered_fields['url'])
            ];

            if ($fields_set['comment_author'] && $fields_set['comment_author_email'] && $fields_set['comment_author_url']) {
                $cookies_msg = __('Save my name, email, and website in this browser for the next time I comment.', 'wpct');
            } elseif ($fields_set['comment_author'] && $fields_set['comment_author_email']) {
                $cookies_msg = __('Save my name, and email in this browser for the next time I comment.', 'wpct');
            } elseif ($fields_set['comment_author'] && $fields_set['comment_author_url']) {
                $cookies_msg = __('Save my name, and website in this browser for the next time I comment.', 'wpct');
            } elseif ($fields_set['comment_author_email'] && $fields_set['comment_author_url']) {
                $cookies_msg = __('Save my email and website in this browser for the next time I comment.', 'wpct');
            } elseif ($fields_set['comment_author']) {
                $cookies_msg = __('Save my name in this browser for the next time I comment.', 'wpct');
            } elseif ($fields_set['comment_author_email']) {
                $cookies_msg = __('Save my email in this browser for the next time I comment.', 'wpct');
            } elseif ($fields_set['comment_author_url']) {
                $cookies_msg = __('Save my website in this browser for the next time I comment.', 'wpct');
            }

            if ($custom_cookies_msg) {
                $custom_cookies_msg = str_replace('[cookies_msg]', $cookies_msg, $custom_cookies_msg);
                $custom_cookies_msg = str_replace('[privacy_policy_link]', $privacy_policy_link, $custom_cookies_msg);
            } else {
                $custom_cookies_msg = $cookies_msg . $privacy_policy_link;
            }

            $ordered_fields['cookies'] = '<p class="comment-form-cookies-consent"><label for="wp-comment-cookies-consent"><input id="wp-comment-cookies-consent" name="wp-comment-cookies-consent" type="checkbox" value="yes"' . $consent . '>' . $custom_cookies_msg . '</label></p>';
        }
        return $ordered_fields;
    }

    public function limit_comment_length($comment) {
        $max_length = esc_html(get_option('wpct_comment_message_limit', 280));
        if (strlen($comment['comment_content']) > $max_length) {
            wp_die('<strong>Warning:</strong> Please keep your comment under ' . $max_length . ' characters.', 'Comment Length Warning', array('response' => 500, 'back_link' => true));
        }
        return $comment;
    }
}

new WP_Comment_Toolbox_Comment_Form_Layout();
