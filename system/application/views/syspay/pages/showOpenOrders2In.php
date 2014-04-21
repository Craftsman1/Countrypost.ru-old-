<div class='content syspay'>
	<? View::show('/syspay/elements/client_payment_box'); ?>
	<h3>Новые заявки на пополнение</h3>
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