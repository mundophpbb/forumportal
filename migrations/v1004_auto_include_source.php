<?php
/**
 * Forum Portal migration: auto include source topics.
 */

namespace mundophpbb\forumportal\migrations;

class v1004_auto_include_source extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1003_custom_html_storage');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_auto_include_source']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_auto_include_source', 0)),
        );
    }
}
