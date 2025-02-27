# WP Comment Toolbox Settings

This plugin provides customization options for comment form settings in WordPress. You can adjust various aspects of comment behavior, including security, formatting, and layout preferences.

## Features

- **Comment Settings Panel**: Manage all comment-related settings from one place.
- **Spam & Security**: Control comment length, disable clickable links to reduce spam, and more.
- **Comment List**: Customize the visibility and format of author links, including the link type (internal/external) and comment text formatting.
- **Comment Form Layout**: Define the structure of the comment form using placeholders.
- **Quick Tags Toolbar**: Enable a quick tags toolbar in the comment form to make it easier for users to format their comments.

## Installation

1. Upload the plugin files to the `/wp-content/plugins/wp-comment-toolbox-settings/` directory.
2. Activate the plugin through the 'Plugins' menu in WordPress.
3. Go to **Appearance** > **Customize** to configure the plugin settings.

## Settings

### Spam & Security

- **Comment Character Limit**: Set the maximum number of characters allowed in a comment.
- **Disable Clickable Links**: Prevent links in comments from being clickable to reduce spam.
- **Enable Spam Protection**: Prevent bots from sending comments via `wp-comments-post.php`, which can reduce spam & improve security.

### Comment List

- **Author Link Visibility**: Choose who can have a clickable author link in comments.
- **Author Link Type**: Select whether the author link leads to the author’s website or the WordPress author page.
- **Format Comment Text**: Choose the format for comment text:
  - **Auto**: Applies `wpautop` for paragraph tags.
  - **nl2br**: Converts newlines to `<br>` tags.
  - **None**: No formatting.

### Comment Form

- **Author Placeholder**: Choose the placeholder for the author input field (Full Name, Username, or Both).
- **Comment Textarea Row Count**: Set the number of rows in the comment textarea.
- **Comment Form Layout**: Define the structure of the comment form using placeholders (`[author]`, `[email]`, `[url]`, `[comment]`, `[cookies]`).
- **Comment Cookies Message**: Customize the message for cookie consent in the comment form.

### Extra Settings

- **Enable Quick Tags Toolbar**: Add a quick tags toolbar to the comment form for easier formatting with Bold, Italic, and Underline text.
- **Toolbar Style**: Choose between a light or dark mode for the quick tags toolbar.
- **Enable Character Count**: Add a Twitter-style character counter below the comment textarea, showing the number of characters remaining.

## Customizer Options

- **Comment Character Limit**: Control the maximum number of characters in a comment.
- **Comment Text Format**: Define how comment text is formatted (`wpautop`, `nl2br`, or no formatting).
- **Comment Form Layout**: Structure the comment form using placeholders.
- **Quick Tags Toolbar**: Enable or disable a toolbar for comment formatting.




## Credit
- Elazar for the WP Spam Honeypot idea: https://github.com/elazar/wp-spam-honeypot