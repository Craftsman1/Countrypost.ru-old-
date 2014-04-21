ALTER TABLE `addresses` ENGINE = InnoDB;

ALTER TABLE `addresses` ADD `address_country` INT NOT NULL AFTER `address_user` ,
ADD INDEX ( `address_country` ) ;

ALTER TABLE `addresses` ADD FOREIGN KEY ( `address_country` ) REFERENCES `countrypost.service`.`countries` (
`country_id`
);

ALTER TABLE `addresses` ADD `address_recipient` VARCHAR( 255 ) NOT NULL AFTER `address_user` ;