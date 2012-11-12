ALTER TABLE `clients` ADD `website` VARCHAR( 4096 ) NOT NULL AFTER `client_phone` ,
ADD `skype` VARCHAR( 255 ) NOT NULL AFTER `website`;

ALTER TABLE  `manager_ratings` CHANGE  `rating`  `communication_rating` ENUM(  '0',  '0.25',  '0.5',  '0.75',
'1' ) NOT NULL DEFAULT  '0';

ALTER TABLE  `manager_ratings` ADD  `buy_rating` ENUM(  '0',  '0.25',  '0.5',  '0.75',  '1' ) NOT NULL DEFAULT  '0' AFTER  `communication_rating` ,
ADD  `consolidation_rating` ENUM(  '0',  '0.25',  '0.5',  '0.75',  '1' ) NOT NULL DEFAULT  '0' AFTER  `buy_rating` ,
ADD  `pack_rating` ENUM(  '0',  '0.25',  '0.5',  '0.75',  '1' ) NOT NULL DEFAULT  '0' AFTER  `consolidation_rating`
;

CREATE TABLE  `countrypost.service`.`rating_comments` (
`comment_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `user_id` INT( 11 ) NOT NULL ,
 `bid_id` INT( 11 ) NOT NULL ,
 `message` TEXT NOT NULL ,
 `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP ,
 `status` ENUM(  'active',  'deleted' ) NOT NULL ,
PRIMARY KEY (  `comment_id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;

ALTER TABLE  `rating_comments` CHANGE  `bid_id`  `rating_id` INT( 11 ) NOT NULL;