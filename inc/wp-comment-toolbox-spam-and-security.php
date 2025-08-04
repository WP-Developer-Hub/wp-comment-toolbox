<?php
if (!defined('ABSPATH')) {
    exit;
}

class WP_Comment_Toolbox_Span_And_Security {
    public function __construct() {
        add_action('init', [$this, 'toggle_make_clickable']);
        add_action('pre_comment_approved', [$this, 'check_honeypot']);
        add_action('check_comment_flood', [$this, 'check_referrer'], 5);
        add_filter('preprocess_comment', [$this, 'limit_comment_length']);
        add_action('pre_comment_on_post', [$this, 'verify_wp_nonce_field']);
        add_filter('comment_text', [$this, 'strip_bad_html_form_comment'], 9);
        add_action('comment_form', [$this, 'add_wp_nonce_and_huonoy_pot_field']);
        add_filter('preprocess_comment', [$this, 'wpct_verify_math_captcha'], 8);
        add_filter('pre_comment_content', [$this, 'strip_bad_html_form_comment'], 9);
        add_filter('comment_form_field_comment', [$this, 'wpct_math_captcha_field'], 9);
    }

    public function toggle_make_clickable() {
        if (get_option('wpct_disable_clickable_links', 0)) {
            remove_filter('comment_text', 'make_clickable', 9);
        }
    }

    public function verify_wp_nonce_field() {
        if (get_option('wpct_enable_spam_protect', 0)) {
            // Define custom error messages
            $error_title = __('Security Check', 'wpct');
            $error_message = __('An issue occurred while processing your request. If this continues, please contact support.', 'wpct');

            // Check if the HTTP_REFERER header is set and non-empty
            if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == "") {
                // Handle the absence of referer header
                $is_curl_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false);
                $is_wget_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Wget') !== false);

                // Handle cURL/Wget requests differently
                if ($is_curl_request || $is_wget_request) {
                    header('HTTP/1.1 403 Forbidden');
                    header('Content-Type: text/plain; charset=UTF-8');
                    die($error_message);
                } else {
                    wp_die($error_message, $error_title, array('response' => 403));
                }
            }

            // After checking referer, check for nonce
            if (!isset($_POST['wpct_comment_nonce']) || !wp_verify_nonce($_POST['wpct_comment_nonce'], 'comment_nonce')) {
                // Handle nonce verification failure for cURL/Wget requests
                $is_curl_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false);
                $is_wget_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Wget') !== false);

