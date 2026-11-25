<?php

namespace mundophpbb\forumportal\service;

use mundophpbb\revolutionappearance\service\settings;

class revolution_appearance
{
	/** @var settings|null */
	protected $settings;

	public function set_settings(settings $settings)
	{
		$this->settings = $settings;
	}

	public function stylesheet($style_path)
	{
		if ($this->settings === null || $style_path !== settings::STYLE_PATH)
		{
			return '';
		}

		$values = $this->settings->read();
		if (!$values['enabled'])
		{
			return '';
		}

		$rules = array();
		$palette = '#phpbb .forumportal-page:not(.forumportal-page--dark-force, .forumportal-page--detected-dark)';
		$cards = ' :is(.forumportal-hero, .forumportal-story, .forumportal-sidecard, .forumportal-custom-html)';
		$sidebar_rows = ' :is(.forumportal-sidebar__block-link, .forumportal-poll__option, .forumportal-contributors__item)';
		$excerpts = ' :is(.forumportal-hero__excerpt, .forumportal-story__excerpt)';
		$text = ' :is(.forumportal-page__title, .forumportal-section-head__title, .forumportal-sidecard__title, '
			. '.forumportal-poll__question, .forumportal-poll__choice-text, .forumportal-poll__status, '
			. '.forumportal-card__stats, .forumportal-authorline, .forumportal-pagination__summary, '
			. '.forumportal-story__excerpt, .forumportal-hero__excerpt)';
		$topic_links = ' :is(.forumportal-hero__title, .forumportal-story__title) a';
		$sidebar_links = ' :is(.forumportal-sidebar__block-link, .forumportal-custom-links__link, .forumportal-poll__title a)';
		$styles = array(
			'page' => array($palette, 'background-color: %s;'),
			'surface' => array($palette . $cards, 'background-color: %s;'),
			'frame' => array($palette . $cards, 'border-color: %s;'),
			'category' => array($palette . ' :is(.forumportal-section-head, .forumportal-sidecard__title)', 'border-bottom-color: %s;'),
			'category_text' => array($palette . ' .forumportal-card__badge', 'color: %s;'),
			'nav' => array($palette . ' .forumportal-topbar', 'background-color: %s;'),
			'row' => array($palette . $sidebar_rows, 'background-color: %s;'),
			'text' => array($palette . ', ' . $palette . $text, 'color: %s;'),
			'link' => array($palette . $topic_links . ', ' . $palette . ' .forumportal-sidebar' . $sidebar_links, 'color: %s !important;'),
			'post_size' => array('#phpbb .forumportal-page' . $excerpts, 'font-size: %spx;'),
		);

		foreach ($styles as $key => list($selector, $declaration))
		{
			if ($values[$key] !== '')
			{
				$rules[] = $selector . ' { ' . sprintf($declaration, $values[$key]) . ' }';
			}
		}

		// Body and heading fonts are loaded by Revolution Appearance. Keep the portal typography setting authoritative.
		if ($values['heading_font'] !== '')
		{
			$rules[] = '#phpbb .forumportal-page--font-portal :is(h1, h2, h3, h4, h5, h6) { font-family: inherit; }';
		}
		if ($values['base_size'] !== '')
		{
			$rules[] = '#phpbb .forumportal-page .forumportal-sidebar { font-size: calc(1.3 * ' . $values['base_size'] . 'px); }';
		}

		return implode("\n", $rules);
	}
}
