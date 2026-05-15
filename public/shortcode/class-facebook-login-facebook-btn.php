<?php

namespace facebook_login\ShortCodes;

class Admin_Facebook_Btn
{

    public function __construct()
    {
        add_shortcode('admin_facebook_login', array($this, 'callback'));
    }
    public function callback($atts)
    {

?>
        <div class="facebook-login">
            <div class="facebook-login-facebook-btn">
                <span class="icon">
                    <img src="<?php echo FACEBOOK_LOGIN_URL ?>assets/images/logo.png" loading="lazy" alt="" />
                </span>
                <span class="text"><?php echo __('Đăng nhập bằng Facebook', 'facebook-login'); ?></span>

            </div>
            <div class="facebook-login-response"></div>
        </div>
<?php
    }
}
