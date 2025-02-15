<?php

class WP_Comment_Toolbox_Comment_list {
    public function __construct() {
        add_action('init', array($this, 'initialize_comment_filters'));
        add_action('wp_loaded', array($this, 'apply_comment_format_filters'), 20); // Apply filters after WP initialization
    }

    public function initialize_comment_filters() {
        // Initial filter application
        $this->apply_comment_format_filters();
    }

    public function apply_comment_format_filters() {
        // Get the selected format for comment text
        $comment_format = get_option('wpct_format_comment_text', 'auto'); // Default is 'auto'

        // Apply filters based on the selected format
        switch ($comment_format) {
            case 'nl2br':
                // Apply nl2br formatting
                remove_filter('comment_text', 'wpautop', 30); // Remove wpautop
                add_filter('comment_text', 'nl2br', 30); // Apply nl2br
                break;
            case 'none':
                // Remove both wpautop and nl2br
                remove_filter('comment_text', 'wpautop', 30); // Remove wpautop
                remove_filter('comment_text', 'nl2br', 30); // Remove nl2br
                break;
            case 'auto':
            default:
                break;
        }

        // Ensure other filters like author link visibility are also applied
        add_filter('get_comment_author_link', array($this, 'remove_comment_author_link_other'), 10, 3);
    }

    // Handle the author link visibility logic
    public function remove_comment_author_link_other($return, $author, $comment_id) {
        $disable_author_link = get_option('wpct_author_link_visibility', 'all');
        $author_link_type = get_option('wpct_author_link_type', 'external');

        $comment = get_comment($comment_id);
        if (!$comment) {
            return $return;
        }

        $user = get_user_by('id', $comment->user_id);
        $post = get_post($comment->comment_post_ID);

        // Handle author link visibility
        switch ($disable_author_link) {
            case 'none': // Disable for Everyone
                return $author;
            case 'registered': // Show for Registered Users Only
                if (!$user) {
                    return $author;
                }
                break;
            default:
                return $return;
                break;
        }

        // Handle author link type (external or internal)
        if ($user && user_can($user->ID, 'edit_others_posts')) {
            if ($author_link_type === 'internal') {
                return sprintf('<a href="%s">%s</a>', get_author_posts_url($user->ID), get_comment_author());
            }
        }
        return $return;
    }
}

new WP_Comment_Toolbox_Comment_list();
