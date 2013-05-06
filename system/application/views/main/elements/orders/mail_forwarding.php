<h2 id='page_title'>Новый заказ <?= $order_types[$order->order_type] ?> №<?= $order->order_id ?></h2>
<div>
	<? View::show('client/elements/showAddOrder/mail_forwarding'); ?>
	<h3>Добавить товар:</h3>
	<? View::show('client/elements/showAddProduct/mail_forwarding'); ?>
	<? if ( ! empty($orders)) : ?>
		<? View::show('client/ajax/showOrderDetails'); ?>
	<? endif; ?>
</div>