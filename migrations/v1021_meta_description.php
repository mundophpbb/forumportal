<?php
/**
 * Adds a custom meta description for the portal page.
 */

namespace mundophpbb\forumportal\migrations;

class v1021_meta_description extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1020_seo_noindex_paginated');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_meta_description']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_meta_description', '')),
        );
    }
}
