ALTER TABLE  `currencies` CHANGE  `currency_symbol`  `currency_symbol` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE
utf8_general_ci NULL DEFAULT NULL;

INSERT INTO currencies( currency_name, currency_symbol, cbr_exchange_rate, cbr_cross_rate, currency_tax )
SELECT DISTINCT country_currency,  '', 1, 1, 1
FROM countries c2
LEFT OUTER JOIN currencies c1 ON c2.country_currency = c1.currency_name
WHERE ISNULL( c1.currency_name ) =1;

ALTER TABLE  `currencies` ADD  `exchange_service` ENUM(  'CBR',  'Google',  '',  '' ) NULL DEFAULT  'CBR';

CREATE TABLE IF NOT EXISTS `exchange_rates` (
  `exchange_rate_id` int(11) NOT NULL AUTO_INCREMENT,
  `currency_from` varchar(3) NOT NULL,
  `currency_to` varchar(3) NOT NULL,
  `service_name` varchar(255) NOT NULL,
  PRIMARY KEY (`exchange_rate_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `exchange_rates` ADD  `updated` TIMESTAMP ON UPDATE CURRENT_TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE  `exchange_rates` ADD  `rate` FLOAT( 11 ) NOT NULL DEFAULT  '1' AFTER  `exchange_rate_id`;
/*
INSERT INTO exchange_rates( rate,  `currency_from` ,  `currency_to` ,  `service_name` )
SELECT DISTINCT cbr_exchange_rate, currency_name,  'USD',  'CBR'
FROM currencies
WHERE cbr_exchange_rate !=1;

UPDATE  `exchange_rates` SET currency_to =  'RUR';

INSERT INTO  `countrypost.service`.`exchange_rates` (
`exchange_rate_id` ,
`rate` ,
`currency_from` ,
`currency_to` ,
`service_name` ,
`updated`
)
VALUES (
NULL ,  '4.8665574899999999',  'CNY',  'RUR',  'Google',
CURRENT_TIMESTAMP
), (
NULL ,  '0.160413',  'CNY',  'USD',  'Google',
CURRENT_TIMESTAMP
);

INSERT INTO  `countrypost.service`.`exchange_rates` (
`exchange_rate_id` ,
`rate` ,
`currency_from` ,
`currency_to` ,
`service_name` ,
`updated`
)
VALUES (
NULL ,  '1',  'CNY',  'UAH',  '1.3059225800000001',
CURRENT_TIMESTAMP
), (
NULL ,  '1',  'CNY',  'KZT',  '24.111945899999998',
CURRENT_TIMESTAMP
);

*/

INSERT INTO `exchange_rates` (`exchange_rate_id`, `rate`, `currency_from`, `currency_to`, `service_name`, `updated`) VALUES
(1, 5.0092, 'CNY', 'RUR', 'CBR', '2013-02-25 10:59:35'),
(2, 38.7467, 'EUR', 'RUR', 'CBR', '2013-02-25 10:59:35'),
(3, 0.408791, 'JPY', 'RUR', 'CBR', '2013-02-25 10:59:35'),
(4, 0.0273156, 'KRW', 'RUR', 'CBR', '2013-02-25 10:59:35'),
(5, 0.224165, 'KZT', 'RUR', 'CBR', '2013-02-25 10:59:35'),
(6, 17.5665, 'TRY', 'RUR', 'CBR', '2013-02-25 10:59:35'),
(7, 4.07915, 'UAH', 'RUR', 'CBR', '2013-02-25 10:59:35'),
(8, 33.285, 'USD', 'RUR', 'CBR', '2013-02-25 10:59:35'),
(16, 4.86656, 'CNY', 'RUR', 'Google', '2013-02-25 12:12:53'),
(17, 0.160413, 'CNY', 'USD', 'Google', '2013-02-25 12:12:53'),
(18, 1.30592, 'CNY', 'UAH', 'Google', '2013-02-25 12:23:45'),
(19, 24.1119, 'CNY', 'KZT', 'Google', '2013-02-25 12:24:07');

TRUNCATE TABLE  `exchange_rates`;

UPDATE  `countrypost.service`.`countries` SET  `country_name_en` =  'Malta',
`country_currency` =  'EUR' WHERE  `countries`.`country_id` =136;

SELECT *
FROM  `countries`
WHERE NOT
EXISTS (

SELECT 1
FROM exchange_rates
WHERE exchange_rates.currency_from =  `countries`.country_currency
);

/*UPDATE  `countrypost.service`.`countries` SET  `country_currency` =  'USD' WHERE  `countries`.`country_id` =52;
*/

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

