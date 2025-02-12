<?php
class WP_Comment_Toolbox_Comment_Author_Roles {
    public function __construct() {
        add_filter('get_comment_author', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('bbp_get_reply_author_display_name', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('bbp_get_topic_author_display_name', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('bp_get_member_name', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('woocommerce_customer_name', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('learndash_user_profile_field', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('um_user_name', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('wpml_translate_role', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('memberpress_user_name', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('wp_simple_pay_user_name', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('event_espresso_user_name', [$this, 'add_user_role_to_author'], 10, 2);
        add_filter('woocommerce_subscriptions_user_name', [$this, 'add_user_role_to_author'], 10, 2);
    }

    // Adds user role to author display
    public function add_user_role_to_author($author, $user_id_or_comment_id, $is_comment = true) {
        if (!is_admin()) {
            $enabled_roles = get_option('wpct_roles_enabled', '0');
            $guest_role_lebel = (get_option('wpct_guest_label', 'Guest') ?? __('Guest', 'wpct'));
            $admin_role_lebel = (get_option('wpct_admin_label', 'Admin') ?? __('Admin', 'wpct'));
            $sub_role_lebel = (get_option('wpct_subscriber_label', 'Subscriber') ?? __('Subscriber', 'wpct'));
            $admin_author_role_label = (get_option('wpct_admin_author_label', 'Admin/Author') ?? __('Admin/Author', 'wpct'));

            if ($enabled_roles === '1') {
                if ($is_comment) {
                    $comment = get_comment($user_id_or_comment_id);
                    $user = get_userdata($comment->user_id);
                    $post_author_id = get_post_field('post_author', $comment->comment_post_ID);
                } else {
                    // If it's bbPress, fetch user data based on user ID
                    $user = get_userdata($user_id_or_comment_id);
                    $post_author_id = null; // Not needed for bbPress
                }

                if ($user && !empty($user->roles)) {
                    // Get the user's role and convert to lowercase for matching
                    $role = strtolower($user->roles[0]);

                    // Check if user is an administrator and determine role accordingly
                    if ($role === 'administrator') {
                        // For administrator, check if the user is the post author
                        $role = ($is_comment && $user->ID === (int) $post_author_id) ? $admin_author_role_label : $admin_role_lebel;
                    } elseif ($role === 'subscriber') {
                        // For subscriber, use custom label if available
                        $role = $sub_role_lebel;
                    } else {
                        // For other roles, use the custom label if available, else capitalize the role name
                        $role = $role;
                    }

                    // Append the role to the author display
                    $author .= ' <span class="sbu-role user-role-' . esc_attr(strtolower($role)) . '">(' . esc_html(strtolower($role)) . ')</span>';
                } else {
                    // If no user found, display 'Guest' role
                    $author .= ' <span class="sbu-role user-role-guest">(' . esc_html(strtolower($guest_role_lebel)) . ')</span>';
                }
            }
        }
        return $author;
    }
}

// Instantiate the plugin class
new WP_Comment_Toolbox_Comment_Author_Roles();
