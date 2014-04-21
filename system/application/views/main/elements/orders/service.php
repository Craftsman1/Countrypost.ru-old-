<h2 id='page_title'>Новая <?= $order_types[$order->order_type] ?> №<?= $order->order_id ?></h2>
<div>
	<? View::show('client/elements/showAddOrder/service'); ?>
	<h3>Добавить товар:</h3>
	<? View::show('client/elements/showAddProduct/service'); ?>
	<? if ( ! empty($orders)) : ?>
		<? View::show('client/ajax/showOrderDetails'); ?>
	<? endif; ?>
</div>