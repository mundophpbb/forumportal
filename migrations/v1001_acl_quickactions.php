<?php
/**
 * Forum Portal ACL migration (buttons removed; options workflow retained).
 */

namespace mundophpbb\forumportal\migrations;

class v1001_acl_quickactions extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1000_initial');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_quick_actions_enabled']);
    }

    public function update_data()
    {
        return array(
            array('permission.add', array('f_forumportal_publish')),
            array('permission.add', array('m_forumportal_edit')),
            array('permission.add', array('m_forumportal_feature')),
            array('config.add', array('forumportal_quick_actions_enabled', 1)),
        );
    }
}
