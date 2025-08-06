<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

// Pass the commands to the wpct_test_code method
WPCT_Helper::wpct_test_code(
    'spam_test',
    'Spam Test Command',
    sprintf(
        'curl --data "author=Mr. SpamBot&email=spambot@example.com&comment=Hello from a spam bot! (random code = %s) &comment_post_ID=%d" %s/wp-comments-post.php',
        wp_generate_password(8, false),
        get_posts(array('post_type' => 'post', 'numberposts' => 1))[0]->ID,
        get_bloginfo('url')
    ),
    'Copy and paste this command into your terminal to test spam protection. Each test generates a unique comment.',
    array('rows' => '3', 'readonly' => 'readonly', 'style' => 'resize: none;')
);
