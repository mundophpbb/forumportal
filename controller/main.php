<?php
/**
 *
 * Forum Portal controller.
 *
 */

namespace mundophpbb\forumportal\controller;

class main
{
    /** @var \phpbb\auth\auth */
    protected $auth;

    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

    /** @var \phpbb\event\dispatcher_interface */
    protected $dispatcher;

    /** @var \phpbb\request\request */
    protected $request;

    /** @var \phpbb\template\template */
    protected $template;

    /** @var \phpbb\user */
    protected $user;

    /** @var string */
    protected $phpbb_root_path;

    /** @var string */
    protected $php_ext;

    /** @var string */
    protected $portal_topics_table;

    /** @var string */
    protected $forumportal_html_table;

    /** @var array|null */
    protected $topic_comment_metric = null;

    /** @var array */
    protected $topic_icon_cache = array();

    public function __construct(
        \phpbb\auth\auth $auth,
        \phpbb\config\config $config,
        \phpbb\controller\helper $helper,
        \phpbb\db\driver\driver_interface $db,
        \phpbb\event\dispatcher_interface $dispatcher,
        \phpbb\request\request $request,
        \phpbb\template\template $template,
        \phpbb\user $user,
        $phpbb_root_path,
        $php_ext,
        $table_prefix
    ) {
        $this->auth = $auth;
        $this->config = $config;
        $this->helper = $helper;
        $this->db = $db;
        $this->dispatcher = $dispatcher;
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $php_ext;
        $this->portal_topics_table = $table_prefix . 'forumportal_topics';
        $this->forumportal_html_table = $table_prefix . 'forumportal_html';
    }

    public function handle()
    {
        $this->user->add_lang_ext('mundophpbb/forumportal', 'common');

        if (function_exists('add_form_key'))
        {
            add_form_key('mundophpbb_forumportal_poll');
        }

        if (!(int) $this->config['forumportal_enabled'])
        {
            trigger_error($this->user->lang('FORUMPORTAL_DISABLED'));
        }

        $source_forum_ids = $this->get_readable_source_forum_ids();

        if (empty($source_forum_ids))
        {
            trigger_error($this->user->lang('FORUMPORTAL_FORUM_UNAVAILABLE'));
        }

        $forum_sql = $this->db->sql_in_set('t.forum_id', $source_forum_ids);

        if (!function_exists('generate_text_for_display'))
        {
            include_once($this->phpbb_root_path . 'includes/functions_content.' . $this->php_ext);
        }

        if (!function_exists('phpbb_get_avatar'))
        {
            include_once($this->phpbb_root_path . 'includes/functions_display.' . $this->php_ext);
        }

        $page_title = (string) $this->config['forumportal_page_title'];
        if ($page_title === '')
        {
            $page_title = $this->user->lang('FORUMPORTAL_DEFAULT_PAGE_TITLE');
        }

        $nav_title = (string) $this->config['forumportal_nav_title'];
        if ($nav_title === '')
        {
            $nav_title = $this->user->lang('FORUMPORTAL_DEFAULT_NAV_TITLE');
        }

        $meta_description = '';
        if (isset($this->config['forumportal_meta_description']))
        {
            $meta_description = $this->clean_meta_description($this->config['forumportal_meta_description']);
        }

        $per_page = max(1, min(50, (int) $this->config['forumportal_topics_per_page']));
        $excerpt_limit = max(80, min(1200, (int) $this->config['forumportal_excerpt_limit']));
        $default_image = trim((string) $this->config['forumportal_default_image']);
        $custom_html = $this->get_custom_html();
        if (!$this->has_meaningful_markup($custom_html))
        {
            $custom_html = '';
        }
        $custom_html_title = trim((string) (isset($this->config['forumportal_custom_html_title']) ? $this->config['forumportal_custom_html_title'] : ''));
        if ($custom_html_title === '')
        {
            $custom_html_title = $this->user->lang('FORUMPORTAL_CUSTOM_BLOCK');
        }
        $custom_html_position = ((isset($this->config['forumportal_custom_html_position']) && (string) $this->config['forumportal_custom_html_position'] === 'bottom') ? 'bottom' : 'top');
        $custom_links_raw = $this->get_custom_links();
        $custom_links = $this->parse_custom_links($custom_links_raw);
        $custom_links_title = trim((string) (isset($this->config['forumportal_custom_links_title']) ? $this->config['forumportal_custom_links_title'] : ''));
        if ($custom_links_title === '')
        {
            $custom_links_title = $this->user->lang('FORUMPORTAL_CUSTOM_LINKS');
        }
        $show_custom_links = $this->config_bool('forumportal_show_custom_links', false);
        $typography_style = (isset($this->config['forumportal_typography_style']) && (string) $this->config['forumportal_typography_style'] === 'forum') ? 'forum' : 'portal';
        $visual_mode = (isset($this->config['forumportal_visual_mode']) && (string) $this->config['forumportal_visual_mode'] === 'prosilver') ? 'prosilver' : 'editorial';
        $posts_layout = (isset($this->config['forumportal_posts_layout']) && (string) $this->config['forumportal_posts_layout'] === 'grid2') ? 'grid2' : 'list';
        $dark_compat_mode = isset($this->config['forumportal_dark_compat_mode']) ? (string) $this->config['forumportal_dark_compat_mode'] : 'auto';
        if (!in_array($dark_compat_mode, array('auto', 'force', 'off'), true))
        {
            $dark_compat_mode = 'auto';
        }
        $story_icon_mode = isset($this->config['forumportal_story_icon_mode']) ? (string) $this->config['forumportal_story_icon_mode'] : 'megaphone';
        if (!in_array($story_icon_mode, array('megaphone', 'topic', 'none'), true))
        {
            $story_icon_mode = 'megaphone';
        }
        $open_forum_color = $this->sanitize_hex_color(isset($this->config['forumportal_open_forum_color']) ? (string) $this->config['forumportal_open_forum_color'] : '', '#105289');
        $header_mode = isset($this->config['forumportal_header_mode']) ? (string) $this->config['forumportal_header_mode'] : 'standard';
        if (!in_array($header_mode, array('standard', 'custom', 'custom_only'), true))
        {
            $header_mode = 'standard';
        }
        $header_image = trim((string) (isset($this->config['forumportal_header_image']) ? $this->config['forumportal_header_image'] : ''));
        $header_title = trim((string) (isset($this->config['forumportal_header_title']) ? $this->config['forumportal_header_title'] : ''));
        $header_subtitle = trim((string) (isset($this->config['forumportal_header_subtitle']) ? $this->config['forumportal_header_subtitle'] : ''));
        $header_height = max(120, min(600, (int) (isset($this->config['forumportal_header_height']) ? $this->config['forumportal_header_height'] : 240)));
        $show_custom_header = ($header_mode !== 'standard' && $header_image !== '');
        $hide_standard_header = ($header_mode === 'custom_only' && $header_image !== '');
        $body_classes = array();
        if ($hide_standard_header)
        {
            $body_classes[] = 'forumportal-hide-standard-header';
        }
        if ($show_custom_header)
        {
            $body_classes[] = 'forumportal-has-custom-header';
        }
        if ($dark_compat_mode === 'force')
        {
            $body_classes[] = 'forumportal-force-dark-portal';
        }
        $body_class = implode(' ', $body_classes);

        $custom_html = strtr($custom_html, array(
            '{TEXT}'         => $custom_html_title,
            '{TEXT1}'        => $page_title,
            '{BOARD_NAME}'   => (string) $this->config['sitename'],
            '{PORTAL_TITLE}' => $page_title,
            '{NAV_TITLE}'    => $nav_title,
        ));
        $start = max(0, (int) $this->request->variable('start', 0));
        $noindex_paginated = $this->config_bool('forumportal_noindex_paginated', true);

        $has_topics = false;
        $has_hero_topic = false;
        $notices = array();
        $headlines = array();
        $most_read_topics = array();
        $most_commented_topics = array();
        $top_contributors = array();
        $polls = array();
        $poll_message = '';
        $pagination_base = $this->helper->route('mundophpbb_forumportal_controller');
        $headline_limit = max(1, min(15, (int) (isset($this->config['forumportal_headlines_limit']) ? $this->config['forumportal_headlines_limit'] : 5)));
        $most_read_limit = max(1, min(15, (int) (isset($this->config['forumportal_most_read_limit']) ? $this->config['forumportal_most_read_limit'] : 5)));
        $most_commented_limit = max(1, min(15, (int) (isset($this->config['forumportal_most_commented_limit']) ? $this->config['forumportal_most_commented_limit'] : 5)));
        $most_read_days = max(0, min(3650, (int) (isset($this->config['forumportal_most_read_days']) ? $this->config['forumportal_most_read_days'] : 0)));
        $most_commented_days = max(0, min(3650, (int) (isset($this->config['forumportal_most_commented_days']) ? $this->config['forumportal_most_commented_days'] : 0)));
        $top_contributors_limit = max(1, min(15, (int) (isset($this->config['forumportal_top_contributors_limit']) ? $this->config['forumportal_top_contributors_limit'] : 5)));
        $top_contributors_days = max(0, min(3650, (int) (isset($this->config['forumportal_top_contributors_days']) ? $this->config['forumportal_top_contributors_days'] : 30)));
        $polls_limit = max(1, min(5, (int) (isset($this->config['forumportal_polls_limit']) ? $this->config['forumportal_polls_limit'] : 1)));
        $polls_days = max(0, min(3650, (int) (isset($this->config['forumportal_polls_days']) ? $this->config['forumportal_polls_days'] : 0)));
        $poll_topic_id = max(0, (int) (isset($this->config['forumportal_poll_topic_id']) ? $this->config['forumportal_poll_topic_id'] : 0));
        $polls_mode = (isset($this->config['forumportal_polls_mode']) && (string) $this->config['forumportal_polls_mode'] === 'random') ? 'random' : 'recent';
        $notices_limit = max(1, min(15, (int) (isset($this->config['forumportal_notices_limit']) ? $this->config['forumportal_notices_limit'] : 5)));
        $show_author = $this->config_bool('forumportal_show_author', true);
        $show_date = $this->config_bool('forumportal_show_date', true);
        $show_views = $this->config_bool('forumportal_show_views', true);
        $show_headlines = $this->config_bool('forumportal_show_headlines', true);
        $show_most_read = $this->config_bool('forumportal_show_most_read', true);
        $show_most_commented = $this->config_bool('forumportal_show_most_commented', true);
        $show_top_contributors = $this->config_bool('forumportal_show_top_contributors', true);
        $show_polls = $this->config_bool('forumportal_show_polls', true);
        $allow_poll_vote = $this->config_bool('forumportal_allow_poll_vote', true);
        $show_notices = $this->config_bool('forumportal_show_notices', true);
        $show_hero_excerpt = $this->config_bool('forumportal_show_hero_excerpt', true);
        $prevent_duplicate_topics = $this->config_bool('forumportal_prevent_duplicate_topics', true);
        $used_topic_ids = array();
        $fixed_topic_id = isset($this->config['forumportal_fixed_topic_id']) ? (int) $this->config['forumportal_fixed_topic_id'] : 0;

        $count_sql = 'SELECT COUNT(t.topic_id) AS total_topics
                FROM ' . TOPICS_TABLE . ' t
                ' . $this->get_portal_topic_join_sql() . '
                WHERE ' . $forum_sql . '
                    AND t.topic_visibility = ' . ITEM_APPROVED . '
                    AND ' . $this->get_portal_topic_visibility_sql();
        $result = $this->db->sql_query($count_sql);
        $total_topics = (int) $this->db->sql_fetchfield('total_topics');
        $this->db->sql_freeresult($result);

        $fixed_hero_topic = ($start === 0 && $fixed_topic_id > 0)
            ? $this->get_fixed_hero_topic($source_forum_ids, $fixed_topic_id, $excerpt_limit, $default_image)
            : array();

        $use_fixed_hero = !empty($fixed_hero_topic);
        $use_hero_layout = ($start === 0 && ($use_fixed_hero || $total_topics > 1));

        if ($use_fixed_hero)
        {
            $has_topics = true;
            $has_hero_topic = true;
            $has_sidebar = (!empty($notices) || !empty($headlines) || !empty($top_contributors) || !empty($polls) || !empty($most_read_topics) || !empty($most_commented_topics) || ($custom_html !== '' && $custom_html_position === 'top') || ($show_custom_links && !empty($custom_links)));

        $this->template->assign_vars(array(
                'S_HAS_HERO_TOPIC'      => true,
                'HERO_TITLE'            => $fixed_hero_topic['TITLE'],
                'HERO_EXCERPT'          => $fixed_hero_topic['EXCERPT'],
                'HERO_IMAGE'            => $fixed_hero_topic['IMAGE'],
                'HERO_DATE'             => $fixed_hero_topic['DATE'],
                'HERO_AUTHOR_FULL'      => $fixed_hero_topic['AUTHOR_FULL'],
                'HERO_AUTHOR_AVATAR'    => $fixed_hero_topic['AUTHOR_AVATAR'],
                'HERO_VIEWS'            => $fixed_hero_topic['VIEWS'],
                'HERO_S_FEATURED'       => true,
                'U_HERO_VIEW_TOPIC'     => $fixed_hero_topic['U_VIEW_TOPIC'],
            ));

            $this->remember_topic_used($fixed_topic_id, $used_topic_ids, $prevent_duplicate_topics);
        }

        $query_limit = $per_page + (($start === 0 && $use_fixed_hero) ? 1 : 0) + 10;
        // The hero topic consumes one item from the first paginated page.
        // Without this adjustment, /portal can show hero + $per_page cards,
        // while /portal?start=$per_page starts from an item already visible
        // on the first page.
        $topic_cards_limit = ($use_hero_layout && $start === 0) ? max(0, $per_page - 1) : $per_page;
        $topic_cards_assigned = 0;
        $main_topic_ids = array();

        $sql = 'SELECT t.topic_id, t.forum_id, t.icon_id, t.topic_title, t.topic_time, t.topic_views, t.topic_first_post_id,
                       p.post_text, p.bbcode_uid, p.bbcode_bitfield,
                       p.enable_bbcode, p.enable_smilies, p.enable_magic_url,
                       u.user_id, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height,
                       fp.portal_image, fp.portal_excerpt, fp.portal_featured, fp.portal_order
                FROM ' . TOPICS_TABLE . ' t
                INNER JOIN ' . POSTS_TABLE . ' p
                    ON p.post_id = t.topic_first_post_id
                INNER JOIN ' . USERS_TABLE . ' u
                    ON u.user_id = t.topic_poster
                ' . $this->get_portal_topic_join_sql() . '
                WHERE ' . $forum_sql . '
                    AND t.topic_visibility = ' . ITEM_APPROVED . '
                    AND ' . $this->get_portal_topic_visibility_sql() . '
                ORDER BY CASE WHEN ' . $this->get_portal_order_expression() . ' > 0 THEN 0 ELSE 1 END ASC, ' . $this->get_portal_order_expression() . ' ASC, ' . $this->get_portal_featured_expression() . ' DESC, t.topic_time DESC';
        $result = $this->db->sql_query_limit($sql, $query_limit, $start);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $topic_id = (int) $row['topic_id'];

            if ($use_fixed_hero && $topic_id === $fixed_topic_id)
            {
                continue;
            }

            if ($this->should_skip_topic($topic_id, $main_topic_ids, $used_topic_ids, $prevent_duplicate_topics))
            {
                continue;
            }

            if ($topic_cards_assigned >= $topic_cards_limit)
            {
                break;
            }

            $topic_data = $this->build_topic_display_data($row, $excerpt_limit, $default_image);
            $has_topics = true;

            if ($use_hero_layout && !$has_hero_topic)
            {
                $has_hero_topic = true;
                $has_sidebar = (!empty($notices) || !empty($headlines) || !empty($top_contributors) || !empty($polls) || !empty($most_read_topics) || !empty($most_commented_topics) || ($custom_html !== '' && $custom_html_position === 'top') || ($show_custom_links && !empty($custom_links)));

        $this->template->assign_vars(array(
                    'S_HAS_HERO_TOPIC'      => true,
                    'HERO_TITLE'            => $topic_data['TITLE'],
                    'HERO_EXCERPT'          => $topic_data['EXCERPT'],
                    'HERO_IMAGE'            => $topic_data['IMAGE'],
                    'HERO_DATE'             => $topic_data['DATE'],
                    'HERO_AUTHOR_FULL'      => $topic_data['AUTHOR_FULL'],
                    'HERO_AUTHOR_AVATAR'    => $topic_data['AUTHOR_AVATAR'],
                    'HERO_VIEWS'            => $topic_data['VIEWS'],
                    'HERO_S_FEATURED'       => $topic_data['S_FEATURED'],
                    'U_HERO_VIEW_TOPIC'     => $topic_data['U_VIEW_TOPIC'],
                ));
                $this->remember_topic_used($topic_id, $used_topic_ids, $prevent_duplicate_topics);
                continue;
            }

            $this->template->assign_block_vars('topics', $topic_data);
            $this->remember_topic_used($topic_id, $used_topic_ids, $prevent_duplicate_topics);
            $topic_cards_assigned++;
        }
        $this->db->sql_freeresult($result);

