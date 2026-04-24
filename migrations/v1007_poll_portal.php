<?php
/**
 * Forum Portal poll portal migration.
 */

namespace mundophpbb\forumportal\migrations;

class v1007_poll_portal extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\mundophpbb\forumportal\migrations\v1006_top_contributors');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_show_polls'])
            && isset($this->config['forumportal_polls_limit'])
            && isset($this->config['forumportal_polls_days'])
            && isset($this->config['forumportal_poll_topic_id'])
            && isset($this->config['forumportal_allow_poll_vote']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_show_polls', 1)),
            array('config.add', array('forumportal_polls_limit', 1)),
            array('config.add', array('forumportal_polls_days', 0)),
            array('config.add', array('forumportal_poll_topic_id', 0)),
            array('config.add', array('forumportal_allow_poll_vote', 1)),
        );
    }
}
