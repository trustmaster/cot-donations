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
Hooks=input
[END_COT_EXT]

 */

if (!defined('COT_CODE')) { die('Wrong URL.'); }
if (defined('COT_PLUG'))
{
	$r = cot_import('r', 'G', 'ALP');
	if($r == 'donations')
	{
		define('COT_NO_ANTIXSS', TRUE);
	}
}

?>