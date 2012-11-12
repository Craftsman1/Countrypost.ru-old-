CREATE TABLE  `countrypost.service`.`manager_pricelists` (

`delivery_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `delivery_name` VARCHAR( 32 ) NOT NULL ,
 `country_id` INT( 11 ) NOT NULL ,
PRIMARY KEY (  `delivery_id` )
) ENGINE = INNODB DEFAULT CHARSET = utf8;

ALTER TABLE  `manager_pricelists` CHANGE  `delivery_id`  `pricelist_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
CHANGE  `delivery_name`  `manager_id` INT( 11 ) NOT NULL ,
CHANGE  `country_id`  `description` VARCHAR( 65535 ) CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL;

ALTER TABLE  `manager_pricelists` ADD  `country_id` INT( 11 ) NOT NULL AFTER  `manager_id`;
ALTER TABLE  `manager_pricelists` CHANGE  `description`  `description` TEXT CHARACTER SET utf32 COLLATE utf32_general_ci NOT NULL;

DROP TABLE  `admins`;

ALTER TABLE  `managers` CHANGE  `status`  `is_cashback` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `managers` ADD  `about_me` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `managers` ADD  `is_mail_forwarding` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `is_cashback`;
ALTER TABLE  `managers` ADD  `is_internal_payments` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `is_mail_forwarding`;
ALTER TABLE  `managers` ADD  `city` VARCHAR( 255 ) NOT NULL AFTER  `manager_address_description`;
ALTER TABLE  `managers` CHANGE  `website`  `website` VARCHAR( 4096 ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL;
ALTER TABLE  `managers` CHANGE  `manager_name`  `manager_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `managers` ADD  `cashback_limit` INT( 11 ) NOT NULL AFTER  `is_cashback`;
ALTER TABLE  `blogs` ADD  `title` VARCHAR( 255 ) NOT NULL AFTER  `user_id`;
ALTER TABLE  `blogs` CHANGE  `created`  `created` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE  `managers` ADD  `manager_address` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL AFTER
`manager_addres`;
UPDATE  `managers` SET manager_address = manager_addres;