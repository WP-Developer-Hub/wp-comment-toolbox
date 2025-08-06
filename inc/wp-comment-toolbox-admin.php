<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WP_Comment_Toolbox_Admin')) {
    class WP_Comment_Toolbox_Admin {
        public function __construct() {
            // Hook into the admin comment list setup
            add_filter('manage_edit-comments_columns', [$this, 'add_comment_column']);
            add_action('manage_comments_custom_column', [$this, 'show_comment_column_content'], 10, 2);
            add_filter('views_edit-comments', [$this, 'filter_comments_by_scam']);
            add_filter('comments_clauses', [$this, 'filter_comments_by_scam_query'], 10, 2);

            // Add the comment text filter based on the option
            add_filter('comment_text', [$this, 'filter_comment_text'], PHP_INT_MAX);
        }

        // Check if comment status exists function (since it's not built-in)
        public function comment_status_exists($status) {
            global $wpdb;
            $statuses = $wpdb->get_col("SELECT comment_status FROM {$wpdb->comments} GROUP BY comment_status");
            return in_array($status, $statuses);
        }

        // Add the custom column to the Comments list
        public function add_comment_column($columns) {
            if ('1' === get_option('wpct_scam_filter_enabled')) {
                $columns['has_scam'] = __('Scam Filter', 'wpct');
            }
            return $columns;
        }

        // Display the content in the custom column
        public function show_comment_column_content($column, $comment_ID) {
            if ($column === 'has_scam') {
                // Only process the scam detection if the filter is enabled
                if ('1' === get_option('wpct_scam_filter_enabled')) {
                    $comment_text = get_comment_text($comment_ID);

                    // Check if comment contains links using regex (considered as scam)
                    if (preg_match('/https?:\/\/[^\s]+/', $comment_text)) {
                        update_comment_meta($comment_ID, 'has_scam', '1'); // Mark as scam
                    } else {
                        update_comment_meta($comment_ID, 'has_scam', '0'); // No scam
                    }
                }

                // Display the meta value (has_scam)
                echo get_comment_meta($comment_ID, 'has_scam', true) === '1' ? __('Yes', 'wpct') : __('No', 'wpct');
            }
        }

        // Add a filter dropdown to the comments screen with counts for scam filter
        public function filter_comments_by_scam($views) {
            // Only add this filter if the scam filter is enabled
            if ('1' === get_option('wpct_scam_filter_enabled')) {
                $args = array(
                    'number' => 0,
                    'meta_query' => array(
                        array(
                            'key' => 'has_scam',
                            'value' => '1',
                            'compare' => '='
                        ),
                    ),
                );
                $comment_query = new WP_Comment_Query($args);
                $comments = $comment_query->comments;

                // Change the label to "View Scam Comments"
                $views['has_scam'] = '<a href="' . add_query_arg('has_scam', '1') . '">' . __('View Scam Comments', 'wpct') . ' <span class="count"><span class="scam-count">(' . count($comments) . ')</span></span></a>';
            }

            return $views;
        }

        // Filter comments based on the presence of links (marked as scam)
        public function filter_comments_by_scam_query($clauses, $query) {
            // Check if we are in the admin area and a "has_scam" filter is applied
            if (isset($_GET['has_scam']) && $_GET['has_scam'] === '1' && '1' === get_option('wpct_scam_filter_enabled')) {
                global $wpdb;

                // Only add the join if not already present
                if (empty($clauses['join'])) {
                    $clauses['join'] .= " LEFT JOIN {$wpdb->prefix}commentmeta AS cm ON {$wpdb->comments}.comment_ID = cm.comment_id ";
                    $clauses['where'] .= " AND cm.meta_key = 'has_scam' AND cm.meta_value = '1' ";
                }
            }

            return $clauses;
        }

        // Filter comment text based on wpct_disable_comment_formatting option
        public function filter_comment_text($comment_text) {
            if (is_admin() && get_option('wpct_disable_comment_formatting')) {
                // Preserve the actual URLs but strip the anchor tags
                $comment_text = preg_replace_callback('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', function($matches) {
                    return filter_var($matches[1], FILTER_VALIDATE_URL) ? $matches[1] : $matches[2];
                }, $comment_text);

                // Strip all remaining HTML tags
                $comment_text = wp_strip_all_tags($comment_text);
            }
            return $comment_text;
        }
    }
    new WP_Comment_Toolbox_Admin();
}
