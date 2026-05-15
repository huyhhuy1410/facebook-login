# Facebook Login for WordPress

A WordPress plugin that adds Facebook-based login to the frontend and optionally to the WordPress admin login screen. It includes a settings page, shortcode rendering, JavaScript login flow, token verification, user creation, and avatar import.

## What It Does

- Adds a Facebook login button through shortcode.
- Optionally displays the Facebook login button on the WordPress login screen.
- Provides a WordPress admin settings page for Facebook App configuration.
- Verifies Facebook login tokens before creating or logging in users.
- Creates WordPress users from Facebook profile data when no matching user exists.
- Logs existing users in when the Facebook email matches an existing account.
- Imports and stores the Facebook profile image as the WordPress avatar metadata.
- Supports redirect configuration after successful login.

## Shortcodes

- `[facebook_login]` renders the frontend Facebook login button.
- `[admin_facebook_login]` renders the admin/login-screen button.

## Main Features

- Configurable App ID / client ID.
- Optional login redirect URL.
- Optional display on the WordPress admin login form.
- Custom class and ID settings for the rendered shortcode button.
- Admin tabs for guide, general settings, and usage.
- Lightweight notice system for settings validation and save feedback.

## Technical Notes

- Main class: `Facebook_Login`
- Login method class: `Facebook_Login_Method`
- Admin page class: `Facebook_Login_Admin_Page`
- Shortcode classes:
  - `facebook_login\Shortcodes\Facebook_Btn`
  - `facebook_login\Shortcodes\Admin_Facebook_Btn`
- AJAX action: `facebook_login`
- Frontend assets:
  - `assets/js/login.js`
  - `assets/css/login.css`
- Token verification endpoint: Facebook token info API.

## Installation

1. Upload the plugin folder to `wp-content/plugins/facebook-login`.
2. Activate the plugin in WordPress Admin.
3. Open the Facebook Login settings page.
4. Enter the Facebook App ID / client ID.
5. Add `[facebook_login]` to any page, template, or builder field where the login button should appear.

