<?php

namespace facebook_login\ShortCodes;

class Facebook_Btn
{
    private $class;
    private $id;
    public function __construct()
    {
        $settings = \Facebook_Login::get_settings();
        $this->id = $settings['id'];
        $this->class = $settings['class'];
        add_shortcode('facebook_login', array($this, 'callback'));
    }
    public function callback($atts)
    {
?>
        <div class="facebook-login <?php echo $this->class
                                    ?>" id="<?php echo $this->id
                                            ?>">
            <div class="facebook-login-btn">
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
