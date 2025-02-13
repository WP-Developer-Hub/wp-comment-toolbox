<?php

class WP_Comment_Toolbox_Comment_Form {
    public function __construct() {
        add_filter('comment_form_fields', array($this, 'reorder_comment_form_fields'));
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
}

new WP_Comment_Toolbox_Comment_Form();
