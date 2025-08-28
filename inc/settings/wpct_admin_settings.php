<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Show "Block IP" Button in Comment List
WPCT_Helper::wpct_select_box(
    'wpct_show_block_ip_action',
    __('Show Block IP Actions', 'wpct'),
    1,
    __('Enable this option to show the "Block IP" action to the row actions in the comment list, allowing you to quickly add comment author IP addresses to the block list.', 'wpct'),
    array(
        0 => __('No', 'wpct'),
        1 => __('Yes', 'wpct'),
    )
);

// Enable Nuke Button
WPCT_Helper::wpct_select_box(
    'wpct_enable_nuke_all_sus_comment_button',
    __('Enable all sus comments button', 'wpct'),
    0,
    sprintf(
        __('Enable this option to add a "Nuke all sus comments" feature which checks all comments for words listed in moderation_keys & or links only when comment_max_links is set and manual comment approval is enabled in %1$s. When enabled, a new "Nuke all sus comments" button will appear next to the filter by type apply button in the comment list admin screen to quickly scan and nuke suspicious comments in one shot plus add the comment authors IP Address to a block list.', 'wpct'),
        WPCT_Helper::wpct_add_wp_setting_link('options-discussion.php#comment-moderation', 'Discussion')
    ),
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

