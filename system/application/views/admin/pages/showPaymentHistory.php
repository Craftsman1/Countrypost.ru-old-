<div class='content'>
	<?  Breadcrumb::showCrumbs(); ?>
	<? View::show($viewpath.'elements/div_submenu'); ?>
	<a name="pagerScroll"></a>
	<h2>Статистика платежей</h2>
	<? View::show($viewpath.'elements/payment_filter'); ?>
	<br />
	<? View::show($viewpath.'ajax/showPaymentHistory'); ?>
</div>
<script>
	function updatePerPage(dropdown)
	{
		var id = $(dropdown).find('option:selected').val();
		window.location.href = '<?= $selfurl ?>updatePaymentsPerPage/' + id;
	}
</script>
<? View::show('admin/elements/history/scripts'); ?>