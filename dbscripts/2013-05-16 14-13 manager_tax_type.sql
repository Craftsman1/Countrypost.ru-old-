ALTER TABLE  `bids` ADD  `tax_type` ENUM(  'products_delivery',  'products',  'custom',
'' ) NOT NULL DEFAULT  'custom' AFTER  `order_id`;

ALTER TABLE  `bids` CHANGE  `tax_type`  `manager_tax_type` ENUM(  'products_delivery',  'products',  'custom',
'' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT 'custom';