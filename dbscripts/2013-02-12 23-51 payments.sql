ALTER TABLE `orders2in`
  DROP `order2in_amount_local`,
  DROP `order2in_amount_rur`,
  DROP `order2in_amount_kzt`,
  DROP `order2in_tax`;

  ALTER TABLE  `orders2in` ADD  `order2int_to` INT( 11 ) NOT NULL AFTER  `order2in_from`;

  ALTER TABLE  `orders2in` ADD  `order_id` INT( 11 ) NOT NULL;

  ALTER TABLE  `orders2in` ADD  `payment_service_name` VARCHAR( 255 ) NOT NULL AFTER  `order2in_payment_service`;

  ALTER TABLE  `orders2in` CHANGE  `order2int_to`  `order2in_to` INT( 11 ) NOT NULL;
  ALTER TABLE  `orders2in` ADD  `order2in_amount_local` INT( 11 ) NOT NULL AFTER  `order2in_amount`;



ALTER TABLE  `orders2in` CHANGE  `order_id`  `order_id` INT( 11 ) NOT NULL DEFAULT  '1';

ALTER TABLE  `orders2in` CHANGE  `order2in_to`  `order2in_to` INT( 11 ) NOT NULL DEFAULT  '1',
CHANGE  `order_id`  `order_id` INT( 11 ) NOT NULL;

ALTER TABLE  `orders2in` CHANGE  `order2in_status`  `order2in_status` ENUM(  'processing',  'payed',
'not_delivered',  'deleted' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'processing';

ALTER TABLE  `orders2in` CHANGE  `order2in_status`  `order2in_status` ENUM(  'processing',  'payed',
'not_delivered',  'no_screenshot',  'deleted' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'processing';

ALTER TABLE  `orders2in` ADD  `is_money_sent` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `orders2in` ADD  `is_countrypost` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `order_id` ;