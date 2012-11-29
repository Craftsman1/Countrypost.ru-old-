ALTER TABLE  `orders` ADD  `tracking_no` TEXT NOT NULL ,
ADD  `payed_date` TIMESTAMP NULL ,
ADD  `sent_date` TIMESTAMP NULL;

ALTER TABLE  `orders` CHANGE  `tracking_no`  `tracking_no` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NULL;

ALTER TABLE  `orders` ADD  `address_id` INT( 11 ) NOT NULL DEFAULT  '0';

ALTER TABLE  `orders` CHANGE  `order_address`  `order_address` TEXT CHARACTER SET utf8 COLLATE utf8_general_ci NOT
NULL;

ALTER TABLE  `addresses` ADD  `is_generated` TINYINT( 1 ) NOT NULL DEFAULT  '0';