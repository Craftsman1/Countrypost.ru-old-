ALTER TABLE  `managers` ADD  `package_disconnected_tax` FLOAT NULL AFTER  `package_tax`;
UPDATE  `managers` SET  `package_disconnected_tax` =  `package_tax`;