<?PHP

/**
 * PayPal Donations
 *
 * @package Cotonti
 * @version 0.0.3
 * @author Kilandor - Jason Booth
 * @copyright All rights reserved. 2009
 * @license BSD
 */

if (!defined('COT_CODE') && !defined('COT_PLUG'))
{
	die('Wrong URL.');
}
$sql_recent = $db->query("SELECT donation_userid, donation_username, donation_amount, donation_date FROM $db_donations WHERE donation_status=1 ORDER by donation_date DESC LIMIT " . $cfg['plugin']['donations']['recentlimit']);
while ($fa_recent = $sql_recent->fetch())
{
	if ($fa_recent['donation_userid'] > 0)
	{
		$user = cot_build_user($fa_recent['donation_userid'], $fa_recent['donation_username']);
	}
	else
	{
		$user = $L['Anonymous'];
	}
	$t->assign(array(
		"DONATIONS_RECENT_USER" => $user,
		"DONATIONS_RECENT_AMOUNT" => sprintf($L['donations_currency_mask'], $L['donations_currency_' . $cfg['plugin']['donations']['paypal_currency']], $fa_recent['donation_amount']),
		"DONATIONS_RECENT_DATE" => @date($cfg['plugin']['donations']['datemask'], $fa_recent['donation_date']),
	));
	$t->parse('MAIN.DONATIONS_BODY.RECENT.LOOP');
}
$sql_top = $db->query("SELECT donation_userid, donation_username, donation_amount, donation_date FROM $db_donations WHERE donation_status=1 ORDER by donation_amount DESC LIMIT " . $cfg['plugin']['donations']['toplimit']);
while ($fa_top = $sql_top->fetch())
{
	if ($fa_top['donation_userid'] > 0)
	{
		$user = cot_build_user($fa_top['donation_userid'], $fa_top['donation_username']);
	}
	else
	{
		$user = $L['Anonymous'];
	}
	$t->assign(array(
		"DONATIONS_TOP_USER" => $user,
		"DONATIONS_TOP_AMOUNT" => sprintf($L['donations_currency_mask'], $L['donations_currency_' . $cfg['plugin']['donations']['paypal_currency']], $fa_top['donation_amount']),
		"DONATIONS_TOP_DATE" => @date($cfg['plugin']['donations']['datemask'], $fa_top['donation_date']),
	));
	$t->parse('MAIN.DONATIONS_BODY.TOP_DONATION.LOOP');
}
$sql_top = $db->query("SELECT donation_userid, donation_username, donation_totalamount FROM $db_donations_users ORDER by donation_totalamount DESC LIMIT " . $cfg['plugin']['donations']['toplimit']);
while ($fa_top = $sql_top->fetch())
{
	if ($fa_top['donation_userid'] > 0)
	{
		$user = cot_build_user($fa_top['donation_userid'], $fa_top['donation_username']);
	}
	else
	{
		$user = $L['Anonymous'];
	}
	$t->assign(array(
		"DONATIONS_TOP_USER" => $user,
		"DONATIONS_TOP_AMOUNT" => sprintf($L['donations_currency_mask'], $L['donations_currency_' . $cfg['plugin']['donations']['paypal_currency']], $fa_top['donation_totalamount']),
	));
	$t->parse('MAIN.DONATIONS_BODY.TOP_TOTAL.LOOP');
}
if (!$all_donators)
{
	$sql_all = $db->query("SELECT donation_userid, donation_username FROM $db_donations_users WHERE donation_userid != 0");
	while ($fa_all = $sql_all->fetch())
	{
		$user = cot_build_user($fa_all['donation_userid'], $fa_all['donation_username']);
		$all_donators .= $user . ", ";
	}
	$all_donators = substr($all_donators, 0, -2);
	$cache && $cache->db->store('all_donators', $all_donators, 'cot', $sys['now'] + 31556926);
}
$t->parse('MAIN.DONATIONS_BODY.RECENT');
$t->parse('MAIN.DONATIONS_BODY.TOP_DONATION');
$t->parse('MAIN.DONATIONS_BODY.TOP_TOTAL');
if ($usr['id'] == 0)
{
	$t->parse('MAIN.DONATIONS_BODY.GUEST');
}
else
{
	$t->parse('MAIN.DONATIONS_BODY.MEMBER');
}
$t->assign(array(
	"DONATIONS_BUTTON" => donations_link(),
	"DONATIONS_ALL_DONATORS" => $all_donators
));
?>
