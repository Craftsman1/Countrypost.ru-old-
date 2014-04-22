ALTER TABLE  `payments` ADD  `excess_amount` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `payment_amount_tax`;
ALTER TABLE  `orders2in` ADD  `excess_amount` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `order2in_amount_local`;