        $notices = $show_notices ? $this->get_notice_topics($source_forum_ids, $notices_limit, $used_topic_ids, $prevent_duplicate_topics) : array();
        foreach ($notices as $notice)
        {
            $this->template->assign_block_vars('notices', $notice);
        }

        $headlines = $show_headlines ? $this->get_latest_headlines($source_forum_ids, $headline_limit, $used_topic_ids, $prevent_duplicate_topics) : array();
        foreach ($headlines as $headline)
        {
            $this->template->assign_block_vars('headlines', $headline);
        }

        $top_contributors = $show_top_contributors ? $this->get_top_contributors($source_forum_ids, $top_contributors_limit, $top_contributors_days) : array();
        foreach ($top_contributors as $top_contributor)
        {
            $this->template->assign_block_vars('top_contributors', $top_contributor);
        }

        $poll_message = $this->handle_poll_vote($source_forum_ids);
        // Polls are sidebar widgets and may legitimately point to a topic that is also
        // displayed as a latest/highlighted topic. Do not let duplicate-topic
        // filtering hide the entire poll block.
        $polls = $show_polls ? $this->get_poll_topics($source_forum_ids, $polls_limit, $polls_days, $poll_topic_id, $allow_poll_vote, $used_topic_ids, false, $polls_mode) : array();
        foreach ($polls as $poll)
        {
            $poll_options = isset($poll['OPTIONS']) ? $poll['OPTIONS'] : array();
            unset($poll['OPTIONS']);
            $this->template->assign_block_vars('polls', $poll);

            foreach ($poll_options as $poll_option)
            {
                $this->template->assign_block_vars('polls.options', $poll_option);
            }
        }

        $most_read_topics = $show_most_read ? $this->get_most_read_topics($source_forum_ids, $most_read_limit, $most_read_days, $used_topic_ids, $prevent_duplicate_topics) : array();
        foreach ($most_read_topics as $most_read_topic)
        {
            $this->template->assign_block_vars('most_read', $most_read_topic);
        }

        $most_commented_topics = $show_most_commented ? $this->get_most_commented_topics($source_forum_ids, $most_commented_limit, $most_commented_days, $used_topic_ids, $prevent_duplicate_topics) : array();
        foreach ($most_commented_topics as $most_commented_topic)
        {
            $this->template->assign_block_vars('most_commented', $most_commented_topic);
        }

        if ($show_custom_links)
        {
            foreach ($custom_links as $custom_link)
            {
                $this->template->assign_block_vars('custom_links', $custom_link);
            }
        }

        $has_sidebar = (!empty($notices) || !empty($headlines) || !empty($top_contributors) || !empty($polls) || !empty($most_read_topics) || !empty($most_commented_topics) || ($custom_html !== '' && $custom_html_position === 'top') || ($show_custom_links && !empty($custom_links)));

