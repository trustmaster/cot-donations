CREATE TABLE IF NOT EXISTS `cot_donations` (
  `donation_id` int(11) unsigned NOT NULL auto_increment,
  `donation_status` tinyint(1) NOT NULL,
  `donation_userid` int(11) unsigned NOT NULL default '0',
  `donation_username` varchar(255) NOT NULL,
  `donation_firstname` varchar(255) NOT NULL,
  `donation_lastname` varchar(255) NOT NULL,
  `donation_email` varchar(255) NOT NULL,
  `donation_date` int(11) NOT NULL default '0',
  `donation_amount` FLOAT NOT NULL,
  `donation_txnid` varchar(30) NOT NULL,
  PRIMARY KEY  (`donation_id`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

CREATE TABLE IF NOT EXISTS `cot_donations_users` (
  `donation_userid` int(11) unsigned NOT NULL default '0',
  `donation_username` varchar(255) NOT NULL,
  `donation_email` varchar(255) NOT NULL,
  `donation_totalamount` FLOAT NOT NULL,
  PRIMARY KEY  (`donation_userid`, `donation_email`)
) ENGINE=MyISAM  DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;