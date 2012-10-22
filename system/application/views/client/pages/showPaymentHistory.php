<div class='content'>
	<h2>История платежей</h2>
	<br />
	<?View::show($viewpath.'ajax/showPaymentHistory', array(
		'Payments' => $Payments,
		'services' => $services,
		'pager' => $pager));?>
</div>