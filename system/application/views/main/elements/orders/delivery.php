<h2 id='page_title'>Новый заказ на доставку №<?= $order->order_id ?></h2>
<div>
	<? View::show('client/elements/showAddOrder/delivery'); ?>
	<h3>Добавить товар/груз:</h3>
	<? View::show('client/elements/showAddProduct/delivery'); ?>
	<? if ( ! empty($orders)) : ?>
		<? View::show('client/ajax/showOrderDetails'); ?>
	<? endif; ?>
</div>