UPDATE `countrypost.service`.`countries` SET `country_currency` = 'RUR' WHERE `countries`.`country_id` =12 LIMIT 1 ;

ALTER TABLE `clients` ADD `about_me` TEXT NOT NULL;