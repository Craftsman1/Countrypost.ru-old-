ALTER TABLE  `managers` ADD  `skype` VARCHAR( 255 ) NOT NULL AFTER  `website`;
ALTER TABLE  `managers` CHANGE  `last_client_added`  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;
ALTER TABLE  `managers` ADD  `min_order_tax` FLOAT NOT NULL AFTER  `order_tax`;
ALTER TABLE  `managers` ADD  `join_tax` FLOAT NOT NULL AFTER  `min_order_tax` ,
ADD  `foto_tax` FLOAT NOT NULL AFTER  `join_tax` ,
ADD  `insurance_tax` FLOAT NOT NULL AFTER  `foto_tax`;
ALTER TABLE  `managers` ADD  `pricelist_description` VARCHAR( 1024 ) NOT NULL;
ALTER TABLE  `managers` ADD  `payments_description` VARCHAR( 1024 ) NOT NULL;

DROP TABLE IF EXISTS `blog`;
CREATE TABLE IF NOT EXISTS `blog` (
  `blog_id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) NOT NULL,
  `message` mediumtext NOT NULL,
  `created` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00' ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`blog_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

RENAME TABLE  `countrypost.service`.`blog` TO  `countrypost.service`.`blogs` ;

ALTER TABLE  `blogs` ADD  `status` ENUM(  'active',  'deleted' ) NOT NULL;