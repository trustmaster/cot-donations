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

$L['donations_button_img'] = $cfg['plugins_dir'] . '/donations/img/btn_donate.gif';//Paypal Button Image - This may be default image provided by paypal, or a image of your own.
$L['donations_button_txt'] = 'Donate!';
$L['donations_main'] = "Donations";
$L['donations_title_seperator'] = " / ";
$L['donations_title_admin'] = "Admin";
$L['donations_title_policy'] = "Policy";

//------ Currency ------\\
//This list was compiled from http://www.xe.com/symbols.php please excuse if anything is incorrect
$L['donations_currency_mask'] = '%1$s %2$s'; // %1$s outputs Currency Symbol, %2$s outputs the amount, you can change this to suit your curency's normal symbol placement
$L['donations_currency_AUD'] = "$";
$L['donations_currency_CAD'] = "$";
$L['donations_currency_CHF'] = "CHF";
$L['donations_currency_CZK'] = "Kč";
$L['donations_currency_DKK'] = "kr";
$L['donations_currency_EUR'] = "€";
$L['donations_currency_GBP'] = "£";
$L['donations_currency_HKD'] = "$";
$L['donations_currency_HUF'] = "Ft";
$L['donations_currency_JPY'] = "¥";
$L['donations_currency_NOK'] = "kr";
$L['donations_currency_NZD'] = "$";
$L['donations_currency_PLN'] = "zł";
$L['donations_currency_SEK'] = "kr";
$L['donations_currency_SGD'] = "$";
$L['donations_currency_USD'] = "$";
//------ Currency ------\\

//------ Navigation ------\\
$L['donations_nav_seperator'] = " | "; // To look correct it should contain a space as a spacer on both sides.
$L['donations_nav_admin'] = "Admin";
$L['donations_nav_add'] = $L['Add'];
$L['donations_nav_delete'] = $L['Delete'];
$L['donations_nav_edit'] = $L['Edit'];
$L['donations_nav_policy'] = "Policies";
//------ Navigation ------\\

//------ Return ------\\
$L['donations_return_http_connection_fail'] = "Unable to connect to PayPal Site, to verrify data. Please contact a site administrator regarding your donation.";
$L['donations_return_verification_fail'] = "There was a problem when attempting to verify the data being returned by PayPal.
This is likely due to a page left open then refreshed, or the return url being accessed again.
If your donation does not show up Please contact a site administrator regarding your donation.";
//This message may be show if by some chance IPN updates before the user returns to the site via PDT By default its commented out so its not shown. it is also commented out on donations.return.php on line 201
//Though this message in reality should not be needed, and may only cause concern for the user if shown.
//$L['donations_return_duplicate_txn'] = "This Tranasaction ID was already found in the database. Your donation has already been processed if your have any problems please contact a site administrator regarding your donation.";
//According to PayPal Rules, if you have PDT enabled or auto-return, you must provide the below message, regarding the emailing of the recipt and that they can login to paypal to view the details.
$L['donations_return_donate_complete'] = "<h3>Thank you for donating for %1\$s, <strong>%2\$s %3\$s</strong>.</h3><br />
Your transaction has been completed, and a receipt for your donation has been emailed to you.
You may log into your account at http://www.paypal.com/ to view details of this transaction."; //%1\$s Outputs Product Name, %2\$s Outputs First Name, %3\$s Outputs Last name.
$L['donations_return_donate_pending'] = "<h3>Thank you for donating for %1\$s, <strong>%2\$s %3\$s</strong>.</h3><br />
Your transaction has been completed with paypal, but may still be pending, a receipt for your donation has been emailed to you.
You may log into your account at http://www.paypal.com/ to view details of this transaction."; //%1\$s Outputs Product Name, %2\$s Outputs First Name, %3\$s Outputs Last name.
//------ Return ------\\

