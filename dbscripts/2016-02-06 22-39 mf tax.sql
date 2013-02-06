ALTER TABLE  `managers` ADD  `order_mail_forwarding_tax` FLOAT NULL DEFAULT 0 AFTER  `order_tax`;
ALTER TABLE `managers`
  DROP `manager_max_clients`,
  DROP `package_tax`,
  DROP `package_disconnected_tax`,
  DROP `package_foto_tax`,
  DROP `package_foto_system_tax`;