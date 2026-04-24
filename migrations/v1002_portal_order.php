<?php
/**
 * Forum Portal migration: editorial order.
 */

namespace mundophpbb\forumportal\migrations;

class v1002_portal_order extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1001_acl_quickactions');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_editorial_order_enabled']);
    }

    public function update_schema()
    {
        return array(
            'add_columns' => array(
                $this->table_prefix . 'forumportal_topics' => array(
                    'portal_order' => array('UINT', 0),
                ),
            ),
        );
    }

    public function revert_schema()
    {
        return array(
            'drop_columns' => array(
                $this->table_prefix . 'forumportal_topics' => array(
                    'portal_order',
                ),
            ),
        );
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_editorial_order_enabled', 1)),
        );
    }
}
