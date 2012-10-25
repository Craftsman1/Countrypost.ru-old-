<div class='content'>
	<? Breadcrumb::showCrumbs(); ?>
	<br>
	<br>
	<h2><?=$order->order_type?> Заказ №<?=$order->order_id?> <?=$order->order_country_from?> - <?=$order->order_country_to?> (<?=$order->order_city_to?>)</h2>
	<? View::show('main/elements/orders/showOrderDetails'); ?>
	<? View::show('main/elements/orders/newRequestForm'); ?>
	<? View::show('main/elements/orders/bids'); ?>
</div>
<script type="text/javascript">
</script>