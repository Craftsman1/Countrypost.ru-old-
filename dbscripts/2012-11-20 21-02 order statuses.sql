ALTER TABLE  `orders` CHANGE  `order_status`  `order_status` ENUM(  'pending',  'processing',  'not_payed',  'not_available',  'payed',  'waiting',  'bought',  'completed',  'deleted' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL;

ALTER TABLE  `orders` CHANGE  `order_type`  `order_type` ENUM(  'online',  'offline',  'service',  'delivery',
'mail_forwarding' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'online';
ALTER TABLE  `orders` CHANGE  `order_status`  `order_status` ENUM(  'pending',  'processing',  'not_payed',
'not_available',  'payed',  'bought',  'completed',  'deleted' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'pending';