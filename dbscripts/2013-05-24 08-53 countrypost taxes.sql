ALTER TABLE `taxes`
  DROP `country_id`,
  DROP `package`,
  DROP `package_disconnected`,
  DROP `package_joint`,
  DROP `package_declaration`,
  DROP `package_insurance`,
  DROP `min_order`,
  DROP `max_package_insurance`,
  DROP `package_foto`,
  DROP `package_foto_system`;

  ALTER TABLE  `taxes` CHANGE  `order`  `order_id` INT( 11 ) NOT NULL;

  ALTER TABLE  `taxes` ADD  `amount` FLOAT NOT NULL DEFAULT  '0',
ADD  `amount_usd` FLOAT NOT NULL DEFAULT  '0';

ALTER TABLE  `taxes` ADD  `manager_id` INT( 11 ) NOT NULL AFTER  `tax_id`;

ALTER TABLE  `taxes` ADD  `status` ENUM(  'not_payed',  'payed') NOT NULL DEFAULT  'not_payed' AFTER  `order_id`;

ALTER TABLE  `taxes` ADD  `usd_rate` FLOAT NOT NULL ,
ADD  `usd_conversion_rate` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE  `taxes` CHANGE  `usd_rate`  `usd_rate` FLOAT NOT NULL ,
CHANGE  `usd_conversion_rate`  `usd_conversion_date` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;

ALTER TABLE  `taxes` CHANGE  `usd_rate`  `usd_conversion_rate` FLOAT NOT NULL;

ALTER TABLE  `taxes` CHANGE  `status`  `status` ENUM(  'not_payed',  'payed',  'deleted' ) CHARACTER SET utf8 COLLATE
 utf8_general_ci NOT NULL DEFAULT  'not_payed';

 ALTER TABLE  `taxes` ADD  `currency` VARCHAR( 3 ) NOT NULL;