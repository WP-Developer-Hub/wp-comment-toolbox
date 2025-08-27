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
            add_filter('comment_row_actions', [$this, 'add_block_ip_action_to_comment'], 10, 2);
            add_action('wp_ajax_wptc_block_commentor_ip', [$this, 'handle_block_ip_action']);
            add_action('admin_post_wptc_block_commentor_ip', [$this, 'handle_block_ip_action']);
            add_filter('admin_notices', [$this, 'handle_block_ip_notices']);

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
                   return (filter_var($matches[1], FILTER_VALIDATE_URL) ? $matches[1] : $matches[2]);
                }, $comment_text);

                // Strip any remaining HTML tags
                return wp_strip_all_tags($comment_text);
            }
            return $comment_text;
        }

        /**
         * Add "Block IP" action link to each comment row in wp-admin.
         */
        public function add_block_ip_action_to_comment($actions, $comment) {
            if (get_option('wpct_show_block_ip_action', 1)) {
                $id = $comment->comment_ID;
                $ip = $comment->comment_author_IP;
                $txt = esc_html__('Block IP', 'wpct');
                $label = __('Block commentor IP?', 'wpct');

                // Consistent nonce action
                $nonce_action = 'wptc_block_commentor_ip_' . $ip;

                $params = [
                    'id' => $id,
                    'ip' => $ip,
                ];

                $block_url = WPCT_Helper::wpct_create_action_url('wptc_block_commentor_ip', $nonce_action, $params);

                // Same nonce for AJAX
                $ajax_nonce = wp_create_nonce($nonce_action);

                $actions['wptc_block_ip'] = sprintf(
                    '<a href="%1$s" class="wptc_block_ip vim-b vim-destructive aria-button-if-js" 
                    data-wp-lists="wptc_block_commentor_ip:the-comment-list:comment-%2$d:ip=%3$s::trash=1"
                    aria-label="%4$s" role="button" style="color: #b32d2e;">%5$s</a>',
                    esc_url($block_url),
                    esc_attr($id),
                    esc_attr($ip),
                    esc_attr($label),
                    $txt
                );
            }
            return $actions;
        }

        public function handle_block_ip_action() {
            if (!get_option('wpct_show_block_ip_action', 1)) {
                return;
            }

            $redirect_url = WPCT_Helper::wpct_get_referer('edit-comments.php');

            if (!current_user_can('manage_options')) {
                WPCT_Helper::wpct_create_admin_notices(__('Unauthorized user', 'wpct'), 3, true);
                wp_safe_redirect($redirect_url);
                exit;
            }

            $id = isset($_GET['id']) ? intval($_GET['id']) : 0;
            $ip = isset($_GET['ip']) ? sanitize_text_field(wp_unslash($_GET['ip'])) : '';
            $nonce = isset($_GET['_wpnonce']) ? wp_unslash($_GET['_wpnonce']) : '';

            // Validate IP
            if (empty($ip) || !filter_var($ip, FILTER_VALIDATE_IP)) {
                WPCT_Helper::wpct_create_admin_notices(__('Invalid IP address', 'wpct'), 3, true);
                wp_safe_redirect($redirect_url);
                exit;
            }

            // Validate comment ID
            if (empty($id) || $id <= 0) {
                WPCT_Helper::wpct_create_admin_notices(__('Invalid comment ID', 'wpct'), 3, true);
                wp_safe_redirect($redirect_url);
                exit;
            }

            // Validate nonce
            if (!wp_verify_nonce($nonce, 'wptc_block_commentor_ip_' . $ip)) {
                WPCT_Helper::wpct_create_admin_notices(__('Invalid request', 'wpct'), 3, true);
                wp_safe_redirect($redirect_url);
                exit;
            }

            // 1. Block the IP
            WPCT_Helper::wpct_update_comment_blocklist($ip);

            // 2. Decide what to do with the comment based on status
            WPCT_Helper::wpct_handel_with_comment($id);

            // 3. Redirect with success notice
            $redirect_url = remove_query_arg(['block_ip_status', 'blocked_ip'], $redirect_url);
            $redirect_url = add_query_arg([
                'block_ip_status' => 'success',
                'blocked_ip' => $ip,
            ], $redirect_url);

            wp_safe_redirect($redirect_url);
            exit;
        }

        public function handle_block_ip_notices() {
            if (get_option('wpct_show_block_ip_action', 1)) {
                if (isset($_GET['block_ip_status']) && $_GET['block_ip_status'] === 'success' && !empty($_GET['blocked_ip'])) {
                    $blocked_ip = sanitize_text_field(wp_unslash($_GET['blocked_ip']));
                    $blacklist = WPCT_Helper::wpct_get_comment_blocklist();

                    // Prepare the dynamic message text based on IP presence in blacklist
                    if (in_array($blocked_ip, $blacklist, true)) {
                        $message = sprintf(
                            /* translators: %s is an IP address */
                            esc_html__('IP address %s is already in the comment blacklist and remains blocked.', 'wpct'),
                            '<strong>' . esc_html($blocked_ip) . '</strong>'
                        );
                    } else {
                        $message = sprintf(
                            /* translators: %s is an IP address */
                            esc_html__('IP address %s has been added to the comment blacklist and blocked from commenting.', 'wpct'),
                            '<strong>' . esc_html($blocked_ip) . '</strong>'
                        );
                    }

                    // Output the static wrapper, inserting the dynamic message
                    echo WPCT_Helper::wpct_create_admin_notices($message, 1, true, ['block_ip_status', 'blocked_ip']);
                }
            }
        }
    }
    new WP_Comment_Toolbox_Admin();
}
