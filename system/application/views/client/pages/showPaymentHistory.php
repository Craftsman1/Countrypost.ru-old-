<div class='content'>
	<?  Breadcrumb::showCrumbs(); ?>
	<a name="pagerScroll"></a>
	<h2>Статистика платежей</h2>
	<br />
	<? View::show($viewpath.'ajax/showPaymentHistory', array(
		'Payments' => $Payments,
		'services' => $services,
		'pager' => $pager));?>
</div>