<div class='content syspay'>
	<? Breadcrumb::showCrumbs(); ?>
	<h2>Оплата заказа №<?= $order->order_id ?> (через Countrypost.ru)</h2>
	<? View::show('/syspay/elements/client_payment_box'); ?>
	<h3>Все заявки на оплату</h3>
	<br />
	<? View::show('/client/ajax/showOpenOrders2In'); ?>
</div>
<script>
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}
</script>