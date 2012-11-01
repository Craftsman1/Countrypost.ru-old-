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