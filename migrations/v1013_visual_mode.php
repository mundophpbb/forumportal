<?php
/**
 * Adds a visual mode option for ProSilver-style integration.
 */

namespace mundophpbb\forumportal\migrations;

class v1013_visual_mode extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\mundophpbb\forumportal\migrations\v1012_headlines_visibility_mobile');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_visual_mode']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_visual_mode', 'editorial')),
        );
    }
}
