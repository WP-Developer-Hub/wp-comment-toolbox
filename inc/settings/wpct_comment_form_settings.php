<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enable HTML5 Validation
WPCT_Helper::wpct_select_box(
    'wpct_enabled_html5_validation',
    __('Enable HTML5 Validation', 'wp-comment-toolbox'),
    0,
    __('Enable HTML5 validation for comment forms.', 'wp-comment-toolbox'),
    [
        0 => __('Disabled', 'wp-comment-toolbox'),
        1 => __('Enabled', 'wp-comment-toolbox')
    ]
);

// Author Placeholder
WPCT_Helper::wpct_select_box(
    'wpct_author_placeholder',
    __('Author Placeholder', 'wp-comment-toolbox'),
    'full_name',
    __('Select the placeholder type for the author input.', 'wp-comment-toolbox'),
    [
        'full_name' => __('Full Name', 'wp-comment-toolbox'),
        'username' => __('Username', 'wp-comment-toolbox'),
        'both' => __('Both', 'wp-comment-toolbox')
    ]
);

// Comment Textarea Placeholder
WPCT_Helper::wpct_input_field(
    'wpct_comment_textarea_placeholder',
    __('Comment Placeholder', 'wp-comment-toolbox'),
    'text',
    '',
    __('Set a placeholder for the comment textarea. Leave empty to disable.', 'wp-comment-toolbox')
);

// Comment Textarea Height
WPCT_Helper::wpct_input_field(
    'wpct_comment_textarea_height',
    __('Comment Textarea Height', 'wp-comment-toolbox'),
    'number',
    150,
    __('Set the height of the comment textarea (in pixels).', 'wp-comment-toolbox'),
    ['min' => 150, 'max' => 500]
);

// Comment Notes Before
WPCT_Helper::wpct_text_area(
    'wpct_comment_notes_before',
    __('Comment Notes Before', 'wp-comment-toolbox'),
    '[default_msg]',
    __('Enter custom content to display before the comment form. HTML allowed.', 'wp-comment-toolbox'),
    ['rows' => 8]
);

// Comment Notes After
WPCT_Helper::wpct_text_area(
    'wpct_comment_notes_after',
    __('Comment Notes After', 'wp-comment-toolbox'),
    '',
    __('Enter custom content to display after the comment form. HTML allowed.', 'wp-comment-toolbox'),
    ['rows' => 8]
);

// Comment Form Layout (Converted Setting)
WPCT_Helper::wpct_text_area(
    'wpct_comment_form_layout',
    __('Comment Form Layout', 'wp-comment-toolbox'),
    '[author] 
[email]
[url]
[comment]
[cookies]',
    __('Define the structure of the comment form using placeholders: <strong>[author]</strong>, <strong>[email]</strong>, <strong>[url]</strong>, <strong>[comment]</strong>, <strong>[cookies]</strong>.', 'wp-comment-toolbox'),
    ['rows' => 6]
);

// Comment Form Cookies Message (Converted Setting)
WPCT_Helper::wpct_text_area(
    'wpct_comment_form_cookies_msg',
    __('Comment Cookies Message', 'wp-comment-toolbox'),
    '[cookies_msg]',
    sprintf(
        /* translators: 1: opening anchor tag, 2: closing anchor tag, 3: strong open, 4: strong close */
        __('Customize the cookies message for the comment form. <br> - Use <strong>[cookies_msg]</strong> to include the default cookies text.<br> - Use <strong>[privacy_policy_link]</strong> to insert a link to your privacy policy page.<br>This setting depends on the Show comments cookies opt-in checkbox option in %1$s. Disabling that option will disable this setting.', 'wp-comment-toolbox'),
        WPCT_Helper::wpct_add_wp_setting_link('options-discussion.php#other-comment-settings', 'Discussion')
    ),
    array_merge(
        ['rows' => 6],
        get_option('show_comments_cookies_opt_in', true) ? [] : ['disabled' => 'disabled']
    )
);
