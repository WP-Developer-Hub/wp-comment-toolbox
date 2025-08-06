<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

WPCT_Helper::wpct_select_box(
    'wpct_comment_cookie_lifetime',
    __('Comment Cookie Lifetime', 'wpct'),
    259200,
    __('Choose how long (in seconds) comment author info is stored in cookies to pre-fill the comment form.', 'wpct'),
    array(
        0 => __('No Day', 'wpct'),
        86400   => __('1 Day', 'wpct'),
        172800  => __('2 Days', 'wpct'),
        259200  => __('3 Days', 'wpct'),
        604800  => __('7 Days', 'wpct'),
        31536000 => __('1 Year (default)', 'wpct'),
    )
);

