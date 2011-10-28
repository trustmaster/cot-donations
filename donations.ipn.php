<?PHP

/**
 * PayPal Donations
 *
 * @package donations
 * @version 1.1
 * @author Kilandor - Jason Booth
 * @copyright All rights reserved. 2009-2011
 * @license BSD

[BEGIN_COT_EXT]
Hooks=ajax
[END_COT_EXT]

 */
if (!defined('COT_CODE') && !defined('COT_PLUG')) { die('Wrong URL.'); }

require_once cot_incfile('donations', 'plug');

// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-validate';

foreach ($_POST as $key => $value)
{
	$value = urlencode(stripslashes($value));
	$req .= "&$key=$value";
}

$paypal_mode = ($cfg['plugin']['donations']['paypal_mode'] == "Live") ? "www.paypal.com" : "www.sandbox.paypal.com";
// post back to PayPal system to validate
$header .= "POST /cgi-bin/webscr HTTP/1.0\r\n";
$header .= "Content-Type: application/x-www-form-urlencoded\r\n";
$header .= "Content-Length: " . strlen($req) . "\r\n\r\n";
$fp = fsockopen($paypal_mode, 80, $errno, $errstr, 30);
// If possible, securely post back to paypal using HTTPS
// Your PHP server will need to be SSL enabled
// $fp = fsockopen ('ssl://www.sandbox.paypal.com', 443, $errno, $errstr, 30);

