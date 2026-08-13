# Login with Facebook — Custom WordPress Authentication Plugin

> **Lightweight OAuth 2.0 Social Login Plugin for WordPress**  
> *A clean, modular WordPress plugin enabling seamless 1-click user authentication via the Facebook Graph API.*

[![WordPress](https://img.shields.io/badge/WordPress-5.0%2B-21759B?style=flat-square&logo=wordpress&logoColor=white)](https://wordpress.org)
[![PHP](https://img.shields.io/badge/PHP-7.4%2B-777BB4?style=flat-square&logo=php&logoColor=white)](https://php.net)
[![OAuth 2.0](https://img.shields.io/badge/OAuth-2.0-3DDC84?style=flat-square&logo=openid&logoColor=white)](https://oauth.net/2/)

---

## 📌 Technical Motivation

Reducing login friction is critical for e-commerce conversion rates. **Login with Facebook** is an **independent custom WordPress plugin** built to integrate OAuth 2.0 social login directly into WordPress registration and WooCommerce checkout workflows without bloated third-party dependencies.

---

## ⚙️ Core Technical Features

1. **OAuth 2.0 Authentication Flow**
   - Redirects users securely to Facebook's OAuth 2.0 dialog (`https://www.facebook.com/v12.0/dialog/oauth`).
   - Exchanges authorization codes for Graph API access tokens.
   - Fetches verified profile data (`id`, `name`, `email`) to authenticate or auto-register WordPress users.
2. **Shortcode & Admin Integration**
   - Admin settings page (`admin.php?page=facebook-login`) for configuring App ID (Client ID) and App Secret.
   - Embeddable shortcodes for customizable login buttons anywhere in posts, pages, or sidebar widgets.
3. **Automatic Account Provisioning**
   - If an existing user matches the verified Facebook email, they are logged in immediately.
   - If no account exists, a new WordPress user is generated with a secure random password and logged in seamlessly.

---

## 🚀 Quick Start & Setup

1. Clone or download into your WordPress plugins directory:
   ```bash
   cd wp-content/plugins/
   git clone https://github.com/huyhhuy1410/facebook-login.git facebook-login
   ```
2. Activate **Login with Facebook** in **WordPress Admin $\rightarrow$ Plugins**.
3. Go to **Settings $\rightarrow$ Facebook Login** and enter your **Facebook App ID** and **App Secret** (obtained from [Meta for Developers](https://developers.facebook.com/)).
4. Add the shortcode to your login page or WooCommerce checkout:
   ```text
   [facebook_login_btn]
   ```

---

## 📄 License & Provenance Notice

Created by Vo Quang Huy for technical demonstration. Open-source and free of proprietary code.
