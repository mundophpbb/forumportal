<?php
/**
 * Adds the portal post layout option.
 */

namespace mundophpbb\forumportal\migrations;

class v1015_posts_layout_images extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\mundophpbb\forumportal\migrations\v1014_poll_permission_review');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_posts_layout']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_posts_layout', 'list')),
        );
    }
}