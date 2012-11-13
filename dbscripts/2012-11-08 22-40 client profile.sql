ALTER TABLE `clients` ADD `skype` VARCHAR( 255 ) NOT NULL AFTER `client_phone`;

ALTER TABLE `clients`
  DROP `website`,
  DROP `about_me`;

ALTER TABLE `clients` DROP `not_show_email`;