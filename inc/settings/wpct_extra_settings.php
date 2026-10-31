<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Toolbar Toggle Setting
WPCT_Helper::wpct_select_box(
    'wpct_toolbar_enabled',
    __('Enable Quick Tags Toolbar', 'wp-comment-toolbox'),
    0,
    __('When enabled, a Quick Tags toolbar will be available for easy text formatting in comment forms.', 'wp-comment-toolbox'),
    array(
        0 => __('No', 'wp-comment-toolbox'),
        1 => __('Yes', 'wp-comment-toolbox'),
    )
);

// Toolbar Style Setting
WPCT_Helper::wpct_select_box(
    'wpct_toolbar_mode',
    __('Toolbar Style', 'wp-comment-toolbox'),
    get_option('wpct_toolbar_mode', 'light'),
    __('Choose the style of the quick tags toolbar. Light mode has a bright background, while dark mode uses a darker background for better visibility in low-light environments.', 'wp-comment-toolbox'),
    array(
        'light' => __('Light', 'wp-comment-toolbox'),
        'dark'  => __('Dark', 'wp-comment-toolbox'),
    )
);

// Character Count Toggle Setting
WPCT_Helper::wpct_select_box(
    'wpct_character_count_enabled',
    __('Enable Character Count', 'wp-comment-toolbox'),
    0,
    __('When enabled, a character count will be displayed in the comment form to show how many characters the user has typed.', 'wp-comment-toolbox'),
    array(
        0 => __('No', 'wp-comment-toolbox'),
        1 => __('Yes', 'wp-comment-toolbox'),
    )
);

WPCT_Helper::wpct_checkbox_group(
    'wpct_public_commenting_toggles',
    sprintf(
        /* translators: %s: 'Enable' or 'Disable' state text depending on comment registration */
        __('%1$s Public Commenting', 'wp-comment-toolbox'),
        (get_option('comment_registration') ? 'Enable' : 'Disable')
    ),
    sprintf(
        /* translators: 1: Discussion settings link opening tag, 2: Discussion settings link closing tag */
        __('When enabled, user can leave comment on select post types with no needed to login at all.<br>This setting depends on the Users must be registered and logged in to comment %1$s. Toggling that option will invert this setting.', 'wp-comment-toolbox'),
        WPCT_Helper::wpct_add_wp_setting_link('options-discussion.php#comment_registration', 'Discussion')
    ),
    WPCT_Helper::wpct_get_post_type()
);
