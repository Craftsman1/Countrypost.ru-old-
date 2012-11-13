ALTER TABLE `clients` ADD `skype` VARCHAR( 255 ) NOT NULL AFTER `client_phone`;
ALTER TABLE `clients` ADD `not_show_email` TINYINT( 1 ) NOT NULL AFTER `notifications_on`

ALTER TABLE `clients`
  DROP `website`,
  DROP `about_me`;