        $this->template->assign_vars(array(
            'PORTAL_PAGE_TITLE'              => $page_title,
            'PORTAL_CUSTOM_HTML'             => $custom_html,
            'PORTAL_CUSTOM_HTML_TITLE'       => $custom_html_title,
            'PORTAL_CUSTOM_LINKS_TITLE'      => $custom_links_title,
            'S_FORUMPORTAL_SHOW_CUSTOM_LINKS'=> ($show_custom_links && !empty($custom_links)),
            'S_PORTAL_CUSTOM_HTML_TOP'       => ($custom_html !== '' && $custom_html_position === 'top'),
            'S_PORTAL_CUSTOM_HTML_BOTTOM'    => ($custom_html !== '' && $custom_html_position === 'bottom'),
            'S_HAS_PORTAL_TOPICS'            => $has_topics,
            'S_HAS_HERO_TOPIC'               => $has_hero_topic,
            'S_HAS_PAGINATION'               => ($total_topics > $per_page),
            'S_HAS_HEADLINES'                => !empty($headlines),
            'S_HAS_TOP_CONTRIBUTORS'        => !empty($top_contributors),
            'S_HAS_POLLS'                   => !empty($polls),
            'S_HAS_POLL_MESSAGE'            => ($poll_message !== ''),
            'FORUMPORTAL_POLL_MESSAGE'      => $poll_message,
            'S_HAS_MOST_READ'                => !empty($most_read_topics),
            'S_HAS_MOST_COMMENTED'           => !empty($most_commented_topics),
            'S_HAS_SIDEBAR'                  => $has_sidebar,
            'S_FORUMPORTAL_SHOW_AUTHOR'      => $show_author,
            'S_FORUMPORTAL_SHOW_DATE'        => $show_date,
            'S_FORUMPORTAL_SHOW_VIEWS'       => $show_views,
            'S_FORUMPORTAL_SHOW_HEADLINES'   => $show_headlines,
            'S_FORUMPORTAL_SHOW_MOST_READ'   => $show_most_read,
            'S_FORUMPORTAL_SHOW_MOST_COMMENTED' => $show_most_commented,
            'S_FORUMPORTAL_SHOW_TOP_CONTRIBUTORS' => $show_top_contributors,
            'S_FORUMPORTAL_SHOW_POLLS'      => $show_polls,
            'S_FORUMPORTAL_ALLOW_POLL_VOTE' => $allow_poll_vote,
            'FORUMPORTAL_MOST_READ_DAYS'        => $most_read_days,
            'FORUMPORTAL_MOST_COMMENTED_DAYS'   => $most_commented_days,
            'FORUMPORTAL_TOP_CONTRIBUTORS_DAYS' => $top_contributors_days,
            'FORUMPORTAL_MOST_READ_PERIOD_LABEL' => $this->build_period_label($most_read_days),
            'FORUMPORTAL_MOST_COMMENTED_PERIOD_LABEL' => $this->build_period_label($most_commented_days),
            'FORUMPORTAL_TOP_CONTRIBUTORS_PERIOD_LABEL' => $this->build_period_label($top_contributors_days),
            'FORUMPORTAL_POLLS_PERIOD_LABEL' => $this->build_period_label($polls_days),
            'FORUMPORTAL_POLLS_DAYS'        => $polls_days,
            'S_FORUMPORTAL_SHOW_NOTICES'     => $show_notices,
            'S_FORUMPORTAL_SHOW_HERO_EXCERPT' => $show_hero_excerpt,
            'S_FORUMPORTAL_PREVENT_DUPLICATE_TOPICS' => $prevent_duplicate_topics,
            'S_FORUMPORTAL_TYPOGRAPHY_FORUM'  => ($typography_style === 'forum'),
            'S_FORUMPORTAL_TYPOGRAPHY_PORTAL' => ($typography_style === 'portal'),
            'S_FORUMPORTAL_VISUAL_EDITORIAL'  => ($visual_mode === 'editorial'),
            'S_FORUMPORTAL_VISUAL_PROSILVER'  => ($visual_mode === 'prosilver'),
            'S_FORUMPORTAL_POSTS_LAYOUT_LIST' => ($posts_layout === 'list'),
            'S_FORUMPORTAL_POSTS_LAYOUT_GRID2'=> ($posts_layout === 'grid2'),
            'S_FORUMPORTAL_DARK_COMPAT_AUTO'  => ($dark_compat_mode === 'auto'),
            'S_FORUMPORTAL_DARK_COMPAT_FORCE' => ($dark_compat_mode === 'force'),
            'S_FORUMPORTAL_DARK_COMPAT_OFF'   => ($dark_compat_mode === 'off'),
            'S_FORUMPORTAL_STORY_ICON_MEGAPHONE' => ($story_icon_mode === 'megaphone'),
            'S_FORUMPORTAL_STORY_ICON_TOPIC'  => ($story_icon_mode === 'topic'),
            'S_FORUMPORTAL_STORY_ICON_NONE'   => ($story_icon_mode === 'none'),
            'FORUMPORTAL_OPEN_FORUM_COLOR'    => $open_forum_color,
            'FORUMPORTAL_BOARD_TITLE'         => (string) $this->config['sitename'],
            'FORUMPORTAL_BLOCK_ORDER_NOTICES'  => isset($this->config['forumportal_block_order_notices']) ? (int) $this->config['forumportal_block_order_notices'] : 10,
            'FORUMPORTAL_BLOCK_ORDER_HEADLINES' => isset($this->config['forumportal_block_order_headlines']) ? (int) $this->config['forumportal_block_order_headlines'] : 20,
            'FORUMPORTAL_BLOCK_ORDER_TOP_CONTRIBUTORS' => isset($this->config['forumportal_block_order_top_contributors']) ? (int) $this->config['forumportal_block_order_top_contributors'] : 30,
            'FORUMPORTAL_BLOCK_ORDER_POLLS'    => isset($this->config['forumportal_block_order_polls']) ? (int) $this->config['forumportal_block_order_polls'] : 40,
            'FORUMPORTAL_BLOCK_ORDER_MOST_READ' => isset($this->config['forumportal_block_order_most_read']) ? (int) $this->config['forumportal_block_order_most_read'] : 50,
            'FORUMPORTAL_BLOCK_ORDER_MOST_COMMENTED' => isset($this->config['forumportal_block_order_most_commented']) ? (int) $this->config['forumportal_block_order_most_commented'] : 60,
            'FORUMPORTAL_BLOCK_ORDER_CUSTOM_HTML' => isset($this->config['forumportal_block_order_custom_html']) ? (int) $this->config['forumportal_block_order_custom_html'] : 70,
            'FORUMPORTAL_BLOCK_ORDER_CUSTOM_LINKS' => isset($this->config['forumportal_block_order_custom_links']) ? (int) $this->config['forumportal_block_order_custom_links'] : 75,
            'S_FORUMPORTAL_CUSTOM_HEADER'    => $show_custom_header,
            'S_FORUMPORTAL_CUSTOM_HEADER_TEXT' => ($header_title !== '' || $header_subtitle !== ''),
            'S_FORUMPORTAL_HIDE_STANDARD_HEADER' => $hide_standard_header,
            'FORUMPORTAL_HEADER_IMAGE'       => $header_image,
            'FORUMPORTAL_HEADER_TITLE'       => $header_title,
            'FORUMPORTAL_HEADER_SUBTITLE'    => $header_subtitle,
            'FORUMPORTAL_HEADER_HEIGHT'      => $header_height,
            'BODY_CLASS'                     => $body_class,
            'PAGINATION'                     => $this->build_pagination($pagination_base, $total_topics, $per_page, $start),
            'PAGE_NUMBER'                    => $this->build_page_number($total_topics, $per_page, $start),
            'TOTAL_TOPICS'                   => $total_topics,
            'U_FORUMPORTAL'                  => $pagination_base,
            'U_FORUM_INDEX'                  => append_sid($this->phpbb_root_path . 'index.' . $this->php_ext, 'forumportal_bypass=1'),
            'U_FORUM_INDEX_BYPASS'           => append_sid($this->phpbb_root_path . 'index.' . $this->php_ext, 'forumportal_bypass=1'),
            'U_INDEX'                        => append_sid($this->phpbb_root_path . 'index.' . $this->php_ext, 'forumportal_bypass=1'),
            'FORUMPORTAL_NAV_TITLE'          => $nav_title,
            'FORUMPORTAL_META_DESCRIPTION'    => $meta_description,
            'S_FORUMPORTAL_META_DESCRIPTION'  => ($meta_description !== ''),
            'S_HAS_FIXED_HEADLINE'           => $use_fixed_hero,
            'S_FORUMPORTAL_PAGE'             => true,
            'S_FORUMPORTAL_NOINDEX_ROBOTS'   => ($noindex_paginated && $start > 0),
        ));

        /**
         * Allow extensions to modify the Forum Portal page data before the portal template is rendered.
         *
         * @event mundophpbb.forumportal.controller_before_render
         * @var string page_title The portal page title.
         * @since 1.2.15
         */
        $event = new \phpbb\event\data(array(
            'page_title' => $page_title,
        ));
        $this->dispatcher->dispatch('mundophpbb.forumportal.controller_before_render', $event);
        $page_title = $event['page_title'];

