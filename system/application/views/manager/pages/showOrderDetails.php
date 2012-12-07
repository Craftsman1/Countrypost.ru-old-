<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2><?=$order->order_type?> Заказ №<?=$order->order_id?> <?=$order->order_country_from?> -
		<?=$order->order_country_to?> (<?=$order->order_city_to?>)</h2>
	<? View::show('manager/ajax/showOrderInfo'); ?>
	<h3 class='managerOrderInfo' <? if ($order->order_manager != $this->user->user_id OR empty($order->bid)) : ?>style="display:none;"<?
	endif; ?>>Товары в заказе</h3>
	<? View::show('manager/ajax/showOrderDetails'); ?>
	<? View::show('main/elements/orders/newRequestForm'); ?>
	<? View::show('main/elements/orders/bids'); ?>
</div>
<? View::show('manager/elements/orders/scripts'); ?>