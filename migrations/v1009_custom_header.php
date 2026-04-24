<?php
/**
 * Forum Portal custom header migration.
 */

namespace mundophpbb\forumportal\migrations;

class v1009_custom_header extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1008_typography_style');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_header_mode']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_header_mode', 'standard')),
            array('config.add', array('forumportal_header_image', '')),
            array('config.add', array('forumportal_header_title', '')),
            array('config.add', array('forumportal_header_subtitle', '')),
            array('config.add', array('forumportal_header_height', '240')),
        );
    }
}
