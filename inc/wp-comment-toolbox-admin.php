<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WP_Comment_Toolbox_Admin')) {
    class WP_Comment_Toolbox_Admin {
        public function __construct() {
            // Hook into the admin comment list setup
            add_filter('comment_row_actions', [$this, 'add_block_ip_action_to_comment'], 10, 2);
            add_action('wp_ajax_wptc_block_commentor_ip', [$this, 'handle_block_ip_action']);
            add_action('admin_post_wptc_block_commentor_ip', [$this, 'handle_block_ip_action']);
            add_filter('admin_notices', [$this, 'handle_block_ip_notices']);

            // Add the comment text filter based on the option
            add_filter('comment_text', [$this, 'filter_comment_text'], PHP_INT_MAX);

            // Add check comments for sus button
            add_action('admin_post_wptc_check_comments_for_sus', [$this, 'handle_check_comments_for_sus_action']);
            add_filter('manage_comments_nav', [$this, 'add_check_comments_for_sus_button'], 10, 2);
            add_filter('admin_notices', [$this, 'handle_check_comments_for_sus_notices']);

            // Add new comment type called flagged
            add_filter('admin_comment_types_dropdown', [$this, 'add_fleged_comments_type'], PHP_INT_MAX);
        }

        public function add_fleged_comments_type($comment_types) {
            $comment_types['flagged'] = __('Flagged', 'wpct');
            return $comment_types;
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

        public function add_check_comments_for_sus_button($comment_status, $which) {
            if ($which === 'top' && get_option('wpct_enable_spam_filter', 0)) {
                $nonce = isset($_GET['comment_status']) ? wp_unslash($_GET['_wpnonce']) : '';
                $params = [
                    'wptc_comment_status' => !empty($comment_status) ? $comment_status : 'all',
                ];

                $url = WPCT_Helper::wpct_create_action_url(
                    'wptc_check_comments_for_sus',
                    'wptc_check_comments_for_sus_action',
                    $params
                );
                echo '<a href="' . esc_url($url) . '" id="wptc-ccfs" style="margin: 0 0 0 8px;" class="button">' . esc_html__('Check for sus', 'wpct') . '</a>';
            }
        }

        public function handle_check_comments_for_sus_action() {
            if (!get_option('wpct_enable_spam_filter', 1)) {
                return;
            }

            $redirect_url = WPCT_Helper::wpct_get_referer('edit-comments.php');

            // Capability check
            if (!current_user_can('manage_options')) {
                WPCT_Helper::wpct_create_admin_notices(__('Unauthorized user', 'wpct'), 3, true);
                wp_safe_redirect($redirect_url);
                exit;
            }

            $nonce = isset($_GET['_wpnonce']) ? wp_unslash($_GET['_wpnonce']) : '';

            // Check nonce and query param for spam check trigger
            if (!wp_verify_nonce($nonce, 'wptc_check_comments_for_sus_action')) {
                WPCT_Helper::wpct_create_admin_notices(__('Invalid request', 'wpct'), 3, true);
                wp_safe_redirect($redirect_url);
                exit;
            }

            $comment_status = isset($_GET['wptc_comment_status']) ? wp_unslash($_GET['wptc_comment_status']) : 'all';

            // Get all comments regardless of status or number
            $comments = get_comments(['status' => $comment_status]);

            $flagged_count = 0;

            foreach ($comments as $comment) {
                // Convert WP_Comment object to array as expected by helper
                $commentdata = (array) $comment;

                // Use your helper to determine if comment is suspicious
                WPCT_Helper::wpct_check_comment_for_spam($commentdata);

                $comment_id = $comment->comment_ID;
                $flagged_comment = get_comment($comment_id);

                if ($flagged_comment && $flagged_comment->comment_type === 'flagged') {
                    wp_set_comment_status($comment_id, 'spam', false);
                }
            }

            // Redirect back to comments admin with success message + flagged count
            wp_safe_redirect(add_query_arg([
                'wptc_check_spam' => 'success',
            ], WPCT_Helper::wpct_get_referer('edit-comments.php')));
            exit;
        }

        public function handle_check_comments_for_sus_notices() {
            if (get_option('wpct_enable_spam_filter', 0)) {
                if (isset($_GET['wptc_check_spam']) && $_GET['wptc_check_spam'] === 'success') {
                    $flagged_count = count(get_comments(['type' => 'flagged']));

                    if ($flagged_count > 0) {
                        $message = sprintf(
                            /* translators: %d is the number of flagged suspect comments */
                            _n(
                                '%d sus comment found.',
                                '%d sus comments found.',
                                $flagged_count,
                                'wpct'
                            ),
                            $flagged_count
                        );
                    } else {
                        $message = esc_html__('No sus comments found.', 'wpct');
                    }

                    echo WPCT_Helper::wpct_create_admin_notices($message, 1, true, ['wptc_check_spam']);
                }
            }
        }
    }
    new WP_Comment_Toolbox_Admin();
}
