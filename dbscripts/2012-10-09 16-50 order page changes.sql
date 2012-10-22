ALTER TABLE  `managers` ADD  `rating` INT( 11 ) NOT NULL DEFAULT  '0',
ADD  `website` VARCHAR( 255 ) NULL ,
ADD  `status` ENUM(  'basic',  'cashback' ) NOT NULL DEFAULT  'basic';

ALTER TABLE  `requests` CHANGE  `request_id`  `bid_id` INT( 11 ) NOT NULL AUTO_INCREMENT;
RENAME TABLE  `countrypost.service`.`requests` TO  `countrypost.service`.`bids` ;
ALTER TABLE  `bids` ADD  `status` ENUM(  'active',  'deleted' ) NOT NULL DEFAULT  'active';

ALTER TABLE  `bids` ADD  `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP;