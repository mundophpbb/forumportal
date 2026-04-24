<?php
/**
 * Makes the Latest headlines visibility option explicit in fresh installs.
 */

namespace mundophpbb\forumportal\migrations;

class v1012_headlines_visibility_mobile extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1011_sidebar_order');
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_show_headlines', 1)),
        );
    }
}
