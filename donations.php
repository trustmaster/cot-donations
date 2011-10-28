<?PHP
/* ====================
 * PayPal Donations
 *
 * @package donations
 * @version 1.1
 * @author Kilandor - Jason Booth
 * @copyright All rights reserved. 2009-2011
 * @license BSD

[BEGIN_COT_EXT]
Hooks=standalone
[END_COT_EXT]
==================== */

if (!defined('COT_CODE') && !defined('COT_PLUG'))
{
	die("Wrong URL.");
}

//------ Donations Import Variables ------\\
$id = cot_import('id', 'G', 'INT');
$s = cot_import('s', 'G', 'ALP');
$a = cot_import('a', 'G', 'TXT');

require_once cot_incfile('donations', 'plug'); //Loads the Functions file, used to store functions to be used by the donations.

$out['subtitle'] = $L['donations_main'];

$title = '<a href="' . cot_url('plug', 'e=donations') . '">' . $L['donations_main'] . '</a>';

switch ($m)
{
	case 'return':
		$inc = 'return';
		break;

	case 'admin':
		if (!empty($s) && file_exists(cot_tplfile("donations.admin.$s", 'plug')))
		{
			$inc = "admin.$s";
		}
		else
		{
			$inc = 'admin';
		}
		break;

	default:
		$inc = 'main';
		break;
}

$t = new XTemplate(cot_tplfile("donations.$inc", 'plug'));

//------ Donations Header Parsing ------\\
donations_nav(); //Initializes the navigation, which is used on all pages, quick easy, effective way to keep it all in one place. Must be done after TPL is declared
$title .= donations_title(); //Use to build title navigation
$t->assign(array(
	"DONATIONS_TITLE" => $title,
	"DONATIONS_SUBTITLE" => $subtitle,
	"DONATIONS_GLOBAL_STATUS" => $global_status,
));
$t->parse("MAIN.DONATIONS_HEADER");

//------ Donations Body Parsing ------\\

include_once cot_incfile('donations', 'plug', $inc);

$t->parse("MAIN.DONATIONS_BODY");


//------ Donations Footer Parsing ------\\
$t->parse("MAIN.DONATIONS_FOOTER");

?>