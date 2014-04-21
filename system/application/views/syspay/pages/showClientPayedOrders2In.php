<div class='content'>
	<? View::show('/admin/elements/div_submenu'); ?>
	<h3>Закрытые заявки на пополнение</h3>
	<br />
	<? View::show($viewpath.'elements/div_float_upload_bill'); ?>
	<? View::show('/admin/ajax/showClientPayedOrders2In', array(
		'Orders2In' => $Orders2In,
		'Orders2InStatuses'	=> $Orders2InStatuses,
		'Orders2InFoto' => $Orders2InFoto,
		'services'	=> $services,
		'pager' => $pager)); ?>

</div>
<script>
	function setRel(id){
		$("a[rel*='lightbox_"+id+"']").lightBox();
		var aa = $("a[rel*='lightbox_"+id+"']");
		$(aa[0]).click();
	}
</script>