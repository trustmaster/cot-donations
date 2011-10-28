<?PHP

/**
 * PayPal Donations
 *
 * @package Cotonti
 * @version 1.1
 * @author Kilandor - Jason Booth
 * @copyright All rights reserved. 2009-2011
 * @license BSD
 */

if (!defined('COT_CODE') && !defined('COT_PLUG')) { die('Wrong URL.'); }

 global $db_donations, $db_x;
$db_donations = (isset($db_donations)) ? $db_donations : $db_x . 'donations';
$db_donations_users = (isset($db_donations_users)) ? $db_donations_users : $db_x . 'donations_users';

require_once cot_langfile('donations', 'plug');

//------------------------------------\\
//navigation, which is used on all pages, quick easy, effective way to keep it all in one place.

function donations_nav()
{
	global $t, $L, $usr, $m;
	if ($usr['isadmin'] && $m == "admin")
	{
		$t->assign(array(
			"DONATIONS_NAV_ADMIN_ADD" => '<a href="' . cot_url('plug', 'e=donations&m=admin&s=add') . '">' . $L['donations_nav_add'] . '</a>',
		));
		$t->parse("MAIN.DONATIONS_HEADER.DONATIONS_NAV_ADMIN");
	}
	elseif ($usr['id'])
	{
		if ($usr['isadmin'])
		{
			$t->assign(array(
				"DONATIONS_NAV_ADMIN" => '<a href="' . cot_url('plug', 'e=donations&m=admin') . '">' . $L['donations_nav_admin'] . '</a>'
			));
			$t->parse("MAIN.DONATIONS_HEADER.DONATIONS_NAV_ADMIN");
		}
		//$t->parse("MAIN.DONATIONS_HEADER.DONATIONS_NAV_USER");
	}
	else
	{
		//
	}
}

//------------------------------------\\
//------------------------------------\\
//Used to call the Purchase URL's

function donations_link()
{
	global $L, $usr, $cfg, $s;
	$paypal_mode = ($cfg['plugin']['donations']['paypal_mode'] == "Live") ? "www.paypal.com" : "www.sandbox.paypal.com";
	$paypal_form_data = "<form action=\"https://" . $paypal_mode . "/cgi-bin/webscr\" method=\"post\">\r\n";
	$paypal_form_data .= "	<input type=\"hidden\" name=\"cmd\" value=\"_donations\" />\r\n";
	$paypal_form_data .= "	<input type=\"hidden\" name=\"business\" value=\"" . $cfg['plugin']['donations']['paypal_email'] . "\" />\r\n"; //Normal method disabled for testing.htmlspecialchars($cfg['plugin']['donations']['paypal_buisness_email'])
	$paypal_form_data .= "	<input type=\"hidden\" name=\"item_name\" value=\"" . $cfg['plugin']['donations']['paypal_donation_title'] . "\" />\r\n";
	//$paypal_form_data .= "	<input type=\"hidden\" name=\"item_number\" value=\"".$cfg['plugin']['donations']['paypal_donation_uid']."\" />\r\n";
	//$paypal_form_data .= "	<input type=\"hidden\" name=\"no_note\" value=\"1\" />\r\n";
	$paypal_form_data .= "	<input type=\"hidden\" name=\"no_shipping\" value=\"1\" />\r\n";
	$paypal_form_dat .= "	<input type=\"hidden\" name=\"notify_url\" value=\"" . $cfg['mainurl'] . "/" . cot_url('plug', 'r=donations', '', true) . "\" />\r\n";
	$paypal_form_data .= "	<input type=\"hidden\" name=\"return\" value=\"" . $cfg['mainurl'] . "/" . cot_url('plug', 'e=donations&m=return', '', true) . "\" />\r\n";
	$paypal_form_data .= "	<input type=\"hidden\" name=\"cancel_return\" value=\"" . $cfg['mainurl'] . "/" . cot_url('plug', 'e=donations', '', true) . "\" />\r\n";
	$paypal_form_data .= "	<input type=\"hidden\" name=\"notify_url\" value=\"" . $cfg['mainurl'] . "/" . cot_url('plug', 'r=donations', '', true) . "\" />\r\n";
	$paypal_form_data .= "	<input type=\"hidden\" name=\"rm\" value=\"2\" />\r\n"; // Return method 0/ommited get/ 1 get / 2 post
	$paypal_form_data .= "	<input type=\"hidden\" name=\"currency_code\" value=\"" . htmlspecialchars($cfg['plugin']['donations']['paypal_currency']) . "\" />\r\n";
	$paypal_form_data .= "	<input type=\"hidden\" name=\"os0\" value=\"" . $L['donations_purchase_paypal_userid'] . $usr['id'] . $L['donations_purchase_paypal_username'] . $usr['name'] . "\" />\r\n";
	$paypal_form_data .= "	<input type=\"hidden\" name=\"custom\" value=\"" . $usr['id'] . "\" />\r\n";
	$paypal_form_data .= "	<input type=\"image\" src=\"" . $L['donations_button_img'] . "\" class=\"formimg\" name=\"submit\" alt=\"" . $L['donations_button_txt'] . "\" />\r\n";
	$paypal_form_data .= "</form>\r\n";

	return ($paypal_form_data);
}

//------------------------------------\\
//------------------------------------\\

function donations_title()
{
	global $L, $usr, $cfg, $m, $id, $s, $a;
	$m = htmlspecialchars($m);
	$s = htmlspecialchars($s);
	if ($m == "admin")
	{
		$url = $L['donations_title_seperator'] . "<a href=\"plug.php?e=donations&amp;m=admin\">" . $L['donations_nav_admin'] . "</a>";
		if (!empty($s))
		{
			$url .= $L['donations_title_seperator'] . "<a href=\"plug.php?e=donations&amp;m=admin&amp;s=" . $s . "\">" . $L['donations_nav_' . $s] . "</a>";
		}
		if (!empty($id) && ($s == "edit" || $s == "delete"))
		{
			$url .= $L['donations_title_seperator'] . "<a href=\"plug.php?e=donations&mp;m=admin&amp;s=" . $s . "&amp;id=" . $i . "\">" . $L['donations_admin_txnid'] . " - " . $id . "</a>";
		}
		return $url;
	}
	elseif (!empty($m))
	{
		//$url = $L['donations_title_seperator']."<a href=\"plug.php?e=donations&amp;m=".$m."\">".$L['donations_nav_'.$m]."</a>";
		return $url;
	}
}
?>