<?PHP
/* ====================
[BEGIN_COT_EXT]
Code=donations
Name=PayPal Donations
Description=PayPal Donations System
Version=1.2
Date=2011-10-28
Author=Kilandor
Copyright=Kilandor
Notes=To use sandbox mode you need to goto https://developer.paypal.com/ and create the appropriate infomation. <br /><br /> These 3 tags can be used in any template, to provide links, Direct is the same donate button used on the plugin page, pageimg, links to the page with an image, pagetxt links to the page but as text. {PHP.out.donations_button_direct} {PHP.out.donations_button_pageimg} {PHP.out.donations_button_pagetxt}
SQL=
Auth_guests=R
Lock_guests=W12345A
Auth_members=R
Lock_members=W12345A
[END_COT_EXT]

[BEGIN_COT_EXT_CONFIG]
paypal_mode=010:select:Live,Sandbox:Sandbox:Setting to live will submit actual payments, Turn to Sandbox to test with paypal sandbox
paypal_currency=011:select:AUD,CAD,CHF,CZK,DKK,EUR,GBP,HKD,HUF,JPY,NOK,NZD,PLN,SEK,SGD,USD:USD:Select currency to accept your payments in (this just causes the currecny to be converted from the buyers currency to yours)
paypal_email=012:string:::Paypal Email, the email to send the payment to (note sandbox mod has unique info for this)
paypal_pdt_token=013:string:::Paypal Payment Data Token - generated by paypal used for authenticating the data sent back by paypal, and to confirm a completed payment
paypal_donation_title=014:string::Donation:Paypal Donation Title - The title of the donation, (ex MyWebsiteName Donation)
upgrade=015:radio:Yes,No:0:Upgrade users to a specific group on sucessful donation?
upgrade_type=016:select:Main,Sub,Both:Both:Upgrade Main group or Sub Group(If Main a user will only be upgraded if his main level is less than the upgrade group level, sub will always be added, both will add sub, if can't upgrade main)
upgrade_group=017:string:::Group to place upgraded users in
datemask=018:string::n-j:Date mask to use for top/recent listings(php.net/date)
recentlimit=019:string::5:Recent Donations Limit
toplimit=020:string::5:*Top Donations Limit
[END_COT_EXT_CONFIG]

==================== */

/**
 * PayPal Donations
 *
 * @package donations
 * @version 1.1
 * @author Kilandor - Jason Booth
 * @copyright All rights reserved. 2009-2011
 * @license BSD
 */

if (!defined('COT_CODE')) { die('Wrong URL.'); }

?>