<div class='content'>
	<?  Breadcrumb::showCrumbs(); ?>
	<h2>Заявка на оплату №<?= $o2i->order2in_id ?> (<?= empty($o2i->is_countrypost) ?
		'напрямую посреднику' :
		'через Countrypost.ru' ?>)</h2>
	<? View::show('/main/elements/payments/payment_info'); ?>
	<h3>Комментарии</h3>
	<? View::show('/main/elements/payments/comments'); ?>
</div>