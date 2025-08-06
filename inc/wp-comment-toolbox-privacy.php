<?php
if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('WP_Comment_Toolbox_Privacy')) {
    class WP_Comment_Toolbox_Privacy {
        public function __construct() {
            add_filter('comment_cookie_lifetime', [$this, 'wpct_set_comment_cookie_lifetime']);
        }

        public function wpct_set_comment_cookie_lifetime($seconds) {
            $lifetime = WPCT_Helper::wpct_get_comment_cookie_lifetime();

            if ($lifetime < 0) {
                $lifetime = 0;
            }

            return $lifetime;
        }
    }
    new WP_Comment_Toolbox_Privacy();
}
