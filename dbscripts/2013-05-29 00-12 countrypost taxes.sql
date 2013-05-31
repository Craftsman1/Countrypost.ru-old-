ALTER TABLE  `bids` ADD  `countrypost_tax` FLOAT NOT NULL AFTER  `order_id` ,
ADD  `usd_exchange_rate` FLOAT NOT NULL AFTER  `countrypost_tax`;

ALTER TABLE  `bids` ADD  `countrypost_tax_usd` FLOAT NOT NULL DEFAULT  '0' AFTER  `countrypost_tax`;

ALTER TABLE  `bids` CHANGE  `countrypost_tax`  `countrypost_tax` FLOAT NOT NULL DEFAULT  '0';

ALTER TABLE  `bids` CHANGE  `usd_exchange_rate`  `usd_conversion_rate` FLOAT NOT NULL;

ALTER TABLE  `orders` ADD  `countrypost_tax` FLOAT( 0 ) NOT NULL AFTER  `order_cost_payed`;

ALTER TABLE  `orders` CHANGE  `countrypost_tax`  `countrypost_tax` FLOAT NOT NULL DEFAULT  '0';

ALTER TABLE  `orders` ADD  `countrypost_tax_usd` FLOAT NOT NULL DEFAULT  '0' AFTER  `countrypost_tax` ,
ADD  `usd_conversion_rate` FLOAT NOT NULL AFTER  `countrypost_tax_usd`;