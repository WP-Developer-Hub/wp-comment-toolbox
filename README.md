# WP Comment Toolbox

A comprehensive WordPress plugin that gives you fine-grained control over the comment system — spam protection, privacy, comment list display, form layout, extras, and admin tools.

## Installation Guide

### 1. Download the Plugin
1. **Click** the green **Code** button → **Download ZIP** if you want the lasted changes.
2. **Click** the **Releases Page** link to **Download** the latest Stable **Releases ZIP** .
---
### 2. Install & Activate
1. Log in to your WordPress Admin Dashboard.
2. Go to **Plugins → Add New Plugin**.
3. Click **Upload Plugin** at the top.
4. Choose the downloaded `.zip` file and click **Install Now**.
5. After installation, click **Activate Plugin**.
---
### 3. Access Settings
#### Once activated, go to **Comments → Comment Settings** in the admin sidebar to configure the plugin.
---
### Tabs & Features
### 1. Spam & Security
- **Comment Character Limit** — Set the maximum number of characters allowed per comment.
- **Comment Flood Delay** — Minimum wait time between comments from the same user.
- **Disable Clickable Links** — Strip or disable hyperlinks in comments.
- **Sanitize Comments** — Enforce safe HTML tags and strip unwanted HTML from fields.
- **Enable Spam Protection** — Block bot POST requests to `wp-comments-post.php` via honeypot.
- **Submit Button Name** — Customize the submit button attribute name used by the honeypot check.
- **Enable Math Captcha** — Require users to solve a simple math question before submitting.
- **Math Captcha Difficulty Level** — Adjust difficulty (1–15).
---
### 2. Privacy
- **Comment Cookie Lifetime** — Control how long author information is stored in cookies.
---
### 3. Comment List
- **Author Link Visibility** — Control who gets a clickable author link.
- **Author Link Type** — Choose between external site links or internal author archive pages.
- **Format Comment Text** — Choose formatting rule: `Auto` / `wpautop`, `nl2br`, or `None`.
- **Enable Twitter Mentions Linking** — Automatically turn `@username` mentions into links.
---
### 4. Comment Form
- **Enable HTML5 Validation** — Toggle native HTML5 form validation.
- **Author Placeholder** — Choose the placeholder type for the author field.
- **Comment Placeholder** — Custom placeholder text for the comment textarea.
- **Comment Textarea Height** — Set the visible height / number of rows.
- **Comment Notes Before** — Custom HTML/content shown before the form.
- **Comment Notes After** — Custom HTML/content shown after the form.
- **Comment Form Layout** — Reorder form components using: `[author]`, `[email]`, `[url]`, `[comment]`, `[cookies]`.
- **Comment Cookies Message** — Customize the cookie compliance notice text.
---
### 5. Extras
- **Enable Quick Tags Toolbar** — Inline formatting bar (Bold, Italic, etc.) above the comment field.
- **Toolbar Style** — Light Mode or Dark Mode.
- **Enable Character Count** — Real-time counter showing remaining characters.
- **Disable Public Commenting** — Toggle commenting availability per post type (`Post`, `Cam`, `User Post`, `Video`, `Gallery`, `Product`, `Code Snippet`, etc.).
---
### 6. Admin
- **Show Block IP Actions** — Adds a “Block IP” row action to the admin comments list.
- **Enable All Sus Comments Button** — One-click scan/nuke button based on moderation key / link criteria.
- **Disable Formatting** — Prevent HTML execution in the admin area for safer analysis.
- **Enhance Comment List Tables** — Optional extra columns: `Comment Type`, `Comment Author Role`, `Comment ID`.

### 7. Test Command
- **Spam Test Command** — Generates a ready-to-use `curl` command so you can test the honeypot / spam protection from the terminal.
---
## Credits
- Honeypot concept inspired by [Elazar’s WP Spam Honeypot](https://github.com/elazar/wp-spam-honeypot).

## License
GPL-3.0 (or later)
