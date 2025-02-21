<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Toolbar Toggle Setting
WPCT_Helper::wpct_select_box(
    'wpct_toolbar_enabled',
    __('Enable Quick Tags Toolbar', 'wpct'),
    0,
    __('When enabled, a Quick Tags toolbar will be available for easy text formatting in comment forms.', 'wpct'),
    array(
        0 => __('No', 'wpct'),
        1 => __('Yes', 'wpct'),
    )
);

// Toolbar Style Setting
WPCT_Helper::wpct_select_box(
    'wpct_toolbar_mode',
    __('Toolbar Style', 'wpct'),
    get_option('wpct_toolbar_mode', 'light'),
    __('Choose the style of the quick tags toolbar. Light mode has a bright background, while dark mode uses a darker background for better visibility in low-light environments.', 'wpct'),
    array(
        'light' => __('Light', 'wpct'),
        'dark'  => __('Dark', 'wpct'),
    )
);

// Character Count Toggle Setting
WPCT_Helper::wpct_select_box(
    'wpct_character_count_enabled',
    __('Enable Character Count', 'wpct'),
    0,
    __('When enabled, a character count will be displayed in the comment form to show how many characters the user has typed.', 'wpct'),
    array(
        0 => __('No', 'wpct'),
        1 => __('Yes', 'wpct'),
    )
);

