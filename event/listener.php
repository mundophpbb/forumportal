<?php
/**
 *
 * Forum Portal event listener.
 *
 */

namespace mundophpbb\forumportal\event;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class listener implements EventSubscriberInterface
{
    /** @var \phpbb\auth\auth */
    protected $auth;

    /** @var \phpbb\config\config */
    protected $config;

    /** @var \phpbb\controller\helper */
    protected $helper;

    /** @var \phpbb\db\driver\driver_interface */
    protected $db;

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

    public static function getSubscribedEvents()
    {
        return array(
            'core.user_setup'                   => 'load_language_on_setup',
            'core.permissions'                  => 'add_permissions',
            'core.page_header'                  => 'handle_page_header',
            'core.posting_modify_template_vars' => 'inject_posting_fields',
            'core.submit_post_end'              => 'save_portal_topic_data',
        );
    }

    public function __construct(
        \phpbb\auth\auth $auth,
        \phpbb\config\config $config,
        \phpbb\controller\helper $helper,
        \phpbb\db\driver\driver_interface $db,
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
        $this->request = $request;
        $this->template = $template;
        $this->user = $user;
        $this->phpbb_root_path = $phpbb_root_path;
        $this->php_ext = $php_ext;
        $this->portal_topics_table = $table_prefix . 'forumportal_topics';
    }

    public function load_language_on_setup($event)
    {
        $lang_set_ext = $event['lang_set_ext'];
        $lang_set_ext[] = array(
            'ext_name' => 'mundophpbb/forumportal',
            'lang_set' => 'common',
        );
        $event['lang_set_ext'] = $lang_set_ext;
    }

    public function add_permissions($event)
    {
        $categories = $event['categories'];
        $permissions = $event['permissions'];

        $categories['forumportal'] = 'ACL_CAT_FORUMPORTAL';
        $permissions['f_forumportal_publish'] = array(
            'lang' => 'ACL_F_FORUMPORTAL_PUBLISH',
            'cat'  => 'forumportal',
        );
        $permissions['m_forumportal_edit'] = array(
            'lang' => 'ACL_M_FORUMPORTAL_EDIT',
            'cat'  => 'forumportal',
        );
        $permissions['m_forumportal_feature'] = array(
            'lang' => 'ACL_M_FORUMPORTAL_FEATURE',
            'cat'  => 'forumportal',
        );

        $event['categories'] = $categories;
        $event['permissions'] = $permissions;
    }

    public function handle_page_header($event)
    {
        $this->add_page_header_link();
        $this->redirect_index_to_portal();
    }

    protected function add_page_header_link()
    {
        $nav_title = trim((string) $this->config['forumportal_nav_title']);
        if ($nav_title === '')
        {
            $nav_title = $this->user->lang('FORUMPORTAL_DEFAULT_NAV_TITLE');
        }

        $forum_index_url = append_sid($this->phpbb_root_path . 'index.' . $this->php_ext, 'forumportal_bypass=1');

        $this->template->assign_vars(array(
            'U_FORUMPORTAL'              => $this->helper->route('mundophpbb_forumportal_controller'),
            'U_FORUM_INDEX_BYPASS'       => $forum_index_url,
            'FORUMPORTAL_NAV_TITLE'      => $nav_title,
            'S_FORUMPORTAL_HOME_ENABLED' => ((int) $this->config['forumportal_home_enabled'] === 1),
            'U_INDEX'                    => (((int) $this->config['forumportal_home_enabled'] === 1) ? $forum_index_url : append_sid($this->phpbb_root_path . 'index.' . $this->php_ext)),
        ));
    }

    protected function redirect_index_to_portal()
    {
        if (defined('ADMIN_START') || !isset($this->config['forumportal_home_enabled']) || !(int) $this->config['forumportal_home_enabled'])
        {
            return;
        }

        $script_name = basename((string) $this->request->server('SCRIPT_NAME'));
        if ($script_name !== 'index.' . $this->php_ext)
        {
            return;
        }

        if ((bool) $this->request->variable('forumportal_bypass', false))
        {
            return;
        }

        redirect($this->helper->route('mundophpbb_forumportal_controller'));
    }

