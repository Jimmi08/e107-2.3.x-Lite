<?php
require_once("class2.php");

// LITE FEATURE: News is an installable plugin in Lite (installRequired=true).
// Guard this entry point so an uninstalled/missing plugin does not fatal on the
// require_once below. Do not remove when syncing. Mirrors login.php / signup.php
// (redirect non-admins to the front page; show a notice to the main admin).
if (!e107::isInstalled('news') || !is_readable(e_PLUGIN.'news/news.php'))
{
	if (!getperms('0'))                 // not main admin -> front page
	{
		e107::redirect();
		exit;
	}
	require_once(HEADERF);              // main admin -> no redirect, just inform
	e107::getRender()->tablerender('News', 'The News plugin is not installed (or its files are missing). Install it under Admin → Plugins.');
	require_once(FOOTERF);
	exit;
}

require_once(e_PLUGIN."news/news.php");
