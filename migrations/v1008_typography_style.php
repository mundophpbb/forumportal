<?php
/**
 * Forum Portal typography style migration.
 */

namespace mundophpbb\forumportal\migrations;

class v1008_typography_style extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\mundophpbb\forumportal\migrations\v1007_poll_portal');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_typography_style']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_typography_style', 'portal')),
        );
    }
}
