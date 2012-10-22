ALTER TABLE  `odetails` ADD  `odetail_weight` FLOAT UNSIGNED NOT NULL DEFAULT  '0' AFTER  `odetail_joint_id`;

DROP TABLE IF EXISTS `requests`;
CREATE TABLE IF NOT EXISTS `requests` (
  `request_id` int(11) NOT NULL AUTO_INCREMENT,
  `manager_id` int(11) NOT NULL,
  `order_id` int(11) NOT NULL,
  PRIMARY KEY (`request_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;

ALTER TABLE  `requests` ADD  `manager_tax` FLOAT NOT NULL ,
ADD  `foto_tax` FLOAT NOT NULL ,
ADD  `delivery_cost` FLOAT NOT NULL ,
ADD  `delivery_name` VARCHAR( 255 ) NOT NULL;

ALTER TABLE  `requests` ADD  `total_cost` FLOAT NOT NULL;