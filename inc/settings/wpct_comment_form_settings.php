<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Enable HTML5 Validation
WPCT_Helper::wpct_select_box(
    'wpct_enabled_html5_validation',
    __('Enable HTML5 Validation', 'wpct'),
    0,
    __('Enable HTML5 validation for comment forms.', 'wpct'),
    [
        0 => __('Disabled', 'wpct'),
        1 => __('Enabled', 'wpct')
    ]
);

// Author Placeholder
WPCT_Helper::wpct_select_box(
    'wpct_author_placeholder',
    __('Author Placeholder', 'wpct'),
    'full_name',
    __('Select the placeholder type for the author input.', 'wpct'),
    [
        'full_name' => __('Full Name', 'wpct'),
        'username' => __('Username', 'wpct'),
        'both' => __('Both', 'wpct')
    ]
);

// Comment Textarea Placeholder
WPCT_Helper::wpct_input_field(
    'wpct_comment_textarea_placeholder',
    __('Comment Placeholder', 'wpct'),
    'text',
    '',
    __('Set a placeholder for the comment textarea. Leave empty to disable.', 'wpct')
);

// Comment Textarea Height
WPCT_Helper::wpct_input_field(
    'wpct_comment_textarea_height',
    __('Comment Textarea Height', 'wpct'),
    'number',
    150,
    __('Set the height of the comment textarea (in pixels).', 'wpct'),
    ['min' => 150, 'max' => 500]
);

// Comment Notes Before
WPCT_Helper::wpct_text_area(
    'wpct_comment_notes_before',
    __('Comment Notes Before', 'wpct'),
    '[default_msg]',
    __('Enter custom content to display before the comment form. HTML allowed.', 'wpct'),
    ['rows' => 8]
);

// Comment Notes After
WPCT_Helper::wpct_text_area(
    'wpct_comment_notes_after',
    __('Comment Notes After', 'wpct'),
    '',
    __('Enter custom content to display after the comment form. HTML allowed.', 'wpct'),
    ['rows' => 8]
);

// Comment Form Layout (Converted Setting)
WPCT_Helper::wpct_text_area(
    'wpct_comment_form_layout',
    __('Comment Form Layout', 'wpct'),
    '[author] 
[email]
[url]
[comment]
[cookies]',
    __('Define the structure of the comment form using placeholders: <strong>[author]</strong>, <strong>[email]</strong>, <strong>[url]</strong>, <strong>[comment]</strong>, <strong>[cookies]</strong>.', 'wpct'),
    ['rows' => 6]
);

// Comment Form Cookies Message (Converted Setting)
WPCT_Helper::wpct_text_area(
    'wpct_comment_form_cookies_msg',
    __('Comment Cookies Message', 'wpct'),
    '[cookies_msg]',
    __('Customize the cookies message for the comment form. <br> - Use <strong>[cookies_msg]</strong> to include the default cookies text.<br> - Use <strong>[privacy_policy_link]</strong> to insert a link to your privacy policy page.', 'wpct'),
    ['rows' => 6]
);
