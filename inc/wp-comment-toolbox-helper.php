<?php
if (!defined('ABSPATH')) {
    exit; // Exit if accessed directly
}

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
                    <p class="description"><?php echo esc_html($description); ?></p>
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
            <td>
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
                    <p class="description"><?php echo esc_html($description); ?></p>
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
                    <p class="description"><?php echo esc_html($description); ?></p>
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
                    <p class="description"><?php echo esc_html($description); ?></p>
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
        if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
            @session_start();

            if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                error_log("[WPCT] Session started by '{$by}' at " . date('Y-m-d H:i:s'));
            }
        } else {
            if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                error_log("[WPCT] Session not started by '{$by}'. Status: " . session_status() . ", Headers sent: " . (headers_sent() ? 'true' : 'false'));
            }
        }
    }

    // Destroys the PHP session only if it's currently active, with optional cleanup and logging
    public static function wpct_destroy_session($by = 'unknown') {
        if (session_status() == PHP_SESSION_ACTIVE) {
            // Clear all session variables
            $_SESSION = [];

            // Destroy the session
            session_destroy();

            // Also delete the PHPSESSID cookie to fully clear the session client-side
            if (ini_get("session.use_cookies")) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }

            if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                error_log("[WPCT] Session destroyed by '{$by}' at " . date('Y-m-d H:i:s'));
            }
        } else {
            if (defined('WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON') && WP_COMMENT_TOOLBOX_PLUGIN_IS_DEBUG_ON) {
                error_log("[WPCT] Session not destroyed by '{$by}'. Current status: " . session_status());
            }
        }
    }

}
?>
