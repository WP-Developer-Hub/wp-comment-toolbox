<?php
class WP_Comment_Toolbox_Settings {
    public function __construct() {
        add_action('customize_register', array($this, 'customize_register'));
    }

    public function customize_register($wp_customize) {
        // Add a Panel for Comment Form Settings
        $wp_customize->add_panel('wpct_comment_form_panel', array(
            'title' => __('Comment Settings', 'wpct'),
            'priority' => 200,
        ));

        // Spam and Security Settings Section
        $wp_customize->add_section('wpct_spam_and_security', array(
            'title' => __('Spam & Security', 'wpct'),
            'panel' => 'wpct_comment_form_panel',
        ));

        $wp_customize->add_setting('wpct_comment_message_limit', array(
            'type' => 'option',
            'default' => '280',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('wpct_comment_message_limit', array(
            'label' => __('Comment Character Limit', 'wpct'),
            'section' => 'wpct_spam_and_security',
            'type' => 'number',
            'input_attrs' => array(
                'min' => '240',
                'max' => '480',
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
                'placeholder' => '280',
            ),
            'description' => __('Set the maximum number of characters allowed in the comment text. This helps prevent overly long comments and spammy content.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_disable_clickable_links', array(
            'type' => 'option',
            'default' => '1',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_disable_clickable_links', array(
            'label' => __('Disable clickable links', 'wpct'),
            'section' => 'wpct_spam_and_security',
            'type' => 'select',
            'choices' => array(
                '0' => __('Disable', 'wpct'),
                '1' => __('Enable', 'wpct'),
            ),
            'description' => __('When enabled, links in comments will not be clickable. This can help reduce spam and unwanted external links.', 'wpct'),
        ));

        // Comment List Setting Section
        $wp_customize->add_section('wpct_comment_list', array(
            'title' => __('Comment List', 'wpct'),
            'panel' => 'wpct_comment_form_panel',
        ));

        $wp_customize->add_setting('wpct_author_link_visibility', array(
            'type' => 'option',
            'default' => 'all',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_author_link_visibility', array(
            'label' => __('Author Link Visibility', 'wpct'),
            'section' => 'wpct_comment_list',
            'type' => 'select',
            'choices' => array(
                'none' => __('Disable for all users', 'wpct'),
                'all' => __('Enable for all users', 'wpct'),
                'registered' => __('Enable for registered users only', 'wpct'),
            ),
            'description' => __('Control who can have a clickable author link in comments. Users with the ability to edit posts will not be affected.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_author_link_type', array(
            'type' => 'option',
            'default' => 'external',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_author_link_type', array(
            'label' => __('Author Link Type', 'wpct'),
            'section' => 'wpct_comment_list',
            'type' => 'select',
            'choices' => array(
                'internal' => __('Author Page', 'wpct'),
                'external' => __('Author Website', 'wpct'),
            ),
            'description' => __('Control whether the author link for users who can edit posts links to their website or the WordPress author page.', 'wpct'),
        ));

        // Add setting and control for wpautop and nl2br options
        $wp_customize->add_setting('wpct_format_comment_text', array(
            'type' => 'option',
            'default' => 'auto',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_format_comment_text', array(
            'label' => __('Format Comment Text', 'wpct'),
            'section' => 'wpct_comment_list',
            'type' => 'select',
            'choices' => array(
                'auto' => __('Auto (wpautop)', 'wpct'),
                'nl2br' => __('nl2br (Convert newlines to <br>)', 'wpct'),
                'none' => __('None (No Formatting)', 'wpct'),
            ),
            'description' => __('Choose how to format the comment text: Auto applies wpautop (paragraph tags), nl2br converts newlines to <br> tags, and None means no formatting is applied.', 'wpct'),
        ));

        // Comment Form Setting Section
        $wp_customize->add_section('wpct_comment_form', array(
            'title' => __('Comment Form', 'wpct'),
            'panel' => 'wpct_comment_form_panel',
        ));

        // Add setting for author placeholder
        $wp_customize->add_setting('wpct_author_placeholder', array(
            'type' => 'option',
            'default' => 'full_name', // Default is full name
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_author_placeholder', array(
            'label' => __('Author Placeholder', 'wpct'),
            'section' => 'wpct_comment_form',
            'type' => 'select',
            'choices' => array(
                'full_name' => __('Full Name', 'wpct'),
                'username' => __('Username', 'wpct'),
                'both' => __('Both', 'wpct'),
            ),
            'description' => __('Select what the placeholder should be for the author input field. This will be displayed as a hint to users when filling in their name.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_comment_textarea_row_count', array(
            'type' => 'option',
            'default' => '8',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('wpct_comment_textarea_row_count', array(
            'label' => __('Comment Character Limit', 'wpct'),
            'section' => 'wpct_comment_form',
            'type' => 'number',
            'input_attrs' => array(
                'min' => '8',
                'max' => '40',
                'inputmode' => 'numeric',
                'pattern' => '[0-9]*',
                'placeholder' => '280',
            ),
            'description' => __('Set the maximum number of characters allowed in the comment text. This helps prevent overly long comments and spammy content.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_comment_form_layout', array(
            'type' => 'option',
            'default' => '[author] [email] [url] [comment] [cookies]',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $wp_customize->add_control('wpct_comment_form_layout', array(
            'label' => __('Comment Form Layout', 'wpct'),
            'section' => 'wpct_comment_form',
            'type' => 'textarea',
            'description' => __('Define the structure of the comment form using placeholders: <strong>[author]</strong>, <strong>[email]</strong>, <strong>[url]</strong>, <strong>[comment]</strong>, <strong>[cookies]</strong>.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_comment_form_cookies_msg', array(
            'type' => 'option',
            'default' => '[cookies_msg]',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $wp_customize->add_control('wpct_comment_form_cookies_msg', array(
            'label' => __('Comment Cookies Message', 'wpct'),
            'section' => 'wpct_comment_form',
            'type' => 'textarea',
            'description' => __('Customize the cookies message for the comment form. <br> - Use <strong>[cookies_msg]</strong> to include the default cookies text.<br> - Use <strong>[privacy_policy_link]</strong> to insert a link to your privacy policy page.', 'wpct'),
        ));

        // Extra Settings Section
        $wp_customize->add_section('wpct_extra', array(
            'title' => __('Extra Settings', 'wpct'),
            'panel' => 'wpct_comment_form_panel',
        ));

        $wp_customize->add_setting('wpct_toolbar_enabled', array(
            'type' => 'option',
            'default' => '0',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_toolbar_enabled', array(
            'label' => __('Enable Quick Tags Toolbar', 'wpct'),
            'section' => 'wpct_extra',
            'type' => 'select',
            'choices' => array(
                '0' => __('Disable', 'wpct'),
                '1' => __('Enable', 'wpct'),
            ),
            'description' => __('Enabling this setting will add a quick tags toolbar to the comment form, allowing users to easily format their comments (e.g., bold, italics, etc.).', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_toolbar_mode', array(
            'type' => 'option',
            'default' => 'light',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_toolbar_mode', array(
            'label' => __('Toolbar Style', 'wpct'),
            'section' => 'wpct_extra',
            'type' => 'select',
            'choices' => array(
                'light' => __('Light', 'wpct'),
                'dark'  => __('Dark', 'wpct'),
            ),
            'description' => __('Choose the style of the quick tags toolbar. Light mode has a bright background, while dark mode uses a darker background for better visibility in low-light environments.', 'wpct'),
        ));
    }
}

// Initialize the class
new WP_Comment_Toolbox_Settings();
