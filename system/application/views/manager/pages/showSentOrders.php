<div class='content'>
	<h3>Закрытые заказы</h3>
	<? View::show($viewpath.'elements/order_filter', array(
		'handler' => 'filterSentOrders',
	)); ?>
	<? View::show($viewpath.'elements/div_float_new_package'); ?>
	<? View::show($viewpath.'ajax/showSentOrders', array(
		'orders' => $orders,
		'pager' => $pager)); ?>
</div>
