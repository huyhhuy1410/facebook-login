<?php

namespace facebook_login;

class Notice
{
    private $notices;

    public function add_notice($message, $type = 'success')
    {
        $this->notices[] = array('message' => $message, 'type' => $type);
    }

    public function display_notices()
    {
        if (empty($this->notices)) {
            return;
        }

        foreach ($this->notices as $notice) {
            $class = esc_attr($notice['type']);
            echo "<div class='notice {$class}'><p><strong>" . esc_html($notice['message']) . "</strong></p></div>";
        }

        // Clear notices after displaying
        $this->notices = array();
    }
}
