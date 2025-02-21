<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

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

// Toggle Scam Filter Setting
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
