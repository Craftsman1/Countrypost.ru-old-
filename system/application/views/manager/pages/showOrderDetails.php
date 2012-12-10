<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<? View::show('main/elements/orders/header'); ?>
	<? View::show('manager/ajax/showOrderInfo'); ?>
	<h3 class='managerOrderInfo' <? if ($order->order_manager != $this->user->user_id OR empty($order->bid)) : ?>style="display:none;"<?
	endif; ?>>
		Товары в заказе
	</h3>
	<? View::show('manager/ajax/showOrderDetails'); ?>
	<? View::show('main/elements/orders/newBidForm'); ?>
	<? View::show('main/elements/orders/bids'); ?>
</div>
<? View::show('manager/elements/orders/scripts'); ?>