ALTER TABLE  `managers` ADD  `order_mail_forwarding_tax` FLOAT NULL DEFAULT 0 AFTER  `order_tax`;
ALTER TABLE `managers`
  DROP `manager_max_clients`,
  DROP `package_tax`,
  DROP `package_disconnected_tax`,
  DROP `package_foto_tax`,
  DROP `package_foto_system_tax`;

  ALTER TABLE  `orders` ADD  `manager_tax` FLOAT NOT NULL DEFAULT  '0' AFTER  `order_cost_payed` ,
ADD  `foto_tax` FLOAT NOT NULL DEFAULT  '0' AFTER  `manager_tax` ,
ADD  `delivery_cost` FLOAT NOT NULL DEFAULT  '0' AFTER  `foto_tax` ,
ADD  `delivery_name` VARCHAR( 255 ) NOT NULL AFTER  `delivery_cost` ,
ADD  `extra_tax` FLOAT NOT NULL DEFAULT  '0' AFTER  `delivery_name`;