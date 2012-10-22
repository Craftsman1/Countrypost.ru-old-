ALTER TABLE  `odetails` CHANGE  `odetail_shop_name`  `odetail_comment` VARCHAR( 255 ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;
ALTER TABLE  `odetails` ADD  `odetail_foto_requested` TINYINT( 1 ) NOT NULL DEFAULT  '0' AFTER  `odetail_product_amount`;
ALTER TABLE  `orders` ADD  `order_city_to` VARCHAR( 255 ) NOT NULL AFTER  `order_country_to`;
ALTER TABLE  `orders` ADD  `preferred_delivery` VARCHAR( 255 ) NOT NULL;