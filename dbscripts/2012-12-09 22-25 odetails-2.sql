ALTER TABLE `odetails` ADD `odetail_volume` DECIMAL( 10, 3 ) NULL ,
ADD `odetail_tnved` VARCHAR( 255 ) NULL AFTER `odetail_volume` ,
ADD `odetail_insurance` TINYINT( 1 ) NULL AFTER `odetail_tnved`,
ADD `odetail_shop` VARCHAR( 255 ) DEFAULT NULL 