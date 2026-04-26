<?php
/**
 * Adds an SEO option to prevent indexing of paginated portal pages.
 */

namespace mundophpbb\forumportal\migrations;

class v1020_seo_noindex_paginated extends \phpbb\db\migration\migration
{
    public static function depends_on()
    {
        return array('\\mundophpbb\\forumportal\\migrations\\v1019_prevent_duplicate_topics');
    }

    public function effectively_installed()
    {
        return isset($this->config['forumportal_noindex_paginated']);
    }

    public function update_data()
    {
        return array(
            array('config.add', array('forumportal_noindex_paginated', 1)),
        );
    }
}
