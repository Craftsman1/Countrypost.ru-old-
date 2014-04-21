DROP TABLE IF EXISTS `o2p`;
CREATE TABLE IF NOT EXISTS `o2p` (
  `order_id` int(11) NOT NULL,
  `package_id` int(11) NOT NULL,
  UNIQUE KEY `unique_links` (`order_id`,`package_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
COMMIT;