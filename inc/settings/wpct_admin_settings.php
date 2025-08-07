<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Show "Block IP" Button in Comment List
WPCT_Helper::wpct_select_box(
    'wpct_show_block_ip_action',
    __('Show Block IP Button', 'wpct'),
    1,
    __('Enable this option to show a "Block IP" button to the row actions in the comment list, allowing you to quickly add comment author IP addresses to the block list.', 'wpct'),
    array(
        0 => __('No', 'wpct'),
        1 => __('Yes', 'wpct'),
    )
);

// Toggle Scam Filter Setting
WPCT_Helper::wpct_select_box(
    'wpct_scam_filter_enabled',
    __('Enable Scam Filter', 'wpct'),
    0,
    __('When enabled, you will be able to see comments with links, which might indicate spam. This feature is experimental.', 'wpct'),
    array(
        0 => __('No', 'wpct'),
        1 => __('Yes', 'wpct'),
    )
);

// Toggle Formatting Setting
WPCT_Helper::wpct_select_box(
    'wpct_disable_comment_formatting',
    __('Disable Formating', 'wpct'),
    0,
   __('When enabled, all HTML tags are removed from comments in the admin area, reducing formatting for less distraction.', 'wpct'),
    array(
        0 => __('No', 'wpct'),
        1 => __('Yes', 'wpct'),
    )
);

