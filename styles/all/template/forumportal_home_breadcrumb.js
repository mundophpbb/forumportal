(function () {
    'use strict';

    function updateBoardIndexBreadcrumbs() {
        var portalButton = document.querySelector('.forumportal-nav-button[data-forumportal-forum-index-url]');
        if (!portalButton) {
            return;
        }

        var forumIndexUrl = portalButton.getAttribute('data-forumportal-forum-index-url');
        if (!forumIndexUrl) {
            return;
        }

        var links = document.querySelectorAll('.breadcrumbs a[href]');
        for (var i = 0; i < links.length; i++) {
            var link = links[i];
            var href = link.getAttribute('href') || '';

            if (href.indexOf('index.') === -1 || href.indexOf('forumportal_bypass=1') !== -1) {
                continue;
            }

            link.setAttribute('href', forumIndexUrl);
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', updateBoardIndexBreadcrumbs);
    } else {
        updateBoardIndexBreadcrumbs();
    }
}());
