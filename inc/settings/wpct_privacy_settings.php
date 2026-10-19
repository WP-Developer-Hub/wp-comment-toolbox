<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

WPCT_Helper::wpct_select_box(
    'wpct_comment_cookie_lifetime',
    __('Comment Cookie Lifetime', 'wp-comment-toolbox'),
    259200,
    __('Choose how long (in seconds) comment author info is stored in cookies to pre-fill the comment form.', 'wp-comment-toolbox'),
    array(
        0 => __('No Day', 'wp-comment-toolbox'),
        86400   => __('1 Day', 'wp-comment-toolbox'),
        172800  => __('2 Days', 'wp-comment-toolbox'),
        259200  => __('3 Days', 'wp-comment-toolbox'),
        604800  => __('7 Days', 'wp-comment-toolbox'),
        31536000 => __('1 Year (default)', 'wp-comment-toolbox'),
    )
);

