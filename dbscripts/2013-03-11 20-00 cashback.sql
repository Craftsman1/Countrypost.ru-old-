UPDATE  `managers` SET is_cashback =0;

ALTER TABLE  `managers` CHANGE  `order_tax`  `order_tax` FLOAT NULL DEFAULT  '10',
CHANGE  `order_mail_forwarding_tax`  `order_mail_forwarding_tax` FLOAT NULL DEFAULT  '50',
CHANGE  `min_order_tax`  `min_order_tax` FLOAT NOT NULL DEFAULT  '60',
CHANGE  `join_tax`  `join_tax` FLOAT NOT NULL DEFAULT  '0',
CHANGE  `foto_tax`  `foto_tax` FLOAT NOT NULL DEFAULT  '0',
CHANGE  `insurance_tax`  `insurance_tax` FLOAT NOT NULL DEFAULT  '2';

ALTER TABLE  `managers` CHANGE  `order_mail_forwarding_tax`  `order_mail_forwarding_tax` FLOAT NULL DEFAULT  '0',
CHANGE  `min_order_tax`  `min_order_tax` FLOAT NOT NULL DEFAULT  '0';

ALTER TABLE  `managers` CHANGE  `insurance_tax`  `insurance_tax` FLOAT NOT NULL DEFAULT  '0';