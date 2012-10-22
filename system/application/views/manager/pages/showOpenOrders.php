<div class='content'>
	<h3>Заказы "Помощь в покупке"</h3>
	<? View::show($viewpath.'elements/order_filter', array(
		'handler' => 'filterOpenOrders',
		'show_status_filter' => TRUE
	)); ?>
	<? View::show($viewpath.'ajax/showOpenOrders', array(
		'orders' => $orders,
		'pager' => $pager)); ?>
	<h3>Все новые заказы</h3>
	<? View::show($viewpath.'ajax/showUnassignedOrders', array(
		'orders' => $unassigned_orders)); ?>
</div>
<script type="text/javascript">
	$('#ordersForm').submit(function() {
		if ($('#ordersForm input:checkbox:checked').size() == 0)
		{
			alert('Выберите заказы для отправки.');
			return false;
		}
		
		if (!confirm('Вы уверены, что хотите отправить выбранные заказы?'))
		{
			return false;
		}
	});
</script>