if (!$fp)
{
	// HTTP ERROR
}
else
{
	fputs($fp, $header . $req);
	while (!feof($fp))
	{
		$res = fgets($fp, 1024);
		if (strcmp($res, "VERIFIED") == 0)
		{
			$verified = true;
			break;
		}
		elseif (strcmp($res, "INVALID") == 0)
		{
			$verified = false;
			break;
		}
	}
	if ($verified)
	{
		// check the payment_status is Completed
		// check that txn_id has not been previously processed
		// check that receiver_email is your Primary PayPal email
		// check that payment_amount/payment_currency are correct
		// process payment
		$firstname = $_POST['first_name'];
		$lastname = $_POST['last_name'];
		$prod_name = $_POST['item_name'];
		$prod_id = $_POST['item_number'];
		$amount = $_POST['mc_gross'];
		$currency = $_POST['mc_currency'];
		$payment_date = $_POST['payment_date'];
		$custom = $_POST['custom'];
		$paypal_email = $_POST['payer_email'];
		$txn_id = $_POST['txn_id'];
		$payment_status = $_POST['payment_status'];
		$donation_status = ($payment_status == "Completed") ? 1 : 0;

		$sql_txnid = $db->query("SELECT donation_txnid FROM $db_donations WHERE donation_txnid='" . $db->prep($txn_id) . "' LIMIT 1");
		$txnid_nr = $sql_txnid->rowCount();

		$sql_usergroup = $db->query("SELECT user_maingrp FROM $db_users WHERE user_id = '" . (int) $custom . "' LIMIT 1");
		$user_maingrp = $sql_usergroup->fetchColumn();
		$custom = ($sql_usergroup->rowCount() > 0) ? $custom : 0;
		if ($txnid_nr == 0)
		{
			if ($custom > 0)
			{
				$sql_username = $db->query("SELECT user_name FROM $db_users WHERE user_id=" . (int) $custom . " LIMIT 1");
				$user_name = $sql_username->fetchColumn();
			}
			$db->query("INSERT INTO $db_donations
				(donation_status, donation_userid, donation_username, donation_firstname, donation_lastname,
				donation_email, donation_date, donation_amount, donation_txnid)
				VALUES
				('" . $donation_status . "', '" . (int) $custom . "', '" . $db->prep($user_name) . "', '" . $db->prep($firstname) . "', '" . $db->prep($lastname) . "',
				'" . $db->prep($paypal_email) . "', '" . $sys['now'] . "', '" . (float) $amount . "', '" . $db->prep($txn_id) . "')");
			if ($custom > 0)
			{
				$sql_donate_user = $db->query("SELECT * FROM $db_donations_users WHERE donation_userid = '" . (int) $custom . "' LIMIT 1");
			}
			else
			{
				$sql_donate_user = $db->query("SELECT * FROM $db_donations_users WHERE donation_userid = '" . (int) $custom . "' AND donation_email = '" . $db->prep($paypal_email) . "' LIMIT 1");
			}
			if ($sql_donate_user->rowCount() > 0 && $custom > 0 && $donation_status)
			{
				$db->query("UPDATE $db_donations_users SET donation_totalamount=donation_totalamount+" . (float) $amount . " WHERE donation_userid='" . (int) $custom . "' LIMIT 1");
			}
			elseif ($sql_donate_user->rowCount() > 0 && $donation_status)
			{
				$db->query("UPDATE $db_donations_users SET donation_totalamount=donation_totalamount+" . (float) $amount . " WHERE donation_userid='" . (int) $custom . "' AND donation_email = '" . $db->prep($paypal_email) . "' LIMIT 1");
			}
			elseif ($donation_status)
			{
				$cache && $cache->db->remove('all_donators');
				$db->query("INSERT INTO $db_donations_users (donation_userid, donation_username, donation_email, donation_totalamount) VALUES ('" . (int) $custom . "', '" . $db->prep($user_name) . "', '" . $db->prep($paypal_email) . "', '" . (float) $amount . "')");
			}
			if ($cfg['plugin']['donations']['upgrade'] && $donation_status && $custom > 0 && ($user_maingrp != COT_GROUP_BANNED && $user_maingrp != COT_GROUP_INACTIVE))
			{
				if ($cfg['plugin']['donations']['upgrade_type'] == 'Main')
				{
					if ($cot_groups[$user_maingrp]['level'] <= $cot_groups[$cfg['plugin']['donations']['upgrade_group']]['level'])
					{
						$db->query("UPDATE $db_users SET user_maingrp='" . $cfg['plugin']['donations']['upgrade_group'] . "' WHERE user_id='" . (int) $custom . "'");
						$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
						if ($sql_groups->rowCount() == 0)
						{
							$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $custom . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
						}
						$db->query("DELETE FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='$user_maingrp'");
					}
				}
				elseif ($cfg['plugin']['donations']['upgrade_type'] == 'Sub')
				{
					$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
					if ($sql_groups->rowCount() == 0)
					{
						$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $custom . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
					}
				}
				else
				{
					if ($cot_groups[$user_maingrp]['level'] <= $cot_groups[$cfg['plugin']['donations']['upgrade_group']]['level'])
					{
						$db->query("UPDATE $db_users SET user_maingrp='" . $cfg['plugin']['donations']['upgrade_group'] . "' WHERE user_id='" . (int) $custom . "'");
						$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
						if ($sql_groups->rowCount() == 0)
						{
							$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $custom . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
						}
						$db->query("DELETE FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='$user_maingrp'");
					}
					$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
					if ($sql_groups->rowCount() == 0)
					{
						$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $custom . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
					}
				}
			}
		}
		else
		{
			$sql_donation = $db->query("SELECT * FROM $db_donations WHERE donation_txnid='" . $db->prep($txn_id) . "' LIMIT 1");
			$fa_donaiton = $sql_donation->fetch();
			if ($fa_donation['donation_status'] == 0)
			{
				if ($donation_status)
				{
					$db->query("UPDATE $db_donations SET donation_status=" . $donation_status . " WHERE donation_txnid='" . $db->prep($txn_id) . "' LIMIT 1");
					if ($custom > 0)
					{
						$sql_donate_user = $db->query("SELECT * FROM $db_donations_users WHERE donation_userid = '" . (int) $custom . "' LIMIT 1");
					}
					else
					{
						$sql_donate_user = $db->query("SELECT * FROM $db_donations_users WHERE donation_userid = '" . (int) $custom . "' AND donation_email = '" . $db->prep($paypal_email) . "' LIMIT 1");
					}
					if ($sql_donate_user->rowCount() > 0 && $custom > 0 && $donation_status)
					{
						$db->query("UPDATE $db_donations_users SET donation_totalamount=donation_totalamount+" . (float) $amount . " WHERE donation_userid='" . (int) $custom . "' LIMIT 1");
					}
					elseif ($sql_donate_user->rowCount() > 0 && $donation_status)
					{
						$db->query("UPDATE $db_donations_users SET donation_totalamount=donation_totalamount+" . (float) $amount . " WHERE donation_userid='" . (int) $custom . "' AND donation_email = '" . $db->prep($paypal_email) . "' LIMIT 1");
					}
					elseif ($donation_status)
					{
						$cache && $cache->db->remove('all_donators');
						$db->query("INSERT INTO $db_donations_users (donation_userid, donation_username, donation_email, donation_totalamount) VALUES ('" . (int) $custom . "', '" . $db->prep($user_name) . "', '" . $db->prep($paypal_email) . "', '" . (float) $amount . "')");
					}
					if ($cfg['plugin']['donations']['upgrade'] && $donation_status && $custom > 0 && ($user_maingrp != COT_GROUP_BANNED && $user_maingrp != COT_GROUP_INACTIVE))
					{
						if ($cfg['plugin']['donations']['upgrade_type'] == 'Main')
						{
							if ($cot_groups[$user_maingrp]['level'] <= $cot_groups[$cfg['plugin']['donations']['upgrade_group']]['level'])
							{
								$db->query("UPDATE $db_users SET user_maingrp='" . $cfg['plugin']['donations']['upgrade_group'] . "' WHERE user_id='" . (int) $custom . "'");
								$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
								if ($sql_groups->rowCount() == 0)
								{
									$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $custom . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
								}
								$db->query("DELETE FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='$user_maingrp'");
							}
						}
						elseif ($cfg['plugin']['donations']['upgrade_type'] == 'Sub')
						{
							$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
							if ($sql_groups->rowCount() == 0)
							{
								$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $custom . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
							}
						}
						else
						{
							if ($cot_groups[$user_maingrp]['level'] <= $cot_groups[$cfg['plugin']['donations']['upgrade_group']]['level'])
							{
								$db->query("UPDATE $db_users SET user_maingrp='" . $cfg['plugin']['donations']['upgrade_group'] . "' WHERE user_id='" . (int) $custom . "'");
								$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
								if ($sql_groups->rowCount() == 0)
								{
									$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $custom . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
								}
								$db->query("DELETE FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='$user_maingrp'");
							}
							$sql_groups = $db->query("SELECT gru_userid FROM $db_groups_users WHERE gru_userid='" . (int) $custom . "' AND gru_groupid='" . $cfg['plugin']['donations']['upgrade_group'] . "'");
							if ($sql_groups->rowCount() == 0)
							{
								$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $custom . ", " . $cfg['plugin']['donations']['upgrade_group'] . ")");
							}
						}
					}
				}
			}
		}
	}//end success
	elseif (!$verified)
	{
		
	}
}
fclose($fp);
?>