        return $this->helper->render('portal_body.html', $page_title);
    }


    protected function clean_meta_description($value)
    {
        $value = trim((string) $value);
        $value = strip_tags($value);
        $value = preg_replace('/\s+/u', ' ', $value);

        if (function_exists('utf8_substr'))
        {
            return utf8_substr($value, 0, 320);
        }

        return substr($value, 0, 320);
    }


    protected function get_fixed_hero_topic(array $forum_ids, $topic_id, $excerpt_limit, $default_image)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $topic_id = (int) $topic_id;

        if (empty($forum_ids) || $topic_id <= 0)
        {
            return array();
        }

        $sql = 'SELECT t.topic_id, t.forum_id, t.icon_id, t.topic_title, t.topic_time, t.topic_views, t.topic_first_post_id,
                       p.post_text, p.bbcode_uid, p.bbcode_bitfield,
                       p.enable_bbcode, p.enable_smilies, p.enable_magic_url,
                       u.user_id, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height,
                       fp.portal_image, fp.portal_excerpt, fp.portal_featured, fp.portal_order
                FROM ' . TOPICS_TABLE . ' t
                INNER JOIN ' . POSTS_TABLE . ' p
                    ON p.post_id = t.topic_first_post_id
                INNER JOIN ' . USERS_TABLE . ' u
                    ON u.user_id = t.topic_poster
                ' . $this->get_portal_topic_join_sql() . '
                WHERE t.topic_id = ' . (int) $topic_id . '
                    AND ' . $this->db->sql_in_set('t.forum_id', $forum_ids) . '
                    AND t.topic_visibility = ' . ITEM_APPROVED . '
                    AND ' . $this->get_portal_topic_visibility_sql();
        $result = $this->db->sql_query_limit($sql, 1);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$row)
        {
            return array();
        }

        return $this->build_topic_display_data($row, $excerpt_limit, $default_image);
    }

    protected function build_topic_display_data(array $row, $excerpt_limit, $default_image)
    {
        $bbcode_options = 0;
        if (!empty($row['enable_bbcode']) && defined('OPTION_FLAG_BBCODE'))
        {
            $bbcode_options |= OPTION_FLAG_BBCODE;
        }
        if (!empty($row['enable_smilies']) && defined('OPTION_FLAG_SMILIES'))
        {
            $bbcode_options |= OPTION_FLAG_SMILIES;
        }
        if (!empty($row['enable_magic_url']) && defined('OPTION_FLAG_LINKS'))
        {
            $bbcode_options |= OPTION_FLAG_LINKS;
        }

        $rendered = generate_text_for_display(
            $row['post_text'],
            $row['bbcode_uid'],
            $row['bbcode_bitfield'],
            $bbcode_options
        );

        $excerpt = trim((string) $row['portal_excerpt']);
        if ($excerpt === '')
        {
            $excerpt = $this->truncate_text($this->extract_plain_text($rendered), $excerpt_limit);
        }
        else
        {
            $excerpt = $this->truncate_text($this->extract_plain_text($excerpt), $excerpt_limit);
        }

        $date_data = $this->build_date_display_data($row['topic_time']);

        return array(
            'TOPIC_ID'        => (int) $row['topic_id'],
            'TITLE'           => $row['topic_title'],
            'EXCERPT'         => $excerpt,
            'IMAGE'           => $this->resolve_topic_image($row, $rendered, $default_image),
            'TOPIC_ICON'      => $this->build_topic_icon_html(isset($row['icon_id']) ? (int) $row['icon_id'] : 0),
            'DATE'            => $date_data['DATE'],
            'DATE_DAY'        => $date_data['DAY'],
            'DATE_MONTH'      => $date_data['MONTH'],
            'DATE_YEAR'       => $date_data['YEAR'],
            'AUTHOR_FULL'     => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
            'AUTHOR_AVATAR'   => $this->build_author_avatar($row),
            'REPLIES'         => 0,
            'VIEWS'           => (int) $row['topic_views'],
            'S_FEATURED'      => (bool) $row['portal_featured'],
            'U_VIEW_TOPIC'    => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . (int) $row['topic_id']),
        );
    }

    protected function build_topic_icon_html($icon_id)
    {
        $icon_id = (int) $icon_id;
        if ($icon_id <= 0 || !defined('ICONS_TABLE'))
        {
            return '';
        }

        if (!array_key_exists($icon_id, $this->topic_icon_cache))
        {
            $this->topic_icon_cache[$icon_id] = '';

            $sql = 'SELECT icons_url, icons_width, icons_height
                FROM ' . ICONS_TABLE . '
                WHERE icons_id = ' . (int) $icon_id;
            $result = $this->db->sql_query_limit($sql, 1);
            $row = $this->db->sql_fetchrow($result);
            $this->db->sql_freeresult($result);

            if ($row && !empty($row['icons_url']))
            {
                $icons_path = trim((string) (isset($this->config['icons_path']) ? $this->config['icons_path'] : 'images/icons'), '/');
                $board_url = function_exists('generate_board_url') ? generate_board_url() . '/' : $this->phpbb_root_path;
                $src = $board_url . $icons_path . '/' . ltrim((string) $row['icons_url'], '/');
                $width = max(0, (int) $row['icons_width']);
                $height = max(0, (int) $row['icons_height']);
                $size = '';
                if ($width > 0)
                {
                    $size .= ' width="' . $width . '"';
                }
                if ($height > 0)
                {
                    $size .= ' height="' . $height . '"';
                }

                $this->topic_icon_cache[$icon_id] = '<img src="' . utf8_htmlspecialchars($src, ENT_COMPAT, 'UTF-8') . '"' . $size . ' alt="" loading="lazy" />';
            }
        }

        return $this->topic_icon_cache[$icon_id];
    }

    protected function build_author_avatar(array $row)
    {
        $avatar = isset($row['user_avatar']) ? (string) $row['user_avatar'] : '';
        $avatar_type = isset($row['user_avatar_type']) ? (string) $row['user_avatar_type'] : '';
        $avatar_width = isset($row['user_avatar_width']) ? (int) $row['user_avatar_width'] : 0;
        $avatar_height = isset($row['user_avatar_height']) ? (int) $row['user_avatar_height'] : 0;

        if ($avatar === '' || $avatar_type === '')
        {
            return '';
        }

        $alt = isset($row['username']) ? (string) $row['username'] : 'USER_AVATAR';

        $avatar_row = array(
            'avatar'        => $avatar,
            'avatar_type'   => $avatar_type,
            'avatar_width'  => $avatar_width,
            'avatar_height' => $avatar_height,
        );

        if (function_exists('phpbb_get_avatar'))
        {
            return phpbb_get_avatar($avatar_row, $alt, false);
        }

        return '';
    }

    protected function build_date_display_data($timestamp)
    {
        $timestamp = (int) $timestamp;
        $month_number = (int) $this->user->format_date($timestamp, 'n');

        return array(
            'DATE'  => $this->format_portal_date($timestamp),
            'DAY'   => $this->user->format_date($timestamp, 'd'),
            'MONTH' => $this->get_portal_month_short($month_number, $timestamp),
            'YEAR'  => $this->user->format_date($timestamp, 'Y'),
        );
    }

    protected function get_portal_month_short($month_number, $timestamp)
    {
        $month_number = (int) $month_number;
        $key = 'FORUMPORTAL_MONTH_SHORT_' . $month_number;
        $translated = $this->user->lang($key);

        if ($translated !== '' && $translated !== $key)
        {
            return $translated;
        }

        return $this->user->format_date((int) $timestamp, 'M');
    }

    protected function sanitize_hex_color($value, $fallback)
    {
        $value = trim((string) $value);
        if (preg_match('/^#[0-9a-fA-F]{6}$/', $value))
        {
            return strtolower($value);
        }

        return $fallback;
    }

    protected function config_bool($key, $default)
    {
        if (!isset($this->config[$key]))
        {
            return (bool) $default;
        }

        return (bool) ((int) $this->config[$key]);
    }

    protected function format_portal_date($timestamp)
    {
        $timestamp = (int) $timestamp;
        $date_format = trim((string) (isset($this->config['forumportal_date_format']) ? $this->config['forumportal_date_format'] : ''));

        if ($date_format === '')
        {
            $user_lang = isset($this->user->data['user_lang']) ? strtolower((string) $this->user->data['user_lang']) : '';

            if ($user_lang === 'pt' || strpos($user_lang, 'pt_') === 0)
            {
                $date_format = 'd/m/Y H:i';
            }
        }

        return ($date_format !== '') ? $this->user->format_date($timestamp, $date_format) : $this->user->format_date($timestamp);
    }

    protected function resolve_topic_image(array $row, $rendered_html, $default_image)
    {
        $image = trim((string) (isset($row['portal_image']) ? $row['portal_image'] : ''));
        if ($image === '__FORUMPORTAL_NO_IMAGE__')
        {
            return '';
        }

        if ($image !== '')
        {
            return $image;
        }

        $post_id = isset($row['topic_first_post_id']) ? (int) $row['topic_first_post_id'] : 0;
        if ($post_id > 0)
        {
            $attachment_image = $this->get_first_attachment_image($post_id);
            if ($attachment_image !== '')
            {
                return $attachment_image;
            }
        }

        $inline_image = $this->get_first_image_from_html($rendered_html);
        if ($inline_image !== '')
        {
            return $inline_image;
        }

        return trim((string) $default_image);
    }

    protected function get_first_attachment_image($post_id)
    {
        if (!defined('ATTACHMENTS_TABLE'))
        {
            return '';
        }

        $sql = 'SELECT attach_id, mimetype, extension, is_orphan
            FROM ' . ATTACHMENTS_TABLE . '
            WHERE post_msg_id = ' . (int) $post_id . '
                AND in_message = 0
                AND is_orphan = 0
            ORDER BY attach_id ASC';
        $result = $this->db->sql_query_limit($sql, 10);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $mimetype = strtolower((string) $row['mimetype']);
            $extension = strtolower((string) $row['extension']);
            if (strpos($mimetype, 'image/') === 0 || in_array($extension, array('jpg', 'jpeg', 'png', 'gif', 'webp', 'bmp', 'svg'), true))
            {
                $this->db->sql_freeresult($result);
                return append_sid($this->phpbb_root_path . 'download/file.' . $this->php_ext, 'id=' . (int) $row['attach_id']);
            }
        }
        $this->db->sql_freeresult($result);

        return '';
    }

    protected function get_first_image_from_html($html)
    {
        $html = (string) $html;
        if ($html === '')
        {
            return '';
        }

        if (!preg_match_all('/<img\b[^>]*>/i', $html, $matches))
        {
            return '';
        }

        foreach ($matches[0] as $img_tag)
        {
            if (!preg_match('/\bsrc=["\']([^"\']+)["\']/i', $img_tag, $src_match))
            {
                continue;
            }

            $src = html_entity_decode(trim((string) $src_match[1]), ENT_QUOTES | ENT_HTML5, 'UTF-8');
            if ($src === '' || stripos($src, 'data:image') === 0)
            {
                continue;
            }

            $signature = strtolower($img_tag . ' ' . $src);
            if (preg_match('/(icon|emoji|emoticon|smil|smiley|avatar|rank|reaction|badge)/i', $signature))
            {
                continue;
            }

            $width = 0;
            $height = 0;
            if (preg_match('/\bwidth=["\']?(\d+)/i', $img_tag, $w_match))
            {
                $width = (int) $w_match[1];
            }
            if (preg_match('/\bheight=["\']?(\d+)/i', $img_tag, $h_match))
            {
                $height = (int) $h_match[1];
            }

            if (($width > 0 && $width <= 96) || ($height > 0 && $height <= 96))
            {
                continue;
            }

            return $src;
        }

        return '';
    }

    protected function get_portal_topic_row($topic_id)
    {
        $sql = 'SELECT *
            FROM ' . $this->portal_topics_table . '
            WHERE topic_id = ' . (int) $topic_id;
        $result = $this->db->sql_query($sql);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        return $row;
    }

    protected function build_pagination($base_url, $total_items, $per_page, $start)
    {
        $total_items = (int) $total_items;
        $per_page = max(1, (int) $per_page);
        $start = max(0, (int) $start);

        if ($total_items <= $per_page)
        {
            return '';
        }

        $total_pages = (int) ceil($total_items / $per_page);
        $current_page = (int) floor($start / $per_page) + 1;
        $window = 2;
        $first_page = max(1, $current_page - $window);
        $last_page = min($total_pages, $current_page + $window);
        $links = array();

        if ($first_page > 1)
        {
            $links[] = $this->build_page_link($base_url, 1, $per_page, $current_page);
            if ($first_page > 2)
            {
                $links[] = '…';
            }
        }

        for ($page = $first_page; $page <= $last_page; $page++)
        {
            $links[] = $this->build_page_link($base_url, $page, $per_page, $current_page);
        }

        if ($last_page < $total_pages)
        {
            if ($last_page < $total_pages - 1)
            {
                $links[] = '…';
            }
            $links[] = $this->build_page_link($base_url, $total_pages, $per_page, $current_page);
        }

        return implode(' ', $links);
    }

    protected function build_page_link($base_url, $page, $per_page, $current_page)
    {
        $page = (int) $page;
        $current_page = (int) $current_page;

        if ($page === $current_page)
        {
            return '<strong>' . $page . '</strong>';
        }

        $start = ($page - 1) * (int) $per_page;
        $url = ($start > 0) ? ($base_url . '?start=' . $start) : $base_url;

        return '<a href="' . utf8_htmlspecialchars($url) . '">' . $page . '</a>';
    }

    protected function build_page_number($total_items, $per_page, $start)
    {
        $total_items = max(0, (int) $total_items);
        $per_page = max(1, (int) $per_page);
        $start = max(0, (int) $start);

        if ($total_items === 0)
        {
            return '1';
        }

        $current_page = (int) floor($start / $per_page) + 1;
        $total_pages = (int) ceil($total_items / $per_page);

        return $current_page . ' / ' . $total_pages;
    }


    protected function get_topic_comment_metric()
    {
        if ($this->topic_comment_metric !== null)
        {
            return $this->topic_comment_metric;
        }

        $this->topic_comment_metric = array(
            'field' => '',
            'type'  => '',
        );

        $sql = 'SELECT * FROM ' . TOPICS_TABLE;
        $result = $this->db->sql_query_limit($sql, 1);
        $row = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!is_array($row) || empty($row))
        {
            return $this->topic_comment_metric;
        }

        $candidates = array(
            'topic_posts_approved'   => 'posts',
            'topic_replies_approved' => 'replies',
            'topic_replies'          => 'replies',
            'topic_posts'            => 'posts',
        );

        foreach ($candidates as $field => $type)
        {
            if (array_key_exists($field, $row))
            {
                $this->topic_comment_metric = array(
                    'field' => $field,
                    'type'  => $type,
                );
                break;
            }
        }

        return $this->topic_comment_metric;
    }

    protected function get_topic_comment_count(array $row)
    {
        $metric = $this->get_topic_comment_metric();

        if (isset($row['topic_comment_metric']))
        {
            $value = (int) $row['topic_comment_metric'];
        }
        else if (!empty($metric['field']) && isset($row[$metric['field']]))
        {
            $value = (int) $row[$metric['field']];
        }
        else
        {
            return 0;
        }

        if ($metric['type'] === 'posts')
        {
            return max(0, $value - 1);
        }

        return max(0, $value);
    }

    protected function get_notice_topics(array $forum_ids, $limit, array &$used_topic_ids, $prevent_duplicates = false)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $topics = array();
        $block_topic_ids = array();

        if (empty($forum_ids))
        {
            return $topics;
        }

        $types = array();
        if (defined('POST_ANNOUNCE'))
        {
            $types[] = (int) POST_ANNOUNCE;
        }
        if (defined('POST_STICKY'))
        {
            $types[] = (int) POST_STICKY;
        }
        if (defined('POST_GLOBAL'))
        {
            $types[] = (int) POST_GLOBAL;
        }

        if (empty($types))
        {
            return $topics;
        }

        $forum_sql = '(' . $this->db->sql_in_set('t.forum_id', $forum_ids);
        if (defined('POST_GLOBAL') && in_array((int) POST_GLOBAL, $types, true))
        {
            $forum_sql .= ' OR t.topic_type = ' . (int) POST_GLOBAL;
        }
        $forum_sql .= ')';

        $sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.topic_type
            FROM ' . TOPICS_TABLE . ' t
            WHERE ' . $forum_sql . '
                AND ' . $this->db->sql_in_set('t.topic_type', $types) . '
                AND t.topic_visibility = ' . ITEM_APPROVED . '
            ORDER BY CASE
                WHEN t.topic_type = ' . (defined('POST_GLOBAL') ? (int) POST_GLOBAL : -1) . ' THEN 0
                WHEN t.topic_type = ' . (defined('POST_ANNOUNCE') ? (int) POST_ANNOUNCE : -1) . ' THEN 1
                WHEN t.topic_type = ' . (defined('POST_STICKY') ? (int) POST_STICKY : -1) . ' THEN 2
                ELSE 3
            END ASC, t.topic_time DESC';
        $result = $this->db->sql_query_limit($sql, $this->get_duplicate_aware_query_limit($limit, $used_topic_ids, $prevent_duplicates));

        while ($row = $this->db->sql_fetchrow($result))
        {
            $topic_id = (int) $row['topic_id'];

            if ($this->should_skip_topic($topic_id, $block_topic_ids, $used_topic_ids, $prevent_duplicates))
            {
                continue;
            }

            $type_label = $this->user->lang('FORUMPORTAL_NOTICE_LABEL');
            if (defined('POST_GLOBAL') && (int) $row['topic_type'] === (int) POST_GLOBAL)
            {
                $type_label = $this->user->lang('FORUMPORTAL_NOTICE_GLOBAL');
            }
            else if (defined('POST_ANNOUNCE') && (int) $row['topic_type'] === (int) POST_ANNOUNCE)
            {
                $type_label = $this->user->lang('FORUMPORTAL_NOTICE_ANNOUNCEMENT');
            }
            else if (defined('POST_STICKY') && (int) $row['topic_type'] === (int) POST_STICKY)
            {
                $type_label = $this->user->lang('FORUMPORTAL_NOTICE_STICKY');
            }

            $topics[] = array(
                'TITLE'        => $row['topic_title'],
                'DATE'         => $this->format_portal_date($row['topic_time']),
                'TYPE'         => $type_label,
                'U_VIEW_TOPIC' => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . $topic_id),
            );

            $this->remember_topic_used($topic_id, $used_topic_ids, $prevent_duplicates);

            if (count($topics) >= $limit)
            {
                break;
            }
        }
        $this->db->sql_freeresult($result);

        return $topics;
    }

    protected function get_top_contributors(array $forum_ids, $limit, $days = 30)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $days = max(0, (int) $days);
        $contributors = array();

        if (empty($forum_ids))
        {
            return $contributors;
        }

        $anonymous_id = defined('ANONYMOUS') ? (int) ANONYMOUS : 1;
        $time_sql = ($days > 0) ? ' AND p.post_time >= ' . (time() - ($days * 86400)) : '';

        $sql = 'SELECT p.poster_id, COUNT(p.post_id) AS contribution_count, MAX(p.post_time) AS last_post_time,
                       u.user_id, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
            FROM ' . POSTS_TABLE . ' p
            INNER JOIN ' . USERS_TABLE . ' u
                ON u.user_id = p.poster_id
            WHERE ' . $this->db->sql_in_set('p.forum_id', $forum_ids) . '
                AND p.post_visibility = ' . ITEM_APPROVED . '
                AND p.poster_id <> ' . $anonymous_id . '
                ' . $time_sql . '
            GROUP BY p.poster_id, u.user_id, u.username, u.user_colour, u.user_avatar, u.user_avatar_type, u.user_avatar_width, u.user_avatar_height
            ORDER BY contribution_count DESC, last_post_time DESC';
        $result = $this->db->sql_query_limit($sql, $limit);

        $rank = 1;
        while ($row = $this->db->sql_fetchrow($result))
        {
            $contributors[] = array(
                'RANK'          => $rank,
                'USERNAME'      => $row['username'],
                'USERNAME_FULL' => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
                'AVATAR'        => $this->build_author_avatar($row),
                'CONTRIBUTIONS' => (int) $row['contribution_count'],
                'LAST_DATE'     => $this->format_portal_date($row['last_post_time']),
                'U_PROFILE'     => append_sid($this->phpbb_root_path . 'memberlist.' . $this->php_ext, 'mode=viewprofile&u=' . (int) $row['user_id']),
            );
            $rank++;
        }
        $this->db->sql_freeresult($result);

        return $contributors;
    }

    protected function handle_poll_vote(array $forum_ids)
    {
        if (!$this->request->is_set_post('forumportal_poll_vote'))
        {
            return '';
        }

        if (!defined('POLL_OPTIONS_TABLE') || !defined('POLL_VOTES_TABLE'))
        {
            return $this->user->lang('FORUMPORTAL_POLL_ERROR_UNAVAILABLE');
        }

        if (function_exists('check_form_key') && !check_form_key('mundophpbb_forumportal_poll'))
        {
            return $this->user->lang('FORM_INVALID');
        }

        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $topic_id = max(0, (int) $this->request->variable('forumportal_poll_topic_id', 0));
        $selected_option_ids = $this->normalise_poll_option_ids($this->request->variable('poll_option_id', array(0)));

        if (empty($forum_ids) || $topic_id <= 0 || empty($selected_option_ids))
        {
            return $this->user->lang('FORUMPORTAL_POLL_ERROR_SELECT');
        }

        $sql = 'SELECT t.topic_id, t.forum_id, t.topic_title, t.poll_title, t.poll_start, t.poll_length, t.poll_max_options, t.poll_vote_change, t.topic_status, f.forum_status
            FROM ' . TOPICS_TABLE . ' t
            INNER JOIN ' . FORUMS_TABLE . ' f
                ON f.forum_id = t.forum_id
            WHERE t.topic_id = ' . (int) $topic_id . '
                AND ' . $this->db->sql_in_set('t.forum_id', $forum_ids) . '
                AND t.topic_visibility = ' . ITEM_APPROVED . '
                AND t.topic_moved_id = 0' . "
                AND t.poll_title <> ''
                AND t.poll_start > 0";
        $result = $this->db->sql_query_limit($sql, 1);
        $poll = $this->db->sql_fetchrow($result);
        $this->db->sql_freeresult($result);

        if (!$poll)
        {
            return $this->user->lang('FORUMPORTAL_POLL_ERROR_UNAVAILABLE');
        }

        $topic_locked = $this->is_topic_locked($poll['topic_status']);
        $forum_locked = $this->is_forum_locked($poll['forum_status']);
        $poll_open = !$topic_locked && !$forum_locked && ((int) $poll['poll_length'] === 0 || ((int) $poll['poll_start'] + (int) $poll['poll_length']) > time());

        if (!$poll_open)
        {
            return $this->user->lang('FORUMPORTAL_POLL_ERROR_CLOSED');
        }

        if (!$this->auth->acl_get('f_vote', (int) $poll['forum_id']))
        {
            return $this->user->lang('FORUMPORTAL_POLL_ERROR_NO_PERMISSION');
        }

        if ($this->is_guest_user() && !$this->config_bool('forumportal_allow_poll_guest_vote', true))
        {
            return $this->user->lang('FORUMPORTAL_POLL_ERROR_GUESTS_DISABLED');
        }

        $max_options = max(1, (int) $poll['poll_max_options']);
        if (count($selected_option_ids) > $max_options)
        {
            return $this->user->lang('FORUMPORTAL_POLL_ERROR_TOO_MANY', $max_options);
        }

        $valid_option_ids = $this->get_valid_poll_option_ids($topic_id, $selected_option_ids);
        if (count($valid_option_ids) !== count($selected_option_ids))
        {
            return $this->user->lang('FORUMPORTAL_POLL_ERROR_SELECT');
        }

        $existing_vote_ids = $this->get_user_poll_vote_ids($topic_id);
        if (!empty($existing_vote_ids) && !(int) $poll['poll_vote_change'])
        {
            return $this->user->lang('FORUMPORTAL_POLL_ERROR_ALREADY_VOTED');
        }

        if (method_exists($this->db, 'sql_transaction'))
        {
            $this->db->sql_transaction('begin');
        }

        if (!empty($existing_vote_ids))
        {
            foreach ($existing_vote_ids as $old_option_id)
            {
                $sql = 'UPDATE ' . POLL_OPTIONS_TABLE . '
                    SET poll_option_total = CASE WHEN poll_option_total > 0 THEN poll_option_total - 1 ELSE 0 END
                    WHERE topic_id = ' . (int) $topic_id . '
                        AND poll_option_id = ' . (int) $old_option_id;
                $this->db->sql_query($sql);
            }

            $anonymous_id = defined('ANONYMOUS') ? (int) ANONYMOUS : 1;
            $user_id = (int) $this->user->data['user_id'];

            if ($user_id !== $anonymous_id)
            {
                $sql = 'DELETE FROM ' . POLL_VOTES_TABLE . '
                    WHERE topic_id = ' . (int) $topic_id . '
                        AND vote_user_id = ' . (int) $user_id;
            }
            else
            {
                $sql = 'DELETE FROM ' . POLL_VOTES_TABLE . '
                    WHERE topic_id = ' . (int) $topic_id . '
                        AND vote_user_id = ' . (int) $anonymous_id . "
                        AND vote_user_ip = '" . $this->db->sql_escape((string) $this->user->ip) . "'";
            }

            $this->db->sql_query($sql);
        }

        foreach ($selected_option_ids as $option_id)
        {
            $sql = 'UPDATE ' . POLL_OPTIONS_TABLE . '
                SET poll_option_total = poll_option_total + 1
                WHERE topic_id = ' . (int) $topic_id . '
                    AND poll_option_id = ' . (int) $option_id;
            $this->db->sql_query($sql);

            $sql_ary = array(
                'topic_id'       => $topic_id,
                'poll_option_id' => (int) $option_id,
                'vote_user_id'   => (int) $this->user->data['user_id'],
                'vote_user_ip'   => (string) $this->user->ip,
            );

            $sql = 'INSERT INTO ' . POLL_VOTES_TABLE . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
            $this->db->sql_query($sql);
        }

        if (method_exists($this->db, 'sql_transaction'))
        {
            $this->db->sql_transaction('commit');
        }

        return $this->user->lang(!empty($existing_vote_ids) ? 'FORUMPORTAL_POLL_CHANGED' : 'FORUMPORTAL_POLL_VOTED');
    }

    protected function get_poll_topics(array $forum_ids, $limit, $days, $fixed_topic_id, $allow_vote, array &$used_topic_ids, $prevent_duplicates = false, $mode = 'recent')
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $days = max(0, (int) $days);
        $fixed_topic_id = max(0, (int) $fixed_topic_id);
        $mode = ((string) $mode === 'random') ? 'random' : 'recent';
        $polls = array();
        $block_topic_ids = array();

        if (empty($forum_ids) || !defined('POLL_OPTIONS_TABLE') || !defined('POLL_VOTES_TABLE'))
        {
            return $polls;
        }

        $time_sql = ($days > 0 && $fixed_topic_id === 0) ? ' AND t.poll_start >= ' . (time() - ($days * 86400)) : '';
        $fixed_sql = ($fixed_topic_id > 0) ? ' AND t.topic_id = ' . $fixed_topic_id : '';
        $order_sql = ($mode === 'random' && $fixed_topic_id === 0 && method_exists($this->db, 'sql_random')) ? $this->db->sql_random() : 't.poll_start DESC, t.topic_time DESC';

        $sql = 'SELECT t.topic_id, t.forum_id, t.topic_title, t.poll_title, t.poll_start, t.poll_length, t.poll_max_options, t.poll_vote_change, t.topic_status, f.forum_status
            FROM ' . TOPICS_TABLE . ' t
            INNER JOIN ' . FORUMS_TABLE . ' f
                ON f.forum_id = t.forum_id
            WHERE ' . $this->db->sql_in_set('t.forum_id', $forum_ids) . '
                AND t.topic_visibility = ' . ITEM_APPROVED . '
                AND t.topic_moved_id = 0' . "
                AND t.poll_title <> ''
                AND t.poll_start > 0" . $fixed_sql . $time_sql . '
            ORDER BY ' . $order_sql;
        $result = $this->db->sql_query_limit($sql, $this->get_duplicate_aware_query_limit($limit, $used_topic_ids, $prevent_duplicates));

        $topic_ids = array();
        while ($row = $this->db->sql_fetchrow($result))
        {
            $topic_id = (int) $row['topic_id'];

            if ($this->should_skip_topic($topic_id, $block_topic_ids, $used_topic_ids, $prevent_duplicates))
            {
                continue;
            }

            $topic_ids[] = $topic_id;
            $polls[$topic_id] = $row;
            $polls[$topic_id]['OPTIONS'] = array();
            $polls[$topic_id]['TOTAL_VOTES'] = 0;

            if (count($topic_ids) >= $limit)
            {
                break;
            }
        }
        $this->db->sql_freeresult($result);

        if (empty($topic_ids))
        {
            return array();
        }

        $sql = 'SELECT topic_id, poll_option_id, poll_option_text, poll_option_total
            FROM ' . POLL_OPTIONS_TABLE . '
            WHERE ' . $this->db->sql_in_set('topic_id', $topic_ids) . '
            ORDER BY topic_id ASC, poll_option_id ASC';
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $topic_id = (int) $row['topic_id'];
            if (!isset($polls[$topic_id]))
            {
                continue;
            }

            $total = max(0, (int) $row['poll_option_total']);
            $polls[$topic_id]['TOTAL_VOTES'] += $total;
            $polls[$topic_id]['OPTIONS'][] = array(
                'ID'    => (int) $row['poll_option_id'],
                'TEXT'  => $this->prepare_poll_text($row['poll_option_text']),
                'VOTES' => $total,
            );
        }
        $this->db->sql_freeresult($result);

        $prepared = array();
        $guest_vote_blocked = $this->is_guest_user() && !$this->config_bool('forumportal_allow_poll_guest_vote', true);
        foreach ($polls as $topic_id => $row)
        {
            if (empty($row['OPTIONS']))
            {
                continue;
            }

            $existing_vote_ids = $this->get_user_poll_vote_ids($topic_id);
            $topic_locked = $this->is_topic_locked($row['topic_status']);
            $forum_locked = $this->is_forum_locked($row['forum_status']);
            $poll_open = !$topic_locked && !$forum_locked && ((int) $row['poll_length'] === 0 || ((int) $row['poll_start'] + (int) $row['poll_length']) > time());
            $can_vote = (bool) $allow_vote
                && !$guest_vote_blocked
                && $poll_open
                && $this->auth->acl_get('f_vote', (int) $row['forum_id'])
                && (empty($existing_vote_ids) || (int) $row['poll_vote_change']);
            $show_results = !$can_vote || !empty($existing_vote_ids);
            $total_votes = max(0, (int) $row['TOTAL_VOTES']);
            $prepared_options = array();

            foreach ($row['OPTIONS'] as $option)
            {
                $percentage = ($total_votes > 0) ? round(((int) $option['VOTES'] / $total_votes) * 100, 1) : 0;
                $prepared_options[] = array(
                    'ID'          => (int) $option['ID'],
                    'TEXT'        => $option['TEXT'],
                    'VOTES'       => (int) $option['VOTES'],
                    'PERCENT'     => $percentage,
                    'PERCENT_INT' => (int) round($percentage),
                    'S_VOTED'     => in_array((int) $option['ID'], $existing_vote_ids, true),
                );
            }

            $prepared[] = array(
                'TOPIC_ID'         => $topic_id,
                'TITLE'            => $this->prepare_poll_text($row['topic_title']),
                'QUESTION'         => $this->prepare_poll_text($row['poll_title']),
                'TOTAL_VOTES'      => $total_votes,
                'MAX_OPTIONS'      => max(1, (int) $row['poll_max_options']),
                'S_CAN_VOTE'       => $can_vote,
                'S_CAN_CHANGE'     => (!empty($existing_vote_ids) && (int) $row['poll_vote_change']),
                'S_HAS_VOTED'      => !empty($existing_vote_ids),
                'S_SHOW_RESULTS'   => $show_results,
                'S_MULTIPLE'       => (max(1, (int) $row['poll_max_options']) > 1),
                'S_CLOSED'         => !$poll_open,
                'S_GUEST_BLOCKED'  => $guest_vote_blocked,
                'U_VIEW_TOPIC'     => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . $topic_id),
                'OPTIONS'          => $prepared_options,
            );

            $this->remember_topic_used($topic_id, $used_topic_ids, $prevent_duplicates);
        }

        return $prepared;
    }

    protected function get_valid_poll_option_ids($topic_id, array $option_ids)
    {
        $valid_option_ids = array();

        if (empty($option_ids))
        {
            return $valid_option_ids;
        }

        $sql = 'SELECT poll_option_id
            FROM ' . POLL_OPTIONS_TABLE . '
            WHERE topic_id = ' . (int) $topic_id . '
                AND ' . $this->db->sql_in_set('poll_option_id', $option_ids);
        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $valid_option_ids[] = (int) $row['poll_option_id'];
        }
        $this->db->sql_freeresult($result);

        sort($valid_option_ids);
        sort($option_ids);

        return $valid_option_ids;
    }

    protected function get_user_poll_vote_ids($topic_id)
    {
        $vote_ids = array();

        if (!defined('POLL_VOTES_TABLE'))
        {
            return $vote_ids;
        }

        $anonymous_id = defined('ANONYMOUS') ? (int) ANONYMOUS : 1;
        $user_id = (int) $this->user->data['user_id'];

        if ($user_id !== $anonymous_id)
        {
            $sql = 'SELECT poll_option_id
                FROM ' . POLL_VOTES_TABLE . '
                WHERE topic_id = ' . (int) $topic_id . '
                    AND vote_user_id = ' . (int) $user_id;
        }
        else
        {
            $sql = 'SELECT poll_option_id
                FROM ' . POLL_VOTES_TABLE . '
                WHERE topic_id = ' . (int) $topic_id . '
                    AND vote_user_id = ' . (int) $anonymous_id . "
                    AND vote_user_ip = '" . $this->db->sql_escape((string) $this->user->ip) . "'";
        }

        $result = $this->db->sql_query($sql);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $vote_ids[] = (int) $row['poll_option_id'];
        }
        $this->db->sql_freeresult($result);

        return $vote_ids;
    }

    protected function is_guest_user()
    {
        $anonymous_id = defined('ANONYMOUS') ? (int) ANONYMOUS : 1;

        return (int) $this->user->data['user_id'] === $anonymous_id;
    }

    protected function is_topic_locked($status)
    {
        return $this->is_locked_status($status, 'ITEM_LOCKED');
    }

    protected function is_forum_locked($status)
    {
        return $this->is_locked_status($status, 'FORUM_LOCKED');
    }

    protected function is_locked_status($status, $constant_name)
    {
        if (defined($constant_name))
        {
            return (int) $status === (int) constant($constant_name);
        }

        return (int) $status === 1;
    }

    protected function normalise_poll_option_ids($option_ids)
    {
        if (!is_array($option_ids))
        {
            $option_ids = array($option_ids);
        }

        $normalised = array();
        foreach ($option_ids as $option_id)
        {
            $option_id = (int) $option_id;
            if ($option_id > 0)
            {
                $normalised[$option_id] = $option_id;
            }
        }

        return array_values($normalised);
    }

    protected function prepare_poll_text($text)
    {
        $text = html_entity_decode((string) $text, ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = strip_tags($text);
        $text = trim($text);

        if (function_exists('censor_text'))
        {
            $text = censor_text($text);
        }

        return utf8_htmlspecialchars($text, ENT_COMPAT, 'UTF-8');
    }

    protected function build_period_label($days)
    {
        $days = max(0, (int) $days);

        if ($days === 1)
        {
            return $this->user->lang('FORUMPORTAL_PERIOD_LAST_DAY');
        }

        if ($days > 1)
        {
            return $this->user->lang('FORUMPORTAL_PERIOD_LAST_DAYS', $days);
        }

        return $this->user->lang('FORUMPORTAL_PERIOD_ALL_TIME');
    }

    protected function get_most_commented_topics(array $forum_ids, $limit, $days, array &$used_topic_ids, $prevent_duplicates = false)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $days = max(0, (int) $days);
        $topics = array();
        $block_topic_ids = array();
        $metric = $this->get_topic_comment_metric();

        if (empty($forum_ids) || empty($metric['field']))
        {
            return $topics;
        }

        $field = $metric['field'];
        $time_sql = ($days > 0) ? ' AND t.topic_time >= ' . (time() - ($days * 86400)) : '';
        $sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.' . $field . ' AS topic_comment_metric, fp.portal_featured
            FROM ' . TOPICS_TABLE . ' t
            ' . $this->get_portal_topic_join_sql() . '
            WHERE ' . $this->db->sql_in_set('t.forum_id', $forum_ids) . '
                AND t.topic_visibility = ' . ITEM_APPROVED . '
                AND ' . $this->get_portal_topic_visibility_sql() . '
                ' . $time_sql . '
            ORDER BY t.' . $field . ' DESC, ' . $this->get_portal_featured_expression() . ' DESC, t.topic_time DESC';
        $result = $this->db->sql_query_limit($sql, $this->get_duplicate_aware_query_limit($limit, $used_topic_ids, $prevent_duplicates));

        while ($row = $this->db->sql_fetchrow($result))
        {
            $topic_id = (int) $row['topic_id'];

            if ($this->should_skip_topic($topic_id, $block_topic_ids, $used_topic_ids, $prevent_duplicates))
            {
                continue;
            }

            $topics[] = array(
                'TITLE'          => $row['topic_title'],
                'DATE'           => $this->format_portal_date($row['topic_time']),
                'COMMENTS'       => $this->get_topic_comment_count($row),
                'S_FEATURED'     => (bool) $row['portal_featured'],
                'U_VIEW_TOPIC'   => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . $topic_id),
            );

            $this->remember_topic_used($topic_id, $used_topic_ids, $prevent_duplicates);

            if (count($topics) >= $limit)
            {
                break;
            }
        }
        $this->db->sql_freeresult($result);

        return $topics;
    }

    protected function get_most_read_topics(array $forum_ids, $limit, $days, array &$used_topic_ids, $prevent_duplicates = false)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $days = max(0, (int) $days);
        $topics = array();
        $block_topic_ids = array();

        if (empty($forum_ids))
        {
            return $topics;
        }

        $time_sql = ($days > 0) ? ' AND t.topic_time >= ' . (time() - ($days * 86400)) : '';
        $sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.topic_views, fp.portal_featured
            FROM ' . TOPICS_TABLE . ' t
            ' . $this->get_portal_topic_join_sql() . '
            WHERE ' . $this->db->sql_in_set('t.forum_id', $forum_ids) . '
                AND t.topic_visibility = ' . ITEM_APPROVED . '
                AND ' . $this->get_portal_topic_visibility_sql() . '
                ' . $time_sql . '
            ORDER BY t.topic_views DESC, ' . $this->get_portal_featured_expression() . ' DESC, t.topic_time DESC';
        $result = $this->db->sql_query_limit($sql, $this->get_duplicate_aware_query_limit($limit, $used_topic_ids, $prevent_duplicates));

        while ($row = $this->db->sql_fetchrow($result))
        {
            $topic_id = (int) $row['topic_id'];

            if ($this->should_skip_topic($topic_id, $block_topic_ids, $used_topic_ids, $prevent_duplicates))
            {
                continue;
            }

            $topics[] = array(
                'TITLE'        => $row['topic_title'],
                'DATE'         => $this->format_portal_date($row['topic_time']),
                'VIEWS'        => (int) $row['topic_views'],
                'S_FEATURED'   => (bool) $row['portal_featured'],
                'U_VIEW_TOPIC' => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . $topic_id),
            );

            $this->remember_topic_used($topic_id, $used_topic_ids, $prevent_duplicates);

            if (count($topics) >= $limit)
            {
                break;
            }
        }
        $this->db->sql_freeresult($result);

        return $topics;
    }

    protected function get_latest_headlines(array $forum_ids, $limit, array &$used_topic_ids, $prevent_duplicates = false)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $headlines = array();
        $block_topic_ids = array();

        if (empty($forum_ids))
        {
            return $headlines;
        }

        $sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, fp.portal_featured
            FROM ' . TOPICS_TABLE . ' t
            ' . $this->get_portal_topic_join_sql() . '
            WHERE ' . $this->db->sql_in_set('t.forum_id', $forum_ids) . '
                AND t.topic_visibility = ' . ITEM_APPROVED . '
                AND ' . $this->get_portal_topic_visibility_sql() . '
            ORDER BY CASE WHEN ' . $this->get_portal_order_expression() . ' > 0 THEN 0 ELSE 1 END ASC, ' . $this->get_portal_order_expression() . ' ASC, ' . $this->get_portal_featured_expression() . ' DESC, t.topic_time DESC';
        $result = $this->db->sql_query_limit($sql, $this->get_duplicate_aware_query_limit($limit, $used_topic_ids, $prevent_duplicates));

        while ($row = $this->db->sql_fetchrow($result))
        {
            $topic_id = (int) $row['topic_id'];

            if ($this->should_skip_topic($topic_id, $block_topic_ids, $used_topic_ids, $prevent_duplicates))
            {
                continue;
            }

            $headlines[] = array(
                'TITLE'        => $row['topic_title'],
                'DATE'         => $this->format_portal_date($row['topic_time']),
                'S_FEATURED'   => (bool) $row['portal_featured'],
                'U_VIEW_TOPIC' => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . $topic_id),
            );

            $this->remember_topic_used($topic_id, $used_topic_ids, $prevent_duplicates);

            if (count($headlines) >= $limit)
            {
                break;
            }
        }
        $this->db->sql_freeresult($result);

        return $headlines;
    }

    protected function should_skip_topic($topic_id, array &$block_topic_ids, array &$used_topic_ids, $prevent_duplicates)
    {
        $topic_id = (int) $topic_id;

        if ($topic_id <= 0)
        {
            return true;
        }

        if (isset($block_topic_ids[$topic_id]))
        {
            return true;
        }

        if ($prevent_duplicates && isset($used_topic_ids[$topic_id]))
        {
            return true;
        }

        $block_topic_ids[$topic_id] = true;

        return false;
    }

    protected function remember_topic_used($topic_id, array &$used_topic_ids, $prevent_duplicates)
    {
        if (!$prevent_duplicates)
        {
            return;
        }

        $topic_id = (int) $topic_id;
        if ($topic_id > 0)
        {
            $used_topic_ids[$topic_id] = true;
        }
    }

    protected function get_duplicate_aware_query_limit($limit, array $used_topic_ids, $prevent_duplicates)
    {
        $limit = max(1, (int) $limit);

        if (!$prevent_duplicates)
        {
            return $limit;
        }

        return min(200, $limit + count($used_topic_ids) + 15);
    }

    protected function get_readable_source_forum_ids()
    {
        $forum_ids = $this->normalise_forum_ids(explode(',', (string) $this->config['forumportal_source_forum']));
        $readable = array();

        foreach ($forum_ids as $forum_id)
        {
            if ($this->auth->acl_get('f_read', $forum_id))
            {
                $readable[] = $forum_id;
            }
        }

        return $readable;
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


    protected function is_auto_include_enabled()
    {
        return !empty($this->config['forumportal_auto_include_source']);
    }

    protected function get_portal_topic_join_sql()
    {
        if ($this->is_auto_include_enabled())
        {
            return 'LEFT JOIN ' . $this->portal_topics_table . ' fp
                    ON fp.topic_id = t.topic_id';
        }

        return 'INNER JOIN ' . $this->portal_topics_table . ' fp
                    ON fp.topic_id = t.topic_id';
    }

    protected function get_portal_topic_visibility_sql()
    {
        if ($this->is_auto_include_enabled())
        {
            return '(fp.topic_id IS NULL OR fp.portal_enabled = 1)';
        }

        return 'fp.portal_enabled = 1';
    }

    protected function get_portal_order_expression()
    {
        return 'COALESCE(fp.portal_order, 0)';
    }

    protected function get_portal_featured_expression()
    {
        return 'COALESCE(fp.portal_featured, 0)';
    }

    protected function get_custom_links()
    {
        $sql = 'SELECT html_value
            FROM ' . $this->forumportal_html_table . "
            WHERE html_key = 'forumportal_custom_links'";
        $result = $this->db->sql_query_limit($sql, 1);
        $links = (string) $this->db->sql_fetchfield('html_value');
        $this->db->sql_freeresult($result);

        return html_entity_decode((string) $links, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function parse_custom_links($raw_links)
    {
        $items = array();
        $lines = preg_split('/\r\n|\r|\n/', (string) $raw_links);

        foreach ($lines as $line)
        {
            $line = trim((string) $line);
            if ($line === '' || preg_match('/^#\s+/', $line))
            {
                continue;
            }

            $title = '';
            $url = '';

            if (strpos($line, '|') !== false)
            {
                $parts = array_map('trim', explode('|', $line, 2));
                $title = (string) $parts[0];
                $url = (string) $parts[1];
            }
            else if (preg_match('/^(.+?)\s+=\s+(.+)$/u', $line, $match))
            {
                // Only accept "Title = URL" when the equal sign is surrounded by spaces.
                // This keeps valid query strings such as viewforum.php?f=5 intact.
                $title = trim((string) $match[1]);
                $url = trim((string) $match[2]);
            }
            else
            {
                $url = $line;
                $title = $line;
            }

            if ($this->looks_like_url($title) && !$this->looks_like_url($url))
            {
                $tmp = $title;
                $title = $url;
                $url = $tmp;
            }

            $url = $this->normalise_custom_link_url($url);
            $title = trim($title);

            if ($url === '' || $title === '')
            {
                continue;
            }

            $items[] = array(
                'TITLE' => $title,
                'URL'   => $url,
            );
        }

        return $items;
    }

    protected function looks_like_url($value)
    {
        $value = trim((string) $value);

        return (bool) preg_match('#^(https?://|mailto:|ftp://|/|\./|\.\./|\#)#i', $value);
    }

    protected function normalise_custom_link_url($url)
    {
        $url = trim((string) $url);
        if ($url === '')
        {
            return '';
        }

        $url = preg_replace('/[\\x00-\\x1F\\x7F]/u', '', $url);

        $scheme = parse_url($url, PHP_URL_SCHEME);
        if ($scheme !== null && $scheme !== false && $scheme !== '')
        {
            $scheme = strtolower((string) $scheme);
            if (!in_array($scheme, array('http', 'https', 'mailto', 'ftp'), true))
            {
                return '';
            }

            return $url;
        }

        if (strpos($url, '//') === 0)
        {
            return '';
        }

        if ($url[0] === '#')
        {
            return $url;
        }

        if (strpos($url, ':') !== false && preg_match('/^[^\\/\\?#]+:/', $url))
        {
            return '';
        }

        // Links entered as viewforum.php?f=14, viewtopic.php?t=10, app.php/route, etc.
        // must point to the board root. If left as plain relative URLs, browsers resolve
        // them against the portal route (/app.php/...), producing /app.php/viewforum.php.
        if ($url[0] !== '/')
        {
            return $this->make_board_absolute_url($url);
        }

        return $url;
    }

    protected function make_board_absolute_url($url)
    {
        $url = trim((string) $url);
        if ($url === '')
        {
            return '';
        }

        $url = preg_replace('#^\\./+#', '', $url);
        if (strpos($url, '../') === 0 || strpos($url, '/../') !== false)
        {
            return '';
        }

        $board_url = function_exists('generate_board_url') ? generate_board_url() : rtrim($this->phpbb_root_path, '/');

        return rtrim((string) $board_url, '/') . '/' . ltrim($url, '/');
    }

    protected function get_custom_html()
    {
        $sql = 'SELECT html_value
            FROM ' . $this->forumportal_html_table . "
            WHERE html_key = 'forumportal_custom_html'";
        $result = $this->db->sql_query_limit($sql, 1);
        $html = (string) $this->db->sql_fetchfield('html_value');
        $this->db->sql_freeresult($result);

        return html_entity_decode((string) $html, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    }

    protected function has_meaningful_markup($html)
    {
        $html = (string) $html;
        if ($html === '')
        {
            return false;
        }

        $plain = html_entity_decode(strip_tags($html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $plain = str_replace(array("\xc2\xa0", '&nbsp;'), ' ', $plain);
        $plain = preg_replace('/\s+/u', ' ', $plain);

        return trim((string) $plain) !== '';
    }

    protected function extract_plain_text($html)
    {
        $text = html_entity_decode(strip_tags((string) $html), ENT_QUOTES | ENT_HTML5, 'UTF-8');
        $text = str_replace(array("\xc2\xa0", '&nbsp;'), ' ', $text);
        $text = preg_replace('/[\x{200B}-\x{200D}\x{FEFF}]/u', '', $text);
        $text = preg_replace('/\s+/u', ' ', $text);
        $text = trim((string) $text);
        $text = preg_replace('/^(?:[-–—:•|]+\s*)+/u', '', $text);

        return trim((string) $text);
    }

    protected function truncate_text($text, $limit)
    {
        $text = trim((string) $text);
        if ($text === '')
        {
            return '';
        }

        $use_utf8 = function_exists('utf8_strlen') && function_exists('utf8_substr');
        $length = $use_utf8 ? utf8_strlen($text) : mb_strlen($text, 'UTF-8');

        if ($length <= $limit)
        {
            return $text;
        }

        $slice = $use_utf8 ? utf8_substr($text, 0, $limit) : mb_substr($text, 0, $limit, 'UTF-8');
        $slice = rtrim((string) $slice);
        $min_boundary = max(1, (int) floor($limit * 0.6));

        if (preg_match('/^(.{' . $min_boundary . ',})\s+\S*$/u', $slice, $match))
        {
            $slice = rtrim($match[1]);
        }

        $slice = rtrim($slice, " \t\n\r\0\x0B,;:.!-–—");

        return $slice . '…';
    }
}
