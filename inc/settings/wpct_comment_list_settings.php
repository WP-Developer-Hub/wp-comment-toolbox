<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

WPCT_Helper::wpct_select_box(
    'wpct_author_link_visibility',
    __('Author Link Visibility', 'wpct'),
    'all', // Default value
    __('Control who can have a clickable author link in comments. Users with the ability to edit posts will not be affected.', 'wpct'),
    array(
        'none' => __('Disable for all users', 'wpct'),
        'all' => __('Enable for all users', 'wpct'),
        'registered' => __('Enable for registered users only', 'wpct'),
    )
);

WPCT_Helper::wpct_select_box(
    'wpct_author_link_type',
    __('Author Link Type', 'wpct'),
    'external', // Default value
    __('Control whether the author link for users who can edit posts links to their website or the WordPress author page.', 'wpct'),
    array(
        'internal' => __('Author Page', 'wpct'),
        'external' => __('Author Website', 'wpct'),
    )
);

WPCT_Helper::wpct_select_box(
    'wpct_format_comment_text',
    __('Format Comment Text', 'wpct'),
    'auto', // Default value
    __('Choose how to format the comment text: Auto applies wpautop (paragraph tags), nl2br converts newlines to <br> tags, and None means no formatting is applied.', 'wpct'),
    array(
        'auto' => __('Auto (wpautop)', 'wpct'),
        'nl2br' => __('nl2br (Convert newlines to <br>)', 'wpct'),
        'none' => __('None (No Formatting)', 'wpct'),
    )
);

// Select box for enabling/disabling auto-linking of Twitter mentions
WPCT_Helper::wpct_select_box(
    'wpct_twitter_mentions_linking',
    __('Enable Twitter Mentions Linking', 'wpct'),
    0,
    __('Enable or disable auto-linking of Twitter mentions (@username) in comment text.', 'wpct'),
    array(
        0 => __('Disabled', 'wpct'),
        1 => __('Enabled', 'wpct'),
    )
);
