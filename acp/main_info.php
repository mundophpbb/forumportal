<?php
/**
 * Forum Portal ACP module info.
 */

namespace mundophpbb\forumportal\acp;

class main_info
{
    public function module()
    {
        return array(
            'filename'  => '\\mundophpbb\\forumportal\\acp\\main_module',
            'title'     => 'ACP_FORUMPORTAL',
            'modes'     => array(
                'settings' => array(
                    'title' => 'ACP_FORUMPORTAL_SETTINGS',
                    'auth'  => 'ext_mundophpbb/forumportal && acl_a_board',
                    'cat'   => array('ACP_FORUMPORTAL'),
                ),
            ),
        );
    }
}
