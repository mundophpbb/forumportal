<?php
/**
 * Adds poll selection mode for random poll rotation.
 */

namespace mundophpbb\forumportal\migrations;

class v1022_random_polls extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1021_meta_description');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_polls_mode']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_polls_mode', 'recent')),
        );
    }
}
