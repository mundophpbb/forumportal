(function () {
	'use strict';

	const form = document.getElementById('acp_forumportal');
	const nav = document.querySelector('.forumportal-acp-tabs');
	if (!form || !nav) {
		return;
	}

	const tabs = Array.from(nav.querySelectorAll('a[href^="#"]'), (link) => ({
		link,
		panel: document.getElementById(link.hash.slice(1))
	}));
	if (!tabs.length || tabs.some(({panel}) => !panel || !form.contains(panel))) {
		return;
	}

	function selectTab(active) {
		tabs.forEach(({link, panel}) => {
			const selected = panel === active.panel;
			panel.hidden = !selected;
			if (selected) {
				link.setAttribute('aria-current', 'location');
			} else {
				link.removeAttribute('aria-current');
			}
		});
	}

	function selectFromLocation() {
		const active = tabs.find(({link}) => link.hash === window.location.hash);
		selectTab(active || tabs[0]);
	}

	tabs.forEach((tab) => {
		tab.link.addEventListener('click', (event) => {
			if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
				return;
			}

			event.preventDefault();
			selectTab(tab);
			window.history.replaceState(null, '', tab.link.hash);
		});
	});

	let revealingInvalidField = false;
	form.addEventListener('invalid', (event) => {
		if (revealingInvalidField) {
			return;
		}

		const tab = tabs.find(({panel}) => panel.contains(event.target));
		if (tab) {
			revealingInvalidField = true;
			selectTab(tab);
			window.history.replaceState(null, '', tab.link.hash);
			window.setTimeout(() => { revealingInvalidField = false; }, 0);
		}
	}, true);

	window.addEventListener('hashchange', selectFromLocation);
	selectFromLocation();
})();
