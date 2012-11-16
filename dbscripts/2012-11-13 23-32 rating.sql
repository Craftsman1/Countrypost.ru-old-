ALTER TABLE  `manager_ratings` ADD  `rating_value` ENUM(  '-1',  '0',  '1',  '' ) NOT NULL DEFAULT  '0' AFTER
`client_id`;

ALTER TABLE  `manager_ratings` CHANGE  `rating_value`  `rating_type` ENUM(  '-1',  '0',  '1',  '' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  '0',
CHANGE  `communication_rating`  `communication_rating` ENUM(  '0',  '0.25',  '0.5',  '0.75',  '1' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `buy_rating`  `buy_rating` ENUM(  '0',  '0.25',  '0.5',  '0.75',  '1' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `consolidation_rating`  `consolidation_rating` ENUM(  '0',  '0.25',  '0.5',  '0.75',  '1' ) CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT NULL ,
CHANGE  `pack_rating`  `pack_rating` ENUM(  '0',  '0.25',  '0.5',  '0.75',  '1' ) CHARACTER SET utf8 COLLATE
utf8_general_ci NULL DEFAULT NULL;

ALTER TABLE  `manager_ratings` ADD  `status` ENUM(  'active',  'deleted',  '',  '' ) NOT NULL DEFAULT  'active' AFTER
  `pack_rating`;

ALTER TABLE  `users` ADD  `positive_reviews` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0',
ADD  `neutral_reviews` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0',
ADD  `negative_reviews` INT( 11 ) UNSIGNED NOT NULL DEFAULT  '0';

ALTER TABLE  `managers` ADD  `communication_rating` FLOAT NULL DEFAULT NULL AFTER  `rating` ,
ADD  `buy_rating` FLOAT NULL DEFAULT NULL AFTER  `communication_rating` ,
ADD  `consolidation_rating` FLOAT NULL DEFAULT NULL AFTER  `buy_rating` ,
ADD  `pack_rating` FLOAT NULL DEFAULT NULL AFTER  `consolidation_rating`;

ALTER TABLE  `managers` CHANGE  `rating`  `rating` FLOAT NOT NULL DEFAULT  '0';
ALTER TABLE  `managers` ADD  `buy_rating_count` INT NOT NULL DEFAULT  '0' AFTER  `pack_rating` ,
ADD  `pack_rating_count` INT NOT NULL DEFAULT  '0' AFTER  `buy_rating_count` ,
ADD  `consolidation_rating_count` INT NOT NULL DEFAULT  '0' AFTER  `pack_rating_count` ,
ADD  `communication_rating_count` INT NOT NULL DEFAULT  '0' AFTER  `consolidation_rating_count`;