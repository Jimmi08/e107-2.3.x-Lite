<?php
if (!defined('e107_INIT')) { exit; }

/**
 * LITE MODIFICATION (Refs #84): News URL routing.
 * Upstream ships only an 'index' key here and routes all other news URLs through
 * e107_core/url/news/*.php (core eRouter SEF configs). Lite drops that core dir
 * and serves every news route from this addon module, consumed via
 * e107::url('news', <key>, $row). Redirect target is root news.php (the single
 * entry point) using the legacy dotted query the news controller parses. Inbound
 * regexes consume any trailing query so pagination (?page=N) survives via $_GET.
 * REVERT WHEN: Lite reinstates e107_core/url/news/ and switches news emission
 * back to e107::getUrl()->create('news/...').
 */
class news_url
{
	function config()
	{
		$config = array();

		// Front page. Owns inbound matching for bare /news/ (?page=N preserved via $_GET).
		$config['index'] = array(
			'alias'    => 'news',
			'regex'    => '^{alias}/?(?:\?.*)?$',
			'sef'      => '{alias}/',
			'redirect' => '{e_BASE}news.php',
			'legacy'   => '{e_BASE}news.php',
		);

		// Default list — outbound synonym of index (no regex; index owns inbound).
		$config['items'] = array(
			'alias'  => 'news',
			'sef'    => '{alias}/',
			'legacy' => '{e_BASE}news.php?default',
		);

		// View item:  /news/view-{id}-{sef}
		$config['item'] = array(
			'alias'    => 'news/view',
			'regex'    => '^{alias}-(\d+)-[\w-]*(?:[/?].*)?$',
			'sef'      => '{alias}-{news_id}-{news_sef}',
			'redirect' => '{e_BASE}news.php?extend.$1',
			'legacy'   => '{e_BASE}news.php?extend.{news_id}',
		);

		// Category list:  /news/category-{id}-{sef}
		$config['category'] = array(
			'alias'    => 'news/category',
			'regex'    => '^{alias}-(\d+)-[\w-]*(?:[/?].*)?$',
			'sef'      => '{alias}-{category_id}-{category_sef}',
			'redirect' => '{e_BASE}news.php?list.$1',
			'legacy'   => '{e_BASE}news.php?list.{category_id}',
		);

		// Category brief:  /news/short-{id}-{sef}
		$config['short'] = array(
			'alias'    => 'news/short',
			'regex'    => '^{alias}-(\d+)-[\w-]*(?:[/?].*)?$',
			'sef'      => '{alias}-{category_id}-{category_sef}',
			'redirect' => '{e_BASE}news.php?cat.$1',
			'legacy'   => '{e_BASE}news.php?cat.{category_id}',
		);

		// All news:  /news/all
		$config['all'] = array(
			'alias'    => 'news/all',
			'regex'    => '^{alias}/?(?:\?.*)?$',
			'sef'      => '{alias}/',
			'redirect' => '{e_BASE}news.php?all.0',
			'legacy'   => '{e_BASE}news.php?all.0',
		);

		// Day archive:  /news/day-{id}
		$config['day'] = array(
			'alias'    => 'news/day',
			'regex'    => '^{alias}-(\d+)(?:[/?].*)?$',
			'sef'      => '{alias}-{id}',
			'redirect' => '{e_BASE}news.php?day.$1',
			'legacy'   => '{e_BASE}news.php?day.{id}',
		);

		// Month archive:  /news/month-{id}
		$config['month'] = array(
			'alias'    => 'news/month',
			'regex'    => '^{alias}-(\d+)(?:[/?].*)?$',
			'sef'      => '{alias}-{id}',
			'redirect' => '{e_BASE}news.php?month.$1',
			'legacy'   => '{e_BASE}news.php?month.{id}',
		);

		// Tag:  /news/tag-{tag}
		$config['tag'] = array(
			'alias'    => 'news/tag',
			'regex'    => '^{alias}-([\w\-]+)(?:[/?].*)?$',
			'sef'      => '{alias}-{tag}',
			'redirect' => '{e_BASE}news.php?tag=$1',
			'legacy'   => '{e_BASE}news.php?tag={tag}',
		);

		// Author:  /news/author-{author}
		$config['author'] = array(
			'alias'    => 'news/author',
			'regex'    => '^{alias}-([\w\-]+)(?:[/?].*)?$',
			'sef'      => '{alias}-{user_name}',
			'redirect' => '{e_BASE}news.php?author=$1',
			'legacy'   => '{e_BASE}news.php?author={user_name}',
		);

		return $config;
	}
}
