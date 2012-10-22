CREATE TABLE  `bid_comments` (
`comment_id` INT( 11 ) NOT NULL AUTO_INCREMENT ,
 `user_id` INT( 11 ) NOT NULL ,
 `order_id` INT( 11 ) UNSIGNED ZEROFILL NOT NULL ,
 `message` TEXT NOT NULL ,
 `created` TIMESTAMP NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
PRIMARY KEY (`comment_id` )
) ENGINE = MYISAM DEFAULT CHARSET = utf8;
		
		
ALTER TABLE  `bid_comments` CHANGE  `order_id`  `bid_id` INT( 11 ) UNSIGNED ZEROFILL NOT NULL;
ALTER TABLE  `bid_comments` ADD  `status` ENUM(  'active',  'deleted' ) NOT NULL;

ALTER TABLE  `bids` ADD  `client_id` INT( 11 ) NOT NULL AFTER  `manager_id`;