ALTER TABLE  `bids` ADD  `extra_tax` FLOAT NOT NULL DEFAULT  '0' AFTER  `delivery_name`;

CREATE TABLE  `countrypost.service`.`bid_extras` (
`bid_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `manager_id` INT( 11 ) NOT NULL ,
 `client_id` INT( 11 ) NOT NULL ,
 `order_id` INT( 11 ) NOT NULL ,
 `manager_tax` FLOAT NOT NULL ,
 `foto_tax` FLOAT NOT NULL ,
 `delivery_cost` FLOAT NOT NULL ,
 `delivery_name` VARCHAR( 255 ) NOT NULL ,
 `extra_tax` FLOAT NOT NULL DEFAULT  '0',
 `total_cost` FLOAT NOT NULL ,
 `status` ENUM(  'active',  'deleted' ) NOT NULL DEFAULT  'active',
 `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE = INNODB DEFAULT CHARSET = utf8;

ALTER TABLE `bid_extras`
  DROP `manager_id`,
  DROP `client_id`,
  DROP `order_id`,
  DROP `manager_tax`,
  DROP `foto_tax`,
  DROP `delivery_cost`,
  DROP `total_cost`,
  DROP `created`;

ALTER TABLE  `bid_extras` CHANGE  `delivery_name`  `extra_name` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE
utf8_general_ci NOT NULL;

ALTER TABLE  `bid_extras` CHANGE  `bid_id`  `bid_id` INT( 11 ) NOT NULL;

ALTER TABLE  `bid_extras` ADD  `bid_extra_id` INT( 11 ) NOT NULL AUTO_INCREMENT PRIMARY KEY FIRST;