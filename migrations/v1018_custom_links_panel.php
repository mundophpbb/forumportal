<?php
/**
 * Adds a separate custom links sidebar panel.
 */

namespace mundophpbb\forumportal\migrations;

class v1018_custom_links_panel extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1017_separate_button_colors');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_show_custom_links'])
            && isset($this->config['forumportal_block_order_custom_links'])
            && isset($this->config['forumportal_custom_links_title']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_show_custom_links', 0)),
            array('config.add', array('forumportal_block_order_custom_links', 75)),
            array('config.add', array('forumportal_custom_links_title', '')),
        );
    }
}
