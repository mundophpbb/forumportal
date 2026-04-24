<?php
/**
 * Forum Portal feedback tuning migration.
 */

namespace mundophpbb\forumportal\migrations;

class v1005_feedback_tuning extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\mundophpbb\forumportal\migrations\v1004_auto_include_source');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_most_read_days']) && isset($this->config['forumportal_most_commented_days']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_most_read_days', 0)),
            array('config.add', array('forumportal_most_commented_days', 0)),
        );
    }
}
