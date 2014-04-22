ALTER TABLE  `odetails` CHANGE  `odetail_status`  `odetail_status` ENUM(  'processing',  'available',
'not_available',  'not_available_color',  'not_available_size',  'not_available_count',  'bought',  'sent_by_seller', 'completed',  'exchange',  'return',  'deleted' ) CHARACTER SET utf8 COLLATE utf8_general_ci NOT NULL DEFAULT  'processing';

ALTER TABLE `orders`
  DROP `order_country`,
  DROP `order_login`,
  DROP `package_delivery_cost`,
  DROP `package_id`,
  DROP `order_shop_name`,
  DROP `order_delivery_cost_local`,
  DROP `order_products_cost_local`,
  DROP `order_manager_comission_local`,
  DROP `order_manager_comission_payed_local`,
  DROP `order_manager_cost_local`,
  DROP `order_manager_cost_payed_local`;

  ALTER TABLE `odetails`
  DROP `odetail_price_usd`,
  DROP `odetail_pricedelivery_usd`;