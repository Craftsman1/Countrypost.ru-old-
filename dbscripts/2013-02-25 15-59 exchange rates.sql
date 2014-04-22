ALTER TABLE  `currencies` CHANGE  `currency_symbol`  `currency_symbol` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE
utf8_general_ci NULL DEFAULT NULL;

INSERT INTO currencies( currency_name, currency_symbol, cbr_exchange_rate, cbr_cross_rate, currency_tax )
SELECT DISTINCT country_currency,  '', 1, 1, 1
FROM countries c2
LEFT OUTER JOIN currencies c1 ON c2.country_currency = c1.currency_name
WHERE ISNULL( c1.currency_name ) =1;

ALTER TABLE  `currencies` ADD  `exchange_service` ENUM(  'CBR',  'Google',  '',  '' ) NULL DEFAULT  'CBR';

UPDATE  `countries` SET  `country_name_en` =  'Malta',
`country_currency` =  'EUR' WHERE  `countries`.`country_id` =136;


ALTER TABLE  `orders2in` ADD  `amount_usd` INT( 11 ) NOT NULL DEFAULT  '0',
ADD  `usd_conversion_date` TIMESTAMP NOT NULL;

ALTER TABLE  `orders2in` CHANGE  `order2in_lastchange`  `order2in_lastchange` TIMESTAMP NULL;

ALTER TABLE  `orders2in` CHANGE  `usd_conversion_date`  `usd_conversion_date` TIMESTAMP NOT NULL DEFAULT
CURRENT_TIMESTAMP;

ALTER TABLE `orders2in`
  DROP `amount_usd`,
  DROP `usd_conversion_date`;

  ALTER TABLE  `payments` ADD  `amount_usd` INT( 11 ) NOT NULL DEFAULT  '0',
ADD  `usd_conversion_date` TIMESTAMP NOT NULL;
ALTER TABLE  `payments` CHANGE  `usd_conversion_date`  `usd_conversion_rate` FLOAT( 11 ) NOT NULL;

TRUNCATE TABLE  `payments`;
TRUNCATE TABLE  `orders2in`;

ALTER TABLE  `payments` ADD  `status` ENUM(  'sent_by_client',  'not_payed',  'payed' ) NOT NULL;

DROP TABLE IF EXISTS `exchange_rates`;
CREATE TABLE IF NOT EXISTS `exchange_rates` (
  `exchange_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `rate` float NOT NULL DEFAULT '1',
  `min_client_rate` float NOT NULL DEFAULT '0',
  `min_manager_rate` float NOT NULL DEFAULT '0',
  `client_extra_tax` float NOT NULL DEFAULT '0',
  `manager_extra_tax` float NOT NULL DEFAULT '0',
  `currency_from` varchar(3) NOT NULL,
  `currency_to` varchar(3) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  `updated` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`exchange_rate_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=1281 ;

SELECT *
FROM  `countries`
WHERE NOT
EXISTS (

SELECT 1
FROM exchange_rates
WHERE exchange_rates.currency_from =  `countries`.country_currency
);


ALTER TABLE  `payments` ADD  `order2in_id` INT( 11 ) NOT NULL DEFAULT  '0' AFTER  `order_id`;

ALTER TABLE  `payments` ADD  `payment_service_name` VARCHAR( 255 ) NOT NULL AFTER  `payment_service_id`;

ALTER TABLE  `payments` ADD  `payment_details` TEXT NOT NULL AFTER  `payment_purpose`;

ALTER TABLE  `payments` CHANGE  `amount_usd`  `amount_usd` FLOAT NOT NULL DEFAULT  '0';

ALTER TABLE  `orders` CHANGE  `order_cost`  `order_cost` INT( 11 ) NOT NULL DEFAULT  '0',
CHANGE  `order_cost_payed`  `order_cost_payed` INT( 11 ) NOT NULL;