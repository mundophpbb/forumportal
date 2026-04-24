<?php
/**
 * Forum Portal dark style compatibility migration.
 */

namespace mundophpbb\forumportal\migrations;

class v1010_dark_compat_mode extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1009_custom_header');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_dark_compat_mode']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_dark_compat_mode', 'auto')),
        );
    }
}
