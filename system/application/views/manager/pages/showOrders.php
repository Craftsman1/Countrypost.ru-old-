<div class='content'>
	<? Breadcrumb::showCrumbs(); ?>
	<a name="pagerScroll"></a>
	<h2>Мои заказы</h2>
	<? View::show("{$viewpath}elements/orders/filter"); ?>
	<? View::show("{$viewpath}ajax/show{$view_status}Orders", array(
		'orders' => $orders,
		'pager' => $pager)); ?>
</div>
<script>
	function refundItem(id)
	{
		if (confirm("Возместить клиенту заказ №" + id + "?")){
			window.location.href = '<?= $selfurl ?>refundOrder/' + id;
		}
	}

	function status_handler(page_status)
	{
		order_status_handler('<?= $selfurl ?>', page_status)
	}
</script>