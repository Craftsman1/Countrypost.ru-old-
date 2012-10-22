CREATE TABLE IF NOT EXISTS `country_pricelist` (
  `country_id` int(11) NOT NULL,
  `description` varchar(4096) NOT NULL,
  PRIMARY KEY (`country_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

INSERT INTO `country_pricelist` (`country_id`, `description`) VALUES
(5, ''),
(6, '');