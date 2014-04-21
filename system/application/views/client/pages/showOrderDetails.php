<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<? View::show('main/elements/orders/header'); ?>
	<? View::show('client/ajax/showOrderInfo'); ?>
	<h3 class='clientOrderInfo' <? if ($order->order_client != $this->user->user_id) : ?>style="display:none;"<?
	endif; ?>>
		Товары в заказе
	</h3>
	<? View::show('client/ajax/showOrderDetails'); ?>
	<? View::show('main/elements/orders/bids'); ?>
	<? View::show('/client/elements/div_float_upload_bill'); ?>
</div>
<? View::show('client/elements/orders/scripts'); ?>