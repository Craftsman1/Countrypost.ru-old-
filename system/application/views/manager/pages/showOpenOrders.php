<div class='content'>
	<a name="pagerScroll"></a>
	<h3>Мои заказы</h3>
	<? View::show($viewpath.'elements/order_filter', array(
		'handler' => 'filterOpenOrders',
		'show_status_filter' => TRUE
	)); ?>
	<? View::show($viewpath.'ajax/showOpenOrders', array(
		'orders' => $orders,
		'pager' => $pager)); ?>
</div>
<script>
	function refundItem(id) {
		if (confirm("Возместить клиенту заказ №" + id + "?")){
			window.location.href = '<?= $selfurl ?>refundOrder/' + id;
		}
	}

	function status_handler(page_status)
	{
		order_status_handler('<?= $selfurl ?>', page_status)
	}
</script>