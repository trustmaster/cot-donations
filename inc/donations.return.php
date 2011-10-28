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

 //------ Shop Import Variables ------\\
$id = cot_import('id', 'G', 'INT');
$s = cot_import('s', 'G', 'ALP');
//------ Shop Import Variables ------\\
// read the post from PayPal system and add 'cmd'
$req = 'cmd=_notify-synch';

$tx_token = $_GET['tx'];
$req .= "&tx=" . $tx_token . "&at=" . $cfg['plugin']['donations']['paypal_pdt_token'];
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
	$error .= $L['donations_return_http_connection_fail'] . "<br />";
	// HTTP ERROR
}
else
{
	fputs($fp, $header . $req);
	// read the body data
	$res = '';
	$headerdone = false;
	while (!feof($fp))
	{
		$line = fgets($fp, 1024);
		if (strcmp($line, "\r\n") == 0)
		{
			// read the header
			$headerdone = true;
		}
		elseif ($headerdone)
		{
			// header has been read. now read the contents
			$res .= $line;
		}
	}

	// parse the data
	$lines = explode("\n", $res);
	$keyarray = array();
	if (strcmp($lines[0], "SUCCESS") == 0)
	{
		for ($i = 1; $i < count($lines); $i++)
		{
			list($key, $val) = explode("=", $lines[$i]);
			$keyarray[urldecode($key)] = urldecode($val);
		}
		// check the payment_status is Completed
		// check that txn_id has not been previously processed
		// check that receiver_email is your Primary PayPal email
		// check that payment_amount/payment_currency are correct
		// process payment
		$firstname = $keyarray['first_name'];
		$lastname = $keyarray['last_name'];
		$prod_name = $keyarray['item_name'];
		$prod_id = $keyarray['item_number'];
		$amount = $keyarray['mc_gross'];
		$currency = $keyarray['mc_currency'];
		$payment_date = $keyarray['payment_date'];
		$custom = $keyarray['custom'];
		$paypal_email = $keyarray['payer_email'];
		$txn_id = $keyarray['txn_id'];
		$payment_status = $keyarray['payment_status'];
		$donation_status = ($payment_status == "Completed") ? 1 : 0;

		$sql_txnid = $db->query("SELECT donation_txnid FROM $db_donations WHERE donation_txnid='" . $db->prep($txn_id) . "' LIMIT 1");
		$txnid_nr = $sql_txnid->rowCount();

		$t->assign(array(
			"DONATIONS_RETURN_USERID" => $custom,
			"DONATIONS_RETURN_FIRSTNAME" => htmlspecialchars($firstname),
			"DONATIONS_RETURN_LASTNAME" => htmlspecialchars($lastname),
			"DONATIONS_RETURN_PRODUCT_NAME" => htmlspecialchars($prod_name),
			"DONATIONS_RETURN_PRODUCT_ID" => (int) $prod_id,
			"DONATIONS_RETURN_PAYMENT_DATE" => htmlspecialchars($payment_date),
			"DONATIONS_RETURN_PAYMENT_STATUS" => htmlspecialchars($payment_status),
			"DONATIONS_RETURN_PAYPAL_EMAIL" => htmlspecialchars($paypal_email),
			"DONATIONS_RETURN_AMOUNT" => htmlspecialchars($amount),
			"DONATIONS_RETURN_TRANSACTION_ID" => htmlspecialchars($txn_id),
			"DONATIONS_RETURN_CURRENCY" => htmlspecialchars($currency),
		));
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
				$t->assign(array(
					"DONATIONS_RETURN_MESSAGE" => sprintf($L['donations_return_donate_complete'], htmlspecialchars($prod_name), htmlspecialchars($firstname), htmlspecialchars($lastname))
				));
			}
			elseif ($sql_donate_user->rowCount() > 0 && $donation_status)
			{
				$db->query("UPDATE $db_donations_users SET donation_totalamount=donation_totalamount+" . (float) $amount . " WHERE donation_userid='" . (int) $custom . "' AND donation_email = '" . $db->prep($paypal_email) . "' LIMIT 1");
				$t->assign(array(
					"DONATIONS_RETURN_MESSAGE" => sprintf($L['donations_return_donate_complete'], htmlspecialchars($prod_name), htmlspecialchars($firstname), htmlspecialchars($lastname))
				));
			}
			elseif ($donation_status)
			{
				$cache && $cache->db->remove('all_donators');
				$db->query("INSERT INTO $db_donations_users (donation_userid, donation_username, donation_email, donation_totalamount) VALUES ('" . (int) $custom . "', '" . $db->prep($user_name) . "', '" . $db->prep($paypal_email) . "', '" . (float) $amount . "')");
				$t->assign(array(
					"DONATIONS_RETURN_MESSAGE" => sprintf($L['donations_return_donate_complete'], htmlspecialchars($prod_name), htmlspecialchars($firstname), htmlspecialchars($lastname))
				));
			}
			else
			{
				$t->assign(array(
					"DONATIONS_RETURN_MESSAGE" => sprintf($L['donations_return_donate_pending'], htmlspecialchars($prod_name), htmlspecialchars($firstname), htmlspecialchars($lastname))
				));
			}

			if ($cfg['plugin']['donations']['upgrade'] && $donation_status && $custom > 0)
			{
				$sql_usergroup = $db->query("SELECT user_maingrp FROM " . $db_users . " WHERE user_id = '" . (int) $custom . "' LIMIT 1");
				$user_maingrp = $sql_usergroup->fetchColumn();
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
						$db->query("INSERT INTO $db_groups_users (gru_userid, gru_groupid) VALUES (" . (int) $custom . ", " . $db->quote($cfg['plugin']['donations']['upgrade_group']) . ")");
					}
				}
			}
		}
		else
		{
			if ($donation_status)
			{
				$t->assign(array(
					"DONATIONS_RETURN_MESSAGE" => sprintf($L['donations_return_donate_complete'], htmlspecialchars($prod_name), htmlspecialchars($firstname), htmlspecialchars($lastname))
				));
			}
			else
			{
				$t->assign(array(
					"DONATIONS_RETURN_MESSAGE" => sprintf($L['donations_return_donate_pending'], htmlspecialchars($prod_name), htmlspecialchars($firstname), htmlspecialchars($lastname))
				));
			}
			//$error .= $L['donations_return_duplicate_txn']."<br />";
		}
	}//end success
	elseif (strcmp($lines[0], "FAIL") == 0)
	{
		$error .= $L['donations_return_verification_fail'] . "<br />";
	}
}

fclose($fp);

if (!empty($error))
{
	$t->assign(array(
		"DONATIONS_ERROR_BODY" => $error
	));
	$t->parse("MAIN.DONATIONS_BODY.DONATIONS_ERROR");
}

?>
