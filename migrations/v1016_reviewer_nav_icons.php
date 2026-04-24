<?php
/**
 * Adds reviewer-requested visual tuning options.
 */

namespace mundophpbb\forumportal\migrations;

class v1016_reviewer_nav_icons extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\mundophpbb\forumportal\migrations\v1015_posts_layout_images');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_story_icon_mode'])
            && isset($this->config['forumportal_nav_portal_color'])
            && isset($this->config['forumportal_nav_forum_color']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_story_icon_mode', 'megaphone')),
            array('config.add', array('forumportal_nav_portal_color', '#bc2a4d')),
            array('config.add', array('forumportal_nav_forum_color', '#105289')),
        );
    }
}
