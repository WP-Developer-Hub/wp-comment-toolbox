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
            add_action('admin_post_wptc_nuke_all_sus_comments', [$this, 'handle_nuke_all_sus_comments_action']);
            add_filter('manage_comments_nav', [$this, 'add_nuke_all_sus_comments_button'], 10, 2);
            add_filter('admin_notices', [$this, 'handle_nuke_all_sus_comments_notices']);

            add_filter('manage_edit-comments_columns', array($this, 'add_comment_type_column'));
            add_action('manage_comments_custom_column', array($this, 'output_comment_type_column'), 10, 2);
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

        public function add_nuke_all_sus_comments_button($comment_status, $which) {
            if ($which === 'top' && get_option('wpct_enable_nuke_all_sus_comment_button', 0)) {
                $params = [
                    'wptc_comment_status' => !empty($comment_status) ? $comment_status : 'all',
                ];

                $url = WPCT_Helper::wpct_create_action_url(
                    'wptc_nuke_all_sus_comments',
                    'wptc_nuke_all_sus_comments_action',
                    $params
                );
                echo '<a href="' . esc_url($url) . '" id="wptc-ccfs" style="margin: 0 0 0 8px;" class="button">' . esc_html__('Block all sus comment', 'wpct') . '</a>';
            }
        }

        public function handle_nuke_all_sus_comments_action() {
            if (!get_option('wpct_enable_nuke_all_sus_comment_button', 0)) {
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
            if (!wp_verify_nonce($nonce, 'wptc_nuke_all_sus_comments_action')) {
                WPCT_Helper::wpct_create_admin_notices(__('Invalid request', 'wpct'), 3, true);
                wp_safe_redirect($redirect_url);
                exit;
            }

            $comment_status = isset($_GET['wptc_comment_status']) ? wp_unslash($_GET['wptc_comment_status']) : 'all';

            // Get all comments regardless of status or number
            $comments = get_comments(['status' => $comment_status]);

            $comment_count = 0;

            foreach ($comments as $comment) {
                if (WPCT_Helper::wpct_check_comment_for_spam($comment)) {
                    if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && !WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                        WPCT_Helper::wpct_update_comment_blocklist($comment->comment_author_IP);
                        WPCT_Helper::wpct_handel_with_comment($comment->comment_ID);
                    }
                    $comment_count++;
                }
            }

            // Redirect back to comments admin with success message + flagged count
            wp_safe_redirect(add_query_arg([
                'wptc_spam_nuked' => 'success',
                'wptc_comment_count' => $comment_count,
            ], WPCT_Helper::wpct_get_referer('edit-comments.php')));
            exit;
        }

        public function handle_nuke_all_sus_comments_notices() {
            if (get_option('wpct_enable_nuke_all_sus_comment_button', 0)) {

                $is_spam_nuked_successful = (isset($_GET['wptc_spam_nuked']) && $_GET['wptc_spam_nuked'] === 'success');

                if ($is_spam_nuked_successful && isset($_GET['wptc_comment_count'])) {
                    $flagged_count = intval($_GET['wptc_comment_count']);

                    if ($flagged_count > 0) {
                        $message = sprintf(
                            /* translators: %d is the number of flagged suspect comments */
                            _n(
                                '%d sus comment has been nuked and its IP address has been added to the block list.',
                                '%d sus comments have been nuked and their IP addresses have been added to the block list.',
                                $flagged_count,
                                'wpct'
                            ),
                            $flagged_count
                        );
                    } else {
                        $message = esc_html__('No sus comments found.', 'wpct');
                    }

                    echo WPCT_Helper::wpct_create_admin_notices(
                        $message,
                        1,
                        true,
                        ['wptc_spam_nuked', 'wptc_comment_count']
                    );
                }
            }
        }

        public function add_comment_type_column($columns) {
            $enabled_columns = (array) get_option('wpct_comment_list_enhancement', []);
            
            // Make a new array to build columns
            $new_columns = [];

            foreach ($columns as $key => $label) {
                $new_columns[$key] = $label;

                if ($key === 'date') {
                    // Insert columns enabled by settings, after the 'date' column
                    if (in_array('wptc_comment_type', $enabled_columns, true)) {
                        $new_columns['wptc_comment_type'] = __('Type', 'wptc');
                    }
                    if (in_array('wptc_comment_author_role', $enabled_columns, true)) {
                        $new_columns['wptc_comment_author_role'] = __('Role', 'wptc');
                    }
                    if (in_array('wptc_comment_id', $enabled_columns, true)) {
                        $new_columns['wptc_comment_id'] = __('ID', 'wptc');
                    }
                }
            }

            return $new_columns;
        }

        public function output_comment_type_column($column, $comment_ID) {
            $enabled_columns = (array) get_option('wpct_comment_list_enhancement', []);
            if (in_array('wptc_comment_type', $enabled_columns, true) && $column === 'wptc_comment_type') {
                $comment = get_comment($comment_ID);
                $type = $comment->comment_type;
                echo esc_html(!empty($type) ? ucfirst($type) : __('Comment', 'wptc'));
            }

            if (in_array('wptc_comment_author_role', $enabled_columns, true) && $column === 'wptc_comment_author_role') {
                $comment = get_comment($comment_id);
                if ($comment->user_id) {
                    $user = get_userdata($comment->user_id);
                    $roles = ($user ? implode(', ', $user->roles) : __('No Role', 'text-domain'));
                    echo esc_html($roles);
                } else {
                    echo __('Guest', 'text-domain');
                }
            }

            if (in_array('wptc_comment_id', $enabled_columns, true) && $column === 'wptc_comment_id') {
                $comment = get_comment($comment_id);
                echo esc_html($comment_ID);
            }
        }
    }
    new WP_Comment_Toolbox_Admin();
}
