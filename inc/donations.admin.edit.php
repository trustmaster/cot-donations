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

 //------ Donation Import Variables ------\\
$id = cot_import('id', 'G', 'ALP');
$s = cot_import('s', 'G', 'ALP');
$u = cot_import('u', 'G', 'ALP');
//------ Donation Import Variables ------\\
if ($u == 'update')
{
	$sql_txnid = $db->query("SELECT * FROM $db_donations WHERE donation_txnid='" . $db->prep($id) . "' LIMIT 1");
	if ($sql_txnid->rowCount() > 0)
	{
		$firstname = cot_import('firstname', 'P', 'ALP');
		$lastname = cot_import('lastname', 'P', 'ALP');
		$user = cot_import('user', 'P', 'INT');
		$email = cot_import('email', 'P', 'TXT');
		$date = cot_import('date', 'P', 'TXT');
		$txnid = cot_import('txnid', 'P', 'ALP');
		$amount = cot_import('amount', 'P', 'TXT');
		$status = cot_import('status', 'P', 'BOL');
		$error .= ( empty($user) && $user != 0) ? $L['donations_admin_error_user'] . '<br />' : '';
		$error .= ( empty($amount)) ? $L['donations_admin_error_amount'] . '<br />' : '';
		if (!is_numeric($date))
		{
			$date = cot_date2stamp($date);
		}
		if (!$error)
		{
			if ($user > 0)
			{
				$sql_username = $db->query("SELECT user_name FROM $db_users WHERE user_id=" . (int) $user . " LIMIT 1");
				$user_name = $sql_username->fetchColumn();
				$sql_usergroup = $db->query("SELECT user_maingrp FROM " . $db_users . " WHERE user_id = '" . (int) $user . "' LIMIT 1");
				$user_maingrp = $sql_usergroup->fetchColumn();
			}
			$db->query("UPDATE $db_donations SET
				donation_firstname = '" . $db->prep($firstname) . "',
				donation_lastname = '" . $db->prep($lastname) . "',
				donation_userid = '" . $db->prep($user) . "',
				donation_username = '" . $db->prep($user_name) . "',
				donation_email = '" . $db->prep($email) . "',
				donation_date = '" . $db->prep($date) . "',
				donation_txnid = '" . $db->prep($txnid) . "',
				donation_amount = '" . $db->prep((float) $amount) . "',
				donation_status = '" . $db->prep($status) . "'
				WHERE donation_txnid = '" . $db->prep($id) . "' LIMIT 1");
			if ($status)
			{
				$sql_donate_user = $db->query("SELECT * FROM $db_donations_users WHERE donation_userid = '" . (int) $user . "' LIMIT 1");
				if ($sql_donate_user->rowCount() > 0 && $user > 0 && $status)
				{
					$db->query("UPDATE $db_donations_users SET donation_totalamount=donation_totalamount+" . (float) $amount . " WHERE donation_userid='" . (int) $user . "' AND donation_email='" . $db->prep($email) . "' LIMIT 1");
				}
				elseif ($status)
				{
					$cache && $cache->db->remove('all_donators');
					$db->query("INSERT INTO $db_donations_users (donation_userid, donation_username, donation_email, donation_totalamount) VALUES ('" . (int) $user . "', '" . $db->prep($user_name) . "', '" . $db->prep($email) . "', '" . (float) $amount . "')");
				}

				if ($cfg['plugin']['donations']['upgrade_type'] == 'Main' && ($user_maingrp != COT_GROUP_BANNED && $user_maingrp != COT_GROUP_INACTIVE))
				{
					if ($cot_groups[$user_maingrp]['level'] <= $cot_groups[$cfg['plugin']['donations']['upgrade_group']]['level'])
					{
						$db->query("UPDATE $db_users SET user_maingrp='" . $cfg['plugin']['donations']['upgrade_group'] . "' WHERE user_id='" . (int) $user . "'");
						$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $user . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
						if ($sql_groups->rowCount() == 0)
						{
							$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $user . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
						}
						$db->query("DELETE FROM $db_groups_users WHERE gru_userid='" . (int) $user . "' AND gru_groupid='$user_maingrp'");
					}
				}
				elseif ($cfg['plugin']['donations']['upgrade_type'] == 'Sub' && ($user_maingrp != COT_GROUP_BANNED && $user_maingrp != COT_GROUP_INACTIVE))
				{
					$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $user . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
					if ($sql_groups->rowCount() == 0)
					{
						$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $user . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
					}
				}
				elseif ($user_maingrp != COT_GROUP_BANNED && $user_maingrp != COT_GROUP_INACTIVE)
				{
					if ($cot_groups[$user_maingrp]['level'] <= $cot_groups[$cfg['plugin']['donations']['upgrade_group']]['level'])
					{
						$db->query("UPDATE $db_users SET user_maingrp='" . $cfg['plugin']['donations']['upgrade_group'] . "' WHERE user_id='" . (int) $user . "'");
						$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $user . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
						if ($sql_groups->rowCount() == 0)
						{
							$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $user . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
						}
						$db->query("DELETE FROM $db_groups_users WHERE gru_userid='" . (int) $user . "' AND gru_groupid='$user_maingrp'");
					}
					$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $user . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
					if ($sql_groups->rowCount() == 0)
					{
						$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $user . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
					}
				}
			}
			header('Location: ' . cot_url('plug', 'e=donations&m=admin', '', true));
			exit;
		}
	}
	else
	{
		header('Location: ' . cot_url('plug', 'e=donations&m=admin', '', true));
		exit;
	}
}
$sql_donations = $db->query("SELECT * FROM $db_donations WHERE donation_txnid='" . $db->prep($id) . "' LIMIT 1");
$fa_donations = $sql_donations->fetch();
$t->assign(array(
	"DONATION_ADMIN_SEND" => cot_url('plug', 'e=donations&m=admin&s=edit&id=' . $fa_donations['donation_txnid'] . '&u=update'),
	"DONATION_ADMIN_FIRSTNAME" => '<input type="text" name="firstname" value="' . $fa_donations['donation_firstname'] . '" />',
	"DONATION_ADMIN_LASTNAME" => '<input type="text" name="lastname" value="' . $fa_donations['donation_lastname'] . '" />',
	"DONATION_ADMIN_USER" => '<input type="text" name="user" value="' . $fa_donations['donation_userid'] . '" />',
	"DONATION_ADMIN_EMAIL" => '<input type="text" name="email" value="' . $fa_donations['donation_email'] . '" />',
	"DONATION_ADMIN_DATE" => '<input type="text" name="date" value="' . $fa_donations['donation_date'] . '" />',
	"DONATION_ADMIN_TXNID" => '<input type="text" name="txnid" value="' . $fa_donations['donation_txnid'] . '" />',
	"DONATION_ADMIN_AMOUNT" => '<input type="text" name="amount" value="' . $fa_donations['donation_amount'] . '" />',
	"DONATION_ADMIN_STATUS" => '<select name="status"><option value="0">' . $L['donations_admin_status_pending'] . '</option><option value="1">' . $L['donations_admin_status_completed'] . '</option></select>'
));
if (!empty($error))
{
	$t->assign(array(
		"DONATIONS_ERROR_BODY" => $error
	));
	$t->parse("MAIN.DONATIONS_BODY.DONATIONS_ERROR");
}
?>