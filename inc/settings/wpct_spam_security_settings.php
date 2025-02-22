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
    __( 'When enabled, your WordPress site will be protected from spam comments sent via bots. It checks the referrer header to ensure that the comment is coming from a valid source. If spam protection is enabled and the referrer is missing, comments will be blocked, preventing bot submissions.', 'wpct' ),
    array(
        0 => __( 'No', 'wpct' ),
        1 => __( 'Yes', 'wpct' ),
    )
);

