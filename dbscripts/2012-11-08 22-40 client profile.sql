ALTER TABLE `clients` ADD `website` VARCHAR( 4096 ) NOT NULL AFTER `client_phone` ,
ADD `skype` VARCHAR( 255 ) NOT NULL AFTER `website`