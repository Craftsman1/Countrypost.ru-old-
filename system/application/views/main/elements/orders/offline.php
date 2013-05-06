<h2 id='page_title'>Новый <?= $order_types[$order->order_type] ?> №<?= $order->order_id ?></h2>
<div>
	<? View::show('client/elements/showAddOrder/offline'); ?>
	<h3>Добавить товар:</h3>
	<? View::show('client/elements/showAddProduct/offline'); ?>
	<? if ( ! empty($orders)) : ?>
		<? View::show('client/ajax/showOrderDetails'); ?>
	<? endif; ?>
</div>