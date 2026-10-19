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

