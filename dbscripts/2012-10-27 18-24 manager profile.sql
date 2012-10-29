ALTER TABLE  `managers` ADD  `skype` VARCHAR( 255 ) NOT NULL AFTER  `website`;
ALTER TABLE  `managers` CHANGE  `last_client_added`  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;