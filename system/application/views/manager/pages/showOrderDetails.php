<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<? View::show('main/elements/orders/header'); ?>
	<? View::show('manager/ajax/showOrderInfo'); ?>
	<h3 class='managerOrderInfo' <? if ( ! in_array($order->order_status, $editable_statuses)) : ?>style="display:none;
	"<?
	endif; ?>>
		Товары в заказе
	</h3>
	<? View::show('manager/ajax/showOrderDetails'); ?>
	<? if ($bids_accepted) : ?>
	<? View::show('main/elements/orders/newBidForm'); ?>
	<? endif; ?>
	<? View::show('main/elements/orders/bids'); ?>
</div>
<? View::show('manager/elements/orders/scripts'); ?>