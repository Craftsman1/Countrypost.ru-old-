<div class='content'>
	<?  Breadcrumb::showCrumbs(); ?>
	<a name="pagerScroll"></a>
	<h2>Комиссия Countrypost</h2>
	<? View::show($viewpath.'elements/taxes_filter'); ?>
	<br />
	<? View::show($viewpath.'ajax/showCountrypostTaxes'); ?>
</div>
<script>
	function updatePerPage(dropdown)
	{
		var id = $(dropdown).find('option:selected').val();
		window.location.href = '<?= $selfurl ?>updateTaxesPerPage/' + id;
	}
</script>