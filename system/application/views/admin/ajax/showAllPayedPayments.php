<div id='payments'>
	<? View::show('main/elements/payments/tabsAll', array('selected_submenu' => 'payed_payments')); ?>
	<? View::show($viewpath . 'elements/payments/showAllPayments'); ?>
</div>