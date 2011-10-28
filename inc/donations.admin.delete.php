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
$id = cot_import('id', 'G', 'ALP');
$s = cot_import('s', 'G', 'ALP');
$u = cot_import('u', 'G', 'ALP');
//------ Donations Import Variables ------\\

$sql_txnid = $db->query("SELECT * FROM $db_donations WHERE donation_txnid='" . $db->prep($id) . "' LIMIT 1");
if ($sql_txnid->rowCount() > 0)
{
	if ($u == 'delete')
	{
		$db->query("DELETE FROM $db_donations WHERE donation_txnid='" . $db->prep($id) . "' LIMIT 1");
		header('Location: ' . cot_url('plug', 'e=donations&m=admin', '', true));
		exit;
	}
	$t->assign(array(
		"DONATION_ADMIN_YES" => '<a href="' . cot_url('plug', 'e=donations&m=admin&s=delete&id=' . $id . '&u=delete') . '">' . $L['Yes'] . '</a>',
		"DONATION_ADMIN_NO" => '<a href="' . cot_url('plug', 'e=donations&m=admin') . '">' . $L['No'] . '</a>',
	));
}
else
{
	header('Location: ' . cot_url('plug', 'e=donations&m=admin', '', true));
	exit;
}
?>
