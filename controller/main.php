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
        $custom_html_position = ((string) $this->config['forumportal_custom_html_position'] === 'bottom') ? 'bottom' : 'top';

        $custom_html = strtr($custom_html, array(
            '{TEXT}'         => $custom_html_title,
            '{TEXT1}'        => $page_title,
            '{BOARD_NAME}'   => (string) $this->config['sitename'],
            '{PORTAL_TITLE}' => $page_title,
            '{NAV_TITLE}'    => $nav_title,
        ));

        $start = max(0, (int) $this->request->variable('start', 0));

        $has_topics = false;
        $has_hero_topic = false;
        $pagination_base = $this->helper->route('mundophpbb_forumportal_controller');
        $headline_limit = max(1, min(15, (int) (isset($this->config['forumportal_headlines_limit']) ? $this->config['forumportal_headlines_limit'] : 5)));
        $most_read_limit = max(1, min(15, (int) (isset($this->config['forumportal_most_read_limit']) ? $this->config['forumportal_most_read_limit'] : 5)));
        $most_commented_limit = max(1, min(15, (int) (isset($this->config['forumportal_most_commented_limit']) ? $this->config['forumportal_most_commented_limit'] : 5)));
        $notices_limit = max(1, min(15, (int) (isset($this->config['forumportal_notices_limit']) ? $this->config['forumportal_notices_limit'] : 5)));
        $show_author = $this->config_bool('forumportal_show_author', true);
        $show_date = $this->config_bool('forumportal_show_date', true);
        $show_views = $this->config_bool('forumportal_show_views', true);
        $show_headlines = $this->config_bool('forumportal_show_headlines', true);
        $show_most_read = $this->config_bool('forumportal_show_most_read', true);
        $show_most_commented = $this->config_bool('forumportal_show_most_commented', true);
        $show_notices = $this->config_bool('forumportal_show_notices', true);
        $show_hero_excerpt = $this->config_bool('forumportal_show_hero_excerpt', true);
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
            $has_sidebar = (!empty($notices) || !empty($headlines) || !empty($most_read_topics) || !empty($most_commented_topics) || ($custom_html !== '' && $custom_html_position === 'top'));

        $this->template->assign_vars(array(
                'S_HAS_HERO_TOPIC'      => true,
                'HERO_TITLE'            => $fixed_hero_topic['TITLE'],
                'HERO_EXCERPT'          => $fixed_hero_topic['EXCERPT'],
                'HERO_IMAGE'            => $fixed_hero_topic['IMAGE'],
                'HERO_DATE'             => $fixed_hero_topic['DATE'],
                'HERO_AUTHOR_FULL'      => $fixed_hero_topic['AUTHOR_FULL'],
                'HERO_VIEWS'            => $fixed_hero_topic['VIEWS'],
                'HERO_S_FEATURED'       => true,
                'U_HERO_VIEW_TOPIC'     => $fixed_hero_topic['U_VIEW_TOPIC'],
            ));
        }

        $query_limit = $per_page + (($start === 0 && $use_fixed_hero) ? 1 : 0);
        $topic_cards_assigned = 0;

        $sql = 'SELECT t.topic_id, t.forum_id, t.topic_title, t.topic_time, t.topic_views, t.topic_first_post_id,
                       p.post_text, p.bbcode_uid, p.bbcode_bitfield,
                       p.enable_bbcode, p.enable_smilies, p.enable_magic_url,
                       u.user_id, u.username, u.user_colour,
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
            if ($use_fixed_hero && (int) $row['topic_id'] === $fixed_topic_id)
            {
                continue;
            }

            if ($topic_cards_assigned >= $per_page)
            {
                break;
            }

            $topic_data = $this->build_topic_display_data($row, $excerpt_limit, $default_image);
            $has_topics = true;

            if ($use_hero_layout && !$has_hero_topic)
            {
                $has_hero_topic = true;
                $has_sidebar = (!empty($notices) || !empty($headlines) || !empty($most_read_topics) || !empty($most_commented_topics) || ($custom_html !== '' && $custom_html_position === 'top'));

        $this->template->assign_vars(array(
                    'S_HAS_HERO_TOPIC'      => true,
                    'HERO_TITLE'            => $topic_data['TITLE'],
                    'HERO_EXCERPT'          => $topic_data['EXCERPT'],
                    'HERO_IMAGE'            => $topic_data['IMAGE'],
                    'HERO_DATE'             => $topic_data['DATE'],
                    'HERO_AUTHOR_FULL'      => $topic_data['AUTHOR_FULL'],
                    'HERO_VIEWS'            => $topic_data['VIEWS'],
                    'HERO_S_FEATURED'       => $topic_data['S_FEATURED'],
                    'U_HERO_VIEW_TOPIC'     => $topic_data['U_VIEW_TOPIC'],
                ));
                continue;
            }

            $this->template->assign_block_vars('topics', $topic_data);
            $topic_cards_assigned++;
        }
        $this->db->sql_freeresult($result);

        $notices = $show_notices ? $this->get_notice_topics($source_forum_ids, $notices_limit) : array();
        foreach ($notices as $notice)
        {
            $this->template->assign_block_vars('notices', $notice);
        }

        $headlines = $show_headlines ? $this->get_latest_headlines($source_forum_ids, $headline_limit) : array();
        foreach ($headlines as $headline)
        {
            $this->template->assign_block_vars('headlines', $headline);
        }

        $most_read_topics = $show_most_read ? $this->get_most_read_topics($source_forum_ids, $most_read_limit) : array();
        foreach ($most_read_topics as $most_read_topic)
        {
            $this->template->assign_block_vars('most_read', $most_read_topic);
        }

        $most_commented_topics = $show_most_commented ? $this->get_most_commented_topics($source_forum_ids, $most_commented_limit) : array();
        foreach ($most_commented_topics as $most_commented_topic)
        {
            $this->template->assign_block_vars('most_commented', $most_commented_topic);
        }

        $has_sidebar = (!empty($notices) || !empty($headlines) || !empty($most_read_topics) || !empty($most_commented_topics) || ($custom_html !== '' && $custom_html_position === 'top'));

        $this->template->assign_vars(array(
            'PORTAL_PAGE_TITLE'              => $page_title,
            'PORTAL_CUSTOM_HTML'             => $custom_html,
            'PORTAL_CUSTOM_HTML_TITLE'       => $custom_html_title,
            'S_PORTAL_CUSTOM_HTML_TOP'       => ($custom_html !== '' && $custom_html_position === 'top'),
            'S_PORTAL_CUSTOM_HTML_BOTTOM'    => ($custom_html !== '' && $custom_html_position === 'bottom'),
            'S_HAS_PORTAL_TOPICS'            => $has_topics,
            'S_HAS_HERO_TOPIC'               => $has_hero_topic,
            'S_HAS_PAGINATION'               => ($total_topics > $per_page),
            'S_HAS_HEADLINES'                => !empty($headlines),
            'S_HAS_MOST_READ'                => !empty($most_read_topics),
            'S_HAS_MOST_COMMENTED'           => !empty($most_commented_topics),
            'S_HAS_SIDEBAR'                  => $has_sidebar,
            'S_FORUMPORTAL_SHOW_AUTHOR'      => $show_author,
            'S_FORUMPORTAL_SHOW_DATE'        => $show_date,
            'S_FORUMPORTAL_SHOW_VIEWS'       => $show_views,
            'S_FORUMPORTAL_SHOW_HEADLINES'   => $show_headlines,
            'S_FORUMPORTAL_SHOW_MOST_READ'   => $show_most_read,
            'S_FORUMPORTAL_SHOW_MOST_COMMENTED' => $show_most_commented,
            'S_FORUMPORTAL_SHOW_NOTICES'     => $show_notices,
            'S_FORUMPORTAL_SHOW_HERO_EXCERPT' => $show_hero_excerpt,
            'PAGINATION'                     => $this->build_pagination($pagination_base, $total_topics, $per_page, $start),
            'PAGE_NUMBER'                    => $this->build_page_number($total_topics, $per_page, $start),
            'TOTAL_TOPICS'                   => $total_topics,
            'U_FORUMPORTAL'                  => $pagination_base,
            'U_FORUM_INDEX'                  => append_sid($this->phpbb_root_path . 'index.' . $this->php_ext, 'forumportal_bypass=1'),
            'U_FORUM_INDEX_BYPASS'           => append_sid($this->phpbb_root_path . 'index.' . $this->php_ext, 'forumportal_bypass=1'),
            'U_INDEX'                        => append_sid($this->phpbb_root_path . 'index.' . $this->php_ext, 'forumportal_bypass=1'),
            'FORUMPORTAL_NAV_TITLE'          => $nav_title,
            'S_HAS_FIXED_HEADLINE'           => $use_fixed_hero,
            'S_FORUMPORTAL_PAGE'             => true,
        ));

        $event = new \phpbb\event\data(array(
            'page_title' => $page_title,
        ));
        $this->dispatcher->dispatch('mundophpbb.forumportal.controller_before_render', $event);

        return $this->helper->render('portal_body.html', $page_title);
    }



    protected function get_fixed_hero_topic(array $forum_ids, $topic_id, $excerpt_limit, $default_image)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $topic_id = (int) $topic_id;

        if (empty($forum_ids) || $topic_id <= 0)
        {
            return array();
        }

        $sql = 'SELECT t.topic_id, t.forum_id, t.topic_title, t.topic_time, t.topic_views, t.topic_first_post_id,
                       p.post_text, p.bbcode_uid, p.bbcode_bitfield,
                       p.enable_bbcode, p.enable_smilies, p.enable_magic_url,
                       u.user_id, u.username, u.user_colour,
                       fp.portal_image, fp.portal_excerpt, fp.portal_featured, fp.portal_order
                FROM ' . TOPICS_TABLE . ' t
                INNER JOIN ' . POSTS_TABLE . ' p
                    ON p.post_id = t.topic_first_post_id
                INNER JOIN ' . USERS_TABLE . ' u
                    ON u.user_id = t.topic_poster
                ' . $this->get_portal_topic_join_sql() . '
                WHERE t.topic_id = ' . $topic_id . '
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

        return array(
            'TITLE'           => $row['topic_title'],
            'EXCERPT'         => $excerpt,
            'IMAGE'           => $this->resolve_topic_image($row, $rendered, $default_image),
            'DATE'            => $this->format_portal_date($row['topic_time']),
            'AUTHOR_FULL'     => get_username_string('full', (int) $row['user_id'], $row['username'], $row['user_colour']),
            'REPLIES'         => 0,
            'VIEWS'           => (int) $row['topic_views'],
            'S_FEATURED'      => (bool) $row['portal_featured'],
            'U_VIEW_TOPIC'    => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . (int) $row['topic_id']),
        );
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

        return '<a href="' . htmlspecialchars($url) . '">' . $page . '</a>';
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

    protected function get_notice_topics(array $forum_ids, $limit)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $topics = array();

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
        $result = $this->db->sql_query_limit($sql, $limit);

        while ($row = $this->db->sql_fetchrow($result))
        {
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
                'U_VIEW_TOPIC' => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . (int) $row['topic_id']),
            );
        }
        $this->db->sql_freeresult($result);

        return $topics;
    }

    protected function get_most_commented_topics(array $forum_ids, $limit)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $topics = array();
        $metric = $this->get_topic_comment_metric();

        if (empty($forum_ids) || empty($metric['field']))
        {
            return $topics;
        }

        $field = $metric['field'];
        $sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.' . $field . ' AS topic_comment_metric, fp.portal_featured
            FROM ' . TOPICS_TABLE . ' t
            ' . $this->get_portal_topic_join_sql() . '
            WHERE ' . $this->db->sql_in_set('t.forum_id', $forum_ids) . '
                AND t.topic_visibility = ' . ITEM_APPROVED . '
                AND ' . $this->get_portal_topic_visibility_sql() . '
            ORDER BY t.' . $field . ' DESC, ' . $this->get_portal_featured_expression() . ' DESC, t.topic_time DESC';
        $result = $this->db->sql_query_limit($sql, $limit);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $topics[] = array(
                'TITLE'          => $row['topic_title'],
                'DATE'           => $this->format_portal_date($row['topic_time']),
                'COMMENTS'       => $this->get_topic_comment_count($row),
                'S_FEATURED'     => (bool) $row['portal_featured'],
                'U_VIEW_TOPIC'   => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . (int) $row['topic_id']),
            );
        }
        $this->db->sql_freeresult($result);

        return $topics;
    }

    protected function get_most_read_topics(array $forum_ids, $limit)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $topics = array();

        if (empty($forum_ids))
        {
            return $topics;
        }

        $sql = 'SELECT t.topic_id, t.topic_title, t.topic_time, t.topic_views, fp.portal_featured
            FROM ' . TOPICS_TABLE . ' t
            ' . $this->get_portal_topic_join_sql() . '
            WHERE ' . $this->db->sql_in_set('t.forum_id', $forum_ids) . '
                AND t.topic_visibility = ' . ITEM_APPROVED . '
                AND ' . $this->get_portal_topic_visibility_sql() . '
            ORDER BY t.topic_views DESC, ' . $this->get_portal_featured_expression() . ' DESC, t.topic_time DESC';
        $result = $this->db->sql_query_limit($sql, $limit);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $topics[] = array(
                'TITLE'        => $row['topic_title'],
                'DATE'         => $this->format_portal_date($row['topic_time']),
                'VIEWS'        => (int) $row['topic_views'],
                'S_FEATURED'   => (bool) $row['portal_featured'],
                'U_VIEW_TOPIC' => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . (int) $row['topic_id']),
            );
        }
        $this->db->sql_freeresult($result);

        return $topics;
    }

    protected function get_latest_headlines(array $forum_ids, $limit)
    {
        $forum_ids = $this->normalise_forum_ids($forum_ids);
        $limit = max(1, (int) $limit);
        $headlines = array();

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
        $result = $this->db->sql_query_limit($sql, $limit);

        while ($row = $this->db->sql_fetchrow($result))
        {
            $headlines[] = array(
                'TITLE'        => $row['topic_title'],
                'DATE'         => $this->format_portal_date($row['topic_time']),
                'S_FEATURED'   => (bool) $row['portal_featured'],
                'U_VIEW_TOPIC' => append_sid($this->phpbb_root_path . 'viewtopic.' . $this->php_ext, 't=' . (int) $row['topic_id']),
            );
        }
        $this->db->sql_freeresult($result);

        return $headlines;
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
