<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

if (!class_exists('WPCT_Helper')) {
    class WPCT_Helper {
        // Submit Button
        public static function wpct_submit_button($tabindex = 0) {
            echo '<div class="wpct_settings_toolbar">';
            submit_button(__('Save Setting', 'wpct'), 'primary', __('Save Setting', 'wpct'), false, [
                'id' => 'wpct_save_button_' . $tabindex,
                'tabindex' => $tabindex
            ]);
            echo '</div>';
        }

        // Input Field
        public static function wpct_input_field($name, $label, $type = 'text', $value = '', $description = '', $attr = array()) {
            // Fetch the stored value for this option (default to $value if not set)
            $input_value = get_option($name, $value);

            // Prepare additional attributes
            $additional_attributes = '';
            if (!empty($attr)) {
                foreach ($attr as $key => $val) {
                    $additional_attributes .= ' ' . esc_attr($key) . '="' . esc_attr($val) . '"';
                }
            }

            ?>
            <tr>
                <th scope="row">
                    <?php if ($label): ?>
                        <label for="<?php echo esc_attr($name); ?>" class="status-label"><?php echo esc_html($label); ?></label>
                    <?php endif; ?>
                </th>
                <td>
                    <input class="regular-text wpct-input" type="<?php echo esc_attr($type); ?>"
                           name="<?php echo esc_attr($name); ?>"
                           id="<?php echo esc_attr($name); ?>"
                           value="<?php echo esc_attr($input_value); ?>"
                           <?php echo $additional_attributes; ?>
                    />
                    <?php if ($description): ?>
                        <p class="description"><?php echo wp_kses_post($description); ?></p>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
        }

        // Select Box
        public static function wpct_select_box($name, $label, $value = '', $description = '', $choices = array(), $attr = array()) {
            // Fetch the stored value for this option (default to $value if not set)
            $input_value = get_option($name, $value);

            // Prepare additional attributes
            $additional_attributes = '';
            if (!empty($attr)) {
                foreach ($attr as $key => $val) {
                    $additional_attributes .= ' ' . esc_attr($key) . '="' . esc_attr($val) . '"';
                }
            }

            ?>
            <tr>
                <th scope="row">
                    <?php if ($label): ?>
                        <label for="<?php echo esc_attr($name); ?>" class="status-label"><?php echo esc_html($label); ?></label>
                    <?php endif; ?>
                </th>
                <td colspan="1">
                    <select class="regular-text wpct-input" name="<?php echo esc_attr($name); ?>" id="<?php echo esc_attr($name); ?>" <?php echo $additional_attributes; ?>>
                        <?php
                        // Loop through choices and output the option tags
                        foreach ($choices as $key => $label) {
                            $selected = selected($input_value, $key, false); // Check if this value is selected
                            echo '<option value="' . esc_attr($key) . '" ' . $selected . '>' . esc_html($label) . '</option>';
                        }
                        ?>
                    </select>
                    <?php if ($description): ?>
                        <p class="description"><?php echo wp_kses_post($description) ?></p>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
        }

        // Text Area Field
        public static function wpct_text_area($name, $label, $value = '', $description = '', $attr = array()) {
            // Fetch the stored value for this option (default to $value if not set)
            $text_value = get_option($name, $value);

            // Prepare additional attributes
            $additional_attributes = '';
            if (!empty($attr)) {
                foreach ($attr as $key => $val) {
                    $additional_attributes .= ' ' . esc_attr($key) . '="' . esc_attr($val) . '"';
                }
            }

            ?>
            <tr>
                <th scope="row">
                    <?php if ($label): ?>
                        <label for="<?php echo esc_attr($name); ?>" class="status-label"><?php echo esc_html($label); ?></label>
                    <?php endif; ?>
                </th>
                <td>
                    <textarea class="regular-text wpct-input"
                              name="<?php echo esc_attr($name); ?>"
                              id="<?php echo esc_attr($name); ?>"
                              <?php echo $additional_attributes; ?>
                    ><?php echo esc_textarea($text_value); ?></textarea>
                    <?php if ($description): ?>
                        <p class="description"><?php echo wp_kses_post($description) ?></p>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
        }

        // Test Code Field
        public static function wpct_test_code($name, $label, $value = '', $description = '', $attr = array()) {
            // Prepare additional attributes
            $additional_attributes = '';
            if (!empty($attr)) {
                foreach ($attr as $key => $val) {
                    $additional_attributes .= ' ' . esc_attr($key) . '="' . esc_attr($val) . '"';
                }
            }

            ?>
            <tr>
                <th scope="row">
                    <?php if ($label): ?>
                        <label for="<?php echo esc_attr($name); ?>" class="status-label"><?php echo esc_html($label); ?></label>
                    <?php endif; ?>
                </th>
                <td>
                    <textarea class="widefat wpct-input"
                              id="<?php echo esc_attr($name); ?>"
                              <?php echo $additional_attributes; ?> ><?php echo esc_textarea($value); ?></textarea>
                    <?php if ($description): ?>
                        <p class="description"><?php echo wp_kses_post($description) ?></p>
                    <?php endif; ?>
                </td>
            </tr>
            <?php
        }

        public static function wpct_get_comment_cookie_lifetime() {
            // Get the raw option value for cookie lifetime, default to 259200 (3 days)
            $lifetime_value = intval(get_option('wpct_comment_cookie_lifetime', 259200));
            
            // Apply filter to allow dynamic modification
            $lifetime = apply_filters('wpct_comment_cookie_lifetime_value', $lifetime_value);
            
            // Optional: sanitize again in case filter returns unexpected value
            return intval($lifetime);
        }

        // Starts a PHP session if not already started and headers allow it
        public static function wpct_start_session($by = 'unknown') {
            if (session_status() == PHP_SESSION_NONE) {
                session_start();

                if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                    error_log("WPCT: session started by {$by}");
                }
            } else {
                if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                    error_log("WPCT: session NOT started by {$by}");
                }
            }
        }

        // Destroys the PHP session only if it's currently active, with optional cleanup and logging
        public static function wpct_destroy_session($by = 'unknown') {
            if (session_status() == PHP_SESSION_ACTIVE) {
                $_SESSION = [];
                session_destroy();

                if (ini_get("session.use_cookies")) {
                    $params = session_get_cookie_params();
                    setcookie(session_name(), '', time() - 42000,
                        $params["path"], $params["domain"],
                        $params["secure"], $params["httponly"]
                    );
                }

                if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                    error_log("WPCT: session destroyed by {$by}");
                }
            } else {
                if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                    error_log("WPCT: session NOT destroyed by {$by}");
                }
            }
        }

        // Get Comment Disallowed Keyes Formted
        public static function wpct_get_comment_blocklist() {
            $raw = get_option('disallowed_keys');
            if (!$raw) {
                return [];
            }
            return array_filter(array_map('trim', explode("\n", $raw)));
        }

        // Update Comment Disallowed Keyes Formted
        public static function wpct_update_comment_blocklist($item) {
            $blacklist = self::wpct_get_comment_blocklist();

            if (!in_array($item, $blacklist, true)) {
                $blacklist[] = $item;
                update_option('disallowed_keys', implode("\n", $blacklist));
            }
            return $blacklist;
        }

        // Get wp_get_referer with fallback
        public static function wpct_get_referer($fallback) {
            return wp_get_referer() ? wp_get_referer() : admin_url(esc_url($fallback));
        }

        public static function wpct_create_admin_notices($message, $level = 1, $is_dismissible = true, $vars = []) {
            // Map numeric levels to CSS classes
            $levels_map = [
                1 => 'success',
                2 => 'warning',
                3 => 'error',
                4 => 'info',
            ];

            if ($is_dismissible) {
                // If vars param is not empty, pass it to JS inline script
                if (!empty($vars) && is_array($vars)) {
                    // JSON encode safely for JS
                    $vars_json = wp_json_encode($vars);

                    wp_add_inline_script('wpct-script', '(function($){$(function(){$.fn.adminNoticeDismissCleaner(' . $vars_json . ');});})(jQuery);');
                }
            }

            // Use wp_admin_notice (WP 6.4+)
            $args = [
                'type' => ((isset($levels_map[$level])) ? $levels_map[$level] : 'info'),
                'dismissible' => $is_dismissible,
            ];

            // Render the notice and capture output (wp_admin_notice echos by default)
            // We'll capture it so that your function still returns the HTML string
            wp_admin_notice($message, $args);
        }

        // Decide what to do with the comment based on status
        public static function wpct_handel_with_comment($id) {
            $comment = get_comment($id);
            if ($comment && ($comment->comment_approved === 'spam' || $comment->comment_approved === 'trash')) {
                wp_delete_comment($id, true);
            } else {
                wp_set_comment_status($id, 'trash', true);
            }
        }

        public static function wpct_add_wp_setting_link($path, $name) {
            $url = $path;
            return sprintf(
                /* translators: %1$s = opening anchor tag with URL, %2$s = closing anchor tag, %3$s = opening strong tag */
                __('%3$sSettings → %1$s%5$s%2$s%4$s', 'wpct'),
                '<a href="' . esc_url(admin_url($url)) . '" target="_blank" rel="noopener noreferrer">',
                '</a>',
                '<strong>',
                '</strong>',
                $name
            );
        }
    }
}
