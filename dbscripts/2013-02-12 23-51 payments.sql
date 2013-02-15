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