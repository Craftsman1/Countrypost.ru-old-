<div class='content'>
	<?View::show($viewpath.'elements/form_add_o2o', array('rate' => $rate));?>
	<h3>Ваши заявки на вывод</h3>
	<br />
	<?View::show($viewpath.'ajax/showOutMoney', array(
		'Orders' => $Orders,
		'statuses'	=> $statuses,
		'pager' => $pager));?>
</div>