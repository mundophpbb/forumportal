<?php
/**
 * Adds a dedicated color setting for the Open forum button.
 */

namespace mundophpbb\forumportal\migrations;

class v1017_separate_button_colors extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1016_reviewer_nav_icons');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_open_forum_color']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_open_forum_color', '#105289')),
        );
    }
}