    public function inject_posting_fields($event)
    {
        $source_forums = $this->get_source_forum_ids();
        $forum_id = (int) $event['forum_id'];
        $mode = (string) $event['mode'];
        $post_data = $event['post_data'];
        $post_id = (int) $event['post_id'];

        $can_publish = $forum_id > 0 && ($this->auth->acl_get('f_forumportal_publish', $forum_id) || $this->auth->acl_get('m_forumportal_edit', $forum_id));
        $can_feature = $forum_id > 0 && $this->auth->acl_get('m_forumportal_feature', $forum_id);

        $show_fields = !empty($source_forums)
            && in_array($forum_id, $source_forums, true)
            && ($mode === 'post' || ($post_id > 0 && isset($post_data['topic_first_post_id']) && $post_id === (int) $post_data['topic_first_post_id']))
            && ($can_publish || $can_feature);

        $portal_enabled = false;
        $portal_image = '';
        $portal_excerpt = '';
        $portal_featured = false;
        $portal_fixed_headline = false;
        $portal_no_image = false;
        $portal_order = 0;

        if ($show_fields)
        {
            $topic_id = 0;
            if (!empty($post_data['topic_id']))
            {
                $topic_id = (int) $post_data['topic_id'];
            }
            else if ((int) $this->request->variable('t', 0) > 0)
            {
                $topic_id = (int) $this->request->variable('t', 0);
            }

            if ($topic_id > 0)
            {
                $meta = $this->get_portal_topic_row($topic_id);
                if ($meta)
                {
                    $portal_enabled = (bool) $meta['portal_enabled'];
                    $portal_image = (string) $meta['portal_image'];
                    $portal_excerpt = (string) $meta['portal_excerpt'];
                    $portal_featured = (bool) $meta['portal_featured'];
                    $portal_no_image = ((string) $meta['portal_image'] === '__FORUMPORTAL_NO_IMAGE__');
                    $portal_order = isset($meta['portal_order']) ? (int) $meta['portal_order'] : 0;
                    if ($portal_no_image)
                    {
                        $portal_image = '';
                    }
                }

                $portal_fixed_headline = ((int) (isset($this->config['forumportal_fixed_topic_id']) ? $this->config['forumportal_fixed_topic_id'] : 0) === $topic_id);
            }
        }

        $this->template->assign_vars(array(
            'S_FORUMPORTAL_FIELDS'         => $show_fields && (int) $this->config['forumportal_enabled'],
            'S_FORUMPORTAL_CAN_PUBLISH'    => $can_publish,
            'S_FORUMPORTAL_CAN_FEATURE'    => $can_feature,
            'S_FORUMPORTAL_ENABLED'        => $portal_enabled,
            'FORUMPORTAL_IMAGE'            => $portal_image,
            'FORUMPORTAL_EXCERPT'          => $portal_excerpt,
            'S_FORUMPORTAL_FEATURED'       => $portal_featured,
            'S_FORUMPORTAL_FIXED_HEADLINE' => $portal_fixed_headline,
            'S_FORUMPORTAL_NO_IMAGE'       => $portal_no_image,
            'FORUMPORTAL_ORDER'            => $portal_order,
        ));
    }

