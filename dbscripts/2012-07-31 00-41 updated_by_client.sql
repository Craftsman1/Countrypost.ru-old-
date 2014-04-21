ALTER TABLE  `odetails` ADD  `updated_by_client` TINYINT NOT NULL DEFAULT  '0';
ALTER TABLE  `odetails` CHANGE  `updated_by_client`  `updated_by_client` TINYINT( 1 ) NOT NULL DEFAULT  '0';
ALTER TABLE  `orders` ADD  `updated_by_client` TINYINT NOT NULL DEFAULT  '0';
ALTER TABLE  `orders` CHANGE  `updated_by_client`  `updated_by_client` TINYINT( 1 ) NOT NULL DEFAULT  '0';