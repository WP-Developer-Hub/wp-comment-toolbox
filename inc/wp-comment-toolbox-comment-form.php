<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

class WP_Comment_Toolbox_Comment_Form {
    public function __construct() {
        add_filter('wp_head', [$this, 'wpct_add_custom_comment_css']);
        add_filter('wp_footer', [$this, 'toggle_html5_comment_form_validation']);
        add_filter('comment_form_fields', [$this, 'reorder_comment_form_fields']);
        add_filter('comment_form_defaults', [$this, 'override_comment_form_defaults']);
    }

    public function wpct_add_custom_comment_css() {
        $height = get_option('wpct_comment_textarea_height', 150); // Default to 150px
        ?>
        <style>.comment-form textarea { min-height: <?php echo esc_attr($height); ?>px !important; }</style>
        <?php
    }

    public function toggle_html5_comment_form_validation() {
        // Only remove novalidate if the validation is enabled
        if (get_option('wpct_enabled_html5_validation', '0') === '1') {
            if (comments_open() && current_theme_supports('html5')) {
                echo '<script>document.getElementById("commentform").removeAttribute("novalidate");</script>' . PHP_EOL;
            }
        }
    }

    public function reorder_comment_form_fields($fields) {
        $max_length = esc_html(get_option('wpct_comment_message_limit', 280));
        $name_or_username = get_option('wpct_author_placeholder', 'full_name');
        $custom_cookies_msg = esc_html(get_option('wpct_comment_form_cookies_msg'), '');
        $comment_textarea_placeholder = get_option('wpct_comment_textarea_placeholder');
        $comment_form_layout = get_option('wpct_comment_form_layout', '[author] [email] [url] [comment] [cookies]');
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

        if (isset($ordered_fields['comment'])) {
            $ordered_fields['comment'] = preg_replace('/\bmaxlength="65525"/', 'maxlength="' . esc_attr($max_length) . '"', $ordered_fields['comment'], 1);

            if(!empty($comment_textarea_placeholder)){
                $ordered_fields['comment'] = preg_replace('/\btextarea/', 'textarea placeholder="' . esc_attr($comment_textarea_placeholder) . '"', $ordered_fields['comment'], 1);
            }
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

    public function override_comment_form_defaults($defaults) {
        // Retrieve custom comment notes from the options
        $comment_notes_after = get_option('wpct_comment_notes_after', '');
        $comment_notes_before = get_option('wpct_comment_notes_before', '[default_msg]');

        // Handle comment_notes_after
        if (empty($comment_notes_after)) {
            $defaults['comment_notes_after'] = ''; // Set to empty if no custom note is provided
        } else {
            $comment_notes_after = str_replace('[required]', '<span class="required">*</span>', $comment_notes_after);
            $defaults['comment_notes_after'] = '<p class="comment-notes">' . $comment_notes_after . '</p>';
        }

        // Handle comment_notes_before
        if (empty($comment_notes_before)) {
            $defaults['comment_notes_before'] = ''; // Set to empty if no custom note is provided
        } else {
            // Only wrap comment_notes_before if [default_msg] is not present
            if (!str_contains($comment_notes_before, '[default_msg]')) {
                $comment_notes_before = str_replace('[required]', '<span class="required">*</span>', $comment_notes_before);
                $defaults['comment_notes_before'] = '<p class="comment-notes">' . $comment_notes_before . '</p>';
            }
        }
        return $defaults;
    }
}

new WP_Comment_Toolbox_Comment_Form();