    public function save_portal_topic_data($event)
    {
        if (!(int) $this->config['forumportal_enabled'])
        {
            return;
        }

        $data = isset($event['data']) ? $event['data'] : array();
        $mode = isset($event['mode']) ? (string) $event['mode'] : '';
        $forum_id = isset($data['forum_id']) ? (int) $data['forum_id'] : (int) $this->request->variable('f', 0);
        $topic_id = isset($data['topic_id']) ? (int) $data['topic_id'] : (int) $this->request->variable('t', 0);
        $post_id = isset($data['post_id']) ? (int) $data['post_id'] : (int) $this->request->variable('p', 0);
        $topic_first_post_id = isset($data['topic_first_post_id']) ? (int) $data['topic_first_post_id'] : 0;

        if (!in_array($forum_id, $this->get_source_forum_ids(), true))
        {
            return;
        }

        $can_publish = $this->auth->acl_get('f_forumportal_publish', $forum_id) || $this->auth->acl_get('m_forumportal_edit', $forum_id);
        $can_feature = $this->auth->acl_get('m_forumportal_feature', $forum_id);

        if (!$can_publish && !$can_feature)
        {
            return;
        }

        if (!($mode === 'post' || ($mode === 'edit' && $post_id > 0 && $topic_first_post_id > 0 && $post_id === $topic_first_post_id)))
        {
            return;
        }

        if ($topic_id <= 0)
        {
            return;
        }

        $existing = $this->get_portal_topic_row($topic_id);
        $portal_enabled = $can_publish ? (int) $this->request->variable('forumportal_enabled', 0) : (int) (!empty($existing['portal_enabled']));
        $portal_image = $can_publish ? trim((string) $this->request->variable('forumportal_image', '', true)) : (string) (!empty($existing['portal_image']) ? $existing['portal_image'] : '');
        $portal_excerpt = $can_publish ? trim((string) $this->request->variable('forumportal_excerpt', '', true)) : (string) (!empty($existing['portal_excerpt']) ? $existing['portal_excerpt'] : '');
        $portal_no_image = $can_publish ? (int) $this->request->variable('forumportal_no_image', 0) : (int) ((isset($existing['portal_image']) && (string) $existing['portal_image'] === '__FORUMPORTAL_NO_IMAGE__') ? 1 : 0);
        $portal_order = $can_publish ? max(0, (int) $this->request->variable('forumportal_order', 0)) : (int) (isset($existing['portal_order']) ? $existing['portal_order'] : 0);
        $portal_featured = $can_feature ? (int) $this->request->variable('forumportal_featured', 0) : (int) (!empty($existing['portal_featured']));
        $portal_fixed_headline = $can_feature ? (int) $this->request->variable('forumportal_fixed_headline', 0) : 0;
        $current_fixed_topic_id = isset($this->config['forumportal_fixed_topic_id']) ? (int) $this->config['forumportal_fixed_topic_id'] : 0;

        if ($portal_fixed_headline && !$portal_enabled && $can_publish)
        {
            $portal_enabled = 1;
        }

        if ($portal_no_image)
        {
            $portal_image = '__FORUMPORTAL_NO_IMAGE__';
        }

        if (!$portal_enabled && $portal_image === '__FORUMPORTAL_NO_IMAGE__')
        {
            $portal_image = '';
        }

        if (!$portal_enabled && $portal_order > 0)
        {
            $portal_order = 0;
        }

        if (!$portal_enabled && $portal_image === '' && $portal_excerpt === '' && !$portal_featured && $portal_order <= 0)
        {
            $sql = 'DELETE FROM ' . $this->portal_topics_table . '
                WHERE topic_id = ' . (int) $topic_id;
            $this->db->sql_query($sql);

            if ($current_fixed_topic_id === $topic_id)
            {
                $this->config->set('forumportal_fixed_topic_id', 0);
            }
            return;
        }

        $sql_ary = array(
            'topic_id'        => (int) $topic_id,
            'portal_enabled'  => $portal_enabled ? 1 : 0,
            'portal_image'    => $portal_image,
            'portal_excerpt'  => $portal_excerpt,
            'portal_featured' => $portal_featured ? 1 : 0,
            'portal_order'    => $portal_order,
            'portal_updated'  => time(),
        );

        if ($existing)
        {
            $sql = 'UPDATE ' . $this->portal_topics_table . '
                SET ' . $this->db->sql_build_array('UPDATE', $sql_ary) . '
                WHERE topic_id = ' . (int) $topic_id;
            $this->db->sql_query($sql);
        }
        else
        {
            $sql = 'INSERT INTO ' . $this->portal_topics_table . ' ' . $this->db->sql_build_array('INSERT', $sql_ary);
            $this->db->sql_query($sql);
        }

        if ($can_feature)
        {
            if ($portal_fixed_headline && $portal_enabled)
            {
                $this->config->set('forumportal_fixed_topic_id', (int) $topic_id);
            }
            else if (!$portal_fixed_headline && $current_fixed_topic_id === $topic_id)
            {
                $this->config->set('forumportal_fixed_topic_id', 0);
            }
        }
    }

    protected function get_source_forum_ids()
    {
        $forum_ids = array();
        foreach (explode(',', (string) $this->config['forumportal_source_forum']) as $forum_id)
        {
            $forum_id = (int) $forum_id;
            if ($forum_id > 0)
            {
                $forum_ids[$forum_id] = $forum_id;
            }
        }

        return array_values($forum_ids);
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
}
