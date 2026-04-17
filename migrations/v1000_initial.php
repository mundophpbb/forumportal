<?php
/**
 * Forum Portal migration.
 */

namespace mundophpbb\forumportal\migrations;

class v1000_initial extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array();
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_enabled']);
    }

    public function update_schema()
    {
        return array(
            'add_tables' => array(
                $this->table_prefix . 'forumportal_topics' => array(
                    'COLUMNS' => array(
                        'topic_id'        => array('UINT', 0),
                        'portal_enabled'  => array('BOOL', 0),
                        'portal_image'    => array('VCHAR:500', ''),
                        'portal_excerpt'  => array('TEXT_UNI', ''),
                        'portal_featured' => array('BOOL', 0),
                        'portal_updated'  => array('TIMESTAMP', 0),
                    ),
                    'PRIMARY_KEY' => 'topic_id',
                ),
            ),
        );
    }

    public function revert_schema()
    {
        return array(
            'drop_tables' => array(
                $this->table_prefix . 'forumportal_topics',
            ),
        );
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_enabled', 1)),
            array('config.add', array('forumportal_home_enabled', 0)),
            array('config.add', array('forumportal_source_forum', 0)),
            array('config.add', array('forumportal_topics_per_page', 8)),
            array('config.add', array('forumportal_excerpt_limit', 320)),
            array('config.add', array('forumportal_page_title', 'Portal')),
            array('config.add', array('forumportal_nav_title', 'Portal')),
            array('config.add', array('forumportal_default_image', '')),
            array('config.add', array('forumportal_date_format', 'd/m/Y H:i')),
            array('config.add', array('forumportal_headlines_limit', 5)),
            array('config.add', array('forumportal_most_read_limit', 5)),
            array('config.add', array('forumportal_most_commented_limit', 5)),
            array('config.add', array('forumportal_custom_html', '')),
            array('config.add', array('forumportal_custom_html_position', 'top')),

            array('module.add', array('acp', 'ACP_CAT_DOT_MODS', 'ACP_FORUMPORTAL')),
            array('module.add', array(
                'acp',
                'ACP_FORUMPORTAL',
                array(
                    'module_basename' => '\\mundophpbb\\forumportal\\acp\\main_module',
                    'modes'           => array('settings'),
                ),
            )),
        );
    }
}