                // Handle cURL/Wget requests differently
                if ($is_curl_request || $is_wget_request) {
                    header('HTTP/1.1 403 Forbidden');
                    header('Content-Type: text/plain; charset=UTF-8');
                    die($error_message);
                } else {
                    wp_die($error_message, $error_title, array('response' => 403));
                }
            }
        }
    }

    public function strip_bad_html_form_comment($content) {
        if (get_option('wpct_disable_clickable_links', 0)) {
            $content = preg_replace_callback('/<a[^>]+href=["\']([^"\']+)["\'][^>]*>(.*?)<\/a>/is', function($matches) {
                return filter_var($matches[1], FILTER_VALIDATE_URL) ? $matches[1] : $matches[2];
            }, $content);
        }

        if(get_option('wpct_enable_wp_kses_post', 0)){
            $content = wp_kses_post($content);
        }

        return $content;
    }

    public function limit_comment_length($comment) {
        $max_length = esc_html(get_option('wpct_comment_message_limit', 280));
        if (strlen($comment['comment_content']) > $max_length) {
            wp_die(sprintf( '<strong>%s</strong> %s', __('Warning:', 'wpct'), __('Please keep your comment under ', 'wpct') . $max_length . __(' characters.', 'wpct') ), __('Comment Length Warning', 'wpct'), array('response' => 500, 'back_link' => true));
        }
        return $comment;
    }

    public function check_referrer() {
        if (get_option('wpct_enable_spam_protect', 0)) {
            // Define custom error messages
            $error_title = __('Security Check', 'wpct');
            $error_message = __('An issue occurred while processing your request. If this continues, please contact support.', 'wpct');

            if (!isset($_SERVER['HTTP_REFERER']) || $_SERVER['HTTP_REFERER'] == "") {
                // Check for cURL and wget in the User-Agent header
                $is_curl_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'curl') !== false);
                $is_wget_request = (isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'Wget') !== false);

                // Check if it's a cURL or wget request
                if ($is_curl_request || $is_wget_request) {
                    header('HTTP/1.1 403 Forbidden');
                    header('Content-Type: text/plain; charset=UTF-8');
                    die($error_message);
                } else {
                    wp_die($error_message, $error_title, array('response' => 403));
                }
            }
        }
    }

    public function add_wp_nonce_and_huonoy_pot_field() {
        if (get_option('wpct_enable_spam_protect', 0)) {
            $textarea_name = str_shuffle(base64_encode(wp_generate_uuid4() . uniqid('', true)));

            wp_nonce_field('comment_nonce', 'wpct_comment_nonce');

            // Start the session if it's not already started
            if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
                @session_start();
            }

            // Store the textarea name in the session variable
            $_SESSION['wpct_comment_honeypot_name'] = $textarea_name;

            $textarea_section_name = sanitize_text_field($_SESSION['wpct_comment_honeypot_name']);

            // Output the hidden textarea field
            echo '<p style="display:none">';
            echo '<textarea name="' . esc_attr($textarea_section_name) . '" cols="100" rows="10"></textarea>';
            echo '<label>' . esc_html__('If you are a human, do not fill in this field.', 'wpct') . '</label>';
            echo '</p>';
        }
    }

    public function check_honeypot($approved) {
        if (get_option('wpct_enable_spam_protect', 0)) {
            // Start the session if it's not already started
            if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
                @session_start();
            }

            // Check nonce validity
            if (!isset($_POST['wpct_comment_nonce']) || !check_admin_referer('comment_nonce', 'wpct_comment_nonce')) {
                $approved = 'spam'; // Mark as spam if nonce is invalid
            }

            // Retrieve the textarea name from the session
            if (isset($_SESSION['wpct_comment_honeypot_name'])) {
                $textarea_name = sanitize_text_field($_SESSION['wpct_comment_honeypot_name']);

                // Get the submit button name from settings
                $submit_name = sanitize_text_field(get_option('wpct_submit_button_name'));

                // Check if the hidden textarea field is filled out
                $textarea_filled_out = !empty($_POST[$textarea_name]);

                // Check if the submit button name is missing
                $submit_name_missing = !empty($submit_name) && empty($_POST[$submit_name]);

                if ($textarea_filled_out || $submit_name_missing) {
                    $approved = 'spam'; // Mark as spam
                }
            }

            // Unset session variable after the check
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_destroy();
            }
        }

        return $approved;
    }

    // Add the math question only for guest users
    public function wpct_math_captcha_field(string $field) {
        if (get_option('wpct_enable_math_captcha', 0)) {
            if (is_user_logged_in()) {
                return $field;
            }

            if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
                @session_start();
            }

            $captcha_level = $this->wpct_ren_math_captcha_level();
            $num1 = rand(1, $captcha_level);
            $num2 = rand(1, $captcha_level);

            // Store the correct CAPTCHA answer (MD5 hash of the sum) in the session
            $_SESSION['wptc_captcha_answer'] = md5($num1 + $num2);
            $_SESSION['wptc_captcha_num1'] = $num1;
            $_SESSION['wptc_captcha_num2'] = $num2;

            $problem = "<span class=\"wptc-captcha-problem\">{$num1} + {$num2}</span>";

            // Append the CAPTCHA after the comment box
            $field .= '<p class="wptc-math-captcha">';
            $field .= '<label for="wpct_math_captcha" class="wptc-captcha-label">';
            $field .= sprintf(__('What is %s?', 'wpct'), $problem);
            $field .= '</label>';
            $field .= '<input type="text" name="wpct_math_captcha" id="wpct_math_captcha" required autocomplete="off">';
            $field .= '<input type="hidden" name="wpct_math_num1" value="' . $num1 . '">';
            $field .= '<input type="hidden" name="wpct_math_num2" value="' . $num2 . '">';
            $field .= '</p>';
        }
        return $field;
    }

    // Validate the math CAPTCHA (only for guests)
    public function wpct_verify_math_captcha($commentdata) {
        if (get_option('wpct_enable_math_captcha', 0)) {
            // Define custom error messages
            $error_message = __('CAPTCHA Failed', 'wpct');
            $missing_fields_message = __('Please answer the CAPTCHA question.', 'wpct');
            $tamper_answer_message = __('CAPTCHA validation error: Possible tampering detected.', 'wpct');
            $incorrect_answer_message = __('Your CAPTCHA answer was incorrect. Please try again.', 'wpct');

            // Skip validation for logged-in users
            if (is_user_logged_in()) {
                return $commentdata;
            }

            if (session_status() == PHP_SESSION_NONE && !headers_sent()) {
                @session_start();
            }

            // Check if CAPTCHA fields or session variables are missing
            if (!isset($_POST['wpct_math_captcha']) || !isset($_SESSION['wptc_captcha_answer'])) {
                wp_die($missing_fields_message, $error_message, array('response' => 403, 'back_link' => true));
            }

            // Check if hidden fields match session values (to prevent tampering)
            if ($_POST['wpct_math_num1'] != $_SESSION['wptc_captcha_num1'] || $_POST['wpct_math_num2'] != $_SESSION['wptc_captcha_num2']) {
                wp_die($tamper_answer_message, $error_message, array('response' => 403, 'back_link' => true));
            }

            // Validate if the answer provided matches the MD5 hash stored in the session
            $user_answer = trim($_POST['wpct_math_captcha']);
            $user_answer = sanitize_text_field($user_answer);  // Sanitizing user input

            // MD5 validation
            if (md5($user_answer) !== $_SESSION['wptc_captcha_answer']) {
                wp_die($incorrect_answer_message, $error_message, array('response' => 403, 'back_link' => true));
            }

            // Unset session variable after the check
            if (session_status() == PHP_SESSION_ACTIVE) {
                session_destroy();
            }
        }
        return $commentdata;
    }

    // Generate a random operator based on difficulty level
    private function wpct_ren_math_captcha_level() {
        switch (get_option('wpct_math_captcha_level', 'easy')) {
            case 'medium':
                return 15;
            case 'hard':
                return 20;
            case 'extreme':
                return 50;
            default:
                return 10;
        }
    }
}
new WP_Comment_Toolbox_Span_And_Security();
