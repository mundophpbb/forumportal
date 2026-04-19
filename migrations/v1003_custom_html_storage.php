<?php
/**
 * Forum Portal migration: custom HTML storage table.
 */

namespace mundophpbb\forumportal\migrations;

class v1003_custom_html_storage extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1002_portal_order');
    }

    public function effectively_installed()
    {
        return $this->db_tools->sql_table_exists($this->table_prefix . 'forumportal_html');
    }

    public function update_schema()
    {
        return array(
            'add_tables' => array(
                $this->table_prefix . 'forumportal_html' => array(
                    'COLUMNS' => array(
                        'html_key'   => array('VCHAR:100', ''),
                        'html_value' => array('MTEXT_UNI', ''),
                    ),
                    'PRIMARY_KEY' => 'html_key',
                ),
            ),
        );
    }

    public function revert_schema()
    {
        return array(
            'drop_tables' => array(
                $this->table_prefix . 'forumportal_html',
            ),
        );
    }

    public function update_data()
    {
        return array(
            array('custom', array(array($this, 'migrate_custom_html'))),
        );
    }

    public function migrate_custom_html()
    {
        $existing = '';

        if (isset($this->config['forumportal_custom_html']))
        {
            $existing = (string) $this->config['forumportal_custom_html'];
        }

        $sql = 'DELETE FROM ' . $this->table_prefix . "forumportal_html
            WHERE html_key = 'forumportal_custom_html'";
        $this->db->sql_query($sql);

        $sql_ary = array(
            'html_key'   => 'forumportal_custom_html',
            'html_value' => $existing,
        );

        $sql = 'INSERT INTO ' . $this->table_prefix . 'forumportal_html ' . $this->db->sql_build_array('INSERT', $sql_ary);
        $this->db->sql_query($sql);
    }
}
