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
        $input_value = empty(get_option($name, $value)) ? $value : get_option($name, $value);

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
        $input_value = empty(get_option($name, $value)) ? $value : get_option($name, $value);

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
        $text_value = empty(get_option($name, $value)) ? $value : get_option($name, $value);
        
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
}
?>
