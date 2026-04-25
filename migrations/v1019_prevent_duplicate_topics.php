<?php
/**
 * Adds an option to prevent duplicate topics across portal blocks.
 */

namespace mundophpbb\forumportal\migrations;

class v1019_prevent_duplicate_topics extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1018_custom_links_panel');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_prevent_duplicate_topics']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_prevent_duplicate_topics', 1)),
        );
    }
}
