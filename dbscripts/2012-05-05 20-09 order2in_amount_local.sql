ALTER TABLE  `orders2in` ADD  `order2in_amount_local` INT( 11 ) NULL AFTER  `order2in_amount`;
ALTER TABLE  `orders2in` ADD  `order2in_currency` VARCHAR( 255 ) NULL;
ALTER TABLE  `currencies` ADD PRIMARY KEY (  `currency_name` ) ;
INSERT INTO `shipito`.`payment_services` (`payment_service_id`, `payment_service_name`, `payment_service_inprompt`, `payment_service_outprompt`) VALUES ('pb', 'Приватбанк', NULL, 'Банковский перевод по Украине');
UPDATE  `shipito`.`payment_services` SET  `payment_service_inprompt` =  'Банковский перевод по Украине',
`payment_service_outprompt` = NULL WHERE  `payment_services`.`payment_service_id` =  'pb';