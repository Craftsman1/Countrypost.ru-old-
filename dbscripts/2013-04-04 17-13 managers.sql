ALTER TABLE  `managers` DROP  `manager_surname` ,
DROP  `manager_otc` ,
DROP  `manager_addres` ;

ALTER TABLE  `managers` ADD  `manager_address_name` VARCHAR( 255 ) NOT NULL AFTER  `manager_address_description`;

ALTER TABLE  `managers` CHANGE  `manager_address_description`  `manager_address_description` VARCHAR( 4096 )
CHARACTER SET utf8 COLLATE utf8_general_ci NULL DEFAULT  '';
