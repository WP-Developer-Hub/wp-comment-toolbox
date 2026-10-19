<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

WPCT_Helper::wpct_select_box(
    'wpct_author_link_visibility',
    __('Author Link Visibility', 'wp-comment-toolbox'),
    'all', // Default value
    __('Control who can have a clickable author link in comments. Users with the ability to edit posts will not be affected.', 'wp-comment-toolbox'),
    array(
        'none' => __('Disable for all users', 'wp-comment-toolbox'),
        'all' => __('Enable for all users', 'wp-comment-toolbox'),
        'registered' => __('Enable for registered users only', 'wp-comment-toolbox'),
    )
);

WPCT_Helper::wpct_select_box(
    'wpct_author_link_type',
    __('Author Link Type', 'wp-comment-toolbox'),
    'external', // Default value
    __('Control whether the author link for users who can edit posts links to their website or the WordPress author page.', 'wp-comment-toolbox'),
    array(
        'internal' => __('Author Page', 'wp-comment-toolbox'),
        'external' => __('Author Website', 'wp-comment-toolbox'),
    )
);

WPCT_Helper::wpct_select_box(
    'wpct_format_comment_text',
    __('Format Comment Text', 'wp-comment-toolbox'),
    'auto', // Default value
    __('Choose how to format the comment text: Auto applies wpautop (paragraph tags), nl2br converts newlines to <br> tags, and None means no formatting is applied.', 'wp-comment-toolbox'),
    array(
        'auto' => __('Auto (wpautop)', 'wp-comment-toolbox'),
        'nl2br' => __('nl2br (Convert newlines to <br>)', 'wp-comment-toolbox'),
        'none' => __('None (No Formatting)', 'wp-comment-toolbox'),
    )
);

// Select box for enabling/disabling auto-linking of Twitter mentions
WPCT_Helper::wpct_select_box(
    'wpct_twitter_mentions_linking',
    __('Enable Twitter Mentions Linking', 'wp-comment-toolbox'),
    0,
    __('Enable or disable auto-linking of Twitter mentions (@username) in comment text.', 'wp-comment-toolbox'),
    array(
        0 => __('Disabled', 'wp-comment-toolbox'),
        1 => __('Enabled', 'wp-comment-toolbox'),
    )
);
