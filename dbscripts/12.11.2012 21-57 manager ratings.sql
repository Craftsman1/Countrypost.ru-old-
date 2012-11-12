CREATE TABLE IF NOT EXISTS `manager_ratings` (
  `rating_id` int(11) NOT NULL AUTO_INCREMENT,
  `manager_id` int(11) NOT NULL,
  `client_id` int(11) NOT NULL,
  `rating` enum('0','0.25','0.5','0.75','1') NOT NULL DEFAULT '0',
  `created` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`rating_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;