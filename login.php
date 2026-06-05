<?php
/*
 * e107 website system
 *
 * Copyright (C) e107 Inc (e107.org)
 * Released under the terms and conditions of the
 * GNU General Public License (http://www.gnu.org/licenses/gpl.txt)
 *
 * Login routine
 *
*/

require_once("class2.php");

// LITE MODIFICATION: upstream's single dense if() guard rewritten
// into four sequential cases (Refs #78) for clarity AND to fix the
// user_reg=0 + landing=login infinite redirect loop. Upstream
// redirects to $prev/SITEURL on registration-disabled, which loops
// with checkMembersOnly when login.php is the members-only landing.
// Lite redirects to membersonly.php (in self_exceptions) to break
// the loop. Revert condition: upstream adopts a per-case structure
// AND a non-looping target for user_reg=0.
$login_admin_redirect = !getperms('0'); // main admin (perms '0') is never bounced from login.php
$prev = varset(e107::getRedirect()->getPreviousUrl(), SITEURL);

if (e_QUERY !== 'preview' && $login_admin_redirect)
{
	// already logged in -> send to the previous page (or profile edit if landing here)
	if (USER)
	{
		if (defined('e_PAGE') && e_PAGE == 'login.php')
		{
			$prev = e107::getUrl()->create('user/myprofile/edit', array('id' => USERID));
		}
		e107::redirect($prev);
		exit();
	}

	// a plugin overrides the login URL -> bounce away from default login.php
	if (e_LOGIN != e_SELF)
	{
		e107::redirect($prev);
		exit();
	}

	// registration disabled (user_reg=0) and no social login -> private-site splash
	if (empty($pref['user_reg']) && !e107::getUserProvider()->isSocialLoginEnabled())
	{
		e107::redirect(e_HTTP.'membersonly.php');
		exit();
	}
}

e107::coreLan('login');


//if(!defined('e_IFRAME')) define('e_IFRAME',true);

$LOGIN_TEMPLATE = e107::getCoreTemplate('login');

if (isset($LOGIN_TEMPLATE['page']['noiframe']) && $LOGIN_TEMPLATE['page']['noiframe'] === true)
{
	if (!defined('e_IFRAME')) define('e_IFRAME', false);
}
else
{
	if (!defined('e_IFRAME')) define('e_IFRAME', true);
}

require_once(HEADERF);
$use_imagecode = ($pref['logcode'] && extension_loaded("gd"));

define("LOGIN_CAPTCHA", $use_imagecode);

//if (LOGIN_CAPTCHA)
//{
	//require_once(e_HANDLER."secure_img_handler.php");
	//$sec_img = new secure_image;
//}

if (!USER || getperms('0'))
{
	if (!defined('LOGINMESSAGE')) define('LOGINMESSAGE', '');		// LOGINMESSAGE only appears with errors
	require_once(e_HANDLER.'form_handler.php'); // required for BC
 
	$sc = e107::getScBatch('login');
	$sc->wrapper('login/page');


	if(!empty($LOGIN_TEMPLATE['page']))
	{
		$LOGIN_TABLE_HEADER = $LOGIN_TEMPLATE['page']['header'];
		$LOGIN_TABLE 		= "<form id='login-page' class='form-signin' method='post' action='".e_SELF."' onsubmit='hashLoginPassword(this)' >".$LOGIN_TEMPLATE['page']['body']."</form>";
		$LOGIN_TABLE_FOOTER = $LOGIN_TEMPLATE['page']['footer'];
	}


	$text = $tp->parseTemplate($LOGIN_TABLE,true, $sc);

	if(getperms('0'))
	{
		$find			= array('[', ']');
      	$replace 		= array("<a href='".e_HTTP."index.php' class='btn btn-primary' role='button'>", "</a>");
      	$return_link	= str_replace($find, $replace, LAN_LOGIN_33);

		echo "<div class='alert alert-block alert-error alert-danger center'>".LAN_LOGIN_32." <br /><br />".$return_link."</div>";

		if(empty($pref['user_reg']))
		{
			$find    	= array('[', ']');
      		$replace 	= array("<a href='".e_ADMIN_ABS."prefs.php#nav-core-prefs-registration' class='btn btn-primary' role='button' target='_blank'>", "</a>");
      		$pref_link 	= str_replace($find, $replace, LAN_LOGIN_35);

			echo "<div class='alert alert-block alert-error alert-danger center'>".LAN_LOGIN_34." <br /><br />".$pref_link."</div>";
		}

	}


	$login_message = SITENAME; //	$login_message = LAN_LOGIN_3." | ".SITENAME;
	if(strpos($LOGIN_TABLE_HEADER,'LOGIN_TABLE_LOGINMESSAGE') === false && strpos($LOGIN_TABLE,'LOGIN_TABLE_LOGINMESSAGE') === false)
	{
		    if(deftrue('e_IFRAME'))
            {  
              echo LOGINMESSAGE;
            }              
	}

	echo $tp->parseTemplate($LOGIN_TABLE_HEADER,true, $sc);
	$ns->tablerender($login_message, $text, 'login_page');
	echo $tp->parseTemplate($LOGIN_TABLE_FOOTER, true, $sc);

}

require_once(FOOTERF);
