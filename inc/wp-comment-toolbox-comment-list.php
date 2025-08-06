<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WP_Comment_Toolbox_Comment_list')) {
    class WP_Comment_Toolbox_Comment_list {
        public function __construct() {
            add_action('init', [$this, 'initialize_comment_filters']);
            add_action('wp_loaded', [$this, 'apply_comment_format_filters'], 20);
            add_action('comment_text', [$this, 'auto_link_twitter_mentions'], 99);
        }

        public function initialize_comment_filters() {
            $this->apply_comment_format_filters();
        }

        public function apply_comment_format_filters() {
            // Get the selected format for comment text
            $comment_format = get_option('wpct_format_comment_text', 'auto'); // Default is 'auto'

            // Apply filters based on the selected format
            switch ($comment_format) {
                case 'nl2br':
                    // Swap wpautop for nl2br formatting
                    remove_filter('comment_text', 'wpautop', 30);
                    add_filter('comment_text', 'nl2br', 30);
                    break;
                case 'none':
                    // Remove both wpautop and nl2br
                    remove_filter('comment_text', 'wpautop', 30);
                    remove_filter('comment_text', 'nl2br', 30);
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

        // Function to automatically link Twitter mentions in comments
        public function auto_link_twitter_mentions($content) {
            if (get_option('wpct_twitter_mentions_linking', 0)) {
                return preg_replace_callback(
                    '/(?<!\S)@([0-9a-zA-Z_]+)/',
                    function ($matches) {
                        return '<a href="https://twitter.com/' . esc_attr($matches[1]) . '" target="_blank" rel="nofollow">@' . esc_html($matches[1]) . '</a>';
                    },
                    $content
                );
            }
            return $content;
        }
    }
    new WP_Comment_Toolbox_Comment_list();
}
