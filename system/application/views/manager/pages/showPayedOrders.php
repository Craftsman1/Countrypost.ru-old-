<div class='content'>
	<h3>Оплаченные заказы</h3>
	<? View::show($viewpath.'elements/div_float_new_package'); ?>
	<? View::show($viewpath.'elements/order_filter', array(
		'handler' => 'filterPayedOrders',
	)); ?>
	<? View::show($viewpath.'ajax/showPayedOrders', array(
		'orders' => $orders,
		'pager' => $pager)); ?>
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