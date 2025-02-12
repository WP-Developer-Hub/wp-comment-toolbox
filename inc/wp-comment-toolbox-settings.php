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

        // Form Layout Section
        $wp_customize->add_section('wpct_Form', array(
            'title' => __('Comment Form Setting', 'wpct'),
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
            'section' => 'wpct_Form',
            'type' => 'select', // Use a select dropdown for options
            'choices' => array(
                'full_name' => __('Full Name', 'wpct'),
                'username' => __('Username', 'wpct'),
                'both' => __('Both', 'wpct'),
            ),
            'description' => __('Select what the placeholder should be for the author input field. This will be displayed as a hint to users when filling in their name.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_comment_form_layout', array(
            'type' => 'option',
            'default' => '[author] [email] [url] [comment] [cookies]',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $wp_customize->add_control('wpct_comment_form_layout', array(
            'label' => __('Comment Form Layout', 'wpct'),
            'section' => 'wpct_Form',
            'type' => 'textarea',
            'description' => __('Define the structure of the comment form using placeholders: <strong>[author]</strong>, <strong>[email]</strong>, <strong>[url]</strong>, <strong>[comment]</strong>, <strong>[cookies]</strong>.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_comment_form_cookies_msg', array(
            'type' => 'option',
            'default' => '[cookies_msg] [privacy_policy_link]',
            'sanitize_callback' => 'sanitize_textarea_field',
        ));

        $wp_customize->add_control('wpct_comment_form_cookies_msg', array(
            'label' => __('Comment Cookies Message', 'wpct'),
            'section' => 'wpct_Form',
            'type' => 'textarea',
            'description' => __('Customize the cookies message for the comment form. <br> - Use <strong>[cookies_msg]</strong> to include the default cookies text.<br> - Use <strong>[privacy_policy_link]</strong> to insert a link to your privacy policy page.', 'wpct'),
        ));

        // General Settings Section
        $wp_customize->add_section('wpct_spam_and_securty', array(
            'title' => __('Spam & Security Settings', 'wpct'),
            'panel' => 'wpct_comment_form_panel',
        ));

        $wp_customize->add_setting('wpct_comment_message_limit', array(
            'type' => 'option',
            'default' => '280',
            'sanitize_callback' => 'absint',
        ));

        $wp_customize->add_control('wpct_comment_message_limit', array(
            'label' => __('Comment Character Limit', 'wpct'),
            'section' => 'wpct_spam_and_securty',
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
            'section' => 'wpct_spam_and_securty',
            'type' => 'select',
            'choices' => array(
                '0' => __('Disable', 'wpct'),
                '1' => __('Enable', 'wpct'),
            ),
            'description' => __('When enabled, links in comments will not be clickable. This can help reduce spam and unwanted external links.', 'wpct'),
        ));

        $wp_customize->add_section('wpct_role', array(
            'title'    => __('Role Settings', 'wpct'),
            'panel'    => 'wpct_comment_form_panel',
        ));

        $wp_customize->add_setting('wpct_roles_enabled', array(
            'type' => 'option',
            'default' => '0',
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_roles_enabled', array(
            'label' => __('Show user role next to user Name', 'wpct'),
            'section' => 'wpct_role',
            'type' => 'select',
            'choices' => array(
                '0' => __('Hide', 'wpct'),
                '1' => __('Show', 'wpct'),
            ),
            'description' => __('Enable this setting to display the user role (e.g., Admin, Subscriber) next to the comment author’s name.', 'wpct'),
        ));

        // Add more settings under 'Role Setting' section
        $wp_customize->add_setting('wpct_guest_label', array(
            'type' => 'option',
            'default' => __('Guest', 'wpct'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_guest_label', array(
            'label' => __('Guest Label', 'wpct'),
            'section' => 'wpct_role',
            'type' => 'text',
            'description' => __('Set the label for guest users. This will be displayed next to their name in the comment section.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_subscriber_label', array(
            'type' => 'option',
            'default' => __('Subscriber', 'wpct'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_subscriber_label', array(
            'label' => __('Subscriber Label', 'wpct'),
            'section' => 'wpct_role',
            'type' => 'text',
            'description' => __('Set the label for subscriber users. This will be displayed next to their name in the comment section.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_admin_label', array(
            'type' => 'option',
            'default' => __('Admin', 'wpct'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_admin_label', array(
            'label' => __('Admin Label', 'wpct'),
            'section' => 'wpct_role',
            'type' => 'text',
            'description' => __('Set the label for admin users. This will be displayed next to their name in the comment section.', 'wpct'),
        ));

        $wp_customize->add_setting('wpct_admin_author_label', array(
            'type' => 'option',
            'default' => __('Admin/Author', 'wpct'),
            'sanitize_callback' => 'sanitize_text_field',
        ));

        $wp_customize->add_control('wpct_admin_author_label', array(
            'label' => __('Admin Author Label', 'wpct'),
            'section' => 'wpct_role',
            'type' => 'text',
            'description' => __('Set the label for admin authors. This will be displayed next to their name in the comment section.', 'wpct'),
        ));

        // Toolbar Settings Section
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
