<div class='content smallheader'>
	<? Breadcrumb::showCrumbs(); ?>
	<a name="pagerScroll"></a>
	<h2>Все заявки на оплату</h2>
	<? View::show($viewpath.'elements/payments/filter'); ?>
	<br />
	<? View::show('admin/ajax/showAllOpenPayments'); ?>
	<? View::show('/admin/elements/div_float_upload_bill'); ?>
</div>
<? View::show('admin/elements/payments/scripts'); ?>