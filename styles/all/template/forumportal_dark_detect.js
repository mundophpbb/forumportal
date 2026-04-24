/* Forum Portal dark-theme detector.
 * Adds a portal-only class when the surrounding phpBB style is visually dark.
 * This avoids depending on a specific dark-style class name.
 */
(function () {
	'use strict';

	var DARK_CLASS = 'forumportal-page--detected-dark';
	var TRANSPARENT = /rgba?\(\s*0\s*,\s*0\s*,\s*0\s*(?:,\s*0\s*)?\)|transparent/i;

	function parseRgb(value) {
		var match, alpha;

		if (!value || TRANSPARENT.test(value)) {
			return null;
		}

		match = value.match(/rgba?\(\s*([\d.]+)\s*,\s*([\d.]+)\s*,\s*([\d.]+)(?:\s*,\s*([\d.]+))?\s*\)/i);
		if (!match) {
			return null;
		}

		alpha = typeof match[4] === 'undefined' ? 1 : parseFloat(match[4]);
		if (alpha === 0) {
			return null;
		}

		return [parseFloat(match[1]), parseFloat(match[2]), parseFloat(match[3])];
	}

	function luminance(rgb) {
		return (0.2126 * rgb[0] + 0.7152 * rgb[1] + 0.0722 * rgb[2]) / 255;
	}

	function hasDarkMarker() {
		var root = document.documentElement;
		var body = document.body;
		var links = Array.prototype.map.call(document.querySelectorAll('link[href]'), function (link) {
			return link.getAttribute('href') || '';
		}).join(' ');
		var haystack = [
			root.className || '',
			body ? body.className || '' : '',
			root.getAttribute('data-theme') || '',
			root.getAttribute('data-color-scheme') || '',
			body ? body.getAttribute('data-theme') || '' : '',
			body ? body.getAttribute('data-color-scheme') || '' : '',
			links
		].join(' ').toLowerCase();

		return haystack.indexOf('dark') !== -1 ||
			haystack.indexOf('black') !== -1 ||
			haystack.indexOf('night') !== -1 ||
			haystack.indexOf('midnight') !== -1 ||
			haystack.indexOf('slate') !== -1 ||
			haystack.indexOf('carbon') !== -1;
	}

	function firstVisibleBackground(elements) {
		var i, color;

		for (i = 0; i < elements.length; i++) {
			if (!elements[i]) {
				continue;
			}

			color = parseRgb(window.getComputedStyle(elements[i]).backgroundColor);
			if (color) {
				return color;
			}
		}

		return null;
	}

	function detectDark(page) {
		var body = document.body;
		var background, textColor;

		if (hasDarkMarker()) {
			return true;
		}

		background = firstVisibleBackground([
			page.parentElement,
			document.getElementById('wrap'),
			document.querySelector('.wrap'),
			document.getElementById('page-body'),
			body,
			document.documentElement
		]);

		if (background && luminance(background) < 0.5) {
			return true;
		}

		textColor = parseRgb(window.getComputedStyle(body || document.documentElement).color);
		return !!(textColor && luminance(textColor) > 0.58 && (!background || luminance(background) < 0.62));
	}

	function apply() {
		document.querySelectorAll('.forumportal-page--dark-auto').forEach(function (page) {
			page.classList.toggle(DARK_CLASS, detectDark(page));
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', apply);
	} else {
		apply();
	}

	window.addEventListener('load', apply);
})();
