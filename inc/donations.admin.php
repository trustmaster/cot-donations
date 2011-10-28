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

if (!defined('COT_CODE') && !defined('COT_PLUG')) { die('Wrong URL.'); }

//------ Donations Import Variables ------\\
list($pn, $d, $d_url) = cot_import_pagenav('d', $cfg['maxrowsperpage']);
//------ Donations Import Variables ------\\
$d = (empty($d)) ? 0 : $d;
$completed = 0;
$pending = 0;
$sql_donations_pending = $db->query("SELECT * FROM $db_donations WHERE donation_status=0 ORDER BY donation_date DESC");
foreach ($sql_donations_pending->fetchAll() as $fa_donations)
{
	if($fa_donations['donation_userid'] > 0)
	{
		$sql_username = $db->query("SELECT user_name FROM $db_users WHERE user_id=".$fa_donations['donation_userid']." LIMIT 1");
		$user_name = @$sql_username->fetchColumn();
		$user = cot_build_user($fa_donations['donation_userid'], $user_name);
	}
	else
	{
		$user = $L['Anonymous'];
	}
	$t->assign(array(
		"DONATION_ADMIN_NAME" => $fa_donations['donation_firstname'].' '.$fa_donations['donation_lastname'],
		"DONATION_ADMIN_USER" => $user,
		"DONATION_ADMIN_EMAIL" => $fa_donations['donation_email'],
		"DONATION_ADMIN_DATE" => cot_date('datetime_medium', $fa_donations['donation_date'] + $usr['timezone'] * 3600),
		"DONATION_ADMIN_TXNID" => $fa_donations['donation_txnid'],
		"DONATION_ADMIN_AMOUNT" => sprintf($L['donations_currency_mask'], $L['donations_currency_'.$cfg['plugin']['donations']['paypal_currency']], $fa_donations['donation_amount']),
		"DONATION_ADMIN_EDIT" => '<a href="'.cot_url('plug', 'e=donations&m=admin&s=edit&id='.$fa_donations['donation_txnid']).'">'.$L['Edit'].'</a>',
		"DONATION_ADMIN_DELETE" => '<a href="'.cot_url('plug', 'e=donations&m=admin&s=delete&id='.$fa_donations['donation_txnid']).'">'.$L['Delete'].'</a>',
		));
	$t->parse('MAIN.DONATIONS_BODY.DONATIONS_PENDING.LOOP');
	$pending++;
}
$sql_donations = $db->query("SELECT COUNT(*) FROM $db_donations WHERE donation_status=1");
$totallines = $sql_donations->fetchColumn();
$sql_donations = $db->query("SELECT * FROM $db_donations WHERE donation_status=1 ORDER BY donation_date DESC LIMIT $d, ".$cfg['maxrowsperpage']);

foreach($sql_donations->fetchAll() as $fa_donations)
{
	if($fa_donations['donation_userid'] > 0)
	{
		$sql_username = $db->query("SELECT user_name FROM $db_users WHERE user_id=".$fa_donations['donation_userid']." LIMIT 1");
		$user_name = $sql_username->fetchColumn();
		$user = cot_build_user($fa_donations['donation_userid'], $user_name);
	}
	else
	{
		$user = $L['Anonymous'];
	}
	$t->assign(array(
		"DONATION_ADMIN_NAME" => $fa_donations['donation_firstname'].' '.$fa_donations['donation_lastname'],
		"DONATION_ADMIN_USER" => $user,
		"DONATION_ADMIN_EMAIL" => $fa_donations['donation_email'],
		"DONATION_ADMIN_DATE" => cot_date('datetime_medium', $fa_donations['donation_date'] + $usr['timezone'] * 3600),
		"DONATION_ADMIN_TXNID" => $fa_donations['donation_txnid'],
		"DONATION_ADMIN_AMOUNT" => sprintf($L['donations_currency_mask'], $L['donations_currency_'.$cfg['plugin']['donations']['paypal_currency']], $fa_donations['donation_amount']),
		));
	$t->parse('MAIN.DONATIONS_BODY.DONATIONS_COMPLETED.LOOP');
	$completed++;
}
$totalpages = ceil($totallines / $cfg['maxrowsperpage']);
$currentpage = $pn;
$pagination = cot_pagenav('plug', 'e=donations&m=admin', $d, $totallines, $cfg['maxrowsperpage']);

if($completed == 0)
{
	$t->parse('MAIN.DONATIONS_BODY.DONATIONS_COMPLETED.EMPTY');
}
if($pending == 0)
{
	$t->parse('MAIN.DONATIONS_BODY.DONATIONS_PENDING.EMPTY');
}
$t->assign(array(
	"DONATIONS_COMPLETED_PAGINATION" => $pagination['prev'].$pagination['main'].$pagination['next'],
	));
$t->parse('MAIN.DONATIONS_BODY.DONATIONS_COMPLETED');
$t->parse('MAIN.DONATIONS_BODY.DONATIONS_PENDING');

?>