//------ Main ------\\
$L['donations_recent_donations'] = "Last ".$cfg['plugin']['donations']['recentlimit']." Donations";
$L['donations_top_donations'] = "Top ".$cfg['plugin']['donations']['recentlimit']." Donations";
$L['donations_top_total_donations'] = "Top  ".$cfg['plugin']['donations']['recentlimit']." Donations Totals";
$L['donations_all_donators'] = "List of all donators";
$L['donations_donation_message'] = "Cotonti is an Open Source Project, and is supported by community and team contributions.
We have decided to open donations, to allow for users who enjoy using Cotonti and wish to contribute to help out.
We have no minimum donation limit, and any donations are considered as equally important to us as the next. So we appreciate any and all donations.<br /><br />
Donations will go to support Cotonti, such as Web Hosting, or development of new things.
There are also some possible plans to use funds to support the creation of a new wonderful default skin, as well as some possible contests!.<br /><br />
Anyone who donates will be moved into a special \"Donator\" Group, for those that are higher ranked, you will have a sub group added.<br />
For regular members this will allow you access to a Special Donator forum, that is only accessible to donators and staff!
As well as increased PFS storage.<br />
In the future we will likely add more features for anyone who donates.<br /><br />
The Cotonti Team thanks you for your support!";
$L['donations_donation_message_guest'] = "Cotonti Team accepts donations from guest, as well as members, so just as a notice.
That registered members receive a account upgrade linked to their account, and donations tracked by their user.
But if you choose to continue your donation will be listed here as Anonymous, but donations are still tracked cumulatively via your PayPal email you paid with.";
$L['donations_donation_message_member'] = "If you wish to donate Anonymously you may simply logout and return to this page, however your account will be unable to receive an upgrade to donator status.";
$L['donations_donation_message_webmoney'] = 'Cotonti — это проект с открытым исходным кодом, спонсируемый сообществом и усилиями его команды. Мы решили сделать возможными пожертвования, чтобы пользователи, которым нравится использовать Cotonti, смогли нам помочь таким образом. У пожертвований нет нижнего предела, и любые пожертвования одинаково важны для нас. Так что мы ценим любой вклад.<br /><br />
Пожертвования пойдут на поддержку Cotonti, оплату веб-хостинга и разработки новых возможностей. Кроме того, они могут пойти на оплату работы дизайнеров или в призовой фонд новых конкурсов.<br /><br />
Каждый, кто сделает пожертвование, станет членом специальной группы &quot;Donator&quot; и получит дополнительные привилегии на сайте.<br /><br />
Команда разработчиков Cotonti благодарит вас за вашу поддержку!<br /><br />
<span style="color:#CC1212;">Вы можете сделать ваше пожертвование с помощью системы WebMoney, отправив деньги на следующий WM-кошелёк:<br />
<strong>WMZ: Z400297152811 </strong><br />
В комментарии к переводу укажите, пожалуйста, слово Cotonti и ваш логин на сайте. Добавьте слово Аноним, если хотите сделать пожертвование анонимным.</span>';
//------ Main ------\\

//------ Admin ------\\
$L['donations_admin_donations_info'] = "Total # of donations - ";
$L['donations_admin_pending'] = "Pending Donations";
$L['donations_admin_completed'] = "Completed Donations";
$L['donations_admin_pending_empty'] = "There are no pending donations.";
$L['donations_admin_completed_empty'] = "There are no completed donations.";
$L['donations_admin_amount'] = "Amount";
$L['donations_admin_txnid'] = "TXNID";
$L['donations_admin_status_pending'] = "Pending";
$L['donations_admin_status_completed'] = "Completed";
$L['donations_admin_error_user'] = "You must supply a userid.";
$L['donations_admin_error_amount'] = "You must supply a donation amount.";
$L['donations_admin_delete_confirm'] = "Are you sure you want to delete this pending donation?";
$L['donations_admin_error_txnid_exists'] = "This Transaction ID already exists.";
//------ Admin ------\\
?>
