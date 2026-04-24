<?php
/**
 * Forum Portal top contributors migration.
 */

namespace mundophpbb\forumportal\migrations;

class v1006_top_contributors extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\mundophpbb\forumportal\migrations\v1005_feedback_tuning');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_top_contributors_limit'])
            && isset($this->config['forumportal_top_contributors_days'])
            && isset($this->config['forumportal_show_top_contributors']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_top_contributors_limit', 5)),
            array('config.add', array('forumportal_top_contributors_days', 30)),
            array('config.add', array('forumportal_show_top_contributors', 1)),
        );
    }
}
