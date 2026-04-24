<?php
/**
 * Forum Portal language file [en].
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
    'FORUMPORTAL_DEFAULT_PAGE_TITLE'      => 'Portal',
    'FORUMPORTAL_DEFAULT_NAV_TITLE'       => 'Portal',
    'FORUMPORTAL_DISABLED'                => 'The portal is currently disabled.',
    'FORUMPORTAL_FORUM_UNAVAILABLE'       => 'The portal source forums are unavailable or you do not have permission to read them.',
    'FORUMPORTAL_NAV'                     => 'Portal',
    'FORUMPORTAL_BACK_TO_FORUM'           => 'Go to forum index',
    'FORUMPORTAL_GO_TO_FORUM'             => 'Open forum',
    'FORUMPORTAL_FORUM_INDEX'             => 'Forum',
    'FORUMPORTAL_READ_MORE'               => 'Read more',
    'FORUMPORTAL_POST_OPTIONS'            => 'Portal settings',
    'FORUMPORTAL_ENABLE_LABEL'            => 'Show this topic on the portal',
    'FORUMPORTAL_ENABLE_EXPLAIN'          => 'Only available for the first post of a topic inside one of the selected source forums.',
    'FORUMPORTAL_IMAGE_LABEL'             => 'External image URL',
    'FORUMPORTAL_IMAGE_EXPLAIN'           => 'Optional. If left blank, the portal will try the first attached image, then the first relevant image found in the post, and finally the default image configured in the ACP.',
    'FORUMPORTAL_NO_IMAGE_LABEL'          => 'Do not use an image',
    'FORUMPORTAL_NO_IMAGE_EXPLAIN'        => 'If checked, the portal will not use an image for this topic, even if there is a manual URL, attachment, icon, or image in the content.',
    'FORUMPORTAL_ORDER_LABEL'             => 'Portal order',
    'FORUMPORTAL_ORDER_EXPLAIN'           => 'Optional. Use 0 for automatic order. Lower values appear earlier on the portal.',
    'FORUMPORTAL_EXCERPT_LABEL'           => 'Custom excerpt',
    'FORUMPORTAL_EXCERPT_EXPLAIN'         => 'Optional. Leave empty to generate the summary automatically from the first post.',
    'FORUMPORTAL_FEATURED_LABEL'           => 'Feature on portal',
    'FORUMPORTAL_FEATURED_EXPLAIN'         => 'Optional. Shows this topic before the others on the portal.',
    'FORUMPORTAL_FIXED_HEADLINE_LABEL'     => 'Use as main headline',
    'FORUMPORTAL_FIXED_HEADLINE_EXPLAIN'   => 'Optional. Sets this topic as the portal\'s main headline. If you uncheck it and this topic is the current fixed headline, the portal returns to automatic behavior.',
    'FORUMPORTAL_EMPTY'                   => 'There are no published topics on the portal yet.',
    'FORUMPORTAL_STATS_REPLIES'           => 'Replies',
    'FORUMPORTAL_STATS_VIEWS'             => 'Views',
    'FORUMPORTAL_STATS_COMMENTS'          => 'Comments',
    'FORUMPORTAL_FEATURED'                => 'Featured',
    'FORUMPORTAL_NO_IMAGE'                => 'No image',
    'FORUMPORTAL_EDITORIAL_HIGHLIGHT'     => 'Editorial highlight',
    'FORUMPORTAL_LATEST_STORIES'          => 'Latest stories',
    'FORUMPORTAL_HEADLINES'               => 'Latest headlines',
    'FORUMPORTAL_NOTICES'                 => 'Notices and stickies',
    'FORUMPORTAL_NOTICE_LABEL'            => 'Notice',
    'FORUMPORTAL_NOTICE_ANNOUNCEMENT'     => 'Announcement',
    'FORUMPORTAL_NOTICE_STICKY'           => 'Sticky',
    'FORUMPORTAL_NOTICE_GLOBAL'           => 'Global',
    'FORUMPORTAL_MOST_READ'               => 'Most read',
    'FORUMPORTAL_MOST_COMMENTED'          => 'Most commented',
    'FORUMPORTAL_TOP_CONTRIBUTORS'        => 'Top contributors',
    'FORUMPORTAL_PERIOD_ALL_TIME'        => 'all time',
    'FORUMPORTAL_PERIOD_LAST_DAY'        => 'last day',
    'FORUMPORTAL_PERIOD_LAST_DAYS'       => 'last %d days',
    'FORUMPORTAL_STATS_CONTRIBUTIONS'     => 'Contributions',
    'FORUMPORTAL_FORUM_GATEWAY'           => 'Continue in the forum',
    'FORUMPORTAL_FORUM_GATEWAY_EXPLAIN'   => 'Read the highlight here and open the forum to see the full topic and the discussion.',
    'FORUMPORTAL_CUSTOM_BLOCK'            => 'Custom block',

    'ACL_CAT_FORUMPORTAL'                => 'Forum Portal',
    'ACL_F_FORUMPORTAL_PUBLISH'          => 'Can publish topics on the portal and edit portal data in the first post options',
    'ACL_M_FORUMPORTAL_EDIT'             => 'Can edit portal publication data in the first post options',
    'ACL_M_FORUMPORTAL_FEATURE'          => 'Can feature or unfeature topics on the portal',

    'FORUMPORTAL_ENABLE_EXPLAIN_AUTO' => 'Topics from the selected source forums can be included automatically. Uncheck this option to keep the topic out of the portal.',
    'FORUMPORTAL_POLLS'                   => 'Polls',
    'FORUMPORTAL_POLL_VOTE'               => 'Vote',
    'FORUMPORTAL_POLL_VIEW_TOPIC'         => 'View poll in topic',
    'FORUMPORTAL_POLL_TOTAL_VOTES'        => 'Total votes',
    'FORUMPORTAL_POLL_SELECT_ONE'         => 'Select one option.',
    'FORUMPORTAL_POLL_MAX_OPTIONS'        => 'Maximum options',
    'FORUMPORTAL_POLL_ALREADY_VOTED'      => 'You have already voted in this poll.',
    'FORUMPORTAL_POLL_GUESTS_DISABLED'   => 'Guests must vote in the topic.',
    'FORUMPORTAL_POLL_CAN_CHANGE'         => 'You can change your vote.',
    'FORUMPORTAL_POLL_CLOSED'             => 'Poll closed.',
    'FORUMPORTAL_POLL_VOTED'              => 'Your vote has been recorded.',
    'FORUMPORTAL_POLL_CHANGED'            => 'Your vote has been updated.',
    'FORUMPORTAL_POLL_ERROR_SELECT'       => 'Select a valid option to vote.',
    'FORUMPORTAL_POLL_ERROR_TOO_MANY'     => 'You selected too many options. The maximum allowed is %1$d.',
    'FORUMPORTAL_POLL_ERROR_CLOSED'       => 'This poll has already closed.',
    'FORUMPORTAL_POLL_ERROR_ALREADY_VOTED'=> 'You have already voted in this poll and vote changes are not allowed.',
    'FORUMPORTAL_POLL_ERROR_GUESTS_DISABLED'=> 'Guest voting from the portal is disabled.',
    'FORUMPORTAL_POLL_ERROR_NO_PERMISSION'=> 'You do not have permission to vote in this poll.',
    'FORUMPORTAL_POLL_ERROR_UNAVAILABLE'  => 'The poll is not available right now.',

));
