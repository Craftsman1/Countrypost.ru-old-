ALTER TABLE  `orders2in` CHANGE  `order2in_amount`  `order2in_amount` FLOAT NOT NULL ,
CHANGE  `order2in_amount_local`  `order2in_amount_local` FLOAT NOT NULL ,
CHANGE  `excess_amount`  `excess_amount` FLOAT NOT NULL DEFAULT  '0';

ALTER TABLE  `orders` CHANGE  `order_cost`  `order_cost` FLOAT NOT NULL DEFAULT  '0',
CHANGE  `order_cost_payed`  `order_cost_payed` FLOAT NOT NULL;