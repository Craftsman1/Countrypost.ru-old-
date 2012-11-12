ALTER TABLE `clients` ADD `website` VARCHAR( 4096 ) NOT NULL AFTER `client_phone` ,
ADD `skype` VARCHAR( 255 ) NOT NULL AFTER `website`;

ALTER TABLE `clients` ADD `about_me` TEXT NOT NULL AFTER `notifications_on`; 
