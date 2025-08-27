<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Comment Character Limit
WPCT_Helper::wpct_input_field(
    'wpct_comment_message_limit',
    __( 'Comment Character Limit', 'wpct' ),
    'number',
    280,
    __( 'Set the maximum number of characters allowed in the comment text. This helps prevent overly long comments and spammy content.', 'wpct' ),
    array( 'min' => 240, 'max' => 480, 'inputmode' => 'numeric', 'pattern' => '[0-9]*', 'placeholder' => '280' )
);

WPCT_Helper::wpct_select_box(
    'wpct_comment_flood_delay',
    __('Comment Flood Delay', 'wpct'),
    '15',
    sprintf(
        __('Set the minimum wait time between user comments. <br>This setting depends on the %1$sShow comments cookies opt-in checkbox%2$s option in %3$sSettings → Discussion%4$s. Disabling that option will disable this setting.', 'wpct'),
        '<a href="' . esc_url(admin_url('options-discussion.php#other-comment-settings')) . '" target="_blank" rel="noopener noreferrer">',
        '</a>',
        '<strong>',
        '</strong>'
    ),
    array(
        '15' => __('15 seconds', 'wpct'),
        '30' => __('30 seconds', 'wpct'),
        '45' => __('45 seconds', 'wpct'),
        '60' => __('1 minute', 'wpct'),
        '120' => __('2 minutes', 'wpct'),
    ),
    get_option('show_comments_cookies_opt_in', true) ? [] : ['disabled' => 'disabled']
);

// Disable Clickable Links in Comments
WPCT_Helper::wpct_select_box(
    'wpct_disable_clickable_links',
    __( 'Disable clickable links', 'wpct' ),
    0,
    __( 'When enabled, links in comments will not be clickable. This can help reduce spam and unwanted external links.', 'wpct' ),
    array(
        0 => __( 'No', 'wpct' ),
        1 => __( 'Yes', 'wpct' ),
    )
);

// Enable WP Kses Post
WPCT_Helper::wpct_select_box(
    'wpct_enable_wp_kses_post',
    __( 'Filter HTML', 'wpct' ),
    0,
    __( 'When enabled, only safe HTML tags such as links, blockquotes, and basic formatting will be allowed in comments. This helps prevent malicious code from being rendered for users with higher roles, while keeping basic styling.', 'wpct' ),
    array(
        0 => __( 'No', 'wpct' ),
        1 => __( 'Yes', 'wpct' ),
    )
);

// Enable Spam Protection
WPCT_Helper::wpct_select_box(
    'wpct_enable_spam_protect',
    __( 'Enable Spam Protection', 'wpct' ),
    0,
    __( 'When enabled, your WordPress site will be protected from spam comments sent via bots. It checks the referrer header to ensure that the comment is coming from a valid source. If spam protection is enabled and the referrer is missing, comments will be blocked, preventing bot submissions. Additionally, a honeypot mechanism will be added to the comment form. This includes a hidden textarea field that bots are likely to fill out, and if detected, the comment will be marked as spam. You can also customize the submit button name used for honeypot checks.', 'wpct' ),
    array(
        0 => __( 'No', 'wpct' ),
        1 => __( 'Yes', 'wpct' ),
    )
);

// Enable Spam Filter
WPCT_Helper::wpct_select_box(
    'wpct_spam_filter_enabled',
    __('Enable Spam Filter', 'wpct'),
    0,
    sprintf(
        __('When enabled, comments containing words from the moderation_keys list will be flagged if that list is not empty. Comments with links will only be flagged if the comment_max_links option is set to a value greater than zero and the number of links meets or exceeds that value. Additionally, the Comment must be manually approved setting is required. All three settings must be enabled and configured correctly in %3$sSettings → %1$sDiscussion%2$s%4$s for full spam filtering effect.%4$s You can then filter comments by this flagged type in the WordPress admin to easily find and moderate potential spam.', 'wpct'),
        '<a href="' . esc_url(admin_url('options-discussion.php#comment-moderation')) . '" target="_blank" rel="noopener noreferrer">',
        '</a>',
        '<strong>',
        '</strong>'
    ),
    array(
        0 => __('No', 'wpct'),
        1 => __('Yes', 'wpct'),
    )
);

// Submit Button Field Name
WPCT_Helper::wpct_input_field(
    'wpct_submit_button_name',
    __( 'Submit Button Name', 'wpct' ),
    'text',
    'submit',
    __( 'This name will be used when performing the honeypot check. Set the name for the submit button. If your theme changes the default name for the submit button, you can inspect the HTML of the comment form or consult your theme\'s documentation to find the appropriate name attribute for the submit button. Typically, it is something like "submit" or "comment-submit".', 'wpct' ),
    array( 'placeholder' => 'Submit Button Name Go Here' )
);

// Enable Math Captcha
WPCT_Helper::wpct_select_box(
    'wpct_enable_math_captcha',
    __( 'Enable Math Captcha', 'wpct' ),
    0,
    __('When enabled, the user will have to solve a math question to submit a comment. This helps prevent spam and automated submissions from bots.', 'wpct'),
    array(
        0 => __('No', 'wpct'),
        1 => __('Yes', 'wpct'),
    )
);

// Math Captcha Difficulty Level
WPCT_Helper::wpct_select_box(
    'wpct_math_captcha_level',
    __('Math Captcha Difficulty Level', 'wpct'),
    'easy',
    __('Choose from four different difficulty levels.', 'wpct'),
    array(
        'easy' => __('Easy (1-10)', 'wpct'),
        'medium' => __('Medium (1-15)', 'wpct'),
        'hard' => __('Hard (1-20)', 'wpct'),
        'extreme' => __('Extreme (1-50)', 'wpct'),
    )
);

