<?php
/**
 * Forum Portal sidebar block order migration.
 */

namespace mundophpbb\forumportal\migrations;

class v1011_sidebar_order extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1010_dark_compat_mode');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_block_order_notices']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_block_order_notices', 10)),
            array('config.add', array('forumportal_block_order_headlines', 20)),
            array('config.add', array('forumportal_block_order_top_contributors', 30)),
            array('config.add', array('forumportal_block_order_polls', 40)),
            array('config.add', array('forumportal_block_order_most_read', 50)),
            array('config.add', array('forumportal_block_order_most_commented', 60)),
            array('config.add', array('forumportal_block_order_custom_html', 70)),
        );
    }
}
