<?php
/**
 * Forum Portal ACP module.
 */

namespace mundophpbb\forumportal\acp;

class main_module
{
    public $u_action;
    public $tpl_name;
    public $page_title;

    public function main($id, $mode)
    {
        global $config, $db, $request, $template, $user, $table_prefix;

        $user->add_lang_ext('mundophpbb/forumportal', 'common');
        $user->add_lang_ext('mundophpbb/forumportal', 'info_acp_forumportal');

        $this->tpl_name = 'acp_forumportal_body';
        $this->page_title = $user->lang('ACP_FORUMPORTAL_SETTINGS');

        add_form_key('mundophpbb_forumportal');

        $submit = $request->is_set_post('submit');
        $sync_existing = $request->is_set_post('sync_existing');
        $errors = array();

        if ($submit || $sync_existing)
        {
            if (!check_form_key('mundophpbb_forumportal'))
            {
                $errors[] = $user->lang('FORM_INVALID');
            }

            $source_forums = $this->normalise_forum_ids($request->variable('forumportal_source_forum', array(0)));
            $auto_include_source = (int) $request->variable('forumportal_auto_include_source', 0);
            $topics_per_page = max(1, min(50, (int) $request->variable('forumportal_topics_per_page', 8)));
            $excerpt_limit = max(80, min(1200, (int) $request->variable('forumportal_excerpt_limit', 320)));
            $enabled = (int) $request->variable('forumportal_enabled', 0);
            $home_enabled = (int) $request->variable('forumportal_home_enabled', 0);
            $page_title = trim((string) $request->variable('forumportal_page_title', '', true));
            $nav_title = trim((string) $request->variable('forumportal_nav_title', '', true));
            $default_image = trim((string) $request->variable('forumportal_default_image', '', true));
            $fixed_topic_id = max(0, (int) $request->variable('forumportal_fixed_topic_id', 0));
            $date_format = trim((string) $request->variable('forumportal_date_format', '', true));
            $headlines_limit = max(1, min(15, (int) $request->variable('forumportal_headlines_limit', 5)));
            $most_read_limit = max(1, min(15, (int) $request->variable('forumportal_most_read_limit', 5)));
            $most_commented_limit = max(1, min(15, (int) $request->variable('forumportal_most_commented_limit', 5)));
            $notices_limit = max(1, min(15, (int) $request->variable('forumportal_notices_limit', 5)));
            $show_author = (int) $request->variable('forumportal_show_author', 1);
            $show_date = (int) $request->variable('forumportal_show_date', 1);
            $show_views = (int) $request->variable('forumportal_show_views', 1);
            $show_headlines = (int) $request->variable('forumportal_show_headlines', 1);
            $show_most_read = (int) $request->variable('forumportal_show_most_read', 1);
            $show_most_commented = (int) $request->variable('forumportal_show_most_commented', 1);
            $show_notices = (int) $request->variable('forumportal_show_notices', 1);
            $show_hero_excerpt = (int) $request->variable('forumportal_show_hero_excerpt', 1);
            $custom_html = html_entity_decode((string) $request->variable('forumportal_custom_html', '', true), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            $custom_html_title = trim((string) $request->variable('forumportal_custom_html_title', '', true));
            $custom_html_position = (string) $request->variable('forumportal_custom_html_position', 'top');

            if (!in_array($custom_html_position, array('top', 'bottom')))
            {
                $custom_html_position = 'top';
            }

            if ($page_title === '')
            {
                $page_title = $user->lang('FORUMPORTAL_DEFAULT_PAGE_TITLE');
            }

            if ($nav_title === '')
            {
                $nav_title = $user->lang('FORUMPORTAL_DEFAULT_NAV_TITLE');
            }

            if (!$errors)
            {
                set_config('forumportal_enabled', $enabled);
                set_config('forumportal_home_enabled', $home_enabled);
                set_config('forumportal_source_forum', implode(',', $source_forums));
                set_config('forumportal_auto_include_source', $auto_include_source);
                set_config('forumportal_topics_per_page', $topics_per_page);
                set_config('forumportal_excerpt_limit', $excerpt_limit);
                set_config('forumportal_page_title', $page_title);
                set_config('forumportal_nav_title', $nav_title);
                set_config('forumportal_default_image', $default_image);
                set_config('forumportal_fixed_topic_id', $fixed_topic_id);
                set_config('forumportal_date_format', $date_format);
                set_config('forumportal_headlines_limit', $headlines_limit);
                set_config('forumportal_most_read_limit', $most_read_limit);
                set_config('forumportal_most_commented_limit', $most_commented_limit);
                set_config('forumportal_notices_limit', $notices_limit);
                set_config('forumportal_show_author', $show_author);
                set_config('forumportal_show_date', $show_date);
                set_config('forumportal_show_views', $show_views);
                set_config('forumportal_show_headlines', $show_headlines);
                set_config('forumportal_show_most_read', $show_most_read);
                set_config('forumportal_show_most_commented', $show_most_commented);
                set_config('forumportal_show_notices', $show_notices);
                set_config('forumportal_show_hero_excerpt', $show_hero_excerpt);
                set_config('forumportal_custom_html_title', $custom_html_title);
                set_config('forumportal_custom_html_position', $custom_html_position);

                $this->save_custom_html($db, $table_prefix, $custom_html);

                if ($sync_existing)
                {
                    $synced_topics = $this->sync_existing_topics($db, $table_prefix, $source_forums);
                    trigger_error($user->lang('ACP_FORUMPORTAL_SYNCED', (int) $synced_topics) . adm_back_link($this->u_action));
                }

                trigger_error($user->lang('ACP_FORUMPORTAL_SAVED') . adm_back_link($this->u_action));
            }
        }

        $custom_html = $this->get_custom_html($db, $table_prefix);

        $template->assign_vars(array(
            'U_ACTION'                           => $this->u_action,
            'ERROR_MSG'                          => implode('<br>', $errors),
            'S_ERROR'                            => !empty($errors),
            'S_FORUMPORTAL_ENABLED'              => (int) $config['forumportal_enabled'],
            'S_FORUMPORTAL_HOME_ENABLED'         => (int) $config['forumportal_home_enabled'],
            'S_FORUMPORTAL_AUTO_INCLUDE_SOURCE'  => $this->config_bool($config, 'forumportal_auto_include_source', false),
            'FORUMPORTAL_SOURCE_FORUM_OPTIONS'   => $this->build_forum_options($db, $this->parse_source_forums((string) $config['forumportal_source_forum'])),
            'FORUMPORTAL_TOPICS_PER_PAGE'        => (int) $config['forumportal_topics_per_page'],
            'FORUMPORTAL_EXCERPT_LIMIT'          => (int) $config['forumportal_excerpt_limit'],
            'FORUMPORTAL_PAGE_TITLE'             => (string) $config['forumportal_page_title'],
            'FORUMPORTAL_NAV_TITLE'              => (string) $config['forumportal_nav_title'],
            'FORUMPORTAL_DEFAULT_IMAGE'          => (string) $config['forumportal_default_image'],
            'FORUMPORTAL_FIXED_TOPIC_ID'         => isset($config['forumportal_fixed_topic_id']) ? (int) $config['forumportal_fixed_topic_id'] : 0,
            'FORUMPORTAL_DATE_FORMAT_OPTIONS'    => $this->build_date_format_options(isset($config['forumportal_date_format']) ? (string) $config['forumportal_date_format'] : '', $user),
            'FORUMPORTAL_HEADLINES_LIMIT'        => isset($config['forumportal_headlines_limit']) ? (int) $config['forumportal_headlines_limit'] : 5,
            'FORUMPORTAL_MOST_READ_LIMIT'        => isset($config['forumportal_most_read_limit']) ? (int) $config['forumportal_most_read_limit'] : 5,
            'FORUMPORTAL_MOST_COMMENTED_LIMIT'   => isset($config['forumportal_most_commented_limit']) ? (int) $config['forumportal_most_commented_limit'] : 5,
            'FORUMPORTAL_NOTICES_LIMIT'          => isset($config['forumportal_notices_limit']) ? (int) $config['forumportal_notices_limit'] : 5,
            'S_FORUMPORTAL_SHOW_AUTHOR'          => $this->config_bool($config, 'forumportal_show_author', true),
            'S_FORUMPORTAL_SHOW_DATE'            => $this->config_bool($config, 'forumportal_show_date', true),
            'S_FORUMPORTAL_SHOW_VIEWS'           => $this->config_bool($config, 'forumportal_show_views', true),
            'S_FORUMPORTAL_SHOW_HEADLINES'       => $this->config_bool($config, 'forumportal_show_headlines', true),
            'S_FORUMPORTAL_SHOW_MOST_READ'       => $this->config_bool($config, 'forumportal_show_most_read', true),
            'S_FORUMPORTAL_SHOW_MOST_COMMENTED'  => $this->config_bool($config, 'forumportal_show_most_commented', true),
            'S_FORUMPORTAL_SHOW_NOTICES'         => $this->config_bool($config, 'forumportal_show_notices', true),
            'S_FORUMPORTAL_SHOW_HERO_EXCERPT'    => $this->config_bool($config, 'forumportal_show_hero_excerpt', true),
            'FORUMPORTAL_CUSTOM_HTML'            => $custom_html,
            'FORUMPORTAL_CUSTOM_HTML_TITLE'      => isset($config['forumportal_custom_html_title']) ? (string) $config['forumportal_custom_html_title'] : '',
            'S_FORUMPORTAL_HTML_TOP'             => ((string) $config['forumportal_custom_html_position'] !== 'bottom'),
            'S_FORUMPORTAL_HTML_BOTTOM'          => ((string) $config['forumportal_custom_html_position'] === 'bottom'),
        ));
    }

    protected function build_date_format_options($current_format, $user)
    {
        $current_format = (string) $current_format;
        $options = '';
        $formats = array();

        if (isset($user->lang['dateformats']) && is_array($user->lang['dateformats']))
        {
            $formats = $user->lang['dateformats'];
        }

        $options .= '<option value=""' . ($current_format === '' ? ' selected="selected"' : '') . '>' . htmlspecialchars($user->lang('ACP_FORUMPORTAL_DATE_FORMAT_DEFAULT')) . '</option>';

        foreach ($formats as $key => $value)
        {
            if (is_string($key) && $key !== '')
            {
                $format = (string) $key;
                $example = (string) $value;
            }
            else
            {
                $format = (string) $value;
                $example = $user->format_date(time(), $format);
            }

            $selected = ($current_format === $format) ? ' selected="selected"' : '';
            $label = ($example !== '') ? ($format . ' — ' . $example) : $format;
            $options .= '<option value="' . htmlspecialchars($format) . '"' . $selected . '>' . htmlspecialchars($label) . '</option>';
        }

        if ($current_format !== '' && !in_array($current_format, $formats, true))
        {
            $options .= '<option value="' . htmlspecialchars($current_format) . '" selected="selected">' . htmlspecialchars($current_format . ' — ' . $user->lang('ACP_FORUMPORTAL_DATE_FORMAT_CUSTOM')) . '</option>';
        }

        return $options;
    }


    protected function config_bool($config, $key, $default)
    {
        if (!isset($config[$key]))
        {
            return (bool) $default;
        }

        return (bool) ((int) $config[$key]);
    }

    protected function build_forum_options($db, array $selected_forums)
    {
        $options = '';
        $selected_lookup = array_fill_keys($selected_forums, true);

        $sql = 'SELECT forum_id, forum_name, forum_type
            FROM ' . FORUMS_TABLE . '
            ORDER BY left_id ASC';
        $result = $db->sql_query($sql);

        while ($row = $db->sql_fetchrow($result))
        {
            if ((int) $row['forum_type'] == FORUM_CAT)
            {
                continue;
            }

            $forum_id = (int) $row['forum_id'];
            $options .= '<option value="' . $forum_id . '"' . (isset($selected_lookup[$forum_id]) ? ' selected="selected"' : '') . '>' . htmlspecialchars($row['forum_name']) . '</option>';
        }
        $db->sql_freeresult($result);

        return $options;
    }

    protected function parse_source_forums($config_value)
    {
        return $this->normalise_forum_ids(explode(',', (string) $config_value));
    }

    protected function normalise_forum_ids($forum_ids)
    {
        if (!is_array($forum_ids))
        {
            $forum_ids = array($forum_ids);
        }

        $normalised = array();
        foreach ($forum_ids as $forum_id)
        {
            $forum_id = (int) $forum_id;
            if ($forum_id > 0)
            {
                $normalised[$forum_id] = $forum_id;
            }
        }

        return array_values($normalised);
    }

    protected function portal_topics_table($table_prefix)
    {
        return $table_prefix . 'forumportal_topics';
    }

    protected function sync_existing_topics($db, $table_prefix, array $forum_ids)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        if (empty($forum_ids))
        {
            return 0;
        }

        $sql = 'SELECT t.topic_id
            FROM ' . TOPICS_TABLE . ' t
            LEFT JOIN ' . $this->portal_topics_table($table_prefix) . ' fp
                ON fp.topic_id = t.topic_id
            WHERE ' . $db->sql_in_set('t.forum_id', $forum_ids) . '
                AND t.topic_visibility = ' . ITEM_APPROVED . '
                AND fp.topic_id IS NULL';
        $result = $db->sql_query($sql);

        $topic_ids = array();
        while ($row = $db->sql_fetchrow($result))
        {
            $topic_ids[] = (int) $row['topic_id'];
        }
        $db->sql_freeresult($result);

        if (empty($topic_ids))
        {
            return 0;
        }

        $now = time();
        foreach ($topic_ids as $topic_id)
        {
            $sql = 'INSERT INTO ' . $this->portal_topics_table($table_prefix) . ' ' . $db->sql_build_array('INSERT', array(
                'topic_id'        => $topic_id,
                'portal_enabled'  => 1,
                'portal_image'    => '',
                'portal_excerpt'  => '',
                'portal_featured' => 0,
                'portal_order'    => 0,
                'portal_updated'  => $now,
            ));
            $db->sql_query($sql);
        }

        return count($topic_ids);
    }

    protected function get_custom_html($db, $table_prefix)
    {
        $sql = 'SELECT html_value
            FROM ' . $table_prefix . "forumportal_html
            WHERE html_key = 'forumportal_custom_html'";
        $result = $db->sql_query_limit($sql, 1);
        $html = (string) $db->sql_fetchfield('html_value');
        $db->sql_freeresult($result);

        return html_entity_decode((string) $html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function save_custom_html($db, $table_prefix, $custom_html)
    {
        $sql = 'DELETE FROM ' . $table_prefix . "forumportal_html
            WHERE html_key = 'forumportal_custom_html'";
        $db->sql_query($sql);

        $sql_ary = array(
            'html_key'   => 'forumportal_custom_html',
            'html_value' => (string) $custom_html,
        );

        $sql = 'INSERT INTO ' . $table_prefix . 'forumportal_html ' . $db->sql_build_array('INSERT', $sql_ary);
        $db->sql_query($sql);
    }
}
