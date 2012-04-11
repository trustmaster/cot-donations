ALTER TABLE `cot_donations_users` DROP PRIMARY KEY;
ALTER TABLE `cot_donations_users` ADD PRIMARY KEY  (`donation_userid`, `donation_email`);