<?php
/**
 * Forum Portal poll permission review migration.
 */

namespace mundophpbb\forumportal\migrations;

class v1014_poll_permission_review extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\mundophpbb\forumportal\migrations\v1013_visual_mode');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_allow_poll_guest_vote']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_allow_poll_guest_vote', 1)),
        );
    }
}
