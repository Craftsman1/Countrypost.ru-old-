<div class='content'>
	<?  Breadcrumb::showCrumbs(); ?>
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