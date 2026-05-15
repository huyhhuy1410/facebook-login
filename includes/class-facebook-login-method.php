<?php
if (!defined('ABSPATH')) {
	exit;
}
class Facebook_Login_Method
{
	private $client_id;
	// private $client_secret;
	private $redirect_url;
	private $show_admin;
	public function __construct() {}
	public function __call_action($client_id, $client_secret, $redirect_url, $show_admin)
	{
		$this->client_id = sanitize_text_field($client_id);
		// $this->client_secret = sanitize_text_field($client_secret);
		$this->redirect_url = esc_url_raw($redirect_url);
		$this->show_admin = sanitize_text_field($show_admin);
		add_action('wp_head', [$this, 'url_facebook_dev']);
		// add_action('wp_head', [$this, 'url_header']);
		add_action('wp_enqueue_scripts', [$this, 'facebook_login_style']);
		add_action('wp_ajax_nopriv_facebook_login', [$this, 'facebook_ajax_login']);
		// add_action('wp_ajax_nopriv_facebook_ajax_social_data', [$this, 'facebook_ajax_social_data']);
		if ($this->show_admin === 'yes') {
			add_action('login_head', [$this, 'add_facebook_login_head']);
			add_action('login_enqueue_scripts', [$this, 'facebook_login_style']);
			add_action('login_form', [$this, 'add_facebook_login_to_login_form']);
		}
	}
	public function url_facebook_dev()
	{
		echo '<div id="fb-root"></div>';
		echo '<script async defer crossorigin="anonymous" src="https://connect.facebook.net/vi_VN/sdk.js#xfbml=1&version=v21.0&appId=' . $this->client_id . '"></script>';
?>
		<script>
			// (function(d, s, id) {
			// 	var js, fjs = d.getElementsByTagName(s)[0];
			// 	if (d.getElementById(id)) {
			// 		return;
			// 	}
			// 	js = d.createElement(s);
			// 	js.id = id;
			// 	js.src = "https://connect.facebook.net/en_US/sdk.js";
			// 	fjs.parentNode.insertBefore(js, fjs);
			// }(document, 'script', 'facebook-jssdk'));
			var ajaxURL = '<?php echo esc_url(admin_url('admin-ajax.php')); ?>';
			window.fbAsyncInit = function() {
				FB.init({
					appId: '<?php echo $this->client_id ?>',
					cookie: true, // Enable cookies to allow the server to access the session.
					xfbml: true, // Parse social plugins on this webpage.
					version: 'v21.0' // Use this Graph API version for this call.
				});
			};
		</script>
<?php
	}
	public function facebook_login_style()
	{
		wp_enqueue_style('facebook-login-css', FACEBOOK_LOGIN_URL . 'assets/css/login.css', [], false);
		wp_enqueue_script('facebook-login-js', FACEBOOK_LOGIN_URL . 'assets/js/login.js', [], false, true);
	}
	public function add_facebook_login_head()
	{
		// $this->url_header();
		$this->url_facebook_dev();
	}
	public function add_facebook_login_to_login_form()
	{
		echo do_shortcode('[admin_facebook_login]');
	}
	// public function facebook_ajax_social_data()
	// {
	// 	$data = sanitize_text_field($_POST['code']);
	// 	if (sanitize_text_field($_POST['type']) === 'facebook') {
	// 		$data = [
	// 			'id' => sanitize_text_field($data['id']),
	// 			'user_name' => sanitize_text_field($data['name']),
	// 			'user_email' => sanitize_email($data['email']),
	// 		];
	// 	}
	// 	$email = $data['user_email'];
	// 	$name = $data['user_name'];
	// 	$fbid = $data['id'];
	// 	if ($fbid) {
	// 		$user = get_user_by('email', $email) ?: get_user_by('login', 'facebook_social_' . $fbid);
	// 		if ($user) {
	// 			wp_clear_auth_cookie();
	// 			wp_set_auth_cookie($user->ID, true);
	// 			do_action('wp_login', $user->user_login, $user);
	// 			wp_send_json_success(['message' => __('Login successful, redirecting shortly...', 'facebook-login'), 'url' => get_author_posts_url($user->ID)]);
	// 		} else {
	// 			wp_send_json_success(['status' => 'register', 'message' => ['email' => $email, 'name' => $name, 'login' => 'social_' . $fbid]]);
	// 		}
	// 	} else {
	// 		wp_send_json_error(['message' => __('An error occurred while logging in. Please try again later.', 'facebook-login')]);
	// 	}
	// }
	public function facebook_ajax_login()
	{
		$payload = $this->sanitize_array($_POST['data']);
		// error_log(print_r($payload));
		if (!empty($payload['email']) || !empty($payload['id'])) {
			$email = !empty($payload['email']) ? sanitize_email($payload['email']) : '';
			$name = !empty($payload['name']) ? sanitize_text_field($payload['name']) : '';
			// error_log($name);
			// Get the user avatar URL from the payload (if available)
			$avatar_url = isset($payload['picture']['data']['url']) && !empty($payload['picture']['data']['url']) ? sanitize_url($payload['picture']['data']['url']) : '';
			// error_log($avatar_url);
			$unique_id = $email ? $email : 'fb_user_' . sanitize_text_field($payload['id']) . '@noemail.com';
			$user = get_user_by('email', $email);

			if (!$user) {
				$user_id = wp_insert_user([
					'user_login' => $unique_id, // Use email if available, otherwise fallback to fb_user_id
					'user_pass'  => wp_generate_password(12, false),
					'user_email' => $email ?: $unique_id, // Provide a dummy email if none exists
					'display_name' => $name,
					'first_name' => $name, // Optionally split the name if needed
				]);

				if (is_wp_error($user_id)) {
					wp_send_json_error(['message' => __('Không thể tạo tài khoản.', 'facebook-login')]);
				}
				$user = get_user_by('id', $user_id);
			}

			// Check the stored avatar URL for comparison
			$stored_avatar_url = get_user_meta($user->ID, 'facebook_avatar_compare', true);

			// Upload the avatar from the URL
			if ($avatar_url && $stored_avatar_url !== $avatar_url) {
				$avatar_id = $this->upload_avatar_from_url($avatar_url, 'facebook_avatar_' . $user->ID);
				if ($avatar_id) {
					update_user_meta($user->ID, 'facebook_avatar', $avatar_id);
					update_user_meta($user->ID, 'facebook_avatar_compare', $avatar_url);
				}
			}

			wp_set_current_user($user->ID);
			wp_set_auth_cookie($user->ID);
			wp_send_json_success(['redirect_url' => esc_url($this->redirect_url)]);
		} else {
			wp_send_json_error(['message' => __('Đăng nhập Facebook thất bại.', 'facebook-login')]);
		}
	}
	public function upload_avatar_from_url($url, $filename = '', $title = null)
	{
		// require_once(ABSPATH . "/wp-load.php");
		// require_once(ABSPATH . "/wp-admin/includes/image.php");
		require_once(ABSPATH . "/wp-admin/includes/file.php");
		require_once(ABSPATH . "/wp-admin/includes/media.php");
		$tmp = download_url(esc_url_raw($url));
		// Check if the download was successful
		if (is_wp_error($tmp)) {
			error_log('Failed to download image: ' . $tmp->get_error_message());
			return false; // Early exit if download fails
		}
		$filename = sanitize_title($filename ?: pathinfo($url, PATHINFO_FILENAME));
		// $extension = pathinfo($url, PATHINFO_EXTENSION);
		$args = ['name' => "$filename.jpg", 'tmp_name' => $tmp];
		$attachment_id = media_handle_sideload($args, 0, $title);
		if (is_wp_error($attachment_id)) {
			error_log('Failed to upload image: ' . $attachment_id->get_error_message());
			return false; // Return false if upload fails
		}
		@unlink($tmp);
		return is_wp_error($attachment_id) ? false : $attachment_id;
	}
	public function verify_facebook_token($token)
	{
		$url = 'https://oauth2.facebookapis.com/tokeninfo?id_token=' . sanitize_text_field($token);
		$response = wp_remote_get($url);
		if (is_wp_error($response)) {
			return false;
		}
		return json_decode(wp_remote_retrieve_body($response), true);
	}
	public function sanitize_array($array)
	{
		foreach ($array as $key => $value) {
			if (is_array($value)) {
				$array[$key] = $this->sanitize_array($value); // Recursively sanitize nested arrays
			} else {
				$array[$key] = sanitize_text_field($value);
			}
		}
		return $array;
	}
}
