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

    public function add_user_role_to_author($author, $user_id_or_comment_id, $is_comment = true) {
        if (!is_admin()) {
            $enabled_roles = get_option('wpct_roles_enabled', '0');
            $guest_role_label = get_option('wpct_guest_label', __('Guest', 'wpct'));
            $admin_role_label = get_option('wpct_admin_label', __('Admin', 'wpct'));
            $sub_role_label = get_option('wpct_subscriber_label', __('Subscriber', 'wpct'));
            $admin_author_role_label = get_option('wpct_admin_author_label', __('Admin/Author', 'wpct'));

            if ($enabled_roles === '1') {
                $user = null;
                $post_author_id = null;

                if ($is_comment) {
                    $comment = get_comment($user_id_or_comment_id);
                    if ($comment) {
                        $user = get_userdata($comment->user_id);
                        $post_author_id = get_post_field('post_author', $comment->comment_post_ID);
                    }
                } else {
                    $user = get_userdata($user_id_or_comment_id);
                }

                if ($user && !empty($user->roles)) {
                    $role = array_shift($user->roles);

                    if ($role === 'administrator') {
                        $role = ($is_comment && $user->ID === (int) $post_author_id) ? $admin_author_role_label : $admin_role_label;
                    } elseif ($role === 'subscriber') {
                        $role = $sub_role_label;
                    }

                    $author .= ' <span class="sbu-role user-role-' . esc_attr(strtolower($role)) . '">(' . esc_html(strtolower($role)) . ')</span>';
                } else {
                    $author .= ' <span class="sbu-role user-role-guest">(' . esc_html(strtolower($guest_role_label)) . ')</span>';
                }
            }
        }
        return $author;
    }
}

new WP_Comment_Toolbox_Comment_Author_Roles();
