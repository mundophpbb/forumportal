/* Forum Portal dark-theme detector.
 * v1022: ignores Forum Portal's own dark CSS files when detecting the active phpBB style.
 * Adds a portal-only class when the surrounding phpBB style is visually dark.
 */
(function () {
	'use strict';

	var DARK_CLASS = 'forumportal-page--detected-dark';
	var TRANSPARENT = /rgba?\(\s*0\s*,\s*0\s*,\s*0\s*(?:,\s*0\s*)?\)|transparent/i;
	var DARK_WORDS = /(?:^|[\W_-])(dark|black|night|midnight|slate|carbon)(?:$|[\W_-])/i;

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

	function isDarkToken(value) {
		return !!(value && DARK_WORDS.test(String(value).toLowerCase()));
	}

	function getBoardStyleTokens() {
		var tokens = [];

		Array.prototype.forEach.call(document.querySelectorAll('link[href]'), function (link) {
			var rawHref = link.getAttribute('href') || '';
			var href = rawHref.split('#')[0].split('?')[0].toLowerCase();
			var match;

			/* Do not let this extension's own filenames such as forumportal_dark_auto.css
			   force ProSilver into dark mode. Only the active board style should count. */
			if (!href || href.indexOf('/ext/') !== -1 || href.indexOf('forumportal_') !== -1) {
				return;
			}

			match = href.match(/(?:^|\/)styles\/([^\/]+)\/theme\//i);
			if (match && match[1]) {
				tokens.push(match[1]);
				tokens.push(href);
			}
		});

		return tokens.join(' ');
	}

	function hasDarkMarker() {
		var root = document.documentElement;
		var body = document.body;
		var markerHaystack = [
			root.className || '',
			body ? body.className || '' : '',
			root.getAttribute('data-theme') || '',
			root.getAttribute('data-color-scheme') || '',
			body ? body.getAttribute('data-theme') || '' : '',
			body ? body.getAttribute('data-color-scheme') || '' : ''
		].join(' ');

		return isDarkToken(markerHaystack) || isDarkToken(getBoardStyleTokens());
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
			page.classList.remove(DARK_CLASS);
			if (detectDark(page)) {
				page.classList.add(DARK_CLASS);
			}
		});
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', apply);
	} else {
		apply();
	}

	window.addEventListener('load', apply);
})();
