# Login with Facebook — Custom WordPress Plugin

> **Facebook JS SDK Social Login Plugin for WordPress**  
> *A WordPress plugin that signs users in with a one-click Facebook button and provisions or reuses the matching WordPress account.*

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-21759B?style=flat-square&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![Facebook](https://img.shields.io/badge/Facebook-JS%20SDK-1877F2?style=flat-square&logo=facebook&logoColor=white)](https://developers.facebook.com/)

---

## 📌 Technical Motivation

Reducing login friction matters on e-commerce checkout and registration pages. **Login with Facebook** is an **independent custom WordPress plugin** that adds a one-click Facebook button using the official JavaScript SDK, without a heavyweight third-party plugin.

---

## ⚙️ Core Technical Features

1. **Facebook JS SDK Login**
   - Loads the Meta JavaScript SDK (`connect.facebook.net/.../sdk.js`, Graph API v21) and calls `FB.login()` with the `public_profile, email` scopes.
   - On success it calls `FB.api('/me', { fields: 'name, email, picture' })` and posts the response to `admin-ajax.php` with action `facebook_login`.
2. **Automatic Account Provisioning**
   - The handler looks up an existing WordPress user by the email in the Facebook response. If found, it signs that user in with `wp_set_auth_cookie()`.
   - If not found, it creates one with `wp_insert_user()` and a random 12-character password, then signs it in. Users who do not expose an email get a placeholder `fb_user_<id>@noemail.com` address.
3. **Avatar Sideloading**
   - The Facebook picture is downloaded with `download_url()` and added to the media library with `media_handle_sideload()`. The stored source URL is compared against the `facebook_avatar_compare` user meta so the avatar is only re-fetched when Facebook returns a new picture.
4. **Admin Settings & Shortcodes**
   - A settings page under **Facebook Login** for the App ID and the redirect URL. The settings form is nonce-protected.
   - Shortcodes `[facebook_login]` (front-end button) and `[admin_facebook_login]` (button injected into the `wp-login.php` form).

---

## 🚀 Quick Start & Setup

1. Clone or download into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/huyhhuy1410/facebook-login.git facebook-login
   ```
2. Activate **Login with Facebook** in **WordPress Admin $\rightarrow$ Plugins**.
3. Go to **Facebook Login** and enter your **Facebook App ID** (from [Meta for Developers](https://developers.facebook.com/)) and the redirect URL.
4. Add the shortcode to your login page or WooCommerce checkout:
   ```text
   [facebook_login]
   ```

---

## ⚠️ Known Limitations

Read this before deploying. The first item is a real vulnerability, not a style preference.

* **The server does not verify the Facebook identity.** The `wp_ajax_nopriv_facebook_login` handler trusts the email that arrives in the POST body. The `client_secret` is not used: the property, the settings check, and the admin field are all commented out, and a `verify_facebook_token()` helper exists but is never called from the login path. Anyone who can POST to `admin-ajax.php` with a known email address can sign in as that WordPress user. The fix is to send the Facebook access token with the request, call `verify_facebook_token()` (or the Graph `/me` endpoint) server-side, and provision the user from the verified response instead of the client payload. The Google Login plugin in this workspace already does this correctly and is a useful reference.
* **No provider-id account linking.** The plugin matches users by email only. A user who changes their Facebook email is treated as a new account, and there is no `facebook_user_id` column on the WordPress user to link against.
* **No automated tests.** The plugin ships no PHPUnit or integration test suite.
* **Avatar downloads are unvalidated.** The picture URL is fetched with `download_url()` without an explicit content-type or size check before being sideloaded into the media library.

---

## 🤝 Contributing

Contributions, bug reports, and feature proposals are welcome! Feel free to open an issue or submit a Pull Request.

---

## 📄 License & Provenance Notice

Created by Vo Quang Huy for technical demonstration. Open-source and free of proprietary code.
