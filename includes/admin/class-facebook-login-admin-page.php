<?php
if (! defined('ABSPATH')) {
	exit;
}
require_once FACEBOOK_LOGIN_DIR . 'includes/notice/class-notice.php';
/**
 * Create the admin page under wp-admin -> WooCommerce -> Woo Viet
 */
class Facebook_Login_Admin_Page
{
	/**
	 * @var string The message to display after saving settings
	 */
	public $message = '';
	private $notices;
	/**
	 * Facebook_Login_Admin_Page constructor.
	 */
	public function __construct()
	{
		$this->notices = new facebook_login\Notice();
		// Catch and run the save_settings() action
		if (isset($_REQUEST['facebook_login_nonce']) && isset($_REQUEST['action']) && 'facebook_login_save_settings' == $_REQUEST['action']) {
			$this->save_settings();
		}
		add_action('admin_menu', array($this, 'register_submenu_page'));
	}
	/**
	 * Save settings for the plugin
	 */
	public function save_settings()
	{
		$settings = $_REQUEST['settings'];
		if (wp_verify_nonce($_REQUEST['facebook_login_nonce'], 'facebook_login_save_settings')) {
			update_option('facebook-login', $this->sanitize_settings($settings));
			$this->notices->add_notice(__('Cài đặt đã được lưu.', 'facebook-login'), 'updated');
		} else {
			$this->notices->add_notice(__('Không thể lưu! Vui lòng thử tải lại trang.', 'facebook-login'), 'error');
		}
		// Example validation checks
		if (empty($settings['client_id'])) {
			$this->notices->add_notice(__('App ID chưa hợp lệ.', 'facebook-login'), 'error');
		}
		// if (empty($settings['client_secret'])) {
		// 	$this->notices->add_notice(__('Client Secret chưa hợp lệ.', 'facebook-login'), 'error');
		// }
	}
	/**
	 * Register the sub-menu under "WooCommerce"
	 * Link: http://my-site.com/wp-admin/admin.php?page=facebook-login
	 */
	public function register_submenu_page()
	{
		add_submenu_page(
			'options-general.php',          // Parent slug
			__('Đăng nhập bằng Facebook', 'facebook-login'),
			__('Đăng nhập bằng Facebook', 'facebook-login'),
			'manage_options',
			'facebook-login',
			array($this, 'admin_page_html'),
		);
	}
	/**
	 * Generate the HTML code of the settings page
	 */
	public function admin_page_html()
	{
		// check user capabilities
		if (! current_user_can('manage_options')) {
			return;
		}
		$settings = Facebook_Login::get_settings();
		// Tab navigation
		$active_tab = isset($_GET['tab']) ? $_GET['tab'] : 'general';
?>
		<div class="wrap">
			<h1><?php echo esc_html(get_admin_page_title()); ?></h1>
			<?php $this->notices->display_notices(); ?>
			<h2 class="nav-tab-wrapper">
				<a href="?page=facebook-login&tab=guide" class="nav-tab <?php echo $active_tab === 'guide' ? 'nav-tab-active' : ''; ?>">
					<?php echo __('Hướng dẫn', 'facebook-login'); ?>
				</a>
				<a href="?page=facebook-login&tab=general" class="nav-tab <?php echo $active_tab === 'general' ? 'nav-tab-active' : ''; ?>">
					<?php echo __('Cài đặt', 'facebook-login'); ?>
				</a>
				<a href="?page=facebook-login&tab=usage" class="nav-tab <?php echo $active_tab === 'usage' ? 'nav-tab-active' : ''; ?>">
					<?php echo __('Sử dụng', 'facebook-login'); ?>
				</a>
			</h2>
			<?php
			// Display content based on the selected tab
			if ($active_tab === 'general') {
				$this->render_general_settings($settings);
			} elseif ($active_tab === 'guide') {
				$this->render_guide();
			} elseif ($active_tab === 'usage') {
				$this->render_usage($settings);
			}
			?>
		</div>
	<?php
	}
	private function render_general_settings($settings)
	{
	?>
		<div class="gg-container" style="max-width: 65em;">
			<div id="facebook-login-header"
				style="padding: 5px;">
				<p><strong>
						<?php
						printf(
							__('Vui lòng xem hướng dẫn <a href="%1$s" target="_blank">tại đây</a> và nhập đầy đủ thông tin để phương thức đăng nhập hoạt động.', 'mone-admin'),
							'?page=facebook-login&tab=guide',
						)
						?>
					</strong></p>
			</div>
			<form method="post">
				<?php echo $this->message; ?>
				<input type="hidden" id="action" name="action" value="facebook_login_save_settings">
				<input type="hidden" id="facebook_login_nonce" name="facebook_login_nonce" value="<?php echo wp_create_nonce('facebook_login_save_settings'); ?>">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php _e('App ID - <em>(Bắt buộc)</em>', 'facebook-login'); ?></th>
							<td>
								<input name="settings[client_id]" type="text" value="<?php echo esc_attr($settings['client_id']); ?>" class="regular-text">
							</td>
						</tr>
						<!-- <tr>
							<th scope="row"><?php _e('Client Secret - <em>(Bắt buộc)</em>', 'facebook-login'); ?></th>
							<td>
								<input name="settings[client_secret]" type="text" value="<?php //echo esc_attr($settings['client_secret']); 
																							?>" class="regular-text">
							</td>
						</tr> -->
						<tr>
							<th scope="row"><?php _e('Redirect URL', 'facebook-login'); ?></th>
							<td>
								<input name="settings[login_redirect]" type="text" value="<?php echo esc_attr($settings['login_redirect']); ?>" class="regular-text">
								<p><?php echo __('Nhập URL muốn chuyển hướng sau khi đăng nhập. <br/>Mặc định:<strong> ' . get_site_url() . '</strong>', 'facebook-login'); ?></p>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Đăng nhập Admin', 'facebook-login'); ?></th>
							<td>
								<input name="settings[show_facebook_button]" type="checkbox" value="yes" <?php checked('yes', $settings['show_facebook_button']); ?> />
								<p><?php echo __('Hiển thị nút đăng nhập bằng Facebook ở trang đăng nhập Admin', 'facebook-login'); ?></p>
							</td>
						</tr>
					</tbody>
				</table>
				<input name="settings[id]" type="hidden" value="<?php echo esc_attr($settings['id']); ?>" class="regular-text">
				<input name="settings[class]" type="hidden" value="<?php echo esc_attr($settings['class']); ?>" class="regular-text">

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Lưu thay đổi', 'facebook-login'); ?>">
				</p>
			</form>
		</div>
	<?php
	}
	private function render_usage($settings)
	{
	?>
		<div class="gg-container" style="max-width: 65em;">
			<div id="facebook-login-header"
				style="padding: 5px;">
				<h2><?php echo __('Shortcode nút đăng nhập Facebook', 'facebook-login'); ?><h4>

			</div>
			&nbsp;
			<textarea readonly>[facebook_login]</textarea>
			<p><strong>
					<?php
					echo  __('Shortcode chỉ hiển thị khi người dùng chưa đăng nhập.', 'facebook-login')
					?>
				</strong></p>
			<form method="post">
				<?php echo $this->message; ?>
				<input type="hidden" id="action" name="action" value="facebook_login_save_settings">
				<input type="hidden" id="facebook_login_nonce" name="facebook_login_nonce" value="<?php echo wp_create_nonce('facebook_login_save_settings'); ?>">
				<table class="form-table">
					<tbody>
						<tr>
							<th scope="row"><?php _e('Tuỳ chỉnh Class', 'facebook-login'); ?></th>
							<td>
								<input name="settings[class]" type="text" value="<?php echo esc_attr($settings['class']); ?>" class="regular-text">
								<p><?php echo __('Thêm class cho shortcode nút đăng nhập<br/>
								<strong>Ví dụ:</strong>', 'facebook-login'); ?></p><textarea readonly>class-1 class-2 class-3</textarea>
							</td>
						</tr>
						<tr>
							<th scope="row"><?php _e('Tuỳ chỉnh ID', 'facebook-login'); ?></th>
							<td>
								<input name="settings[id]" type="text" value="<?php echo esc_attr($settings['id']); ?>" class="regular-text">
								<p><?php echo __('Thêm ID cho shortcode nút đăng nhập', 'facebook-login'); ?></p>
							</td>
						</tr>
						<!-- <tr>
							<th scope="row"><?php //_e('Redirect', 'facebook-login'); 
											?></th>
							<td>
								<input name="settings[redirect]" type="text" value="<?php //echo esc_attr($settings['redirect']); 
																					?>" class="regular-text">
							</td>
						</tr> -->
					</tbody>
				</table>
				<input name="settings[client_id]" type="hidden" value="<?php echo esc_attr($settings['client_id']); ?>" class="regular-text">
				<input name="settings[login_redirect]" type="hidden" value="<?php echo esc_attr($settings['login_redirect']); ?>" class="regular-text">
				<input name="settings[show_facebook_button]" type="hidden" value="yes" <?php checked('yes', $settings['show_facebook_button']); ?> />

				<p class="submit">
					<input type="submit" name="submit" id="submit" class="button button-primary" value="<?php echo __('Lưu thay đổi', 'facebook-login'); ?>">
				</p>
			</form>
		</div>
	<?php
	}
	// Function to render guide
	private function render_guide()
	{
	?>
		<div class="gg-container" style="max-width: 65em;">
			<h2><?php _e('Hướng dẫn sử dụng', 'facebook-login'); ?></h2>
			<p>
				<?php _e('Để cho phép người truy cập đăng nhập bằng tài khoản Facebook của họ, trước tiên bạn phải tạo Ứng dụng Facebook. Sau đó chuyển đến <strong>"Cài đặt"</strong> và cấu hình <strong>"App ID"</strong> theo hướng dẫn.', 'facebook-login'); ?>
			</p>
			
			<h3><?php _e('Tạo ứng dụng Facebook', 'facebook-login'); ?></h3>
			<ol>
				<li><?php _e('Truy cập vào', 'facebook-login'); ?> <a href="https://developers.facebook.com/apps/" target="_blank">https://developers.facebook.com/apps/</a></li>
				<li><?php _e('Đăng nhập với tài khoản Facebook của bạn nếu bạn chưa đăng nhập.', 'facebook-login'); ?></li>
				<li><?php _e('Nhấp vào nút <strong>"Tạo ứng dụng"</strong> và chọn <strong>"Tiếp"</strong>. Sau đó chọn <strong>"Xác thực và yêu cầu dữ liệu từ người dùng qua phương thức Đăng nhập bằng Facebook"</strong> và nhấn <strong>"Tiếp"</strong>!', 'facebook-login'); ?></li>
				<li><?php _e('Điền các trường <strong>"Tên ứng dụng"</strong> và <strong>"Email liên hệ ứng dụng"</strong>.', 'facebook-login'); ?></li>
				<li><?php _e('Nhấp vào nút <strong>"Tạo ứng dụng"</strong> và hoàn thành kiểm tra bảo mật.', 'facebook-login'); ?></li>
				<li><?php _e('Lúc này ở trong Bảng điều khiển ứng dụng. Nhấp vào tab <strong>"Trường hợp sử dụng"</strong> ở bên trái, sau đó nhấp vào nút <strong>"Tùy chỉnh"</strong> xuất hiện bên cạnh mục <strong>"Xác thực và yêu cầu dữ liệu qua Đăng nhập Facebook"</strong>.', 'facebook-login'); ?></li>
				<li><?php _e('Dưới phần "Quyền", tìm quyền "email" và nhấp vào nút "Thêm".', 'facebook-login'); ?></li>
				<li><?php _e('Nhấn nút <strong>"Settings"</strong> mà bạn có thể tìm thấy bên dưới phần "Facebook Login".', 'facebook-login'); ?></li>

				<li><?php _e('Bật tuỳ chọn <strong>"Đăng nhập bằng JavaScript SDK"</strong> và thêm URL sau vào trường <strong>"Miền được phép cho JavaScript SDK"</strong>:', 'facebook-login'); ?></li>
				<ul>
					<li><strong><?php echo get_site_url() ?></strong></li>
				</ul>
				<li><?php _e('Nhấp vào nút <strong>"Lưu thay đổi"</strong>. (Nếu bạn nhận được trang trắng sau khi nhấn nút "Lưu thay đổi", bạn có thể cần làm mới trang.)', 'facebook-login'); ?></li>
				<li><?php _e('Ở bên trái, nhấp vào tab <strong>"Cài đặt ứng dụng" (Hình bánh răng) </strong>, sau đó nhấp vào <strong>"Thông tin cơ bản"</strong>.', 'facebook-login'); ?></li>
				<li><?php _e('Nhập tên miền của bạn vào trường <strong>"Miền ứng dụng"</strong>, có thể là:', 'facebook-login'); ?> </li>
				<ul>
					<li><strong><?php echo get_site_url() ?></strong></li>
				</ul>
				<li><?php _e('Điền vào trường <strong>"URL chính sách quyền riêng tư"</strong>. Cung cấp một trang chính sách bảo mật của bạn.', 'facebook-login'); ?></li>
				<li><?php _e('Tại <strong>"Xóa dữ liệu người dùng"</strong>, chọn tùy chọn <strong>"URL Hướng dẫn xóa dữ liệu"</strong> và nhập URL của trang bạn* với hướng dẫn về cách người dùng có thể xóa tài khoản của họ trên trang của bạn.', 'facebook-login'); ?></li>
				<li><?php _e('Chọn một <strong>"Hạng mục", một "Biểu tượng ứng dụng"</strong>.', 'facebook-login'); ?></li>
				<li><?php _e('Cuộn xuống dưới cùng của trang, nhấn nút <strong>"+ Thêm nền tảng"</strong>.', 'facebook-login'); ?></li>
				<li><?php _e('Chọn nền tảng <strong>"Website"</strong>, sau đó nhấn <strong>"Tiếp"</strong> và nhập URL sau vào trường <strong>"URL trang web"</strong>:', 'facebook-login'); ?> </li>
				<ul>
					<li><strong><?php echo get_site_url() ?></strong></li>
				</ul>
				<li><?php _e('Nhấn nút <strong>"Lưu thay đổi"</strong>.', 'facebook-login'); ?></li>
				<li><?php _e('Để có Quyền truy cập Nâng cao, bạn sẽ cần trải qua quá trình Xác minh Doanh nghiệp, mà bạn có thể bắt đầu trên tab <strong>"Xét duyệt > Xác minh"</strong> ở bên trái.', 'facebook-login'); ?></li>
				<li><?php _e('Hiện tại ứng dụng của bạn đang ở chế độ Phát triển, nghĩa là người ngoài sẽ không sử dụng được. Bạn cần truy cập vào tab <strong>"Xét duyệt > Xét duyệt ứng dụng"</strong> và mở yêu cầu xem xét với nút <strong>"Chỉnh sửa"</strong>. Điền vào bất kỳ trường nào còn thiếu ở đây, sau đó gửi biểu mẫu để xem xét, và chờ cho đến khi được Meta chấp thuận, có thể mất vài ngày.', 'facebook-login'); ?></li>
				<li><?php _e('Sau khi xác minh hoàn tất, nhấp vào tab <strong>"Đăng"</strong> và đăng ứng dụng của bạn. Trước khi nhấn, nên kiểm tra các bước được liệt kê trên trang "Đăng", nếu bạn đã cấu hình đúng mọi thứ.', 'facebook-login'); ?></li>
				<li><?php _e('Sau khi tất cả đã hoàn thành, nhấp vào tab <strong>"Cài đặt ứng dụng"</strong>, sau đó nhấp vào <strong>"Thông tin cơ bản"</strong>.', 'facebook-login'); ?></li>
				<li><?php _e('Ở đầu trang, bạn có thể tìm thấy <strong>"App ID"</strong> của mình. Sao chép nó để nhập vào phần cài đặt bắt buộc của plugin', 'facebook-login'); ?></li>
			</ol>

			<p><strong><?php _e('CẢNH BÁO:', 'facebook-login'); ?></strong> <u><?php _e('Không thay thế ứng dụng Facebook của bạn bằng ứng dụng khác!', 'facebook-login'); ?></u> <?php _e('Vì người dùng WordPress đã liên kết tài khoản Facebook chỉ có thể đăng nhập bằng ứng dụng Facebook đã được sử dụng lúc liên kết.', 'facebook-login'); ?> </p>
		</div>
<?php
	}
	/**
	 * Sanitize admin settings.
	 *
	 * @param  array  $settings User input settings.
	 *
	 * @return array
	 */
	private function sanitize_settings(array $settings): array
	{
		$sanitized_settings = array();
		// error_log(print_r($settings));
		foreach ($settings as $feature => $value) {
			// foreach ($feature_options as $option => $value) {
			$sanitized_settings[$feature] = esc_html(sanitize_text_field($value));
			// }
		}
		return $sanitized_settings;
	}
	private function get_domain_url($url)
	{
		$parsed_url = parse_url($url);
		$host = preg_replace('/^www\./', '', $parsed_url['host']);
		return $host;
	}
}
?>