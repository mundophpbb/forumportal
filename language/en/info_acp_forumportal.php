<?php
/**
 * Forum Portal ACP language [en].
 */

if (!defined('IN_PHPBB'))
{
    exit;
}

if (empty($lang) || !is_array($lang))
{
    $lang = array();
}

$lang = array_merge($lang, array(
    'ACP_FORUMPORTAL'                           => 'Forum Portal',
    'ACP_FORUMPORTAL_SETTINGS'                  => 'Settings',
    'ACP_FORUMPORTAL_SETTINGS_EXPLAIN'          => 'Configure a portal-style homepage fed by topics from one or more forums.',
    'ACP_FORUMPORTAL_GENERAL'                   => 'General settings',
    'ACP_FORUMPORTAL_DISPLAY'                   => 'Display settings',
    'ACP_FORUMPORTAL_DISPLAY_EXPLAIN'           => 'Adjust titles, item counts, fallback image, and the general portal homepage setup.',
    'ACP_FORUMPORTAL_EDITORIAL'                 => 'Editorial controls',
    'ACP_FORUMPORTAL_EDITORIAL_EXPLAIN'         => 'Choose which blocks and metadata appear on the portal. Empty blocks are hidden automatically.',
    'ACP_FORUMPORTAL_CUSTOM_HTML'               => 'Custom HTML block',
    'ACP_FORUMPORTAL_CUSTOM_HTML_SECTION_EXPLAIN' => 'Use this area for notices, institutional text, a simple banner, or complementary content.',
    'ACP_FORUMPORTAL_SAVED'                     => 'Forum Portal settings saved successfully.',
    'ACP_FORUMPORTAL_ENABLED'                   => 'Enable portal',
    'ACP_FORUMPORTAL_ENABLED_EXPLAIN'           => 'Turns the portal page on or off.',
    'ACP_FORUMPORTAL_HOME_ENABLED'              => 'Use portal as homepage',
    'ACP_FORUMPORTAL_HOME_ENABLED_EXPLAIN'      => 'When enabled, requests to index.php are redirected to /portal.',
    'ACP_FORUMPORTAL_SOURCE_FORUM'              => 'Source forums',
    'ACP_FORUMPORTAL_SOURCE_FORUM_EXPLAIN'      => 'Only topics from the selected forums can be published to the portal. Hold Ctrl to select multiple items.',
    'ACP_FORUMPORTAL_PAGE_TITLE'                => 'Portal page title',
    'ACP_FORUMPORTAL_NAV_TITLE'                 => 'Navigation label',
    'ACP_FORUMPORTAL_TOPICS_PER_PAGE'           => 'Number of cards',
    'ACP_FORUMPORTAL_EXCERPT_LIMIT'             => 'Excerpt character limit',
    'ACP_FORUMPORTAL_DEFAULT_IMAGE'             => 'Default image URL',
    'ACP_FORUMPORTAL_FIXED_TOPIC_ID'           => 'Pinned headline topic ID (manual)',
    'ACP_FORUMPORTAL_FIXED_TOPIC_ID_EXPLAIN'   => 'Optional. Manually enter the topic ID of a topic already published in the portal to keep it as the main homepage headline. You can also set this directly from the first post options.',
    'ACP_FORUMPORTAL_DEFAULT_IMAGE_EXPLAIN'     => 'Optional fallback image when a topic has no custom portal image.',
    'ACP_FORUMPORTAL_HTML_POSITION'             => 'Custom HTML position',
    'ACP_FORUMPORTAL_HTML_TOP'                  => 'Top of portal',
    'ACP_FORUMPORTAL_HTML_BOTTOM'               => 'Bottom of portal',
    'ACP_FORUMPORTAL_CUSTOM_HTML_TITLE'         => 'Custom HTML block title',
    'ACP_FORUMPORTAL_CUSTOM_HTML_TITLE_EXPLAIN' => 'Optional. Shows a title above the custom HTML block.',
    'ACP_FORUMPORTAL_CUSTOM_HTML_EXPLAIN'       => 'Optional HTML block shown above or below the list of portal topics.',
    'ACP_FORUMPORTAL_DATE_FORMAT'                    => 'Portal date and time format',
    'ACP_FORUMPORTAL_DATE_FORMAT_EXPLAIN'            => 'Use the same date format options available in phpBB. Leave blank to follow the phpBB/user default format.',
    'ACP_FORUMPORTAL_DATE_FORMAT_DEFAULT'            => 'Use phpBB / user default',
    'ACP_FORUMPORTAL_DATE_FORMAT_CUSTOM'             => 'previously saved custom format',
    'ACP_FORUMPORTAL_HEADLINES_LIMIT'                => 'Headlines block count',
    'ACP_FORUMPORTAL_HEADLINES_LIMIT_EXPLAIN'        => 'Defines how many items appear in the Headlines block on the right column.',
    'ACP_FORUMPORTAL_MOST_READ_LIMIT'                => 'Most read block count',
    'ACP_FORUMPORTAL_MOST_READ_LIMIT_EXPLAIN'        => 'Defines how many items appear in the Most read block.',
    'ACP_FORUMPORTAL_MOST_COMMENTED_LIMIT'           => 'Most commented block count',
    'ACP_FORUMPORTAL_MOST_COMMENTED_LIMIT_EXPLAIN'   => 'Defines how many items appear in the Most commented block.',
    'ACP_FORUMPORTAL_NOTICES_LIMIT'                  => 'Notices and stickies block count',
    'ACP_FORUMPORTAL_NOTICES_LIMIT_EXPLAIN'          => 'Defines how many items appear in the Notices and stickies block.',

    'ACP_FORUMPORTAL_SHOW_AUTHOR'               => 'Show author',
    'ACP_FORUMPORTAL_SHOW_AUTHOR_EXPLAIN'       => 'Show or hide the author name in the portal hero and cards.',
    'ACP_FORUMPORTAL_SHOW_DATE'                 => 'Show date',
    'ACP_FORUMPORTAL_SHOW_DATE_EXPLAIN'         => 'Show or hide the date/time across portal blocks.',
    'ACP_FORUMPORTAL_SHOW_VIEWS'                => 'Show views',
    'ACP_FORUMPORTAL_SHOW_VIEWS_EXPLAIN'        => 'Show or hide the view count in the hero and cards.',
    'ACP_FORUMPORTAL_SHOW_HEADLINES'            => 'Show Latest headlines',
    'ACP_FORUMPORTAL_SHOW_HEADLINES_EXPLAIN'    => 'Show or hide the Latest headlines block in the right column.',
    'ACP_FORUMPORTAL_SHOW_MOST_READ'            => 'Show Most read',
    'ACP_FORUMPORTAL_SHOW_MOST_READ_EXPLAIN'    => 'Show or hide the Most read block.',
    'ACP_FORUMPORTAL_SHOW_MOST_COMMENTED'       => 'Show Most commented',
    'ACP_FORUMPORTAL_SHOW_MOST_COMMENTED_EXPLAIN'=> 'Show or hide the Most commented block.',
    'ACP_FORUMPORTAL_SHOW_NOTICES'              => 'Show Notices and stickies',
    'ACP_FORUMPORTAL_SHOW_NOTICES_EXPLAIN'      => 'Show or hide the block with sticky topics and announcements from the source forums.',
    'ACP_FORUMPORTAL_SHOW_HERO_EXCERPT'         => 'Show excerpt in main headline',
    'ACP_FORUMPORTAL_SHOW_HERO_EXCERPT_EXPLAIN' => 'When disabled, the main headline shows only title, meta and link.',

));
