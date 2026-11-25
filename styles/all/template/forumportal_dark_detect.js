/* Forum Portal dark-theme detector.
 * v1.2.14: only accepts explicit dark-mode tokens/attributes.
 * It must not treat arbitrary class names such as "darkchocolate" as dark mode.
 */
(function () {
	'use strict';

	var DARK_CLASS = 'forumportal-page--detected-dark';
	var TRANSPARENT = /rgba?\(\s*0\s*,\s*0\s*,\s*0\s*(?:,\s*0\s*)?\)|transparent/i;
	var DARK_TOKENS = {
		'dark': true,
		'dark-mode': true,
		'darkmode': true,
		'phpbb-dark': true,
		'color-scheme-dark': true,
		'theme-dark': true,
		'black': true,
		'night': true,
		'midnight': true,
		'carbon': true
	};

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

	function normaliseToken(value) {
		return String(value || '').trim().toLowerCase();
	}

	function isDarkToken(value) {
		return !!DARK_TOKENS[normaliseToken(value)];
	}

	function hasDarkClass(element) {
		if (!element || !element.classList) {
			return false;
		}

		return Array.prototype.some.call(element.classList, function (className) {
			return isDarkToken(className);
		});
	}

	function hasDarkDataAttribute(element) {
		var value;

		if (!element) {
			return false;
		}

		value = normaliseToken(element.getAttribute('data-theme'));
		if (isDarkToken(value)) {
			return true;
		}

		value = normaliseToken(element.getAttribute('data-color-scheme'));
		return isDarkToken(value);
	}

	function styleNameLooksDark(styleName) {
		var parts;

		styleName = normaliseToken(styleName);
		if (!styleName) {
			return false;
		}

		/* Split style identifiers into real tokens. This allows names such as
		   "my_style_dark" or "theme-dark", but not "darkchocolate". */
		parts = styleName.split(/[^a-z0-9]+/);
		return parts.some(isDarkToken);
	}

	function hasDarkBoardStyleName() {
		var darkStyle = false;

		Array.prototype.forEach.call(document.querySelectorAll('link[href]'), function (link) {
			var rawHref = link.getAttribute('href') || '';
			var href = rawHref.split('#')[0].split('?')[0].toLowerCase();
			var match;

			if (darkStyle || !href || href.indexOf('/ext/') !== -1 || href.indexOf('forumportal_') !== -1) {
				return;
			}

			match = href.match(/(?:^|\/)styles\/([^\/]+)\/theme\//i);
			if (match && match[1] && styleNameLooksDark(match[1])) {
				darkStyle = true;
			}
		});

		return darkStyle;
	}

	function hasDarkMarker() {
		var root = document.documentElement;
		var body = document.body;

		return hasDarkClass(root) || hasDarkClass(body) ||
			hasDarkDataAttribute(root) || hasDarkDataAttribute(body) ||
			hasDarkBoardStyleName();
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
