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
Hooks=global
[END_COT_EXT]

 */
if (!defined('COT_CODE')) { die('Wrong URL.'); }

require_once cot_incfile('donations', 'plug'); //Loads the Functions file, used to store functions to be used by the donations.

$out['donations_button_direct'] = donations_link();
$out['donations_button_pageimg'] = '<a href="'.cot_url('plug', 'e=donations').'"><img src="'.$L['donations_button_img'].'" alt="'.$L['donations_button_txt'].'" /></a>';
$out['donations_button_pagetxt'] = '<a href="'.cot_url('plug', 'e=donations').'">'.$L['donations_button_txt'].'</a>';

?>