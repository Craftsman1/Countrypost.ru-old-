CREATE TABLE IF NOT EXISTS `pricelist_description` (
  `pricelist_description_id` int(11) NOT NULL AUTO_INCREMENT,
  `pricelist_country_from` int(11) NOT NULL,
  `pricelist_country_to` int(11) NOT NULL,
  `pricelist_description` text,
  PRIMARY KEY (`pricelist_description_id`),
  UNIQUE KEY `pricelist_delivery` (`pricelist_country_from`,`pricelist_country_to`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
