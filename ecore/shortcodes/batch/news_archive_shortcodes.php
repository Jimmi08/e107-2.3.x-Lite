<?php
/*
 * e107 website system
 *
 * Copyright (C) 2008-2009 e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 *
 *
 * $Source: /cvs_backup/e107_0.8/e107_files/shortcode/batch/news_archives.php,v $
 * $Revision$
 * $Date$
 * $Author$
 */

if (!defined('e107_INIT')) { exit; }
// include_once(e_HANDLER.'shortcode_handler.php');
// $news_archive_shortcodes = $tp -> e_sc -> parse_scbatch(__FILE__);


class news_archive_shortcodes extends e_shortcode
{

	function sc_archive_bullet()
	{
		$bullet = '';
		if(defined('BULLET'))
		{
			$bullet = '<img src="'.THEME.'images/'.BULLET.'" alt="" class="icon" />';
		}
		elseif(file_exists(THEME.'images/bullet2.gif'))
		{
			$bullet = '<img src="'.THEME.'images/bullet2.gif" alt="" class="icon" />';
		}
		return $bullet;
	
	}

	
	// LITE MODIFICATION: escape output + emit the link via e107::url().
	// Upstream prints news_title unescaped and hardcodes news.php?item.X (legacy).
	// Reported upstream: https://github.com/e107inc/e107/issues/5785
	// REVERT WHEN: upstream #5785 is merged (escaping + URL API added upstream).
	function sc_archive_link()
	{
		$tp    = e107::getParser();
		$title = $tp->toHTML($this->var['news_title'], TRUE, 'TITLE');
		return "<a href='".e107::url('news', 'item', $this->var)."'>".$title."</a>";
	}

	
	function sc_archive_author()
	{
		return "<a href='".e_BASE."user.php?id.".$this->var['user_id']."'>".$this->var['user_name']."</a>";
	}
	

	function sc_archive_datestamp()
	{
		return e107::getParser()->toDate($this->var['news_datestamp'], 'short');
	}
	

	function sc_archive_category()
	{
		// LITE MODIFICATION: escape category_name — see #5785.
		return e107::getParser()->toHTML($this->var['category_name'], TRUE, 'TITLE');
	}


}
		

