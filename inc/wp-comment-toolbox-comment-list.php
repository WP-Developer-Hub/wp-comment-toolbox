<?php

class WP_Comment_Toolbox_Comment_list {
    public function __construct() {
        add_filter('get_comment_author_link', array($this, 'remove_comment_author_link_other'), 10, 3);
    }

    public function remove_comment_author_link_other($return, $author, $comment_id) {
        $disable_author_link = get_option('wpct_author_link_visibility', 'all'); // Default: Show for everyone
        $author_link_type = get_option('wpct_author_link_type', 'external'); // Default: External URL

        $comment = get_comment($comment_id);
        if (!$comment) {
            return $return; // Prevent errors
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
